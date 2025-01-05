<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;

Route::get('/', function () {
  return Inertia::render('Welcome', [
    'canLogin' => Route::has('login'),
    'canRegister' => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion' => PHP_VERSION,
  ]);
});

// Route::middleware(['verified', config('jetstream.two-factor')])->group(function () {
Route::middleware(['verified'])->group(function () {
  Route::get('/test', function () {
    return Inertia::render('Test');
  })->name("test");
});

Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {
  Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
  })->name('dashboard');
});

Route::get('/two-factor-challenge', function () {
  return Inertia::render('Auth/TwoFactorChallenge');
})->middleware(['auth'])->name('two-factor.challenge');

Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
->name('two-factor.login');