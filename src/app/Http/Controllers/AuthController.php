<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
        try {
            DB::beginTransaction();

            // Validate the request data
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => ['required', 'regex:/^(\+63|09)\d{9}$/'],
                'password' => 'required|min:8|confirmed',
            ], [
                'name.required' => 'Please enter your full name.',
                'name.max' => 'Your name cannot exceed 255 characters.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered. Please use a different email or try logging in.',
                'phone.required' => 'Please enter your phone number.',
                'phone.regex' => 'Please enter a valid Philippine mobile number starting with 09 or +63.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'Your password must be at least 8 characters long.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            // Create the user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);

            if (!$user) {
                throw new \Exception('Failed to create user account.');
            }

            DB::commit();

            return redirect()->route('login')
                ->with('success', 'Registration successful! You can now login.');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Registration validation failed: ' . json_encode($e->errors()));
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Registration failed: ' . $e->getMessage() . '. Please try again or contact support if the problem persists.'
                ]);
        }
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'Please enter your password.',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
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
}
