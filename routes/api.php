<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// cookieのミドルウェアを勝手に割当
Route::middleware([\App\Http\Middleware\EncryptCookies::class])->prefix('plan')->group(function(){
    Route::get('', 'PlanController@list');
    Route::get('/{id}', 'PlanController@show');
    Route::put('/{id}', 'PlanController@update');
    Route::post('', 'PlanController@create');
    Route::delete('/{id}', 'PlanController@destroy');
});


Route::middleware([\App\Http\Middleware\EncryptCookies::class])->prefix('calendar')->group(function(){
    Route::get('/{date}', 'CalendarController@list');
    Route::post('{date}/{plan_id}', 'CalendarController@add');
    Route::delete('/{date}', 'CalendarController@remove');
});
