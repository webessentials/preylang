<?php

use App\Helpers\ImpactHelper;
use App\Models\Impact;
use Faker\Generator as Faker;

$factory->define(Impact::class, function (Faker $faker) {
    $rawImpact = factory(App\Models\RawImpact::class, 1)->create()->first();

    $designation = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'type' => config('settings.setting_types.4'),
        'read_only' => false
    ]);

    $offenderNames = ['Company', 'Other', 'Police chief'];
    $randomOffenderName = $faker->randomElement($offenderNames);
    $offender = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'name' => $randomOffenderName,
        'type' => 'offender',
        'sys_value' => strtolower($randomOffenderName)
    ]);

    $proof = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'type' => config('settings.setting_types.3'),
        'read_only' => false
    ]);

    $threatening = createIfNoneExistsReturnFirstOtherwise('App\Models\Setting', [
        'type' => config('settings.setting_types.5'),
        'read_only' => false
    ]);

    if ($rawImpact) {
        return [
            'active' => true,
            'agreement' => $faker->numberBetween(0, 1),
            'burned_wood' => $faker->numberBetween(0, 1),
            'by_audio' => $faker->numberBetween(0, 1),
            'by_track' => $faker->numberBetween(0, 1),
            'by_visual' => $faker->numberBetween(0, 1),
            'categories' => $rawImpact->categories,
            'category_modified' => $faker->numberBetween(0, 1),
            'created_at' => $rawImpact->created_at,
            'designation_id' => $designation->id,
            'employer' => $faker->name,
            'excluded' => $faker->numberBetween(0, 1),
            'excluded_note' => $faker->text,
            'excluded_reason_id' => $rawImpact->excluded_reason_id,
            'impact_number' => Impact::formatImpactNumber($rawImpact->id),
            'latitude' => $rawImpact->latitude,
            'longitude' => $rawImpact->longitude,
            'modified' => true,
            'name' => $faker->name,
            'note' => $faker->text,
            'note_kh' => $faker->text,
            'number_of_items' => $rawImpact->number_of_items,
            'offender_id' => ImpactHelper::getSettingTypeId('offender', $offender),
            'patroller_note' => $faker->text,
            'proof_id' => ImpactHelper::getSettingTypeId('proof', $proof),
            'raw_impact_id' => $rawImpact->id,
            'reason_id' => $rawImpact->reason_id,
            'report_date' => $rawImpact->report_date,
            'report_to' => $rawImpact->report_to,
            'threatening_id' => ImpactHelper::getSettingTypeId('threatening', $threatening),
            'updated_at' => $faker->date(config('settings.date_time_format')),
            'victim_type_id' => $rawImpact->victim_type_id,
            'villager_id' => $rawImpact->villager_id,
            'witness' => $faker->name
        ];
    } else {
        return [];
    }
});
