<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Settings;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->withPersonalTeam()->create();

    User::factory()
      ->withPersonalTeam()
      ->create([
        'name' => 'Pejoal Hanna',
        'username' => 'pejoal',
        'email' => 'pejoal.official@gmail.com',
      ]);

    User::factory()
      ->withPersonalTeam()
      ->create([
        'name' => 'Test User',
        'username' => 'test',
        'email' => 'test@example.com',
      ]);

    for ($i = 0; $i < 10; $i++) {
      Client::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);

      Project::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);
    }

    TaskStatus::insert([
      ['name' => 'Pending'],
      ['name' => 'In Progress'],
      ['name' => 'Completed'],
    ]);

    TaskPriority::insert([
      ['name' => 'Low'],
      ['name' => 'Medium'],
      ['name' => 'High'],
    ]);

    $taskStatuses = TaskStatus::pluck('id')->toArray();
    $taskPriorities = TaskPriority::pluck('id')->toArray();

    $projects = Project::get();
    $projects->each(function ($project) use ($taskStatuses, $taskPriorities) {
      Task::factory(3)->create([
        'project_id' => $project->id,
        'status_id' => $taskStatuses[array_rand($taskStatuses)],
        'priority_id' => $taskPriorities[array_rand($taskPriorities)],
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);
    });

    Settings::create([
      'clients_color' => '#4040f0',
      'projects_color' => '#40f040',
      'tasks_color' => '#f04040',
    ]);
  }
}
