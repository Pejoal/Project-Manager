<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Inertia\Inertia;
use App\Models\Settings;

class DashboardController extends Controller
{
  public function show()
  {
    $clientsCount = Client::count();
    $projectsCount = Project::count();
    $tasksCount = Task::count();
    $settings = Settings::first();

    return Inertia::render('Dashboard', [
      'translations' => [
        'welcome' => __('messages.welcome'),
      ],
      'doughnutData' => [
        'labels' => ['Clients', 'Projects', 'Tasks'],
        'datasets' => [
          [
            'data' => [$clientsCount, $projectsCount, $tasksCount],
            'backgroundColor' => [
              $settings->clients_color,
              $settings->projects_color,
              $settings->tasks_color,
            ],
          ],
        ],
      ],
      'settings' => $settings,
    ]);
  }
}
