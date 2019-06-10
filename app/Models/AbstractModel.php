<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\UserGroupRequest\CreateUserGroupRequest;
use App\Http\Requests\UserGroupRequest\UpdateUserGroupRequest;

/**
 * App\Models\AbstractModel
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AbstractModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AbstractModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AbstractModel query()
 * @mixin \Eloquent
 */
class AbstractModel extends Model
{
    /** @var array $guarded */
    protected $guarded = ['id'];

    /**
     * Create or Update Record
     *
     * @param array|UpdateUserGroupRequest|CreateUserGroupRequest $input
     * @return void
     */
    public function createUpdateRecord($input)
    {
        foreach ($this->fillable as $field) {
            if (isset($input[$field])) {
                $this->{$field} = $input[$field];
            }
        }
        $this->save();
    }
}
