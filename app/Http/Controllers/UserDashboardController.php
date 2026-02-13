<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Access;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\DailyStrength;
use App\Models\DailyWarmup;
use App\Models\Newprofile;
use App\Models\Strength;
use App\Models\StrengthSetRep;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserDashboardController extends Controller
{
    //user dashboard
    public function viewdashboard()
    {
        try {
            $user = Auth::user(); // Get the currently authenticated user

            if (!$user) {
                // If the user is not authenticated, return the unauthorized view
                return view('error.unauthorized');
            }

            $member = Newprofile::where('user_id', $user->id)->first();
            // Decode the image paths if necessary
            $imagePaths = json_decode($member->image_paths, true);

            // Check if decoding was successful
            if (is_array($imagePaths) && !empty($imagePaths)) {
                $profileImage = 'storage/' . $imagePaths[0]; // Assuming you want the first image
            } else {
                $profileImage = 'storage/default-profile.png'; // Default image if no image paths
            }
            // dd($profileImage);

            return view('user.user.dashboard', compact('user', 'member', 'profileImage'));
        } catch (Exception $e) {
            // Log the error message
            Log::error('Error in viewdashboard: ' . $e->getMessage());

            // Return a generic error view or an unauthorized view
            return view('error.unauthorized');
        }
    }


    public function getData(Request $request)
    {
        try {
            Log::info('getData function called.');

            $date = $request->input('date');
            Log::info('Date received: ' . $date);

            $user = Auth::user(); // Get the currently authenticated user
            Log::info('Authenticated user: ', ['user' => $user]);

            if (!$user) {
                // If the user is not authenticated, return the unauthorized view
                Log::warning('Unauthorized access attempt.');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $member = Newprofile::where('user_id', $user->id)->first();
            Log::info('Member found: ', ['member' => $member]);

            if (!$member) {
                // If member is not found, return an empty array
                Log::info('No member found for user.');
                return response()->json([]);
            }

            $warmups = DailyWarmup::with(['member', 'warmup.category'])
                ->where('member_id', $member->id)
                ->whereDate('date', $date)
                ->get()
                ->map(function ($item) {
                    Log::info('Mapping warmup item: ', ['item' => $item]);
                    return [
                        'warmup' => [
                            'workout' => $item->warmup,  // Access warmup details
                            'category' => $item->warmup->category,  // Access warmup category details
                        ],
                        'reps' => $item->reps,
                        'date' => $item->date,
                    ];
                });

            Log::info('Warmups retrieved: ', ['warmups' => $warmups]);

            // Retrieve the strengths
            $strengths = DailyStrength::where('member_id', $member->id)
                ->where('date', $date)
                ->get();

            Log::info('Strengths retrieved gggg:', ['warmups' => $strengths->toArray()] );
            $Sdetails = [];
            // Optional: You can iterate over strengths to get data from the Strength table
            foreach ($strengths as $strength) {
                if ($strength) { // Check if the strength relationship is not null
                    $strengthDetails = $strength->strenght;
            
                    if ($strengthDetails) {
                        $detail = [];
            
                        if ($strength->type == "Primary") {
                            $detail['mapStrCat'] = $strengthDetails->category ? $strengthDetails->category->category_name : 'N/A';
                            $detail['mapStrWork'] = $strengthDetails->workout ? $strengthDetails->workout->workout : 'N/A';
                            $detail['sets'] = StrengthSetRep::where('strength_id', $strengthDetails->id)->pluck('sets');
                        } else {
                            $detail['mapStrCat'] = $strengthDetails->altCategory ? $strengthDetails->altCategory->category_name : 'N/A';
                            $detail['mapStrWork'] = $strengthDetails->altWorkout ? $strengthDetails->altWorkout->workout : 'N/A';
                            $detail['sets'] = StrengthSetRep::where('strength_id', $strengthDetails->id)->pluck('alt_sets');
                        }
            
                        $detail['weight'] = $strength->weight;
                        $detail['reps'] = $strength->reps;
            
                        $Sdetails[] = $detail; // Add each detail to the array
            
                        Log::info('Details:', $detail); // Log each strength's details
                    } else {
                        Log::warning('Strength relationship is null for strength ID: ' . $strength->id);
                    }
                } else {
                    Log::warning('Strength is null in the list.');
                }
            }
            
            // Log all details after the loop if needed
            Log::info('All Details:', $Sdetails);
            

            // Log the strengths data in a more readable format
            Log::info('Strengths retrieved:', ['strengths' => $strengths->toArray()]);

            return response()->json(['warmup' => $warmups, 'strengths' => $strengths]);
        } catch (Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    //  Dashboard Search Function
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Perform search
        $user = User::where('user_type', 'client')
            ->where('name', 'like', '%' . $query . '%')
            ->first();

        if ($user) {
            // Store user details in session
            Session::put('searchedUser', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                // Add other user details as needed
            ]);

            return response()->json(['user' => $user]);
        } else {
            // Clear session if no user found
            Session::forget('searchedUser');

            return response()->json(['user' => null]);
        }
    }


    // // Clear searched user details from session
    // public function clearSearchedUser()
    // {
    //     Session::forget('searchedUser');

    //     return redirect()->back()->with('message', 'Searched user details cleared from session.');
    // }
}
