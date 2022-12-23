<div class="dropdown">
    <a href="" class="btn btn-sm btn-link" data-toggle="dropdown">
        Operations<i class="la la-caret-down"></i>
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ url($crud->route.'/'.$entry->getKey().'/start-repair') }}" class="btn btn-sm btn-link"><i class="la la-edit"></i> Start Repair</a>
        <a class="dropdown-item" href="{{ url($crud->route.'/'.$entry->getKey().'/cancel-repair') }}" class="btn btn-sm btn-link"><i class="la la-times"></i> Cancel Repair</a>
      </div>
    </div>
