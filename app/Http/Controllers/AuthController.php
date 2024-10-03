<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            's_number' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['s_number' => $request->s_number, 'password' => $request->password])) {
            return redirect()->route('home');
        }

        return redirect()->route('login')->withErrors(['login_error' => 'Invalid credentials']);
    }

    // Handle logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }


     // Show registration form
     public function showRegisterForm()
     {
         return view('auth.register');
     }
 
     // Handle registration
     public function register(Request $request)
     {
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             's_number' => 'required|string|unique:users',
             'password' => 'required|string|min:6|confirmed',
         ]);
 
         User::create([
             'name' => $request->name,
             'email' => $request->email,
             's_number' => $request->s_number,
             'password' => Hash::make($request->password),
             'role' => 'student',  // Default role for new registrations
         ]);
 
         return redirect()->route('login')->with('success', 'Registration successful! You can now login.');
     }
}
