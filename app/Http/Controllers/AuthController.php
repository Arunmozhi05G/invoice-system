<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    // Display Login Page
    public function showLogin(): View
    {
        return view('auth.login');
    }

    // Handle Login
    public function login(Request $request): RedirectResponse
    {
        // 1. Validate
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Authenticate
        if (Auth::attempt($credentials)) {

            // 3. Regenerate Session
            $request->session()->regenerate();

            // 4. Redirect to Dashboard
            return redirect()->intended('/dashboard');
        }

        // Login Failed
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    // Handle Logout
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
