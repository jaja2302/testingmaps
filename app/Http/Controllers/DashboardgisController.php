<?php

namespace App\Http\Controllers;

use App\Models\PlosEstataGis;
use App\Traits\GisHelperTrait;
use Illuminate\Http\Request;

class DashboardgisController extends Controller
{
    use GisHelperTrait;

    //
    public function index()
    {
        $list_options = PlosEstataGis::pluck('est')->unique();
        return view('gisdasboard', compact('list_options'));
    }

    public function getPlots(Request $request)
    {
        $estate = $request->input('estate');
        $query = PlosEstataGis::where('est', $estate);

        return response()->json([
            'plots' => $this->getFormattedPlots($query)
        ]);
    }

    public function savePlots(Request $request)
    {
        $validationRules = [
            'est' => 'required|string',
            'coordinates' => 'required|array',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lon' => 'required|numeric',
        ];

        $whereConditions = [
            'est' => $request->input('est')
        ];

        return $this->handlePlotSave($request, PlosEstataGis::class, $validationRules, $whereConditions);
    }
}
