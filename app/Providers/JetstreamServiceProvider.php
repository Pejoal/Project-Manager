<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class JetstreamServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    $this->configurePermissions();

    Jetstream::createTeamsUsing(CreateTeam::class);
    Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
    Jetstream::addTeamMembersUsing(AddTeamMember::class);
    Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
    Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
    Jetstream::deleteTeamsUsing(DeleteTeam::class);
    Jetstream::deleteUsersUsing(DeleteUser::class);

    Fortify::authenticateUsing(function (Request $request) {
      $user = User::where('email', $request->username)->orWhere('username', $request->username)->first();

      if ($user && Hash::check($request->password, $user->password)) {
        return $user;
      }
    });

    Route::group(
      [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
      ],
      function () {
        // Register Jetstream routes within the localization group
        Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
          Route::get('/user/profile', [
            \Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController::class,
            'show',
          ])->name('profile.show');
        });
      }
    );
  }

  /**
   * Configure the roles and permissions that are available within the application.
   */
  protected function configurePermissions(): void
  {
    Jetstream::defaultApiTokenPermissions(['read']);

    Jetstream::role('admin', 'Administrator', ['create', 'read', 'update', 'delete'])->description(
      'Administrator users can perform any action.'
    );

    Jetstream::role('editor', 'Editor', ['read', 'create', 'update'])->description(
      'Editor users have the ability to read, create, and update.'
    );
  }
}
