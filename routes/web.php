<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Template\Category;
use App\Livewire\Template\Template;
use App\Livewire\Ticket\Ticket;
use App\Livewire\User\UserManagement;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes (only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/tickets', Ticket::class)->name('tickets');

    // Logout route
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', UserManagement::class)->name('admin.users');
        Route::get('/admin/categories', Category::class)->name('categories');
        Route::get('admin/templates/{categoryId}', Template::class)->name('admin.templates');
    });
});
