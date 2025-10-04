<?php

namespace App\Http\Middleware;

use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
  /**
   * The root template that is loaded on the first page visit.
   *
   * @var string
   */
  protected $rootView = 'app';

  /**
   * Determine the current asset version.
   */
  public function version(Request $request): ?string
  {
    return parent::version($request);
  }

  /**
   * Define the props that are shared by default.
   *
   * @return array<string, mixed>
   */
  public function share(Request $request): array
  {
    $user = $request->user();

    return [
      ...parent::share($request),
      'user.can' => $user ? $user->getAllPermissions()->pluck('name')->toArray() : [],
      'user.roles' => $user ? $user->getRoleNames()->toArray() : [],
      'breadcrumbs' => function () {
        // Return an empty array if no breadcrumbs are defined for the route
        try {
          return Breadcrumbs::generate();
        } catch (\Exception $e) {
          return [];
        }
      },

      'ziggy' => fn() => [...(new Ziggy())->toArray(), 'location' => $request->url()],
    ];
  }
}
