<?php

namespace App\Http\Controllers;

use App\Models\Search;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Console\Input\Input;

class SearchController extends Controller
{
    public function index()
    {
    }

    public function findSearch(Request $request)
    {
        $search = $request->keyword;
        if (!empty($request->keyword)) {
            $data = Search::where('name', 'LIKE', '%' . $search . '%')->get();
            $request->session()->put('results', $data);
        }
        return view('index');
    }
}