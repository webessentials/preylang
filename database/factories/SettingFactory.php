<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Setting::class, function (Faker $faker) {
    $text = $faker->text;
    return [
        'name' => $text,
        'type' => $faker->text,
        'sys_value' => strtolower($text),
        'read_only' => 1
    ];
});
