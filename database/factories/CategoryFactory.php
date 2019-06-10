<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Category::class, function (Faker $faker) {
    return [
        'sys_value' => $faker->name,
        'name' => $faker->name,
        'name_kh' => $faker->name
    ];
});
