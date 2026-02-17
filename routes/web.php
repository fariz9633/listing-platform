<?php

use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public Routes
Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');

// Enquiry Routes
Route::get('/listings/{listing:slug}/enquire', [EnquiryController::class, 'create'])->name('enquiries.create');
Route::post('/listings/{listing:slug}/enquire', [EnquiryController::class, 'store'])->name('enquiries.store');

// Provider Dashboard Routes 
Route::prefix('provider')->name('provider.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');

    Route::get('/listings/create', [ProviderDashboardController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ProviderDashboardController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ProviderDashboardController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ProviderDashboardController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ProviderDashboardController::class, 'destroy'])->name('listings.destroy');
    Route::post('/listings/{listing}/submit', [ProviderDashboardController::class, 'submit'])->name('listings.submit');
});

// Admin Moderation Routes 
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
    Route::get('/moderation/{listing}', [ModerationController::class, 'show'])->name('moderation.show');
    Route::post('/moderation/{listing}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('/moderation/{listing}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');
    Route::post('/moderation/{listing}/suspend', [ModerationController::class, 'suspend'])->name('moderation.suspend');
});

// ============================================================================
// TESTING ROUTES 
// ============================================================================

Route::get('/test-login/{email}', function ($email) {
    $user = \App\Models\User::where('email', $email)->first();

    // if (!$user) {
    //     return response()->json([
    //         'error' => 'User not found',
    //         'available_users' => [
    //             'admin' => 'admin@example.com',
    //             'providers' => ['sarah@example.com', 'emma@example.com', 'olivia@example.com'],
    //             'guests' => ['guest1@example.com', 'guest2@example.com'],
    //         ]
    //     ], 404);
    // }

    Auth::login($user);

    // Redirect based on role
    if ($user->isAdmin()) {
        return redirect('/admin/moderation')->with('success', 'Logged in as Admin: ' . $user->name);
    } elseif ($user->isProvider()) {
        return redirect('/provider/dashboard')->with('success', 'Logged in as Provider: ' . $user->name);
    } else {
        return redirect('/listings')->with('success', 'Logged in as Guest: ' . $user->name);
    }
})->name('test.login');

// Quick logout
Route::get('/test-logout', function () {
    Auth::logout();
    return redirect('/')->with('success', 'Logged out successfully');
})->name('test.logout');

// Login page (simple form)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
