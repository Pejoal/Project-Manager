<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled {
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    // Check if the user is authenticated
    if (Auth::check()) {
      // Check if the user has enabled and confirmed two-factor authentication
      if (!Auth::user()->two_factor_confirmed) {
        // Redirect to the 2FA setup page or show an error
        return redirect()->route('two-factor.login');
      }
    }

    // Allow the request to proceed
    return $next($request);
  }
}