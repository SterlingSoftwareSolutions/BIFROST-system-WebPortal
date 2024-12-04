<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Access;
use App\Models\Newprofile;
use App\Models\MonthlyImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    // Display user profile if authorized
    public function viewprofile()
    {
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

        // Check if $searchedUser is an array and convert it to an object if needed
        $searchedUser = $user;
        if (is_array($searchedUser)) {
            $searchedUser = (object) $searchedUser;
        }

        // Get the current month and two previous months
        $months = collect();
        for ($i = 0; $i < 3; $i++) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $date->format('m'),
                'year' => $date->format('Y')
            ]);
        }

        // Fetch images for the current and previous two months
        $images = MonthlyImage::where('user_id', $user->id)
            ->where(function ($query) use ($months) {
                foreach ($months as $month) {
                    $query->orWhere(function ($query) use ($month) {
                        $query->whereMonth('month', $month['month'])
                            ->whereYear('month', $month['year']);
                    });
                }
            })
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->month)->format('Y-m'); // Group by year-month
            });

        // Pass the data to the view
        return view('user.user.profile', [
            'user' => $user,
            'images' => $images,
            'months' => $months,
            'profileImage' => $profileImage,
            'member' => $member
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|date',
            'front_image' => 'required|image|mimes:png,jpg,jpeg,gif,img',
            'side_image' => 'required|image|mimes:png,jpg,jpeg,gif,img',
            'back_image' => 'required|image|mimes:png,jpg,jpeg,gif,img',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $frontImagePath = $request->file('front_image')->store('images', 'public');
            $sideImagePath = $request->file('side_image')->store('images', 'public');
            $backImagePath = $request->file('back_image')->store('images', 'public');

            // dd($frontImagePath);
            MonthlyImage::create([
                'month' => $request->month,
                'front_image' => $frontImagePath,
                'side_image' => $sideImagePath,
                'back_image' => $backImagePath,
                'user_id' => $request->user_id,
            ]);

            return redirect()->back()->with('success', 'Images uploaded successfully!');
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function handlePrevData(Request $request)
    {
        // Validate the request data
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'id'   => 'required|integer|exists:users,id'
        ]);

        // Retrieve the date and user ID from the request
        $date = $request->input('date');
        $userId = $request->input('id');

        // Convert the date to a Carbon instance
        $currentDate = Carbon::parse($date);

        // Get the current month and two previous months
        $months = [];
        for ($i = 0; $i < 3; $i++) {
            $months[] = [
                'year' => $currentDate->copy()->subMonths($i)->format('Y'),
                'month' => $currentDate->copy()->subMonths($i)->format('m')
            ];
        }

        // Fetch images for the current and previous two months
        $images = MonthlyImage::where('user_id', $userId)
            ->where(function ($query) use ($months) {
                foreach ($months as $month) {
                    $query->orWhere(function ($query) use ($month) {
                        $query->whereMonth('month', $month['month'])
                            ->whereYear('month', $month['year']);
                    });
                }
            })
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->month)->format('Y-m'),
                    'front_image' => $item->front_image,
                    'side_image' => $item->side_image,
                    'back_image' => $item->back_image,
                ];
            })
            ->sortBy('month') // Sort by month
            ->values(); // Reindex the array to reset the keys

        // Return the images as a JSON response
        return response()->json($images);
    }
    public function handleNextData(Request $request)
    {
        // Validate the request data
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'id'   => 'required|integer|exists:users,id'
        ]);

        // Retrieve the date and user ID from the request
        $date = $request->input('date');
        $userId = $request->input('id');

        // Convert the date to a Carbon instance
        $currentDate = Carbon::parse($date);

        // Get the current month and two next months
        $months = [];
        for ($i = 0; $i < 3; $i++) {
            $months[] = [
                'year' => $currentDate->copy()->addMonths($i)->format('Y'),
                'month' => $currentDate->copy()->addMonths($i)->format('m')
            ];
        }

        // Fetch images for the current and next two months
        $images = MonthlyImage::where('user_id', $userId)
            ->where(function ($query) use ($months) {
                foreach ($months as $month) {
                    $query->orWhere(function ($query) use ($month) {
                        $query->whereMonth('month', $month['month'])
                            ->whereYear('month', $month['year']);
                    });
                }
            })
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::parse($item->month)->format('Y-m'),
                    'front_image' => $item->front_image,
                    'side_image' => $item->side_image,
                    'back_image' => $item->back_image,
                ];
            })
            ->sortBy('month') // Sort by month
            ->values(); // Reindex the array to reset the keys

        // Return the images as a JSON response
        return response()->json($images);
    }

    // Display new profile creation form
    public function viewnewprofile()
    {
        return view('user.user.new-profile');
    }

    // Handle form submission for creating a new profile
    public function newProfileShow(Request $request)
    {
        // dd($request);
        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|in:Male,Female',
            'age' => 'required|integer|min:0',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'height' => 'required|integer|min:0',
            'weight' => 'required|integer|min:0',
            'bmr' => 'required|numeric|min:0',
            'primary-goal' => 'required|string|max:255',
            'subscription-level' => 'required|string|max:255',
            'profile_image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB per file
        ]);

        // Handle  image uploads
        $imagePaths = [];
        if ($request->hasFile('profile_image')) {
            foreach ($request->file('profile_image') as $file) {
                $imageName = time() . '-' . $file->getClientOriginalName();
                $filePath = $file->storeAs('profile_images', $imageName, 'public');
                $imagePaths[] = $filePath;
            }
        }

        // Generate 4-digit PIN
        $pin = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Create new user
        $user = new User();
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->pin = $pin;
        $user->user_type = 'client'; // Default user type
        $user->save();

        // Create new profile associated with the user
        $profile = new Newprofile();
        $profile->user_id = $user->id;
        $profile->name = $validatedData['name'];
        $profile->nickname = $validatedData['nickname'];
        $profile->dob = $validatedData['dob'];
        $profile->gender = $validatedData['gender'];
        $profile->age = $validatedData['age'];
        $profile->phone = $validatedData['phone'];
        $profile->email = $validatedData['email'];
        $profile->address = $validatedData['address'];
        $profile->height = $validatedData['height'];
        $profile->weight = $validatedData['weight'];
        $profile->bmr = $validatedData['bmr'];
        $profile->primary_goal = $validatedData['primary-goal'];
        $profile->subscription_level = $validatedData['subscription-level'];
        $profile->image_paths = json_encode($imagePaths); // Store image paths in JSON format
        // dd($profile);
        $profile->save();

        // Optionally, you can return a response or redirect
        return redirect()->route('userprofile')->with('success', 'Profile created successfully!');
    }
}
