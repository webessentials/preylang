<?php

return [
    'date_time_format' => 'Y-m-d H:i:s',
    'file_path' => 'app/preylang',
    'import_path' => [
        'category' => [
            'preylang' => 'database/import/category/preylang.json',
            'preylang_translation' => 'database/import/category/preylang_translation.json'
        ],
    ],
    'graphs' => [
        'donut' => 'Donut',
        'line' => 'Line',
        'bar' => 'Bar'
    ],
    'graph_settings' => [
        'default_start_date' => '2014-11-01'
    ],
    'user_roles' => [
        'superAdmin',
        'admin',
        'superDataManager',
        'dataManager',
        'patroller'
    ],
    'user_super_roles' => [
        'superAdmin' => 'superAdmin',
        'superDataManager' => 'superDataManager'
    ],
    'user_low_level_roles' => [
        'admin',
        'dataManager',
        'patroller'
    ],
    'default_user_group' => [
        'name' => 'Prey Lang',
        'name_kh' => 'ព្រៃឡង់'
    ],
    'record_per_page' => '15',
    'villager_role'=> 'patroller',
    'user_roles_mappings' => [
        'Prey.Lang:Admin' => 'superAdmin',
        'Prey.Lang:Article19' => 'admin',
        'Prey.Lang:Manager' => 'superDataManager',
        'Prey.Lang:Patroller' => 'patroller',
        'Prey.Lang:User' => 'patroller',
        'Prey.Lang:Owner' => 'superAdmin'
    ],
    'setting_types' => [
        'reason',
        'offender',
        'victimType',
        'proof',
        'designation',
        'threatening',
        'excludedReason',
        'province'
    ],
    'history_types' => [
        'impact' =>'impact',
        'user' => 'user'
    ],
    'permits' => [
        'No permit' => 'No permit',
        'License-Do-Not-Know' => 'License-Do-Not-Know',
        "Don't know" => "Don't know",
        'Permit' => 'Permit',
        'License-Permit' => 'License-Permit',
        'License-No-Permit' => 'License-No-Permit'
    ],
    'category_field_mapping' => [
        'category' => 'category',
        'sub_category_1' => 'subCategory1',
        'sub_category_2' => 'subCategory2',
        'sub_category_3' => 'subCategory3',
        'sub_category_4' => 'subCategory4',
        'sub_category_5' => 'subCategory5',
        'permit' => 'permit',
    ],
    'category_levels' => [
        'category' => 0,
        'sub_category_1' => 1,
        'sub_category_2' => 2,
        'sub_category_3' => 3,
        'sub_category_4' => 4,
        'sub_category_5' => 5,
    ],
    'impact_keyword_search_fields' => ['impact_number', 'category', 'sub_category_1', 'sub_category_2', 'villager_id'],
    'records_per_page' => ['15', '25', '50', '100'],
    'convert_files_source' => 'tests/Feature/Assets/files-for-test',
    'reporting_categories' => [
        'threat',
        'violence'
    ],
    'logging_categories' => [
        'logging'
    ],
    'dont_know_categories' => [
        'unknown',
        'dontKnow'
    ],
    'interaction_no_categories' => [
        'interactionNo'
    ],
    'other_categories' => [
        'other'
    ],
    'global_user_group_session_name' => 'user_group_filter',
    'excel_record_highlight_color_code' => "93cbf9"
];
