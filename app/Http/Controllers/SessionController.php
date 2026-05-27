<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Access;
use App\Models\Classes;
use App\Models\ClientManagement;

use App\Models\Newprofile;

use App\Models\Conditioning;

use App\Models\Strength;
use App\Models\StrengthSetRep;
use App\Models\Test;
use App\Models\Warmup;
use App\Models\Weightlifting;
use App\Models\WeightliftingSet;
use App\Models\WorkoutAssign;
use App\Models\WorkoutLibrary;
use App\Models\CategoryOption;
use App\Models\PyramidSet;
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
        $classes = Classes::all();

        if ($access && $access->session === 'enable') {
            // Pass the access type to the view using compact
            $accessType = $access->access_type;
            return view('admin.user.session', compact('accessType','classes'));
        } else {
            // Redirect to an unauthorized access view
            return view('error.unauthorized');
        }
    }
    // get category
    public function getCategory(Request $request)
    {
        $tab = strtolower($request->input('tab'));
        Log::info('Tab value: ' . $tab);
        // Fetch workouts based on the type
        if($tab == 'test' || $tab == 'all'){
            $categoryOptions = CategoryOption::all();
        }
        else{
            $workouts = WorkoutLibrary::where('type', $tab)
            ->with('categoryOption') // Load the category options
            ->get();

        // Get unique category options based on the fetched workouts
        $categoryOptions = $workouts->pluck('categoryOption')->unique('id');
        }
        return response()->json([
            'category_options' => $categoryOptions
        ]);
    }

    // ... (skipping store method)

    // ... (skipping getdata method)

    public function getworkout(Request $request)
    {
        $tab = strtolower($request->tab);
        $id = $request->id;
        // category option id andb type filter
        if($tab == 'test'){
            if($id){
                $workouts = WorkoutLibrary::where([
                    ['category_options_id', $id],
                ])->get();
            } else {
                 $workouts = WorkoutLibrary::all();
            }
        } elseif ($tab == 'all') {
            // New logic for 'all': No type filter
            $query = WorkoutLibrary::query();
            if($id){
                $query->where('category_options_id', $id);
            }
             $workouts = $query->get();
        } else{
            $query = WorkoutLibrary::where('type', $tab);
            if($id){
                $query->where('category_options_id', $id);
            }
            $workouts = $query->get();
        }

        return response()->json(['workouts' => $workouts]);
    }

    public function getworkouts(Request $request)
    {
        $workouts = WorkoutLibrary::select('workout')
            ->distinct()
            ->get();

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
        Log::info('Incoming Warmup Request Data: ', $request->all());

        try {
            $request->validate([
                'selectdatew' => 'required|string',
                'namew_1' => 'nullable|string',
                'categoryw_*' => 'required|exists:category_options,id',
                'workoutw_*' => 'required|exists:workout_libraries,id',
            ]);

            $requestData = $request->all();
            $maxIndex = 10; // Adjust based on expected warmup group count

            for ($index = 1; $index <= $maxIndex; $index++) {
                $processedData = [];

                foreach ($requestData as $key => $value) {
                    if (strpos($key, "_{$index}") !== false) {
                        $processedData[$key] = $value;
                    }
                }

                if (!empty($processedData)) {
                    $this->filterdatawarmup($processedData, $index, $request->input('selectdatew'));
                }
            }

            return response()->json(['message' => 'Warmup data stored successfully!'], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while storing warmup data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filterdatawarmup($processedData, $index, $date)
    {
        Log::info("Processing Warmup Group #{$index}:", $processedData);

        $parsedData = [];

        // Map form keys to DB columns
        $fields = [
            'categoryw' => 'category_id',
            'workoutw' => 'workout_id',
            'repsw' => 'reps',
            'weigthc' => 'weight',
            'unit' => 'unit',
            'male' => 'male',
            'female' => 'female',
        ];

        foreach ($fields as $inputField => $dbField) {
            $key = $inputField . "_{$index}";
            if (isset($processedData[$key])) {
                $parsedData[$dbField] = $processedData[$key];
            }
        }

        // Non-indexed (shared) fields
        $parsedData['workoutname'] = request()->input('namew_1') ?? null;
        $parsedData['date'] = $date;

        // Save to DB
        $warmup = Warmup::store($parsedData); // This assumes you have a `store` method on your Warmup model
        Log::info("Warmup entry saved with ID: {$warmup->id}");
    }



    public function updateWarmup(Request $request)
    {

        Log::info('Incoming Warmup Update Request Data: ', $request->all());

        // Validate the common ID
        $validatedData = $request->validate([
            'warmup_id' => 'required|integer|exists:warmups,id',
        ]);
        try {
            // Find the warmup record by ID
            $warmup = Warmup::findOrFail($validatedData['warmup_id']);

            // Update the fields
            $warmup->workoutname = $request->input('namew_1');
            $warmup->category_id = $request->input('categoryw_1');
            $warmup->workout_id = $request->input('workoutw_1');
            $warmup->reps = $request->input('repsw_1');
            $warmup->weight = $request->input('weigthw_1');
            $warmup->date = $request->input('selectdatew');

            $warmup->save();

            return response()->json(['message' => 'Warmup updated successfully!']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating warmup data.',
                'error' => $e->getMessage()
            ], 500);
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
            $data =  [
                'id' => $item->id,
                'date' => $item->date,
                'category_id' => $item->category_id,
                'workoutname' => $item->workoutname,
                'unit' => $item->unit,
                'weight' => $item->weight,
                'male' => $item->male,
                'female' => $item->female,
                'category_name' => $item->category ? $item->category->category_name : null,
                'workout_id' => $item->workout_id,
                'workout_type' => $item->workout ? $item->workout->type : null,
                'reps' => $item->reps,
                'is_assigned' => $item->is_assigned,
                'assigned_class_ids' => WorkoutAssign::where([
                        'workout_id' => $item->id,
                        'workout_type' => 'warmup',
                        'date' => $item->date
                    ])->pluck('class_id')->toArray(),
            ];
            
            if ($item->unit === 'Cal' || $item->unit === 'Kg') {
                $data['weightvalu'] = $item->male ?? $item->female;
            } else {
                $data['weightvalu'] = $item->weight;
            }

            return $data;
        });

        // Fetch classes for the specific date
        $dailyClasses = Classes::where('date', $date)
            ->orderBy('time', 'asc')
            ->get();

        return response()->json(['result' => $result, 'categoryOptions' => $categoryOptions, 'daily_classes' => $dailyClasses]);
    }


    // serach warmup
    public function searchSetWarmup(Request $request)
    {
        try {
            $date = $request->input("date");
            $name = $request->input("name");
            $categoryId = $request->input("category_id");
            $workoutId = $request->input("workout_id");

            // Always start by filtering by date
            $warmupQuery = Warmup::with(['category', 'workout'])
            ->where('date', $date);
            Log::info('Response filtered data Warmup: ', ['Warmup' => $warmupQuery]);

            if ($categoryId && $workoutId && $name) {
                $warmupQuery = $warmupQuery->where('category_id', $categoryId)
                                                    ->where('workout_id', $workoutId)
                                                    ->whereHas('d', function ($query) use ($name) {
                                                    $query->where('workoutname', 'like', '%' . $name . '%');});
            }

            elseif ($name) {
                $warmupQuery = $warmupQuery->whereHas('workout', function ($query) use ($name) {
                    $query->where('workoutname', 'like', '%' . $name . '%');
                });
            }

            elseif ($categoryId) {
                $warmupQuery = $warmupQuery->where('category_id', $categoryId);
            }

            elseif ($workoutId) {
                $warmupQuery = $warmupQuery->where('workout_id', $workoutId);
            }
            $warmupRecords = $warmupQuery->get();
            $workouts = WorkoutLibrary::where('type', 'Warmup')->with('categoryOption')->get();
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $result = $warmupRecords->map(function ($item) {
                $data = [
                    'id' => $item->id,
                    'date' => $item->date,
                    'category_id' => $item->category_id,
                    'workoutname' => $item->workoutname,
                    'unit' => $item->unit,
                    'weight' => $item->weight,
                    'male' => $item->male,
                    'female' => $item->female,
                    'category_name' => $item->category ? $item->category->category_name : null,
                    'workout_id' => $item->workout_id,
                    'workout_type' => $item->workout ? $item->workout->type : null,
                    'reps' => $item->reps,
                    'is_assigned' => $item->is_assigned,
                    'assigned_class_ids' => WorkoutAssign::where([
                        'workout_id' => $item->id,
                        'workout_type' => 'warmup',
                        'date' => $item->date
                    ])->pluck('class_id')->toArray(),
                ];

                if ($item->unit === 'Cal' || $item->unit === 'Kg') {
                    $data['weightvalu'] = $item->male ?? $item->female;
                } else {
                    $data['weightvalu'] = $item->weight;
                }

                return $data;
            });

            // Fetch classes for the specific date
            $dailyClasses = Classes::where('date', $date)
                ->orderBy('time', 'asc')
                ->get();

            return response()->json([
                'warmup' => $result,
                'categoryOptions' => $categoryOptions,
                'daily_classes' => $dailyClasses
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getwarmup: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    //delete selected warmup data
    public function deletewarmup(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:warmups,id',
        ]);

        try {
            $warmup = Warmup::findOrFail($request->id);
            $warmup->delete();

            return response()->json(['status' => 'success', 'message' => 'Warmup record deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Error in deletetwarmup: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete Warmup record.']);
        }
    }
    // Store Weightlifting Start
    //  Store Weightlifting
    public function storeweightlifting(Request $request)
    {
        Log::info('Incoming Weightlifting Request Data: ', $request->all());
        // dd($request);
        // Validate the request data
        $request->validate([
            'selectdatewe' => 'required',
            'namewe_*' => 'string|nullable',
            'categorywe_*' => 'required|integer|exists:category_options,id',
            'workoutwe_*' => 'required|integer|exists:workout_libraries,id',
            'weigthwe_*' => 'required|integer',
            'setswe_*' => 'required|integer',
            'repswe_*' => 'required|integer',
            'setweightwe_*' => 'nullable',
            // Add restwe_* validation
            'restredwe_*' => 'nullable',
            'restyellowwe_*' => 'nullable',
            'restgreenwe_*' => 'nullable',

            'intensitywe_*' => 'string|nullable',

            'alt-namewe_*' => 'string|nullable',
            'alt-categorywe_*' => 'integer|exists:category_options,id',
            'alt-workoutwe_*' => 'integer|exists:workout_libraries,id',
            'alt-weigthwe_*' => 'integer',
            'alt-setswe_*' => 'integer',
            'alt-repswe_*' => 'integer',
            // Add alt-restwe_* validation
            'alt-restredwe_*' => 'nullable',
            'alt-restyellowwe_*' => 'nullable',
            'alt-restgreenwe_*' => 'nullable',
            'alt-intensitywe_*' => 'string',
        ]);
        try {
            $requestData = $request->all();
            $maxIndex = 10; // Maximum index to check, adjust this as needed

            // Loop through each index

                $processedData = []; // Initialize the array for the current index

                // Iterate over all request data
                foreach ($requestData as $key => $value) {
                    // Check if the key contains the current index
                    if (str_ends_with($key, '_1')){
                        // Add the key-value pair to the array
                        $processedData[$key] = $value;
                    }
                }

                if (!empty($processedData)) {
                    // Call the filterdata function to process and store the data
                    // dd($processedData);
                    $this->filterdata($processedData, 1, $request->input('selectdatewe'));
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
        Log::info('processedData Weightlifting Request Data: ', $processedData);
        // Initialize an array to hold the parsed data
        $parsedData = [];
        $setParsedData = [];

        // Extract the indexed values from the input data
        $fields = ['category', 'workout','name', 'weigth','unit', 'restred','restgreen','restyellow', 'intensity', 'alt-category', 'alt-workout', 'alt-name', 'alt-weigth', 'alt-restred','alt-restyellow','alt-restgreen','alt-intensity'];

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
        Log::info('Created Weightlifting ID', ['id' => optional($WeightliftingData)->id]);
        $foreignKey = $WeightliftingData->id;

        // Now handle all set-related fields from entire request (not just _1)
        $allRepsSets = [];
        foreach (request()->all() as $key => $value) {
            if (preg_match('/^(setswe|repswe|setweightwe|alt-setswe|alt-repswe)_(\d+)$/', $key, $matches)) {
                $type = $matches[1];   // e.g., 'setswe', 'repswe'
                $suffix = $matches[2]; // e.g., '1', '12', '13'

                $allRepsSets[$suffix][$type] = $value;
            }
        }

        // Convert to final row format and save
        foreach ($allRepsSets as $suffix => $values) {
            $row = [
                  'sets' => isset($values['setswe']) ? $values['setswe'] : 1,
                'reps' => $values['repswe'] ?? null,
                'weight' => $values['setweightwe'] ?? null, 
                'alt_set' => $values['alt-setswe'] ?? null,
                'alt_reps' => $values['alt-repswe'] ?? null,
                'weightlifting_id' => $foreignKey,
            ];

            Log::info('Saving WeightliftingSet row', $row);
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
                    'workoutname' => $item->workoutname,
                    'workout_id' => $item->workout_id,
                    'workout_type' => $item->workout ? $item->workout->workout : null,

                    'weight' => $item->weight,
                    'unit' => $item->unit,

                    'restwered' => $item->restredwe,
                    'restweyellow' => $item->restyellowwe,
                    'restwegreen' => $item->restgreenwe,

                    'intensity' => $item->intensity,
                    'is_assigned' => $item->is_assigned,

                    'alt_category_id' => $item->alt_category_id,
                    'alt_category_name' => $item->altCategory ? $item->altCategory->category_name : null,
                    'alt_workoutname' => $item->alt_workoutname,
                    'alt_workout_id' => $item->alt_workout_id,
                    'alt_workout_type' => $item->altWorkout ? $item->altWorkout->workout : null,

                    'alt_weight' => $item->alt_weight,

                    'alt_restwered' => $item->altrestredwe,
                    'alt_restweyellow' => $item->altrestyellowwe,
                    'alt_restwegreen' => $item->altrestgreenwe,

                    'alt_intensity' => $item->alt_intensity,

                    'assigned_class_ids' => WorkoutAssign::where([
                        'workout_id' => $item->id,
                        'workout_type' => 'weightlifting',
                        'date' => $item->date
                    ])->pluck('class_id')->toArray(),

                    'date' => $item->date,
                    'sets' => $sets, // Include the sets in the result
                ];
            });

            // Fetch classes for the specific date
            $dailyClasses = Classes::where('date', $date)
                ->orderBy('time', 'asc')
                ->get();

            Log::info('return Weightlifting Request Data: ', $result->all());
            return response()->json([
                'weightlifting' => $result,
                'categoryOptions' => $categoryOptions,
                'daily_classes' => $dailyClasses,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // serach weightlifting
    public function searchSetWeightlifting(Request $request)
    {
        try {
            $date = $request->input("date");
            $name = $request->input("name");
            $categoryId = $request->input("category_id");
            $workoutId = $request->input("workout_id");

            // Always start by filtering by date
            $weightliftingQuery = Weightlifting::with(['category', 'workout', 'sets', 'altCategory', 'altWorkout'])
            ->where('date', $date);
            Log::info('Response filtered data weightlifting: ', ['Weightlifting' => $weightliftingQuery]);
            // Further in-memory filtering
            // if ($name) {
            //     $strengthRecords = $strengthRecords->filter(function ($item) use ($name) {
            //         return stripos($item->workout->workout ?? '', $name) !== false;
            //     });
            // }
            if ($categoryId && $workoutId && $name) {
                $weightliftingQuery = $weightliftingQuery->where('category_id', $categoryId)
                                                    ->where('workout_id', $workoutId)
                                                    ->whereHas('workout', function ($query) use ($name) {
                                                    $query->where('workoutname', 'like', '%' . $name . '%');});
            }

            elseif ($name) {
                $weightliftingQuery = $weightliftingQuery->whereHas('workout', function ($query) use ($name) {
                    $query->where('workoutname', 'like', '%' . $name . '%');
                });
            }

            elseif ($categoryId) {
                $weightliftingQuery = $weightliftingQuery->where('category_id', $categoryId);
            }

            elseif ($workoutId) {
                $weightliftingQuery = $weightliftingQuery->where('workout_id', $workoutId);
            }
            $weightliftingRecords = $weightliftingQuery->get();
            $workouts = WorkoutLibrary::where('type', 'Weightlifting')->with('categoryOption')->get();
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $dailyClasses = Classes::where('date', $date)
                ->orderBy('time', 'asc')
                ->get();

            $result = $weightliftingRecords->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_name' => optional($item->category)->category_name,
                    'workout_id' => $item->workout_id,
                    'workout_type' => optional($item->workout)->workout,
                    'workoutname' => $item->workoutname,
                    'weight' => $item->weight,
                    'restwered' => $item->restredwe,
                    'restweyellow' => $item->restyellowwe,
                    'restwegreen' => $item->restgreenwe,
                    'intensity' => $item->intensity,
                    'is_assigned' => $item->is_assigned,
                    'alt_category_id' => $item->alt_category_id,
                    'alt_category_name' => optional($item->altCategory)->category_name,
                    'alt_workout_id' => $item->alt_workout_id,
                    'alt_workout_type' => optional($item->altWorkout)->workout,
                    'alt_weight' => $item->altweight,
                    'alt_restred' => $item->altrestred,
                    'alt_restyellow' => $item->altrestyellow,
                    'alt_restgreen' => $item->altrestgreen,
                    'alt_intensity' => $item->altintensity,

                    'assigned_class_ids' => WorkoutAssign::where([
                        'workout_id' => $item->id,
                        'workout_type' => 'weightlifting',
                        'date' => $item->date
                    ])->pluck('class_id')->toArray(),

                    'date' => $item->date,
                    'sets' => $item->sets,
                ];/* dd($result); */
            });

            return response()->json([
                'weightlifting' => $result,
                'categoryOptions' => $categoryOptions,
                'daily_classes' => $dailyClasses,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getweightlifting: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    // update weightlifting
    public function updateWeightlifting(Request $request)
    {
        Log::info('Weight Request Data dd:', ['request' => $request->all()]);
        try {
            // Validate the incoming request data
            $request->validate([
                'id_*' => 'required|integer|exists:weightliftings,id',
                'categoryweight_*' => 'required|integer|exists:category_options,id',
                'workoutweight_*' => 'required|integer|exists:workout_libraries,id',
                'nameweight_*' => 'string',
                'weigthweight_*' => 'required|integer',
                'setsweight_*' => 'required|integer',
                'repsweight_*' => 'required|integer',
                'restredweight_*' => 'required|date_format:H:i:s',
                'restyellowweight_*' => 'required|date_format:H:i:s',
                'restgreenweight_*' => 'required|date_format:H:i:s',
                'intensityweight_*' => 'required|string',
                'altcategoryweight_*' => 'required|integer|exists:category_options,id',
                'altworkoutweight_*' => 'required|integer|exists:workout_libraries,id',
                'altnameweight_*' => 'string',
                'altweigthweight_*' => 'required|integer',
                'altsetsweight_*' => 'required|integer',
                'altrepsweight_*' => 'required|integer',
                'altrestredweight_*' => 'required|date_format:H:i:s',
                'altrestyellowweight_*' => 'required|date_format:H:i:s',
                'altrestgreenweight_*' => 'required|date_format:H:i:s',
                'altintensityweight_*' => 'required|string',
            ]);

            // Dynamically find the ID from the request
            $weightliftingId = null;
            $weightliftingId = $request->input('weightlifting_id');

            // Check if ID was found
            if (!$weightliftingId) {
                return response()->json(['message' => 'ID not found'], 400);
            }

            // Retrieve the existing weightlifting record by ID
            $weightlifting = Weightlifting::findOrFail($weightliftingId);

            // Update the weightlifting record with new data
            $weightlifting->update([
                'category_id' => $request->input('categorywe_1'),
                'workout_id' => $request->input('workoutwe_1'),
                'workoutname' => $request->input('namewe_1'),
                'weight' => $request->input('weigthwe_1'),
                'restredwe' => $request->input('restredwe_1'),
                'restyellowwe' => $request->input('restyellowwe_1'),
                'restgreenwe' => $request->input('restgreenwe_1'),
                'intensity' => $request->input('intensitywe_1'),
                'alt_category_id' => $request->input('altcategoryweight_' . $weightliftingId),
                'alt_workout_id' => $request->input('altworkoutweight_' . $weightliftingId),
                'alt_workoutname' => $request->input('altnameweight_' . $weightliftingId),
                'alt_weight' => $request->input('altweigthweight_' . $weightliftingId),
                'altrestredwe' => $request->input('altrestredweight_' . $weightliftingId) ?? '00:00:00',
                'altrestyellowwe' => $request->input('altrestyellowweight_' . $weightliftingId) ?? '00:00:00',
                'altrestgreenwe' => $request->input('altrestgreenweight_' . $weightliftingId) ?? '00:00:00',
                'alt_intensity' => $request->input('altintensityweight_' . $weightliftingId),
            ]);

            // Update existing weightlifting sets

            foreach ($request->all() as $key => $value) {
                if (preg_match('/^setwid_(\d+)$/', $key, $matches)) {
                    $index = $matches[1];

                    $setId = $request->input("setwid_$index");
                    $sets = $request->input("setswe_$index");
                    $reps = $request->input("repswe_$index");

                    if ($setId && $sets !== null && $reps !== null) {
                        $set = WeightliftingSet::find($setId);
                        if ($set) {
                            $set->update([
                                'sets' => $sets,
                                'reps' => $reps,
                            ]);
                        }
                    }
                }
            }


            // Create new weightlifting sets
            foreach ($request->all() as $key => $value) {
                if (preg_match('/^setswe_' . $weightliftingId . '(\d+)$/', $key, $matches)) {
                    $index = $matches[1];

                    $sets = $request->input("setswe_{$weightliftingId}{$index}");
                    $reps = $request->input("repswe_{$weightliftingId}{$index}");
                    $alt_sets = $request->input("alt-setswe_{$weightliftingId}{$index}");
                    $alt_reps = $request->input("alt-repswe_{$weightliftingId}{$index}");

                    // Only save if at least sets or reps is provided
                    if ($sets !== null || $reps !== null) {
                        WeightliftingSet::create([
                            'sets' => $sets,
                            'reps' => $reps,
                            'alt_sets' => $alt_sets,
                            'alt_reps' => $alt_reps,
                            'weightlifting_id' => $weightlifting->id,
                        ]);
                    }
                }
            }

            // dd("Weightlifting data updated successfully");
            return response()->json(['message' => 'Weightlifting data updated successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle other exceptions
            return response()->json([
                'message' => 'An error occurred while updating the weightlifting data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //delete selected weightlifting data
    public function deleteweightlifting(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:weightliftings,id',
        ]);

        try {
            $weightlifting = Weightlifting::findOrFail($request->id);
            $weightlifting->delete();

            return response()->json(['status' => 'success', 'message' => 'Weightlifting record deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete Weightlifting record.']);
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

    // assign strength to class
    public function assignweightlifting(Request $request)
    {
        Log::info('assigned weightlifting: ', $request->all());

        $request->validate([
            'workout_id' => 'required|integer',
            'workout_type' => 'required|string|in:strength,weightlifting,warmup,conditioning,test',
            'assigned' => 'required|boolean',
            'date' => 'required|string',
        ]);

        $workoutId = $request->workout_id;
        $workoutType = $request->workout_type;
        $assigned = $request->assigned;
        $date = $request->date;
        $classId = $request->class_id;

        // If unassigning and no class_id is provided, find it from the pivot table
        if (!$assigned && !$classId) {
            $existing = WorkoutAssign::where([
                'workout_id' => $workoutId,
                'workout_type' => $workoutType,
                'date' => $date,
            ])->first();

            if ($existing) {
                $classId = $existing->class_id;
            }
        }

        // Ensure we have a class ID
        if (!$classId) {
            return response()->json(['error' => 'Class ID not found for unassigning.'], 422);
        }

        // Step 1: Assign or unassign in pivot table
        if ($assigned) {
            WorkoutAssign::updateOrCreate(
                [
                    'class_id' => $classId,
                    'workout_id' => $workoutId,
                    'workout_type' => $workoutType,
                    'date' => $date,
                ]
            );
            $message = 'Workout assigned to class successfully.';
        } else {
            WorkoutAssign::where([
                'class_id' => $classId,
                'workout_id' => $workoutId,
                'workout_type' => $workoutType,
                'date' => $date,
            ])->delete();
            $message = 'Workout unassigned from class successfully.';
        }

        // Step 2: Update class table is_* flag
        $class = Classes::find($classId);
        if ($class) {
            switch ($workoutType) {
                case 'strength':
                    $class->is_strength = $assigned;
                    break;
                case 'weightlifting':
                    $class->is_weightlifting = $assigned;
                    break;
                case 'warmup':
                    $class->is_warmup = $assigned;
                    break;
                case 'conditioning':
                    $class->is_conditioning = $assigned;
                    break;
                case 'accessory':
                    $class->is_accessory = $assigned;
                    break;
                case 'accessory':
                    $class->is_accessory = $assigned;
                    break;
                case '1rm':
                    $class->is_1rm = $assigned;
                    break;
            }
            $class->save();
        }

        // Step 3: Update is_assigned in the workout's actual table
        switch ($workoutType) {
            case 'strength':
                Strength::where('id', $workoutId)->update(['is_assigned' => $assigned]);
                break;
            case 'weightlifting':
                Weightlifting::where('id', $workoutId)->update(['is_assigned' => $assigned]);
                break;
            case 'warmup':
                Warmup::where('id', $workoutId)->update(['is_assigned' => $assigned]);
                break;
            case 'conditioning':
                Conditioning::where('id', $workoutId)->update(['is_assigned' => $assigned]);
                break;
            case 'accessory':
                Test::where('id', $workoutId)->update(['is_assigned' => $assigned]);
                break;
        }

        return response()->json(['message' => $message], 200);
    }

    // Assign generic workout to class (Strength, etc)
    public function assignWorkoutToClass(Request $request)
    {
        Log::info('assignWorkoutToClass: ', $request->all());

        $request->validate([
            'workout_id' => 'required|integer',
            'class_id' => 'required', // Removed 'integer' to allow 'all' flag
            'type' => 'required|string', // 'strength', 'warmup', etc.
            'action' => 'required|string|in:assign,unassign,detach,assign_all',
            'date' => 'required|string',
        ]);

        $workoutId = $request->workout_id;
        $classId = $request->class_id;
        $type = $request->type; // incoming might be 'strength' or 'workout_manager'
        $action = $request->action;
        $date = $request->date;

        // Resolve "Real" Type for storage
        $storedType = $type;
        if ($type === 'workout_manager') {
            $wm = \App\Models\WorkoutManager::find($workoutId);
            if ($wm && $wm->type) {
                $storedType = strtolower($wm->type->name);
            }
        }

        if ($action === 'assign_all') {
            // Fetch all classes for this date
            $classes = Classes::where('date', $date)->get();
            
            foreach ($classes as $cls) {
                WorkoutAssign::firstOrCreate([
                    'class_id' => $cls->id,
                    'workout_id' => $workoutId,
                    'workout_type' => $storedType, // Use resolved type
                    'date' => $date,
                ]);
                Log::info('Workout assigned successfully.', ['type' => $type]);
                // Update Class Flag based on type
                switch ($type) {
                    case 'strength': $cls->is_strength = 1; break;
                    case 'weightlifting': $cls->is_weightlifting = 1; break;
                    case 'warmup': $cls->is_warmup = 1; break;
                    case 'conditioning': $cls->is_conditioning = 1; break;
                    case 'accessory': 
                        if (\Illuminate\Support\Facades\Schema::hasColumn('classes', 'is_accessory')) {
                            $cls->is_accessory = 1;
                        }
                        break;
                    case '1rm': 
                    case "pr's":
                    case "PR's": 
                        if (\Illuminate\Support\Facades\Schema::hasColumn('classes', 'is_1rm')) {
                            $cls->is_1rm = 1;
                        }
                        break;
                    case 'workout_manager':
                         // Resolve the actual type from WorkoutManager
                         $wm = \App\Models\WorkoutManager::find($workoutId);
                         if ($wm && $wm->type) {
                             $wmType = strtolower($wm->type->name); // e.g., 'strength', 'conditioning'
                             if ($wmType == 'strength') $cls->is_strength = 1;
                             elseif ($wmType == 'conditioning') $cls->is_conditioning = 1;
                             elseif ($wmType == 'weightlifting') $cls->is_weightlifting = 1;
                             elseif ($wmType == 'warmup') $cls->is_warmup = 1;
                             elseif ($wmType == 'accessory') {
                                 if (\Illuminate\Support\Facades\Schema::hasColumn('classes', 'is_accessory')) {
                                     $cls->is_accessory = 1;
                                 }
                             }
                             elseif ($wmType == '1rm' || $wmType == "pr's" || $wmType == "PR's") {
                                 if (\Illuminate\Support\Facades\Schema::hasColumn('classes', 'is_1rm')) {
                                     $cls->is_1rm = 1;
                                 }
                             }
                         }
                         break;
                }
                $cls->save();
            }
            
            // Update Workout Flag (Legacy only)
             if ($type !== 'workout_manager') {
                 switch ($type) {
                    case 'strength':
                        Strength::where('id', $workoutId)->update(['is_assigned' => 1]);
                        break;
                    case 'warmup':
                        Warmup::where('id', $workoutId)->update(['is_assigned' => 1]);
                        break;
                     // Add others if needed
                }
             }

            return response()->json(['message' => 'Assignments updated for all classes.', 'status' => 'success']);
        }

        if ($action === 'assign') {
            WorkoutAssign::firstOrCreate([
                'class_id' => $classId,
                'workout_id' => $workoutId,
                'workout_type' => $storedType, // Use resolved type
                'date' => $date,
            ]);
            $message = 'Workout assigned successfully.';
        } else {
            if ($classId === 'all') {
                 WorkoutAssign::where([
                    'workout_id' => $workoutId,
                    'workout_type' => $storedType, // Use resolved type
                    'date' => $date
                ])->delete();
                $message = 'Workout unassigned from all classes.';
            } else {
                WorkoutAssign::where([
                    'class_id' => $classId,
                    'workout_id' => $workoutId,
                    'workout_type' => $storedType, // Use resolved type
                ])->delete();
                $message = 'Workout unassigned successfully.';
            }
        }



        // B) WorkoutManager assignment where the manager item resolves to 'strength'
        
        $checkTypeExists = function($classId, $realType) {
            if (WorkoutAssign::where('class_id', $classId)->where('workout_type', $realType)->exists()) {
                return true;
            }
            
            // 2. Check WorkoutManager items
            $wmAssignments = WorkoutAssign::where('class_id', $classId)
                             ->where('workout_type', $realType)
                             ->get();
                             
            foreach($wmAssignments as $assign) {
                // If the ID exists in WorkoutManager, it's a match.
                if (\App\Models\WorkoutManager::where('id', $assign->workout_id)->exists()) {
                     return true;
                }
            }
            
            return false;
        };

        
        $classesToUpdate = [];
        if ($classId === 'all') {
             $classesToUpdate = Classes::where('date', $date)->get();
        } else {
             $c = Classes::find($classId);
             if ($c) $classesToUpdate[] = $c;
        }

        foreach ($classesToUpdate as $class) {
             // We need to re-evaluate ALL flags for this class because we don't know exactly what was removed effectively 
             
             $typesToCheck = [];
             $typesToCheck[] = $storedType; 
             
             foreach ($typesToCheck as $t) {
                 $exists = $checkTypeExists($class->id, $t);
                 switch ($t) {
                     case 'strength': $class->is_strength = $exists ? 1 : 0; break;
                     case 'weightlifting': $class->is_weightlifting = $exists ? 1 : 0; break;
                     case 'warmup': $class->is_warmup = $exists ? 1 : 0; break;
                     case 'conditioning': $class->is_conditioning = $exists ? 1 : 0; break;
                     case 'accessory': 
                         if (\Illuminate\Support\Facades\Schema::hasColumn('classes', 'is_accessory')) {
                             $class->is_accessory = $exists ? 1 : 0;
                         }
                         break;
                     case '1rm': 
                     case "pr's":
                     case "PR's":
                         if (\Illuminate\Support\Facades\Schema::hasColumn('classes', 'is_1rm')) {
                             $class->is_1rm = $exists ? 1 : 0;
                         }
                         break;
                 }
             }
             $class->save();
        }
        
        // 2. Update Workout 'is_assigned' flag (Legacy ONLY)
        // WorkoutManager items do not have an 'is_assigned' column.
        if ($type !== 'workout_manager') {
            $isAssignedAny = WorkoutAssign::where([
                'workout_id' => $workoutId,
                'workout_type' => $storedType
            ])->exists();

            switch ($storedType) {
                case 'strength':
                    Strength::where('id', $workoutId)->update(['is_assigned' => $isAssignedAny ? 1 : 0]);
                    break;
                case 'warmup':
                    Warmup::where('id', $workoutId)->update(['is_assigned' => $isAssignedAny ? 1 : 0]);
                    break;
                case 'weightlifting':
                    Weightlifting::where('id', $workoutId)->update(['is_assigned' => $isAssignedAny ? 1 : 0]);
                    break;
                case 'conditioning':
                    Conditioning::where('id', $workoutId)->update(['is_assigned' => $isAssignedAny ? 1 : 0]);
                    break;
                case 'accessory':
                    Test::where('id', $workoutId)->update(['is_assigned' => $isAssignedAny ? 1 : 0]);
                    break;
            }
        }

        return response()->json(['message' => $message, 'status' => 'success']);
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
            'name_*' => 'required',
            'weight_*' => 'required',
            'restred_*' => 'nullable',
            'restyellow_*'=>'nullable',
            'restgreen_*'=>'nullable',
            'intensity_*' => 'nullable',
            'alt_category_*' => 'nullable|exists:category_options,id',
            'alt_workout_*' => 'nullable|exists:workout_libraries,id',
            'alt_weight_*' => 'nullable',
            'alt_restred_*' => 'nullable',
            'alt_restyellow_*'=>'nullable',
            'alt_restgreen_*'=>'nullable',
            'alt_intensity_*' => 'nullable',
            'selectdate_*' => 'required',
            'sets_*' => 'required',
            'reps_*' => 'required',
            'setweight_*' => 'nullable',
            'alt_sets_*' => 'nullable',
            'alt_reps_*' => 'nullable',
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
        if ($request->ajax()) {
            return response()->json(['message' => 'Strength record stored successfully!']);
        }
        return redirect()->back()->with('success', 'Strength record stored successfully!');

    }
    // strenghtfillter
    public function filterdatastrength($processedData, $index, $date)
    {
        Log::info('processedData strenght Request Data: ', $processedData);

        $parsedData = [];
        $setParsedData = [];
        // Extract the indexed values from the input data
        $fields = ['categorys', 'workouts', 'names', 'weigths','unit',  'restreds','restyellows','restgreens', 'intensitys', 'alt-categorys', 'alt-workouts', 'alt-weigths', 'alt-restreds','alt-restyellows','alt-restgreens','alt-intensitys'];

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
        Log::info('Created Strength ID', ['id' => optional($strengthData)->id]);

        $foreignKey = $strengthData->id;
        // Now handle all set-related fields from entire request (not just _1)
        $allRepsSets = [];
        foreach (request()->all() as $key => $value) {
            if (preg_match('/^(sets|reps|setweight|alt-sets|alt-reps)_(\d+)$/', $key, $matches)) {
                $type = $matches[1];   // e.g., 'setswe', 'repswe'
                $suffix = $matches[2]; // e.g., '1', '12', '13'

                $allRepsSets[$suffix][$type] = $value;
            }
        }

        // Convert to final row format and save
        foreach ($allRepsSets as $suffix => $values) {
            $row = [
                'sets' => isset($values['sets']) ? $values['sets'] : 1,
                'reps' => $values['reps'] ?? null,
                'weight' => $values['setweight'] ?? null, // <--- ADD THIS
                'alt_set' => $values['alt-sets'] ?? null,
                'alt_reps' => $values['alt-reps'] ?? null,
                'strength_id' => $foreignKey,
            ];

            Log::info('Saving StrengthSetRep row', $row);
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
                Log::info('Response data strength: ', ['Strength' => $strengthRecords]);
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
                    'workoutname' => $item->workoutname,
                    'weight' => $item->weight,
                    'unit' => $item->unit,
                    'restred' => $item->restred,
                    'restyellow'=>$item->restyellow,
                    'restgreen'=>$item->restgreen,
                    'intensity' => $item->intensity,
                    'is_assigned' => $item->is_assigned,

                    'alt_category_id' => $item->alt_category_id,
                    'alt_category_name' => $item->altCategory ? $item->altCategory->category_name : null,


                    'alt_workout_id' => $item->alt_workout_id,
                    'alt_workout_type' => $item->altWorkout ? $item->altWorkout->workout : null,

                    'assigned_class_ids' => WorkoutAssign::where([
                        'workout_id' => $item->id,
                        'workout_type' => 'strength',
                        'date' => $item->date
                    ])->pluck('class_id')->toArray(),

                    'alt_weight' => $item->altweight,
                    'alt_restred' => $item->altrestred,
                    'alt_restyellow'=>$item->altrestyellow,
                    'alt_restgreen'=>$item->altrestgreen,
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

    // serach strength
    public function searchSetStrength(Request $request)
    {
            Log::info('return Strength Request Data: ', $request->all());

        try {
            $date = $request->input("date");
            $name = $request->input("name");
            $categoryId = $request->input("category_id");
            $workoutId = $request->input("workout_id");

            // Always start by filtering by date
            $strengthQuery = Strength::with(['category', 'workout', 'setstrengthsetsreps', 'altCategory', 'altWorkout'])
            ->where('date', $date);
            Log::info('Response filtered data strength: ', ['Strength' => $strengthQuery]);
            // Further in-memory filtering
            // if ($name) {
            //     $strengthRecords = $strengthRecords->filter(function ($item) use ($name) {
            //         return stripos($item->workout->workout ?? '', $name) !== false;
            //     });
            // }
            if ($categoryId && $workoutId && $name) {
                $strengthQuery = $strengthQuery->where('category_id', $categoryId)
                                                    ->where('workout_id', $workoutId)
                                                    ->whereHas('workout', function ($query) use ($name) {$query->where('workoutname', 'like', '%' . $name . '%');});
            }
            if ($categoryId && $workoutId) {
                $strengthQuery = $strengthQuery->where('category_id', $categoryId)
                                                    ->where('workout_id', $workoutId);
            }
            if ($name) {
                $strengthQuery->whereHas('workout', function ($query) use ($name) {
                    $query->where('workoutname', 'like', '%' . $name . '%');
                });
            }

            if ($categoryId) {
                $strengthQuery = $strengthQuery->where('category_id', $categoryId);
            }

            if ($workoutId) {
                $strengthQuery = $strengthQuery->where('workout_id', $workoutId);
            }

            $strengthRecords = $strengthQuery->get();

            $workouts = WorkoutLibrary::where('type', 'Strength')->with('categoryOption')->get();
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $result = $strengthRecords->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_name' => optional($item->category)->category_name,
                    'workout_id' => $item->workout_id,
                    'workoutname' => $item->workoutname,
                    'workout_type' => optional($item->workout)->workout,
                    'weight' => $item->weight,
                    'restred' => $item->restred,
                    'restyellow' => $item->restyellow,
                    'restgreen' => $item->restgreen,
                    'intensity' => $item->intensity,
                    'is_assigned' => $item->is_assigned,
                    'alt_category_id' => $item->alt_category_id,
                    'alt_category_name' => optional($item->altCategory)->category_name,
                    'alt_workout_id' => $item->alt_workout_id,
                    'alt_workout_type' => optional($item->altWorkout)->workout,
                    'alt_weight' => $item->altweight,
                    'alt_restred' => $item->altrestred,
                    'alt_restyellow' => $item->altrestyellow,
                    'alt_restgreen' => $item->altrestgreen,
                    'alt_intensity' => $item->altintensity,

                    'date' => $item->date,
                    'sets' => $item->setstrengthsetsreps,
                ];
            });

            return response()->json([
                'Strength' => $result,
                'categoryOptions' => $categoryOptions,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getstrength: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }





        // $date = $request->input("date");
        // $query = Strength::query();

        // // if ($request->filled('name')) {
        // //     $query->where('name', 'like', '%' . $request->name . '%');
        // // }

        // if ($request->filled('category_id')) {
        //     $query->where('category_id', $request->category_id);
        // }

        // if ($request->filled('workout_id')) {
        //     $query->where('workout_id', $request->workout_id);
        // }

        // $setStrengths = $query->get();

        // $html = view('partials.setstrength-cards', compact('setStrengths'))->render();

        // return response()->json(['html' => $html]);
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
                'restredstrength_*' => 'required|date_format:H:i:s',
                'restyellowstrength_*' => 'required|date_format:H:i:s',
                'restgreenstrength_*' => 'required|date_format:H:i:s',

                'intensitystrength_*' => 'required|string',

                'altcategorystrength_*' => 'required|integer|exists:category_options,id',
                'altworkoutstrengths_*' => 'required|integer|exists:workout_libraries,id',
                'altweigthstrength_*' => 'required|integer',
                'altsetsstrength_*' => 'required|integer',
                'altrepssstrength_*' => 'required|integer',
                'altrestredstrength_*' => 'required|date_format:H:i:s',
                'altrestyellowstrength_*' => 'required|date_format:H:i:s',
                'altrestgreenstrength_*' => 'required|date_format:H:i:s',
                'altintensitystrength_*' => 'required|string',

            ]);
            // Dynamically find the ID from the request
            $strengthId = null;
            $strengthId = $request->input('strength_id');

            // Check if ID was found
            if (!$strengthId) {
                return response()->json(['message' => 'ID not found'], 400);
            }
            $strength = Strength::findOrFail($strengthId);

            // Update the weightlifting record with new data
            //  dd($request);
            //  dd($request->input('altworkoutstrengths_' . $strengthId));
            $strength->update([
                'category_id' => $request->input('categorys_1'),
                'workout_id' => $request->input('workouts_1'),
                'weight' => $request->input('weigths_1'),
                'restred' => $request->input('restreds_1'),
                'restyellow' => $request->input('restyellows_1'),
                'restgreen' => $request->input('restgreens_1'),

                'intensity' => $request->input('intensitys_1'),
                'alt_category_id' => $request->input('altcategorystrength_1'),
                'alt_workout_id' => $request->input('altworkoutstrengths_1'),
                'altweight' => $request->input('altweigthstrength_1'),
                'altrestred' => $request->input('altrestredstrength_1') ?? '00:00:00',
                'altrestyellow' => $request->input('altrestyellowstrength_1') ?? '00:00:00',
                'altrestgreen' => $request->input('altrestgreenstrength_1') ?? '00:00:00',
                'altintensity' => $request->input('altintensitystrength_1'),
            ]);
            // dd($strength);
            // Update existing StrengthSetRep records
foreach ($request->all() as $key => $value) {
    if (preg_match('/^setsid_(\d+)$/', $key, $matches)) {
        $suffix = $matches[1]; // e.g., 12
        $setId = $value; // e.g., 55

        $setsKey = "sets_$suffix";
        $repsKey = "reps_$suffix";

        $sets = $request->input($setsKey);
        $reps = $request->input($repsKey);

        if ($sets !== null && $reps !== null) {
            $set = StrengthSetRep::find($setId);
            if ($set) {
                $set->update([
                    'sets' => $sets,
                    'reps' => $reps,
                    'strength_id' => $strengthId,
                ]);

                Log::info("Updated Set ID $setId: sets=$sets, reps=$reps");
            } else {
                Log::warning("Set with ID $setId not found for update.");
            }
        } else {
            Log::warning("No sets/reps found for suffix $suffix (keys: $setsKey, $repsKey)");
        }
    }
}

            foreach ($request->all() as $key => $value) {
    if (preg_match('/^sets_(\d+)$/', $key, $matches)) {
        $index = $matches[1];

        // Check if there's a setsid for this index, skip if yes (already updated)
        if ($request->has("setsid_$index")) {
            continue;
        }

        $sets = $request->input("sets_$index");
        $reps = $request->input("reps_$index");

        if (!empty($sets) && !empty($reps)) {
            StrengthSetRep::create([
                'sets' => $sets,
                'reps' => $reps,
                'strength_id' => $strengthId,
            ]);
            Log::info("New main set created: sets_$index => $sets, reps_$index => $reps");
        }
    }

}


            return response()->json(['message' => 'update suceess']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions

            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle other exceptions

            return response()->json([
                'message' => 'An error occurred while updating the weightlifting data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //delete selected strength data
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:strengths,id',
        ]);

        try {
            $strength = Strength::findOrFail($request->id);
            $strength->delete();

            return response()->json(['status' => 'success', 'message' => 'Strength record deleted.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete strength record.']);
        }
    }

    //delete selected test data
    public function deletetests(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tests,id',
        ]);

        try {
            $test = Test::findOrFail($request->id);
            $test->delete();

            return response()->json(['status' => 'success', 'message' => 'Testrecord deleted.']);
        } catch (\Exception $e) {
            Log::error('Error in deletettest: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete Test record.']);
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
        Log::info('Test Request Data dd:', ['request' => $request->all()]);
        try {
            //  dd($request);
            $request->validate([
                'namet_*' => 'required|string',
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
            return response()->json(['message' => 'Test data saved successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while storing warmup data.',
                'error' => $e->getMessage()
            ], 500);
        }

    }
    public function filterdatastest($processedData, $index, $date)

    {
        $parsedData = [];
        // Extract the indexed values from the input data
        $fields = ['namet','test-category', 'test-workout', 'test-member'];
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
                    'is_assigned' => $item->is_assigned,
                    'workout_type' => $item->workout ? $item->workout->workout : null,
                    'member_id' => $item->member_id,
                    'member_name' => $item->member ? $item->member->firstname : null,
                    'workoutname' => $item->workoutname
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
    // serach warmup
    public function searchTest(Request $request)
    {
        try {
            $date = $request->input("date");
            $name = $request->input("name");
            $categoryId = $request->input("category_id");
            $workoutId = $request->input("workout_id");

            // Always start by filtering by date
            $testQuery = Test::with(['category', 'workout'])
            ->where('date', $date);
            Log::info('Response filtered data Test: ', ['Test' => $testQuery]);

            if ($categoryId && $workoutId && $name) {
                $testQuery = $testQuery->where('category_id', $categoryId)
                                                    ->where('workout_id', $workoutId)
                                                    ->whereHas('d', function ($query) use ($name) {
                                                    $query->where('workoutname', 'like', '%' . $name . '%');});
            }

            elseif ($name) {
                $testQuery = $testQuery->whereHas('workout', function ($query) use ($name) {
                    $query->where('workoutname', 'like', '%' . $name . '%');
                });
            }

            elseif ($categoryId) {
                $testQuery = $testQuery->where('category_id', $categoryId);
            }

            elseif ($workoutId) {
                $testQuery = $testQuery->where('workout_id', $workoutId);
            }
            $TestRecords = $testQuery->get();
            $workouts = WorkoutLibrary::where('type', 'Test')->with('categoryOption')->get();
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $result = $TestRecords->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->category_id,
                    'category_name' => $item->category ? $item->category->category_name : null,
                    'workout_id' => $item->workout_id,
                    'is_assigned' => $item->is_assigned,
                    'workout_type' => $item->workout ? $item->workout->workout : null,
                    'member_id' => $item->member_id,
                    'member_name' => $item->member ? $item->member->firstname : null,
                    'workoutname' => $item->workoutname
                ];
            });

            return response()->json([
                'test' => $result,
                'categoryOptions' => $categoryOptions,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in gettest: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function updatest(Request $request)
    {
        // dd($request);
        try {
            $request->validate([
                'test_id' => 'required|integer|exists:weightliftings,id',
            ]);

            $test = Test::findOrFail($request->input('test_id'));
            $test->update([
                'workoutname' => $request->input('namet_1'),
                'category_id' => $request->input('test-category_1'),
                'workout_id' => $request->input('test-workout_1'),
                'member_id' => $request->input('test-member_1')

            ]);


            return response()->json(['message' => 'Test updated suceess']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating test data.',
                'error' => $e->getMessage()
            ], 500);
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
        Log::info('Incoming Conditioning Request Data: ', $request->all());

        try {
            $request->validate([
                'selectdatec' => 'required|string',
                'namec_1' => 'nullable|string',
                'timeTC_1' => 'required|string',
                'categoryc_*' => 'required|exists:category_options,id',
                'workoutc_*' => 'required|exists:workout_libraries,id',
            ]);

            $requestData = $request->all();
            $maxIndex = 10; // Limit for indexed entries

            // Extract pyramid set once globally
            $pyramidRow = $this->extractPyramidSetFromRequest($request);

            for ($index = 1; $index <= $maxIndex; $index++) {
                $processedData = [];

                foreach ($requestData as $key => $value) {
                    if (strpos($key, "_{$index}") !== false) {
                        $processedData[$key] = $value;
                    }
                }

                if (!empty($processedData)) {
                    $this->filterdataconditioning($processedData, $index, $request->input('selectdatec'), $request->input('rounds'), $pyramidRow);
                }
            }

            return response()->json(['message' => 'Conditioning data stored successfully!'], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while storing conditioning data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Extract pyramid values once
    private function extractPyramidSetFromRequest(Request $request)
    {
        return [
            'sets' => $request->input('sets_1'),
            'reps' => $request->input('reps_1'),
            'unit' => $request->input('unit_1'),
            'pyramidweight' => $request->input('weigthPy_1'),
            'pyramidmale' => $request->input('pyramidmale_1'),
            'pyramidfemale' => $request->input('pyramidfemale_1')
        ];
    }

    public function filterdataconditioning($processedData, $index, $date, $rounds, $pyramidRow = null)
    {
        Log::info('Processed Conditioning Data:', $processedData);

        $parsedData = [];
        $fields = [
            'categoryc' => 'category_id',
            'workoutc' => 'workout_id',
            'repsc' => 'reps',
            'weigthc' => 'weight',
            'unit' => 'unit',
            'male' => 'male',
            'female' => 'female',
        ];

        foreach ($fields as $inputField => $dbField) {
            $key = $inputField . "_{$index}";
            if (isset($processedData[$key])) {
                $parsedData[$dbField] = $processedData[$key];
            }
        }

        // Non-indexed (shared) fields from full request
        $parsedData['workoutname'] = request()->input('namec_1') ?? null;
        $parsedData['rounds'] = $rounds;
        $parsedData['time_to_complete'] = request()->input('timeTC_1') ?? null;
        $parsedData['amrap'] = request()->input('amrap') ?? null;
        $parsedData['Pyramid'] = request()->has('pyramidCheckboxCon') ? true : false;
        $parsedData['date'] = $date;

        $conditioning = Conditioning::store($parsedData);
        Log::info('Created Conditioning ID', ['id' => $conditioning->id]);

        // Save pyramid only if pyramid checkbox is checked
        if ($parsedData['Pyramid'] && $pyramidRow) {
            $pyramidRowToInsert = $pyramidRow;
            $pyramidRowToInsert['conditioning_id'] = $conditioning->id;
            Log::info('Saving PyramidSet row:', $pyramidRowToInsert);
            PyramidSet::store($pyramidRowToInsert);
        }
    }


    // get conditioning
    public function getConditioning(Request $request)
    {
       $date = $request->input('date');

        // Validate the date input
        if (!$date) {
            return response()->json(['message' => 'Date is required'], 400);
        }

        // Fetch conditionings for the specific date with relationships
        $conditionings = Conditioning::where('date', $date)
            ->with(['category', 'workout'])
            ->get();

        $result = $conditionings->map(function ($item) {
            $data = [
                'id' => $item->id,
                'date' => $item->date,
                'workoutname' => $item->workoutname,
                'category_id' => $item->category_id,
                'category_name' => $item->category?->category_name,
                'workout_id' => $item->workout_id,
                'workout_type' => $item->workout?->workout,
                'reps' => $item->reps,
                'weight' => $item->weight,
                'unit' => $item->unit,
                'time_to_complete' => $item->time_to_complete,
                'intensity' => $item->intensity,
                'rounds' => $item->rounds,
                'amrap' => $item->amrap,
                'is_assigned' => $item->is_assigned,
                'assigned_class_ids' => WorkoutAssign::where([
                    'workout_id' => $item->id,
                    'workout_type' => 'conditioning',
                    'date' => $item->date
                ])->pluck('class_id')->toArray(),
            ];

            // Only include 'rounds' or 'amrap'
            if (!is_null($item->rounds)) {
                $data['roundsnamrap'] = $item->rounds;
            } elseif ($item->amrap == 1) {
                $data['roundsnamrap'] = $item->amrap;
            }

            return $data;
        });

        return response()->json([
            'result' => $result
        ]);
    }

    // serach conditioning
    public function searchConditioning(Request $request)
    {
        try {
            $date = $request->input("date");
            $name = $request->input("name");
            $categoryId = $request->input("category_id");
            $workoutId = $request->input("workout_id");

            // Always start by filtering by date
            $conditioningQuery = Conditioning::with(['category', 'workout'])
            ->where('date', $date);
            Log::info('Response filtered data Conditioning: ', ['Conditioning' => $conditioningQuery]);

            if ($categoryId && $workoutId && $name) {
                $conditioningQuery = $conditioningQuery->where('category_id', $categoryId)
                                                    ->where('workout_id', $workoutId)
                                                    ->whereHas('d', function ($query) use ($name) {
                                                    $query->where('workoutname', 'like', '%' . $name . '%');});
            }

            elseif ($name) {
                $conditioningQuery = $conditioningQuery->whereHas('workout', function ($query) use ($name) {
                    $query->where('workoutname', 'like', '%' . $name . '%');
                });
            }

            elseif ($categoryId) {
                $conditioningQuery = $conditioningQuery->where('category_id', $categoryId);
            }

            elseif ($workoutId) {
                $conditioningQuery = $conditioningQuery->where('workout_id', $workoutId);
            }
            $conditioningRecords = $conditioningQuery->get();
            $workouts = WorkoutLibrary::where('type', 'Conditioning')->with('categoryOption')->get();
            $categoryOptions = $workouts->pluck('categoryOption')->unique('id');

            $result = $conditioningRecords->map(function ($item) {
                $data = [
                    'id' => $item->id,
                    'date' => $item->date,
                    'workoutname' => $item->workoutname,
                    'category_id' => $item->category_id,
                    'category_name' => $item->category?->category_name,
                    'workout_id' => $item->workout_id,
                    'workout_type' => $item->workout?->workout,
                    'reps' => $item->reps,
                    'weight' => $item->weight,
                    'unit' => $item->unit,
                    'time_to_complete' => $item->time_to_complete,
                    'intensity' => $item->intensity,
                    'rounds' => $item->rounds,
                    'amrap' => $item->amrap,
                    'is_assigned' => $item->is_assigned,
                     'assigned_class_ids' => WorkoutAssign::where([
                        'workout_id' => $item->id,
                        'workout_type' => 'conditioning',
                        'date' => $item->date
                    ])->pluck('class_id')->toArray(),
                ];

                // Only include 'rounds' or 'amrap'
                if (!is_null($item->rounds)) {
                    $data['roundsnamrap'] = $item->rounds;
                } elseif ($item->amrap == 1) {
                    $data['roundsnamrap'] = $item->amrap;
                }

                return $data;
            });

            return response()->json([
                'conditioning' => $result,
                'categoryOptions' => $categoryOptions,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getConditioning: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function updateConditioning(Request $request)
    {
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'conditioning_id' => 'required|integer|exists:conditionings,id',
            ]);
            // Find the Conditioning record
            $conditioning = Conditioning::findOrFail($request->input('conditioning_id'));

            // Convert AMRAP checkbox value
            $amrap = $request->input('amrap', false);
            $amrap = filter_var($amrap, FILTER_VALIDATE_BOOLEAN);

            // Update the conditioning record
            $conditioning->date = $request->input('selectdatec');
            $conditioning->rounds = $request->input('roundCond'); // can be null
            $conditioning->workoutname = $request->input('namec_1');
            $conditioning->category_id = $request->input('categoryc_1');
            $conditioning->workout_id = $request->input('workoutc_1');
            $conditioning->weight = $request->input('weigthc_1');
            $conditioning->unit = $request->input('unit_1');
            $conditioning->reps = $request->input('repsc_1');
            $conditioning->time_to_complete = $request->input('timeTC_1');
            $conditioning->intensity = $request->input('intensityc_1');
            $conditioning->amrap = $amrap;

            $conditioning->save();

            return response()->json([
                'message' => 'Conditioning data updated successfully!'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating conditioning data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //delete selected Conditioning data
    public function deleteconditionings(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:conditionings,id',
        ]);

        try {
            $conditioning = Conditioning::findOrFail($request->id);
            $conditioning->delete();

            return response()->json(['status' => 'success', 'message' => 'Conditioning record deleted.']);
        } catch (\Exception $e) {
            Log::error('Error in deletetconditioning: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete Conditioning record.']);
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
