<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ApiController extends Controller
{
    public function index()
    {
        $data = Service::where('name', 'LIKE', '%'.request('q').'%')->paginate(10);;

        return response()->json($data);
    }

    public function show($id)
    {
        return Service::find($id);
    }

}
