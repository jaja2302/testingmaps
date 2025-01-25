<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

trait GisHelperTrait
{
    protected function getFormattedPlots($query)
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $query->get()
                ->groupBy('est')
                ->map(function ($estateGroup) {
                    $coordinates = [];
                    foreach ($estateGroup as $point) {
                        $coordinates[] = [
                            $point->lat,
                            $point->lon
                        ];
                    }

                    // Ensure polygon is closed
                    if (
                        count($coordinates) > 0 &&
                        ($coordinates[0][0] !== $coordinates[count($coordinates) - 1][0] ||
                            $coordinates[0][1] !== $coordinates[count($coordinates) - 1][1])
                    ) {
                        $coordinates[] = $coordinates[0];
                    }

                    $feature = [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [$coordinates]
                        ],
                        'properties' => [
                            'estate' => $estateGroup->first()->est ?? null,
                            'company_plot__id' => $estateGroup->first()->company_plot__id ?? null,
                            'company_plot__pt' => $estateGroup->first()->company_plot__pt ?? null,
                        ]
                    ];

                    // // Save JSON file inside the map function where $estateGroup is available
                    // file_put_contents(
                    //     public_path('plots' . $estateGroup->first()->est . '.json'),
                    //     json_encode(['type' => 'FeatureCollection', 'features' => [$feature]], JSON_PRETTY_PRINT)
                    // );

                    return $feature;
                })
                ->values()
                ->toArray()
        ];

        return $geojson;
    }

    protected function handlePlotSave(Request $request, $model, array $validationRules, array $whereConditions)
    {
        try {
            $request->validate($validationRules);

            DB::beginTransaction();
            try {
                // Delete existing coordinates
                $model::where($whereConditions)->delete();

                // Insert new coordinates
                foreach ($request->input('coordinates') as $coord) {
                    $plotData = array_merge($whereConditions, [
                        'lat' => $coord['lat'],
                        'lon' => $coord['lon'],
                    ]);

                    if (property_exists($model, 'pt')) {
                        $plotData['pt'] = 'SSS';
                    }

                    $model::create($plotData);
                }

                DB::commit();
                return response()->json(['message' => 'Coordinates saved successfully']);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error saving plots: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in savePlots: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to save coordinates: ' . $e->getMessage()
            ], 500);
        }
    }
}
