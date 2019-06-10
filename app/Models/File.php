<?php

namespace App\Models;

/**
 * App\Models\File
 *
 * @property int $id
 * @property string $file_name
 * @property string $file_type
 * @property int|null $impact_id
 * @property int $is_imported
 * @property string|null $import_date
 * @property int $facebook_post
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $report_date
 * @property string|null $original_file_name
 * @property int $converted
 * @property string|null $converted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereConverted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereConvertedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereFacebookPost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereImpactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereImportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereIsImported($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereOriginalFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class File extends AbstractModel
{
    /** @var array $fillable */
    protected $fillable = [
        'file_name',
        'file_type',
        'impact_id',
        'is_imported',
        'import_date',
        'facebook_post',
        'latitude',
        'longitude',
        'report_date',
        'original_file_name',
        'converted',
        'converted_at',
        'persistence_object_identifier'
    ];
}
