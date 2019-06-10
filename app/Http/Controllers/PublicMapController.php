<?php

namespace App\Http\Controllers;

use App\Helpers\CategoryHelper;
use App\Helpers\ImpactHelper;
use App\Helpers\ResponseHelper;
use App\Models\Category;
use App\Models\Impact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PublicMapController extends Controller
{
    /** @var int maximum number of impacts to deliver to the map */
    private static $maximumNumberOfImpacts = 50000;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $subCategory1 = $request->sub_category_1;
        $dateRangeFrom = isset($request->dateRange['from']) ? $request->dateRange['from'] : null;
        $dateRangeTo = isset($request->dateRange['to']) ? $request->dateRange['to'] : null;
        $activitiesCategory = Category::whereName('Activities')->get()->first();
        $categories = CategoryHelper::getCategoriesByLevel(1, $activitiesCategory->id);
        return view('templates.map.publicMap', compact('categories', 'subCategory1', 'dateRangeFrom', 'dateRangeTo'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getImpactsForPublicMap(Request $request)
    {
        $activitiesCategory = Category::whereName('Activities')->get()->first();
        $searchFields = ImpactHelper::defaultSearchFields() + ['has_location' => true];
        $searchFields['category'] = $activitiesCategory->id;
        $searchFields['edited'] = 'edited';
        $searchFields['excluded'] = 'false';
        $searchFields = array_merge($searchFields, $request->only(array_keys($searchFields)));
        $searchRules = $this->buildFilterQuery($searchFields);
        $query = Impact::search('ANY')
            ->rule(function () use ($searchRules) {
                return $searchRules;
            })
            ->select(['location','report_date', 'category', 'category_path'])
            ->where('active', true);
        return $query->take(self::$maximumNumberOfImpacts)->raw();
    }
}
