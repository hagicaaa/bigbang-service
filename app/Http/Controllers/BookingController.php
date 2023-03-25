<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Computer;
use App\Models\Customer;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request)
    {
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
            if($request->eq_bag){
                $computer->eq_bag = $request->get('eq_bag');
            }
            if($request->eq_charger_cable){
                $computer->eq_charger_cable = $request->get('eq_charger_cable');
            }
            $computer->save();
    
            $booking = new Booking();
            $booking->computer_id = $computer->id;
            $booking->customer_id = $customer->id;
            $booking->book_date = Carbon::createFromFormat('d-m-Y', $request->book_date);
            $booking->save();
            DB::commit();
        } catch(\Exception $e){
            DB::rollBack();

            //set error data for error log
            $error_data = [];
            $error_data["function"] = "index";
            $error_data["controller"] = "BookingController";
            $error_data["message"] = $e->getMessage();

            Log::error("Create failed", $error_data);
            \Alert::error("Create failed")->flash();
        }
        $response = Http::asForm()->post('http://localhost:3000/send', [
            'number' => $customer->phone.'@c.us',
            'message' => 'Hai kak '.$customer->name.', terimakasih telah melakukan booking reparasi. Anda telah melakukan booking service pada tanggal *'.$request->book_date.'*, mohon untuk datang pada tanggal yang ditentukan. Terimakasih, Salam Bigbang!',
        ]);
        if($response->successful()){
            \Alert::add('success', 'Data added succesfully.')->flash();
            return redirect()->back();
        }
    }
}
