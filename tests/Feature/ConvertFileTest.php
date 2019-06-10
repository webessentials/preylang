<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File as FileFacades;

class ConvertFileTest extends TestCase
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
        $this->fileNames = ['Impacts/test/test-mp3.mp3', 'Impacts/test/test-mpga.mpga'];
    }

    /**
     * Test convert file
     *
     * @return void
     */
    public function testConvertFileSuccess()
    {
        // Mock two files.
        foreach ($this->fileNames as $fileName) {
            factory(File::class)->create([
                'file_name' => $fileName,
                'import_date' => '2015-01-01 11:00:00',
                'report_date' => '2015-01-01 10:00:00'
            ]);
        }
        $this->artisan('we:media-convert');

        // Check if file is converted.
        $files = File::all();
        foreach ($files as $file) {
            $this->assertTrue($this->validateAudio($file->file_name), $file->file_name . ' is in the right format.');
        }
    }

    /**
     * @param String $fileName
     * @return bool
     */
    public function validateAudio($fileName)
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
