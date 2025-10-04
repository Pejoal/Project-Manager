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

    // 2. Start with the base query, eager-loading relationships for performance
    $query = Activity::with(['causer', 'subject'])->latest();

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

    // 4. Paginate the results
    $perPage = $request->input('per_page', 10);
    $activities = $query->paginate($perPage)->withQueryString();

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
      // Use the API Resource to transform the paginated data
      'activities' => ActivityResource::collection($activities),
      'filters' => $request->only(['search', 'subject_type', 'causer_id', 'event', 'per_page']),
      'filterOptions' => [
        'subject_types' => $subjectTypes,
        'users' => User::select('id', 'name')->orderBy('name')->get(),
        'events' => $events,
      ],
    ]);
  }
}
