<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
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
      Route::get('/', [ClientController::class, 'index'])->name(
        'clients.index'
      );
      Route::post('/', [ClientController::class, 'store'])->name(
        'clients.store'
      );
      Route::get('/{client}', [ClientController::class, 'show'])->name(
        'clients.show'
      );
      Route::get('/{client}/edit', [ClientController::class, 'edit'])->name(
        'clients.edit'
      );
      Route::put('/{client}', [ClientController::class, 'update'])->name(
        'clients.update'
      );
      Route::delete('/{client}', [ClientController::class, 'destroy'])->name(
        'clients.destroy'
      );
    });

    // Project Management Routes
    Route::prefix('projects')->group(function () {
      Route::get('/', [ProjectController::class, 'index'])->name(
        'projects.index'
      );
      Route::get('/create', [ProjectController::class, 'create'])->name(
        'projects.create'
      );
      Route::post('/', [ProjectController::class, 'store'])->name(
        'projects.store'
      );
      Route::get('/tasks', [TaskController::class, 'all'])->name('tasks.all');
      Route::get('/{project}', [ProjectController::class, 'show'])->name(
        'projects.show'
      );
      Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name(
        'projects.edit'
      );
      Route::put('/{project}', [ProjectController::class, 'update'])->name(
        'projects.update'
      );
      Route::delete('/{project}', [ProjectController::class, 'destroy'])->name(
        'projects.destroy'
      );

      // Task Management Routes
      Route::prefix('{project}/tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/{task}', [TaskController::class, 'show'])->name(
          'tasks.show'
        );
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name(
          'tasks.edit'
        );
        Route::put('/{task}', [TaskController::class, 'update'])->name(
          'tasks.update'
        );
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name(
          'tasks.destroy'
        );
      });
    });
  });
});
