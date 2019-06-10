<?php

namespace App\Http\Controllers;

use App\Helpers\CategoryHelper;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\Impact;
use App\Helpers\ImpactHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class DashboardController extends Controller
{

    /** @var string */
    private static $provinceFieldName = 'province_name.raw';

    /** @var int */
    private static $numberOfProvinces = 0;

    /** @var string */
    private static $categoryFieldName = 'category.raw';

    /** @var int */
    private static $numberOfCategory = 0;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $provinces = $this->getProvincesForFilter();
        $categories = CategoryHelper::getCategoriesByLevel();
        $firstIncidentDate = config('settings.graph_settings.default_start_date');
        return view('dashboard', compact('provinces', 'categories', 'firstIncidentDate'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDataForGraph(Request $request)
    {
        if (Lang::locale() === 'km') {
            self::$provinceFieldName = 'province_name_kh.raw';
            self::$categoryFieldName = 'category_name_kh.raw';
        }

        self::$numberOfProvinces = Setting::where('type', 'province')->count();
        self::$numberOfCategory = Category::where('level', 0)->count();

        $graphType = isset($request['graphType']) ? $request['graphType'] : strtolower(config('settings.graphs.donut'));

        $user = Auth::user();
        if ($user->can('viewListByGroup', Impact::class)) {
            $request['userGroup'] = $user->user_group_id;
        } else {
            $userGroupId = session(config('settings.global_user_group_session_name'));
            if ($userGroupId) {
                $request['userGroup'] = $userGroupId;
            }
        }

        $request['excluded'] = 'no';
        $request['active'] = true;

        $dateRange = $request->dateRange;
        if (!isset($dateRange['from'])) {
            $dateRange['from'] = config('settings.graph_settings.default_start_date');
        }
        if (!isset($dateRange['to'])) {
            $dateRange['to'] = date('Y-m-d');
        }
        $request['dateRange'] = $dateRange;

        $searchFields = ImpactHelper::defaultSearchFields();
        $searchFields = array_merge($searchFields, $request->only(array_keys($searchFields)));
        $searchRules = ImpactHelper::buildFilterQuery($searchFields);
        if (empty($searchRules)) {
            $searchRules = [
                'filter' => []
            ];
        }

        if ($graphType === strtolower(config('settings.graphs.donut'))) {
            // Case donut graph.
            $data = $this->getDataForDonutChart($searchRules);
        } else {
            // Case line or bar graph.
            $data = $this->getDataForLineOrBarChart($searchRules);
        }

        $dateRangeFrom = $request->get('dateRange') ? $request->get('dateRange')['from'] : null;
        $dateRangeTo = $request->get('dateRange') ? $request->get('dateRange')['to'] : null;
        $data['title'] = self::getGraphTitle($request->get('provinces'), $request->get('category'), $dateRangeFrom, $dateRangeTo, $request->get('viewBy'));

        return response()->json([
            $data
        ]);
    }

    /**
     * @param array $searchRules
     *
     * @return array
     */
    private function getDataForLineOrBarChart($searchRules)
    {
        $rawQuery = [
            'from' => 0,
            'size' => 0,
            '_source' => [
                'excludes' => []
            ],
            'query' => [
                'bool' => $searchRules
            ],
            "aggs" => [
                "province" => [
                    "terms" => [
                        "field" => self::$provinceFieldName,
                        "size" => self::$numberOfProvinces,
                        "show_term_doc_count_error" => true,
                        "order" => [
                            "_count" => "desc",
                        ],
                    ],
                    "aggs" => [
                        "reportDate" => [
                            "date_histogram" => [
                                "field" => "report_date",
                                "interval" => "1M",
                                "time_zone" => "Asia/Jakarta",
                                "min_doc_count" => 0
                            ]
                        ]
                      ]
                ],
                "category" => [
                    "terms" => [
                        "field" => self::$categoryFieldName,
                        "size" => self::$numberOfCategory,
                        "order" => [
                            "_count" => "desc"
                        ],
                    ],
                    "aggs" => [
                        "reportDate" => [
                            "date_histogram" => [
                                "field" => "report_date",
                                "interval" => "1M",
                                "time_zone" => "Asia/Jakarta",
                                "min_doc_count" => 0
                            ]
                        ]
                    ]
                ]
            ],
            'docvalue_fields' => ["report_date", "report_date.raw"],
        ];
        $data = Impact::searchRaw($rawQuery);
        return $data;
    }

    /**
     * @param array $searchRules
     *
     * @return array
     */
    private function getDataForDonutChart($searchRules)
    {
        $rawQuery = [
            'from' => 0,
            'size' => 0,
            '_source' => [
                'excludes' => []
            ],
            'query' => [
                'bool' => $searchRules
            ],
            'aggs' => [
                'category' => [
                    'terms' => [
                        'field' => self::$categoryFieldName
                    ],
                ],
                'province' => [
                    'terms' => [
                        'field' => self::$provinceFieldName
                    ],
                ],
            ],
            'docvalue_fields' => ["report_date", "report_date.raw"],
        ];
        $data = Impact::searchRaw($rawQuery);
        return $data;
    }

    /**
     * @param array $provinces
     * @param string $category
     * @param string $startDate
     * @param string $endDate
     * @param string $viewBy
     *
     * @return string
     */
    private function getGraphTitle($provinces, $category, $startDate, $endDate, $viewBy = 'province')
    {
        $dateStr = '';
        if (!(empty($startDate)) && !(empty($endDate))) {
            $startDateStr = date('j M Y', strtotime($startDate));
            $endDateStr = date('j M Y', strtotime($endDate));
            $dateStr = ' (' . $startDateStr . ' - ' . $endDateStr . ')';
        }
        if (empty($provinces) && empty($category)) {
            $title = ($viewBy == 'province') ? Lang::get('preylang.graph.captionProvince') : Lang::get('preylang.graph.captionCategory');
        } elseif (empty($provinces)) {
            $category = Category::where('id', '=', $category)->first();
            $title = Lang::get('preylang.graph.captionCategory') . ' - ' . (Lang::locale() === 'en' ? $category->name : $category->name_kh);
        } elseif (empty($category)) {
            $title = self::generateProvinceNames($provinces);
        } else {
            $category = Category::where('id', $category)->first();
            $title = self::generateProvinceNames($provinces) . ' - ' . (Lang::locale() === 'en' ? $category->name : $category->name_kh);
        }

        return $title . $dateStr;
    }

    /***
     * @param array $provinces
     * @return string
     */
    private function generateProvinceNames($provinces)
    {
        $result = '';
        foreach ($provinces as $key => $province) {
            $province = Setting::where('name', $province)->where('type', 'province')->first();
            if ($key === 0) {
                $result = Lang::locale() === 'en' ? $province->name : $province->name_kh;
            } else {
                $result .= ', ' . (Lang::locale() === 'en' ? $province->name : $province->name_kh);
            }
        }

        if (count($provinces) > 1) {
            $result = Lang::get('preylang.graph.captionProvince') . ' (' . $result . ')';
        }

        return $result;
    }
}
