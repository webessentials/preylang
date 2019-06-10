<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest\SettingCreateRequest;
use App\Http\Requests\SettingRequest\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class DesignationController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('viewListSuperDataManager', Setting::class)) {
            /* @var Setting $designations */
            $designations = Setting::where('type', '=', config('settings.setting_types.4'))->orderBy('name')->get();
            return view('templates.settings.designationSetting.designation', compact('designations'));
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
        if ($user->can('updateAsSuperDataManager', Setting::class)) {
            /* @var Setting $designation */
            $designation = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.4'))->first();
            if (!$designation instanceof Setting) {
                return abort(404);
            }
            if ($designation->read_only) {
                return redirect(route('designation.index'))->with('error', Lang::get('preylang.canNotBeEdited'));
            }
            return view('templates.settings.designationSetting.editDesignation', compact('designation'));
        }
        return abort(401);
    }

    /**
     * @param SettingUpdateRequest $request
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SettingUpdateRequest $request, $id)
    {
        $user = Auth::user();
        if ($user->can('updateAsSuperDataManager', Setting::class)) {
            /* @var Setting $designation */
            $designation = Setting::find($id);
            $designation->createUpdateRecord($request);
            return redirect(route('designation.index'))->with('success', Lang::get('preylang.updateSuccess'));
        }
        return abort(401);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->can('storeAsSuperDataManager', Setting::class)) {
            return view('templates.settings.designationSetting.createDesignation');
        }
        return abort(401);
    }

    /**
     * @param SettingCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SettingCreateRequest $request)
    {
        $user = Auth::user();
        if ($user->can('storeAsSuperDataManager', Setting::class)) {
            /* @var Setting $designation */
            $designation = new Setting();
            $request['type'] = config('settings.setting_types.4');
            $designation->createUpdateRecord($request);
            return redirect(route('designation.index'))->with('success', Lang::get('preylang.saveSuccess'));
        }
        return abort(401);
    }

    /**
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function delete($id)
    {
        $user = Auth::user();
        if ($user->can('deleteAsSuperDataManager', Setting::class)) {
            /* @var Setting $designation */
            $designation = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.4'))->first();
            if (!$designation instanceof Setting) {
                return abort(404);
            }
            if ($designation->designationImpacts->count() === 0) {
                $designation->delete();
                return redirect(route('designation.index'))->with('success', Lang::get('preylang.deleteSuccess'));
            } else {
                return redirect(route('designation.index'))->with('error', Lang::get('preylang.canNotBeDeleted'));
            }
        }
        return abort(401);
    }
}
