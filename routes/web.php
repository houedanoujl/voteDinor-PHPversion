<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Page d'accueil du concours
Route::get('/', [HomeController::class, 'index'])->name('contest.home');
Route::get('/classement', [HomeController::class, 'ranking'])->name('contest.ranking');

// Routes d'authentification standard
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Routes d'authentification sociale
Route::prefix('auth')->group(function () {
    // Redirection vers les fournisseurs OAuth
    Route::get('{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->where('provider', 'google|facebook')
        ->name('auth.redirect');
    
    // Callbacks OAuth
    Route::get('{provider}/callback', [SocialAuthController::class, 'callback'])
        ->where('provider', 'google|facebook')
        ->name('auth.callback');
    
    // Déconnexion sociale
    Route::post('logout', [SocialAuthController::class, 'logout'])
        ->name('auth.logout');
});

// Routes pour les candidats et votes (protection middleware auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/vote/{candidate}', [VoteController::class, 'vote'])->name('vote');
});

// API publique pour le classement
Route::get('/api/ranking', [VoteController::class, 'ranking'])->name('api.ranking');
