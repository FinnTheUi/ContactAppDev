<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;

// Authentication Routes

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard and Contacts Routes (Protected by Auth Middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/recent-contacts', [DashboardController::class, 'recentContacts'])->name('recent.contacts');
   
    // Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [ContactController::class, 'indexCategories'])->name('index');
        Route::get('/create', [ContactController::class, 'createCategory'])->name('create');
        Route::post('/', [ContactController::class, 'storeCategory'])->name('store');
    });
});
Route::middleware(['auth'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');  // This handles form submission
    Route::get('/contacts/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');

});

// Default Home Route
Route::get('/', function () {
    return view('home');
})->name('home');