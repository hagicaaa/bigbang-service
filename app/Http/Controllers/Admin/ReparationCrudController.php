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
 * Class ReparationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ReparationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/need-checking');
        CRUD::setEntityNameStrings('reparation', 'Need Checking');
        CRUD::addClause('where', 'inspection_date', '=', NULL);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addButtonFromView('line', 'done-inspection', 'done_inspection', 'beginning');
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

        $this->crud->removeSaveAction('save_and_preview');
        $this->crud->removeSaveAction('save_and_new');
        $this->crud->removeSaveAction('save_and_edit');
        CRUD::addField([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Customer Name',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ]
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
            'name' => 'eq_charger_cable',
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
        
        DB::beginTransaction();
        try{
            $customer = new Customer();
            $customer->name = $request->get('name');
            $customer->phone = $request->get('phone');
            $customer->save();
    
            $computer = new Computer();
            $computer->brand = $request->get('brand');
            $computer->type = $request->get('type');
            $computer->serial_number = $request->get('serial_number');
            $computer->problem = $request->get('problem');
            $computer->eq_bag = $request->get('eq_bag');
            $computer->eq_charger_cable = $request->get('eq_charger_cable');
            $computer->save();
    
            $reparation = new Reparation();
            $reparation->reparation_id = strtotime("now");
            $reparation->computer_id = $computer->id;
            $reparation->customer_id = $customer->id;
            $reparation->received_by = backpack_user()->id;
            $reparation->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "store";
            $error_data["controller"] = "ReparationCrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        $response = Http::asForm()->post('http://localhost:3000/send', [
            'number' => $customer->phone.'@c.us',
            'message' => 'Hai kak '.$customer->name.', komputer anda berhasil terinput kedalam database kami. Kode reparasi anda adalah *'.$reparation->reparation_id.'*. Teknisi kami akan segera melakukan pengecekan pada komputer anda. Terimakasih, Salam Bigbang!',
        ]);
        if($response->successful()){
            \Alert::add('success', 'Data added succesfully.')->flash();
            return redirect(backpack_url('need-checking'));
        }

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

    public function doneInspection($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        $customer = Customer::where('id', $reparation->customer_id)->first();

        DB::beginTransaction();
        try{
            $reparation->inspection_date = Carbon::now();
            $reparation->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "doneInspection";
            $error_data["controller"] = "ReparationCrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        $response = Http::asForm()->post('http://localhost:3000/send', [
            'number' => $customer->phone.'@c.us',
            'message' => 'Hai kak '.$customer->name.', teknisi kami sudah selesai melakukan pengecekan pada komputer anda. Teknisi kami akan segera menghubungi anda secepatnya untuk detail kerusakan dan konfirmasi perbaikan. Salam Bigbang!',
        ]);
        if($response->successful()){
            \Alert::add('success', 'Data updated succesfully.')->flash();
            return redirect(backpack_url('need-checking'));
        }
    }
}
