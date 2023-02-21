<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ReparationRequest;
use App\Models\Computer;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Reparation;
use App\Models\Service;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Prologue\Alerts\Facades\Alert;
use Datatables;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Class Reparation4CrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class Reparation4CrudController extends CrudController
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/post-reparation-checking');
        CRUD::setEntityNameStrings('reparation', 'QC Inspection');
        CRUD::addClause('where', 'repair_finish', '!=', NULL);
        CRUD::addClause('where', 'post_repair_inspection_date', '=', NULL);
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addButtonFromView('line', 'create_invoice', 'create_invoice', 'beginning');
        CRUD::addButtonFromView('line', 'finish-checking', 'done_checking', 'beginning');
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
            'name' => 'received_by',
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

    public function createInvoice($id)
    {
        $reparation_data = Reparation::where('id', $id)->first();
        $invoice_data = Invoice::where('reparation_id', $id)->first();
        if ($invoice_data == NULL) {
            $invoice = Invoice::create([
                'invoice_id' => 'INV-' . $reparation_data->reparation_id,
                'reparation_id' => $reparation_data->id,
            ]);
            \Alert::add('success', 'Invoice created succesfully!')->flash();
            return redirect(backpack_url('post-reparation-checking'));
        } else {
            \Alert::error("Data already exist!")->flash();
            return redirect(backpack_url('post-reparation-checking'));
        }
    }

    public function addItemtoInvoice(Request $request)
    {
        $data = $request->all();

        $service_price = Service::where('id', $data['item'])->value('price');
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
            $error_data["controller"] = "Reparation4CrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            return response()->json($error_data);
        }

        return response()->json(['success' => 'Item successfully added!']);
    }

    public function delItem($id, $item_id)
    {
        $invoice_item = InvoiceDetail::where('id', $item_id)->delete();
        $invoice = Invoice::where('id', $id)->first();

        $total_all = InvoiceDetail::where('invoice_id', $invoice->id)->sum('price');

        $invoice->total = $total_all;
        $invoice->save();
        \Alert::add('success', 'Data deleted succesfully!')->flash();
            return redirect(backpack_url('post-reparation-checking/'.$id.'/invoice'));
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
        return $pdf->stream($data['invoice']->invoice_id.'.pdf');
        // return view('crud::print', $data);
    }

    // protected function setupInvoiceOperation()
    // {
    //     $reparation_data = Reparation::where('id',\Route::current()->parameter('id'))->first();
    //     $customer_data = Customer::where('id',$reparation_data->customer_id)->first();
    //     $computer_data = Computer::where('id',$reparation_data->computer_id)->first();

    //     CRUD::addField([
    //         'name' => 'invoice_id',
    //         'label' => 'Invoice ID',
    //         'type' => 'text',
    //         'value' => "INV-".$reparation_data->reparation_id,
    //         'wrapper' => ['class' => 'form-group col-md-4'],
    //         'attributes' => [
    //             'readonly'  => 'readonly',
    //         ],
    //         // 'value' => url('admin/api/service')
    //     ]);

    //     CRUD::addField([
    //         'name' => 'customer_name',
    //         'label' => 'Customer',
    //         'type' => 'text',
    //         'value' => $customer_data->name,
    //         'wrapper' => ['class' => 'form-group col-md-4'],
    //         'attributes' => [
    //             'readonly'  => 'readonly',
    //         ],
    //     ]);

    //     CRUD::addField([
    //         'name' => 'phone_number',
    //         'label' => 'Phone',
    //         'type' => 'text',
    //         'prefix' => "+62",
    //         'value' => $customer_data->phone,
    //         'wrapper' => ['class' => 'form-group col-md-4'],
    //         'attributes' => [
    //             'readonly'  => 'readonly',
    //         ],
    //     ]);

    //     CRUD::addField([
    //         'name' => 'laptop_brand',
    //         'label' => 'Brand',
    //         'type' => 'text',
    //         'value' => $computer_data->brand,
    //         'wrapper' => ['class' => 'form-group col-md-6'],
    //         'attributes' => [
    //             'readonly'  => 'readonly',
    //         ],
    //     ]);

    //     CRUD::addField([
    //         'name' => 'laptop_type',
    //         'label' => 'Type',
    //         'type' => 'text',
    //         'value' => $computer_data->type,
    //         'wrapper' => ['class' => 'form-group col-md-6'],
    //         'attributes' => [
    //             'readonly'  => 'readonly',
    //         ],
    //     ]);

    //     CRUD::addField([
    //         'name' => 'laptop_problem',
    //         'label' => 'Problem',
    //         'type' => 'text',
    //         'value' => $computer_data->problem,
    //         'wrapper' => ['class' => 'form-group col-md-12'],
    //         'attributes' => [
    //             'readonly'  => 'readonly',
    //         ],
    //     ]);

    //     CRUD::addField([
    //         'name'  => 'invoiceDetails',
    //         'label' => 'Item(s)',
    //         'type'  => 'repeatable',
    //         'fields' => [
    //             // [
    //             //     'name'    => 'category',
    //             //     'type'    => 'select_from_array',
    //             //     'options' => ['sparepart' => 'Sparepart', 'service' => 'Service'],
    //             //     'label'   => 'Category',
    //             //     'allows_null' => false,
    //             //     'wrapper' => ['class' => 'form-group col-md-3'],
    //             // ],
    //             [
    //                 'name'    => 'item',
    //                 'type'    => 'select2_from_ajax',
    //                 'label'   => 'Item',
    //                 'include_all_form_fields' => true,
    //                 'model' => "App\Models\Service",
    //                 'placeholder' => "Select item",
    //                 'data_source' => url('api/service'),
    //                 'minimum_input_length' => 1,
    //                 'attribute' => 'name',
    //                 'wrapper' => ['class' => 'form-group col-md-7'],
    //             ],
    //             [
    //                 'name'    => 'qty',
    //                 'type'    => 'number',
    //                 'label'   => 'Qty',
    //                 'default' => 1,
    //                 'wrapper' => ['class' => 'form-group col-md-2'],
    //             ],
    //             [
    //                 'name'    => 'price',
    //                 'type'    => 'number',
    //                 'label'   => 'Price',
    //                 'attributes' => [
    //                     'readonly'  => 'readonly',
    //                 ],
    //                 'wrapper' => ['class' => 'form-group col-md-3 price'],
    //             ],
    //         ],
    //     ]);
    //     CRUD::addSaveAction([
    //         'name' => 'create_invoice',
    //         'redirect' => function ($crud, $request, $itemId) {
    //             return $crud->route;
    //         },
    //         'button_text' => 'Create Invoice',
    //     ]);
    // }

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

    public function finishChecking($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        $customer = Customer::where('id', $reparation->customer_id)->first();
        $invoice = Invoice::where('reparation_id', $id);
        if ($invoice) {
            DB::beginTransaction();
            try {
                $reparation->post_repair_inspection_date = Carbon::now();
                $reparation->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                //set error data for error log
                $error_data = [];
                $error_data["function"] = "finishChecking";
                $error_data["controller"] = "ReparationCrudController";
                $error_data["message"] = $e->getMessage();

                Log::error("Create failed", $error_data);
                \Alert::error("Create failed")->flash();
            }
            $response = Http::asForm()->post('http://localhost:3000/send', [
                'phone' => '62' . $customer->phone . '@c.us',
                'message' => 'Hai kak ' . $customer->name . ', komputer anda sudah selesai kami cek dan sudah berfungsi normal. Silakan datang ke toko kami untuk melanjutkan pembayaran dan mengambil komputer anda. Terimakasih sudah mempercayakan perbaikan komputer kepada kami. Salam Bigbang!',
            ]);
            if ($response->successful()) {
                \Alert::add('success', 'Data updated succesfully.')->flash();
                return redirect(backpack_url('post-reparation-checking'));
            }
        } else {
            \Alert::error("Please create invoice data first")->flash();
            return redirect(backpack_url('post-reparation-checking'));
        }
    }
}
