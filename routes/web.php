<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FieldBrowserController;
use App\Http\Controllers\FieldOwnerApprovalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('/contact', 'Footer Info.contact')->name('contact');
Route::view('/help', 'Footer Info.help')->name('help');
Route::view('/privacy', 'Footer Info.privacy')->name('privacy');
Route::view('/terms', 'Footer Info.terms')->name('terms');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::get('/fields', [FieldBrowserController::class, 'index'])->name('fields.index');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function (): void {
        Route::get('/field-owners', [FieldOwnerApprovalController::class, 'index'])->name('field-owners.index');
        Route::patch('/field-owners/{user}/approve', [FieldOwnerApprovalController::class, 'approve'])->name('field-owners.approve');
        Route::patch('/field-owners/{user}/deactivate', [FieldOwnerApprovalController::class, 'deactivate'])->name('field-owners.deactivate');
    });
});
