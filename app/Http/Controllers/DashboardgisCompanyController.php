<?php

namespace App\Http\Controllers;

use App\Models\PlotCompanyGis;
use Illuminate\Http\Request;
use App\Traits\GisHelperTrait;


class DashboardgisCompanyController extends Controller
{
    //
    use GisHelperTrait;

    public function index()
    {
        $company_plot__id = PlotCompanyGis::pluck('company_plot__id')->unique();
        $company_plot__pt = PlotCompanyGis::pluck('company_plot__pt')->unique();
        return view('gisdasboardcompany', [
            'company_plot__id' => $company_plot__id,
            'company_plot__pt' => $company_plot__pt
        ]);
    }

    public function getPlots(Request $request)
    {
        $company_plot__id = $request->input('company_plot__id');
        $company_plot__pt = $request->input('company_plot__pt');

        // dd($company_plot__id);


        $query = PlotCompanyGis::where('company_plot__id', $company_plot__id)
            ->where('company_plot__pt', $company_plot__pt);

        return response()->json([
            'plots' => $this->getFormattedPlots($query)
        ]);
    }

    public function savePlots(Request $request)
    {
        $validationRules = [
            'company_plot__id' => 'required|string',
            'company_plot__pt' => 'required|string',
            'coordinates' => 'required|array',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lon' => 'required|numeric',
        ];

        $whereConditions = [
            'company_plot__id' => $request->input('company_plot__id'),
            'company_plot__pt' => $request->input('company_plot__pt')
        ];

        return $this->handlePlotSave($request, PlotCompanyGis::class, $validationRules, $whereConditions);
    }
}
