<?php

namespace Tests\Feature;

use App\Helpers\ImpactHelper;
use App\Models\Impact;
use App\Models\Setting;
use App\Models\UserGroup;
use App\Models\Villager;
use Illuminate\Support\Facades\File;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Tests\TestCase;

class DropboxTest extends TestCase
{

    /**
     * @var Impact
     */
    protected $impact;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:fresh');

        factory(UserGroup::class, 1)->create();
        Setting::create(['name' => 'Kampong Thom', 'type' => 'province']);

        $offender = factory(Setting::class)->create([
            'name' => 'Company',
            'type' => 'offender',
            'sys_value' => strtolower('Company')
        ]);
        $villager = factory(Villager::class, 1)->create()->first();
        $data = [
            'device_imei' => $villager->device_imei,
            'category' => 'Activities',
            'sub_category_1' => 'Logging',
            'sub_category_2' => 'Transport',
            'sub_category_3' => 'Boat',
            'sub_category_4' => "Don't know",
            'sub_category_5' => 'Interaction yes',
            'permit' => 'No permit',
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
            'impact_number' => 'PLI-0001',
            'villager_id' => $villager->id,
            'offender_id' => $offender->id
        ];
        $this->impact = Impact::create($data);
        config(['dropbox.impact_file_path' => 'Impacts_Test']);
        config(['dropbox.rootDir' => '/Test']);
    }

    /**
     * @return void
     */
    public function testImportDropboxWithEmptyRootDirectoryExpectNoFilesRecord()
    {
        $this->artisan('we:dropbox-import');
        $countFiles = \App\Models\File::count();
        $this->assertEquals(0, $countFiles);
    }

    /**
     * @return void
     */
    public function testImportFileFromDropboxWithCorrectConfigurationExpectHasFilesRecord()
    {
        $dropboxApp = new DropboxApp(
            config('dropbox.app_key'),
            config('dropbox.app_secret'),
            config('dropbox.access_token')
        );
        $dropbox = new Dropbox($dropboxApp);
        $dropboxFile = new DropboxFile(public_path('/images/logo/logo.png'));
        $dropboxpath = '/' . $this->impact->id . '/logo.jpeg';
        $dropbox->simpleUpload($dropboxFile, config('dropbox.rootDir') .$dropboxpath, ['autorename' => true]);
        $this->artisan('we:dropbox-import');
        $countFiles = \App\Models\File::count();
        $this->assertEquals(1, $countFiles);

        $path = config('settings.file_path') . '/' . config('dropbox.impact_file_path');
        $path = storage_path($path);
        unlink($path . $dropboxpath);
        rmdir($path . '/' . $this->impact->id);
        rmdir($path);
    }
}
