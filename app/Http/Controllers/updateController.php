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
}
