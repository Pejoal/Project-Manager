<?php
namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
  public function index(Request $request)
  {
    // Get all activities with eager loading
    $query = Activity::with('causer', 'subject')
      ->latest();

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

    $activities = $query->paginate(100);
    
    // Group activities by subject type for better organization
    $groupedActivities = $activities->getCollection()->groupBy('subject_type');

    return Inertia::render('Activities/Index', [
      'activities' => $groupedActivities,
      'pagination' => [
        'current_page' => $activities->currentPage(),
        'last_page' => $activities->lastPage(),
        'prev_page_url' => $activities->previousPageUrl(),
        'next_page_url' => $activities->nextPageUrl(),
        'total' => $activities->total(),
      ],
    ]);
  }
}
