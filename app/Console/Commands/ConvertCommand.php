<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\Models\File;

class ConvertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'we:media-convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for converting audio files to .mp3 files';

    /**
     * @var boolean
     */
    protected $converterInstalled = false;

    /**
     * @var string
     */
    protected $binary;

    /**
     * @var string
     */
    protected $bitrate;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
        if (config('media.converter.bin')) {
            $this->binary = config('media.converter.bin');
        }
        if (config('media.converter.bitrate')) {
            $this->bitrate = config('media.converter.bitrate');
        }
        if ($this->commandExist($this->binary)) {
            $this->converterInstalled = true;
        }
    }

    /**
     * Execute the console command.
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        if (!$this->converterInstalled) {
            $this->error('ERROR: ffmpeg not installed');
        } else {
            $files = $this->findAudioFilesToConvert();
            if (count($files) > 0) {
                $this->convertFiles($files);
            } else {
                $this->info('INFO: nothing to convert');
            }
        }
    }

    /**
     * @param File $files
     * @return void
     * @throws \Exception
     */
    private function convertFiles($files)
    {
        $convertedNumber = $errorNumber = 0;
        $convertedFiles = $errorFiles = array();

        foreach ($files as $file) {
            $fileName = $file->file_name;
            $source = storage_path(config('settings.file_path')) . '/' . $fileName;
            if (!file_exists($source)) {
                $this->info('INFO: ' . $source . ' does not exists');
                continue;
            }
            $pathInfo = pathinfo($fileName);
            $date = new DateTime();
            $newFile = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-' . $date->format('YmdHis') . '.mp3';
            $destination = storage_path(config('settings.file_path')) . '/' . $newFile;
            $status = $this->convert($source, $destination);
            if ($status) {
                $convertedNumber++;
                $convertedFiles[] = $newFile;
                $file->converted = true;
                $file->original_file_name = $fileName;
                $file->file_name = $newFile;
                $file->converted_at = $date;
                $file->save();
            } else {
                $errorNumber++;
                $errorFiles[] = $fileName;
            }
        }

        if ($convertedNumber) {
            $this->info('------------ '. $convertedNumber . ' file(s) converted ------------');
            $this->info(implode(PHP_EOL, $convertedFiles));
        }
        if ($errorNumber) {
            $this->info('------------ '. $errorNumber . ' error(s) ------------');
            $this->info(implode(PHP_EOL, $errorFiles));
        }
    }

    /**
     * @param int $limit
     * @param bool $convertStatus
     * @return File
     */
    public function findAudioFilesToConvert($limit = 1000, $convertStatus = false)
    {
        $files = File::where([
            ['file_type', '=', 'audio'],
            ['converted', '=', $convertStatus]
        ])->limit($limit)->get();
        return $files;
    }

    /**
     * Check if the command is already existed on the environment
     *
     * @param string $cmd
     * @return bool
     */
    private function commandExist($cmd)
    {
        $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
        return !empty($return);
    }

    /**
     * @param string $source
     * @param string $destination
     *
     * @return bool
     */
    private function convert($source, $destination)
    {
        $output = array();
        $cmd = $this->binary . ' -i ' . escapeshellarg($source) . ' -c:a libmp3lame -b:a '
            . $this->bitrate . ' -y ' . escapeshellarg($destination) .' 2>&1';
        exec($cmd, $output, $retVar);

        // If return value != 0.
        if ($retVar) {
            $this->info('ERROR converting file');
        }
        $status = false;
        // Check if file exists and delete empty file.
        if (file_exists($destination)) {
            if (filesize($destination)) {
                $status = true;
            } else {
                unlink($destination);
                return false;
            }
        }
        return $status;
    }
}
