<?php

namespace App\Http\Controllers;

use App\Helpers\CategoryHelper;
use App\Helpers\ImpactHelper;
use App\Models\Category;
use App\Models\Impact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{
    /** @var int maximum number of impacts to deliver to the map */
    private static $maximumNumberOfImpacts = 50000;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = CategoryHelper::getCategoriesByLevel();

        return view(
            'templates.map.index',
            compact('categories')
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getImpactsWithLocation(Request $request)
    {
        $user = Auth::user();
        $searchFields = ImpactHelper::defaultSearchFields() + ['has_location' => true];
        $searchFields = array_merge($searchFields, $request->only(array_keys($searchFields)));
        if ($user->can('viewListForAllGroups', Impact::class)) {
            $userGroupId = session(config('settings.global_user_group_session_name'));
            if ($userGroupId) {
                $searchFields['userGroup'] = $userGroupId;
            }
        } elseif ($user->can('viewListByGroup', Impact::class)) {
            $searchFields['userGroup'] = $user->user_group_id;
        } else {
            return abort(401);
        }
        $searchRules = $this->buildFilterQuery($searchFields);
        $query = Impact::search('ANY')
            ->rule(function () use ($searchRules) {
                return $searchRules;
            })
            ->select(['location', 'report_date', 'category', 'category_path'])
            ->where('active', true);
        return $impacts = $query->take(self::$maximumNumberOfImpacts)->raw();
    }
}
