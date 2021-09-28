<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'MainController@index');

Route::get('demo', 'DemoController@index');


Route::get('register', 'LoginController@register');
Route::get('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout');
Route::get('withdraw', 'LoginController@withdraw');
