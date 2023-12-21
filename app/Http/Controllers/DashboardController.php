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
            // ->whereIn('blok.afdeling', [144, 145])
            ->get();


        $getplot = $getplot->groupBy('nama');

        $getplot = json_decode($getplot, true);;

        // dd($getplot);

        $estplot = DB::table('estate_plot')
            ->select('estate_plot.*')
            ->join('estate', 'estate.est', '=', 'estate_plot.est')
            ->where('estate.id', $est)
            ->get();


        $estplot = $estplot->groupBy('est');

        $estplot = json_decode($estplot, true);;




        // dd($estplot);
        // $pkLatLnnew = array();
        // $latln2 = '';
        // foreach ($getplot as $key => $value) {
        //     foreach ($value as $key2 => $value2) {
        //         # code...

        //         $latln2 .= '[' . $value2['lon'] . ',' . $value2['lat'] . '],';
        //         $pkLatLnnew[$key]['afd'] = $value2['namaafd'];
        //         $pkLatLnnew[$key]['latln'] = $latln2;
        //     }
        // }

        // dd($pkLatLnnew);

        $plot['plot'] = $getplot;
        $plot['plot_estate'] = $estplot;
        // dd($plot);
        echo json_encode($plot);
        // dd($est);
    }
}
