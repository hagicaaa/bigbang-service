<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InvoiceRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation  { store as traitStore; }
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
        CRUD::setModel(\App\Models\Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/invoice');
        CRUD::setEntityNameStrings('invoice', 'invoices');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        

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
        CRUD::setValidation(InvoiceRequest::class);

        $reparation_id = 123;
        CRUD::addField([
            'name' => 'reparation_id',
            'label' => 'Reparation ID',
            'type' => 'text',
            'value' => $reparation_id,
        ]);
        
        CRUD::addField([
            'name' => 'invoice_id',
            'label' => 'Invoice ID',
            'type' => 'text',
            'value' => "INV".strtotime("now")
        ]);

        CRUD::addField([
            'name'  => 'invoiceDetails',
            'label' => 'Item',
            'type'  => 'repeatable',
            'fields' => [
                [
                    'name'    => 'category',
                    'type'    => 'select_from_array',
                    'options' => ['sparepart' => 'Sparepart', 'service' => 'Service'],
                    'label'   => 'Category',
                    'allows_null' => false,
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ],
                [
                    'name'    => 'item',
                    'type'    => 'text',
                    'label'   => 'Name',
                    'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'qty',
                    'type'    => 'number',
                    'label'   => 'Qty',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name'    => 'price',
                    'type'    => 'number',
                    'label'   => 'Price',
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ],
            ],
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

    public function store()
    {
        $request = $this->crud->getRequest()->request;
        dd($request);
    }
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
