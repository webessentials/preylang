<?php

namespace App\Models;

use App\Helpers\CategoryHelper;
use Kyslik\ColumnSortable\Sortable;
use App\Elasticsearch\ImpactIndexConfigurator;
use ScoutElastic\Searchable;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

/**
 * App\Models\Impact
 *
 * @property int $id
 * @property int|null $number_of_items
 * @property string|null $name
 * @property string|null $employer
 * @property string|null $license
 * @property int $agreement
 * @property int $by_visual
 * @property int $by_audio
 * @property int $by_track
 * @property int $burned_wood
 * @property string|null $report_to
 * @property string|null $report_date
 * @property int $active
 * @property string|null $note
 * @property string|null $note_kh
 * @property string|null $patroller_note
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int $modified
 * @property int $excluded
 * @property int|null $excluded_reason_id
 * @property string|null $excluded_note
 * @property string $impact_number
 * @property int|null $villager_id
 * @property array $categories
 * @property int $category_modified
 * @property int|null $offender_id
 * @property int|null $threatening_id
 * @property int|null $reason_id
 * @property int|null $designation_id
 * @property int|null $victim_type_id
 * @property int|null $proof_id
 * @property string|null $witness
 * @property string|null $location
 * @property int|null $raw_impact_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Setting $designation
 * @property-read \App\Models\Setting|null $excludedReason
 * @property-read \App\Models\Setting|null $offender
 * @property-read \App\Models\Setting|null $proof
 * @property-read \App\Models\Setting|null $reason
 * @property-read \App\Models\Setting|null $threatening
 * @property-read \App\Models\Setting|null $victimType
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereAgreement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereBurnedWood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereByAudio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereByTrack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereByVisual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereCategories($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereCategoryModified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereDesignationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereEmployer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereExcluded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereExcludedNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereExcludedReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereImpactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereModified($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereNoteKh($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereNumberOfItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereOffenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact wherePatrollerNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereProofId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereRawImpactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereReportTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereThreateningId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereVictimTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereVillagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact whereWitness($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read bool $audio
 * @property-read mixed $audios
 * @property-read bool $facebook
 * @property \Highlight|null $highlight
 * @property-read bool $image
 * @property-read mixed $images
 * @property-read \App\Models\Villager|null $villager
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Impact sortable($defaultParameters = null)
 */
class Impact extends AbstractModel
{
    use Sortable;
    use Searchable;
    use HasJsonRelationships;

    /** @var string */
    const FORMAT_PREFIX = 'PLI-';

    /** @var array */
    public static $apiRule = [
        'phoneId' => 'required',
        'category' => 'required',
    ];

    /** @var array */
    protected $fillable = [
        'number_of_items',
        'name',
        'employer',
        'license',
        'agreement',
        'by_visual',
        'by_audio',
        'by_track',
        'burned_wood',
        'report_to',
        'report_date',
        'active',
        'note',
        'note_kh',
        'patroller_note',
        'latitude',
        'longitude',
        'modified',
        'excluded',
        'excluded_reason_id',
        'excluded_note',
        'impact_number',
        'villager_id',
        'categories',
        'category_modified',
        'offender_id',
        'threatening_id',
        'reason_id',
        'designation_id',
        'victim_type_id',
        'proof_id',
        'witness',
        'location',
        'raw_impact_id',
        'created_at',
        'updated_at',
        'persistence_object_identifier'
    ];

    /** @var array */
    protected $attributes = [
        'active' => true
    ];

    /** @var array $casts */
    protected $casts = [
        'categories' => 'array'
    ];

    /** @var array $sortable */
    public $sortable = ['raw_impact_id', 'categories', 'report_date', 'villager_name', 'excluded', 'modified'];
    /** @var string $indexConfigurator */
    protected $indexConfigurator = ImpactIndexConfigurator::class;
    /** @var array $searchRules */
    protected $searchRules = [];

    /**
     * Here you can specify a mapping for a model fields
     * @var array $mapping
     */
    protected $mapping = [
        'properties' => [
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
            'category' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]
            ],
            'category_name_kh' => [
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
            'created_at' => [
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
            'province_name' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]

            ],
            'province_name_kh' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ]
                ]

            ],
            'province_id' => [
                'type' => 'long',
                'fields' => [
                    'raw' => [
                        'type' => 'long',
                    ]
                ]
            ],
            'image' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'facebook' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'audio' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'excluded' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'modified' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'category_modified' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'active' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
                    ]
                ]
            ],
            'has_location' => [
                'type' => 'boolean',
                'fields' => [
                    'raw' => [
                        'type' => 'boolean',
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
            'raw_impact_id' => [
                'type' => 'integer',
                'fields' => [
                    'raw' => [
                        'type' => 'integer',
                    ]
                ]
            ],
            'location' => [
                'properties' => [
                    'latitude' => [
                        'type' => 'float',
                        'fields' => [
                            'raw' => [
                                'type' => 'float',
                            ]
                        ]
                    ],
                    'longitude' => [
                        'type' => 'float',
                        'fields' => [
                            'raw' => [
                                'type' => 'float',
                            ]
                        ]
                    ],
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
            'category_path' => [
                'type' => 'text',
            ],
        ]
    ];

    /**
     * @return array
     */
    public static function returnUpdateImpactRules()
    {
        return [
            'number_of_items' => 'integer',
        ];
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
    public function offender()
    {
        return $this->belongsTo('App\Models\Setting', 'offender_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function threatening()
    {
        return $this->belongsTo('App\Models\Setting', 'threatening_id');
    }

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
    public function designation()
    {
        return $this->belongsTo('App\Models\Setting', 'designation_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proof()
    {
        return $this->belongsTo('App\Models\Setting', 'proof_id');
    }

    /**
     * @param integer $rawImpactId
     *
     * @return string
     */
    public static function formatImpactNumber($rawImpactId)
    {
        return self::FORMAT_PREFIX . str_pad($rawImpactId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param integer $impactNumber
     *
     * @return int
     */
    public static function getImpactNumberFromFormat($impactNumber)
    {
        return (int) str_replace(self::FORMAT_PREFIX, '', $impactNumber);
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
     * Raw impact
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rawImpact()
    {
        return $this->belongsTo('App\Models\RawImpact');
    }

    /**
     * Impact Villager
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function villager()
    {
        return $this->belongsTo('App\Models\Villager')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany('App\Models\File');
    }

    /**
     * @return mixed
     */
    public function getImagesAttribute()
    {
        return $this->files()->whereFileType('image')->get();
    }

    /**
     * @return mixed
     */
    public function getAudiosAttribute()
    {
        return $this->files()->whereFileType('audio')->get();
    }

    /**
     * If impact has image
     *
     * @return boolean
     */
    public function getImageAttribute()
    {
        return (bool)$this->files()->whereFileType('image')->count();
    }

    /**
     * If impact has audio
     *
     * @return boolean
     */
    public function getAudioAttribute()
    {
        return (bool)$this->files()->whereFileType('audio')->count();
    }

    /**
     * If impact has facebook post
     *
     * @return boolean
     */
    public function getFacebookAttribute()
    {
        return (bool)$this->files()->whereFacebookPost(true)->count();
    }

    /**
     * @return string all category names joined by '>'
     */
    public function getCategoryPath()
    {
        $categoryPath = CategoryHelper::getCategoryName([$this->category, $this->categories['category']]);

        for ($i = 1; $i < 6; $i++) {
            $subCategoryName = 'sub_category_' . $i;
            $subCategory = $this->getCategoryByRelationField($subCategoryName);
            if ($subCategory && !empty($subCategory->name)) {
                $categoryPath .= ' > ';
                $categoryPath .= CategoryHelper::getCategoryName([$subCategory, $this->categories[$subCategoryName]]);
            }
        }

        if ($this->categories['permit'] && $this->categories['permit'] !== '') {
            $categoryPath .= ' > ' . $this->categories['permit'];
        }
        return $categoryPath;
    }

    /**
     * @return string all category names in Khmer joined by '>'
     */
    public function getCategoryPathKm()
    {
        $categoryPath = CategoryHelper::getCategoryNameKm([$this->category, $this->categories['category']]);

        for ($i = 1; $i < 6; $i++) {
            $subCategoryName = 'sub_category_' . $i;
            $subCategory = $this->getCategoryByRelationField($subCategoryName);
            if ($subCategory && !empty($subCategory->name_kh)) {
                $categoryPath .= ' > ';
                $categoryPath .= CategoryHelper::getCategoryNameKm([$subCategory, $this->categories[$subCategoryName]]);
            }
        }

        if ($this->categories['permit'] && $this->categories['permit'] !== '') {
            $categoryPath .= ' > ' . $this->categories['permit'];
        }
        return $categoryPath;
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
            'impact_number' => $this->impact_number,
            'category_id' => $this->category ? $this->category->id : null,
            'category' => $this->category ? $this->category->name : '',
            'category_name_kh' => $this->category ? $this->category->name_kh : '',
            'sub_category_1_id' => $this->subCategory1 ? $this->subCategory1->id : null,
            'sub_category_1' => $this->subCategory1 ? $this->subCategory1->name : $this->categories['sub_category_1'],
            'sub_category_2_id' => $this->subCategory2 ? $this->subCategory2->id : null,
            'sub_category_2' => $this->subCategory2 ? $this->subCategory2->name : $this->categories['sub_category_2'],
            'category_path' => $this->getCategoryPath(),
            'report_date' => $this->report_date,
            'villager_id' => $this->villager ? $this->villager->name : '',
            'province_name' => $this->villager ? $this->villager->province ? $this->villager->province->name : '' : '',
            'province_name_kh' => $this->villager ? $this->villager->province ? $this->villager->province->name_kh : '' : '',
            'province_id' => $this->villager ? $this->villager->province_id : '',
            'image' => $this->getImageAttribute(),
            'facebook' => $this->getFacebookAttribute(),
            'audio' => $this->getAudioAttribute(),
            'excluded' => (bool)$this->excluded,
            'modified' => (bool)$this->modified,
            'category_modified' => (bool)$this->category_modified,
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude
            ],
            'has_location' => ($this->longitude && $this->longitude) ? true : false,
            'active' => (bool)$this->active,
            'user_group_id' => $this->villager->user_group_id,
            'created_at' => $this->created_at ? $this->created_at->format(config('settings.date_time_format')) : null,
            'raw_impact_id' => $this->raw_impact_id,
        ];
    }
}
