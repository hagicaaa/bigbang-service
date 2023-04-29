<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SparepartRestockRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SparepartRestockCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SparepartRestockCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\SparepartRestock::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/sparepart-restock');
        CRUD::setEntityNameStrings('sparepart restock', 'sparepart restocks');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        
        CRUD::addColumn([
            'label' => 'Sparepart',
            'name' => 'sparepart',
            'type' => 'select',
            'entity' => 'service',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => 'Qty',
            'name' => 'qty',
        ]);

        CRUD::addColumn([
            'label' => 'Updated at',
            'name' => 'updated_at',
            'type' => 'date'
        ]);

        CRUD::addColumn([
            'label' => 'Photo',
            'name' => 'invoice_dir',
            'type' => 'image',
            'prefix' => 'storage/', 
        ]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    public function setupShowOperation()
    {
        CRUD::addColumn([
            'label' => 'Sparepart',
            'name' => 'sparepart',
            'type' => 'select',
            'entity' => 'service',
            'attribute' => 'name',
        ]);

        CRUD::addColumn([
            'label' => 'Qty',
            'name' => 'qty',
        ]);

        CRUD::addColumn([
            'label' => 'Updated at',
            'name' => 'updated_at',
            'type' => 'date'
        ]);

        CRUD::addColumn([
            'label' => 'Photo',
            'name' => 'invoice_dir',
            'type' => 'image',
            'prefix' => 'storage/', 
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SparepartRestockRequest::class);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
