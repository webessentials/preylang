<?php

namespace App\Models;

use App\Elasticsearch\EditHistoryIndexConfigurator;
use App\Helpers\ActivityHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Lang;
use Kyslik\ColumnSortable\Sortable;
use ScoutElastic\Searchable;

/**
 * App\Models\EditHistory
 *
 * @property int $id
 * @property string $field_list
 * @property string $value_list
 * @property int|null $user_id
 * @property int|null $impact_id
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereFieldList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereImpactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EditHistory whereValueList($value)
 * @mixin \Eloquent
 */
class EditHistory extends AbstractModel
{
    use Sortable;
    use Searchable;

    /** @var array $fillable */
    protected $fillable = [
        'field_list',
        'value_list',
        'user_id',
        'impact_id',
        'type',
        'updated_at',
        'persistence_object_identifier'
    ];

    /** @var array $sortable */
    public $sortable = [
        'impact_number',
        'modified_date',
        'category_path',
        'user_email',
        'field_list',
    ];

    /** @var string $indexConfigurator */
    protected $indexConfigurator = EditHistoryIndexConfigurator::class;

    /**
     * Here you can specify a mapping for a model fields
     * @var array $mapping
     */
    protected $mapping = [
        'properties' => [
            'id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'impact_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'raw_impact_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'impact_number' => [
                'type' => 'text',
                'analyzer' => 'keyword',
                'search_analyzer' => 'keyword',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'modified_date' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'fields' => [
                    'raw' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss'
                    ]
                ]
            ],
            'category_path' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'category_path_km' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'user_email' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'field_list' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'user_group_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'username' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'category' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'sub_category_1' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'sub_category_2' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'sub_category_3' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'sub_category_4' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'leaf_category' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function impact()
    {
        return $this->belongsTo('App\Models\Impact');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        // Customize array.
        // Only listing fields and search fields.
        return [
            'id' => $this->id,
            'impact_id' => $this->impact_id ? $this->impact_id : null,
            'raw_impact_id' => $this->impact ? $this->impact->raw_impact_id : null,
            'impact_number' => $this->impact ? $this->impact->impact_number : '',
            'modified_date' => $this->updated_at ? $this->updated_at->format(config('settings.date_time_format')) : null,
            'category_path' => $this->impact ? $this->impact->getCategoryPath() : '',
            'category_path_km' => $this->impact ? $this->impact->getCategoryPathKm() : '',
            'user_email' => $this->user ? $this->user->email : '',
            'field_list' => ActivityHelper::renderFieldList($this->field_list),
            'user_group_id' => $this->impact ? $this->impact->villager->user_group_id : null,
            'username' => $this->user ? $this->user->username : '',
            'category' => $this->impact->category ? $this->impact->category->name : '',
            'sub_category_1' => $this->impact->subCategory1 ? $this->impact->subCategory1->name : '',
            'sub_category_2' => $this->impact->subCategory2 ? $this->impact->subCategory2->name : '',
            'sub_category_3' => $this->impact->subCategory3 ? $this->impact->subCategory3->name : '',
            'sub_category_4' => $this->impact->subCategory4 ? $this->impact->subCategory4->name : '',
            'leaf_category' => $this->impact->subCategory5 ? $this->impact->subCategory5->name : '',
        ];
    }
}
