<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        // Validate the registration form
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z0-9 ]+$/', // Only letters, numbers, and spaces
            'email' => 'required|string|unique:users,email|regex:/^[a-zA-Z0-9_]+@gmail\.com$/', // Must end with @gmail.com, only numbers and no special characters
            'phone' => 'required|regex:/^\d{9}$/', // Must be exactly 9 digits
            'password' => 'required|string|min:8|confirmed', // Password confirmation required
            
        ]); 
          

        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => '+63' . $request->phone, // Add +63 prefix to the phone number
            'password' => Hash::make($request->password), // Hash the password
        ]);

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Account created successfully. Please log in.');
    }


    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // Validate the login form
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if the input is an email or username
        $credentials = filter_var($request->email, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->email, 'password' => $request->password]
            : ['name' => $request->email, 'password' => $request->password];

        // Attempt to log in the user
        if (Auth::attempt($credentials)) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();

            // Redirect to the dashboard
            return redirect('/dashboard')->with('success', 'Login successful. Welcome!');
        }

        // If login fails, redirect back with an error message
        return back()->withErrors([
            'email' => 'Invalid credentials or account does not exist.',
        ])->withInput();
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Log out the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the CSRF token
        $request->session()->regenerateToken();

        // Redirect to the login page with a success message
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }
};