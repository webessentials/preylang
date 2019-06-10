<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('uniqueCombo', function ($attribute, $value, $parameters, $validator) {

            $query = DB::table($parameters[0])
                ->where($attribute, '=', $value)
                ->where($parameters[1], '=', request($parameters[1]))
                ->get();
            if (isset($parameters[2])) {
                $query = $query->where('id', '<>', $parameters[2]);
            }
            return ($query->count() <= 0);
        }, 'This field needs to be unique.');

        // Custom blade directive to use the correct translation fo the category name.
        Blade::directive('categoryName', function ($category) {
            return "CategoryHelper::getCategoryName($category)";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
