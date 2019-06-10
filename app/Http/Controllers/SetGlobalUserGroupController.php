<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SetGlobalUserGroupController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $request->session()->put([config('settings.global_user_group_session_name') => $request->get('id')]);
        $previousUrl = url()->previous();
        $previousUrl = preg_replace('/&page=(.*)/', '&page=1', $previousUrl);
        $previousUrl = preg_replace('/\\?page=(.*)/', '?page=1', $previousUrl);
        return \redirect($previousUrl);
    }
}
