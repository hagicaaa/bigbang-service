<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ApiController extends Controller
{
    public function index(Request $request)
    {
        $search_term = $request->input('q');
        $form = collect($request->input('form'))->pluck('value', 'name');

        $options = Service::query();

        // if no category has been selected, show no options
        if (! $form['category']) {
            return [];
        }

        // if a category has been selected, only show articles in that category
        if ($form['category']) {
            $options = $options->where('name', !NULL);
        }

        if ($search_term) {
            $results = $options->where('title', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }

        return $options->paginate(10);
    }
}
