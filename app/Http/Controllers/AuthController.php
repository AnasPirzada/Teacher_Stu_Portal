<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
