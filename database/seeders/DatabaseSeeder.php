<?php

namespace Database\Seeders;

use App\Models\Client;
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
      ['name' => 'Planned', 'color' => '#E70A1D'],
      ['name' => 'In Progress', 'color' => '#36A2EB'],
      ['name' => 'Completed', 'color' => '#4BC0C0'],
      ['name' => 'On Hold', 'color' => '#FFA500'],
      ['name' => 'Cancelled', 'color' => '#808080'],
    ]);

    ProjectPriority::insert([
      ['name' => 'Low', 'color' => '#FFCE56'],
      ['name' => 'Medium', 'color' => '#60F070'],
      ['name' => 'High', 'color' => '#FF6384'],
      ['name' => 'Critical', 'color' => '#FF0000'],
      ['name' => 'Urgent', 'color' => '#FF4500'],
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

    TaskStatus::insert([
      ['name' => 'Pending', 'color' => '#E70A1D'],
      ['name' => 'In Progress', 'color' => '#36A2EB'],
      ['name' => 'Completed', 'color' => '#4BC0C0'],
      ['name' => 'On Hold', 'color' => '#FFA500'],
      ['name' => 'Cancelled', 'color' => '#808080'],
    ]);

    TaskPriority::insert([
      ['name' => 'Low', 'color' => '#FFCE56'],
      ['name' => 'Medium', 'color' => '#60F070'],
      ['name' => 'High', 'color' => '#FF6384'],
      ['name' => 'Critical', 'color' => '#FF0000'],
      ['name' => 'Urgent', 'color' => '#FF4500'],
    ]);

    $projects = Project::get();
    $projects->each(function ($project) {
      Task::factory(5)->create([
        'project_id' => $project->id,
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);
    });

    Settings::create([
      'clients_color' => '#FFF080',
      'projects_color' => '#40F0F0',
      'tasks_color' => '#F04090',
    ]);
  }
}
