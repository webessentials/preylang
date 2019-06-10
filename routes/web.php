<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/public/map', 'PublicMapController@index')->name('public.map');

Auth::routes();

Route::group(['middleware' => ['auth', 'web', 'locale', 'shareDataWithViews']], function () {
    Route::get('/setGlobalUserGroup', 'SetGlobalUserGroupController@index')->name('setGlobalUserGroup');
    Route::get('/', 'DashboardController@index')->name('dashboard');
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::prefix('user')->group(function () {
        Route::get('/userSetting', 'UserController@userSetting')->name('user.userSetting');
        Route::put('/updateUserSetting', 'UserController@updateUserSetting')->name('user.updateUserSetting');
    });

    Route::group(['middleware' => ['check.role']], function () {
        Route::prefix('user')->group(function () {
            Route::get('/', 'UserController@index')->name('user.index');
            Route::get('/create', 'UserController@create')->name('user.create');
            Route::post('/store', 'UserController@store')->name('user.store');
            Route::get('/edit/{userId}', 'UserController@edit')->name('user.edit');
            Route::put('/update/{userId}', 'UserController@update')->name('user.update');
            Route::delete('/delete/{userId}', 'UserController@delete')->name('user.delete');
        });

        Route::prefix('setting')->group(function () {
            Route::prefix('usergroups')->group(function () {
                Route::get('/', 'Settings\UserGroupController@index')->name('userGroups.index');
                Route::get('/create', 'Settings\UserGroupController@create')->name('userGroups.create');
                Route::post('/store', 'Settings\UserGroupController@store')->name('userGroups.store');
                Route::get('/edit/{id}', 'Settings\UserGroupController@edit')->name('userGroups.edit');
                Route::put('/update/{id}', 'Settings\UserGroupController@update')
                    ->name('userGroups.update');
                Route::delete('/delete/{id}', 'Settings\UserGroupController@delete')
                    ->name('userGroups.delete');
            });

            Route::prefix('category')->group(function () {
                Route::get('/', 'Settings\CategoryController@index')->name('category.index');
                Route::get('/show/{id}', 'Settings\CategoryController@show')->name('category.show');
            });

            Route::prefix('province')->group(function () {
                Route::get('/', 'Settings\ProvinceController@index')->name('province.index');
                Route::get('/create', 'Settings\ProvinceController@create')->name('province.create');
                Route::post('/store', 'Settings\ProvinceController@store')->name('province.store');
                Route::get('/edit/{id}', 'Settings\ProvinceController@edit')->name('province.edit');
                Route::put('/update/{id}', 'Settings\ProvinceController@update')->name('province.update');
                Route::delete('/delete/{id}', 'Settings\ProvinceController@delete')->name('province.delete');
            });

            Route::prefix('proof')->group(function () {
                Route::get('/', 'Settings\ProofController@index')->name('proof.index');
                Route::get('/create', 'Settings\ProofController@create')->name('proof.create');
                Route::post('/store', 'Settings\ProofController@store')->name('proof.store');
                Route::get('/edit/{id}', 'Settings\ProofController@edit')->name('proof.edit');
                Route::put('/update/{id}', 'Settings\ProofController@update')->name('proof.update');
                Route::delete('/delete/{id}', 'Settings\ProofController@delete')->name('proof.delete');
            });

            Route::prefix('reason')->group(function () {
                Route::get('/', 'Settings\ReasonController@index')->name('reason.index');
                Route::get('/create', 'Settings\ReasonController@create')->name('reason.create');
                Route::post('/store', 'Settings\ReasonController@store')->name('reason.store');
                Route::get('/edit/{id}', 'Settings\ReasonController@edit')->name('reason.edit');
                Route::put('/update/{id}', 'Settings\ReasonController@update')->name('reason.update');
                Route::delete('/delete/{id}', 'Settings\ReasonController@delete')->name('reason.delete');
            });

            Route::prefix('offender')->group(function () {
                Route::get('/', 'Settings\OffenderController@index')->name('offender.index');
                Route::get('/create', 'Settings\OffenderController@create')->name('offender.create');
                Route::post('/store', 'Settings\OffenderController@store')->name('offender.store');
                Route::get('/edit/{id}', 'Settings\OffenderController@edit')->name('offender.edit');
                Route::put('/update/{id}', 'Settings\OffenderController@update')->name('offender.update');
                Route::delete('/delete/{id}', 'Settings\OffenderController@delete')->name('offender.delete');
            });

            Route::prefix('victim_type')->group(function () {
                Route::get('/', 'Settings\VictimTypeController@index')->name('victimType.index');
                Route::get('/create', 'Settings\VictimTypeController@create')->name('victimType.create');
                Route::post('/store', 'Settings\VictimTypeController@store')->name('victimType.store');
                Route::get('/edit/{id}', 'Settings\VictimTypeController@edit')->name('victimType.edit');
                Route::put('/update/{id}', 'Settings\VictimTypeController@update')->name('victimType.update');
                Route::delete('/delete/{id}', 'Settings\VictimTypeController@delete')->name('victimType.delete');
            });

            Route::prefix('designation')->group(function () {
                Route::get('/', 'Settings\DesignationController@index')->name('designation.index');
                Route::get('/create', 'Settings\DesignationController@create')->name('designation.create');
                Route::post('/store', 'Settings\DesignationController@store')->name('designation.store');
                Route::get('/edit/{id}', 'Settings\DesignationController@edit')->name('designation.edit');
                Route::put('/update/{id}', 'Settings\DesignationController@update')->name('designation.update');
                Route::delete('/delete/{id}', 'Settings\DesignationController@delete')
                    ->name('designation.delete');
            });

            Route::prefix('threatening')->group(function () {
                Route::get('/', 'Settings\ThreateningController@index')->name('threatening.index');
                Route::get('/create', 'Settings\ThreateningController@create')->name('threatening.create');
                Route::post('/store', 'Settings\ThreateningController@store')->name('threatening.store');
                Route::get('/edit/{id}', 'Settings\ThreateningController@edit')->name('threatening.edit');
                Route::put('/update/{id}', 'Settings\ThreateningController@update')->name('threatening.update');
                Route::delete('/delete/{id}', 'Settings\ThreateningController@delete')
                    ->name('threatening.delete');
            });
        });

        Route::prefix('impact')->group(function () {
            Route::get('/', 'ImpactController@index')->name('impact.index');
            Route::get('/edit/{impact}', 'ImpactController@edit')->name('impact.edit');
            Route::get('/show/{impact}', 'ImpactController@show')->name('impact.show');
            Route::put('/update/{id}', 'ImpactController@update')->name('impact.update');
            Route::delete('/delete/{id}', 'ImpactController@delete')->name('impact.delete');
            Route::put('/restore/{id}', 'ImpactController@restore')->name('impact.restore');
            Route::get('/process', 'ImpactController@process')->name('impact.process');
            Route::get('/filter', 'ImpactController@filter')->name('impact.filter');
            Route::get('/subcategories/{level}/{category}', 'ImpactController@getSubCategories')
                ->name('impact.subcategories');
            Route::post('/export', 'ImpactController@export')->name('impact.export');
            Route::get('/filterByCategory/{id}', 'ImpactController@filterByCategory')->name('impact.filterByCategory');
            Route::post('/filterByCategory/{id}', 'ImpactController@filterByCategory')->name('impact.filterByCategory');
        });

        Route::prefix('rawimpact')->group(function () {
            Route::get('/', 'RawImpactController@index')->name('rawImpact.index');
            Route::get('/show/{rawImpact}', 'RawImpactController@show')->name('rawImpact.show');
            Route::get('/filterByCategory/{id}', 'RawImpactController@filterByCategory')
                ->name('rawImpact.filterByCategory');
            Route::post('/filterByCategory/{id}', 'RawImpactController@filterByCategory')
                ->name('rawImpact.filterByCategory');
        });

        Route::get('map', 'MapController@index')->name('map');
        Route::get('map/impacts', 'MapController@getImpactsWithLocation')->name('map.impacts');

        Route::prefix('activity')->group(function () {
            Route::get('/', 'EditHistoryController@index')->name('activity.index');
        });

        Route::prefix('villager')->group(function () {
            Route::get('/', 'VillagerController@index')->name('villager.index');
            Route::get('/create', 'VillagerController@create')->name('villager.create');
            Route::post('/store', 'VillagerController@store')->name('villager.store');
            Route::get('/show/{villagerId}', 'VillagerController@show')->name('villager.show');
            Route::get('/edit/{villagerId}', 'VillagerController@edit')->name('villager.edit');
            Route::put('/update/{villagerId}', 'VillagerController@update')->name('villager.update');
            Route::delete('/delete/{villagerId}', 'VillagerController@delete')->name('villager.delete');
        });

        Route::get('/generatedataforgraph', 'DashboardController@generateDataForGraph')->name('dashboard.generateDataForGraph');
    });

    Route::get('files/{filePath}', 'FileController@getFile')->where('filePath', '.*')->name('files.get');
    Route::get('download/{filePath}', 'FileController@downloadFile')->where('filePath', '.*')->name('files.download');
    Route::get('redirect/{filePath}', 'FileController@redirectToDownload')->where('filePath', '.*')->name('files.redirectToDownload');
});
