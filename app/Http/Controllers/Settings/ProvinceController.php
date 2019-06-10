<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProvinceRequest\CreateProvinceRequest;
use App\Http\Requests\ProvinceRequest\UpdateProvinceRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class ProvinceController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->can('viewListSuperAdmin', Setting::class)) {
            /* @var Setting $provinces */
            $provinces = Setting::where('type', '=', config('settings.setting_types.7'))->get();
            return view('templates.settings.provinceSetting.province', compact('provinces'));
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
        if ($user->can('updateAsSuperAdmin', Setting::class)) {
            /* @var Setting $province */
            $province = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.7'))->first();
            if (!$province instanceof Setting) {
                return abort(404);
            }
            return view('templates.settings.provinceSetting.editProvince', compact('province'));
        }
        return abort(401);
    }

    /**
     * @param UpdateProvinceRequest $request
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProvinceRequest $request, $id)
    {
        $user = Auth::user();
        if ($user->can('updateAsSuperAdmin', Setting::class)) {
            /* @var Setting $province */
            $province = Setting::find($id);
            if (isset($request['sys_value'])) {
                unset($request['sys_value']);
            }
            $province->createUpdateRecord($request);
            return redirect(route('province.index'))->with('success', Lang::get('preylang.updateSuccess'));
        }
        return abort(401);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();
        if ($user->can('storeAsSuperAdmin', Setting::class)) {
            return view('templates.settings.provinceSetting.createProvince');
        }
        return abort(401);
    }

    /**
     * @param CreateProvinceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateProvinceRequest $request)
    {
        $user = Auth::user();
        if ($user->can('storeAsSuperAdmin', Setting::class)) {
            /* @var Setting $province */
            $province = new Setting();
            $request['sys_value'] = strtoupper($request['sys_value']);
            $request['type'] = config('settings.setting_types.7');
            $province->createUpdateRecord($request);
            return redirect(route('province.index'))->with('success', Lang::get('preylang.saveSuccess'));
        }
        return abort(401);
    }

    /**
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Exception
     */
    public function delete($id)
    {
        $user = Auth::user();
        if ($user->can('deleteAsSuperAdmin', Setting::class)) {
            /* @var Setting $province */
            $province = Setting::where('id', '=', $id)->where('type', '=', config('settings.setting_types.7'))->first();
            if (!$province instanceof Setting) {
                return abort(404);
            }
            if ($province->villagers->count() === 0) {
                $province->delete();
                return redirect(route('province.index'))->with('success', Lang::get('preylang.deleteSuccess'));
            } else {
                return redirect(route('province.index'))->with('error', Lang::get('preylang.canNotBeDeleted'));
            }
        }
        return abort(401);
    }
}
