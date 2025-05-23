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
            // Validate all fields
            $validator = validator($request->all(), [
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'regex:/^(\+63|09)\d{9}$/', 'unique:users,phone'],
                'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/'],
            ], [
                'name.required' => 'Please enter your full name.',
                'name.max' => 'Your name cannot exceed 255 characters.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already registered.',
                'phone.required' => 'Please enter your phone number.',
                'phone.regex' => 'Please enter a valid Philippine mobile number starting with 09 or +63.',
                'phone.unique' => 'This phone number is already registered.',
                'password.required' => 'Please enter a password.',
                'password.min' => 'Your password must be at least 8 characters long.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            // Check for validation errors before doing custom email validation
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Then do custom email validation
            if ($request->filled('email') && !$this->validateEmail($request->input('email'))) {
                $validator->errors()->add('email', 'Please enter a valid email address.');
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();

            DB::beginTransaction();

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

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Registration successful! You can now login.'
                ], 201);
            }

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

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Registration failed: ' . $e->getMessage()
                ], 500);
            }
            
            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Registration failed: ' . $e->getMessage() . '. Please try again or contact support if the problem persists.'
                ]);
        }
    }

    /**
     * Validate email format.
     */
    protected function validateEmail($email): bool
    {
        if (empty($email)) {
            return false;
        }
        
        // Basic format check
        if (!preg_match("/^[^@]+@[^@]+\.[^@]+$/", $email)) {
            return false;
        }
        
        // Check specific invalid formats
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }
        
        list($local, $domain) = $parts;
        if (empty($local) || empty($domain)) {
            return false;
        }
        
        // Check for double dots
        if (strpos($domain, '..') !== false) {
            return false;
        }
        
        // Check domain has at least one dot and valid TLD
        $domainParts = explode('.', $domain);
        if (count($domainParts) < 2 || empty($domainParts[0]) || empty($domainParts[1])) {
            return false;
        }
        
        return true;
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        try {
            // Validate all fields first
            $validator = validator($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Please enter your password.',
            ]);

            // Check for validation errors before doing custom email validation
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Then do custom email validation
            if ($request->filled('email') && !$this->validateEmail($request->input('email'))) {
                $validator->errors()->add('email', 'Please enter a valid email address.');
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials, $request->filled('remember'))) {
                $request->session()->regenerate();
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Login successful',
                        'user' => Auth::user()
                    ]);
                }
                
                return redirect()->intended('dashboard');
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }
            
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        }

        // Redirect to the login page with a success message
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Check if a phone number is available for registration.
     */
    public function checkPhoneAvailability(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^(\+63|09)\d{9}$/']
        ]);

        $exists = User::where('phone', $request->phone)->exists();
        
        return response()->json([
            'available' => !$exists
        ]);
    }
}
