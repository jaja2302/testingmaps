<?php

namespace App\Http\Controllers;

use App\Models\PlosEstataGis;
use Illuminate\Http\Request;

class DashboardgisController extends Controller
{
    //
    public function index()
    {
        $list_options = PlosEstataGis::pluck('est')->unique();
        return view('gisdasboard', compact('list_options'));
    }

    public function getPlots(Request $request)
    {
        $estate = $request->input('estate');

        $plotData = PlosEstataGis::where('est', $estate)
            ->get()
            ->groupBy('est')
            ->map(function ($estateGroup) {
                // Ubah format koordinat agar sesuai dengan format di drawmaps
                $coordinates = [];
                foreach ($estateGroup as $point) {
                    $coordinates[] = [
                        $point->lat,
                        $point->lon
                    ];
                }
                // Pastikan koordinat membentuk polygon tertutup
                if (
                    count($coordinates) > 0 &&
                    ($coordinates[0][0] !== $coordinates[count($coordinates) - 1][0] ||
                        $coordinates[0][1] !== $coordinates[count($coordinates) - 1][1])
                ) {
                    $coordinates[] = $coordinates[0];
                }
                return [
                    'coordinates' => [$coordinates] // Tambahkan array wrapper
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'plots' => $plotData
        ]);
    }

    public function savePlots(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'est' => 'required|string',
                'coordinates' => 'required|array',
                'coordinates.*.lat' => 'required|numeric',
                'coordinates.*.lon' => 'required|numeric',
            ]);

            $estate = $request->input('est');
            $coordinates = $request->input('coordinates');

            \DB::beginTransaction();
            try {
                // Delete existing coordinates for this estate
                PlosEstataGis::where('est', $estate)->delete();

                // Insert new coordinates
                foreach ($coordinates as $coord) {
                    $plotData = [
                        'est' => $estate,
                        'lat' => $coord['lat'],
                        'lon' => $coord['lon'],
                        'pt' => 'SSS'  // Pastikan field pt selalu diisi
                    ];

                    PlosEstataGis::create($plotData);
                }

                \DB::commit();
                return response()->json(['message' => 'Coordinates saved successfully']);
            } catch (\Exception $e) {
                \DB::rollback();
                \Log::error('Error saving plots: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error in savePlots: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to save coordinates: ' . $e->getMessage()
            ], 500);
        }
    }
}
