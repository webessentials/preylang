<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserGroupRequest\CreateUserGroupRequest;
use App\Http\Requests\UserGroupRequest\UpdateUserGroupRequest;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class UserGroupController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('viewListSuperAdmin', UserGroup::class)) {
            /* @var UserGroup $userGroups */
            $userGroups = UserGroup::paginate(config('settings.record_per_page'));
            return view('templates.settings.userGroupSetting.userGroup', compact('userGroups'));
        }
        return abort(401);
    }

    /**
     * @param integer $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $user = Auth::user();
        if ($user->can('updateAsSuperAdmin', UserGroup::class)) {
            /* @var UserGroup $userGroup */
            $userGroup = UserGroup::find($id);
            if (!$userGroup instanceof UserGroup) {
                return abort(404);
            }
            return view('templates.settings.userGroupSetting.editUserGroup', compact('userGroup'));
        }
        return abort(401);
    }

    /**
     * @param UpdateUserGroupRequest $request
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserGroupRequest $request, $id)
    {
        $user = Auth::user();
        if ($user->can('updateAsSuperAdmin', UserGroup::class)) {
            /* @var UserGroup $userGroup */
            $userGroup = UserGroup::find($id);
            $userGroup->createUpdateRecord($request);
            return redirect(route('userGroups.index'))->with('success', Lang::get('preylang.updateSuccess'));
        }
        return abort(401);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->can('storeAsSuperAdmin', UserGroup::class)) {
            return view('templates.settings.userGroupSetting.createUserGroup');
        }
        return abort(401);
    }

    /**
     * @param CreateUserGroupRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateUserGroupRequest $request)
    {
        $user = Auth::user();
        if ($user->can('storeAsSuperAdmin', UserGroup::class)) {
            /* @var UserGroup $userGroup */
            $userGroup = new UserGroup();
            $userGroup->createUpdateRecord($request);
            return redirect(route('userGroups.index'))->with('success', Lang::get('preylang.saveSuccess'));
        }
        return abort(401);
    }

    /**
     * @param integer $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Exception
     */
    public function delete($id, Request $request)
    {
        $user = Auth::user();
        if ($user->can('deleteAsSuperAdmin', UserGroup::class)) {
            /* @var UserGroup $userGroup */
            $userGroup = UserGroup::find($id);
            if (!$userGroup instanceof UserGroup) {
                return abort(404);
            }
            if ($userGroup->users->count() === 0 && $userGroup->villagers->count() === 0) {
                if ($userGroup->id === (int)session(config('settings.global_user_group_session_name'))) {
                    $request->session()->forget(config('settings.global_user_group_session_name'));
                }
                $userGroup->delete();
                return redirect(route('userGroups.index'))->with('success', Lang::get('preylang.deleteSuccess'));
            } else {
                return redirect(route('userGroups.index'))->with('error', Lang::get('preylang.canNotBeDeleted'));
            }
        }
        return abort(401);
    }
}
