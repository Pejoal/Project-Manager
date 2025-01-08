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

    $lineChartData = [
      'labels' => ['September', 'October', 'November', 'December', 'January'],
      'datasets' => [
        [
          'label' => 'Clients',
          'borderColor' => $settings->clients_color,
          'data' => Client::selectRaw(
            'count(*) as count, MONTH(created_at) as month'
          )
            ->groupBy('month')
            ->pluck('count')
            ->toArray(),
          'fill' => false,
        ],
        [
          'label' => 'Projects',
          'borderColor' => $settings->projects_color,
          'data' => Project::selectRaw(
            'count(*) as count, MONTH(created_at) as month'
          )
            ->groupBy('month')
            ->pluck('count')
            ->toArray(),
          'fill' => false,
        ],
        [
          'label' => 'Tasks',
          'borderColor' => $settings->tasks_color,
          'data' => Task::selectRaw(
            'count(*) as count, MONTH(created_at) as month'
          )
            ->groupBy('month')
            ->pluck('count')
            ->toArray(),
          'fill' => false,
        ],
      ],
    ];

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
      'lineChartData' => $lineChartData,
    ]);
  }
}
