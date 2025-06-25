<?php

namespace App\Http\Controllers;

use App\Models\Classes;
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
        Classes::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Class deleted successfully.');
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
