<?php

namespace Tests\Feature;

use App\Helpers\CategoryHelper;
use App\Helpers\ImpactHelper;
use App\Models\Category;
use App\Models\Impact;
use App\Models\Setting;
use App\Models\UserGroup;
use App\Models\Villager;
use Tests\TestCase;

/**
 * Class ImpactTest
 *
 * @group Impact
 * @package Tests\Feature
 */
class ImpactTest extends TestCase
{

    /**
     * @var array
     */
    protected $userGroups;

    /**
     * @var array
     */
    protected $villagers;

    /**
     * @var array
     */
    protected $provinces;

    /**
     * @var array
     */
    protected $offenders;



    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:fresh');

        // Mock 2 UserGroup.
        $this->userGroups = factory(UserGroup::class, 2)->create();

        // Mock some provinces.
        $provinceNames = ['Kampong Thom', 'Kratie', 'Preah Vihear', 'Steung Treng', 'Preah Rokar'];
        foreach ($provinceNames as $name) {
            $this->provinces[] = factory(Setting::class)->create([
                'name' => $name,
                'type' => 'province'
            ]);
        }

        // Mock some offenders.
        $offenderNames = ['Company', 'Other', 'Police chief'];
        foreach ($offenderNames as $name) {
            $this->offenders[] = factory(Setting::class)->create([
                'name' => $name,
                'type' => 'offender',
                'sys_value' => strtolower($name)
            ]);
        }

        // Mock 1 Villager.
        $this->villagers[] = factory(Villager::class)->create([
            'device_imei' => '388121030353363',
            'province_id' => $this->provinces[0]->id,
            'user_group_id' => $this->userGroups[0]->id
        ]);
    }

    /**
     * @return void
     */
    public function testInitializeData()
    {
        $this->artisan('we:migrate', [ '--all' => true, '--limit' => 150]);
        $totalImpacts = Impact::count();
        $this->assertEquals(150, $totalImpacts, 'Expect total imported impacts');
    }

    /**
     * Test saving impact.
     *
     * @return void
     */
    public function testStoreSuccess()
    {
        $data = [
            'device_imei' => '388121030353363',
            'category' => 'Other',
            'sub_category_1' => 'Logging',
            'sub_category_2' => 'Loggers',
            'sub_category_3' => 'Interaction-No',
            'sub_category_4' => "ELC",
            'sub_category_5' => '',
            'permit' => '',
            'by_visual' => 0,
            'by_audio' => 0,
            'by_track' => 0,
            'excluded' => 1,
            'excluded_reason' => 'Testing entry',
            'offender' => 'Police chief',
            'threatening' => 'Facebook',
            'designation' => 'Reporting to community',
            'proof' => 'Video',
            'victim_type' => 'Family',
            'reason' => 'Meeting police',
            'number_of_items' => 2,
            'patroller_note' => 'Hello',
        ];

        CategoryHelper::importCategories();
        $categories = CategoryHelper::getCategories($data);
        $expectedCategories = [];
        foreach ($categories as $key => $category) {
            $expectedCategories[$key] = Category::find($category);
        }
        $this->assertEquals(
            7,
            count($categories),
            'Test categories for impact'
        );
        $this->assertEquals(
            'Other',
            $expectedCategories['category']->name,
            'Test save free text when category in Decision Tree'
        );
        $this->assertEquals(
            'Logging',
            $expectedCategories['sub_category_1']->name,
            'Test save free text when not in Decision Tree'
        );

        $impact = ImpactHelper::saveImpact($data);
        $this->assertEquals(
            2,
            $impact->number_of_items,
            'Test correct number of items'
        );
        $this->assertEquals(
            'Police chief',
            $impact->offender->name,
            'Test correct name from offender object from table settings'
        );
        $this->assertEquals(
            'Loggers',
            $impact->getCategoryByRelationField('sub_category_2')->name,
            'Test one of the impact categories'
        );
    }
}
