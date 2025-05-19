<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListViewController extends Controller
{
    public function index(){

        $itemCode = DB::connection('sqlsrv')->select('EXEC sp_get_unique_item_codes');

        return view('listView', [
        'itemCode' => $itemCode,
        ]);
    }
    
    public function getScheduleByItemCode($item_code)
    {
        $schedules = DB::connection('sqlsrv')->select('EXEC sp_get_schedule_by_item_code ?', [$item_code]);
        return response()->json($schedules);
    }
}
