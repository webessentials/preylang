<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Impact;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;

class ImpactExport
{

    /** @var \App\Models\User $user */
    private $user;
    /** @var string $type */
    private $type;
    /** @var array $ids */
    private $ids;

    /**
     * Values like 0, true/false, null get omitted by Laravel-Excel
     * therefore we replace these with their string equivalents
     *
     *  see https://github.com/Maatwebsite/Laravel-Excel/issues/1665
     * @param array $values
     *
     * @return mixed
     */
    private static function convertDataTypesToString($values)
    {
        foreach ($values as $key => $value) {
            if (is_bool($value)) {
                $values[$key] = self::changeToYesNo($value);
            } elseif (is_numeric($value)) {
                $values[$key] = strval($value);
            } elseif ($value instanceof Carbon) {
                $values[$key] = $value->toDateTimeString();
            }
        }

        return $values;
    }

    /**
     * @param integer $value
     *
     * @return string
     */
    private static function changeToYesNo($value)
    {
        if ($value) {
            return 'Yes';
        }
        return 'No';
    }

    /**
     * @param Category $category
     *
     * @return string
     */
    private static function checkIfCategory($category)
    {
        if ($category instanceof Category) {
            return $category->name;
        }
        return '';
    }

    /**
     * @param Setting $setting
     *
     * @return string
     */
    private static function checkIfSetting($setting)
    {
        if ($setting instanceof Setting) {
            return $setting->name;
        }
        return '';
    }

    /**
     * exports data of one impact
     *
     * accepts null as an impact, that way we have only one place to maintain
     * both values and labels for the export. And we can get labels by calling
     * array_keys(self::collectExportData(null));
     *
     * @param  Impact $impact impact to be exported
     * @param  string $type
     *
     * @return array  map containing labels as keys and values to be exported
     */
    private static function collectExportData(Impact $impact = null, string $type = 'csv')
    {
        if ($impact) {
            $villager = $impact->villager;

            $impactNumber = $impact->impact_number;
            $categoryName = self::checkIfCategory($impact->getCategoryByRelationField('category'));
            $subCategory1 = self::checkIfCategory($impact->getCategoryByRelationField('sub_category_1'));
            $subCategory2 = self::checkIfCategory($impact->getCategoryByRelationField('sub_category_2'));
            $subCategory3 = self::checkIfCategory($impact->getCategoryByRelationField('sub_category_3'));
            $subCategory4 = self::checkIfCategory($impact->getCategoryByRelationField('sub_category_4'));
            $subCategory5 = self::checkIfCategory($impact->getCategoryByRelationField('sub_category_5'));
            $permit = $impact->categories['permit'];
            $userGroup = $villager ? $villager->userGroup->name : '';
            $note = $impact->note;
            $noteKh = $impact->note_kh;
            $patrollerNote = $impact->patroller_note;
            $latitude = $impact->latitude;
            $longitude = $impact->longitude;
            $deviceImei = $villager ? $villager->device_imei : '';
            $numberOfItems = $impact->number_of_items;
            $villagerName = $villager ? $villager->name : '';
            $employer = $impact->employer;
            $license = $impact->license;
            $agreement = $impact->agreement;
            $byVisual = $impact->by_visual;
            $byAudio = $impact->by_audio;
            $byTrack = $impact->by_track;
            $facebook = $impact->facebook;
            $audioString = '';
            foreach ($impact->audios as $audio) {
                $audioString = $audioString . env('APP_URL') . '/files/' . $audio->file_name . "\n";
            }
            $imageString = '';
            foreach ($impact->images as $image) {
                $imageString = $imageString . env('APP_URL') . '/files/' . $image->file_name . "\n";
            }
            $excluded = $impact->excluded;
            $modified = $impact->modified;
            $reportTo = $impact->report_to;
            $reportDate = $impact->report_date;
            $createdAt = $impact->created_at;
            $victimType = self::checkIfSetting($impact->victimType);
            $reason = self::checkIfSetting($impact->reason);
            $offender = self::checkIfSetting($impact->offender);
            $location = $impact->location;
            $threatening = self::checkIfSetting($impact->threatening);
            $witness = $impact->witness;
            $designation = self::checkIfSetting($impact->designation);
            $proof = self::checkIfSetting($impact->proof);
        }

        return self::convertDataTypesToString([
            'No' => isset($impactNumber) ? $impactNumber : '',
            'Category' => isset($categoryName) ? $categoryName : '',
            'Subcategory1' => isset($subCategory1) ? $subCategory1 : '',
            'Subcategory2' => isset($subCategory2) ? $subCategory2 : '',
            'Subcategory3' => isset($subCategory3) ? $subCategory3 : '',
            'Subcategory4' => isset($subCategory4) ? $subCategory4 : '',
            'Leaf Category' => isset($subCategory5) ? $subCategory5 : '',
            'Permit' => isset($permit) ? $permit : '',
            'User Group' => isset($userGroup) ? $userGroup : '',
            'Note' => isset($note) ? $note : '',
            'Note KH' => isset($noteKh) ? $noteKh : '',
            'Patroller\'s Note' => isset($patrollerNote) ? $patrollerNote : '',
            'Latitude' => isset($latitude) ? $latitude : '',
            'Longitude' => isset($longitude) ? $longitude : '',
            'Phone Serial' => isset($deviceImei) ? $deviceImei : '',
            'Number of items' => isset($numberOfItems) ? $numberOfItems : '',
            'Villager Id' => isset($villagerName) ? $villagerName : '',
            'Employer' => isset($employer) ? $employer : '',
            'License' => isset($license) ? $license : '',
            'Agreement' => isset($agreement) ? self::changeToYesNo($agreement) : '',
            'By Visual' => isset($byVisual) ? self::changeToYesNo($byVisual) : '',
            'By Audio' => isset($byAudio) ? self::changeToYesNo($byAudio) : '',
            'By Track' => isset($byTrack) ? self::changeToYesNo($byTrack) : '',
            'Facebook' => isset($facebook) ? $facebook : '',
            'Audio' => isset($audioString) ? $audioString : '',
            'Images' => isset($imageString) ? $imageString : '',
            'Exclude' => isset($excluded) ? self::changeToYesNo($excluded) : '',
            'Edited' => isset($modified) ? self::changeToYesNo($modified) : '',
            'Report To' => isset($reportTo) ? $reportTo : '',
            'Reported Date' => isset($reportDate) ? $reportDate : '',
            'Created At' => isset($createdAt) ? $createdAt : '',
            'Victim Type' => isset($victimType) ? $victimType : '',
            'Reason/Cause by' => isset($reason) ? $reason : '',
            'Offender' => isset($offender) ? $offender : '',
            'Location' => isset($location) ? $location : '',
            'Threat via' => isset($threatening) ? $threatening : '',
            'Witness' => isset($witness) ? $witness : '',
            'Responding action' => isset($designation) ? $designation : '',
            'Proof' => isset($proof) ? $proof : ''
        ]);
    }

    /**
     * ImpactExport constructor.
     *
     * @param \App\Models\User $user
     * @param string $type
     * @param array $ids
     */
    public function __construct(User $user, string $type, $ids)
    {
        $this->user = $user;
        $this->type = $type;
        $this->ids = $ids;
    }

    /**
     * @return \App\Models\Impact|\Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $ids = $this->ids;
        if ($this->user->can('exportAll', Impact::class)) {
            return (isset($ids) && count($ids) > 0) ? Impact::query()->whereIn('id', $ids) : Impact::query();
        } else {
            $impactsFilteredByUserGroup = Impact::join('villagers', 'impacts.villager_id', '=', 'villagers.id')
                ->where('villagers.user_group_id', $this->user->user_group_id);
            if ((isset($ids) && count($ids) > 0)) {
                return $impactsFilteredByUserGroup->whereIn('impacts.id', $ids);
            } else {
                return $impactsFilteredByUserGroup;
            }
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return array_keys(self::collectExportData());
    }

    /**
     * @param mixed $impact
     *
     * @return array
     */
    public function map($impact): array
    {
        return array_values(self::collectExportData($impact, $this->type));
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
