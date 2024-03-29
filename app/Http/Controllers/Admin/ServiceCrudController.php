<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ServiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \App\Http\Controllers\Admin\Operations\RestockOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Service::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service');
        CRUD::setEntityNameStrings('service/sparepart', 'services');
        CRUD::setHeading('Services & Spareparts');
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
            'name' => 'name',
            'label' => 'Name'
        ]);

        CRUD::addColumn([
            'name' => 'category',
            'label' => 'Category'
        ]);

        CRUD::addColumn([
            'name' => 'qty',
            'label' => 'Qty'
        ]);

        CRUD::addColumn([
            'name' => 'price',
            'label' => 'Price',
            'prefix' => 'Rp '
        ]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServiceRequest::class);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Name',
        ]);

        CRUD::addField([
            'name' => 'category',
            'label' => "Category",
            'type' => 'select_from_array',
            'options' => ['service' => 'Service', 'sparepart' => 'Sparepart'],
            'allows_null' => false,
            'default' => 'service',
        ]);

        CRUD::addField([
            'name' => 'part_number',
            'type' => 'text',
            'label' => 'Part Number',
        ]);

        CRUD::addField([
            'name' => 'qty',
            'type' => 'number',
            'label' => 'Qty',
            'suffix' => 'pcs'
        ]);

        CRUD::addField([
            'name' => 'price',
            'type' => 'number',
            'label' => 'Price',
            'prefix' => 'Rp',
            'attributes' => ["step" => "any"]
        ]);
        

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
