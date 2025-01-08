<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Settings;
use App\Models\Task;
use App\Models\User;
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

    Client::factory(3)->create();

    $projects = Project::factory(3)->create();

    $projects->each(function ($project) {
      Task::factory(2)->create(['project_id' => $project->id]);
    });

    Settings::create([
      "clients_color" => "#4040f0",
      "projects_color" => "#40f040",
      "tasks_color" => "#f04040",
    ]);

  }
}
