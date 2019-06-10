<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BasePolicy
{
    use HandlesAuthorization;

    // List and show for super admin and super data manager.

    /**
     * Determine whether the user can view the all groups record lists for super data manager and super admin.
     *
     * @param User $user
     * @return bool
     */
    public function viewListForAllGroups(User $user)
    {
        if ($user->role === config('settings.user_roles.0') || $user->role === config('settings.user_roles.2')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can view the record's detail of all groups for super data manager and super admin.
     *
     * @param User $user
     * @return bool
     */
    public function showDetailForAllGroups(User $user)
    {
        if ($user->role === config('settings.user_roles.0') || $user->role === config('settings.user_roles.2')) {
            return true;
        }
        return false;
    }

    // List and show for admin and data manager.

    /**
     * Determine whether the user can view the impacts of a certain group for super data manager and super admin.
     *
     * @param User $user
     * @return bool
     */
    public function viewListByGroup(User $user)
    {
        return $user->role === config('settings.user_roles.1') || $user->role === config('settings.user_roles.3');
    }

    /**
     * Determine whether the user can view the record's detail of a certain group
     * for super data manager and super admin.
     * @param User $user
     * @return bool
     */
    public function showDetailByGroup(User $user)
    {
        if ($user->role === config('settings.user_roles.1') || $user->role === config('settings.user_roles.3')) {
            return true;
        }
        return false;
    }

    // Super data manager.

    /**
     * Determine whether the user can create or store the record's detail for super data manager
     *
     * @param User $user
     * @return bool
     */
    public function storeAsSuperDataManager(User $user)
    {
        return $this->checkSuperDataManagerRole($user);
    }


    /**
     * Determine whether the user can edit or update the record's detail for super data manager
     *
     * @param User $user
     * @return bool
     */
    public function updateAsSuperDataManager(User $user)
    {
        return $this->checkSuperDataManagerRole($user);
    }

    /**
     * Determine whether the user can delete the record's detail for super data manager
     *
     * @param User $user
     * @return bool
     */
    public function deleteAsSuperDataManager(User $user)
    {
        return $this->checkSuperDataManagerRole($user);
    }

    /**
     * Determine whether the user can restore the record's detail for super data manager
     *
     * @param User $user
     * @return bool
     */
    public function restoreAsSuperDataManager(User $user)
    {
        return $this->checkSuperDataManagerRole($user);
    }

    // Super admin.

    /**
     * Determine whether the user can create or store the record's detail for super admin
     *
     * @param User $user
     * @return bool
     */
    public function storeAsSuperAdmin(User $user)
    {
        return $this->checkSuperAdminRole($user);
    }

    /**
     * Determine whether the user can edit or update the record's detail for super admin
     *
     * @param User $user
     * @return bool
     */
    public function updateAsSuperAdmin(User $user)
    {
        return $this->checkSuperAdminRole($user);
    }

    /**
     * Determine whether the user can delete the record's detail for super admin
     *
     * @param User $user
     * @return bool
     */
    public function deleteAsSuperAdmin(User $user)
    {
        return $this->checkSuperAdminRole($user);
    }

    // Not patroller action.

    /**
     * Determine whether the not patroller role user can interact in frontend.
     *
     * @param User $user
     * @return bool
     */
    public function interact(User $user)
    {
        return $this->checkIfNotPatroller($user);
    }

    // Check role.

    /**
     * check user role for super data manager
     * @param User $user
     * @return bool
     */
    protected function checkSuperAdminRole(User $user)
    {
        if ($user->role === config('settings.user_roles.0')) {
            return true;
        }
        return false;
    }

    /**
     * check user role for super data manager
     * @param User $user
     * @return bool
     */
    protected function checkSuperDataManagerRole(User $user)
    {
        if ($user->role === config('settings.user_roles.2')) {
            return true;
        }
        return false;
    }

    /**
     * check user role for not patroller
     *
     * @param User $user
     * @return bool
     */
    protected function checkIfNotPatroller(User $user)
    {
        if ($user->role === config('settings.user_roles.4')) {
            return false;
        }
        return true;
    }
}
