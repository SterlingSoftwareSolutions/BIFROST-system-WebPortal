<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\CategoryOption;
use App\Models\WorkoutLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutLibraryController extends Controller
{
    //
    public function viewworkoutlibrary(Request $request)
    {
        $userId = Auth::id(); // Get the currently authenticated user's ID

        // Fetch the access record for the user
        $access = Access::where('user_id', $userId)->first();
        $categoryOptions = CategoryOption::all();

        if ($access && $access->workout_library === 'enable') {
            // Pass the access type to the view using compact
            $accessType = $access->access_type;

            // Initialize the query for WorkoutLibrary
            $query = WorkoutLibrary::query();

            // Apply filters if request parameters are present
            if ($request->has('categoryOption') && $request->categoryOption != '') {
                $query->where('category_options_id', $request->categoryOption);
            }

            if ($request->has('type') && $request->type != '') {
                $query->where('type', $request->type);
            }

            // Get the filtered or unfiltered workout libraries
            $workoutLibraries = $query->with('categoryOption')->get();

            // Return the view with the data
            return view('admin.user.workout_library', compact('accessType', 'categoryOptions', 'workoutLibraries'));
        } else {
            // Redirect to an unauthorized access view
            return view('error.unauthorized');
        }
    }

    public function listworkoutlibrary(Request $request)
    {
        // Validate the request data
        $request->validate([
            'category' => 'required|exists:category_options,id',
            'workout' => 'required|string',
            'link' => 'nullable|url',
        ]);

        // Check if the request has an 'id' for updating an existing entry
        if ($request->id) {
            // Find the existing workout library entry by id
            $workoutLibrary = WorkoutLibrary::find($request->id);

            // Check if the entry exists
            if ($workoutLibrary) {
                // Update the workout library entry
                $workoutLibrary->update([
                    'category_options_id' => $request->input('category'),
                    'workout' => $request->input('workout'),
                    'link' => $request->input('link'),
                ]);

                // Redirect back with a success message
                return redirect()->back()->with('success', 'Workout library entry updated successfully!');
            } else {
                // Redirect back with an error message if the entry is not found
                return redirect()->back()->with('error', 'Workout library entry not found!');
            }
        } else {
            // Create a new workout library entry
            WorkoutLibrary::create([
                'category_options_id' => $request->input('category'),
                'workout' => $request->input('workout'),
                'link' => $request->input('link'),
            ]);

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Workout library entry added successfully!');
        }
    }


    public function delete($id)
    {
        // Find the workout library entry
        $workoutLibrary = WorkoutLibrary::findOrFail($id);

        // Delete the entry
        $workoutLibrary->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Workout library entry deleted successfully!');
    }


    public function edit($id)
    {
        $workoutLibrary = WorkoutLibrary::findOrFail($id);

        // You may adjust this response based on your data structure
        return response()->json([
            'category_id' => $workoutLibrary->categoryOption->category_name,
            'type' => $workoutLibrary->type,
            'workout' => $workoutLibrary->workout,
            'link' => $workoutLibrary->link,
        ]);
    }
}
