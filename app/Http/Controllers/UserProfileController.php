<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Agent;
use Laravel\Jetstream\Http\Controllers\Inertia\Concerns\ConfirmsTwoFactorAuthentication;
use Laravel\Jetstream\Jetstream;

class UserProfileController extends Controller
{
  use ConfirmsTwoFactorAuthentication;

  /**
   * Show the general profile settings screen.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Inertia\Response
   */
  public function show(Request $request)
  {
    $this->validateTwoFactorAuthenticationState($request);

    return Jetstream::inertia()->render($request, 'Profile/Show', [
      'confirmsTwoFactorAuthentication' => Features::optionEnabled(
        Features::twoFactorAuthentication(),
        'confirm'
      ),
      'sessions' => $this->sessions($request)->all(),
      'translations' => [
        'profile' => __('messages.profile'),
        'two_factor_authentication' => __('messages.two_factor_authentication'),
        'two_factor_authentication_description' => __('messages.two_factor_authentication_description'),
        'enabled' => __('messages.enabled'),
        'not_enabled' => __('messages.not_enabled'),
        'finish_enabling' => __('messages.finish_enabling'),
        'secure_random_token' => __('messages.secure_random_token'),
        'qr_code_instructions' => __('messages.qr_code_instructions'),
        'enabled_qr_code_instructions' => __('messages.enabled_qr_code_instructions'),
        'setup_key' => __('messages.setup_key'),
        'recovery_codes_instructions' => __('messages.recovery_codes_instructions'),
        'enable' => __('messages.enable'),
        'confirm' => __('messages.confirm'),
        'regenerate_recovery_codes' => __('messages.regenerate_recovery_codes'),
        'show_recovery_codes' => __('messages.show_recovery_codes'),
        'cancel' => __('messages.cancel'),
        'disable' => __('messages.disable'),
        'profile_update' => __('messages.profile_update'),
        'profile_update_description' => __('messages.profile_update_description'),
        'password_update' => __('messages.password_update'),
        'password_update_description' => __('messages.password_update_description'),
        'profile_information' => __('messages.profile_information'),
        'profile_information_description' => __('messages.profile_information_description'),
        'update_password' => __('messages.update_password'),
        'update_password_description' => __('messages.update_password_description'),
        'browser_sessions' => __('messages.browser_sessions'),
        'browser_sessions_description' => __('messages.browser_sessions_description'),
        'delete_account' => __('messages.delete_account'),
        'delete_account_description' => __('messages.delete_account_description'),
        'current_password' => __('messages.current_password'),
        'new_password' => __('messages.new_password'),
        'confirm_password' => __('messages.confirm_password'),
        'please_confirm_your_password' => __('messages.please_confirm_your_password'),
        'log_out_other_browser_sessions' => __('messages.log_out_other_browser_sessions'),
        'log_out_other_browser_sessions_description' => __('messages.log_out_other_browser_sessions_description'),
        'delete_account_confirmation' => __('messages.delete_account_confirmation'),
        'done' => __('messages.done'),
        'save' => __('messages.save'),
        'saved' => __('messages.saved'),
      ],
    ]);
  }

  /**
   * Get the current sessions.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Support\Collection
   */
  public function sessions(Request $request)
  {
    if (config('session.driver') !== 'database') {
      return collect();
    }

    return collect(
      DB::connection(config('session.connection'))
        ->table(config('session.table', 'sessions'))
        ->where('user_id', $request->user()->getAuthIdentifier())
        ->orderBy('last_activity', 'desc')
        ->get()
    )->map(function ($session) use ($request) {
      $agent = $this->createAgent($session);

      return (object) [
        'agent' => [
          'is_desktop' => $agent->isDesktop(),
          'platform' => $agent->platform(),
          'browser' => $agent->browser(),
        ],
        'ip_address' => $session->ip_address,
        'is_current_device' => $session->id === $request->session()->getId(),
        'last_active' => Carbon::createFromTimestamp(
          $session->last_activity
        )->diffForHumans(),
      ];
    });
  }

  /**
   * Create a new agent instance from the given session.
   *
   * @param  mixed  $session
   * @return \Laravel\Jetstream\Agent
   */
  protected function createAgent($session)
  {
    return tap(
      new Agent(),
      fn($agent) => $agent->setUserAgent($session->user_agent)
    );
  }
}
