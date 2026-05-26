<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteFieldController;
use App\Http\Controllers\FieldBrowserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicMatchController;
use App\Http\Controllers\UserActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;

Route::get('/locale/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, config('app.supported_locales', ['en', 'id']), true), 404);

    $request->session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

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
Route::get('/matches', [PublicMatchController::class, 'index'])->name('matches.index');
Route::get('/matches/{match}', [PublicMatchController::class, 'show'])->whereNumber('match')->name('matches.show');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/favorites', [FavoriteFieldController::class, 'index'])->name('favorites.index');
    Route::post('/fields/{field}/favorite', [FavoriteFieldController::class, 'toggle'])->name('fields.favorite.toggle');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/fields/{field}/bookings/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/fields/{field}/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/payment-proof', [BookingController::class, 'uploadProof'])->name('bookings.payment-proof.store');
    Route::get('/payments', [UserActivityController::class, 'payments'])->name('payments.index');
    Route::get('/matches/create', [PublicMatchController::class, 'create'])->name('matches.create');
    Route::get('/my-matches', [PublicMatchController::class, 'myMatches'])->name('matches.my');
    Route::post('/matches', [PublicMatchController::class, 'store'])->name('matches.store');
    Route::post('/matches/{match}/join', [PublicMatchController::class, 'join'])->name('matches.join');
    Route::post('/matches/{match}/participants/{participant}/confirm', [PublicMatchController::class, 'confirmParticipant'])->name('matches.participants.confirm');
    Route::post('/matches/{match}/participants/{participant}/reject', [PublicMatchController::class, 'rejectParticipant'])->name('matches.participants.reject');
    Route::get('/notifications', [UserActivityController::class, 'notifications'])->name('notifications.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});



