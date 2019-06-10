<?php

namespace App\Http\Controllers;

use App\Elasticsearch\QueryHelper;
use App\Models\Setting;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Lang;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Build filter query
     *
     * @param array $searchFields
     *
     * @return array
     */
    protected function buildFilterQuery($searchFields)
    {
        $searchRules = [];
        $mustRules = [];
        $shouldRules = [];
        foreach ($searchFields as $field => $value) {
            if (! $value) {
                continue;
            }
            switch ($field) {
                case 'category':
                    $mustRules[] = QueryHelper::termQuery('category_id', $value);
                    break;
                case 'sub_category_1':
                    $mustRules[] = QueryHelper::termQuery('sub_category_1_id', $value);
                    break;
                case 'sub_category_2':
                    $mustRules[] = QueryHelper::termQuery('sub_category_2_id', $value);
                    break;
                case 'edited':
                    switch ($value) {
                        case 'edited':
                            $mustRules[] = QueryHelper::termQuery('modified', true);
                            break;
                        case 'not_edited':
                            $mustRules[] = QueryHelper::termQuery('modified', false);
                            break;
                        case 'category_modified':
                            $mustRules[] = QueryHelper::termQuery('category_modified', true);
                            break;
                    }
                    break;
                case 'image':
                case 'audio':
                    $mustRules[] = QueryHelper::termQuery($field, true);
                    break;
                case 'provinces':
                    if (count($value)) {
                        $provinceRules = [];
                        foreach ($value as $name) {
                            $provinceRules[] = QueryHelper::termQuery('province_name', $name);
                        }
                        $mustRules[] = [
                            'bool' => [
                                'should' => $provinceRules,
                                'minimum_should_match' => 1
                            ]
                        ];
                    }
                    break;
                case 'keyword':
                    $searchableFields = config('settings.impact_keyword_search_fields', []);
                    if (count($searchableFields)) {
                        foreach ($searchableFields as $field) {
                            $shouldRules[] = QueryHelper::matchQuery($field, $value);
                        }
                    }
                    break;
                case 'dateRange':
                    $fromDate = null;
                    $toDate = null;
                    if (isset($value['from']) && $value['from'] !== '') {
                        $fromDate = date('Y-m-d 00:00:00', strtotime($value['from']));
                    }

                    if (isset($value['to']) && $value['to'] !== '') {
                        $toDate = date('Y-m-d 23:59:59', strtotime($value['to']));
                    }

                    if ($fromDate || $toDate) {
                        $mustRules[] = QueryHelper::rangeQuery('report_date', $fromDate, $toDate);
                    }
                    break;
                case 'userGroup':
                    if ($value === 'null') {
                        $mustRules[] = [
                            'bool' => [
                                'must_not' => [
                                    'exists' => [
                                        'field' => 'user_group_id'
                                    ]
                                ]
                            ]
                        ];
                    } else {
                        $mustRules[] = QueryHelper::termQuery('user_group_id', $value);
                    }
                    break;

                case 'has_location':
                    $mustRules[] = QueryHelper::termQuery('has_location', $value);
                    break;
                case 'excluded':
                    $mustRules[] = QueryHelper::termQuery('excluded', $value);
                    break;
            }
        }

        if (count($mustRules)) {
            $searchRules['must'] = $mustRules;
        }
        if (count($shouldRules)) {
            $searchRules['should'] = $shouldRules;
            $searchRules['minimum_should_match'] = 1;
        }

        return $searchRules;
    }

    /**
     * Get provinces to filter
     *
     * @return array
     */
    protected function getProvincesForFilter()
    {
        $provinces = Setting::whereType('province')
            ->orderBy('sys_value', 'asc');
        if (Lang::locale() === 'km') {
            return $provinces->pluck('name_kh', 'name')
                ->toArray();
        }
        return $provinces->pluck('name', 'name')
            ->toArray();
    }
}
