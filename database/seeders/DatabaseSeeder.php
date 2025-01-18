<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Phase;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\ProjectStatus;
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

    ProjectStatus::insert([
      ['name' => 'Planned', 'color' => '#FF5733'],
      ['name' => 'In Progress', 'color' => '#36A2EB'],
      ['name' => 'Completed', 'color' => '#4BC0C0'],
      ['name' => 'On Hold', 'color' => '#FFA500'],
      ['name' => 'Cancelled', 'color' => '#808080'],
      ['name' => 'Deferred', 'color' => '#C70039'],
    ]);

    ProjectPriority::insert([
      ['name' => 'Low', 'color' => '#FFCE56'],
      ['name' => 'Medium', 'color' => '#00FF00'],
      ['name' => 'High', 'color' => '#FF1493'],
      ['name' => 'Urgent', 'color' => '#FF1500'],
    ]);

    for ($i = 0; $i < 12; $i++) {
      $client = Client::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);

      $project = Project::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);

      $project->clients()->attach($client->id);
    }
    Phase::factory(15)->create();

    TaskStatus::insert([
      ['name' => 'Pending', 'color' => '#E70A1D'],
      ['name' => 'In Progress', 'color' => '#36A2EB'],
      ['name' => 'Completed', 'color' => '#4BC0C0'],
      ['name' => 'On Hold', 'color' => '#FFA500'],
      ['name' => 'Cancelled', 'color' => '#808080'],
      ['name' => 'Review', 'color' => '#FFD700'],
      ['name' => 'Testing', 'color' => '#8A2BE2'],
    ]);

    TaskPriority::insert([
      ['name' => 'Low', 'color' => '#FFCE56'],
      ['name' => 'Medium', 'color' => '#00FF00'],
      ['name' => 'High', 'color' => '#FF1493'],
      ['name' => 'Urgent', 'color' => '#FF1500'],
    ]);

    $projects = Project::get();
    $projects->each(function ($project) {
      Task::factory(5)->create([
        'project_id' => $project->id,
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);
    });

    Settings::create([
      'clients_color' => '#0FA0FF',
      'projects_color' => '#F02050',
      'tasks_color' => '#30F030',
    ]);
  }
}
