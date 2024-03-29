<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@can('allow_booking_menu')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('booking') }}'><i class='nav-icon la la-calendar'></i> Bookings</a></li>
@endcan
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-tools"></i> Reparations</a>
    <ul class="nav-dropdown-items">
        @can('allow_cashier_activity')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reparations/create') }}'><i class='nav-icon la la-plus'></i> Add Reparation</a></li>
        @endcan
        @can('allow_reparation')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('need-checking') }}'><i class='nav-icon la la-desktop'></i> Need Checking</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('need-reparation') }}'><i class='nav-icon la la-address-card'></i> Need Reparation</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('ongoing-reparation') }}'><i class='nav-icon la la-address-card'></i> Ongoing Reparation</a></li>
        @endcan
        @can('allow_qc_inspection')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('qc-inspection') }}'><i class='nav-icon la la-address-card'></i> QC Inspection</a></li>
        @endcan
        @can('allow_cashier_activity')
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reparation-done') }}'><i class='nav-icon la la-address-card'></i> Reparation Done</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('reparations') }}'><i class='nav-icon la la-desktop'></i> Reparations List</a></li>
        @endcan
    </ul>
</li>
@role('admin')
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-server"></i> Master Data</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('computer') }}'><i class='nav-icon la la-desktop'></i> Computers</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('customer') }}'><i class='nav-icon la la-address-card'></i> Customers</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('service') }}'><i class='nav-icon la la-cogs'></i> Services & Spareparts</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sparepart-restock') }}'><i class='nav-icon la la-box'></i> Sparepart Restock</a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>
@endrole
{{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('invoice') }}'><i class='nav-icon la la-question'></i> Invoices</a></li> --}}