<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Models\Computer;

trait InvoiceOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupInvoiceRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/invoice', [
            'as'        => $routeName.'.invoice',
            'uses'      => $controller.'@invoice',
            'operation' => 'invoice',
        ]);
        
        Route::post($segment . '/{id}/invoice', [
            'as'        => $routeName . '.create-invoice',
            'uses'      => $controller . '@postInvoiceForm',
            'operation' => 'invoice',
        ]);
            
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupInvoiceDefaults()
    {
        $this->crud->allowAccess('invoice');

        $this->crud->operation('invoice', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'invoice', 'view', 'crud::buttons.invoice');
            $this->crud->addButton('line', 'invoice', 'view', 'crud::buttons.create_invoice');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function invoice()
    {
        $this->crud->hasAccessOrFail('invoice');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? 'Invoice '.$this->crud->entity_name;
        $this->data['entry'] = $this->crud->getCurrentEntry();
        $this->data['customer_data'] = Customer::where('id',$this->data['entry']->customer_id)->first();
        $this->data['computer_data'] = Computer::where('id',$this->data['entry']->customer_id)->first();

        // load the view
        return view("crud::operations.invoice_form", $this->data);
    }
}
