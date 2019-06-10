<?php

namespace App\Models;

use App\Elasticsearch\RawImpactIndexConfigurator;
use Kyslik\ColumnSortable\Sortable;
use ScoutElastic\Searchable;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * App\Models\RawImpactHelper
 *
 * @property int $id
 * @property string $categories
 * @property int|null $number_of_items
 * @property int|null $by_visual
 * @property int|null $by_audio
 * @property int|null $by_track
 * @property string|null $report_to
 * @property string|null $report_date
 * @property string|null $note
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int|null $villager_id
 * @property string|null $patroller_note
 * @property int|null $victim_type_id
 * @property int|null $reason_id
 * @property int $excluded
 * @property int|null $excluded_reason_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Setting|null $excludedReason
 * @property-read \App\Models\Setting|null $reason
 * @property-read \App\Models\Setting|null $victimType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereByAudio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereByTrack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereByVisual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereExcluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereExcludedReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereNumberOfItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact wherePatrollerNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereReportTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereVictimTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact whereVillagerId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Villager|null $villager
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RawImpact sortable($defaultParameters = null)
 */
class RawImpact extends AbstractModel
{
    use Sortable;
    use Searchable;
    use HasJsonRelationships;

    /** @var array $fillable */
    protected $fillable = [
        'categories',
        'number_of_items',
        'by_visual',
        'by_audio',
        'by_track',
        'report_to',
        'report_date',
        'note',
        'latitude',
        'longitude',
        'villager_id',
        'patroller_note',
        'victim_type_id',
        'reason_id',
        'excluded',
        'excluded_reason_id',
        'created_at',
        // Temporary field for data migration.
        'impact',
    ];

    /** @var array $casts */
    protected $casts = [
        'categories' => 'array'
    ];

    /** @var array $sortable */
    public $sortable = [
        'report_date',
        'id',
        'number_of_items',
        'villager_name',
        'categories',
        'user_group'
    ];

    /** @var string $indexConfigurator */
    protected $indexConfigurator = RawImpactIndexConfigurator::class;

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
            'report_date' => [
                'type' => 'date',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'fields' => [
                    'raw' => [
                        'type' => 'date',
                        'format' => 'yyyy-MM-dd HH:mm:ss'
                    ]
                ]
            ],
            'villager_id' => [
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
            'user_group' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'category_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'sub_category_1_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'sub_category_2_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'sub_category_3_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'sub_category_4_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'leaf_category_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'number_of_items' => [
                'type' => 'integer',
                'null_value' => 0,
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                        'null_value' => 0,
                    ]
                ]
            ],
        ]
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function victimType()
    {
        return $this->belongsTo('App\Models\Setting', 'victim_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reason()
    {
        return $this->belongsTo('App\Models\Setting', 'reason_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function excludedReason()
    {
        return $this->belongsTo('App\Models\Setting', 'excluded_reason_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function villager()
    {
        return $this->belongsTo('App\Models\Villager')->withTrashed();
    }

    /**
     * Impact category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'categories->category', 'id');
    }

    /**
     * Impact sub category 1
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory1()
    {
        return $this->belongsTo('App\Models\Category', 'categories->sub_category_1', 'id');
    }

    /**
     * Impact sub category 2
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory2()
    {
        return $this->belongsTo('App\Models\Category', 'categories->sub_category_2', 'id');
    }

    /**
     * Impact sub category 3
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory3()
    {
        return $this->belongsTo('App\Models\Category', 'categories->sub_category_3', 'id');
    }

    /**
     * Impact sub category 4
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory4()
    {
        return $this->belongsTo('App\Models\Category', 'categories->sub_category_4', 'id');
    }

    /**
     * Impact sub category 5
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory5()
    {
        return $this->belongsTo('App\Models\Category', 'categories->sub_category_5', 'id');
    }

    /**
     * @param string $jsonField
     *
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getCategoryByRelationField($jsonField)
    {
        return $this->belongsTo('App\Models\Category', 'categories->' . $jsonField, 'id')->first();
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
            'category_id' => $this->category ? $this->category->id : null,
            'category' => $this->category ? $this->category->name : '',
            'sub_category_1_id' => $this->subCategory1 ? $this->subCategory1->id : null,
            'sub_category_1' => $this->subCategory1 ? $this->subCategory1->name : $this->categories['sub_category_1'],
            'sub_category_2_id' => $this->subCategory2 ? $this->subCategory2->id : null,
            'sub_category_2' => $this->subCategory2 ? $this->subCategory2->name : $this->categories['sub_category_2'],
            'sub_category_3_id' => $this->subCategory3 ? $this->subCategory3->id : null,
            'sub_category_3' => $this->subCategory3 ? $this->subCategory3->name : $this->categories['sub_category_3'],
            'sub_category_4_id' => $this->subCategory4 ? $this->subCategory4->id : null,
            'sub_category_4' => $this->subCategory4 ? $this->subCategory4->name : $this->categories['sub_category_4'],
            'leaf_category_id' => $this->subCategory5 ? $this->subCategory5->id : null,
            'leaf_category' => $this->subCategory5 ? $this->subCategory5->name : $this->categories['sub_category_5'],
            'report_date' => $this->report_date ? $this->report_date : null,
            'villager_id' => $this->villager ? $this->villager->name : '',
            'user_group_id' => $this->villager ? $this->villager->user_group_id : null,
            'user_group' => $this->villager ? $this->villager->userGroup->name : '',
            'number_of_items' => $this->number_of_items ? $this->number_of_items : null
        ];
    }
}
