<?php
namespace App\Helpers;

use App\Elasticsearch\QueryHelper;
use App\Models\Impact;
use App\Models\RawImpact;
use App\Models\Setting;
use App\Models\User;
use App\Models\Villager;

class ImpactHelper
{
    /**
     * @param array $data
     * @param boolean $migrating
     *
     * @return \App\Models\Impact|null
     */
    public static function saveImpact($data, $migrating = false)
    {
        $rawImpact = null;
        if (isset($data['impact_number'])) {
            // Get raw impact id from impact number.
            $rawImpactId = Impact::getImpactNumberFromFormat($data['impact_number']);
            if ($rawImpactId) {
                $rawImpact = RawImpact::find($rawImpactId);
            }
        }
        if (is_null($rawImpact)) {
            $rawImpact = self::saveRawImpact($data, $migrating);
        }
        if ($rawImpact) {
            $impact = new Impact();
            $impact->raw_impact_id = $rawImpact->id;
            $impact->impact_number = Impact::formatImpactNumber($rawImpact->id);
            $impact->categories = $rawImpact->categories;
            $impact->villager_id = $rawImpact->villager_id;
            $impact->excluded_reason_id = $rawImpact->excluded_reason_id;
            $impact->reason_id = $rawImpact->reason_id;
            $impact->victim_type_id = $rawImpact->victim_type_id;
            if (isset($data['offender'])) {
                $impact->offender_id = self::getSettingTypeId('offender', $data['offender'], $migrating);
            }
            if (isset($data['threatening'])) {
                $impact->threatening_id = self::getSettingTypeId('threatening', $data['threatening'], $migrating);
            }
            if (isset($data['threatening'])) {
                $impact->proof_id = self::getSettingTypeId('proof', $data['proof'], $migrating);
            }
            if (isset($data['designation'])) {
                $impact->designation_id = self::getSettingTypeId('designation', $data['designation'], $migrating);
            }
            if (isset($data['persistence_object_identifier'])) {
                $impact->persistence_object_identifier = $data['persistence_object_identifier'];
            }
            unset($data['category']);

            // Ignore Elastic indexing while migrating.
            if ($migrating) {
                $impact->timestamps = false;
                Impact::withoutSyncingToSearch(function () use ($impact, $data) {
                    $impact->createUpdateRecord($data);
                });
            } else {
                $impact->createUpdateRecord($data);
            }
            return $impact;
        }
        return null;
    }

    /**
     * @param array $data
     * @param boolean $migrating
     *
     * @return \App\Models\RawImpact|null
     */
    public static function saveRawImpact($data, $migrating = false)
    {
        if (isset($data['persistence_object_identifier'])) {
            return self::getRawImpact($data['persistence_object_identifier'], $migrating);
        }
        $villagerId = self::getVillagerId($data['device_imei']);
        if (! is_null($villagerId)) {
            $data['villager_id'] = $villagerId;
            unset($data['device_imei']);
            if (isset($data['category'])) {
                $data['categories'] = CategoryHelper::getCategories($data);
                unset($data['category']);
            }
            if (isset($data['excluded_reason'])) {
                $data['excluded_reason_id'] = self::getSettingTypeId(
                    'excludedReason',
                    $data['excluded_reason'],
                    $migrating
                );
                unset($data['excluded_reason']);
            }
            if (isset($data['reason'])) {
                $data['reason_id'] = self::getSettingTypeId('reason', $data['reason'], $migrating);
                unset($data['reason']);
            }
            if (isset($data['victim_type'])) {
                $data['victim_type_id'] = self::getSettingTypeId('victimType', $data['victim_type'], $migrating);
                unset($data['victim_type']);
            }

            $data = array_filter($data);
            if ($migrating) {
                /* @var RawImpact $rawImpact */
                $rawImpact = RawImpact::firstOrNew(['impact' => $data['impact']], $data);
                $rawImpact->timestamps = false;
                $rawImpact->id = $data['id'];
                RawImpact::withoutSyncingToSearch(
                    function () use ($rawImpact) {
                        $rawImpact->save();
                    }
                );
                return $rawImpact;
            }

            return RawImpact::create($data);
        }
        return null;
    }

    /**
     * @param string $deviceImei
     *
     * @return int|null
     */
    public static function getVillagerId($deviceImei)
    {
        $record = Villager::select('id')->where('device_imei', $deviceImei)->first();
        if (empty($record)) {
            return null;
        }
        return $record->id;
    }

    /**
     * @param string $type
     * @param string $value
     * @param boolean $migrating
     *
     * @return int|null
     */
    public static function getSettingTypeId($type, $value, $migrating = false)
    {
        $field = $migrating === true ? 'persistence_object_identifier' : 'name';
        $record = Setting::select('id')
            ->where('type', $type)
            ->where($field, $value)
            ->first();
        if (empty($record)) {
            return null;
        }
        return $record->id;
    }

    /**
     * @param string $value
     *
     * @return int|null
     */
    public static function getUserIdByIdentifier($value)
    {
        $record = User::select('id')
            ->where('persistence_object_identifier', $value)->first();
        if (empty($record)) {
            return null;
        }
        return $record->id;
    }

    /**
     * @param string $value
     * @param bool $migrating
     *
     * @return null
     */
    public static function getImpactId($value, $migrating = false)
    {
        $field = $migrating === true ? 'persistence_object_identifier' : 'impact_number';
        $record = Impact::select('id')
            ->where($field, $value)->first();
        if (empty($record)) {
            return null;
        }
        return $record->id;
    }

    /**
     * @param string $value
     * @param boolean $migrating
     *
     * @return RawImpact|null
     */
    public static function getRawImpact($value, $migrating = false)
    {
        $field = $migrating === true ? 'impact' : 'id';
        $record = RawImpact::where($field, $value)->first();
        if ($record) {
            return $record;
        }
        return null;
    }

    /**
     * Get default search fields
     *
     * @return array
     */
    public static function defaultSearchFields()
    {
        return [
            'provinces' => '',
            'category' => '',
            'sub_category_1' => '',
            'sub_category_2' => '',
            'dateRange' => [
                'from' => '',
                'to' => ''
            ],
            'keyword' => '',
            'edited' => '',
            'image' => '',
            'audio' => '',
            'userGroup' => '',
            'active' => true,
            'excluded' => ''
        ];
    }

    /**
     * Build filter query
     *
     * @param array $searchFields
     *
     * @return array
     */
    public static function buildFilterQuery($searchFields)
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
                case 'excluded':
                    if ($value == 'no') {
                        $mustRules[] = QueryHelper::termQuery('excluded', false);
                    }
                    break;
                case 'active':
                    $mustRules[] = QueryHelper::termQuery('active', true);
                    break;
                case 'image':
                case 'audio':
                    $mustRules[] = QueryHelper::termQuery($field, true);
                    break;
                case 'provinces':
                    if (count($value)) {
                        $provinceRules = [];
                        foreach ($value as $name) {
                            $provinceRules[] = QueryHelper::termQuery('province_name.raw', $name);
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
}
