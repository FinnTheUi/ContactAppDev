<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Requires Authentication)
Route::middleware('auth')->group(function () {

    // Dashboard and Recent Contacts
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/recent-contacts', [DashboardController::class, 'recentContacts'])->name('recent.contacts');

    // Category Routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [ContactController::class, 'indexCategories'])->name('index');
        Route::get('/create', [ContactController::class, 'createCategory'])->name('create');
        Route::post('/', [ContactController::class, 'storeCategory'])->name('store');
        Route::delete('/{category}', [ContactController::class, 'destroyCategory'])->name('destroy');
        });
    
    });

    // Contact Routes
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::post('/', [ContactController::class, 'store'])->name('store');
        Route::get('/create', [ContactController::class, 'create'])->name('create');
        Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
        Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
    });


// Default Home Route
Route::get('/', function () {
    return view('home');
})->name('home');
