<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteFieldController;
use App\Http\Controllers\FieldBrowserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserActivityController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
Route::get('/fields/{field}', [FieldBrowserController::class, 'show'])->name('fields.show');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/favorites', [FavoriteFieldController::class, 'index'])->name('favorites.index');
    Route::post('/fields/{field}/favorite', [FavoriteFieldController::class, 'toggle'])->name('fields.favorite.toggle');
    Route::get('/fields/{field}/bookings/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/fields/{field}/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/payment-proof', [BookingController::class, 'uploadProof'])->name('bookings.payment-proof.store');
    Route::get('/payments', [UserActivityController::class, 'payments'])->name('payments.index');
    Route::get('/notifications', [UserActivityController::class, 'notifications'])->name('notifications.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

});
