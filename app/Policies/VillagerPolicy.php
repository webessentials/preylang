<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Villager;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillagerPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Villager $villager
     * @return bool
     */
    public function showByGroup(User $user, Villager $villager)
    {
        if (($user->role === config('settings.user_roles.1') || $user->role === config('settings.user_roles.3')) && $user->user_group_id === $villager->user_group_id) {
            return true;
        }
        return false;
    }

    /** Determine whether the buttons are able to interact for super admin and admin.
     * @param User $user
     * @return bool
     */
    public function interactWithButtons(User $user)
    {
        if ($user->role === config('settings.user_roles.0') || $user->role === config('settings.user_roles.1')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether user can create or store villager or not in user group.
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
     * Determine whether user can edit or update a certain villager or not in user group.
     *
     * @param User $user
     * @param Villager $villager
     * @return bool
     */
    public function updateByGroup(User $user, Villager $villager)
    {
        return $this->checkRecordIsBelongToUserGroup($user, $villager);
    }

    /**
     * Determine whether user can delete a certain villager or not in user group.
     *
     * @param User $user
     * @param Villager $villager
     * @return bool
     */
    public function deleteByGroup(User $user, Villager $villager)
    {
        return $this->checkRecordIsBelongToUserGroup($user, $villager);
    }

    /**
     * check record is belong to User Group
     * @param User $user
     * @param Villager $villager
     * @return bool
     */
    private function checkRecordIsBelongToUserGroup(User $user, Villager $villager)
    {
        if ($user->role === config('settings.user_roles.1') && $user->user_group_id === $villager->user_group_id) {
            return true;
        }
        return false;
    }
}
