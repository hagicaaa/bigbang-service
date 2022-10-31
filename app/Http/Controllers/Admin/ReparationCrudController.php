<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReparationRequest;
use App\Models\Computer;
use App\Models\Customer;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class ReparationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReparationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
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
        CRUD::setModel(\App\Models\Reparation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reparation');
        CRUD::setEntityNameStrings('reparation', 'reparations');
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
            'label' => 'Invoice ID',
            'name' => 'inv_id'
        ]);
        CRUD::addColumn([
            'label' => 'Computer',
            'name' => 'computer_id',
            'type' => 'select',
            'entity' => 'computers',
            'attribute' => 'type',
        ]);
        CRUD::addColumn([
            'label' => 'Customer',
            'name' => 'customer_id',
            'type' => 'select',
            'entity' => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        CRUD::addColumn([
            'label' => 'Received by',
            'name' =>'received_by',
            'type' => 'select',
            'entity' => 'receivedBy', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
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
        CRUD::setValidation(ReparationRequest::class);

        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Customer Name',
        ]);

        CRUD::addField([
            'name' => 'phone',
            'type' => 'text',
            'label' => 'Phone',
            'prefix' => '+62',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ]
        ]);

        CRUD::addField([
            'name' => 'email',
            'type' => 'text',
            'label' => 'Email',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ]
        ]);

        CRUD::addField([
            'name' => 'brand', 
            'type' => 'text',
            'label' => 'Brand',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ]
        ]);

        CRUD::addField([
            'name' => 'type',
            'type' => 'text',
            'label' => 'Type',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ]
        ]);

        CRUD::addField([
            'name' => 'serial_number',
            'type' => 'text',
            'label' => 'Serial Number',
        ]);

        CRUD::addField([
            'name' => 'problem',
            'type' => 'text',
            'label' => 'Problem',
        ]);

        CRUD::addField([
            'name' => 'eq_bag',
            'type' => 'checkbox',
            'label' => 'Bag',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ]
        ]);

        CRUD::addField([
            'name' => 'eq_charger',
            'type' => 'checkbox',
            'label' => 'Charger',
            'wrapper' => [
                'class' => 'form-group col-md-3',
            ]
        ]);

        

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }
    
    public function store()
    {
        $response = $this->traitStore();
        $validator = Validator::make($this->crud->getRequest()->request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'brand' => 'required',
            'type' => 'required',
            'serial_number' => 'required',
            'problem' => 'required',
        ]);
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        
        
        $request = $this->crud->getRequest()->request;

        $customer = new Customer();
        $customer->name = $request->getRequest('name');
        $customer->phone = $request->getRequest('phone');
        $customer->email = $request->getRequest('email');
        $customer->save();

        $computer = new Computer();
        $computer->brand = $request->getRequest('brand');
        $computer->type = $request->getRequest('type');
        $computer->serial_number = $request->getRequest('serial_number');
        $computer->problem = $request->getRequest('problem');
        $computer->eq_bag = $request->getRequest('eq_bag');
        $computer->eq_charger_cable = $request->getRequest('eq_charger_cable');
        $computer->save();

        $reparation = new Reparation();
        $reparation->inv_id = "11";
        $reparation->computer_id = 1;
        $reparation->customer_id = 1;
        $reparation->save();

        //     catch(\Exception $e){
        //     DB::rollBack();

        //     //set error data for error log
        //     $error_data = [];
        //     $error_data["function"] = "store";
        //     $error_data["controller"] = "ReparationCrudController";
        //     $error_data["message"] = $e->getMessage();

        //     Log::error("Create failed", $error_data);

        //     \Alert::error("Create failed")->flash();
        // }
        return $response;

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
