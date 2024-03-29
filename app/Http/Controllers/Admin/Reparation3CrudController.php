<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReparationRequest;
use App\Models\Computer;
use App\Models\Customer;
use App\Models\Reparation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Class Reparation3CrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class Reparation3CrudController extends CrudController
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
        CRUD::setModel(\App\Models\Reparation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/ongoing-reparation');
        CRUD::setEntityNameStrings('reparation', 'Ongoing Reparation');
        CRUD::addClause('where', 'repair_start', '!=', NULL);
        CRUD::addClause('where', 'repair_finish', '=', NULL);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addButtonFromView('line', 'finish-repair', 'repair_finish', 'beginning');
        CRUD::addColumn([
            'label' => 'Reparation ID',
            'name' => 'reparation_id'
        ]);
        CRUD::addColumn([
            'name'  => 'computer',
            'label' => 'Computer', // Table column heading
            'type'  => 'model_function',
            'function_name' => 'getComputer', // the method in your Model
        ]);
        CRUD::addColumn([
            'label' => 'Customer',
            'name' => 'customer_id',
            'type' => 'select',
            'entity' => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    protected function setupShowOperation()
    {
        // by default the Show operation will try to show all columns in the db table,
        // but we can easily take over, and have full control of what columns are shown,
        // by changing this config for the Show operation
        $this->crud->set('show.setFromDb', false);
        CRUD::addColumn([
            'label' => 'Reparation ID',
            'name' => 'reparation_id'
        ]);
        CRUD::addColumn([
            'label' => 'Customer',
            'name' => 'name',
            'type' => 'select',
            'entity' => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        CRUD::addColumn([
            'label' => 'Phone',
            'name' => 'phone',
            'type' => 'select',
            'entity' => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'phone', // foreign key attribute that is shown to user
            'prefix' => '+62'
        ]);
        CRUD::addColumn([
            'label' => 'Brand',
            'name' => 'brand',
            'type' => 'select',
            'entity' => 'computers',
            'attribute' => 'brand',
        ]);
        CRUD::addColumn([
            'label' => 'Type',
            'name' => 'type',
            'type' => 'select',
            'entity' => 'computers',
            'attribute' => 'type',
        ]);
        CRUD::addColumn([
            'label' => 'Serial Number',
            'name' => 'serial_number',
            'type' => 'select',
            'entity' => 'computers',
            'attribute' => 'serial_number',
        ]);
        CRUD::addColumn([
            'label' => 'Problem',
            'name' => 'problem',
            'type' => 'select',
            'entity' => 'computers',
            'attribute' => 'problem',
        ]);
        CRUD::addColumn([
            'label' => 'Equipment (Bag)',
            'name' => 'eq_bag',
            'type' => 'model_function',
            'function_name' => 'getEqBag'
        ]);
        CRUD::addColumn([
            'label' => 'Equipment (Charger Cable)',
            'name' => 'eq_charger_cable',
            'type' => 'model_function',
            'function_name' => 'getEqChargerCable'
        ]);
        CRUD::addColumn([
            'label' => 'Received by',
            'name' =>'received_by',
            'type' => 'select',
            'entity' => 'receivedBy', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
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
        CRUD::setValidation(ReparationRequest::class);

        CRUD::setFromDb(); // fields

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

    public function repairFinish($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        $customer = Customer::where('id', $reparation->customer_id)->first();
        DB::beginTransaction();
        try{
            $reparation->repair_finish = Carbon::now();
            $reparation->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "repairFinish";
            $error_data["controller"] = "ReparationCrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        $response = Http::asForm()->post('http://localhost:3000/send', [
            'number' => $customer->phone.'@c.us',
            'message' => 'Hai kak '.$customer->name.', komputer anda sudah selesai kami perbaiki. Kami akan mengecek ulang komputer anda untuk memastikan masalah sudah teratasi. Salam Bigbang!',
        ]);
        if($response->successful()){
            \Alert::add('success', 'Data updated succesfully.')->flash();
            return redirect(backpack_url('ongoing-reparation'));
        }
    }
}
