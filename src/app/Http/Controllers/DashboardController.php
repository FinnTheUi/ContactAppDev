<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with the user's contacts and categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // The 'auth' middleware already ensures the user is authenticated

        $contacts = Contact::where('user_id', Auth::id())->get();
        $categories = Category::where('user_id', Auth::id())->get();
        $recentContacts = Contact::with('category')->where('user_id', Auth::id())->latest()->take(5)->get();
        $contactsCount = $contacts->count();

        return view('dashboard', compact('contacts', 'categories', 'recentContacts', 'contactsCount'));
    }

    /**
     * Return recent contacts for use with AJAX (e.g., in widgets or dashboards).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentContacts()
    {
        $recentContacts = Contact::where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return response()->json($recentContacts);
    }
}
