<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('need-checking', 'ReparationCrudController');
    Route::get('need-checking/{id}/done-inspection','ReparationCrudController@doneInspection');
    Route::crud('need-reparation', 'Reparation2CrudController');
    Route::crud('post-reparation-checking', 'Reparation3CrudController');
    Route::crud('need-pickup', 'Reparation4CrudController');
    Route::crud('computer', 'ComputerCrudController');
    Route::crud('brand', 'BrandCrudController');
    Route::crud('customer', 'CustomerCrudController');
}); // this should be the absolute last line of this file