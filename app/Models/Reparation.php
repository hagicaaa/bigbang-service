<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Computer;
use App\Models\Customer;
use App\Models\User;

class Reparation extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'reparations';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['inv_id','computer_id','customer_id','inspection_date','repair_start','post_repair_inspection_date','repair_finish','paid_at','received_by'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function computers()
    {
        return $this->belongsTo(Computer::class, 'computer_id');
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getEqBag()
    {
        $computer = Computer::where('id', $this->computer_id)->first();
        if($computer['eq_bag'] == 0){
            return 'No';
        }
        else{
            return 'Yes';
        }
    }

    public function getEqChargerCable()
    {
        $computer = Computer::where('id', $this->computer_id)->first();
        if($computer['eq_charger_cable'] == 0){
            return 'No';
        }
        else{
            return 'Yes';
        }
    }

    public function createInvoiceButton()
    {
        $invoice = Invoice::where('reparation_id',$this->id)->first();
        if($invoice){
            if($invoice->payment_status == 1 && $invoice->pickup_status == 1){
                return '';
            }
            else{
                return '<a href="'.backpack_url('reparation-done').'/'.$this->id.'/invoice/" name="create_invoice_detail" class="btn btn-sm btn-link"><i class="la la-edit"></i> Add Invoice Detail</a>';
            }
        }
        else{
            return '<a href="'.backpack_url('reparation-done').'/'.$this->id.'/invoice/create" name="create_invoice" class="btn btn-sm btn-link"><i class="la la-edit"></i> Create Invoice</a>';
        }
    }
    
    public function generateInvoiceButton()
    {
        $invoice = Invoice::where('reparation_id',$this->id)->first();
        if($invoice){
            return '<a href="'.backpack_url('reparation-done').'/'.$this->id.'/generate-invoice" name="generate_invoice" class="btn btn-sm btn-link"><i class="la la-note"></i> Generate Invoice</a>';
        }
    }

    public function updatePaymentButton()
    {
        $invoice = Invoice::where('reparation_id',$this->id)->first();
        if(!$invoice){
            return '';
        }
        if(!$invoice->payment_status){
            return '<a href="'.backpack_url('reparation-done').'/'.$this->id.'/update-payment" class="btn btn-sm btn-link"><i class="la la-edit"></i> Update Payment</a>';
        }
        else{
            if($invoice->payment_status == 1 && $invoice->pickup_status == 1){
                return '';
            }
            return '<a href="'.backpack_url('reparation-done').'/'.$this->id.'/update-pickup" class="btn btn-sm btn-link"><i class="la la-edit"></i> Update Pickup</a>';
        }
    }

    public function getComputer()
    {
        $computer = Computer::where('id',$this->computer_id)->first();
        return $computer->brand . " " . $computer->type;
    }

    public function getReparationStatus()
    {
        if(!$this->inspection_date){
            return 'On queue for checking';
        }
        if($this->repair_agree == 0){
            return 'Reparation Cancelled';
        }
        if($this->repair_finish){
            return 'Reparation Done';
        }
        if($this->repair_start){
            return 'On repair';
        }
        if($this->inspection_date){
            return 'Waiting for repair confirmation';
        }
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
