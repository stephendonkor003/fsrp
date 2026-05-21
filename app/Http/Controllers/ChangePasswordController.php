<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordChangedNotification;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }



    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        $newPassword = $request->password;

        $user->update([
            'password' => Hash::make($newPassword),
            'must_change_password' => false,
        ]);

        // Send notification email
        Mail::to($user->email)->send(new PasswordChangedNotification($user, $newPassword));

        // Logout user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Password updated successfully. You have been logged out for security. Please log in again.');
    }

}