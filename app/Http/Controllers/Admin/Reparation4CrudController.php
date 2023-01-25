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
use Illuminate\Support\Facades\Route;

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
        CRUD::setEntityNameStrings('reparation', 'Post Reparation Checking');
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

    protected function setupInvoiceOperation()
    {
        $reparation_data = Reparation::where('id',\Route::current()->parameter('id'))->first();
        CRUD::addField([
            'name' => 'reparation_id',
            'label' => 'Reparation ID',
            'type' => 'text',
            'value' => $reparation_data->reparation_id,
        ]);

        CRUD::addField([
            'name' => 'invoice_id',
            'label' => 'Invoice ID',
            'type' => 'text',
            'value' => "INV-".strtotime("now"),
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
                    // 'type'    => 'select2_from_ajax',
                    'label'   => 'Item',
                    // 'data_source' => url('api/service'),
                    // 'attribute' => 'name',
                    // 'wrapper' => ['class' => 'form-group col-md-4'],
                ],
                [
                    'name'    => 'qty',
                    'type'    => 'number',
                    'label'   => 'Qty',
                    'default' => 1,
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
        CRUD::addSaveAction([
            'name' => 'create_invoice',
            'redirect' => function ($crud, $request, $itemId) {
                return $crud->route;
            },
            'button_text' => 'Create Invoice',
        ]);
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

    public function finishChecking($id)
    {
        $reparation = Reparation::where('id', $id)->first();
        $customer = Customer::where('id', $reparation->customer_id)->first();
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
    }
}
