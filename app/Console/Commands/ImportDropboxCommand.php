<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Impact;
use App\Models\RawImpact;
use Illuminate\Support\Facades\Storage;
use Kunnu\Dropbox\Models\FolderMetadata;
use Kunnu\Dropbox\Models\ModelCollection;

class ImportDropboxCommand extends AbstractDropboxCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'we:dropbox-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import file from dropbox to local.';

    /**
     * @var boolean
     */
    protected $hasAudioFiles;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $folderMetadata = $this->dropbox->listFolder(config('dropbox.rootDir'));
            $items = $folderMetadata->getItems();
            if (is_null($folderMetadata) || !count($items)) {
                $this->info('INFO: Root folder is empty.');
                return;
            }
            $path = config('settings.file_path') . '/' . config('dropbox.impact_file_path');
            $this->createDirectory(storage_path($path));
            $this->importFiles($items);
        } catch (\Exception $e) {
            $this->error('ERROR:' . $e->getMessage());
            return;
        }

        if ($this->hasAudioFiles) {
            $this->call('we:media-convert');
        }
    }

    /**
     * @param ModelCollection $items
     *
     * @return void
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     */
    private function importFiles($items)
    {
        foreach ($items as $item) {
            if ($item instanceof FolderMetadata) {
                $itemFolderPath = $item->getPathDisplay();
                if (!empty($itemFolderPath)) {
                    $folderPath = pathinfo($itemFolderPath);
                    $impactId = $folderPath['basename'];
                    $path = config('settings.file_path') . '/' . config('dropbox.impact_file_path');
                    $destinationPath = storage_path($path) . '/' . $impactId;
                    $impact = Impact::find($impactId);
                    if (!$impact) {
                        $this->deleteDropboxFolder($item);
                        continue;
                    }

                    $this->createDirectory($destinationPath);
                    $savePath = config('dropbox.impact_file_path') . '/' . $impactId;
                    $files = $this->downloadAndSaveFile($itemFolderPath, $savePath);
                    $this->saveFilesToObject($files, $impact);
                }
                $this->deleteDropboxFolder($item);
            }
        }
    }

    /**
     * @param string $path
     * @param string $savePath
     *
     * @return array
     * @throws \Kunnu\Dropbox\Exceptions\DropboxClientException
     */
    protected function downloadAndSaveFile($path, $savePath)
    {
        $items = $this->dropbox->listFolder($path);
        $files = [];
        foreach ($items->getItems() as $file) {
            $fileName = $file->getName();
            if (!$this->isValidExtension($fileName)) {
                $this->deleteDropboxFile($file);
                continue;
            }
            $fileInfo = $this->getFileInfo($file);
            $pathFile = config('settings.file_path') . '/' . $savePath . '/' . $fileName;
            $file = $this->dropbox->download($file->getPathDisplay());
            Storage::put($pathFile, $file->getContents());
            array_push($files, $fileInfo);
        }

        return $files;
    }

    /**
     * @param array     $files
     * @param Impact    $impact
     *
     * @return void
     */
    private function saveFilesToObject($files, $impact)
    {
        $path = config('dropbox.impact_file_path') . '/' . $impact->id . '/';
        $fileLocation = storage_path(config('settings.file_path')) . '/' . $path ;
        $rawImpact = $impact->rawImpact;
        foreach ($files as $fileInfo) {
            $file = new File();
            $file->impact_id = $impact->id;
            $file->file_name = $path . $fileInfo['fileName'];
            $file->file_type = $fileInfo['fileType'];
            $file->is_imported = true;
            $mataData = $this->getFileMetaData($fileLocation . $fileInfo['fileName']);
            if (!empty($mataData)) {
                $this->setMataData($mataData, $file, $impact, $rawImpact);
            }

            if ($fileInfo['fileType'] == 'audio') {
                $this->hasAudioFiles = true;
            }

            $file->save();
        }

        $impact->save();
        if ($rawImpact) {
            $rawImpact->save();
        }
    }

    /**
     * @param array     $metadata
     * @param File      $file
     * @param Impact    $impact
     * @param RawImpact $rawImpact
     *
     * @return void
     */
    private function setMataData($metadata, &$file, &$impact, &$rawImpact)
    {
        $reportedDate = $this->getReportDate($metadata);
        if ($reportedDate instanceof \DateTime) {
            $file->report_date = $reportedDate;
            $impact->report_date = $reportedDate->format(config('settings.date_time_format'));
            if ($rawImpact) {
                $rawImpact->report_date = $reportedDate;
            }
        }

        $metadata['facebookTagged'] = false;
        if (!empty($metadata['UserComment']) && strpos($metadata['UserComment'], 'FacebookPost') !== false) {
            $metadata['facebookTagged'] = true;
            $file->facebook_post = true;
            $this->info('Image is facebook tagged');
        }

        $coordinates = $this->getCoordinates($metadata);
        if (!empty($coordinates)) {
            $file->latitude = $coordinates[0];
            $file->longitude = $coordinates[1];
            $impact->latitude = $coordinates[0];
            $impact->longitude = $coordinates[1];
            if ($rawImpact) {
                $rawImpact->latitude = $coordinates[0];
                $rawImpact->longitude = $coordinates[1];
            }
            $this->info('Geodata: latitude = ' . $coordinates[0] . ' | longitude = ' . $coordinates[1]);
        }
    }

    /**
     * @param array $meta
     * @return \DateTime|null
     */
    private function getReportDate($meta)
    {
        $date = null;
        try {
            if (isset($meta['DateTimeOriginal'])) {
                $meta['DateTimeOriginal'] = str_replace('-', ':', $meta['DateTimeOriginal']);
                $date = \DateTime::createFromFormat('Y:m:d H:i:s', $meta['DateTimeOriginal']);
            } elseif (isset($meta['DateTime'])) {
                $date = new \DateTime();
                $date->setTimestamp(($meta['DateTime'] / 1000));
            }
        } catch (\Exception $e) {
            $this->info('Problem of converting date');
        }
        return $date;
    }

    /**
     * Get coordinates (Latitude, Longitude)
     *
     * @param array $metadata
     *
     * @return array
     */
    private function getCoordinates($metadata)
    {
        if (!isset($metadata['GPSLatitude']) or !isset($metadata['GPSLongitude'])) {
            return [];
        }
        $latitude = null;
        $longitude = null;
        if (!is_array($metadata['GPSLatitude'])) {
            // Lat/long format.
            $latitude = $metadata['GPSLatitude'];
            $longitude = $metadata['GPSLongitude'];
        } elseif (count($metadata['GPSLatitude']) == 3) {
            // Degree/minute/second format.
            $latDirection = 'N';
            // Default lat/long direction.
            $longDirection = 'E';
            if (isset($metadata['GPSLatitudeRef']) && $metadata['GPSLatitudeRef']) {
                $latDirection = $metadata['GPSLatitudeRef'];
            }
            if (isset($metadata['GPSLongitudeRef']) && $metadata['GPSLongitudeRef']) {
                $longDirection = $metadata['GPSLongitudeRef'];
            }

            $latDegree = $this->parseDMSValue($metadata['GPSLatitude'][0]);
            $latMinute = $this->parseDMSValue($metadata['GPSLatitude'][1]);
            $latSecond = $this->parseDMSValue($metadata['GPSLatitude'][2]);

            $longDegree = $this->parseDMSValue($metadata['GPSLongitude'][0]);
            $longMinute = $this->parseDMSValue($metadata['GPSLongitude'][1]);
            $longSecond = $this->parseDMSValue($metadata['GPSLongitude'][2]);

            $latitude = $this->DEC2Decimal($latDegree, $latMinute, $latSecond, $latDirection);
            $longitude = $this->DEC2Decimal($longDegree, $longMinute, $longSecond, $longDirection);
        } else {
            return [];
        }
        return [$latitude, $longitude];
    }
}
