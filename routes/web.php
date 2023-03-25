<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

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

Route::redirect('/', '/admin');
Route::view('/tracking','status_checking');
Route::post('/tracking/detail', 'App\Http\Controllers\TrackingController@index');
Route::view('/booking','booking');
Route::post('/booking/book','App\Http\Controllers\BookingController@index');

Route::get('api/service', 'App\Http\Controllers\Api\ApiController@index');
Route::get('api/service/{id}', 'Api\ApiController@show');
Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace' => '\App\Http\Controllers\Admin',
], function () {
    Route::crud('user', 'UserCrudController');
    Route::crud('role', 'RoleCrudController');
    Route::crud('permission', 'PermissionCrudController');

});
