<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Http\UploadedFile;
use App\Entities\User;
use App\Jobs\CropJob;

class TaskTest extends TestCase
{
    use RefreshDatabase;

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
                $job->photo->id === $user->id
                &&
                $job->photo->original_photo === $fileName
            );
        });
    }
}
