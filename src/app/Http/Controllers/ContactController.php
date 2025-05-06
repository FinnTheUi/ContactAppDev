<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

use Illuminate\Routing\Controller;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Display the contact dashboard */
    public function index(Request $request)
    {
        $categories = Category::forUser(Auth::id())->get();
        return view('contacts.index', compact('categories'));
    }

    /** Store a newly created contact */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s]+$/'],
            'phone' => ['required', 'regex:/^(\+63|09)\d{9}$/'],
            'email' => ['required', 'email', 'max:30', 'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'],
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'Please enter the contact\'s name.',
            'name.max' => 'The name cannot exceed 20 characters.',
            'name.regex' => 'Name can only contain letters, numbers, and spaces.',
            'phone.required' => 'Please enter the contact\'s phone number.',
            'phone.regex' => 'Please enter a valid Philippine mobile number starting with 09 or +63.',
            'email.required' => 'Please enter the contact\'s email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 30 characters.',
            'email.regex' => 'Please enter a valid email address format.',
            'category_id.exists' => 'The selected category is invalid.',
        ]);

        // Check for duplicate phone number
        $existingContact = Contact::where('user_id', Auth::id())
            ->where('phone', $request->phone)
            ->first();

        if ($existingContact) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'A contact with this phone number already exists.',
                    'errors' => ['phone' => ['This phone number is already registered in your contacts.']]
                ], 422);
            }
            return back()->withInput()->withErrors(['phone' => 'A contact with this phone number already exists.']);
        }

        // Check for duplicate email
        $existingEmail = Contact::where('user_id', Auth::id())
            ->where('email', $request->email)
            ->first();

        if ($existingEmail) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'A contact with this email already exists.',
                    'errors' => ['email' => ['This email is already registered in your contacts.']]
                ], 422);
            }
            return back()->withInput()->withErrors(['email' => 'A contact with this email already exists.']);
        }

        $categoryId = $request->category_id;
        if (!$categoryId) {
            $uncat = Category::firstOrCreate(
                [
                    'name' => 'Uncategorized',
                    'user_id' => Auth::id(),
                ],
                [
                    'type' => 'personal',
                ]
            );
            $categoryId = $uncat->id;
        }

        try {
            Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'telephone' => $request->telephone,
                'user_id' => Auth::id(),
                'category_id' => $categoryId,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact added successfully!'
                ]);
            }
            return redirect()->route('dashboard')->with('success', 'Contact created successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error creating contact. Please try again.',
                    'errors' => ['general' => ['An unexpected error occurred. Please try again.']]
                ], 500);
            }
            return back()->withInput()->withErrors(['general' => 'Error creating contact. Please try again.']);
        }
    }

    /** Edit a contact */
    public function edit(Contact $contact)
    {
        $this->authorizeContact($contact);
        $categories = Category::forUser(Auth::id())->get();
        return view('contacts.edit', compact('contact', 'categories'));
    }

    /** Update a contact */
    public function update(Request $request, Contact $contact)
    {
        $this->authorizeContact($contact);

        $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s]+$/'],
            'phone' => ['required', 'regex:/^(\+63|09)\d{9}$/'],
            'email' => ['required', 'email', 'max:30', 'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ], [
            'name.required' => 'Please enter the contact\'s name.',
            'name.max' => 'The name cannot exceed 20 characters.',
            'name.regex' => 'Name can only contain letters, numbers, and spaces.',
            'phone.required' => 'Please enter the contact\'s phone number.',
            'phone.regex' => 'Please enter a valid Philippine mobile number starting with 09 or +63.',
            'email.required' => 'Please enter the contact\'s email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 30 characters.',
            'email.regex' => 'Please enter a valid email address format.',
            'category_id.exists' => 'The selected category is invalid.',
        ]);

        // Check for duplicate phone number (excluding current contact)
        $existingPhone = Contact::where('user_id', Auth::id())
            ->where('phone', $request->phone)
            ->where('id', '!=', $contact->id)
            ->first();

        if ($existingPhone) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'A contact with this phone number already exists.',
                    'errors' => ['phone' => ['This phone number is already registered in your contacts.']]
                ], 422);
            }
            return back()->withInput()->withErrors(['phone' => 'A contact with this phone number already exists.']);
        }

        // Check for duplicate email (excluding current contact)
        $existingEmail = Contact::where('user_id', Auth::id())
            ->where('email', $request->email)
            ->where('id', '!=', $contact->id)
            ->first();

        if ($existingEmail) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'A contact with this email already exists.',
                    'errors' => ['email' => ['This email is already registered in your contacts.']]
                ], 422);
            }
            return back()->withInput()->withErrors(['email' => 'A contact with this email already exists.']);
        }

        try {
            $categoryId = $request->category_id;
            if (!$categoryId) {
                $uncat = Category::firstOrCreate([
                    'name' => 'Uncategorized',
                    'user_id' => Auth::id(),
                ], [
                    'type' => 'personal',
                ]);
                $categoryId = $uncat->id;
            }

            $contact->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'telephone' => $request->telephone,
                'category_id' => $categoryId,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contact updated successfully!'
                ]);
            }
            return redirect()->route('dashboard')->with('success', 'Contact updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Error updating contact. Please try again.',
                    'errors' => ['general' => ['An unexpected error occurred. Please try again.']]
                ], 500);
            }
            return back()->withInput()->withErrors(['general' => 'Error updating contact. Please try again.']);
        }
    }

    /** Delete a contact */
    public function destroy(Contact $contact)
    {
        $this->authorizeContact($contact);
        
        try {
            $contact->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('dashboard')->with('success', 'Contact deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Error deleting contact'], 500);
            }
            
            return redirect()->route('dashboard')->with('error', 'Error deleting contact');
        }
    }

    /** Show a contact */
    public function show(Contact $contact)
    {
        $this->authorizeContact($contact);
        return view('contacts.show', compact('contact'));
    }

    /** Get DataTables JSON data */
    public function getData(Request $request)
    {
        $contacts = Contact::with('category')
            ->where('user_id', Auth::id());

        if ($request->filled('category_id')) {
            $contacts->where('category_id', $request->category_id);
        }

        $contacts->select(['id', 'name', 'email', 'phone', 'category_id', 'user_id']);

        return DataTables::of($contacts)
            ->addColumn('category', function($contact) {
                return $contact->category ? $contact->category->name : '—';
            })
            ->addColumn('action', function($contact) {
                return view('partials.actions.contact-actions', compact('contact'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /** Export all user contacts to CSV */
    public function export()
    {
        $contacts = Contact::where('user_id', Auth::id())->get();

        $csvFileName = 'contacts_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        return response()->stream(function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone']);
            foreach ($contacts as $c) {
                fputcsv($handle, [$c->name, $c->email, $c->phone]);
            }
            fclose($handle);
        }, 200, $headers);
    }

    /** CATEGORY SECTION **/

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => ['required', 'regex:/^[A-Za-z0-9 ]+$/', 'max:255', 'unique:categories,name,NULL,id,user_id,' . Auth::id()],
            'type' => 'required|in:business,personal',
        ], [
            'name.required' => 'Category name cannot be empty.',
            'name.regex' => 'Category name cannot contain special characters.',
            'name.unique' => 'Category name already exists.',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Category added successfully!');
    }

    public function destroyCategory($id, Request $request)
    {
        $category = Category::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($category->contacts()->exists()) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Cannot delete a category with associated contacts.'], 400);
            }
            return redirect()->route('contacts.index')
                ->with('error', 'Cannot delete a category with associated contacts.');
        }

        $category->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('contacts.index')->with('success', 'Category deleted successfully.');
    }

    public function indexCategories()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return view('categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('categories.create');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => ['required', 'regex:/^[A-Za-z0-9 ]+$/', 'max:255', 'unique:categories,name,' . $category->id . ',id,user_id,' . Auth::id()],
            'type' => 'required|in:business,personal',
        ], [
            'name.required' => 'Category name cannot be empty.',
            'name.regex' => 'Category name cannot contain special characters.',
            'name.unique' => 'Category name already exists.',
        ]);
        $category->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);
        return redirect()->route('dashboard')->with('success', 'Category updated successfully!');
    }

    /** Ensure contact belongs to the logged-in user */
    protected function authorizeContact(Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
}
