<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacades;

class ConvertFileFailedTest extends TestCase
{

    /**
     * @var array
     */
    protected $fileNames;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $filesSrc = config('settings.convert_files_source');
        $fileDes = storage_path(config('settings.file_path')) . '/Impacts/test';
        if (is_dir($fileDes)) {
            Storage::deleteDirectory(config('settings.file_path') . '/Impacts/test');
        }
        FileFacades::copyDirectory($filesSrc, $fileDes);
        $this->fileNames = ['Impacts/test/test-mpga.mpga', 'Impacts/test/test-mpga-fail.mpga'];
    }

    /**
     * Test convert file
     *
     * @return void
     */
    public function testConvertFileFail()
    {
        // Mock non-existing file.
        factory(File::class)->create([
            'file_name' => $this->fileNames[1],
            'import_date' => '2015-01-01 11:00:00',
            'report_date' => '2015-01-01 10:00:00'
        ]);

        // Run convert file command.
        $this->artisan('we:media-convert');

        // Check if the record is not changed.
        $file = File::first();
        $this->assertEquals($file->file_name, $this->fileNames[1], 'Non-existing file is not converted.');

        // Alter record to be existing file.
        $file->file_name = $this->fileNames[0];
        $file->save();

        // Check if file is not in correct format.
        $this->assertFalse($this->validateAudio($file->file_name), 'Record is not in the correct format.');
    }

    /**
     * @param String $fileName
     * @return bool
     */
    private function validateAudio($fileName)
    {
        $filePath = storage_path(config('settings.file_path')) . '/' . $fileName;
        if (!file_exists($filePath)) {
            return false;
        }

        $allowed = ['audio/mpeg'];
        $fInfo = finfo_open(FILEINFO_MIME_TYPE);
        $info = finfo_file($fInfo, $filePath);
        if (!in_array($info, $allowed)) {
            return false;
        }
        return true;
    }
}
