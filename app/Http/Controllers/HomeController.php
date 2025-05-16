<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class HomeController extends Controller
{
    public function index()
    {
        if (!Session::has('user')) {
            return redirect()->route('login');
        }

        $mesin = DB::connection('sqlsrv')->select('EXEC sp_get_mesin_detail');

        $results = DB::connection('DB2')
            ->table('PRODUCT as p')
            ->distinct()
            ->select(
                DB::raw('a.VALUEDECIMAL'),
                DB::raw('ROUND(a.VALUEDECIMAL * 24) AS CALCULATION'),
                DB::raw('TRIM(p.SUBCODE02) || \'-\' ||TRIM(p.SUBCODE03) AS HANGER'),
                DB::raw('TRIM(p.LONGDESCRIPTION) AS LONGDESCRIPTION')
            )
            ->leftJoin('ADSTORAGE as a', function($join) {
                $join->on('a.UNIQUEID', '=', 'p.ABSUNIQUEID')
                    ->where('a.FIELDNAME', '=', 'ProductionRate');
            })
            ->where('p.ITEMTYPECODE', 'KGF')
            ->orderBy('HANGER', 'ASC')
            ->get();
        // return view('home', compact('mesin'));

        return view('home', [
    'mesin' => $mesin,
    'now' => Carbon::now(),
    'filter' => $results
    ]);
    }

    public function calculation(Request $request)
{
    $tgl_dlv = Carbon::parse($request->date2);
    $qty = (int) $request->txQty;
    $no_item = $request->no_item;
    $today = Carbon::parse($request->date1);
    // if(!empty($request->no_item)){
        [$subcode02, $subcode03] = explode('-', $request->no_item);
    // }
    $results = DB::connection('DB2')
            ->table('PRODUCT as p')
            ->select(
                DB::raw('DISTINCT a.VALUEDECIMAL'),
                DB::raw('ROUND(a.VALUEDECIMAL * 24) AS CALCULATION'),
            )
            ->leftJoin('ADSTORAGE as a', function($join) {
                $join->on('a.UNIQUEID', '=', 'p.ABSUNIQUEID')
                    ->where('a.FIELDNAME', '=', 'ProductionRate');
            })
            ->where('p.ITEMTYPECODE', 'KGF')
            ->where('p.SUBCODE02', $subcode02)
            ->where('p.SUBCODE03', $subcode03)
            ->get();



    $holidays = [

    ];

    $period = CarbonPeriod::create($today, $tgl_dlv);
    $workdays = 0;

    foreach ($period as $date) {
        // Minggu = 0
        if (!in_array($date->toDateString(), $holidays) && $date->dayOfWeek !== Carbon::SUNDAY) {
            $workdays++;
        }
    }

    $count_machine = ROUND($qty/($results[0]->calculation),2);
    $kebutuhan_mesin = max(1, round($count_machine / max($workdays, 1), 0));

    return response()->json([
        'delivery_date' => $tgl_dlv->toDateString(),
        'today' => $today->toDateString(),
        'workdays_until_delivery' => $workdays,
        'qty' => $qty,
        'no_item' => $no_item,
        'calculation' => $results[0]->calculation,
        'jumlah_1mesin' => $count_machine,
        'kebutuhan_mesin' => $kebutuhan_mesin,
        'message' => "Tersisa {$workdays} hari kerja hingga tanggal delivery."
    ]);
}

public function machine(Request $request)
{
    $mesin = DB::connection('sqlsrv')->select('EXEC sp_get_mesin_detail');
}
 public function store(Request $request)
{
    $validated = $request->validate([
        'no_item'        => 'required|string',
        'qty'            => 'required|numeric',
        'qty_day'        => 'required|numeric',
        'start_date'     => 'required|date',
        'delivery_date'  => 'required|date',
        'machines'       => 'required|array',
        'machines.*'     => 'required|string'
    ]);

    $savedData = [];

    foreach ($validated['machines'] as $machineCode) {
        $dataToInsert = [
            'item_code'     => $validated['no_item'],
            'qty'           => $validated['qty'],
            'qty_day'       => $validated['qty_day'],
            'start_date'    => $validated['start_date'],
            'end_date'      => $validated['delivery_date'],
            'mesin_code'    => $machineCode,
            'dept'          => 'KNT',
            'datecreated'   => now(),
        ];

        DB::connection('sqlsrv')->table('schedule_mesin')->insert($dataToInsert);
        $savedData[] = $dataToInsert;
    }

    return response()->json([
        'status' => 'Success',
        'message' => 'Data berhasil disimpan.',
        'data' => $savedData
    ]);
}


}
