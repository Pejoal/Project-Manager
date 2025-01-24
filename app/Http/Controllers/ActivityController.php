<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
  public function index()
  {
    $activities = Activity::with('user', 'subject')->latest()->paginate(100);
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
