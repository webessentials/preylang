<?php

namespace App\Http\Controllers;

use App\Elasticsearch\QueryHelper;
use App\Models\RawImpact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class RawImpactController extends Controller
{

    /**
     * @param \Illuminate\Http\Request $request
     * @param string $categoryId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function filterByCategory(Request $request, $categoryId)
    {
        $request['category'] = $categoryId;
        return $this->listRawImpact($request);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rawImpacts = $this->getRawImpactsOrAbort($request);
        return view('templates.rawImpact.index', compact('rawImpacts'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    private function listRawImpact(Request $request)
    {
        $categoryId = $request['category'];
        $rawImpacts = $this->getRawImpactsOrAbort($request, $categoryId);
        return view('templates.rawImpact.index', compact('rawImpacts'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param null $categoryId
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|void
     */
    protected function getRawImpactsOrAbort(Request $request, $categoryId = null)
    {
        $user = Auth::user();
        if ($user->can('viewListForAllGroups', RawImpact::class)) {
            $userGroupId = session(config('settings.global_user_group_session_name'));
        } elseif ($user->can('viewListByGroup', RawImpact::class)) {
            // User must have a group, if not should throw exception or abort!
            if (! $user->user_group_id) {
                return abort(401);
            }
            $userGroupId = $user->user_group_id;
        } else {
            return abort(401);
        }

        $sortingField = 'report_date';
        $sortingDirection = 'desc';
        if ($request->has('sort') && $request->has('direction')) {
            $sortingField = $request->sort;
            $sortingDirection = $request->direction;
        }
        $query = RawImpact::search('*');
        if ($categoryId) {
            $query = $query->where('category_id', (int)$categoryId);
        }
        if ($userGroupId) {
            $query = $query->where('user_group_id', (int)$userGroupId);
        }
        $query->orderBy($sortingField, $sortingDirection);
        return $query->paginate(Config::get('settings.record_per_page'));
    }
    /**
     * @param RawImpact $rawImpact
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(RawImpact $rawImpact)
    {
        $user = Auth::user();
        if ($user->can('showDetailForAllGroups', RawImpact::class) || $user->can('showByGroup', $rawImpact)) {
            return view('templates.rawImpact.show', compact('rawImpact', 'existingSubCategories', 'excludedReasons'));
        }
        return abort(401);
    }
}
