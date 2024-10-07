<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Google2FA;

class TwoFactorAuthController extends Controller
{
    // Show the 2FA setup page
    public function show2FASetupForm()
    {
        $user = auth()->user();

        if (!$user->google2fa_secret) {
            // Generate a new secret
            $user->google2fa_secret = Google2FA::generateSecretKey();
            $user->save();
        }

        // Generate the QR code for Google Authenticator
        $QR_Image = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return view('auth.2fa_setup', ['QR_Image' => $QR_Image, 'secret' => $user->google2fa_secret]);
    }

    // Enable 2FA
    public function enable2FA(Request $request)
    {
        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        // Verify the code entered by the user
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('2fa_code'));

        if ($valid) {
            $user->2fa_enabled = true;
            $user->save();
            return redirect('/home')->with('success', '2FA enabled successfully.');
        } else {
            return redirect()->back()->withErrors('Invalid 2FA code, try again.');
        }
    }

    // Verify 2FA during login
    public function verify2FA(Request $request)
    {
        $user = auth()->user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('2fa_code'));

        if ($valid) {
            // 2FA success
            $request->session()->put('2fa_verified', true);
            return redirect('/home');
        } else {
            // 2FA failed
            return redirect()->back()->withErrors('Invalid 2FA code, try again.');
        }
    }
}