<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Customer;
use App\Models\Reparation;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

/**
 * Class BookingCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class BookingCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Booking::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/booking');
        CRUD::setEntityNameStrings('booking', 'bookings');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addButtonFromView('line', 'booking-confirm', 'booking_confirm', 'beginning');
        CRUD::addButtonFromView('line', 'booking-delete', 'booking_delete', 'end');
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
            'label' => 'Customer',
            'name' => 'customer_id',
            'type' => 'select',
            'entity' => 'customers', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
        ]);
        CRUD::addColumn([
            'label' => 'Book Date',
            'name' => 'book_date',
            'type' => 'date',
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
        CRUD::setValidation(BookingRequest::class);



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

    public function deleteItem($id)
    {
        $booking_data = Booking::where('id',$id)->first();
        $customer_id = $booking_data->customer_id;
        $computer_id = $booking_data->computer_id;
        DB::beginTransaction();
        try {
            $booking_data->delete();
            Customer::where('id',$customer_id)->delete();
            Computer::where('id',$computer_id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "deleteItem";
            $error_data["controller"] = "BookingCrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Delete failed", $error_data);
            \Alert::error("Delete failed")->flash();
        }
        \Alert::add('success', 'Data deleted succesfully.')->flash();
        return redirect(backpack_url('booking'));
        
    }

    public function confirm($id)
    {
        $booking_data = Booking::where('id', $id)->first();
        $customer = Customer::where('id', $booking_data->customer_id)->first();
        // print_r($customer->name);
        // die();
        DB::beginTransaction();
        try {
            $reparation = new Reparation();
            $reparation->reparation_id = strtotime("now");
            $reparation->computer_id = $booking_data->computer_id;
            $reparation->customer_id = $booking_data->customer_id;
            $reparation->received_by = backpack_user()->id;
            $reparation->save();
            $booking_data->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "store";
            $error_data["controller"] = "BookingCrudController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        $response = Http::asForm()->post('http://localhost:3000/send', [
            'number' => $customer->phone . '@c.us',
            'message' => 'Hai kak ' . $customer->name . ', komputer anda berhasil terinput kedalam database kami. Kode reparasi anda adalah *' . $reparation->reparation_id . '*. Teknisi kami akan segera melakukan pengecekan pada komputer anda. Terimakasih, Salam Bigbang!',
        ]);
        if ($response->successful()) {
            \Alert::add('success', 'Data added succesfully.')->flash();
            return redirect(backpack_url('booking'));
        }
    }
}
