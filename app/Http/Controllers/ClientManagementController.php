<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Access;
use App\Models\Warmup;
use App\Models\Newprofile;
use Illuminate\Http\Request;
use App\Models\WorkoutLibrary;
use App\Models\ClientManagement;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientManagementController extends Controller
{
    public function viewClientManagement()
    {
        $userId = Auth::id(); // Get the currently authenticated user's ID

        // Fetch the access record for the user
        $access = Access::where('user_id', $userId)->first();
        $members = Newprofile::all();

        if ($access && $access->client_management === 'enable') {
            // Pass the access type to the view using compact
            $accessType = $access->access_type;
            return view('admin.user.client_management', compact('accessType', 'members'));
        } else {
            // Redirect to an unauthorized access view
            return view('error.unauthorized');
        }
    }

    public function newProfileclientShow($action = 'add', $id = null)
    {
        // Debug to see what's being passed
        // dd($action, $id);

        // Fetch data if editing an existing profile
        if ($action == 'edit' && $id) {
            $member = Newprofile::findOrFail($id);
        } else {
            // Set $member to null or create a new instance if adding a new profile
            $member = null;
        }

        // Return view with data
        return view('admin.user.client-newprofile', compact('action', 'member'));
    }

    public function clientview($id)
    {
        $member = Newprofile::findOrFail($id);
        // Return view with data
        return view('admin.user.client-profileview', compact('member'));
    }

    // add new function
    public function addnewclient(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'nullable|string|max:255',
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
                'subscription_level' => 'required|string|max:255',
                'profile_image.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB per file
            ]);

            // Check if the email already exists
            if (User::where('email', $validatedData['email'])->exists()) {
                return redirect()->back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
            }

            // Handle image uploads
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
            $user->name = $validatedData['firstname'];
            $user->email = $validatedData['email'];
            $user->pin = $pin;
            $user->user_type = 'client'; // Default user type
            $user->save();

            // Create new profile associated with the user
            $profile = new Newprofile();
            $profile->user_id = $user->id;
            $profile->firstname = $validatedData['firstname'];
            $profile->lastname = $validatedData['lastname'];
            $profile->dob = Carbon::parse($validatedData['dob']);
            $profile->gender = $validatedData['gender'];
            $profile->age = $validatedData['age'];
            $profile->phone = $validatedData['phone'];
            $profile->email = $validatedData['email'];
            $profile->address = $validatedData['address'];
            $profile->height = $validatedData['height'];
            $profile->weight = $validatedData['weight'];
            $profile->bmr = $validatedData['bmr'];
            $profile->primary_goal = $validatedData['primary-goal'];
            $profile->subscription_level = $validatedData['subscription_level'];
            $profile->image_paths = json_encode($imagePaths); // Store image paths in JSON format
            $profile->save();

            // Optionally, you can return a response or redirect
            return redirect()->route('viewadminclientmanagement')->with('success', 'Profile created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    // update function
    public function updatenewclient(Request $request, $id)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'nullable|string|max:255',
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
                'subscription_level' => 'required|string|max:255',
                'profile_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Fetch the profile to update
            $profile = Newprofile::findOrFail($id);

            // Update user associated with the profile if email changes
            if ($profile->email !== $validatedData['email']) {
                if (User::where('email', $validatedData['email'])->exists()) {
                    return redirect()->back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
                }
                $profile->user->update(['email' => $validatedData['email']]);
            }

            // Delete old images if any
            $oldImagePaths = $profile->image_paths ? json_decode($profile->image_paths, true) : [];
            foreach ($oldImagePaths as $oldImagePath) {
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            // Handle image uploads if provided
            $imagePaths = [];
            if ($request->hasFile('profile_image')) {
                foreach ($request->file('profile_image') as $file) {
                    $imageName = time() . '-' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('profile_images', $imageName, 'public');
                    $imagePaths[] = $filePath;
                    \Log::info('Uploaded file path: ' . $filePath);
                }
            }

            \Log::info('Image paths before update: ', $imagePaths);

            // Update the profile data
            $updateSuccessful = $profile->update([
                'firstname' => $validatedData['firstname'],
                'lastname' => $validatedData['lastname'],
                'dob' => Carbon::parse($validatedData['dob']),
                'gender' => $validatedData['gender'],
                'age' => $validatedData['age'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'],
                'height' => $validatedData['height'],
                'weight' => $validatedData['weight'],
                'bmr' => $validatedData['bmr'],
                'primary_goal' => $validatedData['primary-goal'],
                'subscription_level' => $validatedData['subscription_level'],
                'image_paths' => json_encode($imagePaths),
            ]);

            \Log::info('Profile data after update: ', $profile->toArray());

            if ($updateSuccessful) {
                return redirect()->route('viewadminclientmanagement')->with('success', 'Profile updated successfully!');
            } else {
                return redirect()->back()->withErrors(['error' => 'Profile update failed.'])->withInput();
            }
        } catch (\Exception $e) {
            \Log::error('Error updating profile: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    // delete profile
    public function deleteProfile($id)
    {
        // dd($id);
        try {
            // Find the user by ID
            $user = Newprofile::findOrFail($id);

            // Delete the user (this will also delete the associated profile if set up with foreign key constraints)
            $user->delete();

            // Redirect with a success message
            return redirect()->route('viewadminclientmanagement')->with('success', 'Profile deleted successfully!');
        } catch (\Exception $e) {
            // Redirect with an error message
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
