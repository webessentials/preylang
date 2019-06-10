<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\RawImpact::class, function (Faker $faker) {
    $villager = createIfNoneExistsReturnFirstOtherwise('App\Models\Villager');

    $category = createIfNoneExistsReturnFirstOtherwise('App\Models\Category');
    $subCategory1 = createIfNoneExistsReturnFirstOtherwise('App\Models\Category', ['parent_id' => $category->id]);
    $subCategory2 = createIfNoneExistsReturnFirstOtherwise('App\Models\Category', ['parent_id' => $subCategory1->id]);
    $subCategory3 = createIfNoneExistsReturnFirstOtherwise('App\Models\Category', ['parent_id' => $subCategory2->id]);
    $subCategory4 = createIfNoneExistsReturnFirstOtherwise('App\Models\Category', ['parent_id' => $subCategory3->id]);
    $subCategory5 = createIfNoneExistsReturnFirstOtherwise('App\Models\Category', ['parent_id' => $subCategory4->id]);

    $excludedReason = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'type' => config('settings.setting_types.6'),
        'read_only' => false
    ]);

    $reason = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'type' => config('settings.setting_types.0'),
        'read_only' => false
    ]);

    $victimType = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'type' => config('settings.setting_types.2'),
        'read_only' => false
    ]);

    return [
        'by_audio' => $faker->numberBetween(0, 1),
        'by_track' => $faker->numberBetween(0, 1),
        'by_visual' => $faker->numberBetween(0, 1),
        'categories' => [
            'permit' => '',
            'category' => $category->id,
            'sub_category_1' => $subCategory1->id,
            'sub_category_2' => $subCategory2->id,
            'sub_category_3' => $subCategory3->id,
            'sub_category_4' => $subCategory4->id,
            'sub_category_5' => $subCategory5->id,
        ],
        'created_at' => $faker->date(config('settings.date_time_format')),
        'excluded' => $faker->numberBetween(0, 1),
        'excluded_reason_id' => $excludedReason->id,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'note' => $faker->text,
        'number_of_items' => $faker->randomDigit,
        'patroller_note' => $faker->text,
        'reason_id' => $reason->id,
        'report_date' => $faker->date(config('settings.date_time_format')),
        'report_to' => $faker->name,
        'victim_type_id' => $victimType->id,
        'villager_id' => $villager->id,
    ];
});
