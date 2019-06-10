<?php

use Faker\Generator as Faker;

$factory->define(App\Models\File::class, function (Faker $faker) {
    return [
        'file_name' => $faker->text,
        'file_type' => 'audio',
        'impact_id' => null,
        'is_imported' => 1,
        'import_date' => now(),
        'facebook_post' => 1,
        'latitude' => null,
        'longitude' => null,
        'report_date' => now(),
        'original_file_name' => null,
        'converted' => 0
    ];
});
