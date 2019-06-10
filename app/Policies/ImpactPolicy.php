<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Impact;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImpactPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /** Determine whether the buttons are able to interact for super data manager and data manager.
     * @param User $user
     * @return bool
     */
    public function interactWithButtons(User $user)
    {
        if ($user->role === config('settings.user_roles.2') || $user->role === config('settings.user_roles.3')) {
            return true;
        }
        return false;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Impact $impact
     *
     * @return bool
     */
    public function showByGroup(User $user, Impact $impact)
    {
        if (($user->role === config('settings.user_roles.3') || $user->role === config('settings.user_roles.1')) && $user->user_group_id === $impact->villager->user_group_id) {
            return true;
        }
        return false;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Impact $impact
     *
     * @return bool
     */
    public function updateByGroup(User $user, Impact $impact)
    {
        return $this->checkDataManagerRole($user, $impact);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Impact $impact
     *
     * @return bool
     */
    public function restoreByGroup(User $user, Impact $impact)
    {
        return $this->checkDataManagerRole($user, $impact);
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Impact $impact
     *
     * @return bool
     */
    public function deleteByGroup(User $user, Impact $impact)
    {
        return $this->checkDataManagerRole($user, $impact);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function exportAll(User $user)
    {
        return ($user->role === config('settings.user_roles.0') || $user->role === config('settings.user_roles.2'));
    }

    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function process(User $user)
    {
        if ($user->role === config('settings.user_roles.2') || $user->role === config('settings.user_roles.3')) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Impact $impact
     *
     * @return bool
     */
    private function checkDataManagerRole(User $user, Impact $impact)
    {
        if ($user->role === config('settings.user_roles.3') && $user->user_group_id === $impact->villager->user_group_id) {
            return true;
        }
        return false;
    }
}
