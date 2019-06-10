<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy extends BasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the categories list for super data manager.
     *
     * @param User $user
     * @return bool
     */
    public function viewList(User $user)
    {
        return parent::checkSuperDataManagerRole($user);
    }

    /**
     * Determine whether user can show category detail for super data manager.
     *
     * @param User $user
     * @return bool
     */
    public function showDetail(User $user)
    {
        return parent::checkSuperDataManagerRole($user);
    }
}
