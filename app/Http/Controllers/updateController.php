<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class updateController extends Controller
{
    //

    public function index()
    {
        // $data = [];
        // $chunkSize = 1000; // Define your chunk size

        // DB::table('mutu_ancak_new')
        //     ->select("mutu_ancak_new.*", 'estate.*', DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%M") as bulan'), DB::raw('DATE_FORMAT(mutu_ancak_new.datetime, "%Y") as tahun'))
        //     ->join('estate', 'estate.est', '=', 'mutu_ancak_new.estate')
        //     ->join('wil', 'wil.id', '=', 'estate.wil')
        //     ->where('wil.regional', 1)
        //     ->orderBy('estate', 'asc')
        //     ->orderBy('afdeling', 'asc')
        //     ->orderBy('blok', 'asc')
        //     ->orderBy('datetime', 'asc')
        //     ->chunk($chunkSize, function ($results) use (&$data) {
        //         foreach ($results as $result) {
        //             // Grouping logic here, if needed
        //             $data[] = $result;
        //             // Adjust this according to your grouping requirements
        //         }
        //     });

        // // You might need to handle grouping outside the chunk loop, depending on your logic

        // // Process the $data array as needed after the chunking is complete
        // $data = collect($data)->groupBy(['estate', 'afdeling']);
        // $data = $data->toArray(); // Convert to array if needed

        // dd($data);

        return view('update', []);
    }


    public function uploaddata(Request $request)
    {
        $geo = $request->file('geoJsonFile');

        if ($geo) {
            $fileContent = file_get_contents($geo->path()); // Get the content of the uploaded file
            $data = json_decode($fileContent, true); // Decode JSON to PHP array

            if ($data !== null) {
                // Successfully decoded JSON, $data now contains your array of objects
                foreach ($data as $item) {
                    // Access individual objects in the array, for example:
                    $name = $item['name'];
                    $afdeling = $item['afdeling'];
                    $lat = $item['lat'];
                    $lon = $item['lon'];

                    // Process or manipulate the data as needed
                    // For instance, you could save it to a database, perform calculations, etc.
                }

                $names = array_unique(array_column($data, 'name'));

                // Extract all 'afdeling' values
                $afdelings = array_unique(array_column($data, 'afdeling'));

                // Convert the unique values to indexed arrays
                $uniqueNames = array_values($names);
                $uniqueAfdelings = array_values($afdelings);

                try {
                    // Delete records from the 'blok' table based on the unique names and afdelings
                    DB::table('blok')
                        ->whereIn('nama', $uniqueNames)
                        ->whereIn('afdeling', $uniqueAfdelings)
                        ->delete();

                    // Re-insert data from the decoded JSON array
                    foreach ($data as $item) {
                        $name = $item['name'];
                        $afdeling = $item['afdeling'];
                        $lat = $item['lat'];
                        $lon = $item['lon'];

                        // Insert the values into the 'blok' table
                        DB::table('blok')
                            ->insert([
                                'nama' => $name,
                                'afdeling' => $afdeling,
                                'lat' => $lat,
                                'lon' => $lon
                            ]);
                    }
                    return response()->json(['success' => true, 'message' => 'File processed']);
                } catch (\Throwable $th) {
                    // Log the error for debugging
                    Log::error('Error deleting/inserting records: ' . $th->getMessage());
                    // You can also return an error response here if needed
                    // return response()->json(['success' => false, 'message' => 'Error processing file']);
                }


                // Debugging - Dump the unique names and afdelings after deletion
                // dd($uniqueNames, $uniqueAfdelings);


            } else {
                return response()->json(['success' => false, 'message' => 'Invalid JSON format']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'File not found.']);
        }
    }

    public function conver(Request $request)

    {
        return view('concol');
    }


    public function updategeo(Request $request)

    {
        return view('updategeo');
    }



    public function geoupdate(Request $request)
    {
        set_time_limit(0); // Set execution time to unlimited (use with caution)

        $file = $request->file('geoJsonFile');

        // Read the uploaded JSON file
        $jsonData = json_decode(file_get_contents($file), true);

        // Extract 'features' from JSON data
        $features = $jsonData['features'];

        $bulkInsertData = []; // Initialize the bulk insert array

        foreach ($features as $feature) {
            // Extract 'name' and 'coordinates'
            $name = $feature['properties']['name'];
            $coordinates = $feature['geometry']['coordinates'];

            foreach ($coordinates as $coordinateSet) {
                foreach ($coordinateSet as $value) {
                    // Prepare the data for bulk insert
                    $bulkInsertData[] = [
                        'nama' => $name,
                        'afdeling' => 86, // Hardcoded 'afdeling' value
                        'lat' => strval($value[1]), // Latitude
                        'lon' => strval($value[0]), // Longitude
                    ];
                }
            }
        }

        // Chunk and insert prepared data to database table 'blok'
        $chunkedData = array_chunk($bulkInsertData, 1000); // Change the chunk size as needed
        foreach ($chunkedData as $chunk) {
            DB::table('blok')->insert($chunk);
        }

        return response()->json(['message' => 'Data inserted successfully']);
    }
}
