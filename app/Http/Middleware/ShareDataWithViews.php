<?php

namespace App\Http\Middleware;

use App\Models\UserGroup;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;

class ShareDataWithViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currentFiteredUserGroup = Lang::get('preylang.label.all');
        $userGroupId = session(config('settings.global_user_group_session_name'));
        if ($userGroupId) {
            $userGroup = UserGroup::find($userGroupId);
            if ($userGroup instanceof UserGroup) {
                $currentFiteredUserGroup = app()->getLocale() === 'en' ? $userGroup->name : $userGroup->name_kh;
            }
        }
        $userGroups = [];
        if (Auth::user()->role === config('settings.user_roles')[0] || Auth::user()->role === config('settings.user_roles')[2]) {
            $userGroups = UserGroup::all()->toArray();
        }

        $userGroupData = ['userGroups' => $userGroups, 'currentFiteredUserGroup' => $currentFiteredUserGroup];

        View::share('userGroupData', $userGroupData);
        return $next($request);
    }
}
