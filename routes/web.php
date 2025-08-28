<?php

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;

// Page d'accueil du concours
Route::get('/', function () {
    return view('contest.home');
})->name('home');

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
    
    // Déconnexion
    Route::post('logout', [SocialAuthController::class, 'logout'])
        ->name('logout');
});

// Routes pour les candidats (protection middleware auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Route alternative pour la connexion simple
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
