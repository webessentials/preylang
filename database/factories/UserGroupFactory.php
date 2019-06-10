<?php

use Faker\Generator as Faker;

$factory->define(App\Models\UserGroup::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name,
        'name_kh' => 'kh_' . $faker->unique()->name,
        'created_at' => now(),
        'updated_at' => null
    ];
});
