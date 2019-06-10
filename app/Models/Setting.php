<?php

namespace App\Models;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $name
 * @property string|null $name_kh
 * @property string|null $sys_value
 * @property string $type
 * @property int|null $sorting
 * @property int $read_only
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Impact[] $offenderImpacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Impact[] $proofImpacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Impact[] $reasonImpacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Villager[] $villagers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereNameKh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereReadOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereSorting($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereSysValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Impact[] $designationImpacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Impact[] $threateningImpacts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Impact[] $victimTypeImpacts
 */
class Setting extends AbstractModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'name', 'name_kh', 'sys_value', 'type', 'sorting', 'read_only'
    ];

    /**
     * @return array
     */
    public static function returnCreateProvinceRules()
    {
        return [
            'name' => 'required|max:255|min:5|uniqueCombo:settings,type',
            'name_kh' => 'required|max:255|min:2',
            'sys_value' => 'required|max:255|unique:settings|min:2'
        ];
    }

    /**
     * @return array
     */
    public static function returnUpdateProvinceRules()
    {
        return [
            'name' => 'required|max:255|min:5|uniqueCombo:settings,type',
            'name_kh' => 'required|max:255|min:2',
        ];
    }

    /**
     * @return array
     */
    public static function returnCreateUpdateSettingRules()
    {
        return [
            'name' => 'required|max:255|min:3|uniqueCombo:settings,type'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function villagers()
    {
        return $this->hasMany('App\Models\Villager', 'province_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function proofImpacts()
    {
        return $this->hasMany('App\Models\Impact', 'proof_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reasonImpacts()
    {
        return $this->hasMany('App\Models\Impact', 'reason_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offenderImpacts()
    {
        return $this->hasMany('App\Models\Impact', 'offender_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function victimTypeImpacts()
    {
        return $this->hasMany('App\Models\Impact', 'victim_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function designationImpacts()
    {
        return $this->hasMany('App\Models\Impact', 'designation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threateningImpacts()
    {
        return $this->hasMany('App\Models\Impact', 'threatening_id');
    }
}
