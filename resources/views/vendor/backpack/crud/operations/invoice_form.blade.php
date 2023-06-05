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
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            <input type="text" name="reparation_id" value="{{ $entry->reparation_id }}"
                                class="form-control" readonly="readonly" disabled="disabled">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $customer_data->name }}" readonly="readonly"
                                disabled="disabled" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="+62{{ $customer_data->phone }}" readonly="readonly"
                                disabled="disabled" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Brand</label>
                            <input type="text" name="brand" value="{{ $computer_data->brand }}" readonly="readonly"
                                disabled="disabled" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Type</label>
                            <input type="text" name="type" value="{{ $computer_data->type }}" readonly="readonly"
                                disabled="disabled" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <label>Problem</label>
                            <input type="text" name="problem" value="{{ $computer_data->problem }}" readonly="readonly"
                                disabled="disabled" class="form-control">
                        </div>
                        <div class="form-group col-md-7">
                            <label>Item</label>
                            <select id="item" name="item" class="form-control">
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Qty</label>
                            <input id="qty" type="number" name="qty" class="form-control" value="1">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Add Item</label><br>
                            <a href="#" id="addItem" class="btn btn-primary"
                                data-style="zoom-in"><span class="ladda-label">Add</a>
                        </div>
                        <table id="invoice-table"
                            class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:5%; text-align:center">No.</th>
                                    <th style="width:60%; text-align:center">Item</th>
                                    <th style="width:10%; text-align:center">Qty</th>
                                    <th style="width:10%; text-align:center">Price</th>
                                    <th style="width:15%; text-align:center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice_details as $item)
                                <tr>
                                    <th style="width:5%">{{ $loop->iteration }}.</th>
                                    <th style="width:60%">{{ $item->sname }}</th>
                                    <th style="width:10%; text-align:center">{{ $item->item_qty }}</th>
                                    <th style="width:10%; text-align:right">Rp {{ number_format($item->price, 0 , '.' , ',') }}</th>
                                    <th style="width:15%"><a href={{ route('del-item', ['id' => $entry->id , 'item_id' => $item->id]) }}>Delete</a></th>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th style="width:5%"></th>
                                    <th style="width:60%"></th>
                                    <th style="width:10%">Total</th>
                                    <th style="width:25%; text-align:right">Rp {{ number_format($invoice->total, 0 , '.' , ',') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="d-none" id="parentLoadedAssets">[]</div>
                <div id="saveActions" class="form-group">
                    <a href="{{ backpack_url('reparation-done/'.$crud->entry->id.'/generate-invoice') }}" class="btn btn-success"><span class="la la-save"></span>
                        &nbsp;Generate Invoice</a>
                    <div class="btn-group" role="group">
                    </div>
                    <a href="{{ url($crud->route) }}" class="btn btn-default"><span class="la la-ban"></span>
                        &nbsp;Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('crud_fields_styles')

@endpush
@push('crud_fields_scripts')
@endpush
@section('after_scripts')

    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <link href="{{ asset('packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" />
    <!-- Datatables js -->
<script type="text/javascript" src="{{ asset('packages/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('packages/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('packages/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('packages/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('packages/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('packages/datatables.net-fixedheader-bs4/js/fixedHeader.bootstrap4.min.js') }}"></script>

<!-- Backpack js -->
<script src="{{ asset('packages/backpack/crud/js/crud.js') }}"></script>
<script src="{{ asset('packages/backpack/crud/js/form.js') }}"></script>
<script src="{{ asset('packages/backpack/crud/js/list.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $("#item").select2({
                theme: 'bootstrap',
                placeholder: "Select item",
                ajax: {
                    url: "{{ url('api/service') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    processResults: function({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                }
                            })
                        }
                        return result;
                    }
                }
            });

            $('#addItem').on("click", function(e) {
                var item = $("#item").val();
                var qty = $("#qty").val();
                var reparation_id = {{ $entry->id }};
                $.ajax({
                    url: "{{ url('admin/reparation-done/' . $entry->id . '/invoice/add-item') }}",
                    type: "post",
                    dataType: 'json',
                    data: {
                        id: {{ $entry->id }},
                        item: item,
                        qty: qty,

                    },
                    success: function(result) {
                        alert(result.success);
                        location.reload();
                    },
                    error: function(result) {
                        alert(result.error);
                        location.reload();
                    },
                });

            });
        });

        // $("#invoice-table").DataTable({
        //     ajax: "{{ url('admin/qc-inspection/' . $entry->id . '/invoice/list') }}"
            // responsive: false,
            // columnDefs: [
            //     { width: "5%", orderable: false, targets: 0 },
            //     { width: "25%", targets: 1 },
            //     { width: "20%", targets: 2 },
            //     { width: "10%", targets: 3 },
            //     { width: "40%", targets: 4 },
            // ],
            // columns: [
            //     {data: "id", name: 'id'},

            //     {data: 'name', name: 'name'},

            //     {data: 'qty', name: 'qty'},

            //     {data: 'price', name: 'price'},

            //     {data: 'action', name: 'action'},

            // ],
        // });
    </script>
@endsection
