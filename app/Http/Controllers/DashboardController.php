<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //
    public function home()
    {

        $opt = DB::table('estate')
            ->select('estate.*')
            ->where('estate.emp', '!=', 1)
            ->get();

        $opt = json_decode($opt, true);
        // dd($opt);

        return view('welcome', [
            'option' => $opt
        ]);
    }

    public function drawmaps(Request $request)
    {
        $est = $request->input('estate');


        $getplot = DB::table('blok')
            ->select('blok.*', 'afdeling.nama as namaafd')
            ->join('afdeling', 'afdeling.id', '=', 'blok.afdeling')
            ->join('estate', 'estate.id', '=', 'afdeling.estate')
            ->where('estate.id', $est)
            ->get();


        $getplot = $getplot->groupBy(function ($item) {
            return $item->namaafd . '_' . $item->nama;
        });
        $getplot = json_decode($getplot, true);

        // dd($getplot['OA_A001']);

        $pkLatLnnew = array();
        foreach ($getplot as $key => $value) {
            $latln2 = '';
            foreach ($value as $value2) {
                # code...
                // dd($value2);
                $latln2 .= '[' . $value2['lon'] . ',' . $value2['lat'] . '],';
                $pkLatLnnew[$key]['afd'] = $value2['namaafd'];
                $pkLatLnnew[$key]['latln'] = $latln2;
            }
        }


        $plot['plot'] = $pkLatLnnew;
        // dd($plot);
        echo json_encode($plot);
        // dd($est);
    }
}
