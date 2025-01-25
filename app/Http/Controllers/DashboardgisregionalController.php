<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GisHelperTrait;
use App\Models\PlotRegionalGis;

class DashboardgisregionalController extends Controller
{
    //
    use GisHelperTrait;

    public function index()
    {
        $regional = PlotRegionalGis::pluck('reg')->unique();
        return view('gisdasboardregional', [
            'regional' => $regional
        ]);
    }

    public function getPlots(Request $request)
    {
        $regional = $request->input('regional');

        // dd($company_plot__id);


        $query = PlotRegionalGis::where('reg', $regional);

        return response()->json([
            'plots' => $this->getFormattedPlots($query)
        ]);
    }

    public function savePlots(Request $request)
    {
        $validationRules = [
            'regional' => 'required|string',
            'coordinates' => 'required|array',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lon' => 'required|numeric',
        ];

        $whereConditions = [
            'reg' => $request->input('regional')
        ];

        return $this->handlePlotSave($request, PlotRegionalGis::class, $validationRules, $whereConditions);
    }
}
