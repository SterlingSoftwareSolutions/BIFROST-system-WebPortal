<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\ClientManagement;

use App\Models\Newprofile;

use App\Models\Conditioning;

use App\Models\Strength;
use App\Models\StrengthSetRep;
use App\Models\Test;
use App\Models\Warmup;
use App\Models\Weightlifting;
use App\Models\WeightliftingSet;
use App\Models\WorkoutLibrary;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller
{
    //
    public function viewsession()
    {
        $userId = Auth::id(); // Get the currently authenticated user's ID

        // Fetch the access record for the user
        $access = Access::where('user_id', $userId)->first();

        if ($access && $access->session === 'enable') {
            // Pass the access type to the view using compact
            $accessType = $access->access_type;
            return view('admin.user.session', compact('accessType'));
        } else {
            // Redirect to an unauthorized access view
            return view('error.unauthorized');
        }
    }
    // get category
    public function getCategory(Request $request)
    {
        $tab = strtolower($request->input('tab'));

        // Fetch workouts based on the type
        $workouts = WorkoutLibrary::where('type', $tab)
            ->with('categoryOption') // Load the category options
            ->get();

        // Get unique category options based on the fetched workouts
        $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

        return response()->json([
            'category_options' => $categoryOptions
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        // Retrieve the date and tab from the request
        $date = $request->selectdate;
        $tab = strtolower($request->selecttab);

        // Initialize arrays to store the data
        $arrays = [];

        // Process primary data
        foreach ($request->all() as $key => $value) {
            if (preg_match('/^(category|workout|sets|reps|rest|intensity)$/', $key)) {
                $arrays[1][$key] = $value;
                $arrays[1]['type'] = 'primary';
            } elseif (preg_match('/^(category|workout|sets|reps|rest|intensity)_(\d+)$/', $key, $matches)) {
                $type = $matches[1];
                $index = $matches[2];
                if (!isset($arrays[$index])) {
                    $arrays[$index] = [];
                }
                $arrays[$index][$type] = $value;
                if (!isset($arrays[$index]['type'])) {
                    $arrays[$index]['type'] = 'primary';
                }
            }
            if (preg_match('/^(alt-category|alt-workout|alt-sets|alt-reps|alt-rest|alt-intensity)$/', $key)) {
                $type = str_replace('alt-', '', $key);
                $arrays['alt'][$type] = $value;
                $arrays['alt']['type'] = 'alternate';
            } elseif (preg_match('/^(alt-category|alt-workout|alt-sets|alt-reps|alt-rest|alt-intensity)_(\d+)$/', $key, $matches)) {
                $type = str_replace('alt-', '', $matches[1]);
                $index = $matches[2];
                if (!isset($arrays['alt_' . $index])) {
                    $arrays['alt_' . $index] = [];
                }
                $arrays['alt_' . $index][$type] = $value;
                if (!isset($arrays['alt_' . $index]['type'])) {
                    $arrays['alt_' . $index]['type'] = 'alternate';
                }
            }
        }

        // Save primary data to the database
        foreach ($arrays as $index => $data) {
            if (strpos($index, 'alt') === false) {
                $clientManagement = new ClientManagement();
                $clientManagement->category = $data['category'] ?? null;
                $clientManagement->workout = $data['workout'] ?? null;
                $clientManagement->sets = $data['sets'] ?? null;
                $clientManagement->reps = $data['reps'] ?? null;
                $clientManagement->rest = $data['rest'] ?? null;
                $clientManagement->intensity = $data['intensity'] ?? null;
                $clientManagement->date = $date;
                $clientManagement->tab = $tab;
                $clientManagement->type = $data['type'] ?? 'primary';
                $clientManagement->save();
            }
        }

        // Save alternate data to the database
        foreach ($arrays as $index => $data) {
            if (strpos($index, 'alt') !== false) {
                $clientManagement = new ClientManagement();
                $clientManagement->category = $data['category'] ?? null;
                $clientManagement->workout = $data['workout'] ?? null;
                $clientManagement->sets = $data['sets'] ?? null;
                $clientManagement->reps = $data['reps'] ?? null;
                $clientManagement->rest = $data['rest'] ?? null;
                $clientManagement->intensity = $data['intensity'] ?? null;
                $clientManagement->date = $date;
                $clientManagement->tab = $tab;
                $clientManagement->type = $data['type'] ?? 'alternate';
                $clientManagement->save();
            }
        }

        return redirect()->back();
    }
    public function getdata(Request $request)
    {
        $tab = strtolower($request->tab);
        $date = $request->date;
        $workouts = WorkoutLibrary::where('type', $tab)->with('categoryOption',)->get();
        $details = ClientManagement::where('tab', $tab)
            ->where('date', $date)
            ->with('workouts.categoryOption')
            ->get();

        return response()->json(['workouts' => $workouts, 'details' => $details]);
    }

    public function getworkout(Request $request)
    {
        $tab = strtolower($request->tab);
        $id = $request->id;
        // category option id andb type filter
        $workouts = WorkoutLibrary::where([
            ['type', $tab],
            ['category_options_id', $id],
        ])->get();
        return response()->json(['workouts' => $workouts]);
    }
    public function update(Request $request)
    {
        // Extracting data from the request
        $data = $request->all();

        // Loop through the data to find detail ids and update the corresponding records
        foreach ($data as $key => $value) {
            if (preg_match('/^detail_id_(\d+)$/', $key, $matches)) {
                $id = $matches[1];

                // Find the ClientManagement record by id
                $clientManagement = ClientManagement::find($value);

                if ($clientManagement) {
                    // Update the fields
                    $clientManagement->category = $data["category_$id"] ?? null;
                    $clientManagement->workout = $data["workout_$id"] ?? null;
                    $clientManagement->sets = $data["sets_$id"] ?? null;
                    $clientManagement->reps = $data["reps_$id"] ?? null;

                    // Format the time here
                    $restTime = DateTime::createFromFormat('H:i', $data["rest_$id"]);
                    $clientManagement->rest = $restTime ? $restTime->format('h:i') : null;

                    $clientManagement->intensity = $data["intensity_$id"] ?? null;

                    // Save the updated record
                    $clientManagement->save();
                }
            }
        }
        return redirect()->back();
    }

    public function storewarmup(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'categoryw_*' => 'required|exists:category_options,id',
            'workoutw_*' => 'required|exists:workout_libraries,id',
            'repsw_*' => 'required|integer',
            'weigthw_*' => 'required|numeric',
            'selectdatew' => 'required',  // Validate the date
        ]);
        try {
            // Extract the data from the request
            $warmups = [];
            $date = $request->input('selectdatew');  // Get the date

            foreach ($request->all() as $key => $value) {
                if (preg_match('/^categoryw_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $warmups[$index]['category_id'] = $value;
                } elseif (preg_match('/^workoutw_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $warmups[$index]['workout_id'] = $value;
                } elseif (preg_match('/^repsw_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $warmups[$index]['reps'] = $value;
                } elseif (preg_match('/^weigthw_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $warmups[$index]['weight'] = $value;
                }
            }

            // Store each warmup set with the date
            foreach ($warmups as $warmupData) {
                $warmupData['date'] = $date;  // Add the date to each record
                Warmup::create($warmupData);
            }

            // Redirect or respond with a success message
            return response()->json(['message' => 'Warmup data saved successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while store warmups.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateWarmup(Request $request)
    {
        // Validate the common ID
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:warmups,id',
        ]);
        try {
            // Find the warmup record by ID
            $warmup = Warmup::findOrFail($validatedData['id']);

            // Loop through the request data to update corresponding fields
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^categorywe_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];

                    $validatedFormData = $request->validate([
                        "categorywe_$index" => 'required|integer|exists:category_options,id',
                        "workoutwe_$index" => 'required|integer|exists:workout_libraries,id',
                        "repswe_$index" => 'required|integer|min:1',
                        "weightwe_$index" => 'required|numeric|min:0',
                    ]);

                    // Update the warmup record with the validated data
                    $warmup->category_id = $validatedFormData["categorywe_$index"];
                    $warmup->workout_id = $validatedFormData["workoutwe_$index"];
                    $warmup->reps = $validatedFormData["repswe_$index"];
                    $warmup->weight = $validatedFormData["weightwe_$index"];
                    $warmup->save();
                }
            }

            // Return a response
            return response()->json(['message' => 'Warmup updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
    // delete warmup
    public function deleteAllBySelectDateWarmups(Request $request)
    {
        $selectDate = $request->input('selectdatewd');

        // Validate the input
        $request->validate([
            'selectdatewd' => 'required',  // Assuming selectdatew is a date
        ]);
        try {
            // Delete all warmups with the given selectdatew
            Warmup::where('date', $selectDate)->delete();

            return response()->json([
                'message' => 'All warmups for the selected date have been deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting warmups.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // get Warmup
    public function getwarmup(Request $request)
    {
        $date = $request->input('date');

        // Fetch warmup data with related category and workout details
        $warmup = Warmup::with(['category', 'workout'])
            ->where('date', $date)
            ->get();

        // ctecory list
        // Fetch workouts based on the type
        $workouts = WorkoutLibrary::where('type', 'warmup')
            ->with('categoryOption') // Load the category options
            ->get();

        // Get unique category options based on the fetched workouts
        $categoryOptions = $workouts->pluck('categoryOption')->unique('id');


        // Transform the result to include the desired fields
        $result = $warmup->map(function ($item) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'category_id' => $item->category_id,
                'weight' => $item->weight,
                'category_name' => $item->category ? $item->category->category_name : null,
                'workout_id' => $item->workout_id,
                'workout_type' => $item->workout ? $item->workout->type : null,
                'reps' => $item->reps,
            ];
        });

        return response()->json(['result' => $result, 'categoryOptions' => $categoryOptions]);
    }
    // Store Weightlifting Start
    //  Store Weightlifting
    public function storeweightlifting(Request $request)
    {
        // dd($request);
        // Validate the request data
        $request->validate([
            'selectdatewe' => 'required',
            'categorywe_*' => 'required|integer|exists:category_options,id',
            'workoutwe_*' => 'required|integer|exists:workout_libraries,id',
            'weigthwe_*' => 'required|integer',
            'setswe_*' => 'required|integer',
            'repswe_*' => 'required|integer',
            // Add restwe_* validation
            'restwe_*' => 'required|date_format:H:i:s',
            'intensitywe_*' => 'required|string',

            'alt-categorywe_*' => 'required|integer|exists:category_options,id',
            'alt-workoutwe_*' => 'required|integer|exists:workout_libraries,id',
            'alt-weigthwe_*' => 'required|integer',
            'alt-setswe_*' => 'required|integer',
            'alt-repswe_*' => 'required|integer',
            // Add alt-restwe_* validation
            'alt-restwe_*' => 'required|date_format:H:i:s',
            'alt-intensitywe_*' => 'required|string',
        ]);
        try {
            $requestData = $request->all();
            $maxIndex = 10; // Maximum index to check, adjust this as needed

            // Loop through each index
            for ($index = 1; $index <= $maxIndex; $index++) {
                $processedData = []; // Initialize the array for the current index

                // Iterate over all request data
                foreach ($requestData as $key => $value) {
                    // Check if the key contains the current index
                    if (strpos($key, "_$index") !== false) {
                        // Add the key-value pair to the array
                        $processedData[$key] = $value;
                    }
                }

                if (!empty($processedData)) {
                    // Call the filterdata function to process and store the data
                    // dd($processedData);
                    $this->filterdata($processedData, $index, $request->input('selectdatewe'));
                }
            }

            return response()->json([
                'message' => 'Weightlifting record stored successfully'
            ], 201);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json([
                'message' => 'Failed to store Weightlifting record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filterdata($processedData, $index, $date)
    {
        // Initialize an array to hold the parsed data
        $parsedData = [];
        $setParsedData = [];

        // Extract the indexed values from the input data
        $fields = ['category', 'workout', 'weigth', 'rest', 'intensity', 'alt-category', 'alt-workout', 'alt-weigth', 'alt-rest', 'alt-intensity'];

        foreach ($fields as $field) {
            $key = $field . 'we_' . $index;
            if (isset($processedData[$key])) {
                $parsedData[$field] = $processedData[$key];
            }
        }

        if ($date) {
            $parsedData['date'] = $date;
        }

        // Process the data as needed
        $WeightliftingData = Weightlifting::store($parsedData);

        $setFields = ['setswe', 'repswe', 'alt-setswe', 'alt-repswe'];
        foreach ($setFields as $setField) {
            // Check for keys with numbers
            $patternWithNumbers = '/^' . preg_quote($setField . '_' . $index) . '[0-9]+$/';
            // Check for keys without numbers
            $patternWithoutNumbers = '/^' . preg_quote($setField . '_' . $index) . '$/';

            foreach ($processedData as $key => $value) {
                if (preg_match($patternWithNumbers, $key) || preg_match($patternWithoutNumbers, $key)) {
                    $setParsedData[$key] = $value;
                }
            }
        }
        // Add the foreign key if Weightlifting data is available
        if ($WeightliftingData) {
            $setParsedData['foreignKey'] = $WeightliftingData->id;
        }

        // add part
        // Initialize arrays to hold the parsed data
        $sets = [];
        $reps = [];
        $altSets = [];
        $altReps = [];
        $foreignKey = $WeightliftingData->id;

        // Iterate over all processed data
        foreach ($processedData as $key => $value) {
            // Check for setswe keys
            if (strpos($key, 'setswe_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $sets[$suffix] = $value;
            } elseif (strpos($key, 'repswe_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $reps[$suffix] = $value;
            } elseif (strpos($key, 'alt-setswe_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $altSets[$suffix] = $value;
            } elseif (strpos($key, 'alt-repswe_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $altReps[$suffix] = $value;
            } elseif ($key === 'foreignKey') {
                $foreignKey = $value;
            }
        }

        // Combine parsed data into rows
        $combinedData = [];
        $allKeys = array_merge(array_keys($sets), array_keys($reps), array_keys($altSets), array_keys($altReps));
        $uniqueIndexes = array_unique($allKeys);

        foreach ($uniqueIndexes as $index) {
            $row = [
                isset($sets[$index]) ? $sets[$index] : null,
                isset($reps[$index]) ? $reps[$index] : null,
                isset($altSets[$index]) ? $altSets[$index] : null,
                isset($altReps[$index]) ? $altReps[$index] : null,
                $foreignKey,
            ];
            $combinedData[] = $row;
        }

        // Process the combined data as needed
        // Example: Store the combined data in another table
        foreach ($combinedData as $row) {
            // dd($row);
            WeightliftingSet::store($row);
        }
    }
    // Store Weightlifting End

    // get Weightlifting Start
    public function getWeightlifting(Request $request)
    {
        try {
            $date = $request->input("date");

            // Get Weightlifting with category, workout, altCategory, and altWorkout relationships
            $weightlifting = Weightlifting::where('date', $date)
                ->with(['category', 'workout', 'altCategory', 'altWorkout'])
                ->get();

            // Fetch workouts based on the type
            $workouts = WorkoutLibrary::where('type', 'weightlifting')
                ->with('categoryOption') // Load the category options
                ->get();

            // Get unique category options based on the fetched workouts
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            // Map the weightlifting data to include the sets
            $result = $weightlifting->map(function ($item) {
                // Fetch sets for the current weightlifting item
                $sets = WeightliftingSet::where('weightlifting_id', $item->id)->get();

                return [
                    'id' => $item->id,

                    'category_id' => $item->category_id,
                    'category_name' => $item->category ? $item->category->category_name : null,

                    'workout_id' => $item->workout_id,
                    'workout_type' => $item->workout ? $item->workout->workout : null,

                    'weight' => $item->weight,
                    'rest' => $item->rest,
                    'intensity' => $item->intensity,

                    'alt_category_id' => $item->alt_category_id,
                    'alt_category_name' => $item->altCategory ? $item->altCategory->category_name : null,

                    'alt_workout_id' => $item->alt_workout_id,
                    'alt_workout_type' => $item->altWorkout ? $item->altWorkout->workout : null,

                    'alt_weight' => $item->alt_weight,
                    'alt_rest' => $item->alt_rest,
                    'alt_intensity' => $item->alt_intensity,

                    'date' => $item->date,
                    'sets' => $sets, // Include the sets in the result
                ];
            });

            return response()->json([
                'weightlifting' => $result,
                'categoryOptions' => $categoryOptions,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // update weightlifting
    public function updateWeightlifting(Request $request)
    {
        // dd($request);
        try {
            // Validate the incoming request data
            $request->validate([
                'id_*' => 'required|integer|exists:weightliftings,id',
                'categoryweight_*' => 'required|integer|exists:category_options,id',
                'workoutweight_*' => 'required|integer|exists:workout_libraries,id',
                'weigthweight_*' => 'required|integer',
                'setsweight_*' => 'required|integer',
                'repsweight_*' => 'required|integer',
                'restweight_*' => 'required|date_format:H:i:s',
                'intensityweight_*' => 'required|string',
                'altcategoryweight_*' => 'required|integer|exists:category_options,id',
                'altworkoutweight_*' => 'required|integer|exists:workout_libraries,id',
                'altweigthweight_*' => 'required|integer',
                'altsetsweight_*' => 'required|integer',
                'altrepsweight_*' => 'required|integer',
                'altrestweight_*' => 'required|date_format:H:i:s',
                'altintensityweight_*' => 'required|string',
            ]);

            // Dynamically find the ID from the request
            $weightliftingId = null;
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'id_') === 0) {
                    $weightliftingId = $value;
                    break; // Exit the loop once the ID is found
                }
            }

            // Check if ID was found
            if (!$weightliftingId) {
                return response()->json(['message' => 'ID not found'], 400);
            }

            // Retrieve the existing weightlifting record by ID
            $weightlifting = Weightlifting::findOrFail($weightliftingId);

            // Update the weightlifting record with new data
            $weightlifting->update([
                'category_id' => $request->input('categoryweight_' . $weightliftingId),
                'workout_id' => $request->input('workoutweight_' . $weightliftingId),
                'weight' => $request->input('weigthweight_' . $weightliftingId),
                'rest' => $request->input('restweight_' . $weightliftingId),
                'intensity' => $request->input('intensityweight_' . $weightliftingId),
                'alt_category_id' => $request->input('altcategoryweight_' . $weightliftingId),
                'alt_workout_id' => $request->input('altworkoutweight_' . $weightliftingId),
                'alt_weight' => $request->input('altweigthweight_' . $weightliftingId),
                'alt_rest' => $request->input('altrestweight_' . $weightliftingId),
                'alt_intensity' => $request->input('altintensityweight_' . $weightliftingId),
            ]);

            // Update existing weightlifting sets
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^setsid_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $weightliftingSet = WeightliftingSet::findOrFail($request->input('setsid_' . $index));
                    $weightliftingSet->update([
                        'sets' => $request->input('setsweight_' . $index),
                        'reps' => $request->input('repsweight_' . $index),
                        'alt_sets' => $request->input('altsetsweight_' . $index),
                        'alt_reps' => $request->input('altrepsweight_' . $index),
                        'weightlifting_id' => $weightlifting->id,
                    ]);
                }
            }

            // Create new weightlifting sets
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^setswe_(\d+)(\d+)$/', $key, $matches) || preg_match('/^alt-setswe_(\d+)(\d+)$/', $key, $matches)) {
                    $index = $matches[2];
                    $sets = $request->input('setswe_' . $weightliftingId . $index);
                    $reps = $request->input('repswe_' . $weightliftingId . $index);
                    $alt_sets = $request->input('alt-setswe_' . $weightliftingId . $index);
                    $alt_reps = $request->input('alt-repswe_' . $weightliftingId . $index);

                    // Debugging statements
                    Log::info("Processing set: setswe_" . $weightliftingId . $index);
                    Log::info("Sets: $sets, Reps: $reps, Alt Sets: $alt_sets, Alt Reps: $alt_reps");

                    WeightliftingSet::create([
                        'sets' => $sets,
                        'reps' => $reps,
                        'alt_sets' => $alt_sets,
                        'alt_reps' => $alt_reps,
                        'weightlifting_id' => $weightlifting->id,
                    ]);
                }
            }

            // dd("Weightlifting data updated successfully");
            return response()->json(['message' => 'Weightlifting data updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'An error occurred while updating the weightlifting data.'], 500);
        }
    }
    public function deleteAllBySelectDateWeightlifting(Request $request)
    {
        // Validate the selected date
        $validated = $request->validate([
            'selectdateweDelete' => 'required',
        ]);

        $selectedDate = $validated['selectdateweDelete'];

        try {
            // Attempt to delete the records
            $deletedCount = Weightlifting::where('date', $selectedDate)->delete();

            // Check if the request expects a JSON response
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Weightlifting sessions deleted successfully', 'deleted_count' => $deletedCount], 200);
            }

            // For non-AJAX requests, redirect back with success message
            return redirect()->back()->with('success', 'Weightlifting sessions deleted successfully');
        } catch (\Exception $e) {
            // Log the exception message for debugging
            Log::error('Error deleting weightlifting sessions: ' . $e->getMessage());

            // Check if the request expects a JSON response
            if ($request->expectsJson()) {
                return response()->json(['message' => 'An error occurred while deleting weightlifting sessions.'], 500);
            }

            // For non-AJAX requests, redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while deleting weightlifting sessions.');
        }
    }

    // strenght store
    public function strengthstore(Request $request)
    {
        // dd($request);
        // Log the incoming request
        Log::info('Received request data:', $request->all());

        // Validate the incoming request data
        $request->validate([
            'category_*' => 'required|exists:category_options,id',
            'workout_*' => 'required|exists:workout_libraries,id',
            'weight_*' => 'required',
            'rest_*' => 'nullable',
            'intensity_*' => 'required',
            'alt_category_*' => 'required|exists:category_options,id',
            'alt_workout_*' => 'required|exists:workout_libraries,id',
            'alt_weight_*' => 'required',
            'alt_rest_*' => 'nullable',
            'alt_intensity_*' => 'required',
            'selectdate_*' => 'required',
            'sets_*' => 'required',
            'reps_*' => 'required',
            'alt_sets_*' => 'required',
            'alt_reps_*' => 'required',
        ]);

        $requestData = $request->all();
        $maxIndex = 10; // Maximum index to check, adjust this as needed

        for ($index = 1; $index <= $maxIndex; $index++) {
            $processedData = []; // Initialize the array for the current index

            // Iterate over all request data
            foreach ($requestData as $key => $value) {
                // Check if the key contains the current index
                if (strpos($key, "_$index") !== false) {
                    // Add the key-value pair to the array
                    $processedData[$key] = $value;
                }
            }

            if (!empty($processedData)) {
                // Call the filterdatastrength function to process and store the data
                $this->filterdatastrength($processedData, $index, $request->input("selectdates"));
            }
        }

        return redirect()->back();
    }
    // strenghtfillter
    public function filterdatastrength($processedData, $index, $date)
    {

        $parsedData = [];
        $setParsedData = [];
        // Extract the indexed values from the input data
        $fields = ['categorys', 'workouts', 'weigths', 'rests', 'intensitys', 'alt-categorys', 'alt-workouts', 'alt-weigths', 'alt-rests', 'alt-intensitys'];

        foreach ($fields as $field) {
            $key = $field . '_' . $index;
            if (isset($processedData[$key])) {
                $parsedData[$field] = $processedData[$key];
            }
        }
        // dd($parsedData);

        if ($date) {

            $parsedData['date'] = $date;
        }
        // dd($parsedData);
        $strengthData = Strength::store($parsedData);

        $setFields = ['sets', 'reps', 'alt-sets', 'alt-reps'];
        foreach ($setFields as $setField) {

            // Check for keys with numbers
            $patternWithNumbers = '/^' . preg_quote($setField . '_' . $index) . '[0-9]+$/';
            // Check for keys without numbers
            $patternWithoutNumbers = '/^' . preg_quote($setField . '_' . $index) . '$/';

            foreach ($processedData as $key => $value) {
                if (preg_match($patternWithNumbers, $key) || preg_match($patternWithoutNumbers, $key)) {
                    $setParsedData[$key] = $value;
                }
            }
        }
        // dd($setField);
        // Add the foreign key if Strength data is available
        if ($strengthData) {
            $setParsedData['foreignKey'] = $strengthData->id;
        }

        // Initialize arrays to hold the parsed data
        $sets = [];
        $reps = [];
        $altSets = [];
        $altReps = [];
        $foreignKey = $strengthData->id;

        // Iterate over all processed data
        foreach ($processedData as $key => $value) {
            // Check for sets keys
            if (strpos($key, 'sets_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $sets[$suffix] = $value;
            } elseif (strpos($key, 'reps_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $reps[$suffix] = $value;
            } elseif (strpos($key, 'alt-sets_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $altSets[$suffix] = $value;
            } elseif (strpos($key, 'alt-reps_') === 0) {
                $suffix = explode('_', $key)[1]; // Extract numeric suffix
                $altReps[$suffix] = $value;
            } elseif ($key === 'foreignKey') {
                $foreignKey = $value;
            }
        }


        // Combine parsed data into rows
        $combinedData = [];
        $allKeys = array_merge(array_keys($sets), array_keys($reps), array_keys($altSets), array_keys($altReps));
        $uniqueIndexes = array_unique($allKeys);

        foreach ($uniqueIndexes as $index) {
            $row = [
                isset($sets[$index]) ? $sets[$index] : null,
                isset($reps[$index]) ? $reps[$index] : null,
                isset($altSets[$index]) ? $altSets[$index] : null,
                isset($altReps[$index]) ? $altReps[$index] : null,
                $foreignKey,
            ];
            $combinedData[] = $row;
        }

        // Process the combined data as needed
        // Example: Store the combined data in another table
        foreach ($combinedData as $row) {
            //  dd($row);
            StrengthSetRep::store($row);
        }
    }

    // getstrenght
    public function getstrength(Request $request)
    {
        try {
            $date = $request->input("date");
            Log::info('Received date: ' . $date); // Log the received date

            $strengthRecords = Strength::where('date', $date)
                ->with(['category', 'workout', 'setstrengthsetsreps', 'altCategory', 'altWorkout'])
                ->get();

            Log::info('Strength records fetched: ' . $strengthRecords->count()); // Log the number of records

            $workouts = WorkoutLibrary::where('type', 'Strength')
                ->with('categoryOption')
                ->get();

            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $result = $strengthRecords->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_name' => $item->category ? $item->category->category_name : null,



                    'workout_id' => $item->workout_id,
                    'workout_type' => $item->workout ? $item->workout->workout : null,

                    'weight' => $item->weight,
                    'rest' => $item->rest,
                    'intensity' => $item->intensity,


                    'alt_category_id' => $item->alt_category_id,
                    'alt_category_name' => $item->altCategory ? $item->altCategory->category_name : null,


                    'alt_workout_id' => $item->alt_workout_id,
                    'alt_workout_type' => $item->altWorkout ? $item->altWorkout->workout : null,


                    'alt_weight' => $item->altweight,
                    'alt_rest' => $item->altrest,
                    'alt_intensity' => $item->altintensity,

                    'date' => $item->date,
                    'sets' => $item->setstrengthsetsreps, // Use the relationship method to get sets
                ];
            });

            Log::info('Response data strength: ', ['Strength' => $result, 'categoryOptions' => $categoryOptions]);

            return response()->json([
                'Strength' => $result,
                'categoryOptions' => $categoryOptions,

            ]);
        } catch (\Exception $e) {
            Log::error('Error in getstrength: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // updatestrenght
    public function updatestrength(Request $request)
    {

        Log::info('Request Data dd:', ['request' => $request->all()]);

        try {
            $request->validate([
                'id_*' => 'required|integer|exists:weightliftings,id',
                'categorystrength_*' => 'required|integer|exists:category_options,id',
                'workoutstrength_*' => 'required|integer|exists:workout_libraries,id',
                'weigthstrength_*' => 'required|integer',
                'setsstrength_*' => 'required|integer',
                'repsstrength_*' => 'required|integer',
                'reststrength_*' => 'required|date_format:H:i:s',

                'intensitystrength_*' => 'required|string',

                'altcategorystrength_*' => 'required|integer|exists:category_options,id',
                'altworkoutstrengths_*' => 'required|integer|exists:workout_libraries,id',
                'altweigthstrength_*' => 'required|integer',
                'altsetsstrength_*' => 'required|integer',
                'altrepssstrength_*' => 'required|integer',
                'altreststrength_*' => 'required|date_format:H:i:s',
                'altintensitystrength_*' => 'required|string',

            ]);
            // Dynamically find the ID from the request
            $strengthId = null;
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'id_') === 0) {
                    $strengthId = $value;
                    break; // Exit the loop once the ID is found
                }
            }

            // Check if ID was found
            if (!$strengthId) {
                return response()->json(['message' => 'ID not found'], 400);
            }
            $strength = Strength::findOrFail($strengthId);

            // Update the weightlifting record with new data
            //  dd($request);
            //  dd($request->input('altworkoutstrengths_' . $strengthId));
            $strength->update([
                'category_id' => $request->input('categorystrength_' . $strengthId),
                'workout_id' => $request->input('workoutstrength_' . $strengthId),
                'weight' => $request->input('weigthstrength_' . $strengthId),
                'rest' => $request->input('reststrength_' . $strengthId),
                'intensity' => $request->input('intensitystrength_' . $strengthId),
                'alt_category_id' => $request->input('altcategorystrength_' . $strengthId),
                'alt_workout_id' => $request->input('altworkoutstrengths_' . $strengthId),
                'altweight' => $request->input('altweigthstrength_' . $strengthId),
                'altrest' => $request->input('altreststrength_' . $strengthId),
                'altintensity' => $request->input('altintensitystrength_' . $strengthId),
            ]);
            // dd($strength);
            // Update existing weightlifting sets
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^setsid_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];
                    $strengthrepsset = StrengthSetRep::findOrFail($request->input('setsid_' . $index));
                    $strengthrepsset->update([
                        'sets' => $request->input('setsstrength_' . $index),
                        'reps' => $request->input('repsstrength_' . $index),
                        'alt_sets' => $request->input('altsetsstrength_' . $index),
                        'alt_reps' => $request->input('altrepssstrength_' . $index),
                        'strength_id' => $strength->id,
                    ]);
                }
            }
            // Create new  sets
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^sets_(\d+)(\d+)$/', $key, $matches) || preg_match('/^alt-sets_(\d+)(\d+)$/', $key, $matches)) {
                    Log::info("data cheack", ['request' => $request->all()]);
                    $index = $matches[2];

                    $sets = $request->input('sets_' . $strengthId . $index);
                    $reps = $request->input('reps_' . $strengthId . $index);
                    $alt_sets = $request->input('alt-sets_' . $strengthId . $index);
                    $alt_reps = $request->input('alt-reps_' . $strengthId . $index);


                    // Debugging statements
                    Log::info("Processing set: setswe_" . $strengthId . $index);
                    Log::info("Sets: $sets, Reps: $reps, Alt Sets: $alt_sets, Alt Reps: $alt_reps");

                    $newSet = StrengthSetRep::create([
                        'sets' => $sets,
                        'reps' => $reps,
                        'alt_sets' => $alt_sets,
                        'alt_reps' => $alt_reps,
                        'strength_id' => $strength->id,
                    ]);

                    Log::info('New StrengthSetRep created successfully.', ['id' => $newSet->id]);
                }
            }

            return response()->json(['message' => 'update suceess']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions

            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Handle other exceptions

            return redirect()->back()->with('error', 'An error occurred while updating the weightlifting data.');
        }
    }
    // deletestrenght
    public function deleteAllByDelectDataStrenght(Request $request)
    {
        // dd($request);
        $selectedDate = $request->input('selectdatestrenghtDelete');
        // Validate the selected date
        $request->validate([
            'selectdatestrenghtDelete' => 'required',
        ]);
        try {
            // Attempt to delete the records
            Strength::where('date', $selectedDate)->delete();

            // Redirect back with success message
            return redirect()->back()->with('status', 'Strength sessions deleted successfully!');
        } catch (Exception $e) {
            // Log the exception message for debugging
            Log::error('Error deleting Strength sessions: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while deleting Strength sessions.');
        }
    }



    public function getmember(Request $request)
    {
        // Fetch all members
        $members = Newprofile::all();

        // Log the members data
        Log::info('Members Data:', $members->toArray());

        return response()->json([
            'members' => $members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->firstname,
                ];
            })
        ]);
    }


    public function storeTest(Request $request)
    {
        //  dd($request);
        $request->validate([
            'test-category_*' => 'required|exists:category_options,id',
            'test-workout_*' => 'required|exists:workout_libraries,id',
            'test-member_*' => 'required',
            'selectdatet_*' => 'required',
        ]);

        $requestData = $request->all();
        $maxIndex = 10; // Maximum index to check, adjust this as needed

        for ($index = 1; $index <= $maxIndex; $index++) {
            $processedData = []; // Initialize the array for the current index

            // Iterate over all request data
            foreach ($requestData as $key => $value) {
                // Check if the key contains the current index
                if (strpos($key, "_$index") !== false) {
                    // Add the key-value pair to the array
                    $processedData[$key] = $value;
                }
            }

            if (!empty($processedData)) {
                // Call the filterdatastrength function to process and store the data
                $this->filterdatastest($processedData, $index, $request->input("selectdatet"));
            }
        }
        return redirect()->back()->with('success', 'Data saved successfully!');
    }
    public function filterdatastest($processedData, $index, $date)

    {
        $parsedData = [];
        // Extract the indexed values from the input data
        $fields = ['test-category', 'test-workout', 'test-member'];
        // dd($fields);
        foreach ($fields as $field) {
            $key = $field . '_' . $index;
            if (isset($processedData[$key])) {
                $parsedData[$field] = $processedData[$key];
            }
        }
        // dd($parsedData);

        if ($date) {

            $parsedData['date'] = $date;
        }
        // dd($parsedData);
        Test::store($parsedData);
    }

    public function gettest(Request $request)
    {
        try {
            $date = $request->input("date");
            Log::info('Received date: ' . $date);

            $testRecords = Test::where('date', $date)
                ->with(['category', 'workout', 'member'])
                ->get();

            Log::info('Test records fetched: ' . $testRecords->count());
            $memebers = Newprofile::all();
            $workouts = WorkoutLibrary::where('type', 'Test')->with('categoryOption')->get();
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $result = $testRecords->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_name' => $item->category ? $item->category->category_name : null,
                    'workout_id' => $item->workout_id,
                    'workout_name' => $item->workout ? $item->workout->workout : null,
                    'member_id' => $item->member_id,
                    'member_name' => $item->member ? $item->member->name : null
                ];
            });
            Log::info('Test records fetched: ' . $result);
            $responseData = [
                'message' => 'Test data successfully retrieved.',
                'Test' => $result,
                'Members' => $memebers,
                'categoryOptions' => $categoryOptions
            ];

            Log::info('Response data:', $responseData);

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Error in test: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching test data.'], 500);
        }
    }

    public function updatest(Request $request)
    {
        // dd($request);
        try {
            $request->validate([
                'id_*' => 'required|integer|exists:weightliftings,id',
                'categorytest_*' => 'required|integer|exists:category_options,id',
                'workouttest_*' => 'required|integer|exists:workout_libraries,id',
                'test-member_*' => 'required',
            ]);
            $testId = null;
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'id_') === 0) {
                    $testId = $value;
                    break; // Exit the loop once the ID is found
                }
            }
            // Check if ID was found
            if (!$testId) {
                return response()->json(['message' => 'ID not found'], 400);
            }
            $test = Test::findOrFail($testId);
            $test->update([
                'category_id' => $request->input('categorytest_' . $testId),
                'workout_id' => $request->input('workouttest_' . $testId),
                'member_id' => $request->input('test-member_' . $testId)

            ]);


            return response()->json(['message' => 'update suceess']);
        } catch (\Exception $e) {

            return redirect()->back()->with('error', 'an error occurred while updateing the test data');
        }
    }

    public function deletealldatatest(Request $request)
    {
        // dd($request);
        $selectedDate = $request->input('selectdatetestDelete');
        // Validate the selected date
        $request->validate([
            'selectdatetestDelete' => 'required',
        ]);
        try {
            // Attempt to delete the records
            Test::where('date', $selectedDate)->delete();

            // Redirect back with success message
            return redirect()->back()->with('status', 'Strength sessions deleted successfully!');
        } catch (Exception $e) {
            // Log the exception message for debugging
            Log::error('Error deleting Strength sessions: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'An error occurred while deleting Strength sessions.');
        }
    }
    // store conditioning
    public function storeconditioning(Request $request)
    {
        // dd($request);

        // Validate the incoming request data
        $request->validate([
            'selectdatec' => 'required|string',
            'rounds' => 'nullable|integer', // `rounds` is optional
            'amrap' => 'nullable', // `amrapCheckbox` should be a boolean
            'categoryc_*' => 'required|integer|exists:category_options,id',
            'workoutc_*' => 'required|integer|exists:workout_libraries,id',
            'repsc_*' => 'required|integer',
            'weigthc_*' => 'required|numeric',
            'timeTC_*' => 'required|string', // Validate time as string to ensure proper format
            'unit_*' => 'required|string', // Corrected 'unit_' to 'unit_*' to handle multiple fields
        ]);
        // dd($request);
        try {
            // Prepare date for insertion
            $date = $request->input('selectdatec'); // Format: 31/07/24 Wednesday

            // Process each set of data
            $entries = [];
            foreach ($request->except(['_token', 'selectdatec', 'rounds', 'amrap']) as $key => $value) {
                if (preg_match('/^categoryc_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];

                    // Retrieve checkbox value from the request
                    $amrap = $request->input('amrap', false); // Default to false if checkbox is not present

                    // Convert to boolean if necessary
                    $amrap = filter_var($amrap, FILTER_VALIDATE_BOOLEAN);
                    // dd($amrap);
                    $entries[] = [
                        'rounds' => $request->input('rounds'),
                        'category_id' => $request->input('categoryc_' . $index),
                        'workout_id' => $request->input('workoutc_' . $index),
                        'reps' => $request->input('repsc_' . $index),
                        'weight' => $request->input('weigthc_' . $index),
                        'unit' => $request->input('unit_' . $index),
                        'time_to_complete' => $request->input('timeTC_' . $index),
                        'date' => $date,
                        'amrap' => $amrap, // Include 'amrap' in the data if needed
                    ];
                    if ($amrap == "true") {
                        $amrap = 1;
                    } else if ($amrap == "false") {
                        $amrap = 0;
                    }
                    $this->allUpdate($date, $request->input('rounds'), $amrap);
                }
            }

            // Insert data into the database
            foreach ($entries as $entry) {
                Conditioning::create($entry);
            }

            return response()->json([
                'message' => 'Conditioning record stored successfully'
            ], 201);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json([
                'message' => 'Failed to store conditioning record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // get conditioning
    public function getConditioning(Request $request)
    {
        $date = $request->input('date');

        // Fetch conditioning data with related category and workout details
        $conditionings = Conditioning::where('date', $date)
            ->with(['category', 'workout'])
            ->get();

        // Fetch workouts based on the type and include related category options
        $workouts = WorkoutLibrary::where('type', 'conditioning')
            ->with('categoryOption')
            ->get();

        // Get unique category options based on the fetched workouts
        $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

        // Transform the result to include the desired fields
        $result = $conditionings->map(function ($item) {
            return [
                'id' => $item->id,
                'date' => $item->date,
                'category_id' => $item->category_id,
                'weight' => $item->weight,
                'category_name' => $item->category ? $item->category->category_name : null,
                'workout_id' => $item->workout_id,
                'workout_type' => $item->workout ? $item->workout->workout : null,
                'reps' => $item->reps,
                'rounds' => $item->rounds,
                'complete_time' => $item->time_to_complete,
                'unit' => $item->unit,
                'amrap' => $item->amrap,
            ];
        });

        return response()->json([
            'result' => $result,
            'categoryOptions' => $categoryOptions
        ]);
    }
    public function updateConditioning(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'id' => 'required|integer|exists:conditionings,id',
            'category' => 'required',
            'workout' => 'required',
            'reps' => 'required|integer',
            'weight' => 'required',
            'timeToComplete' => 'required',
            'rounds' => 'nullable|integer',
            'date' => 'required',
            'isChecked' => 'nullable',
            'unit' => 'required',
        ]);
        try {
            // Find the Conditioning record by ID
            $conditioning = Conditioning::find($validated['id']);
            if (!$conditioning) {
                Log::error('Conditioning not found', ['id' => $validated['id']]);
                return response()->json(['error' => 'Conditioning not found'], 404);
            }

            if ($request->isChecked == "true") {
                $amrap = 1;
            } else if ($request->isChecked == "false") {
                $amrap = 0;
            }

            // Log the current state before update
            Log::info('Updating Conditioning record', [
                'id' => $conditioning->id,
                'old_data' => $conditioning->toArray(),
                'new_data' => $validated
            ]);

            // Update the Conditioning record
            $conditioning->rounds = $validated['rounds'];
            $conditioning->category_id  = $validated['category'];
            $conditioning->workout_id  = $validated['workout'];
            $conditioning->reps = $validated['reps'];
            $conditioning->weight = $validated['weight'];
            $conditioning->time_to_complete = $validated['timeToComplete'];
            $conditioning->date = $validated['date'];
            $conditioning->unit = $validated['unit'];
            $conditioning->amrap = $amrap;
            // $conditioning->is_checked = $validated['isChecked'];
            $conditioning->save();

            // Update all records with the same date
            $this->allUpdate($validated['date'], $validated['rounds'], $amrap);

            // Log the successful update
            Log::info('Conditioning record updated successfully', [
                'id' => $conditioning->id,
                'updated_data' => $conditioning->toArray()
            ]);

            // Return a success response
            return response()->json(['message' => 'Conditioning updated successfully'], 200);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            Log::error('Failed to update conditioning record', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Failed to update conditioning record', 'message' => $e->getMessage()], 500);
        }
    }

    public function allUpdate($date, $rounds, $amrap)
    {
        // Update rounds and amrap for all records with the same date
        Conditioning::where('date', $date)->update([
            'rounds' => $rounds,
            'amrap' => $amrap
        ]);

        Log::info('Updated all Conditioning records with date', [
            'date' => $date,
            'rounds' => $rounds,
            'amrap' => $amrap
        ]);
    }
    public function deleteConditioning(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'date' => 'required'
        ]);
        try {
            // Delete records by date
            $deleted = Conditioning::where('date', $validated['date'])->delete();

            // Log the deletion
            Log::info('Deleted Conditioning records', [
                'date' => $validated['date'],
                'deleted_count' => $deleted
            ]);

            // Return a success response
            return response()->json(['message' => 'Conditioning records deleted successfully', 'deleted_count' => $deleted], 200);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            Log::error('Failed to delete conditioning records', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Failed to delete conditioning records', 'message' => $e->getMessage()], 500);
        }
    }
}
