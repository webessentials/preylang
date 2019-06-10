<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Exceptions\DropboxClientException;
use Kunnu\Dropbox\Models\FileMetadata;
use Kunnu\Dropbox\Models\FolderMetadata;

abstract class AbstractDropboxCommand extends Command
{
    /**
     * @var Dropbox
     */
    protected $dropbox;

    /**
     * Configures the current command.
     *
     * @return void
     */
    public function configure()
    {
        parent::configure();
        $dropboxApp = new DropboxApp(
            config('dropbox.app_key'),
            config('dropbox.app_secret'),
            config('dropbox.access_token')
        );
        $this->dropbox = new Dropbox($dropboxApp);
    }

    /**
     * @param FileMetadata $item
     *
     * @return void
     */
    protected function deleteDropboxFile($item)
    {
        if ($item instanceof FileMetadata) {
            try {
                $fileDeleted = $this->dropbox->delete($item->getPathLower());
                $this->info('INFO: Deleting file in dropbox called: ' . $fileDeleted->getPathDisplay());
            } catch (DropboxClientException $e) {
                $this->info('INFO: Cannot remove file because ' . $e->getMessage());
            }
        }
    }

    /**
     * @param FolderMetadata $item
     *
     * @return void
     */
    protected function deleteDropboxFolder($item)
    {
        if ($item instanceof FolderMetadata) {
            try {
                $folderDeleted = $this->dropbox->delete($item->getPathLower());
                $this->info('INFO: Deleting folder in dropbox called: ' . $folderDeleted->getPathDisplay());
            } catch (DropboxClientException $e) {
                $this->info('INFO: Cannot remove folder because ' . $e->getMessage());
            }
        }
    }

    /**
     * @param string $file
     * @return bool
     */
    protected function isValidExtension($file)
    {
        $extension = $this->getFileExtension($file);
        return in_array($extension, array_merge(config('dropbox.fileTypes'), config('dropbox.audioFiles')));
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function createDirectory($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getFileExtension($fileName)
    {
        $file = explode('.', $fileName);
        return end($file);
    }

    /**
     * @param FileMetadata $file
     * @return array
     */
    protected function getFileInfo($file)
    {
        $fileName = $file->getName();
        $extension = $this->getFileExtension($fileName);
        return [
            'ext' => $extension,
            'fileName' => $fileName,
            'size' => $file->getSize(),
            'fileType' => in_array($extension, config('dropbox.audioFiles')) ? 'audio' : 'image',
        ];
    }

    /**
     * @param string $value
     *
     * @return float
     */
    protected function parseDMSValue($value)
    {
        $array = explode('/', $value);
        if (count($array) == 2) {
            $value = ($array[0] / $array[1]);
        }
        return $value;
    }

    /**
     * Converts degree/min/sec format to decimal
     *
     * @param float $deg
     * @param float $min
     * @param float $sec
     * @param float $direction
     *
     * @return float
     */
    protected function DEC2Decimal($deg, $min, $sec, $direction)
    {
        $deg = $this->parseDMSValue($deg);
        $min = $this->parseDMSValue($min);
        $sec = $this->parseDMSValue($sec);

        $dec = ($deg + (($min * 60 + $sec) / 3600));
        $hem = strtolower($direction);
        return ($hem == 's' || $hem == 'w') ? $dec *= -1 : $dec;
    }

    /**
     * @param string $filePath
     * @return array
     */
    protected function getFileMetaData($filePath)
    {
        $metadata = @exif_read_data($filePath);
        return empty($metadata) ? [] : $metadata;
    }
}
