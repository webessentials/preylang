<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest\SettingCreateRequest;
use App\Http\Requests\SettingRequest\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class VictimTypeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('viewListSuperDataManager', Setting::class)) {
            /* @var Setting $victimTypes */
            $victimTypes = Setting::where('type', '=', config('settings.setting_types.2'))->get();
            return view('templates.settings.victimTypeSetting.victimType', compact('victimTypes'));
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
            /* @var Setting $victimType */
            $victimType = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.2'))->first();
            if (!$victimType instanceof Setting) {
                return abort(404);
            }
            if ($victimType->read_only) {
                return redirect(route('victimType.index'))->with('error', Lang::get('preylang.canNotBeEdited'));
            }
            return view('templates.settings.victimTypeSetting.editVictimType', compact('victimType'));
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
            /* @var Setting $victimType */
            $victimType = Setting::find($id);
            $victimType->createUpdateRecord($request);
            return redirect(route('victimType.index'))->with('success', Lang::get('preylang.updateSuccess'));
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
            return view('templates.settings.victimTypeSetting.createVictimType');
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
            /* @var Setting $victimType */
            $victimType = new Setting();
            $request['type'] = config('settings.setting_types.2');
            $victimType->createUpdateRecord($request);
            return redirect(route('victimType.index'))->with('success', Lang::get('preylang.saveSuccess'));
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
            /* @var Setting $victimType */
            $victimType = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.2'))->first();
            if (!$victimType instanceof Setting) {
                return abort(404);
            }
            if ($victimType->victimTypeImpacts->count() === 0) {
                $victimType->delete();
                return redirect(route('victimType.index'))->with('success', Lang::get('preylang.deleteSuccess'));
            } else {
                return redirect(route('victimType.index'))->with('error', Lang::get('preylang.canNotBeDeleted'));
            }
        }
        return abort(401);
    }
}
