<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'active' => true,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'password' => '$2y$10$Jcc3Akm53o.W2UVWRYL4YODmysbxlmBg5KLIIqwHI.xxnYlHgrNnS',
        'remember_token' => str_random(10),
        'role' => config('settings.user_roles[' . $faker->numberBetween(0, 6) . ']'),
        'username' => $faker->name
    ];
});
