<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
  public function index()
  {
    $activities = Activity::with('user', 'subject')->latest()->paginate(100)->groupBy('subject_type');
    return Inertia::render('Activities/Index', [
      'activities' => $activities,
    ]);
  }
}
