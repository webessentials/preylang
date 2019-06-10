<?php

namespace App\Http\Controllers;

use App\Models\EditHistory;
use App\Models\Villager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class EditHistoryController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = '';
        $recordsPerPage = (int)Config::get('settings.record_per_page');
        if (Input::get('perpage')) {
            $recordsPerPage = (int)Input::get('perpage');
        }

        $sortingField = 'modified_date';
        $sortingDirection = 'desc';
        if ($request->has('sort') && $request->has('direction')) {
            $sortingField = $request->sort;
            $sortingDirection = $request->direction;
        }

        if (($request->has('keyword') && !is_null($request->keyword))) {
            $keyword = $request->keyword;
            $query = EditHistory::search($keyword);
        } else {
            $query = EditHistory::search('*');
        }

        // List only their own group, while super admin/data manager can see all.
        $user = Auth::user();
        if ($user->can('viewListForAllGroups', Villager::class)) {
            $userGroupId = session(config('settings.global_user_group_session_name'));
        } elseif ($user->can('viewListByGroup', Villager::class)) {
            // User must have a group, if not should throw exception or abort!
            if (! $user->user_group_id) {
                return abort(401);
            }
            $userGroupId = $user->user_group_id;
        } else {
            return abort(401);
        }

        if ($userGroupId) {
            $query->where('user_group_id.raw', (int)$userGroupId);
        }
        $query->orderBy($sortingField, $sortingDirection);
        $activities = $query->paginate($recordsPerPage);

        return view('templates.activity.index', compact('activities', 'recordsPerPage', 'keyword'));
    }
}
