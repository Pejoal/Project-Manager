<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
  public function index(Request $request)
  {
    // Get all activities with eager loading
    $query = Activity::with('user', 'subject')->latest();

    // Apply filters if needed
    if ($request->filled('subject_type')) {
      $query->where('subject_type', $request->subject_type);
    }

    if ($request->filled('user_id')) {
      $query->where('user_id', $request->user_id);
    }

    if ($request->filled('type')) {
      $query->where('type', $request->type);
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
