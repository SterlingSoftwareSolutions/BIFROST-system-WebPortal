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
        // 1. Validate
        $request->validate([
            'common_name' => 'required|string',
            'common_type' => 'required',
            'common_format' => 'required',
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

        // 3. Create main Record
        $workout = WorkoutManager::create([
            'workout_name' => $request->common_name,
            'type_id' => $type->id,
            'format_id' => $format->id,
            'number' => $numberValue,
        ]);
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
        return redirect()->back()->with('success', 'Workout Created Successfully!');
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
                    ]);
                }
            }
        }
    }
    
    private function saveStraightSets(Request $request, $managerId) {
        // 1. Handle Main Exercise
        $mainExName = $request->input('ss_exercise_1');
        $mainLibId = $this->getWorkoutLibId($mainExName);
        
        if($mainLibId) {
            // Create Parent 'Straight' record for Main Exercise
            $straightMain = \App\Models\Straight::create([
                'workout_manager_id' => $managerId,
                'workout_libraries_id' => $mainLibId,
                'training_load' => $request->input('ss_load_1'), // Base load
                'unit_type' => $request->input('ss_unit_1'),
                'reps' => $request->input('ss_reps_main'), // Base reps target
            ]);
            // Save Sets for Main Exercise
            // Loop through rows: ss_reps_1, ss_reps_2... (or ss_reps_1_1 if superset)
            $i = 1;
            while($request->has("ss_reps_" . $i) || $request->has("ss_reps_" . $i . "_1")) {
                // Check if simple or superset key exists
                $repsKey = $request->has("ss_reps_" . $i) ? "ss_reps_" . $i : "ss_reps_" . $i . "_1";
                $loadKey = $request->has("ss_load_" . $i) ? "ss_load_" . $i : "ss_load_" . $i . "_1";
                $unitKey = $request->has("ss_unit_" . $i) ? "ss_unit_" . $i : "ss_unit_" . $i . "_1";
                
                if($request->has($repsKey)) {
                     \App\Models\StraightSet::create([
                        'straight_id' => $straightMain->id,
                        'workout_libraries_id' => $mainLibId, // Redundant but required by schema
                        'res' => $request->input($repsKey),
                        'trainload' => $request->input($loadKey),
                        'unittype' => $request->input($unitKey),
                    ]);
                }
                $i++;
            }
        }
        // 2. Handle Super Set if exists
        if ($request->has('ss_exercise_2') && $request->input('ss_exercise_2')) {
            $secExName = $request->input('ss_exercise_2');
            $secLibId = $this->getWorkoutLibId($secExName);
            
            if($secLibId) {
                // Create Parent 'Straight' record for Secondary
                $straightSec = \App\Models\Straight::create([
                    'workout_manager_id' => $managerId,
                    'workout_libraries_id' => $secLibId,
                    'training_load' => $request->input('ss_load_2'),
                    'unit_type' => $request->input('ss_unit_2'),
                    'reps' => $request->input('ss_reps_secondary'),
                ]);
                // Save Sets for Secondary Exercise
                // Rows are named ss_reps_1_2, ss_reps_2_2...
                $j = 1;
                while($request->has("ss_reps_" . $j . "_2")) {
                    \App\Models\StraightSet::create([
                        'straight_id' => $straightSec->id,
                        'workout_libraries_id' => $secLibId,
                        'res' => $request->input("ss_reps_" . $j . "_2"),
                        'trainload' => $request->input("ss_load_" . $j . "_2"),
                        'unittype' => $request->input("ss_unit_" . $j . "_2"),
                    ]);
                    $j++;
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
                    $unit = $request->input("amrap_unit_$i");

                    Amrap::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        // Ensure empty strings are treated as null if column is nullable integer
                        'training_load' => ($load !== '' && $load !== null) ? $load : null,
                        'unit_type' => ($unit !== '' && $unit !== null) ? $unit : 'N/A',
                        'reps' => ($reps !== '' && $reps !== null) ? $reps : null,
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

                if ($wId) {
                    Circuit::create([
                        'workout_manager_id' => $managerId,
                        'workout_libraries_id' => $wId,
                        'stationumber' => $stationNum, 
                        'training_load' => $load,
                        'unit_type' => $unit,
                        'reps' => $reps,
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
                $classes = \App\Models\Classes::where('date', $date)->get();
            }

            // 2. Fetch Workouts
            $query = WorkoutManager::with([
                'format',
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

            $workouts = $query->orderBy('created_at', 'desc')->get();

             //Attach Assignment Status (if date provided)
             if ($date) {
                $workouts->transform(function ($workout) use ($date) {
                    $realType = $workout->type ? strtolower($workout->type->name) : 'unknown';

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

}