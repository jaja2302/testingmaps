<?php

namespace App\Http\Controllers;

use App\Models\PlotAfdelingGis;
use App\Traits\GisHelperTrait;
use Illuminate\Http\Request;

class DashboardgisAfdelingController extends Controller
{
    use GisHelperTrait;

    public function index()
    {
        $est = PlotAfdelingGis::pluck('est')->unique();
        $afdeling = PlotAfdelingGis::pluck('afd')->unique();
        return view('gisdasboardafdeling', [
            'list_est' => $est,
            'list_afdeling' => $afdeling
        ]);
    }

    public function getPlots(Request $request)
    {
        $estate = $request->input('estate');
        $afdeling = $request->input('afdeling');

        $query = PlotAfdelingGis::where('est', $estate)
            ->where('afd', $afdeling);

        return response()->json([
            'plots' => $this->getFormattedPlots($query)
        ]);
    }

    public function savePlots(Request $request)
    {
        $validationRules = [
            'est' => 'required|string',
            'afdeling' => 'required|string',
            'coordinates' => 'required|array',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lon' => 'required|numeric',
        ];

        $whereConditions = [
            'est' => $request->input('est'),
            'afd' => $request->input('afdeling')
        ];

        return $this->handlePlotSave($request, PlotAfdelingGis::class, $validationRules, $whereConditions);
    }
}
