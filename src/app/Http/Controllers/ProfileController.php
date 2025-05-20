<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('profile.show', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = Auth::user();
        
        // Do validation first, before starting transaction
        try {
            $validated = $this->validateProfileData($request, $user);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->updateBasicProfile($user, $validated);

            // Handle password update if provided
            if ($request->has('password')) {
                $this->updatePassword($user, $request);
            }

            // Handle profile image
            if ($request->hasFile('profile_image')) {
                $this->handleProfileImage($user, $request->file('profile_image'));
            }

            // Handle image removal
            if ($request->boolean('remove_image')) {
                $this->removeProfileImage($user);
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile_image' => $user->profile_image
                ]);
            
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Profile updated successfully',
                    'profile_image' => $user->profile_image ? asset($user->profile_image) : null,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone
                    ]
                ], 200);
            }

            return redirect()->route('profile.show')
                ->with('success', 'Profile updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error updating profile',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    /**
     * Validate the profile update request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return array
     */
    private function validateProfileData(Request $request, $user)
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => [
                'required',
                'regex:/^(\+63|09)\d{9}$/',
                Rule::unique('users')->ignore($user->id)
            ],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];

        if ($request->has('current_password') || $request->has('password')) {
            $rules['current_password'] = ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The current password is incorrect.');
                }
            }];
            $rules['password'] = [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/'
            ];
        }

        return $request->validate($rules, [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Your name cannot exceed 255 characters.',
            'name.regex' => 'Your name can only contain letters and spaces.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different email.',
            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Please enter a valid Philippine mobile number starting with 09 or +63.',
            'phone.unique' => 'This phone number is already registered.',
            'current_password.required' => 'The current password is required.',
            'password.required' => 'The new password is required.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
        ]);
    }

    /**
     * Update the basic profile information.
     *
     * @param  \App\Models\User  $user
     * @param  array  $validated
     * @return void
     */
    private function updateBasicProfile($user, $validated)
    {
        $user->name = trim($validated['name']);
        $user->email = strtolower(trim($validated['email']));
        $user->phone = trim($validated['phone']);
    }

    /**
     * Update the user's password.
     *
     * @param  \App\Models\User  $user
     * @param  array  $validated
     * @return void
     */
    private function updatePassword($user, $request)
    {
        $user->password = Hash::make($request->password);
    }

    /**
     * Handle profile image upload.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return void
     * @throws \Exception
     */
    private function handleProfileImage($user, $file)
    {
        try {
            // Delete old image if exists
            if ($user->profile_image) {
                $this->removeProfileImage($user);
            }

            // Get the original file extension
            $extension = $file->getClientOriginalExtension();
            
            // Validate file extension
            if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new \Exception('Invalid image format. Please use JPEG, PNG, JPG, or GIF.');
            }

            // Generate unique filename with timestamp and original extension
            $filename = 'profile-' . time() . '-' . Str::random(10) . '.' . $extension;
            $directory = 'images/profile-images';

            // Ensure the directory exists
            if (!File::exists(public_path($directory))) {
                File::makeDirectory(public_path($directory), 0755, true);
            }

            // Store new image
            $file->move(public_path($directory), $filename);
            $user->profile_image = $directory . '/' . $filename;

        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove the user's profile image.
     *
     * @param  \App\Models\User  $user
     * @return void
     * @throws \Exception
     */
    private function removeProfileImage($user)
    {
        try {
            if ($user->profile_image) {
                $imagePath = public_path($user->profile_image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $user->profile_image = null;
            }
        } catch (\Exception $e) {
            Log::error('Image removal failed: ' . $e->getMessage());
            throw new \Exception('Failed to remove profile image. Please try again.');
        }
    }
}