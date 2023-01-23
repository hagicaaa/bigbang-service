@extends(backpack_view('blank'))

@php

$defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    'Email' => false,
];
// if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">Create Invoice</span>
            <small>for reparation {!! $entry->reparation_id !!}</small>
            @if ($crud->hasAccess('list'))
                <small>
                    <a href="{{ url($crud->route) }}" class="d-print-none font-sm">
                        <i
                            class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>
                        {{ trans('backpack::crud.back_to_all') }}
                        <span>{{ $crud->entity_name_plural }}</span>
                    </a>
                </small>
            @endif
        </h2>
    </section>
@endsection
@section('content')
<div class="row">
    <div class="col-md-8 bold-labels">
        @if ($errors->any())
            <div class="alert alert-danger pb-0">
                <ul class="list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li><i class="la la-info-circle"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action="">
            @csrf
            <div class="card">
                <div class="card-body row">
                    <div class="form-group col-md-4">
                        <label>Reparation ID</label>
                        <input type="text" name="reparation_id" value="{{ $entry->reparation_id }}" class="form-control" readonly="readonly" disabled="disabled">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ $customer_data->name }}" readonly="readonly" disabled="disabled" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="+62{{ $customer_data->phone }}" readonly="readonly" disabled="disabled" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Brand</label>
                        <input type="text" name="brand" value="{{ $computer_data->brand }}" readonly="readonly" disabled="disabled" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <input type="text" name="type" value="{{ $computer_data->type }}" readonly="readonly" disabled="disabled" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Problem</label>
                        <input type="text" name="problem" value="{{ $computer_data->problem }}" readonly="readonly" disabled="disabled" class="form-control">
                    </div>
                    
                </div>
            </div>
            <div class="d-none" id="parentLoadedAssets">[]</div>
            <div id="saveActions" class="form-group">
                <input type="hidden" name="_save_action" value="send_email">
                <button type="submit" class="btn btn-success">
                    <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
                    <span data-value="send_email">Send Email</span>
                </button>
                <div class="btn-group" role="group">
                </div>
                <a href="{{ url($crud->route) }}" class="btn btn-default"><span class="la la-ban"></span>
                    &nbsp;Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection