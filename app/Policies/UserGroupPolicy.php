<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserGroupPolicy extends BasePolicy
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
}
