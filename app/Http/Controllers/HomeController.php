<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        if (!Session::has('user')) {
            return redirect()->route('login');
        }

        $mesin = DB::connection('sqlsrv')->select('EXEC sp_get_mesin_detail');
        // return view('home', compact('mesin'));

        return view('home', [
    'mesin' => $mesin,
    'now' => Carbon::now()
]);
    }
}
