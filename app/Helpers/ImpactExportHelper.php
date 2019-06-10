<?php

namespace App\Helpers;

use App\Exports\ImpactExport;
use App\Mail\ExportFinished;
use App\Models\User;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\WriterFactory;
use Illuminate\Support\Facades\Mail;

class ImpactExportHelper
{
    /**
     * @var string $exportDirectoryName
     */
    protected static $exportDirectoryName = 'exports/';

    /**
     * @param \App\Models\User $user
     * @param string $type
     * @param array $ids
     *
     * @return string
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public static function exportImpacts($user, $type, $ids = [])
    {
        // 2018-02-01_11:20.
        $timestamp = date('Y-m-d_H:i');
        $fileNameWithoutExtension = 'impact_export_' . $timestamp;

        $basePath = config('settings.file_path') . '/' . self::$exportDirectoryName;
        $absolutePath = storage_path($basePath);
        if (!file_exists($absolutePath)) {
            mkdir($absolutePath, 0777, true);
        }

        switch (strtolower($type)) {
            case 'excel':
                $writer = WriterFactory::create(Type::XLSX);
                $fileName = $fileNameWithoutExtension . '.xlsx';
                break;
            case 'csv':
            default:
                $writer = WriterFactory::create(Type::CSV);
                $fileName = $fileNameWithoutExtension . '.csv';
                break;
        }

        $writer->openToFile($absolutePath . $fileName);
        $export = new ImpactExport($user, $type, $ids);
        $writer->addRow($export->headings());

        $query = $export->query();
        $numberOfImpactsToExport = $query->count();
        $numberOfChunks = ceil($numberOfImpactsToExport / $export->chunkSize());
        $styleBuilder = new StyleBuilder();
        $style = $styleBuilder->setBackgroundColor(config('settings.excel_record_highlight_color_code'))->build();
        for ($page = 1; $page <= $numberOfChunks; $page++) {
            $impacts = $query->forPage($page, $export->chunkSize())->get();
            foreach ($impacts as $impact) {
                if ($impact->category_modified) {
                    $writer->addRowWithStyle($export->map($impact), $style);
                } else {
                    $writer->addRow($export->map($impact));
                }
            }
        }

        $writer->close();

        return self::$exportDirectoryName . $fileName;
    }

    /**
     * @param User $user
     * @param string $email
     * @param string $filePath
     * @return void
     */
    public static function sendEmail($user, $email, $filePath)
    {
        $fullName = '';
        if ($email === $user->email) {
            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $fullName = "$firstName $lastName";
        }
        Mail::to($email)->queue(new ExportFinished($fullName, $filePath));
    }
}
