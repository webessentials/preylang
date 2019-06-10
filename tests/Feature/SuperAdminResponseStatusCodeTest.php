<?php

namespace Tests\Feature;

use App\Helpers\ImpactHelper;
use App\Models\Category;
use App\Models\RawImpact;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\Villager;
use Tests\TestCase;

/**
 * Class SuperAdminResponseStatusCodeTest
 * @package Tests\Feature
 * @group superAdminResponseStatusCodeTest
 */
class SuperAdminResponseStatusCodeTest extends TestCase
{
    /** @var User $user */
    protected $user;
    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->user = factory(User::class)->create([
            'role' => config('settings.user_roles.0'),
        ]);
    }

    /**
     *
     * @return void
     */
    public function testUserRouteResponseStatusCode()
    {
        /* @var User $testUser */
        $testUser = factory(User::class)->create([
            'role' => config('settings.user_roles.4'),
        ]);

        $this->be($this->user);
        $superAdminRouteTests = [
            ['route' => '/', 'expect' => 200],
            ['route' => '/dashboard', 'expect' => 200],
            ['route' => '/user', 'expect' => 200],
            ['route' => '/user/create', 'expect' => 200],
            ['route' => '/user/edit/' . $testUser->id, 'expect' => 200],
            ['route' => '/user/userSetting', 'expect' => 200]
        ];
        foreach ($superAdminRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testSettingsRouteResponseStatusCode()
    {
        $testUserGroup = factory(UserGroup::class)->create();
        $testCategory = factory(Category::class)->create();
        $testProvince = factory(Setting::class)->create([
            'type' => config('settings.setting_types.7'),
        ]);
        $testProof = factory(Setting::class)->create([
            'type' => config('settings.setting_types.3'),
        ]);
        $testReason = factory(Setting::class)->create([
            'type' => config('settings.setting_types.0'),
        ]);
        $testOffender = factory(Setting::class)->create([
            'type' => config('settings.setting_types.1'),
        ]);
        $testVictimType = factory(Setting::class)->create([
            'type' => config('settings.setting_types.2'),
        ]);
        $testDesignation = factory(Setting::class)->create([
            'type' => config('settings.setting_types.4'),
        ]);
        $testThreatening = factory(Setting::class)->create([
            'type' => config('settings.setting_types.5'),
        ]);
        $this->be($this->user);
        $superAdminRouteTests = [
            ['route' => '/setting/usergroups', 'expect' => 200],
            ['route' => '/setting/usergroups/create', 'expect' => 200],
            ['route' => '/setting/usergroups/edit/' . $testUserGroup->id, 'expect' => 200],
            ['route' => '/setting/category', 'expect' => 401],
            ['route' => '/setting/category/show/' . $testCategory->id, 'expect' => 401],
            ['route' => '/setting/province', 'expect' => 200],
            ['route' => '/setting/province/create', 'expect' => 200],
            ['route' => '/setting/province/edit/' . $testProvince->id, 'expect' => 200],
            ['route' => '/setting/proof', 'expect' => 401],
            ['route' => '/setting/proof/create', 'expect' => 401],
            ['route' => '/setting/proof/edit/' . $testProof->id, 'expect' => 401],
            ['route' => '/setting/reason', 'expect' => 401],
            ['route' => '/setting/reason/create', 'expect' => 401],
            ['route' => '/setting/reason/edit/' . $testReason->id, 'expect' => 401],
            ['route' => '/setting/offender', 'expect' => 401],
            ['route' => '/setting/offender/create', 'expect' => 401],
            ['route' => '/setting/offender/edit/' . $testOffender->id, 'expect' => 401],
            ['route' => '/setting/victim_type', 'expect' => 401],
            ['route' => '/setting/victim_type/create', 'expect' => 401],
            ['route' => '/setting/victim_type/edit/' . $testVictimType->id, 'expect' => 401],
            ['route' => '/setting/designation', 'expect' => 401],
            ['route' => '/setting/designation/create', 'expect' => 401],
            ['route' => '/setting/designation/edit/' . $testDesignation->id, 'expect' => 401],
            ['route' => '/setting/threatening', 'expect' => 401],
            ['route' => '/setting/threatening/create', 'expect' => 401],
            ['route' => '/setting/threatening/edit/' . $testThreatening->id, 'expect' => 401],
        ];
        foreach ($superAdminRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testImpactRouteResponseStatusCode()
    {
        /* @var Villager $testVillager */
        $testVillager = factory(Villager::class)->create();
        $data = [
            'device_imei' => $testVillager->device_imei,
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

        $testImpact = ImpactHelper::saveImpact($data);

        $this->be($this->user);
        $superAdminRouteTests = [
            ['route' => '/impact/', 'expect' => 200],
            ['route' => '/impact/edit/' . $testImpact->id, 'expect' => 401],
            ['route' => '/impact/show/' . $testImpact->id, 'expect' => 200],
            ['route' => '/impact/process', 'expect' => 401],
            ['route' => '/impact/filter', 'expect' => 200],
            ['route' => '/impact/subcategories/0/1', 'expect' => 201],
            ['route' => '/activity/', 'expect' => 200]
        ];
        foreach ($superAdminRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testRawImpactRouteResponseStatusCode()
    {
        $testVillager = factory(Villager::class)->create();
        $testRawImpact = factory(RawImpact::class)->create([
            'villager_id' => $testVillager->id,
        ]);
        $this->be($this->user);
        $superAdminRouteTests = [
            ['route' => '/rawimpact', 'expect' => 200],
            ['route' => '/rawimpact/show/' . $testRawImpact->id, 'expect' => 200],
        ];
        foreach ($superAdminRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testVillagerRouteResponseStatusCode()
    {
        $testVillager = factory(Villager::class)->create();
        $this->be($this->user);
        $superAdminRouteTests = [
            ['route' => '/villager/', 'expect' => 200],
            ['route' => '/villager/create', 'expect' => 200],
            ['route' => '/villager/edit/' . $testVillager->id, 'expect' => 200],
        ];
        foreach ($superAdminRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testFilesRouteResponseStatusCode()
    {
        $this->be($this->user);
        $superAdminRouteTests = [
            ['route' => '/files/we.jpg', 'expect' => 200],
        ];
        foreach ($superAdminRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }
}
