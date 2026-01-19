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
        $request->validate([
            'time' => 'required',
            'duration' => 'required|integer',
            'spots' => 'required|integer',
            'selectdatecla' => 'required',
            'days' => 'array|nullable',
        ]);
        $startDate = Carbon::createFromFormat('d/m/y l', $request->selectdatecla)->format('Y-m-d');
        
        // If no recurring days selected, just create one
        if (!$request->has('days') || empty($request->days)) {
             $this->createClass($request, $request->selectdatecla);
             return redirect()->back()->with('success', 'Class added successfully.');
        }
        // Recurring Logic
        $selectedDays = $request->days; // e.g., ['Mon', 'Wed']
        $currentDate = Carbon::parse($startDate);
        $endOfYear = Carbon::now()->endOfYear();
        $createdCount = 0;
        $conflicts = [];
        while ($currentDate->lte($endOfYear)) {
            // Check if current day short name (e.g., 'Mon') is in selected days
            if (in_array($currentDate->format('D'), $selectedDays)) {
                
                // Format date back to your system's format
                // Ensure this matches your DB format e.g. "19/01/26 Sunday"
                $formattedDate = $currentDate->format('d/m/y l');
                // Check for Overlap (Simple check: same date and time)
                $exists = Classes::where('date', $formattedDate)
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
        Classes::create([
            'time' => $request->time,
            'duration' => $request->duration,
            'spots' => $request->spots,
            'availablespots' => $request->spots,
            'workout_assigned' => $request->has('workout_assigned'),
            'date' => $date,
        ]);
    }
    
    public function edit($id)
    {
        $class = Classes::findOrFail($id);
        return response()->json($class);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'time' => 'required',
            'duration' => 'required|integer',
            'spots' => 'required|integer',
        ]);

        $class = Classes::findOrFail($id);
        $class->update([
            'time' => $request->time,
            'duration' => $request->duration,
            'spots' => $request->spots,
        ]);

        return redirect()->back()->with('success', 'Class updated successfully.');
    }

    // Delete class
    public function delete($id)
    {
        try {
            // Fetch all related assigned workouts
            $assignments = WorkoutAssign::where('class_id', $id)->get();

            foreach ($assignments as $assign) {
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
                    default:
                        // Unknown type - skip
                        break;
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
