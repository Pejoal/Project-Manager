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
      $user = User::where('email', $request->username)
        ->orWhere('username', $request->username)
        ->first();

      if ($user && Hash::check($request->password, $user->password)) {
        return $user;
      }
    });

    Jetstream::inertia()->whenRendering('Profile/Show', function (
      Request $request,
      array $data
    ) {
      return array_merge($data, [
        'translations' => [
          'profile' => __('messages.profile'),
          'two_factor_authentication' => __(
            'messages.two_factor_authentication'
          ),
          'two_factor_authentication_description' => __(
            'messages.two_factor_authentication_description'
          ),
          'enabled' => __('messages.enabled'),
          'not_enabled' => __('messages.not_enabled'),
          'finish_enabling' => __('messages.finish_enabling'),
          'secure_random_token' => __('messages.secure_random_token'),
          'qr_code_instructions' => __('messages.qr_code_instructions'),
          'enabled_qr_code_instructions' => __(
            'messages.enabled_qr_code_instructions'
          ),
          'setup_key' => __('messages.setup_key'),
          'recovery_codes_instructions' => __(
            'messages.recovery_codes_instructions'
          ),
          'enable' => __('messages.enable'),
          'confirm' => __('messages.confirm'),
          'regenerate_recovery_codes' => __(
            'messages.regenerate_recovery_codes'
          ),
          'show_recovery_codes' => __('messages.show_recovery_codes'),
          'cancel' => __('messages.cancel'),
          'disable' => __('messages.disable'),
          'profile_update' => __('messages.profile_update'),
          'profile_update_description' => __(
            'messages.profile_update_description'
          ),
          'password_update' => __('messages.password_update'),
          'password_update_description' => __(
            'messages.password_update_description'
          ),
          'profile_information' => __('messages.profile_information'),
          'profile_information_description' => __(
            'messages.profile_information_description'
          ),
          'update_password' => __('messages.update_password'),
          'update_password_description' => __(
            'messages.update_password_description'
          ),
          'browser_sessions' => __('messages.browser_sessions'),
          'browser_sessions_description' => __(
            'messages.browser_sessions_description'
          ),
          'delete_account' => __('messages.delete_account'),
          'delete_account_description' => __(
            'messages.delete_account_description'
          ),
          'current_password' => __('messages.current_password'),
          'new_password' => __('messages.new_password'),
          'confirm_password' => __('messages.confirm_password'),
          'please_confirm_your_password' => __(
            'messages.please_confirm_your_password'
          ),
          'log_out_other_browser_sessions' => __(
            'messages.log_out_other_browser_sessions'
          ),
          'log_out_other_browser_sessions_description' => __(
            'messages.log_out_other_browser_sessions_description'
          ),
          'delete_account_confirmation' => __(
            'messages.delete_account_confirmation'
          ),
          'done' => __('messages.done'),
          'save' => __('messages.save'),
          'saved' => __('messages.saved'),
          'photo' => __('messages.photo'),
          'select_new_photo' => __('messages.select_new_photo'),
          'remove_photo' => __('messages.remove_photo'),
          'name' => __('messages.name'),
          'username' => __('messages.username'),
          'email' => __('messages.email'),
          'email_unverified' => __('messages.email_unverified'),
          'click_to_resend' => __('messages.click_to_resend'),
          'verification_link_sent' => __('messages.verification_link_sent'),
        ],
      ]);
    });
  }

  /**
   * Configure the roles and permissions that are available within the application.
   */
  protected function configurePermissions(): void
  {
    Jetstream::defaultApiTokenPermissions(['read']);

    Jetstream::role('admin', 'Administrator', [
      'create',
      'read',
      'update',
      'delete',
    ])->description('Administrator users can perform any action.');

    Jetstream::role('editor', 'Editor', [
      'read',
      'create',
      'update',
    ])->description(
      'Editor users have the ability to read, create, and update.'
    );
  }
}
