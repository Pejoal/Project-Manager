<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use Inertia\Inertia;
use App\Models\Settings;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function show()
  {
    $clientsCount = Client::count();
    $projectsCount = Project::count();
    $tasksCount = Task::count();
    $settings = Settings::first();

    $today = Carbon::now(); // Current date
    $months = collect(range(0, 11))
      ->map(function ($offset) use ($today) {
        return $today->copy()->subMonths($offset)->format('F Y'); // Format as "Month Year"
      })
      ->reverse()
      ->values(); // Reverse to get chronological order

    $clientsData = Client::selectRaw(
      'count(*) as count, MONTH(created_at) as month, YEAR(created_at) as year'
    )
      ->whereBetween('created_at', [
        $today->copy()->subMonths(11)->startOfMonth(),
        $today->copy()->endOfMonth(),
      ])
      ->groupBy('year', 'month')
      ->get()
      ->keyBy(function ($item) {
        return Carbon::create($item->year, $item->month)->format('F Y');
      });

    $clients = $months->map(function ($month) use ($clientsData) {
      return $clientsData->has($month) ? $clientsData->get($month)->count : 0;
    });

    $projectsData = Project::selectRaw(
      'count(*) as count, MONTH(created_at) as month, YEAR(created_at) as year'
    )
      ->whereBetween('created_at', [
        $today->copy()->subMonths(11)->startOfMonth(),
        $today->copy()->endOfMonth(),
      ])
      ->groupBy('year', 'month')
      ->get()
      ->keyBy(function ($item) {
        return Carbon::create($item->year, $item->month)->format('F Y');
      });

    $projects = $months->map(function ($month) use ($projectsData) {
      return $projectsData->has($month) ? $projectsData->get($month)->count : 0;
    });

    $tasksData = Task::selectRaw(
      'count(*) as count, MONTH(created_at) as month, YEAR(created_at) as year'
    )
      ->whereBetween('created_at', [
        $today->copy()->subMonths(11)->startOfMonth(),
        $today->copy()->endOfMonth(),
      ])
      ->groupBy('year', 'month')
      ->get()
      ->keyBy(function ($item) {
        return Carbon::create($item->year, $item->month)->format('F Y');
      });

    $tasks = $months->map(function ($month) use ($tasksData) {
      return $tasksData->has($month) ? $tasksData->get($month)->count : 0;
    });

    $lineChartData = [
      'labels' => $months,
      'datasets' => [
        [
          'label' => 'Clients',
          'borderColor' => $settings->clients_color,
          'data' => $clients->toArray(),
          'fill' => false,
        ],
        [
          'label' => 'Projects',
          'borderColor' => $settings->projects_color,
          'data' => $projects->toArray(),
          'fill' => false,
        ],
        [
          'label' => 'Tasks',
          'borderColor' => $settings->tasks_color,
          'data' => $tasks->toArray(),
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
