<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\WorkoutManager;
use App\Models\WorkoutLibrary;
use App\Models\Type;
use App\Models\Format;
// Import Child Models
use App\Models\Round;
use App\Models\Interval;
use App\Models\StraightSet;
use App\Models\Straight;
use App\Models\Amrap;
use App\Models\Emom;
use App\Models\ForTime;
use App\Models\Pyramid;
use App\Models\Circuit;
use Illuminate\Support\Facades\Log;

class WorkoutManagerController extends Controller
{
    public function store(Request $request)
    {
        //dd($request);
        // 1. Validate
        $request->validate([
            'common_name' => 'required|string',
            'common_type' => 'required',
            'common_format' => 'required',
            'common_date' => 'required',
        ]);
        $type = Type::where('name', $request->common_type)->first();
        if (!$type) {
            return redirect()->back()->with('error', "Type '{$request->common_type}' not found in database. Please run seeders.");
        }

        $format = Format::where('name', $request->common_format)->first();
        if (!$format) {
            return redirect()->back()->with('error', "Format '{$request->common_format}' not found in database. Please run seeders.");
        }
        // 3. Determine 'number' value based on Format
        $numberValue = null;
        switch($request->common_format) {
            case 'Rounds':
                $numberValue = $request->input('num_rounds');
                break;
            case 'AMRAP':
               // AMRAP usually sends time like "04:00" (MM:SS).
               // Parse to integer (minutes) for storage in 'number' column.
                $timeStr = $request->input('time_to_complete');
                $numberValue = 0;
                if ($timeStr) {
                    if (strpos($timeStr, ':') !== false) {
                        $parts = explode(':', $timeStr);
                        $numberValue = intval($parts[0]); // Take minutes
                    } else {
                        $numberValue = intval($timeStr);
                    }
                }
                break;
            case 'EMOM':
                $numberValue = $request->input('num_minutes'); // Based on Frontend logic
                break;
            case 'Pyramid':
                $numberValue = $request->input('num_layers');
                break;
            case 'Circuit':
                $numberValue = $request->input('num_stations');
                break;
            case 'Intervals':
                $numberValue = $request->input('num_intervals');
                break;
            // For Time usually has no counter number, unless we want to use specific inputs.
        }

        $workout = null;
        $message = '';

        if ($request->filled('common_workout_id')) {
            // Error if trying to update via store
            return redirect()->back()->with('error', 'Use update route for existing workouts.');
        }

        // --- CREATE MODE ---
        $workout = WorkoutManager::create([
            'workout_name' => $request->common_name,
            'type_id' => $type->id,
            'format_id' => $format->id,
            'number' => $numberValue,
            'date' => $request->common_date,
        ]);
        $message = 'Workout Created Successfully!';

        // 4. Handle Dynamic Rows based on Format
        switch($request->common_format) {
            case 'Rounds':
                $this->saveRounds($request, $workout->id);
                break;
            case 'Intervals':
                $this->saveIntervals($request, $workout->id);
                break;
            case 'Straight Sets':
                $this->saveStraightSets($request, $workout->id);
                break;
            case 'AMRAP':
                $this->saveAmrap($request, $workout->id);
                break;
            case 'EMOM':
                $this->saveEmom($request, $workout->id);
                break;
            case 'For Time':
                $this->saveForTime($request, $workout->id);
                break;
            case 'Pyramid':
                $this->savePyramid($request, $workout->id);
                break;
            case 'Circuit':
                $this->saveCircuit($request, $workout->id);
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    public function update(Request $request)
    {

        $type = Type::where('name', $request->common_type)->first();
        if (!$type) return redirect()->back()->with('error', 'Invalid Type Selected');

        $format = Format::where('name', $request->common_format)->first();
        if (!$format) return redirect()->back()->with('error', 'Invalid Format Selected');

        // 2. Calculate 'Number' field based on format
        $numberValue = null;
        switch($request->common_format) {
            case 'Rounds':
                $numberValue = $request->input('num_rounds');
                break;
            case 'AMRAP':
                $timeStr = $request->input('time_to_complete');
                if ($timeStr) {
                    $parts = explode(':', $timeStr);
                    if (count($parts) >= 1) $numberValue = (int)$parts[0];
                }
                break;
            case 'EMOM':
                $numberValue = $request->input('num_minutes');
                break;
            case 'Intervals':
                $numberValue = $request->input('num_intervals');
                break;
            case 'Pyramid':
                $numberValue = $request->input('num_layers');
                break;
            case 'Circuit':
                $numberValue = $request->input('num_stations');
                break;
        }

        // 3. Find and Update
        $workout = WorkoutManager::find($request->common_workout_id);
        if (!$workout) {
             return redirect()->back()->with('error', 'Workout not found for update.');
        }

        $workout->update([
            'workout_name' => $request->common_name,
            'type_id' => $type->id,
            'format_id' => $format->id,
            'number' => $numberValue,
        ]);

        // 4. Clear existing children
        // Use relationship delete or explicit logic
        $workout->rounds()->delete();
        $workout->intervals()->delete();

        // Explicitly delete children of straights if needed (if cascade not set in DB)
        foreach($workout->straights as $s) { $s->sets()->delete(); $s->delete(); }
        $workout->amraps()->delete();
        $workout->emoms()->delete();
        $workout->forTimes()->delete();
        $workout->pyramids()->delete();
        $workout->circuits()->delete();

        // 5. Save Children
        switch($request->common_format) {
            case 'Rounds': $this->saveRounds($request, $workout->id); break;
            case 'Intervals': $this->saveIntervals($request, $workout->id); break;
            case 'Straight Sets': $this->saveStraightSets($request, $workout->id); break;
            case 'AMRAP': $this->saveAmrap($request, $workout->id); break;
            case 'EMOM': $this->saveEmom($request, $workout->id); break;
            case 'For Time': $this->saveForTime($request, $workout->id); break;
            case 'Pyramid': $this->savePyramid($request, $workout->id); break;
            case 'Circuit': $this->saveCircuit($request, $workout->id); break;
        }

        return redirect()->back()->with('success', 'Workout Updated Successfully!');
    }
    private function getWorkoutLibId($name) {
        $lib = WorkoutLibrary::where('workout', $name)->first();
        return $lib ? $lib->id : null;

    }
    private function saveRounds(Request $request, $managerId) {
        $count = $request->input('num_rounds', 1);

        // Loop through all potential rows
        // Since IDs are dynamic (round_load_1, round_load_2...), we loop until we run out of inputs
        $i = 1;
        while($request->has("round_exercise_$i")) {
            $exName = $request->input("round_exercise_$i");
            $wId = $this->getWorkoutLibId($exName);
            if($wId) {
                Round::create([
                    'workout_manager_id' => $managerId,
                    'workout_libraries_id' => $wId,
                    'training_load' => $request->input("round_load_$i"),
                    'unit_type' => $request->input("round_unit_$i"),
                    'reps' => $request->input("round_reps_$i"),
                    'gender' => $request->input("round_gender_$i"),
                    'is_for_time' => $request->has('is_for_time'),
                    'time_to_complete' => $request->has('is_for_time') ? $request->input('time_to_complete') : null,
                ]);
            }
            $i++;
        }
    }
    private function saveIntervals(Request $request, $managerId) {
        $allKeys = $request->keys();
        foreach ($allKeys as $key) {
            // Match pattern: interval_{intervalNum}_exercise_{rowId}
            if (preg_match('/^interval_(\d+)_exercise_([\w]+)$/', $key, $matches)) {
                $intervalNum = $matches[1];
                $rowId = $matches[2];

                $exName = $request->input($key);
                $wId = $this->getWorkoutLibId($exName);

                if ($wId) {
                    Interval::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        'stationumber' => $intervalNum,
                        'training_load' => $request->input("interval_{$intervalNum}_load_{$rowId}"),
                        'unit_type' => $request->input("interval_{$intervalNum}_unit_{$rowId}"),
                        'work' => $request->input("interval_{$intervalNum}_work_{$rowId}"),
                        'rest' => $request->input("interval_{$intervalNum}_rest_{$rowId}"),
                        'gender' => $request->input("interval_{$intervalNum}_gender_{$rowId}"),
                    ]);
                }
            }
        }
    }

    private function saveStraightSets(Request $request, $managerId) {

       // dd($request);
        // 1. Identify all Exercise Indices present in request
        $allKeys = $request->keys();
        $exerciseIndices = [];
        foreach($allKeys as $key) {
            if(preg_match('/^ss_exercise_(\d+)$/', $key, $matches)) {
                $exerciseIndices[] = intval($matches[1]);
            }
        }
        sort($exerciseIndices);

        // If no explicit keys found but format is straight sets, check explicit 'ss_exercise_1'
        if (empty($exerciseIndices) && $request->has('ss_exercise_1')) {
            $exerciseIndices[] = 1;
        }

        $hasMultipleExercises = count($exerciseIndices) > 1;

        foreach($exerciseIndices as $k) {
            $exName = $request->input("ss_exercise_$k");
            // Skip if empty (though validation usually catches required)
            if(!$exName) continue;

            $libId = $this->getWorkoutLibId($exName);

            if($libId) {
                // A. Create Parent 'Straight' Record
                // Determine Header/Default inputs
                $baseReps = ($k == 1) ? $request->input('ss_reps_main') : $request->input("ss_reps_$k");
                $baseLoad = $request->input("ss_load_$k");
                $baseUnit = $request->input("ss_unit_$k");

                $straight = \App\Models\Straight::create([
                    'workout_manager_id' => $managerId,
                    'workout_libraries_id' => $libId,
                    'training_load' => $baseLoad,
                    'unit_type' => $baseUnit,
                    'reps' => $baseReps,
                ]);

                // B. Save Sets (StraightSet)
                // Loop through sets i=1..N until inputs disappear
                $i = 1;
                while(true) {
                    $repsKey = '';
                    $loadKey = '';
                    $unitKey = '';

                    // Determine keys based on Single vs Super Set view
                    if ($k == 1 && !$hasMultipleExercises) {
                        // Standard Single View
                        $repsKey = "ss_reps_$i";
                        $loadKey = "ss_load_$i";
                        $unitKey = "ss_unit_$i";

                        // Fallback: If frontend sent super set format anyway (e.g. if logic changed)
                        if (!$request->has($repsKey) && $request->has("ss_reps_{$i}_1")) {
                             $repsKey = "ss_reps_{$i}_1";
                             $loadKey = "ss_load_{$i}_1";
                             $unitKey = "ss_unit_{$i}_1";
                        }

                    } else {
                        // Super Set View (or k > 1)
                        $repsKey = "ss_reps_{$i}_{$k}";
                        $loadKey = "ss_load_{$i}_{$k}";
                        $unitKey = "ss_unit_{$i}_{$k}";
                    }

                    // Check existence
                    if (!$request->has($repsKey)) {
                        break;
                    }

                    \App\Models\StraightSet::create([
                        'straight_id' => $straight->id,
                        'restred' => $request->restred ?? '00:04:00',
                        'restyellow' => $request->restyellow ?? '00:02:00',
                        'restgreen' => $request->restgreen ?? '00:01:00',
                        'workout_libraries_id' => $libId,
                        'res' => $request->input($repsKey),
                        'training_load' => $request->input($loadKey),
                        'unittype' => $request->input($unitKey),
                    ]);

                    $i++;
                }
            }
        }
    }

    private function saveAmrap(Request $request, $managerId)
    {
        /* Log::info('saveAmrap started', [
            'managerId' => $managerId,
            'ip' => $request->ip(),
        ]);
 */
        $data = $request->all();

        foreach ($data as $key => $value) {
            // Check for amrap_exercise
            if (strpos($key, 'amrap_exercise_') === 0) {
                $i = str_replace('amrap_exercise_', '', $key);

                //  validation that $i is a numeric index
                if (!is_numeric($i)) continue;

                $exName = trim($value);
                $wId = $this->getWorkoutLibId($exName);

                Log::info('AMRAP row processing', [
                    'managerId' => $managerId,
                    'index' => $i,
                    'exercise' => $exName,
                    'workout_library_id' => $wId,
                ]);

                if ($wId) {
                    $load = $request->input("amrap_load_$i");
                    $reps = $request->input("amrap_reps_$i");
                    $reps = $request->input("amrap_reps_$i");
                    $unit = $request->input("amrap_unit_$i");
                    $gender = $request->input("amrap_gender_$i");

                    Amrap::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        // Ensure empty strings are treated as null if column is nullable integer
                        'training_load' => ($load !== '' && $load !== null) ? $load : null,
                        'unit_type' => ($unit !== '' && $unit !== null) ? $unit : 'N/A',
                        'reps' => ($reps !== '' && $reps !== null) ? $reps : null,
                        'gender' => $gender,
                    ]);

                    Log::info("Saved AMRAP row $i");
                } else {
                    Log::warning("AMRAP skipped: Exercise '$exName' not found or ID is null.");
                }
            }
        }
    }


    private function saveEmom(Request $request, $managerId) {
        $allKeys = $request->keys();
        foreach ($allKeys as $key) {
            if (preg_match('/^emom_exercise_(\d+)$/', $key, $matches)) {
                $i = $matches[1];
                $exName = $request->input($key);
                $wId = $this->getWorkoutLibId($exName);

                if($wId) {
                    Emom::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        'training_load' => $request->input("emom_load_$i"),
                        'unit_type' => $request->input("emom_unit_$i"),
                        'reps' => $request->input("emom_reps_$i"),
                        'gender' => $request->input("emom_gender_$i"),
                    ]);
                }
            }
        }
    }

    private function saveForTime(Request $request, $managerId) {
        $allKeys = $request->keys();
        foreach ($allKeys as $key) {
            if (preg_match('/^ft_exercise_(\d+)$/', $key, $matches)) {
                $i = $matches[1];
                $exName = $request->input($key);
                $wId = $this->getWorkoutLibId($exName);
                if($wId) {
                    ForTime::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        'training_load' => $request->input("ft_load_$i"),
                        'unit_type' => $request->input("ft_unit_$i"),
                        'reps' => $request->input("ft_reps_$i"),
                        'gender' => $request->input("ft_gender_$i"),
                    ]);
                }
            }
        }
    }

    private function savePyramid(Request $request, $managerId) {
        $mainExName = $request->input("pyramid_exercise");
        $wId = $this->getWorkoutLibId($mainExName);

        if ($wId) {
            $i = 1;
            while($request->has("pyramid_load_$i")) {
                Pyramid::create([
                    'workout_manager_id' => $managerId,
                    'workout_libraries_id' => $wId,
                    'training_load' => $request->input("pyramid_load_$i"),
                    'unit_type' => $request->input("pyramid_unit_$i"),
                    'reps' => $request->input("pyramid_reps_$i"),
                    'gender' => $request->input("pyramid_gender_$i"),
                    'restred' => $request->restred ?? '00:04:00',
                    'restyellow' => $request->restyellow ?? '00:02:00',
                    'restgreen' => $request->restgreen ?? '00:01:00',
                ]);
                $i++;
            }
        }
    }

    private function saveCircuit(Request $request, $managerId) {


        $allKeys = $request->keys();

        foreach ($allKeys as $key) {
            // Match pattern: station_X_exercise_Y
            if (preg_match('/^station_(\d+)_exercise_([\w]+)$/', $key, $matches)) {
                $stationNum = $matches[1];
                $rowId = $matches[2];

                $exName = $request->input($key);
                $wId = $this->getWorkoutLibId($exName);

                // Get other fields using the same IDs
                $load = $request->input("station_{$stationNum}_load_{$rowId}");
                $unit = $request->input("station_{$stationNum}_unit_{$rowId}");
                $reps = $request->input("station_{$stationNum}_reps_{$rowId}");
                $gender = $request->input("station_{$stationNum}_gender_{$rowId}");

                if ($wId) {
                    Circuit::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        'stationumber' => $stationNum,
                        'training_load' => $load,
                        'unit_type' => $unit,
                        'reps' => $reps,
                        'gender' => $gender,
                        'is_for_time' => $request->has('is_for_time'),
                        'time_to_complete' => $request->has('is_for_time') ? $request->input('time_to_complete') : null,
                    ]);
                }
            }
        }
    }




    public function getWorkouts(Request $request)
    {
        try {
            $date = $request->input('date');

            // 1. Fetch Classes for this date
            $classes = [];
            if ($date) {
                // Assuming 'Classes' model exists and has a 'date' column
                $classes = \App\Models\Classes::where('date', $date)
                ->orderBy('time', 'asc')
                ->get();
            }

            // 2. Fetch Workouts
            $query = WorkoutManager::with([
                'format',
                'type',
                'straights.workoutLibrary', 'straights.sets',
                'rounds.workoutLibrary',
                'intervals.workoutLibrary',
                'amraps.workoutLibrary',
                'emoms.workoutLibrary',
                'pyramids.workoutLibrary',
                'circuits.workoutLibrary',
                'forTimes.workoutLibrary'
            ]);

            // Filter by Name (Workout Name OR Exercise Name)
            if ($request->has('name') && $request->name != '') {
                $name = $request->name;
                $query->where(function($q) use ($name) {
                    $q->where('workout_name', 'like', '%' . $name . '%');

                    // Search in child relationships
                    $relations = [
                        'straights', 'rounds', 'intervals', 'amraps',
                        'emoms', 'pyramids', 'circuits', 'forTimes'
                    ];

                    foreach ($relations as $rel) {
                        $q->orWhereHas($rel, function($sub) use ($name) {
                             $sub->whereHas('workoutLibrary', function($lib) use ($name) {
                                  $lib->where('workout', 'like', '%' . $name . '%');
                             });
                        });
                    }
                });
            }

            // Filter by Category or Exercise (Library ID)
            $catId = $request->input('category_id');
            $libId = $request->input('library_id');

            if (($catId && $catId != '') || ($libId && $libId != '')) {
                $query->where(function($q) use ($catId, $libId) {
                    $relations = [
                        'straights', 'rounds', 'intervals', 'amraps',
                        'emoms', 'pyramids', 'circuits', 'forTimes'
                    ];

                    foreach ($relations as $rel) {
                        $q->orWhereHas($rel, function($sub) use ($catId, $libId) {
                            if ($libId) {
                                $sub->where('workout_libraries_id', $libId);
                            } elseif ($catId) {
                                $sub->whereHas('workoutLibrary', function($lib) use ($catId) {
                                    $lib->where('category_options_id', $catId);
                                });
                            }
                        });
                    }
                });
            }

            // FILTER: Show only Active workouts
            $query->where('status', '!=', 'inactive');

            // FILTER: Workout date
            if ($date) {
                $query->whereDate('date', $date);
            }

            // EXCLUDE workout types 6 and 7
            $query->whereNotIn('type_id', [6, 7]);

            $workouts = $query->orderBy('created_at', 'desc')->get();

             //Attach Assignment Status (if date provided)
             if ($date) {
                $workouts->transform(function ($workout) use ($date) {
                    $realType = $workout->type ? strtolower($workout->type->name) : 'unknown';
                    Log::info("Saved type row $realType");
                    $assignedClassIds = \App\Models\WorkoutAssign::where([
                        'workout_id' => $workout->id,
                        'workout_type' => $realType,
                        'date' => $date
                    ])->pluck('class_id')->toArray();

                    $workout->assigned_class_ids = $assignedClassIds;
                    return $workout;
                });
            }

            return response()->json([
                'workouts' => $workouts,
                'classes' => $classes
            ]);

        } catch (\Exception $e) {
            Log::error("Error fetching Unified Workout List: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $workout = WorkoutManager::find($id);
            if ($workout) {
                $workout->update(['status' => 'inactive']);
                return response()->json(['status' => 'success', 'message' => 'Workout deleted successfully.']);
            }
            return response()->json(['status' => 'error', 'message' => 'Workout not found.'], 404);
        } catch (\Exception $e) {
            Log::error("Error deleting workout: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete workout.'], 500);
        }
    }

}
