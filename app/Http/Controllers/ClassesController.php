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
        ]);

        Classes::create([
            'time' => $request->time,
            'duration' => $request->duration,
            'spots' => $request->spots,
            'workout_assigned' => $request->has('workout_assigned'),
            'date' => $request->selectdatecla,
        ]);

        return redirect()->back()->with('success', 'Class added successfully.');
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
                    // case 'conditioning':
                    //     Conditioning::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                    //     break;
                    case 'warmup':
                        Warmup::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                        break;
                    // case 'test':
                    //     Test::where('id', $assign->workout_id)->update(['is_assigned' => false]);
                    //     break;
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
