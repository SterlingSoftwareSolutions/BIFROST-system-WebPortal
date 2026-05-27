<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\User;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    // view admin access page
    public function viewaccess()
    {
        // Fetch the first 5 users with their access types
        $users = User::join('accesses', 'users.id', '=', 'accesses.user_id')
            ->select('users.*', 'accesses.*')
            ->whereIn('users.user_type', ['admin', 'super admin'])
            ->orderBy('accesses.id', 'desc')
            ->take(5)
            ->get();

        // dd($users);
        return view('admin.user.access', compact('users'));
    }

    // update user name and email
    public function setData(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $id = $request->input('id');

        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'message' => 'Access record not found'
            ], 404);
        }

        $user = User::find($access->user_id);

        if ($user) {
            // Update user's name and email
            $user->name = $name;
            $user->email = $email;
            $user->save(); // Save the changes

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    // delete user
    public function deleteData(Request $request)
    {
        $id = $request->input('id');
        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'message' => 'Access record not found'
            ], 404);
        }

        $user = User::find($access->user_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    // reset user pin number
    public function resetPin(Request $request)
    {
        $id = $request->input('id');
        $customPin = $request->input('pin');
        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'success' => false,
                'message' => 'Access record not found'
            ], 404);
        }

        $user = User::find($access->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        if (!empty($customPin)) {
            // Check if the custom pin is already in use by another user
            if (User::where('pin', $customPin)->where('id', '!=', $user->id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This PIN is already in use. Please choose another one.'
                ]);
            }
            $pin = $customPin;
        } else {
            // Generate a unique 4-digit random PIN
            do {
                $pin = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
            } while (User::where('pin', $pin)->exists());
        }

        // Assuming you have a 'pin' field in your User model to store the PIN
        $user->pin = $pin;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'PIN reset successfully',
            'pin' => $pin,
            'user' => $user
        ]);
    }
    // update access type
    public function updateAccess(Request $request)
    {
        $id = $request->input('id');
        $accessType = $request->input('access_type');

        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'message' => 'Access record not found'
            ], 404);
        }

        $access->access_type = $accessType;
        $access->save();

        return response()->json([
            'message' => 'Access type updated successfully',
            // 'access' => $access
        ]);
    }

    //  update access page
    public function updateAccessPage(Request $request)
    {
        // Validate the input
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string',
        ]);

        $id = $request->input('id');
        $name = $request->input('name');

        $access = Access::find($id);

        if (!$access) {
            return response()->json([
                'message' => 'Access record not found'
            ], 404);
        }

        switch ($name) {
            case 'dashboard':
                $access->dashboard = $access->dashboard === 'enable' ? 'disable' : 'enable';
                break;
            case 'access':
                $access->access = $access->access === 'enable' ? 'disable' : 'enable';
                break;
            case 'client_management':
                $access->client_management = $access->client_management === 'enable' ? 'disable' : 'enable';
                break;
            case 'workout_library':
                $access->workout_library = $access->workout_library === 'enable' ? 'disable' : 'enable';
                break;
            case 'session':
                $access->session = $access->session === 'enable' ? 'disable' : 'enable';
                break;
            case 'financial':
                $access->financial = $access->financial === 'enable' ? 'disable' : 'enable';
                break;
            case 'communication':
                $access->communication = $access->communication === 'enable' ? 'disable' : 'enable';
                break;
            case 'statistics':
                $access->statistics = $access->statistics === 'enable' ? 'disable' : 'enable';
                break;
            case 'user_dashboard':
                $access->user_dashboard = $access->user_dashboard === 'enable' ? 'disable' : 'enable';
            case 'profile':
                $access->profile = $access->profile === 'enable' ? 'disable' : 'enable';
                break;
            case 'goals':
                $access->goals = $access->goals === 'enable' ? 'disable' : 'enable';
                break;
            case 'achievements':
                $access->achievements = $access->achievements === 'enable' ? 'disable' : 'enable';
                break;
            case 'settings':
                $access->settings = $access->settings === 'enable' ? 'disable' : 'enable';
                break;
            default:
                return response()->json([
                    'message' => 'Invalid column name'
                ], 400);
        }

        $access->save();

        return response()->json([
            'message' => 'Access type updated successfully',
            'access' => $access
        ]);
    }

    // next button function
    public function nextShow($id)
    {
        // dd($id);
        $users = User::join('accesses', 'users.id', '=', 'accesses.user_id')
            ->select('users.*', 'accesses.*')
            ->where('accesses.id', '>=', $id)
            ->orderBy('accesses.id', 'asc') // Order by user id in ascending order
            ->take(5)
            ->get();


        // dd($users);
        if ($users->isEmpty()) {
            return redirect()->back();
        }

        return view('admin.user.access', compact('users'));
    }

    // previous button function
    public function previousShow($id)
    {
        $users = User::join('accesses', 'users.id', '=', 'accesses.user_id')
            ->select('users.*', 'accesses.*')
            ->where('accesses.id', '<', $id)
            ->orderByDesc('accesses.id') // Order by accesses id in descending order
            ->take(5)
            ->get();

        if ($users->isEmpty()) {
            return redirect()->route('admindaaccess')->with('error', 'No users found.');
        }

        return view('admin.user.access', compact('users'));
    }

    //add new admin user
    public function newAdminShow($action = 'add', $id = null)
    {
        // Fetch data if editing an existing profile
        if ($action == 'edit' && $id) {
            $user = User::findOrFail($id);
            $pin = $user->pin;
            $access = Access::where('user_id', $user->id)->first();
        } else {
            // Set variables to null if adding a new profile
            $user = null;
            $pin = null;
            $access = null;
        }
        // Return view with data
        return view('admin.user.admin-newuser', compact('action', 'user', 'pin', 'access'));
    }

    public function addnewadmin(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'pin' => ['required', 'regex:/^[0-9]+$/', 'min:0', 'unique:users,pin'],
            ], [
                'pin.unique' => 'The PIN has already been taken. Please choose another one.',
                'email.unique' => 'The email has already been taken.'
            ]);

            $pin = $validatedData['pin'];

            // Create new user
            $user = new User();
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->pin = $pin;
            $user->user_type = 'admin';
            $user->save();

            $accessFields = $request->input('access_fields', []);
            $access = new Access();
            $access->user_id = $user->id;

            $access->dashboard = isset($accessFields['dashboard']) ? 'enable' : 'disable';
            $access->access = isset($accessFields['access']) ? 'enable' : 'disable';
            $access->client_management = isset($accessFields['client_management']) ? 'enable' : 'disable';
            $access->workout_library = isset($accessFields['workout_library']) ? 'enable' : 'disable';
            $access->session = isset($accessFields['session']) ? 'enable' : 'disable';
            $access->financial = isset($accessFields['financial']) ? 'enable' : 'disable';
            $access->communication = isset($accessFields['communication']) ? 'enable' : 'disable';
            $access->statistics = isset($accessFields['statistics']) ? 'enable' : 'disable';
            $access->save();

            return redirect()->route('admindaaccess')->with('success', 'New Admin created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateadmin(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validate incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'pin' => ['required', 'regex:/^[0-9]+$/', 'min:0', 'unique:users,pin,' . $id],
            ], [
                'pin.unique' => 'The PIN has already been taken. Please choose another one.',
                'email.unique' => 'The email has already been taken.'
            ]);

            // Update user
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->pin = $validatedData['pin'];
            $user->save();

            // Update access fields
            $accessFields = $request->input('access_fields', []);
            $access = Access::where('user_id', $user->id)->first();
            if (!$access) {
                $access = new Access();
                $access->user_id = $user->id;
            }

            $access->dashboard = isset($accessFields['dashboard']) ? 'enable' : 'disable';
            $access->access = isset($accessFields['access']) ? 'enable' : 'disable';
            $access->client_management = isset($accessFields['client_management']) ? 'enable' : 'disable';
            $access->workout_library = isset($accessFields['workout_library']) ? 'enable' : 'disable';
            $access->session = isset($accessFields['session']) ? 'enable' : 'disable';
            $access->financial = isset($accessFields['financial']) ? 'enable' : 'disable';
            $access->communication = isset($accessFields['communication']) ? 'enable' : 'disable';
            $access->statistics = isset($accessFields['statistics']) ? 'enable' : 'disable';
            $access->save();

            return redirect()->route('admindaaccess')->with('success', 'Admin updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }
}
