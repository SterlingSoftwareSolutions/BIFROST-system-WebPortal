<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Conditioning;
use App\Models\Strength;
use App\Models\Test;
use App\Models\Warmup;
use App\Models\Weightlifting;
use App\Models\WorkoutAssign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClassesController extends Controller
{
    // get all classes
    public function index()
    {
        $classes = Classes::all();
        return view('admin.user.session', compact('classes'));
    }

    // Store new class
     public function store(Request $request)
    {
        Log::info('Store Class Request Data:', $request->all());

        $request->validate([
            'time' => 'required',
            'duration' => 'required|integer',
            'spots' => 'required|integer',
            'selectdatecla' => 'required',
            'days' => 'array|nullable',
        ]);
        
        try {
            $startDate = Carbon::createFromFormat('d/m/y l', $request->selectdatecla)->format('Y-m-d');
        } catch (\Exception $e) {
             Log::error('Date Parsing Error: ' . $e->getMessage());
             return redirect()->back()->with('error', 'Invalid date format provided.');
        }

        
        // If no recurring days selected, just create one
        if (!$request->has('days') || empty($request->days)) {
             Log::info('Creating single class. Date input: ' . $request->selectdatecla);
             
             // Log the start date calculated
             Log::info('Parsed start date: ' . $startDate);

             $this->createClass($request, $request->selectdatecla);
             
             Log::info('Single class creation called successfully.');
             return redirect()->back()->with('success', 'Class added successfully.');
        }

        $selectedDays = $request->days; // e.g., ['Mon', 'Wed']
        //Log::info('Creating recurring classes for days: ' . implode(', ', $selectedDays));

        $currentDate = Carbon::parse($startDate);
        $endOfYear = Carbon::now()->endOfYear();
        $createdCount = 0;
        $conflicts = [];
        
        // Safety Break: avoid infinite loops if something goes wrong with dates
        $maxIterations = 366; 
        $iterations = 0;

        while ($currentDate->lte($endOfYear) && $iterations < $maxIterations) {
            $iterations++;

            // Check if current day short name (e.g., 'Mon') is in selected days
            if (in_array($currentDate->format('D'), $selectedDays)) {
                
               
                // Ensure this matches your DB format e.g. "19/01/26 Sunday"
                $formattedDate = $currentDate->format('d/m/y l');
               
                $exists = Classes::where('date', $formattedDate)//overlap check
                                ->where('time', $request->time)
                                ->exists();
                if (!$exists) {
                    $this->createClass($request, $formattedDate);
                    $createdCount++;
                } else {
                    $conflicts[] = $formattedDate;
                }
            }
            $currentDate->addDay();
        }
        if (count($conflicts) > 0) {
            $msg = "Created $createdCount classes. Skipped " . count($conflicts) . " due to conflicts.";
            return redirect()->back()->with('warning', $msg);
        }
        return redirect()->back()->with('success', "Recursively created $createdCount classes!");
    }
    // Helper function to keep code clean
    private function createClass($request, $date) {
        Log::info("createClass helper called. Date: $date, Time: {$request->time}, Spots: {$request->spots}");
        
        $class = Classes::create([
            'time' => $request->time,
            'duration' => $request->duration,
            'spots' => $request->spots,
            'availablespots' => $request->spots,
            'workout_assigned' => $request->has('workout_assigned'),
            'date' => $date,
        ]);
        
        Log::info("Class created: " . $class->id);
    }
    
    public function edit($id)
    {
        $class = Classes::findOrFail($id);
        return response()->json($class);
    }

    public function update(Request $request, $id)
    {
        Log::info('Update Class Request:', $request->all());
        $request->validate([
            'time' => 'required',
            'duration' => 'required|integer',
            'spots' => 'required|integer',
            'days' => 'nullable|array', // "Repeat On" days
        ]);
        $class = Classes::findOrFail($id);
        // 1. Standard Update (Fields other than date)
        $class->update([
            'time' => $request->time, 
            'duration' => $request->duration,
            'spots' => $request->spots,
        ]);

        // Date Updating 
        if ($request->has('days') && !empty($request->days)) {
            $newDayShort = $request->days[0]; // 'Mon', 'Tue'.
            // Get Current Class Day
            try {
                // Current Date Format: "d/m/y l" (e.g. 26/01/26 Sunday)
                $currentDate = Carbon::createFromFormat('d/m/y l', $class->date);
                $currentDayShort = $currentDate->format('D');

                if ($newDayShort !== $currentDayShort) {
                    $cDay = Carbon::parse($currentDayShort);
                    $nDay = Carbon::parse($newDayShort);
                    $oldIndex = $currentDate->dayOfWeekIso; 
                    $map = ['Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6, 'Sun'=>7];
                    $newIndex = $map[$newDayShort] ?? $oldIndex;
                    
                    $diff = $newIndex - $oldIndex;
                    
                    // Apply this diff to ALL upfront classes of this series
                    Log::info("Day Shift: $diff days");
                    $allClasses = Classes::where('time', $class->time)->get(); // Filter by time first
                    
                    $updatedCount = 0;
                    
                    foreach ($allClasses as $c) {
                        try {
                            $cDate = Carbon::createFromFormat('d/m/y l', $c->date);
                        } catch (\Exception $e) { continue; }

                        // Check if it's "Future or Present" AND "Is Old Day"
                        if ($cDate->gte($currentDate) && $cDate->format('D') === $currentDayShort) {
                            
                            // Apply Shift
                            $newDate = $cDate->copy()->addDays($diff);
                            
                            // Format: "d/m/y l"
                            $newDateStr = $newDate->format('d/m/y l');
                            $c->date = $newDateStr;
                            $c->save();
                            $updatedCount++;
                        }
                    }
                    
                    //Log::info("repeatedly updated $updatedCount classes.");
                    return redirect()->back()->with('success', "Class updated. Moved $updatedCount classes from $currentDayShort to $newDayShort.");
                }

            } catch (\Exception $e) {
                //Log::error("Error in recursive update: " . $e->getMessage());
                return redirect()->back()->with('error', 'Class updated but failed to process recurrence: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Class updated successfully.');
    }

    // Delete class
    public function delete($id)
    {
        try {
            // Fetch all related assigned workouts
            $assignments = WorkoutAssign::where('class_id', $id)->get();

            foreach ($assignments as $assign) {
                // Check if this workout is assigned to any OTHER class (excluding the one being deleted)
                $otherAssignmentsExist = WorkoutAssign::where('workout_id', $assign->workout_id)
                    ->where('workout_type', $assign->workout_type)
                    ->where('class_id', '!=', $id) // Exclude the class being deleted
                    ->exists();

                // If no other classes have this workout, then mark it as unassigned
                if (!$otherAssignmentsExist) {
                    switch ($assign->workout_type) {
                        case 'strength':
                            Strength::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                            break;
                        case 'weightlifting':
                            Weightlifting::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                            break;
                        case 'conditioning':
                            Conditioning::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                            break;
                        case 'warmup':
                            Warmup::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                            break;
                        case 'test':
                            Test::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                            break;
                    }
                }
            }

            // Delete the class (will also delete workout_assign records due to cascade)

            Classes::findOrFail($id)->delete();

            return response()->json(['success' => true, 'message' => 'Class and workout assignments deleted successfully.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function toggleWorkout(Request $request, $id)
    {
        $class = Classes::findOrFail($id);
        $class->workout_assigned = $request->input("workout_assigned_{$id}", 0);
        $class->save();

        return redirect()->back()->with('success', 'Workout assignment updated.');
    }

    public function getByDay(Request $request)
    {
        $dayName = $request->query('day');
        Log::info('Day received:', ['day' => $dayName]);

        $classes = Classes::where('date', $dayName)->get();

        foreach ($classes as $class) {
             $class->is_warmup = WorkoutAssign::where('class_id', $class->id)->where('workout_type', 'warmup')->exists();
             $class->is_strength = WorkoutAssign::where('class_id', $class->id)->where('workout_type', 'strength')->exists();
             $class->is_weightlifting = WorkoutAssign::where('class_id', $class->id)->where('workout_type', 'weightlifting')->exists();
             $class->is_conditioning = WorkoutAssign::where('class_id', $class->id)->where('workout_type', 'conditioning')->exists();
             $class->is_accessory = WorkoutAssign::where('class_id', $class->id)->where('workout_type', 'accessory')->exists();
             $class->is_1rm = WorkoutAssign::where('class_id', $class->id)->whereIn('workout_type', ['1rm', 'test', "PR's", "pr's"])->exists();
        }

        return response()->json($classes);
    }

    public function reserve(Request $request)
    {
        $classId = $request->input('class_id');

        // Reserve logic here
        // e.g., mark the user as registered

        return redirect()->back()->with('success', 'Class reserved successfully!');
    }

}
