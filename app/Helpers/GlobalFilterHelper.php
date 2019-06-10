<?php
namespace App\Helpers;

class GlobalFilterHelper
{

    /**
     * @var array
     */
    protected static $notShowableRouteNames = [
        'villager.create', 'villager.edit', 'villager.show', 'impact.edit',
        'impact.show', 'rawImpact.show', 'user.create', 'user.edit',
        'userGroups.create', 'userGroups.edit', 'category.show', 'province.create',
        'province.edit', 'proof.create', 'proof.edit', 'reason.create',
        'reason.edit', 'offender.create', 'offender.edit', 'victimType.create',
        'victimType.edit', 'designation.create', 'designation.edit', 'threatening.create',
        'threatening.edit'
    ];

    /**
     * @param string $routeName
     * @return bool
     */
    public static function showFilter($routeName)
    {
        if (in_array($routeName, self::$notShowableRouteNames)) {
            return false;
        }
        return true;
    }
}
