<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

class MesinController extends Controller
{
    public function index()
    {
       $machines = DB::table('machines')->get();
        return view('mesin.index', compact('mesin'));
    }
}
