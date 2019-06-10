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
 * Class GroupManagerResponseStatusCodeTest
 * @package Tests\Feature
 * @group groupManagerResponseStatusCodeTest
 */
class GroupManagerResponseStatusCodeTest extends TestCase
{
    /** @var User $user */
    protected $user;
    /** @var UserGroup $testUserGroup */
    protected $testUserGroup;
    /** @var UserGroup $testOtherUserGroup */
    protected $testOtherUserGroup;
    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->testUserGroup = factory(UserGroup::class)->create();
        $this->testOtherUserGroup = factory(UserGroup::class)->create();
        $this->user = factory(User::class)->create([
            'role' => config('settings.user_roles.3'),
            'user_group_id' => $this->testUserGroup->id
        ]);
    }

    /**
     *
     * @return void
     */
    public function testUserRouteForManager()
    {
        /* @var User $testUser */
        $testUser = factory(User::class)->create([
            'role' => config('settings.user_roles.4'),
        ]);

        $testOtherUser = factory(User::class)->create([
            'role' => config('settings.user_roles.4'),
            'user_group_id' => $this->testOtherUserGroup->id
        ]);

        $this->be($this->user);
        $managerRouteTests = [
            ['route' => '/', 'expect' => 200],
            ['route' => '/dashboard', 'expect' => 200],
            ['route' => '/user', 'expect' => 401],
            ['route' => '/user/create', 'expect' => 401],
            ['route' => '/user/edit/' . $testUser->id, 'expect' => 401],
            ['route' => '/user/edit/' . $testOtherUser->id, 'expect' => 401],
            ['route' => '/user/userSetting', 'expect' => 200]
        ];
        foreach ($managerRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testSettingsRouteForManager()
    {
        $testCategory = factory(Category::class)->create();
        $testProvince = factory(Setting::class)->create([
            'type' => config('settings.setting_types.7'),
            'read_only' => false
        ]);
        $testProof = factory(Setting::class)->create([
            'type' => config('settings.setting_types.3'),
            'read_only' => false
        ]);
        $testReason = factory(Setting::class)->create([
            'type' => config('settings.setting_types.0'),
            'read_only' => false
        ]);
        $testOffender = factory(Setting::class)->create([
            'type' => config('settings.setting_types.1'),
            'read_only' => false
        ]);
        $testVictimType = factory(Setting::class)->create([
            'type' => config('settings.setting_types.2'),
            'read_only' => false
        ]);
        $testDesignation = factory(Setting::class)->create([
            'type' => config('settings.setting_types.4'),
            'read_only' => false
        ]);
        $testThreatening = factory(Setting::class)->create([
            'type' => config('settings.setting_types.5'),
            'read_only' => false
        ]);
        $this->be($this->user);
        $managerRouteTests = [
            ['route' => '/setting/usergroups', 'expect' => 401],
            ['route' => '/setting/usergroups/create', 'expect' => 401],
            ['route' => '/setting/usergroups/edit/' . $this->testUserGroup->id, 'expect' => 401],
            ['route' => '/setting/category', 'expect' => 401],
            ['route' => '/setting/category/show/' . $testCategory->id, 'expect' => 401],
            ['route' => '/setting/province', 'expect' => 401],
            ['route' => '/setting/province/create', 'expect' => 401],
            ['route' => '/setting/province/edit/' . $testProvince->id, 'expect' => 401],
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
        foreach ($managerRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testImpactRouteForManager()
    {
        $testVillager = factory(Villager::class)->create([
            'user_group_id' => $this->testUserGroup->id
        ]);
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

        $testOtherVillager = factory(Villager::class)->create([
            'user_group_id' => $this->testOtherUserGroup->id
        ]);
        $otherData = $data;
        $otherData['device_imei'] = $testOtherVillager->device_imei;
        $testOtherImpact = ImpactHelper::saveImpact($otherData);

        $this->be($this->user);
        $managerRouteTests = [
            ['route' => '/impact/', 'expect' => 200],
            ['route' => '/impact/edit/' . $testImpact->id, 'expect' => 200],
            ['route' => '/impact/show/' . $testImpact->id, 'expect' => 200],
            ['route' => '/impact/edit/' . $testOtherImpact->id, 'expect' => 401],
            ['route' => '/impact/show/' . $testOtherImpact->id, 'expect' => 401],
            ['route' => '/impact/process', 'expect' => 200],
            ['route' => '/impact/filter', 'expect' => 200],
            ['route' => '/impact/subcategories/0/1', 'expect' => 201],
            ['route' => '/activity/', 'expect' => 200]
        ];
        foreach ($managerRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testRawImpactRouteForManager()
    {
        $testVillager = factory(Villager::class)->create([
            'user_group_id' => $this->testUserGroup->id
        ]);
        $testRawImpact = factory(RawImpact::class)->create([
            'villager_id' => $testVillager->id,
        ]);

        $testVillager2 = factory(Villager::class)->create([
            'user_group_id' => $this->testOtherUserGroup->id
        ]);
        $testRawImpact2 = factory(RawImpact::class)->create([
            'villager_id' => $testVillager2->id,
        ]);

        $this->be($this->user);
        $managerRouteTests = [
            ['route' => '/rawimpact', 'expect' => 200],
            ['route' => '/rawimpact/show/' . $testRawImpact->id, 'expect' => 200],
            ['route' => '/rawimpact/show/' . $testRawImpact2->id, 'expect' => 401],
        ];
        foreach ($managerRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testVillagerRouteForManager()
    {
        $testVillager = factory(Villager::class)->create([
            'user_group_id' => $this->testUserGroup->id
        ]);
        $testOtherVillager = factory(Villager::class)->create([
            'user_group_id' => $this->testOtherUserGroup->id
        ]);
        $this->be($this->user);
        $managerRouteTests = [
            ['route' => '/villager/', 'expect' => 200],
            ['route' => '/villager/create', 'expect' => 401],
            ['route' => '/villager/edit/' . $testVillager->id, 'expect' => 401],
            ['route' => '/villager/edit/' . $testOtherVillager->id, 'expect' => 401],
        ];
        foreach ($managerRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }

    /**
     *
     * @return void
     */
    public function testFilesForManager()
    {
        $this->be($this->user);
        $managerRouteTests = [
            ['route' => '/files/we.jpg', 'expect' => 200],
        ];
        foreach ($managerRouteTests as $test) {
            echo "\n" . $test['route'];
            $response = $this->get($test['route']);
            $response->assertStatus($test['expect']);
        }
    }
}
