<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Villager::class, function (Faker $faker) {
    $province = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', ['type' => 'province']);
    $user_group = createIfNoneExistsReturnFirstOtherwise('App\Models\UserGroup');
    return [
        'device_imei' => $faker->unique()->text,
        'name' => $faker->unique()->name,
        'province_id' => $province->id,
        'user_group_id' => $user_group->id,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
    ];
});
