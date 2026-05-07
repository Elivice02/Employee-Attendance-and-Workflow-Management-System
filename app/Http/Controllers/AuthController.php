<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Department;
use App\Mail\NewUserCredentialsMail;
use App\Rules\PasswordPolicy;

class AuthController extends Controller
{
   public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

                $user = Auth::user();

                if ($user->must_change_password) {
            return redirect('/change-password');
        }

            // ROLE REDIRECTION
            switch ($user->role) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'hr':
                    return redirect('/hr/dashboard');
                case 'supervisor':
                    return redirect('/supervisor/dashboard');
                case 'employee':
                    return redirect('/employee/dashboard');
            }
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                new PasswordPolicy(),
            ],
        ], [
            'current_password.required' => 'Current password is required',
            'password.min' => 'Password must be at least 8 characters long',
            'password.confirmed' => 'Passwords do not match',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'must_change_password' => false,
        ]);

        // 🔥 logout to refresh session
        auth()->logout();

        return redirect('/login')->with('success', 'Password updated successfully. Please login again.');
    }
}
