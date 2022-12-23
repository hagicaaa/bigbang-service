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

/**
 * Class Reparation2CrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class Reparation2CrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/need-reparation');
        CRUD::setEntityNameStrings('reparation', 'Need Reparation');
        CRUD::addClause('where', 'inspection_date', '!=', NULL);
        CRUD::addClause('where', 'repair_start', '=', NULL);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addButtonFromView('line', 'start-repair', 'repair_start', 'beginning');
        CRUD::addColumn([
            'label' => 'Reparation ID',
            'name' => 'reparation_id'
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

    public function startRepair($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        DB::beginTransaction();
        try{
            $reparation->repair_start = Carbon::now();
            $reparation->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "startRepair";
            $error_data["controller"] = "ReparationCrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        \Alert::add('success', 'Data updated succesfully.')->flash();
        return redirect(backpack_url('need-reparation'));
    }
}
