<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

use App\Models\Category;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with the user's contacts.
     */

public function index()
{
    // Ensure the user is authenticated
    if (!Auth::check()) {
        return redirect('/login')->with('error', 'You must be logged in to access the dashboard.');
    }

    // Retrieve the authenticated user's contacts and categories
    $contacts = Contact::where('user_id', Auth::id())->get();
    $categories = Category::where('user_id', Auth::id())->get();

    // Pass contacts and categories to the dashboard view
    return view('dashboard', compact('contacts', 'categories'));
}


    
    
}