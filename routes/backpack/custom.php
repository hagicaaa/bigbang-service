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
    Route::crud('reparations', 'ReparationListCrudController');
    
    Route::crud('need-checking', 'ReparationCrudController');
    Route::get('need-checking/{id}/done-inspection','ReparationCrudController@doneInspection');

    Route::crud('need-reparation', 'Reparation2CrudController');
    Route::get('need-reparation/{id}/start-repair', 'Reparation2CrudController@startRepair');
    Route::get('need-reparation/{id}/cancel-repair', 'Reparation2CrudController@cancelRepair');

    Route::crud('ongoing-reparation', 'Reparation3CrudController');
    Route::get('ongoing-reparation/{id}/finish-repair', 'Reparation3CrudController@repairFinish');

    Route::crud('qc-inspection', 'Reparation4CrudController');
    Route::get('qc-inspection/{id}/finish-checking', 'Reparation4CrudController@finishChecking');


    Route::crud('reparation-done', 'Reparation5CrudController');
    Route::get('reparation-done/{id}/invoice/create', 'Reparation5CrudController@createInvoice');
    Route::post('reparation-done/{id}/invoice/add-item', 'Reparation5CrudController@addItemtoInvoice');
    Route::get('reparation-done/{id}/invoice/del-item/{item_id}', 'Reparation5CrudController@delItem')->name('del-item');
    Route::get('reparation-done/{id}/generate-invoice', 'Reparation5CrudController@generateInvoice');
    Route::get('reparation-done/{id}/update-payment', 'Reparation5CrudController@updatePayment');
    Route::get('reparation-done/{id}/update-pickup', 'Reparation5CrudController@updatePickup');
    Route::get('reparation-done/{id}/print-invoice', 'Reparation5CrudController@printInvoice');
    




    
    Route::crud('computer', 'ComputerCrudController');
    Route::crud('brand', 'BrandCrudController');
    Route::crud('customer', 'CustomerCrudController');
    Route::crud('invoice', 'InvoiceCrudController');
    Route::crud('service', 'ServiceCrudController');
    Route::crud('sparepart', 'SparepartCrudController');
    Route::crud('booking', 'BookingCrudController');
    Route::get('booking/{id}/confirm','BookingCrudController@confirm');
    Route::get('booking/{id}/delete','BookingCrudController@deleteItem');
    Route::crud('sparepart-restock', 'SparepartRestockCrudController');
}); // this should be the absolute last line of this file