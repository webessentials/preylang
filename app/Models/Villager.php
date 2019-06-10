<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Laravel\Passport\HasApiTokens;

/**
 * App\Models\Villager
 *
 * @property int $id
 * @property string $device_imei
 * @property string $name
 * @property int|null $province_id
 * @property int|null $user_group_id
 * @property int $active
 * @property string|null $password
 * @property string|null $access_token
 * @property string|null $token_expiration_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Setting|null $province
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereDeviceImei($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereProvinceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereTokenExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereUserGroupId($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Villager onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Villager whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Villager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Villager withoutTrashed()
 */
class Villager extends AbstractModel
{
    use SoftDeletes;
    use Sortable;
    use HasApiTokens;

    /**
     * @var array
     */
    protected $fillable = [
        'device_imei',
        'name',
        'province_id',
        'active',
        'user_group_id',
        'password',
        'access_token',
        'token_expiration_date'
    ];

    /**
     * @var array
     */
    public $sortable = ['device_imei', 'name'];

    /**
     * @return array
     */
    public static function createRule()
    {
        return [
            'device_imei' => 'required|max:255|unique:villagers,device_imei',
            'name' => 'max:255|min:3',
            'password' => 'required|max:255|min:6',
            'province_id' => 'required',
            'user_group_id' => 'required'
        ];
    }

    /**
     * @return array
     */
    public static function updateRule()
    {
        return [
            'device_imei' => 'required|max:255|unique:villagers',
            'name' => 'max:255|min:3',
            'password' => 'max:255|min:6',
            'province_id' => 'required',
            'user_group_id' => 'required'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userGroup()
    {
        return $this->belongsTo('App\Models\UserGroup', 'user_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo('App\Models\Setting', 'province_id');
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getNextId($key)
    {
        $villager = Villager::withTrashed()
            ->where('name', 'like', '%' . $key . '-%')
            ->orderBy('name', 'DESC')
            ->first();
        if ($villager) {
            $lastVillagerId = $villager->name;
            $lastVillagerId = explode('-', $lastVillagerId)[1];
            $newVillagerId = sprintf("%02d", ((int) $lastVillagerId + 1));
            return $key . '-' . $newVillagerId;
        } else {
            return $key . '-01';
        }
    }
}
