<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
  public function index(Request $request)
  {
    // 1. Validate incoming filter requests
    $request->validate([
      'search' => 'nullable|string|max:255',
      'subject_type' => 'nullable|string',
      'causer_id' => 'nullable|integer|exists:users,id',
      'event' => 'nullable|string|in:created,updated,deleted',
      'per_page' => 'nullable|integer|min:1|max:100',
    ]);

    // Apply sorting
    $sortBy = $request->input('sort_by', 'created_at');
    $sortDirection = $request->input('sort_direction', 'desc');

    // 2. Start with the base query, eager-loading relationships for performance
    $query = Activity::with(['causer', 'subject']);
    if ($sortBy === 'causer.name') {
      $query
        ->join('users', 'activity_log.causer_id', '=', 'users.id')
        ->orderBy('users.name', $sortDirection)
        ->select('activity_log.*');
    } else {
      $query->orderBy($sortBy, $sortDirection);
    }

    // 3. Apply filters based on request input
    if ($request->filled('subject_type')) {
      $query->where('subject_type', $request->subject_type);
    }

    if ($request->filled('causer_id')) {
      $query->where('causer_id', $request->causer_id);
    }

    if ($request->filled('event')) {
      $query->where('event', $request->event);
    }

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('description', 'like', "%{$search}%")->orWhereHas('causer', function ($q) use ($search) {
          $q->where('name', 'like', "%{$search}%");
        });
      });
    }

    // 4. Paginate and transform the results, ensuring a plain array is returned for each item.
    $perPage = $request->input('per_page', 10);
    $activities = $query
      ->paginate($perPage)
      ->withQueryString()
      ->through(fn(Activity $activity) => (new ActivityResource($activity))->resolve());

    // 5. Prepare filter options for the frontend dropdowns
    $subjectTypes = Activity::select('subject_type')
      ->distinct()
      ->whereNotNull('subject_type')
      ->pluck('subject_type')
      ->map(fn($type) => ['value' => $type, 'label' => class_basename($type)])
      ->sortBy('label')
      ->values();

    $events = Activity::select('event')
      ->distinct()
      ->whereNotNull('event')
      ->pluck('event')
      ->map(fn($event) => ['value' => $event, 'label' => ucfirst($event)])
      ->values();

    // 6. Render the Inertia view, passing the transformed data and filters
    return Inertia::render('Activities/Index', [
      // Now passing the paginator object with transformed data
      'activities' => $activities,
      'filters' => $request->only(['search', 'subject_type', 'causer_id', 'event', 'per_page']),
      'filterOptions' => [
        'subject_types' => $subjectTypes,
        'users' => User::select('id', 'name')->orderBy('name')->get(),
        'events' => $events,
      ],
    ]);
  }
}
