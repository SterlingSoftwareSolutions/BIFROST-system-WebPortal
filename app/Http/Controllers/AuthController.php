<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    // login
    public function login(Request $request)
    {
        // dd($request);
        try {
            // Validate the request
            $request->validate([
                'pin_1' => 'required|integer|min:0|max:9',
                'pin_2' => 'required|integer|min:0|max:9',
                'pin_3' => 'required|integer|min:0|max:9',
                'pin_4' => 'required|integer|min:0|max:9',
            ]);
            $pin = $request->pin_1 . $request->pin_2 . $request->pin_3 . $request->pin_4;

            $user = User::where(function ($query) use ($request) {
                if ($request->portal === 'admin') {
                    $query->whereIn('user_type', ['admin', 'super admin']);
                } else {
                    $query->where('user_type', 'client')->orWhere('user_type', 'worker');
                }
            })
                ->where('pin', $pin)
                ->first();


            if ($user) {
                // Log the user in
                Auth::login($user);
                if ($request->type == "mobile") {
                    if ($user->user_type == "client") {
                        return redirect()->route('mobile.trainingday');
                    } else {
                        return redirect()->back()->with('error', 'Please enter a correct pin number.');
                    }
                } elseif ($request->type == "web") {
                    if ($user->user_type == "admin" || $user->user_type == "super admin") {
                        return redirect()->route('admin.dashboard');
                    } elseif ($user->user_type == "client") {
                        return redirect()->route('user.dashboard');
                    } else {
                        return redirect()->back()->with('error', 'Please enter a correct pin number.');
                    }
                } else {
                    return redirect()->back()->with('error', 'Something is wrong.');
                }
            }


            // If no user is found, redirect back with an error message
            return redirect()->back()->with('error', 'Please enter a correct pin number.');
        } catch (Exception $e) {
            // return redirect()->back();
            return redirect()->back()->with('error', 'Please enter a correct pin number.');
        }
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }
    // log out
    public function logout(Request $request)
    {
        Auth::logout();

        // Clear all session data
        session()->flush();

        if ($request->type == "web") {
            return redirect('/'); // Redirect to the homepage or any other page
        } elseif ($request->type == "mobile") {
            return redirect('/mobile/login');
        }
    }
}
