<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class UserGoalController extends Controller
{

    // view goal function
    public function viewGoal()
    {

        $searchedUser = Auth::user(); // Get the currently authenticated user's ID

        // Check if $searchedUser is an array and convert it to an object if needed
        if (is_array($searchedUser)) {
            $searchedUser = (object) $searchedUser;
        }

        if ($searchedUser && isset($searchedUser->id)) {
            $goal = Goal::where('user_id', $searchedUser->id)->first();
            return view('user.user.smart-goal', compact('searchedUser', 'goal'));
        } else {
            // Pass an error message to the view
            $errorMessage = "User not found or unauthorized access.";
            return view('user.user.smart-goal', compact('errorMessage'));
        }
    }

    //save function and update function
    public function saveGoal(Request $request)
    {
        $user = Auth::user();
        // dd($request);
        if (!$request->goal_id) {
            $validatedData = $request->validate([
                'goal_name' => 'required|string',
                'specific' => 'required|string',
                'measurable' => 'required|string',
                'achievable' => 'required|string',
                'relevant' => 'required|string',
                'time_bound' => 'required|string',
            ]);

            $goal = new Goal();
            $goal->user_id = $user->id;
            $goal->goal_name = $validatedData['goal_name'];
            $goal->specific = $validatedData['specific'];
            $goal->measurable = $validatedData['measurable'];
            $goal->achievable = $validatedData['achievable'];
            $goal->relevant = $validatedData['relevant'];
            $goal->time_bound = $validatedData['time_bound'];
            $goal->save();

            return redirect()->route('usergoal')->with('success', 'Goal saved successfully.');
        } else {
            // dd("byyy");
            $id = $request->goal_id;
            $goal = Goal::find($id);

            if ($goal) {
                // Update the desired columns
                $goal->achievable_progress = $request->input('achievable_progress');
                $goal->time_progress = $request->input('time_progress');

                // Save the changes
                $goal->save();

                // Return a response, such as a redirect or a success message
                return redirect()->back()->with('success', 'Goal updated successfully.');
            } else {
                // Handle the case where the goal is not found
                return redirect()->back()->with('error', 'Goal not found.');
            }
        }
    }
}
