<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ComputerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ComputerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ComputerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Computer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/computer');
        CRUD::setEntityNameStrings('computer', 'computers');
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
            'name' => 'brand_id',
            'label' => 'Brand'
        ]);
        CRUD::addColumn([
            'name' => 'type',
            'label' => 'Type'
        ]);
        CRUD::addColumn([
            'name' => 'serial_number',
            'label' => 'Serial Number'
        ]);
        CRUD::addColumn([
            'name' => 'problem',
            'label' => 'Problem'
        ]);
        CRUD::addColumn([
            'name' => 'eq_bag',
            'label' => 'Bag'
        ]);
        CRUD::addColumn([
            'name' => 'eq_charger_cable',
            'label' => 'Charger Cable'
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
        CRUD::setValidation(ComputerRequest::class);

        CRUD::addField([
            'name' => 'brand_id',
            'label' => 'Brand',
            'type' => 'text' //
        ]);
        CRUD::addField([
            'name' => 'type',
            'label' => 'Type',
            'type' => 'text'
        ]);
        CRUD::addField([
            'name' => 'serial_number',
            'label' => 'Serial Number',
            'type' => 'text'
        ]);
        CRUD::addField([
            'name' => 'problem',
            'label' => 'Problem',
            'type' => 'text'
        ]);
        CRUD::addField([
            'name' => 'eq_bag',
            'label' => 'Bag',
            'type' => 'text' //
        ]);
        CRUD::addField([
            'name' => 'eq_charger_cable',
            'label' => 'Charger Cable',
            'type' => 'text' //
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
