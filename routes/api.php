<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', 'API\AuthController@login');

Route::group([
    'middleware' => ['auth.villager']
], function () {
    Route::get('/mock', function (Request $request) {
        return \App\Helpers\ResponseHelper::makeResponse('Success', ['message' => 'Valid Access Token']);
    });

    Route::post('/impact', 'API\ImpactAPIController@createRecords');
});

Route::prefix('public')->group(function () {
    Route::get('map/impacts', 'PublicMapController@getImpactsForPublicMap')->name('public.map.impacts');
});
