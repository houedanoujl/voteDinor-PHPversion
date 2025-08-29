<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\CandidateController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

// Page d'accueil du concours
Route::get('/', [HomeController::class, 'index'])->name('contest.home');
Route::get('/classement', [HomeController::class, 'ranking'])->name('contest.ranking');
Route::get('/regles', [\App\Http\Controllers\ContestRulesController::class, 'index'])->name('contest.rules');
Route::get('/inscription/confirmation', [\App\Http\Controllers\RegistrationConfirmationController::class, 'show'])->name('registration.confirmation');
Route::get('/candidat/{id}', [\App\Http\Controllers\CandidateDetailController::class, 'show'])->name('candidate.detail');
Route::post('/vote/{id}', [\App\Http\Controllers\VoteController::class, 'vote'])->name('vote.candidate');

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



// Routes pour les candidats et votes (protection middleware auth)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/vote/{candidate}', [VoteController::class, 'vote'])->name('vote');
});

// API publique pour le classement
Route::get('/api/ranking', [VoteController::class, 'ranking'])->name('api.ranking');

// Routes admin pour approbation des candidats
Route::middleware(['auth', \App\Http\Middleware\AdminOnly::class])->prefix('admin')->group(function () {
    Route::get('/candidates/{candidate}/approve', [CandidateController::class, 'approve'])->name('admin.candidates.approve');
    Route::get('/candidates/{candidate}/reject', [CandidateController::class, 'reject'])->name('admin.candidates.reject');
    Route::delete('/candidates/{candidate}', [CandidateController::class, 'destroy'])->name('admin.candidates.destroy');

    // Routes WhatsApp avec Green API
    Route::prefix('whatsapp')->group(function () {
        Route::post('/send', [\App\Http\Controllers\Admin\WhatsAppGreenApiController::class, 'sendMessage'])->name('admin.whatsapp.send');
        Route::post('/send-status-notification', [\App\Http\Controllers\Admin\WhatsAppGreenApiController::class, 'sendStatusNotification'])->name('admin.whatsapp.send-status');
        Route::post('/send-bulk', [\App\Http\Controllers\Admin\WhatsAppGreenApiController::class, 'sendBulkMessage'])->name('admin.whatsapp.send-bulk');
        Route::get('/status', [\App\Http\Controllers\Admin\WhatsAppGreenApiController::class, 'checkStatus'])->name('admin.whatsapp.status');
    });
});

// Routes pour le streaming vidéo
Route::prefix('video')->group(function () {
    Route::get('/player', [\App\Http\Controllers\VideoController::class, 'player'])->name('video.player');
    Route::get('/stream/{filename?}', [\App\Http\Controllers\VideoController::class, 'stream'])->name('video.stream');
    Route::get('/hls/{filename?}', [\App\Http\Controllers\VideoController::class, 'streamHLS'])->name('video.hls');
    Route::get('/info/{filename?}', [\App\Http\Controllers\VideoController::class, 'info'])->name('video.info');

    // Routes HLS pour les segments et playlists
    Route::get('/hls/{quality}/playlist.m3u8', [\App\Http\Controllers\VideoController::class, 'serveHLSPlaylist'])->name('video.hls.playlist');
    Route::get('/hls/{quality}/{segment}', [\App\Http\Controllers\VideoController::class, 'serveHLSSegment'])->name('video.hls.segment');
});
