<?php

namespace App\Http\Controllers;

use App\Http\Requests\VillagerRequest\CreateVillagerRequest;
use App\Http\Requests\VillagerRequest\UpdateVillagerRequest;
use App\Models\EditHistory;
use App\Models\Impact;
use App\Models\RawImpact;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\UserGroup;
use App\Models\Villager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Integer;

class VillagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $recordsPerPage = (int)Config::get('settings.record_per_page');
        $keyword = '';
        $query = Villager::sortable(['name' => 'asc']);
        if (Input::get('perpage')) {
            $recordsPerPage = (int)Input::get('perpage');
        }
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($query) use ($keyword) {
                $query->whereHas('province', function ($sub_query) use ($keyword) {
                    $sub_query->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('name_kh', 'like', '%' . $keyword . '%');
                })
                    ->orWhere('villagers.name', 'like', '%' . $keyword . '%')
                    ->orWhere('villagers.device_imei', 'like', '%' . $keyword . '%');
            });
        }

        $user = Auth::user();
        if (!$user->can('viewListForAllGroups', Villager::class)) {
            $query->where('user_group_id', '=', Auth::user()->user_group_id);
        } else {
            $userGroupId = session(config('settings.global_user_group_session_name'));
            if ($userGroupId) {
                $query->where('user_group_id', $userGroupId);
            }
        }
        $villagers = $query->paginate($recordsPerPage);
        return view('templates.villager.index', compact('villagers', 'recordsPerPage', 'keyword'));
    }

    /**
     * @param integer $villagerId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function show($villagerId)
    {
        $user = Auth::user();
        /* @var Villager $villager */
        $villager = Villager::find($villagerId);
        if (!$villager instanceof Villager) {
            return abort(404);
        }
        /* @var Setting $provinces */
        $provinces = Setting::where('type', '=', 'province')->get();
        if ($user->can('showDetailForAllGroups', Villager::class) || $user->can('showByGroup', $villager)) {
            return view('templates.villager.show', compact('villager'));
        }
        return abort(401);
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
        /* @var Setting $provinces */
        $provinces = Setting::where('type', '=', 'province')->get();
        if ($user->can('storeAsSuperAdmin', Villager::class)) {
            /* @var UserGroup $userGroups */
            $userGroups = UserGroup::all();
        } elseif ($user->can('storeByGroup', Villager::class)) {
            $userGroups = is_int($user->user_group_id) ? $user->userGroup->name : '';
        } else {
            return abort(401);
        }
        return view('templates.villager.create', compact('provinces', 'userGroups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\VillagerRequest\CreateVillagerRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVillagerRequest $request)
    {
        $user = Auth::user();
        /* @var Villager $villager */
        $villager = new Villager;
        $province = Setting::find($request['province_id']);
        $request['name'] = $villager->getNextId($province->sys_value);
        if ($user->can('storeAsSuperAdmin', Villager::class)) {
            $request['user_group_id'] = $request['user_group_id'] === '' ? null : $request['user_group_id'];
        } elseif ($user->can('storeByGroup', $villager)) {
            $request['user_group_id'] = is_int($user->user_group_id) ? $user->user_group_id : null;
        } else {
            return abort(401);
        }
        $villager->createUpdateRecord($request);
        return redirect(route('villager.index'))->with('success', Lang::get('preylang.saveSuccess'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        /* @var Villager $villager */
        $villager = Villager::find($id);
        if (!$villager instanceof Villager) {
            return abort(404);
        }
        /* @var Setting $provinces */
        $provinces = Setting::where('type', '=', 'province')->get();
        if ($user->can('updateAsSuperAdmin', Villager::class)) {
            /* @var UserGroup $userGroup */
            $userGroups = UserGroup::all();
        } elseif ($user->can('updateByGroup', $villager)) {
            $userGroups = $villager->userGroup;
        } else {
            return abort(401);
        }
        return view('templates.villager.edit', compact('villager', 'provinces', 'userGroups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\VillagerRequest\UpdateVillagerRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVillagerRequest $request, $id)
    {
        /* @var User $user */
        $user = Auth::user();
        /* @var Villager $villager */
        $villager = Villager::find($id);
        if ($request['new_password'] != null) {
            $request['password'] = $request['new_password'];
            $validator = Validator::make($request->all(), [
                'password' => 'required|max:255|min:6'
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        } else {
            $request['password'] = $villager->password;
        }
        if ((int)$request['province_id'] !== $villager->province_id) {
            /* @var Villager $newVillager */
            $newVillager = new Villager;
            /* @var Setting $province */
            $province = Setting::find($request['province_id']);
            $request['name'] = $newVillager->getNextId($province->sys_value);
        }
        if ($user->can('updateAsSuperAdmin', Villager::class)) {
            $request['user_group_id'] = $request['user_group_id'] === '' ? null : $request['user_group_id'];
        } elseif ($user->can('updateByGroup', $villager)) {
            $request['user_group_id'] = is_int($user->user_group_id) ? $user->user_group_id : null;
        } else {
            return abort(401);
        }
        $villager->createUpdateRecord($request);
        Impact::where('villager_id', $id)->searchable();
        RawImpact::where('villager_id', $id)->searchable();
        EditHistory::whereHas('impact', function ($query) use ($id) {
            $query->where('villager_id', $id);
        })->searchable();
        return redirect(route('villager.index'))->with('success', Lang::get('preylang.updateSuccess'));
    }

    /**
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $user = Auth::user();
        /* @var Villager $villager */
        $villager = Villager::find($id);
        if (!$villager instanceof Villager) {
            return abort(404);
        }
        if ($user->can('deleteAsSuperAdmin', Villager::class) || $user->can('deleteByGroup', $villager)) {
            $villager->delete();
            return redirect(route('villager.index'))->with('success', Lang::get('preylang.deleteSuccess'));
        }
        return abort(401);
    }

    /**
     * Get default search fields
     *
     * @return array
     */
    protected function defaultSearchFields()
    {
        return [
            'name' => '',
            'device_imei' => '',
            'province' => ''
        ];
    }
}
