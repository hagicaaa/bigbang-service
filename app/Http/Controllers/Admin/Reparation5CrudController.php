<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReparationRequest;
use App\Models\Computer;
use App\Models\Customer;
use App\Models\Reparation;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Service;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Class Reparation5CrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class Reparation5CrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \App\Http\Controllers\Admin\Operations\InvoiceOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Reparation::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reparation-done');
        CRUD::setEntityNameStrings('reparation', 'Reparation Done');
        CRUD::addClause('where', 'post_repair_inspection_date', '!=', NULL);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::addButtonFromView('line', 'update-payment', 'update_payment', 'beginning');
        // CRUD::addButtonFromView('line', 'create_invoice', 'create_invoice', 'beginning');
        CRUD::addButtonFromModelFunction('line', 'update_payment', 'updatePaymentButton', 'beginning');
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

    public function createInvoice($id)
    {
        $reparation_data = Reparation::where('id', $id)->first();
        $invoice_data = Invoice::where('reparation_id', $id)->first();
        if ($invoice_data == NULL) {
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . $reparation_data->reparation_id,
                'reparation_id' => $reparation_data->id,
            ]);
            \Alert::add('success', 'Invoice created succesfully!')->flash();
            return redirect(backpack_url('reparation-done'));
        } else {
            \Alert::error("Data already exist!")->flash();
            return redirect(backpack_url('reparation-done'));
        }
    }

    public function addItemtoInvoice(Request $request)
    {
        $data = $request->all();

        $service_data = Service::where('id', $data['item'])->first();
        $service_price = $service_data->price;
        $total = $service_price * $data['qty'];
        $invoice = Invoice::where('reparation_id', $data['id'])->first();
        DB::beginTransaction();
        try {
            //add data to invoicedetail
            $invoice_detail = new InvoiceDetail;
            $invoice_detail->invoice_id = $invoice->id;
            $invoice_detail->service_id = $data['item'];
            $invoice_detail->item_qty = $data['qty'];
            $invoice_detail->price = $total;
            $invoice_detail->save();

            //decrement in master data
            if($service_data->category == "sparepart"){
                if($service_data->qty < $data['qty']){
                    return response()->json(['error' => 'Item cannot exceed qty!']);
                }
                else{
                    $service_data->qty = $service_data->qty - $data['qty'];
                    $service_data->save();
                }
            }

            //count total
            $total_all = InvoiceDetail::where('invoice_id', $invoice->id)->sum('price');

            $invoice->total = $total_all;
            $invoice->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "addItemtoInvoice";
            $error_data["controller"] = "Reparation5CrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            return response()->json($error_data);
        }

        return response()->json(['success' => 'Item successfully added!']);
    }

    public function delItem($id, $item_id)
    {
        // $invoice_item = InvoiceDetail::where('id', $item_id)->delete();
        $invoice = Invoice::where('id', $id)->first();
        // $service_data = Service::where('id', $data['item'])->first();
        $invoice_item = InvoiceDetail::where('id', $item_id)->first();
        $service_data = Service::where('id', $invoice_item->service_id)->first();
        
        if($service_data->category == "sparepart"){
            $service_data->qty = $service_data->qty + $invoice_item->item_qty;
            $service_data->save();
        }
        $invoice_item->delete();

        $total_all = InvoiceDetail::where('invoice_id', $invoice->id)->sum('price');

        $invoice->total = $total_all;
        $invoice->save();
        \Alert::add('success', 'Data deleted succesfully!')->flash();
            return redirect(backpack_url('reparation-done/'.$id.'/invoice'));
    }

    public function generateInvoice($id)
    {
        $data = [];
        $data['reparation'] = Reparation::where('id',$id)->first();
        $data['customer_data'] = Customer::where('id', $data['reparation']->customer_id)->first();
        $data['computer_data'] = Computer::where('id', $data['reparation']->computer_id)->first();
        $data['invoice'] = Invoice::where('reparation_id', $id)->first();
        $data['invoice_details'] = InvoiceDetail::select('invoice_details.id', 'invoice_id', 'service_id', 'services.name as sname', 'services.price as sprice' , 'item_qty', 'invoice_details.price as subtotal')
            ->where('invoice_id', $data['invoice']->id)
            ->leftJoin('services', 'services.id', '=', 'service_id')
            ->get();
        $pdf = Pdf::loadView('crud::print', $data)->setPaper('a4', 'landscape');
        return $pdf->stream($data['invoice']->invoice_number.'.pdf');
        // return view('crud::print', $data);
    }

    public function updatePayment($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        $invoice = Invoice::where('reparation_id', $id)->first();
        if($invoice){
            \Alert::error("You have to create invoice data first!")->flash();
            return redirect(backpack_url('reparation-done'));
        }
        $customer = Customer::where('id', $reparation->customer_id)->first();
        DB::beginTransaction();
        try{
            $invoice->payment_status = 1;
            // $reparation-> = Carbon::now();
            $invoice->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "updatePayment";
            $error_data["controller"] = "Reparation5CrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        \Alert::add('success', 'Payment updated succesfully.')->flash();
        return redirect(backpack_url('reparation-done'));
    }

    public function updatePickup($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        $invoice = Invoice::where('reparation_id', $id)->first();
        $customer = Customer::where('id', $reparation->customer_id)->first();
        DB::beginTransaction();
        try{
            $invoice->pickup_status = 1;
            // $reparation-> = Carbon::now();
            $invoice->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "updatePayment";
            $error_data["controller"] = "Reparation5CrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        \Alert::add('success', 'Payment updated succesfully.')->flash();
        return redirect(backpack_url('reparation-done'));
    }
}
