<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Service;
use App\Models\SparepartRestock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Prologue\Alerts\Facades\Alert;

trait RestockOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRestockRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/restock', [
            'as'        => $routeName . '.restock',
            'uses'      => $controller . '@restock',
            'operation' => 'restock',
        ]);

        Route::post($segment . '/{id}/restock', [
            'as'        => $routeName . '.sparepart-restock',
            'uses'      => $controller . '@postRestockForm',
            'operation' => 'restock',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRestockDefaults()
    {
        $this->crud->allowAccess('restock');

        $this->crud->operation('restock', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'restock', 'view', 'crud::buttons.restock');
            $this->crud->addButton('line', 'restock', 'view', 'crud::buttons.restock');
            $this->crud->addButtonFromModelFunction('line', 'restock', 'restockButton', 'end');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function restock()
    {
        $this->crud->hasAccessOrFail('restock');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? 'Restock Sparepart';
        $this->data['entry'] = $this->crud->getCurrentEntry();

        // load the view
        if ($this->data['entry']->category == "service") {
            \Alert::error("Can not restock service!")->flash();
            return redirect(backpack_url('service'));
        } else {
            return view("crud::operations.restock", $this->data);
        }
    }

    public function postRestockForm(Request $request)
    {
        // run validation
        $validator = Validator::make($request->all(), [
            'qty' => 'required'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput();

        $entry = $this->crud->getCurrentEntry();

        // dd(date("Y-m-d H.i.s"));

        
        DB::beginTransaction();
        try {
            $img = $request->image;
            $folderPath = "public/uploads/sparepart_restock_invoice/";
            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $entry->part_number . "_RESTOCK_" . date("Y-m-d H.i.s") . '.png';
            
            $file = $folderPath . $fileName;
            Storage::put($file, $image_base64);
            
            $restock = new SparepartRestock();
            $restock->sparepart_id = $entry->id;
            $restock->qty = $request->qty;
            $restock->invoice_dir = "uploads/sparepart_restock_invoice/" . $fileName;
            $restock->save();
            
            $sparepart_data = Service::where('id',$entry->id)->first();
            $sparepart_data->qty = $sparepart_data->qty + $request->qty;
            $sparepart_data->save();
            DB::commit();

            Alert::success('Stock Updated')->flash();

            return redirect(url($this->crud->route));
        } catch (Exception $e) {
            // show a bubble with the error message
            DB::rollBack();
            Alert::error("Error, " . $e->getMessage())->flash();

            return redirect()->back()->withInput();
        }
    }
}
