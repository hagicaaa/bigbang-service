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
