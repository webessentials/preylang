<?php

namespace App\Http\Controllers;

use App\Models\Villager;
use Illuminate\Database\Eloquent\Builder ;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest\CreateUserRequest;
use App\Http\Requests\UserRequest\UpdateUserRequest;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* @var User $currentUser */
        $currentUser = Auth::user();
        $users = null;
        $recordsPerPage = config('settings.record_per_page');
        $keyword = $request->keyword;
        if (Input::get('perpage')) {
            $recordsPerPage = (int)Input::get('perpage');
        }
        if ($currentUser->can('viewListSuperAdmin', User::class)) {
            $query = User::select('*');
            $query = $this->searchFilter($query, $keyword);
            $userGroupId = session(config('settings.global_user_group_session_name'));
            if ($userGroupId) {
                $query->where('user_group_id', $userGroupId);
            }
            $users = $query->paginate($recordsPerPage);
        } elseif ($currentUser->can('viewListAdmin', User::class)) {
            $query = User::whereUserGroupId($currentUser->user_group_id);
            $query = $this->searchFilter($query, $keyword);
            $users = $query->paginate($recordsPerPage);
        } else {
            return abort(401);
        }

        return view('templates.userManagement.index', compact('users', 'recordsPerPage', 'keyword'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /* @var User $user */
        $user = Auth::user();
        if ($user->can('storeAsSuperAdmin', User::class)) {
            $villagers = Villager::orderBy('name', 'asc')->get();
            $userGroups = UserGroup::all();
        } elseif ($user->can('storeByGroup', User::class)) {
            $villagers = Villager::whereUserGroupId($user->user_group_id)->orderBy('name', 'asc')->get();
            $userGroups = is_int($user->user_group_id) ? $user->userGroup->name : '';
        } else {
            return abort(401);
        }
        return view('templates.userManagement.create', compact('villagers', 'userGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest\CreateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        /* @var User $currentUser */
        $currentUser = Auth::user();
        /* @var User $user */
        $user = new User();
        if ($request['role'] !== config('settings.villager_role')) {
            $request['villager_id'] = null;
        }
        $request['password'] = bcrypt($request['password']);
        if ($currentUser->can('storeAsSuperAdmin', User::class)) {
            $request['user_group_id'] = !empty($request['user_group_id']) ? $request['user_group_id'] : null;
            $user->createUpdateUser($request);
        } elseif ($currentUser->can('storeByGroup', User::class)) {
            $roles = config('settings.user_low_level_roles');
            if (!in_array($request['role'], $roles)) {
                return abort(401);
            }
            $request['user_group_id'] = $currentUser->user_group_id;
            $user->createUpdateUser($request);
        } else {
            return abort(401);
        }
        return redirect(route('user.index'))->with('success', Lang::get('preylang.saveSuccess'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /* @var User $currentUser */
        $currentUser = Auth::user();
        /* @var User $user */
        $user = User::find($id);
        if (!$user instanceof User) {
            return abort(404);
        }
        if ($currentUser->can('updateAsSuperAdmin', User::class)) {
            $villagers = Villager::orderBy('name', 'asc')->get();
            $userGroups = UserGroup::all();
        } elseif ($currentUser->can('updateByGroup', $user)) {
            $villagers = Villager::whereUserGroupId($currentUser->user_group_id)->orderBy('name', 'asc')->get();
            $userGroups = is_int($currentUser->user_group_id) ? $currentUser->userGroup->name : '';
        } else {
            return abort(401);
        }
        return view('templates.userManagement.edit', compact('user', 'villagers', 'userGroups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UserRequest\UpdateUserRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        /* @var User $currentUser */
        $currentUser = Auth::user();
        /* @var User $user */
        $user = User::find($id);
        if (empty($request['password'])) {
            $request['password'] = $user->password;
        } else {
            $request['password'] = bcrypt($request['password']);
        }
        if ($request['role'] !== config('settings.villager_role')) {
            $request['villager_id'] = null;
        }
        if ($request['active'] === null) {
            if ($currentUser->id == $id) {
                $request['active'] = 1;
            } else {
                $request['active'] = 0;
            }
        }
        if ($currentUser->can('updateAsSuperAdmin', User::class)) {
            $request['user_group_id'] = !empty($request['user_group_id']) ? $request['user_group_id'] : null;
            $user->createUpdateUser($request);
        } elseif ($currentUser->can('updateByGroup', $user)) {
            if ($id == $currentUser->id) {
                $request['role'] = $currentUser->role;
            }
            $roles = config('settings.user_low_level_roles');
            if (!in_array($request['role'], $roles)) {
                return abort(401);
            }
            $request['user_group_id'] = $currentUser->user_group_id;
            $user->createUpdateUser($request);
        } else {
            return abort(401);
        }
        return redirect(route('user.index'))->with('success', Lang::get('preylang.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function delete($id)
    {
        /* @var User $currentUser */
        $currentUser = Auth::user();
        /* @var User $user */
        $user = User::find($id);
        if (!$user instanceof User) {
            return abort(404);
        }
        if ($currentUser->can('deleteAsSuperAdmin', User::class) || $currentUser->can('deleteByGroup', $user)) {
            $user->delete();
            return redirect(route('user.index'))->with('success', Lang::get('preylang.deleteSuccess'));
        }
        return abort(401);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userSetting()
    {
        $id = \Auth::user()->id;
        /* @var User $user */
        $user = User::find($id);
        return view('templates.userManagement.userSetting', compact('user'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserSetting(Request $request)
    {
        $id = \Auth::user()->id;
        /* @var User $user */
        $user = User::find($id);
        $locale = $request['language_key'];
        $user->language_key = $locale;
        $user->save();

        return redirect(route('dashboard'))->with('success', Lang::get('preylang.updateSuccess'));
    }

    /**
     * @param Builder $query
     * @param string $keyword
     * @return Builder
     */
    private function searchFilter(Builder $query, $keyword)
    {
        if (!empty($keyword)) {
            $keyword = '%' . $keyword . '%';
            $query->where(function ($query) use ($keyword) {
                $query->orWhere('username', 'like', $keyword)
                    ->orWhere('first_name', 'like', $keyword)
                    ->orWhere('last_name', 'like', $keyword)
                    ->orWhere('role', 'like', $keyword)
                    ->orWhere('email', 'like', $keyword);
            });
        }
        return $query;
    }
}
