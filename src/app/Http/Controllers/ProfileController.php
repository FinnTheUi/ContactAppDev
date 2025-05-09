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
        DB::beginTransaction();
        try {
            $user = Auth::user();

            // Validate the request data
            $validated = $this->validateProfileData($request, $user);

            // Update basic profile information
            $this->updateBasicProfile($user, $validated);

            // Handle password update if provided
            if ($request->filled('new_password')) {
                $this->updatePassword($user, $validated);
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

            return response()->json([
                'message' => 'Profile updated successfully',
                'profile_image' => $user->profile_image ? asset($user->profile_image) : null,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone
                ]
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Profile update failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'An error occurred while updating your profile. Please try again.'
            ], 500);
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
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => ['required', 'regex:/^(\+63|09)\d{9}$/'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];

        // Add password validation rules if password fields are filled
        if ($request->filled('current_password') || $request->filled('new_password')) {
            $rules['current_password'] = [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                }
            ];
            $rules['new_password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ];
        }

        $messages = [
            'name.required' => 'Please enter your full name.',
            'name.max' => 'Your name cannot exceed 255 characters.',
            'name.regex' => 'Your name can only contain letters and spaces.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered. Please use a different email.',
            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Please enter a valid Philippine mobile number starting with 09 or +63.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'Your password must be at least 8 characters long.',
            'new_password.confirmed' => 'The password confirmation does not match.',
            'new_password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'profile_image.image' => 'The file must be an image.',
            'profile_image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'profile_image.max' => 'The image may not be greater than 2MB.',
        ];

        return $request->validate($rules, $messages);
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
    private function updatePassword($user, $validated)
    {
        $user->password = Hash::make($validated['new_password']);
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