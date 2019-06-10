<?php

namespace Tests\Feature;

use App\Helpers\CategoryHelper;
use App\Models\Category;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class CategoryTest
 *
 * @group Category
 * @package Tests\Feature
 */
class CategoryTest extends TestCase
{

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    /**
     * Test category for self join relationship
     *
     * @return void
     */
    public function testCategorySelfJoinRelationship()
    {
        $data = [
            [
                'sys_value' => 'activities',
                'name' => 'Activities',
                'name_kh' => 'Activities KH',
            ],
            [
                'sys_value' => 'cat1',
                'name' => 'Cat 1',
                'name_kh' => 'Cat 1 KH',
                'level' => 1,
                'parent_id' => 1,
            ]
        ];

        foreach ($data as $d) {
            $category = new Category();
            $category->createUpdateRecord($d);
            $category->save();
        }

        $categories = Category::all();
        $this->assertEquals(2, count($categories));
        $this->assertEquals('Activities', $categories[0]->name);
        $this->assertEquals(0, $categories[0]->level);
        $this->assertEquals(0, $categories[0]->parent_id);
        $this->assertEquals('Activities', $categories[1]->parent->name);
    }

    /**
     * Test importing categories
     *
     * @return void
     */
    public function testImportCategories()
    {
        $filePath = Config::get('settings.import_path.category.preylang');
        $this->assertEquals($filePath, CategoryHelper::getDecisionTreeFilePath());

        $categories = CategoryHelper::getCategoriesFromDecisionTree();
        $this->assertEquals(5, count($categories));

        CategoryHelper::importCategories();

        $category = Category::find(1);
        $this->assertEquals(
            0,
            $category->level,
            'Test top level category.'
        );

        $categories = Category::count();
        $this->assertEquals(
            694,
            $categories,
            'Test total categories imported.'
        );

        $category = Category::find(2);
        $this->assertEquals(
            'Activities',
            $category->parent->name,
            'Test assigning parent record after successful import.'
        );

        $this->assertEquals(
            4,
            count($category->children),
            'Test getting sub categories after successful import.'
        );

        $category = Category::find(4);
        $this->assertEquals(
            4,
            $category->level,
            'Test assigning correct level after successful import.'
        );

        $this->assertEquals(
            3,
            $category->parent_id,
            'Test assigning parent record that skips one level after successful import'
        );

        $category = Category::where('name', 'activities')->first();
        $this->assertEquals(
            'ប្រភេទបទល្មើស',
            $category->name_kh,
            'Test assigning correct translation of top level category after successful import.'
        );

        $category = Category::where('name', 'area that\'s missing')->first();
        $this->assertEquals(
            'ឈើកាត់រួចច្រើន',
            $category->name_kh,
            'Test assigning correct translation of sub level category after successful import.'
        );

        $category = Category::where('name', 'area that\'s missing')->first();
        $this->assertEquals(
            'ឈើកាត់រួចច្រើន',
            $category->name_kh,
            'Test assigning correct translation of sub level category after successful import.'
        );

        $category = Category::where('name', 'Khtiign')->first();
        $this->assertEquals(
            'ខ្ទីន',
            $category->name_kh,
            'Test assigning correct translation of natural resource category after successful import.'
        );

        $this->assertEquals(
            'animals',
            $category->type,
            'Test assigning correct translation of natural resource category after successful import.'
        );
    }

    /**
     * Test get category
     * @return void
     */
    public function testGetCategory()
    {
        CategoryHelper::importCategories();
        $data = [];
        $data['sub_category_1'] = 'Change-adaptation';
        $id = CategoryHelper::getCategory($data, 'sub_category_1', 147);
        $this->assertEquals(
            149,
            $id,
            'Test get correct id for exiting category.'
        );

        $data = [];
        $data['sub_category_2'] = 'Test Category';
        $id = CategoryHelper::getCategory($data, 'sub_category_2', 2);
        $lastId = Category::all()->last()->id;
        $this->assertEquals(
            $lastId,
            $id,
            'Test get correct id for new created category.'
        );
    }
}
