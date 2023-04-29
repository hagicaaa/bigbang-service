<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Models\Computer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;

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
        Route::get($segment . '/{id}/invoice', [
            'as'        => $routeName . '.invoice',
            'uses'      => $controller . '@getInvoiceForm',
            'operation' => 'invoice',
        ]);

        Route::post($segment . '/{id}/invoice', [
            'as'        => $routeName . '.create-invoice',
            'uses'      => $controller . '@invoice',
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
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'invoice', 'view', 'crud::buttons.invoice');
            $this->crud->addButtonFromModelFunction('line', 'create_invoice', 'createInvoiceButton', 'beginning');
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
        $this->data['title'] = $this->crud->getTitle() ?? 'Invoice ' . $this->crud->entity_name;
        $this->data['entry'] = $this->crud->getCurrentEntry();
        $this->data['customer_data'] = Customer::where('id', $this->data['entry']->customer_id)->first();
        $this->data['computer_data'] = Computer::where('id', $this->data['entry']->customer_id)->first();

        // load the view
        return view("crud::invoice_form", $this->data);
    }

    public function getInvoiceForm()
    {
        $this->crud->hasAccessOrFail('invoice');

        $this->crud->setHeading('Create Invoice');
        $this->crud->setSubHeading('for Reparation ' . $this->crud->getCurrentEntry()->reparation_id);

        $this->data['crud'] = $this->crud;
        $this->data['entry'] = $this->crud->getCurrentEntry();
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle() ?? 'Invoice ' . $this->crud->entity_name;
        $this->data['customer_data'] = Customer::where('id', $this->data['entry']->customer_id)->first();
        $this->data['computer_data'] = Computer::where('id', $this->data['entry']->computer_id)->first();
        $this->data['invoice'] = Invoice::where('reparation_id', $this->data['entry']->id)->first();
        // $this->data['invoice_details'] = InvoiceDetail::where('invoice_id', $this->data['invoice']->id)
        // ->join('services','services.id','=','service_id')
        // ->get();

        $this->data['invoice_details'] = InvoiceDetail::select('invoice_details.id', 'invoice_id', 'service_id', 'services.name as sname', 'item_qty', 'invoice_details.price')
            ->where('invoice_id', $this->data['invoice']->id)
            ->leftJoin('services', 'services.id', '=', 'service_id')
            ->get();

        return view('crud::operations.invoice_form', $this->data);
    }

    // public function postInvoiceForm(){
    //     $this->crud->hasAccessOrFail('invoice');
    //     $request = $this->crud->validateRequest();

    //     $entry = $this->crud->getCurrentEntry();
    //     try {
    //         // send the actual email
    //         Invoice::raw($request['invoice'], function ($invoice) use ($entry, $request) {
    //             $invoice->from($request->from);
    //             $invoice->replyTo($request->reply_to);
    //             $invoice->to($entry->email, $entry->name);
    //             $invoice->subject($request['subject']);
    //         });

    //         Alert::success('Mail Sent')->flash();

    //         return redirect(url($this->crud->route));
    //     } catch (Exception $e) {
    //         // show a bubble with the error message
    //         Alert::error("Error, " . $e->getMessage())->flash();

    //         return redirect()->back()->withInput();
    //     }
    // }
}
