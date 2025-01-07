<?php

use App\Http\Controllers\ClientController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/', function () {
  return Inertia::render('Welcome', [
    'canLogin' => Route::has('login'),
    'canRegister' => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion' => PHP_VERSION,
  ]);
});

Route::group(['prefix' => LaravelLocalization::setLocale()], function () {
  Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'localeSessionRedirect',
    'localizationRedirect',
    'localeViewPath',
  ])->group(function () {
    $locales = collect(LaravelLocalization::getSupportedLocales())->map(
      function ($properties, $localeCode) {
        return [
          'code' => $localeCode,
          'native' => $properties['native'],
          'url' => LaravelLocalization::getLocalizedURL(
            $localeCode,
            null,
            [],
            true
          ),
          'emoji' => $properties['emoji'],
        ];
      }
    );
    Inertia::share([
      'locales' => $locales,
      'active_locale_code' => LaravelLocalization::getCurrentLocale(),
    ]);

    Route::get('/dashboard', function () {
      return Inertia::render('Dashboard', [
        'translations' => __('messages'),
      ]);
    })->name('dashboard');

    // Client Management Routes
    Route::prefix('clients')->group(function () {
      Route::get('/', [ClientController::class, 'index'])->name('clients.index');
      Route::get('/create', [ClientController::class, 'create'])->name('clients.create');
      Route::post('/', [ClientController::class, 'store'])->name('clients.store');
      Route::get('/{client}', [ClientController::class, 'show'])->name('clients.show');
      Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
      Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
      Route::delete('/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });
  });
});
