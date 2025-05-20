<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'regex:/^[A-Za-z0-9 ]+$/',
                    'max:255',
                    'unique:categories,name,NULL,id,user_id,' . Auth::id()
                ],
                'type' => 'required|in:business,personal',
            ], [
                'name.required' => 'Category name cannot be empty.',
                'name.regex' => 'Category name cannot contain special characters.',
                'name.unique' => 'Category name already exists.',
                'type.required' => 'Category type is required.',
                'type.in' => 'Category type must be either business or personal.',
            ]);

            $category = Category::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'user_id' => Auth::id(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Category added successfully!',
                    'category' => $category
                ], 201);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Category added successfully!');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
    }    public function update(Request $request, Category $category)
    {
        try {
            // Check if the category belongs to the authenticated user
            if ($category->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }

            $validated = $request->validate([
                'name' => [
                    'required',
                    'regex:/^[A-Za-z0-9 ]+$/',
                    'max:255',
                    'unique:categories,name,' . $category->id . ',id,user_id,' . Auth::id()
                ],
                'type' => 'required|in:business,personal',
            ], [
                'name.required' => 'Category name cannot be empty.',
                'name.regex' => 'Category name cannot contain special characters.',
                'name.unique' => 'Category name already exists.',
                'type.required' => 'Category type is required.',
                'type.in' => 'Category type must be either business or personal.',
            ]);

            $category->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Category updated successfully!',
                    'category' => $category
                ]);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Category updated successfully!');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }
    }

    public function destroy(Category $category)
    {
        // Check if the category belongs to the authenticated user
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Check if the category has any associated contacts
        if ($category->contacts()->exists()) {
            return response()->json([
                'error' => 'Cannot delete a category with associated contacts.'
            ], 400);
        }

        $category->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Category deleted successfully.'
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Category deleted successfully.');
    }
}
