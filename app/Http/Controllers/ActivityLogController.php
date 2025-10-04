<?php
namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;

class ActivityLogController extends Controller
{
  public function index(Request $request)
  {
    // Get all activities with eager loading
    $query = Activity::with(['causer', 'subject'])->latest();

    // Apply filters if needed
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

    $perPage = $request->input('per_page', 25);
    $activities = $query->paginate($perPage)->withQueryString();

    // Transform activities for better display
    $transformedActivities = $activities->getCollection()->map(function ($activity) {
      return [
        'id' => $activity->id,
        'log_name' => $activity->log_name,
        'description' => $activity->description,
        'event' => $activity->event,
        'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
        'subject_id' => $activity->subject_id,
        'causer' => $activity->causer
          ? [
            'id' => $activity->causer->id,
            'name' => $activity->causer->name,
            'email' => $activity->causer->email,
            'profile_photo_url' => $activity->causer->profile_photo_url,
          ]
          : null,
        'properties' => $activity->properties,
        'created_at' => $activity->created_at->toISOString(),
        'updated_at' => $activity->updated_at->toISOString(),
      ];
    });

    // Group activities by subject type for better organization
    $groupedActivities = $transformedActivities->groupBy('subject_type');

    // Get filter options
    $subjectTypes = Activity::select('subject_type')
      ->distinct()
      ->whereNotNull('subject_type')
      ->pluck('subject_type')
      ->map(
        fn($type) => [
          'value' => $type,
          'label' => class_basename($type),
        ]
      )
      ->values();

    $users = User::select('id', 'name')->orderBy('name')->get();

    $events = Activity::select('event')
      ->distinct()
      ->whereNotNull('event')
      ->pluck('event')
      ->map(
        fn($event) => [
          'value' => $event,
          'label' => ucfirst($event),
        ]
      )
      ->values();

    return Inertia::render('Activities/Index', [
      'activities' => $groupedActivities,
      'pagination' => [
        'data' => $transformedActivities->values(),
        'current_page' => $activities->currentPage(),
        'last_page' => $activities->lastPage(),
        'per_page' => $activities->perPage(),
        'prev_page_url' => $activities->previousPageUrl(),
        'next_page_url' => $activities->nextPageUrl(),
        'total' => $activities->total(),
      ],
      'filters' => $request->only(['search', 'subject_type', 'causer_id', 'event', 'per_page']),
      'filterOptions' => [
        'subject_types' => $subjectTypes,
        'users' => $users,
        'events' => $events,
      ],
    ]);
  }
}
