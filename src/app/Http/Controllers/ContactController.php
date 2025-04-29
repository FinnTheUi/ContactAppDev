<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controller;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the contacts.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category_id');

        // Get categories for the dropdown based on the current user
        $categories = Category::forUser(Auth::id())->get();

        // Fetch contacts, with optional search and category filter
        $contacts = Contact::query()
            ->where('user_id', Auth::id())
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($categoryFilter, function ($query, $categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->orderBy('name')
            ->get();

        // Return the view with the contacts and categories
        return view('contacts.index', compact('contacts', 'categories'));
    }

    /**
     * Show the form for editing a contact.
     */
    public function edit(Contact $contact)
    {
        $this->authorizeContact($contact);

        // Get categories for the dropdown based on the current user
        $categories = Category::forUser(Auth::id())->get();

        return view('contacts.edit', compact('contact', 'categories'));
    }

    /**
     * Update an existing contact.
     */
    public function update(Request $request, Contact $contact)
    {
        $this->authorizeContact($contact);

        $request->validate([
            'name' => ['required', 'regex:/^[A-Za-z0-9 ]+$/', 'max:255'],
            'email' => ['required', 'regex:/^[A-Za-z0-9]{1,15}@gmail\.com$/'],
            'phone' => ['required', 'regex:/^\+63\d{8,9}$/'],
        ]);

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'category_id' => $request->category_id, // Make sure category is updated
        ]);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    /**
     * Delete a contact.
     */
    public function destroy(Contact $contact)
    {
        $this->authorizeContact($contact);

        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    /**
     * Export contacts to CSV.
     */
    public function export()
    {
        $contacts = Contact::where('user_id', Auth::id())->get();

        $csvFileName = 'contacts_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        return response()->stream(function () use ($contacts) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone']);

            foreach ($contacts as $contact) {
                fputcsv($handle, [$contact->name, $contact->email, $contact->phone]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Authorize the contact to make sure it belongs to the user.
     */
    private function authorizeContact(Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action. You do not own this contact.');
        }
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:business,personal',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Category added successfully!');
    }

    /**
     * Display a listing of the categories.
     */
    public function indexCategories()
    {
        $categories = Category::where('user_id', Auth::id())->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function createCategory()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created contact.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'regex:/^[A-Za-z0-9 ]+$/', 'max:255'],
            'email' => ['required', 'regex:/^[A-Za-z0-9]{1,15}@gmail\.com$/'],
            'phone' => ['required', 'regex:/^\+63\d{8,9}$/'],
        ], [
            'name.regex' => 'Name must contain only letters and numbers.',
            'email.regex' => 'Email must contain only letters and numbers (max 15) and end with @gmail.com.',
            'phone.regex' => 'Phone must start with +63 and be 11 to 12 characters total.',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_id' => Auth::id(),
            'category_id' => $request->category_id, // Add category assignment here
        ]);

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    /**
     * Show a specific contact.
     */
    public function show(Contact $contact)
    {
        $this->authorizeContact($contact);

        return view('contacts.show', compact('contact'));
    }
}
