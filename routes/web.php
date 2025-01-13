<?php

use App\Events\MessageSent;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskPriorityController;
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

      Route::put('/user/profile-information', function (Request $request) {
        $request->validate([
          'name' => ['required', 'string', 'max:255'],
          'username' => [
            'required',
            'string',
            'max:255',
            'unique:users,username,' . $request->user()->id,
          ],
          'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users,email,' . $request->user()->id,
          ],
        ]);

        $request->user()->update([
          'name' => $request->name,
          'username' => $request->username,
          'email' => $request->email,
        ]);
      })->name('user-profile-information.update');

      Route::get('/dashboard', [DashboardController::class, 'show'])->name(
        'dashboard'
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
          Route::put('/tasks/{task}/status', [
            TaskController::class,
            'updateStatus',
          ])->name('tasks.updateStatus');
          Route::put('/tasks/{task}/priority', [
            TaskController::class,
            'updatePriority',
          ])->name('tasks.updatePriority');
        });
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
