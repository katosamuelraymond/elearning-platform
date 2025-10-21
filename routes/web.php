<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Public routes
Route::get('/', function () {
    return view('welcome', [
        'showNavbar' => true,
        'showSidebar' => auth()->check(), // Show sidebar only if logged in
        'showFooter' => true
    ]);
})->name('home');

// Auth routes (for guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

});

    Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Include role-specific route files
Route::middleware('auth')->group(function () {
    require __DIR__.'/admin.php';
    require __DIR__.'/teacher.php';
    require __DIR__.'/student.php';
});
