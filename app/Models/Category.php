<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Lang;
use Kyslik\ColumnSortable\Sortable;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $name_kh
 * @property int $level
 * @property int|null $parent_id
 * @property int $is_last
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $parents
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category sortable($defaultParameters = null)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereIsLast($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereNameKh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends AbstractModel
{
    use Sortable;

    /** @var array $fillable */
    protected $fillable = [
        'sys_value', 'name', 'name_kh', 'level', 'parent_id', 'modify_child', 'type'
    ];

    /** @var array $sortable */
    public $sortable = ['name', 'level'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(self::class, 'id', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @return array
     */
    public function getParents()
    {
        $parents = [];
        if ($this->parent_id) {
            $parentCategory = Category::find($this->parent_id);
            if ($parentCategory instanceof Category) {
                array_unshift($parents, $parentCategory);
                $parents = array_merge($parentCategory->getParents(), $parents);
            } else {
                return [];
            }
        }
        return $parents;
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function formatSysValue($value)
    {
        // Remove apostrophe first to correctly apply camel case.
        $value = str_replace('\'', '', $value);
        // Replace all special characters to space to apply camel case.
        $value = preg_replace('/\W+/', ' ', $value);
        $value = ucwords($value);
        // Remove space and make it lower camel case.
        return preg_replace('/\s+/', '', lcfirst($value));
    }

    /**
     * @param Builder $query
     * @param int $level
     * @param string $direction
     * @return mixed
     */
    private static function joinAndSortByLevel($query, $level, $direction)
    {
        $orderBy = $level === 0 ? 'categories' : 'child' . $level;
        $name = Lang::locale() === 'km' ? 'name_kh' : 'name';

        $query
            ->select(
                "categories.{$name} as name",
                "child1.{$name} as child1",
                "child2.{$name} as child2",
                "child3.{$name} as child3",
                "child4.{$name} as child4",
                "child5.{$name} as child5",
                'categories.id as id',
                'child1.id as id1',
                'child2.id as id2',
                'child3.id as id3',
                'child4.id as id4',
                'child5.id as id5'
            )
            ->leftjoin('categories as child1', 'child1.parent_id', '=', 'categories.id')
            ->leftjoin('categories as child2', 'child2.parent_id', '=', 'child1.id')
            ->leftjoin('categories as child3', 'child3.parent_id', '=', 'child2.id')
            ->leftjoin('categories as child4', 'child4.parent_id', '=', 'child3.id')
            ->leftjoin('categories as child5', 'child5.parent_id', '=', 'child4.id')
            ->where('categories.level', '=', '0')
            ->orderBy($orderBy . '.' . $name, $direction);
        // Sort by root?
        if ($level > 0) {
            $query->orderBy('categories.' . $name, $direction);
        }
        // Sort up to selection.
        for ($i = 1; $i < $level; $i++) {
            $query->orderBy("child{$i}.{$name}", $direction);
        }
        // Sort from selection to end!
        $startSubCategory = $level > 0 ? $level : 1;
        for ($i = $startSubCategory; $i < 6; $i++) {
            $query->orderBy("child{$i}.{$name}", $direction);
        }
        return $query;
    }

    /**
     * @param Builder $query
     * @param string $direction asc|desc
     * @return mixed
     *
     * used to sort the categories table by the root level
     */
    public function level0Sortable($query, $direction)
    {
        return self::joinAndSortByLevel($query, 0, $direction);
    }

    /**
     * @param Builder $query
     * @param string $direction asc|desc
     * @return mixed
     *
     * used to sort the categories table by the first sub-level
     */
    public function level1Sortable($query, $direction)
    {
        return self::joinAndSortByLevel($query, 1, $direction);
    }

    /**
     * @param Builder $query
     * @param string $direction asc|desc
     * @return mixed
     *
     * used to sort the categories table by the second sub-level
     */
    public function level2Sortable($query, $direction)
    {
        return self::joinAndSortByLevel($query, 2, $direction);
    }

    /**
     * @param Builder $query
     * @param string $direction asc|desc
     * @return mixed
     *
     * used to sort the categories table by the thirs sub-level
     */
    public function level3Sortable($query, $direction)
    {
        return self::joinAndSortByLevel($query, 3, $direction);
    }

    /**
     * @param Builder $query
     * @param string $direction asc|desc
     * @return mixed
     *
     * used to sort the categories table by the fourth sub-level
     */
    public function level4Sortable($query, $direction)
    {
        return self::joinAndSortByLevel($query, 4, $direction);
    }

    /**
     * @param Builder $query
     * @param string $direction asc|desc
     * @return mixed
     *
     * used to sort the categories table by the fifth sub-level
     */
    public function level5Sortable($query, $direction)
    {
        return self::joinAndSortByLevel($query, 5, $direction);
    }
}
