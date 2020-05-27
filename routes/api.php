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

Route::get('config', 'MainController@config');

// cookieのミドルウェアを勝手に割当
Route::middleware([\App\Http\Middleware\EncryptCookies::class])->prefix('plan')->group(function(){
    Route::get('', 'PlanController@list');
    Route::get('/{id}', 'PlanController@show');
    Route::put('/{id}', 'PlanController@update');
    Route::post('', 'PlanController@create');
    Route::delete('/{id}', 'PlanController@destroy');
});


Route::middleware([\App\Http\Middleware\EncryptCookies::class])->prefix('calendar')->group(function(){
    Route::get('/{year}/{month}', 'CalendarController@list')->where(['year'=>'\d{4}', 'month'=>'\d{1,2}']);
    Route::post('{date}/{plan_id}', 'CalendarController@add')->where(['date'=>'\d{4}-\d{1,2}-\d{1,2}', 'plan_id'=>'\d+']);
    Route::delete('/{date}', 'CalendarController@remove')->where(['date'=>'\d{4}-\d{1,2}-\d{1,2}']);
});
