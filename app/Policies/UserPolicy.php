<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the users list for super admin.
     *
     * @param User $user
     * @return bool
     */
    public function viewListSuperAdmin(User $user)
    {
        return parent::checkSuperAdminRole($user);
    }

    /** Determine whether the frontend are able to interact for super admin and admin.
     * @param User $user
     * @return bool
     */
    public function interact(User $user)
    {
        if ($user->role === config('settings.user_roles.0') || $user->role === config('settings.user_roles.1')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function viewListAdmin(User $user)
    {
        if ($user->role === config('settings.user_roles.1')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether user can create or store user or not in user group.
     *
     * @param User $user
     * @return bool
     */
    public function storeByGroup(User $user)
    {
        if ($user->role === config('settings.user_roles.1')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether user can edit or update a certain user or not in user group.
     *
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function updateByGroup(User $currentUser, User $user)
    {
        return $this->checkRecordIsBelongToUserGroup($currentUser, $user);
    }

    /**
     * Determine whether user can delete a certain user or not in user group.
     *
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    public function deleteByGroup(User $currentUser, User $user)
    {
        return $this->checkRecordIsBelongToUserGroup($currentUser, $user);
    }

    /**
     * check record is belong to User Group
     * @param User $currentUser
     * @param User $user
     * @return bool
     */
    private function checkRecordIsBelongToUserGroup(User $currentUser, User $user)
    {
        if ($currentUser->role === config('settings.user_roles.1') && $currentUser->user_group_id === $user->user_group_id) {
            if ($user->role !== config('settings.user_roles.0') && $user->role !== config('settings.user_roles.2')) {
                return true;
            }
        }
        return false;
    }
}
