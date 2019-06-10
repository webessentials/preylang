<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest\SettingCreateRequest;
use App\Http\Requests\SettingRequest\SettingUpdateRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class ThreateningController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('viewListSuperDataManager', Setting::class)) {
            /* @var Setting $threatenings */
            $threatenings = Setting::where('type', '=', config('settings.setting_types.5'))->orderBy('name')->get();
            return view('templates.settings.threateningSetting.threatening', compact('threatenings'));
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
            /* @var Setting $threatening */
            $threatening = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.5'))->first();
            if (!$threatening instanceof Setting) {
                return abort(404);
            }
            if ($threatening->read_only) {
                return redirect(route('threatening.index'))->with('error', Lang::get('preylang.canNotBeEdited'));
            }
            return view('templates.settings.threateningSetting.editThreatening', compact('threatening'));
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
            /* @var Setting $threatening */
            $threatening = Setting::find($id);
            $threatening->createUpdateRecord($request);
            return redirect(route('threatening.index'))->with('success', Lang::get('preylang.updateSuccess'));
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
            return view('templates.settings.threateningSetting.createThreatening');
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
            /* @var Setting $threatening */
            $threatening = new Setting();
            $request['type'] = config('settings.setting_types.5');
            $threatening->createUpdateRecord($request);
            return redirect(route('threatening.index'))->with('success', Lang::get('preylang.saveSuccess'));
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
            /* @var Setting $threatening */
            $threatening = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.5'))->first();
            if (!$threatening instanceof Setting) {
                return abort(404);
            }
            if ($threatening->threateningImpacts->count() === 0) {
                $threatening->delete();
                return redirect(route('threatening.index'))->with('success', Lang::get('preylang.deleteSuccess'));
            } else {
                return redirect(route('threatening.index'))->with('error', Lang::get('preylang.canNotBeDeleted'));
            }
        }
        return abort(401);
    }
}
