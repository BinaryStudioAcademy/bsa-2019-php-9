<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Entities\Photo;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Photo::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\Entities\User::class)->create()->id,
        'original_photo' => '',
        'photo_100_100' => '',
        'photo_150_150' => '',
        'photo_250_250' => '',
        'status' => 'UPLOADED'
    ];
});
