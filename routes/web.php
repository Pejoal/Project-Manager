<?php

use App\Events\MessageSent;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPriorityController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskPriorityController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Http\Request;

Route::group(
  [
    'prefix' => LaravelLocalization::setLocale(),
    'middleware',
    ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
  ],
  function () {
    Route::get('/', function () {
      return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
      ]);
    });

    Route::middleware([
      'auth:sanctum',
      config('jetstream.auth_session'),
      'verified',
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

      Route::put('/api/settings', [
        SettingsController::class,
        'updateSettings',
      ]);

      Route::get('/dashboard', [DashboardController::class, 'show'])->name(
        'dashboard'
      );

      Route::get('/profile', [UserProfileController::class, 'show'])->name(
        'profile.show'
      );

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
        Route::get('/{project:slug}', [ProjectController::class, 'show'])->name(
          'projects.show'
        );
        Route::get('/{project:slug}/edit', [
          ProjectController::class,
          'edit',
        ])->name('projects.edit');
        Route::put('/{project:slug}', [
          ProjectController::class,
          'update',
        ])->name('projects.update');
        Route::delete('/{project:slug}', [
          ProjectController::class,
          'destroy',
        ])->name('projects.destroy');

        // Phase Management Routes
        Route::prefix('{project:slug}/phases')->group(function () {
          Route::get('/', [PhaseController::class, 'index'])->name(
            'phases.index'
          );
          Route::post('/', [PhaseController::class, 'store'])->name(
            'phases.store'
          );
          Route::get('/{phase}', [PhaseController::class, 'show'])->name(
            'phases.show'
          );
          Route::get('/{phase}/edit', [PhaseController::class, 'edit'])->name(
            'phases.edit'
          );
          Route::put('/{phase}', [PhaseController::class, 'update'])->name(
            'phases.update'
          );
          Route::delete('/{phase}', [PhaseController::class, 'destroy'])->name(
            'phases.destroy'
          );
          Route::delete('/{phase}', [PhaseController::class, 'sync'])->name(
            'phases.destroy'
          );
        });

        // Task Management Routes
        Route::prefix('{project:slug}/tasks')->group(function () {
          Route::get('/', [TaskController::class, 'index'])->name(
            'tasks.index'
          );
          Route::post('/', [TaskController::class, 'store'])->name(
            'tasks.store'
          );
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

      // Project Status Management Routes
      Route::prefix('project-statuses')->group(function () {
        Route::get('/', [ProjectStatusController::class, 'index'])->name(
          'project-statuses.index'
        );
        Route::post('/', [ProjectStatusController::class, 'store'])->name(
          'project-statuses.store'
        );
        Route::put('/{projectStatus}', [
          ProjectStatusController::class,
          'update',
        ])->name('project-statuses.update');
        Route::delete('/{projectStatus}', [
          ProjectStatusController::class,
          'destroy',
        ])->name('project-statuses.destroy');
      });

      // Project Priority Management Routes
      Route::prefix('project-priorities')->group(function () {
        Route::get('/', [ProjectPriorityController::class, 'index'])->name(
          'project-priorities.index'
        );
        Route::post('/', [ProjectPriorityController::class, 'store'])->name(
          'project-priorities.store'
        );
        Route::put('/{projectPriority}', [
          ProjectPriorityController::class,
          'update',
        ])->name('project-priorities.update');
        Route::delete('/{projectPriority}', [
          ProjectPriorityController::class,
          'destroy',
        ])->name('project-priorities.destroy');
      });

      // Task Status Management Routes
      Route::prefix('task-statuses')->group(function () {
        Route::get('/', [TaskStatusController::class, 'index'])->name(
          'task-statuses.index'
        );
        Route::post('/', [TaskStatusController::class, 'store'])->name(
          'task-statuses.store'
        );
        Route::put('/{taskStatus}', [
          TaskStatusController::class,
          'update',
        ])->name('task-statuses.update');
        Route::delete('/{taskStatus}', [
          TaskStatusController::class,
          'destroy',
        ])->name('task-statuses.destroy');
      });

      // Task Priority Management Routes
      Route::prefix('task-priorities')->group(function () {
        Route::get('/', [TaskPriorityController::class, 'index'])->name(
          'task-priorities.index'
        );
        Route::post('/', [TaskPriorityController::class, 'store'])->name(
          'task-priorities.store'
        );
        Route::put('/{taskPriority}', [
          TaskPriorityController::class,
          'update',
        ])->name('task-priorities.update');
        Route::delete('/{taskPriority}', [
          TaskPriorityController::class,
          'destroy',
        ])->name('task-priorities.destroy');
      });
    });
  }
);
