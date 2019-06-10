<?php

namespace Tests\Feature;

use App\Helpers\ImpactExportHelper;
use App\Models\Impact;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use User;

/**
 * Class ExportTest
 * @package Tests\Feature
 * @group exportTest
 */
class ExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var integer
     */
    public static $numberOfImpacts = 3;
    /**
     * @var User
     */
    private $user;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        factory(Impact::class, self::$numberOfImpacts)->create();
        $this->user = createIfNoneExistsReturnFirstOtherwise(
            'App\Models\User',
            ['role' => config('settings.user_super_roles.superAdmin')]
        );
    }

    /**
     * Values like 0, true/false, null get omitted by Laravel-Excel
     * therefore we replace these with their string equivalents
     *
     *  see https://github.com/Maatwebsite/Laravel-Excel/issues/1665
     * @param array $values
     *
     * @return mixed
     */
    private static function convertDataTypesToString($values)
    {
        foreach ($values as $key => $value) {
            if (is_bool($value)) {
                $values[$key] = self::changeToYesNo($value);
            } elseif (is_numeric($value)) {
                $values[$key] = strval($value);
            } elseif ($value instanceof Carbon) {
                $values[$key] = $value->toDateTimeString();
            } elseif ($value === null) {
                $values[$key] = '';
            }
        }

        return $values;
    }

    /**
     * @param integer $value
     *
     * @return string
     */
    private static function changeToYesNo($value)
    {
        if ($value) {
            return 'Yes';
        }
        return 'No';
    }

    /**
     * @param string $type
     * @param int $rowNumber
     * @param int $impactNumber
     *
     * @return array
     */
    private static function gatherExpectedDataForImpact($type, $rowNumber, $impactNumber)
    {
        if ($rowNumber === 1) {
            // Header row.
            $expectedData = [
                'No',
                'Category',
                'Subcategory1',
                'Subcategory2',
                'Subcategory3',
                'Subcategory4',
                'Leaf Category',
                'Permit',
                'User Group',
                'Note',
                'Note KH',
                'Patroller\'s Note',
                'Latitude',
                'Longitude',
                'Phone Serial',
                'Number of items',
                'Villager Id',
                'Employer',
                'License',
                'Agreement',
                'By Visual',
                'By Audio',
                'By Track',
                'Facebook',
                'Audio',
                'Images',
                'Exclude',
                'Edited',
                'Report To',
                'Reported Date',
                'Created At',
                'Victim Type',
                'Reason/Cause by',
                'Offender',
                'Location',
                'Threat via',
                'Witness',
                'Responding action',
                'Proof'
            ];
            if ($type == 'csv') {
                 // The library box/spout inserts special character in front of first line when exporting to CSV.
                $expectedData[0] = "﻿No";
            }
        } else {
            $impact = Impact::where('impact_number', $impactNumber)->take(1)->get()[0];
            $villager = $impact->villager;

            $expectedData = [
                $impact->impact_number,
                $categoryName = $impact->getCategoryByRelationField('category')->name,
                $subCategory1 = $impact->getCategoryByRelationField('sub_category_1')->name,
                $subCategory2 = $impact->getCategoryByRelationField('sub_category_2')->name,
                $subCategory3 = $impact->getCategoryByRelationField('sub_category_3')->name,
                $subCategory4 = $impact->getCategoryByRelationField('sub_category_4')->name,
                $subCategory5 = $impact->getCategoryByRelationField('sub_category_5')->name,
                $impact->categories['permit'],
                $villager ? $villager->userGroup->name : '',
                $impact->note,
                $impact->note_kh,
                $impact->patroller_note,
                $impact->latitude,
                $impact->longitude,
                $villager ? $villager->device_imei : '',
                $impact->number_of_items,
                $villager ? $villager->name : '',
                $impact->employer,
                $impact->license,
                self::changeToYesNo($impact->agreement),
                self::changeToYesNo($impact->by_visual),
                self::changeToYesNo($impact->by_audio),
                self::changeToYesNo($impact->by_track),
                $impact->facebook,
                $impact->audios->implode('file_name', strtolower($type) == 'excel' ? "\n" : ' '),
                $impact->images->implode('file_name', strtolower($type) == 'excel' ? "\n" : ' '),
                self::changeToYesNo($impact->excluded),
                self::changeToYesNo($impact->modified),
                $impact->report_to,
                $impact->report_date,
                $impact->created_at,
                $impact->victimType->name,
                $impact->reason->name,
                $impact->offender ? $impact->offender->name : '',
                $impact->location,
                $impact->threatening,
                $impact->witness,
                $impact->designation->name,
                $impact->proof
            ];
        }

        $expectedData = self::convertDataTypesToString($expectedData);

        return $expectedData;
    }

    /**
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     */
    public function testExportAllToCSV()
    {
        $type = 'csv';

        $pathToFile = ImpactExportHelper::exportImpacts($this->user, $type);
        $absolutePathToFile = storage_path(config('settings.file_path') . '/' . $pathToFile);

        // File has correct name.
        $this->assertEquals(
            1,
            preg_match('/exports\/impact_export_\d{4}-[01][0-9]-\d{2}_[0-2]\d:[0-5]\d.csv/', $pathToFile)
        );
        // File exists.
        $this->assertTrue(file_exists($absolutePathToFile));
        // File has correct content.
        $csvAsArrayOfLines = file($absolutePathToFile, FILE_SKIP_EMPTY_LINES);
        // To take headerLine into account.
        $this->assertEquals((self::$numberOfImpacts + 1), count($csvAsArrayOfLines));

        if (($fileHandle = fopen($absolutePathToFile, 'r')) !== false) {
            $rowNumber = 1;
            while ($row = fgetcsv($fileHandle)) {
                $expectedDataInRow = self::gatherExpectedDataForImpact($type, $rowNumber, $row[0]);
                $this->assertEquals($expectedDataInRow, $row);
                $rowNumber++;
            }
            fclose($fileHandle);
        }
    }

    /**
     * @param string $absolutePathToFile
     *
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private static function openSpreadsheet($absolutePathToFile)
    {
        $inputFileType = IOFactory::identify($absolutePathToFile);
        $reader = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($absolutePathToFile);

        return $spreadsheet;
    }

    /**
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function testExportAllToExcel()
    {
        $type = 'excel';

        $pathToFile = ImpactExportHelper::exportImpacts($this->user, $type);
        $absolutePathToFile = storage_path(config('settings.file_path') . '/' . $pathToFile);

        // File has correct name.
        $this->assertEquals(
            1,
            preg_match('/exports\/impact_export_\d{4}-[01][0-9]-\d{2}_[0-2]\d:[0-5]\d.xlsx/', $pathToFile)
        );
        // File exists.
        $this->assertTrue(file_exists($absolutePathToFile));

        // File has correct content.
        $spreadsheet = self::openSpreadsheet($absolutePathToFile);

        for ($row_number = 1; $row_number < 4; $row_number++) {
            // First row has coordinate of 1.
            $impactNumber = $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $row_number)->getValue();
            $expectedDataInRow = self::gatherExpectedDataForImpact($type, $row_number, $impactNumber);

            $expectedNumberOfColumns = count($expectedDataInRow);
            $indexOfFirstColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(1);
            $indexOfLastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($expectedNumberOfColumns);

            $row = $spreadsheet->getActiveSheet()->rangeToArray("$indexOfFirstColumn$row_number:$indexOfLastColumn$row_number")
            [0];

            $this->assertEquals($expectedDataInRow, $row);
        }
    }

    /**
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException
     * @throws \Box\Spout\Common\Exception\UnsupportedTypeException
     * @throws \Box\Spout\Writer\Exception\WriterNotOpenedException
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function testExportWithGivenIds()
    {
        $expectedIds = array_map(function ($impact) {
            return $impact['id'];
        }, Impact::all()->take(2)->toArray());

        $pathToFile = ImpactExportHelper::exportImpacts($this->user, 'excel', $expectedIds);
        $absolutePathToFile = storage_path(config('settings.file_path') . '/' . $pathToFile);

        $spreadsheet = self::openSpreadsheet($absolutePathToFile);
        // +1 to take header row into account.
        $expectedLastRow = (count($expectedIds) + 1);

        // Correct impacts are exported.
        $impactNumberCells = $spreadsheet->getActiveSheet()->rangeToArray("A2:A$expectedLastRow");
        $idsInSpreadsheet = array_map(function ($impact_number_cell) {
            return Impact::where('impact_number', $impact_number_cell[0])->take(1)->get()[0]->id;
        }, $impactNumberCells);
        $this->assertEquals($expectedIds, $idsInSpreadsheet);

        // No additional impacts are exported.
        $supposedlyFirstEmptyRow = ($expectedLastRow + 1);
        $lastTestingRow = ($supposedlyFirstEmptyRow + 15);
        $supposedlyEmptyCells = $spreadsheet->getActiveSheet()
            ->rangeToArray("A$supposedlyFirstEmptyRow:A$lastTestingRow");
        $supposedlyEmptyIdsInSpreadsheet = array_map(function ($empty_cell) {
            return $empty_cell[0];
        }, $supposedlyEmptyCells);
        $expectedEmptyIds = [];
        for ($i = 0; $i < 16; $i++) {
            $expectedEmptyIds[$i] = null;
        }
        $this->assertEquals($expectedEmptyIds, $supposedlyEmptyIdsInSpreadsheet);
    }
}
