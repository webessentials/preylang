<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RawImpact;
use Illuminate\Auth\Access\HandlesAuthorization;

class RawImpactPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param \App\Models\User $user
     * @param \App\Models\RawImpact $rawImpact
     *
     * @return bool
     */
    public function showByGroup(User $user, RawImpact $rawImpact)
    {
        if (($user->role === config('settings.user_roles.3') ||  $user->role === config('settings.user_roles.1')) && $user->user_group_id === $rawImpact->villager->user_group_id) {
            return true;
        }
        return false;
    }
}
