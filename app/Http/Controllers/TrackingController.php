<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reparation;
use App\Models\Customer;

class TrackingController extends Controller
{
    public function index(Request $request)
    {

        $tracking_no = $request->tracking_no;

        if(Reparation::where('reparation_id', $tracking_no)->first()){
            $data = Reparation::select('reparation_id', 'computers.brand as cbrand', 'computers.type as ctype', 
            'computers.problem as cprob', 'computers.serial_number as csn', 'computers.eq_bag as cbag', 'computers.eq_charger_cable as ccharger',
            'customers.name as csname', 'customers.phone as csphone', 'inspection_date', 
            'repair_agree', 'repair_start','repair_finish', 'post_repair_inspection_date', 'users.name as received',)
            ->leftJoin('computers', 'computers.id', '=', 'computer_id')
            ->leftJoin('customers', 'customers.id', '=', 'customer_id')
            ->leftJoin('users', 'users.id','=', 'received_by')
            ->where('reparation_id', $tracking_no)
            ->first();
            json_encode($data);
            return view('tracking_detail',$data);
        }
        else{
            $number_reformat = substr($tracking_no, 1);
            if(Customer::where('phone', $number_reformat)->first()){
                $data = Reparation::select('reparation_id', 'computers.brand as cbrand', 'computers.type as ctype', 
                'computers.problem as cprob', 'computers.serial_number as csn', 'computers.eq_bag as cbag', 'computers.eq_charger_cable as ccharger',
                'customers.name as csname', 'customers.phone as csphone', 'inspection_date', 
                'repair_agree', 'repair_start','repair_finish', 'post_repair_inspection_date', 'users.name as received',)
                ->leftJoin('computers', 'computers.id', '=', 'computer_id')
                ->leftJoin('customers', 'customers.id', '=', 'customer_id')
                ->leftJoin('users', 'users.id','=', 'received_by')
                ->where('customers.phone', $number_reformat)
                ->first();
                json_encode($data);
                return view('tracking_detail',$data);
            }
            else{
                abort(404);
            }
        }
    }
}
