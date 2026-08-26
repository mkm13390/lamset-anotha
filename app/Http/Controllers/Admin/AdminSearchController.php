<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminSearchService;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function __construct(
        private readonly AdminSearchService $search
    ) {
    }

    public function index(Request $request)
    {
        $term = $request->string('q')->toString();
        $results = $this->search->search($term);

        return view('admin.search.index', compact(
            'term',
            'results'
        ));
    }
}
