<?php

namespace App\Models;

/**
 * App\Models\ExportLog
 *
 * @property int $id
 * @property string $search_option
 * @property string $token
 * @property string $file_name
 * @property string $file_type
 * @property string $user_email
 * @property int $generated
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereFileType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereGenerated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereSearchOption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ExportLog whereUserEmail($value)
 * @mixin \Eloquent
 */
class ExportLog extends AbstractModel
{
    /** @var array $fillable */
    protected $fillable = [
        'search_option', 'token', 'file_name', 'file_type', 'user_email', 'generated', 'updated_at'
    ];
}
