<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the settings list for super admin.
     *
     * @param User $user
     * @return bool
     */
    public function viewListSuperAdmin(User $user)
    {
        return parent::checkSuperAdminRole($user);
    }


    /**
     * Determine whether the user can view the settings list for super data manager.
     *
     * @param User $user
     * @return bool
     */
    public function viewListSuperDataManager(User $user)
    {
        return parent::checkSuperDataManagerRole($user);
    }
}
