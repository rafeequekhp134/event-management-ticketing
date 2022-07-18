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

Route::get('/', function () {
    return view('welcome');
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
    
});

Route::group([

    'middleware' => 'api'

], function ($router) {

    Route::get('events', 'EventController@index');
    Route::post('events', 'EventController@save');
    Route::delete('events/{id}', 'EventController@delete');
    
    Route::get('bookings', 'BookingController@index');
    Route::post('bookings', 'BookingController@save');
    Route::delete('bookings/{id}', 'BookingController@delete');
    Route::get('bookings/{event_id}', 'BookingController@getByEvent');
    
    Route::get('capacities/{event_id}', 'CapacityController@getByEvent');
    
    Route::get('reports', 'ReportController@index');
});

Route::post('register', 'AuthController@register');

Route::get('/', 'IndexController@index')->name('index');
