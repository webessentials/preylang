<?php

namespace App\Providers;

use App\Models\Impact;
use App\Policies\ImpactPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Impact' => 'App\Policies\ImpactPolicy',
        'App\Models\RawImpact' => 'App\Policies\RawImpactPolicy',
        'App\Models\Setting' => 'App\Policies\SettingPolicy',
        'App\Models\Category' => 'App\Policies\CategoryPolicy',
        'App\Models\User' => 'App\Policies\UserPolicy',
        'App\Models\Villager' => 'App\Policies\VillagerPolicy',
        'App\Models\UserGroup' => 'App\Policies\UserGroupPolicy'

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
