<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\UploadedFile;
use App\Entities\User;
use App\Jobs\CropJob;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ImageProcessedNotification;
use App\Notifications\ImageProcessingFailedNotification;
use Intervention\Image\Facades\Image;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = $this->app->make(Dispatcher::class);
    }

    private function handleJob(User $user): array
    {
        Storage::fake("public");
        $filePath = 'images/' . $user->id . '/';
        $fakeImage = UploadedFile::fake()->image("photo.jpeg", 300, 300);
        $fakeImage->move(Storage::path($filePath), "photo.jpeg");
        $photo = factory(\App\Entities\Photo::class)->create([
            "original_photo" => $filePath . "photo.jpeg",
            "user_id" => $user->id
        ]);
        CropJob::dispatch($photo)->onConnection('sync');

        return [
            "photo" => $photo,
            "path" => $filePath
        ];
    }

    public function test_running_job()
    {
        Queue::fake();
        Storage::fake("public");

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->json("POST", "/api/photos", [
            "photo" => UploadedFile::fake()->image("photo.jpeg")
        ]);

        $response->assertStatus(200);

        $fileName = "images/{$user->id}/photo.jpeg";

        Storage::disk("public")->assertExists($fileName);

        $this->assertDatabaseHas("photos", [
            "user_id" => $user->id,
            "original_photo" => $fileName,
            "status" => "UPLOADED"
        ]);

        Queue::assertPushed(CropJob::class, function ($job) use ($user, $fileName) {
            return (
                $job->photo->user_id === $user->id
                &&
                $job->photo->original_photo === $fileName
            );
        });
    }

    public function test_handle_image()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        [
            "photo" => $photo,
            "path" => $filePath
        ] = $this->handleJob($user);

        Storage::disk('public')->assertExists([
            $filePath . 'photo100x100.jpeg',
            $filePath . 'photo150x150.jpeg',
            $filePath . 'photo250x250.jpeg'
        ]);

        $this->assertDatabaseHas("photos", [
            "id" => $photo->id,
            "user_id" => $user->id,
            "photo_100_100" => $filePath . 'photo100x100.jpeg',
            "photo_150_150" => $filePath . 'photo150x150.jpeg',
            "photo_250_250" => $filePath . 'photo250x250.jpeg',
            "status" => "SUCCESS"
        ]);

        $photo100x100 = Image::make(Storage::path($filePath . 'photo100x100.jpeg'));
        $photo150x150 = Image::make(Storage::path($filePath . 'photo150x150.jpeg'));
        $photo250x250 = Image::make(Storage::path($filePath . 'photo250x250.jpeg'));
        
        $this->assertEquals($photo100x100->width(), 100);
        $this->assertEquals($photo100x100->height(), 100);
        $this->assertEquals($photo150x150->width(), 150);
        $this->assertEquals($photo150x150->height(), 150);
        $this->assertEquals($photo250x250->width(), 250);
        $this->assertEquals($photo250x250->height(), 250);
    }

    public function test_success_notification_sent()
    {
        Notification::fake();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        [
            "photo" => $photo,
            "path" => $filePath
        ] = $this->handleJob($user);

        Notification::assertSentTo(
            $user,
            ImageProcessedNotification::class,
            function ($notification, $channels) use ($photo, $user) {
                $mailData = (string) $notification->toMail($user)->render();

                $this->assertContains("Dear {$user->name},", $mailData);
                $this->assertContains("Photos have been successfully uploaded and processed.", $mailData);
                $this->assertContains("Here are links to the images:", $mailData);
                $this->assertContains($photo->photo_100_100, $mailData);
                $this->assertContains($photo->photo_150_150, $mailData);
                $this->assertContains($photo->photo_250_250, $mailData);
                $this->assertContains("Thanks!", $mailData);

                $broadcastData = $notification->toBroadcast($user)->data;

                $this->assertEquals("success", $broadcastData["status"]);
                $this->assertContains($photo->photo_100_100, $broadcastData["photo_100_100"]);
                $this->assertContains($photo->photo_150_150, $broadcastData["photo_150_150"]);
                $this->assertContains($photo->photo_250_250, $broadcastData["photo_250_250"]);

                return $notification->photo->id === $photo->id;
            }
        );
    }

    public function test_failed_notification_sent()
    {
        Storage::fake('public');
        Notification::fake();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $photo = factory(\App\Entities\Photo::class)->create([
            "original_photo" => UploadedFile::fake()->create('file.pdf', 100),
            "user_id" => $user->id
        ]);
        try {
            CropJob::dispatch($photo)->onConnection('sync');
        } catch (\Exception $e) {
        }

        $this->assertDatabaseHas("photos", [
            "status" => "FAIL"
        ]);

        Notification::assertSentTo(
            $user,
            ImageProcessingFailedNotification::class,
            function ($notification, $channels) use ($photo, $user) {
                $broadcastData = $notification->toBroadcast($user)->data;

                $this->assertEquals("fail", $broadcastData["status"]);

                return true;
            }
        );
    }
}
