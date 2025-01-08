<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Inertia\Inertia;

class DashboardController extends Controller
{
  public function show()
  {
    $clientsCount = Client::count();
    $projectsCount = Project::count();
    $tasksCount = Task::count();

    return Inertia::render('Dashboard', [
      'translations' => [
        'welcome' => __('messages.welcome'),
      ],
      'doughnutData' => [
        'labels' => ['Clients', 'Projects', 'Tasks'],
        'datasets' => [
          [
            'data' => [$clientsCount, $projectsCount, $tasksCount],
            'backgroundColor' => ['#F70E1F', '#00F95F', '#123EFB'],
          ],
        ],
      ],
    ]);
  }
}
