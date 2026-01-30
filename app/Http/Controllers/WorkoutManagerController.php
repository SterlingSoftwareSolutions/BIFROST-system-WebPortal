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
        // 1. Validate Basic Info
        $request->validate([
            'common_name' => 'required|string',
            'common_type' => 'required',
            'common_format' => 'required',
        ]);
        // 2. Look up IDs (since form sends Strings)
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
               // AMRAP usually sends time like "04:00". We save it as is (DB will cast if int, or we explicitly parse if needed).
               // Assuming 'number' column is integer, "04:00" becomes 4.
                $numberValue = $request->input('time_to_complete');
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
        $count = $request->input('num_rounds', 1); // Not really used for looping, better check post keys
        
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
        // 2. Handle Super Set (Secondary Exercise) if exists
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
    Log::info('saveAmrap started', [
        'managerId' => $managerId,
        'ip' => $request->ip(),
        'user_id' => optional($request->user())->id,
    ]);

    $allKeys = $request->keys();
    $rowsProcessed = 0;

    foreach ($allKeys as $key) {
        if (preg_match('/^amrap_exercise_(\d+)$/', $key, $matches)) {
            $i = $matches[1];
            $exName = $request->input($key);
            $wId = $this->getWorkoutLibId($exName);

            Log::info('AMRAP row processing', [
                'managerId' => $managerId,
                'index' => $i,
                'exercise' => $exName,
                'workout_library_id' => $wId,
                'training_load' => $request->input("amrap_load_$i"),
                'unit_type' => $request->input("amrap_unit_$i"),
                'reps' => $request->input("amrap_reps_$i"),
            ]);

            if ($wId) {
                $amrap = Amrap::create([
                    'workout_manager_id' => $managerId,
                    'workout_libraries_id' => $wId,
                    'training_load' => $request->input("amrap_load_$i"),
                    'unit_type' => $request->input("amrap_unit_$i"),
                    'reps' => $request->input("amrap_reps_$i"),
                ]);

                $rowsProcessed++;
                Log::info('AMRAP saved', [
                    'managerId' => $managerId,
                    'index' => $i,
                    'amrap_id' => $amrap->id,
                    'workout_library_id' => $wId,
                ]);
            } else {
                Log::warning('AMRAP skipped (workout library not found)', [
                    'managerId' => $managerId,
                    'index' => $i,
                    'exercise' => $exName,
                ]);
            }
        }
    }

    Log::info('saveAmrap finished', [
        'managerId' => $managerId,
        'rows_processed' => $rowsProcessed,
    ]);
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
        // Sort keys to try and maintain some order, though station_1 vs station_2 is main order.
        
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



}