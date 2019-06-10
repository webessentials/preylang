<?php
namespace App\Helpers;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class CategoryHelper
{
    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function getDecisionTreeFilePath($fileName = 'preylang')
    {
        return Config::get('settings.import_path.category.' . $fileName);
    }

    /**
     * @param string|null $filePath
     * @param string $key
     *
     * @return array|mixed
     */
    public static function getCategoriesFromDecisionTree($filePath = null, $key = 'categories')
    {
        $data = [];
        if (is_null($filePath)) {
            $filePath = self::getDecisionTreeFilePath();
        }
        if ($filePath) {
            $data = \GuzzleHttp\json_decode(File::get($filePath), true);
            if (isset($data[$key])) {
                return $data[$key];
            }
        }
        return $data;
    }

    /**
     * @return void
     */
    public static function importCategories()
    {
        $items = self::getCategoriesFromDecisionTree();
        if ($items) {
            self::saveCategoryAndChild($items);
            self::updateCategoryTranslation();
        }
    }

    /**
     * Update existing categories with Khmer translation
     *
     * @return void
     */
    private static function updateCategoryTranslation()
    {
        // Update translation only.
        $categories = self::getCategoriesFromDecisionTree(self::getDecisionTreeFilePath('preylang_translation'));
        self::updateTranslation($categories);

        // Create natural resource category only.
        $resources = self::getCategoriesFromDecisionTree(
            self::getDecisionTreeFilePath('preylang_translation'),
            'natural_resources'
        );
        foreach ($resources as $resource) {
            $data = [
                'name' => $resource['name'],
                'level' => -1,
                'sys_value' => Category::formatSysValue($resource['name']),
                'type' => $resource['type'],
            ];
            Category::updateOrCreate(
                $data,
                ['name_kh' => empty($resource['khName']) ? $resource['name'] : $resource['khName']]
            );
        }
    }

    /**
     * @param array $items
     *
     * @return void
     */
    private static function updateTranslation($items)
    {
        foreach ($items as $item) {
            if (isset($item['name']) && isset($item['khName']) && ! empty($item['khName'])) {
                Category::where('name', $item['name'])->update(['name_kh' => $item['khName']]);
            }
            if (isset($item['categories']) && count($item['categories']) > 0) {
                self::updateTranslation($item['categories']);
            }
        }
    }

    /**
     * @param array $items
     * @param Category|null $parent
     * @return void
     */
    public static function saveCategoryAndChild($items, $parent = null)
    {
        foreach ($items as $item) {
            $parentId = $parent instanceof Category ? $parent->id : null;
            $category = self::saveCategory($item, $parentId);
            if (isset($item['allowModifySub']['subCategories'])) {
                $item['subCategories'] = $item['allowModifySub']['subCategories'];
            }
            if (isset($item['subCategories']) && count($item['subCategories']) > 0) {
                self::saveCategoryAndChild($item['subCategories'], $category);
            }
        }
    }

    /**
     * @param array $item
     * @param int|null $parentId
     *
     * @return Category
     */
    public static function saveCategory($item, $parentId = null)
    {
        $level = isset($item['subLevel']) ? $item['subLevel'] : 0;
        if ($parentId) {
            $parent = Category::find($parentId);
            if ($parent instanceof Category) {
                // When direct parent allows modify sub.
                if ($parent->modify_child && $level === $parent->level) {
                    $parentId = $parent->id;
                } elseif (is_object($parent->parent) && $parent->parent->modify_child && $level === $parent->level) {
                    $parentId = $parent->parent_id;
                }
            }
        }
        $data = [
            'name' => $item['name'],
            'sys_value' => $item['sysValue'],
            'parent_id' => $parentId,
            'level' => $level,
            'modify_child' => isset($item['allowModifySub']['new']) ? $item['allowModifySub']['new'] : false,
        ];
        return Category::updateOrCreate($data, ['name_kh' => $item['name']]);
    }

    /**
     * @param int $level
     * @param int $parentId
     * @param boolean $allowEmpty
     *
     * @return array
     */
    public static function getCategoriesByLevel($level = 0, $parentId = null, $allowEmpty = false)
    {
        $field = 'name';
        if (Auth::check()) {
            $currentLocale = Auth::user()->language_key;
            if ($currentLocale === 'km') {
                $field = 'name_kh';
            }
        }
        $result = Category::where('level', $level)
            ->where('parent_id', $parentId)
            ->orderBy('name', 'asc')
            ->pluck($field, 'id')
            ->toArray();
        if ($allowEmpty && !empty($result)) {
            $result = ['' => ''] + $result;
        }
        return $result;
    }

    /**
     * @param array $sysValues
     * @param int $level
     *
     * @return array
     */
    public static function getCategoriesBySysValues($sysValues, $level = null)
    {
        $query = Category::select('id')->whereIn('sys_value', $sysValues);

        if ($level) {
            $query = $query->where('level', $level);
        }

        return $query->get();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function getCategories($data)
    {
        $data = (array) $data;
        $category = self::getCategory($data, 'category');
        $sub_category_1 = self::getCategory($data, 'sub_category_1', $category);
        $sub_category_2 = self::getCategory($data, 'sub_category_2', $sub_category_1);
        $sub_category_3 = self::getCategory($data, 'sub_category_3', $sub_category_2);
        $sub_category_4 = self::getCategory($data, 'sub_category_4', $sub_category_3);
        $sub_category_5 = self::getCategory($data, 'sub_category_5', $sub_category_4);
        return [
                'category' => $category,
                'sub_category_1' => $sub_category_1,
                'sub_category_2' => $sub_category_2,
                'sub_category_3' => $sub_category_3,
                'sub_category_4' => $sub_category_4,
                'sub_category_5' => $sub_category_5,
                'permit' => self::getValue($data, 'permit'),
            ];
    }

    /**
     * @param array $data
     * @param string $field
     * @param string|int|null $parent
     *
     * @return string|int
     */
    public static function getCategory($data, $field, $parent = null)
    {
        if (! isset($data[$field])) {
            return null;
        }
        $existingCategory = Category::find($data[$field]);
        if ($existingCategory instanceof Category) {
            return $existingCategory->id;
        }
        $categoryLevels = Config::get('settings.category_levels');
        $value = self::getValue($data, $field);
        $sysValue = Category::formatSysValue($value);
        if (! empty(trim($value))) {
            $category = Category::where(function ($query) use ($value, $sysValue) {
                $query->where('name', $value);
                $query->orWhere('sys_value', $sysValue);
                $query->orWhere('id', $value);
            })->where('level', $categoryLevels[$field])->orderBy('id')->first();
            if ($category instanceof Category && $category->parent_id === $parent) {
                return $category->id;
            } else {
                $item = [];
                $item['name'] = $value;
                $item['sysValue'] = $sysValue;
                $item['subLevel'] = $categoryLevels[$field];

                return self::saveCategory($item, $parent)->id;
            }
        }
        return null;
    }

    /**
     * @param array $data
     * @param string $key
     *
     * @return string
     */
    private static function getValue($data, $key)
    {
        return isset($data[$key]) ? str_replace('?', '', $data[$key]) : '';
    }

    /**
     * @param  array|string $expression from blade template
     * @return string                   localized category name
     */
    public static function getCategoryName($expression)
    {
        if (isset($expression[0]) && $expression[0] != '') {
            // $expression = [App\Models\Category or String, String $fallback ]
            if (is_string($expression[0])) {
                $value = $expression[0];
            } else {
                if (Lang::locale() === 'km') {
                    $value = $expression[0]->name_kh;
                } else {
                    $value = $expression[0]->name;
                }
            }
        } elseif (isset($expression[1])) {
            $value = $expression[1];
        } else {
            $value = '';
        }
        return $value;
    }

    /**
     * @param  array|string $expression from blade template
     * @return string                   localized category name
     */
    public static function getCategoryNameKm($expression)
    {
        if (isset($expression[0]) && $expression[0] != '') {
            // $expression = [App\Models\Category or String, String $fallback ]
            if (is_string($expression[0])) {
                $value = $expression[0];
            } else {
                $value = $expression[0]->name_kh;
            }
        } elseif (isset($expression[1])) {
            $value = $expression[1];
        } else {
            $value = '';
        }
        return $value;
    }
}
