<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Milestone;
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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->withPersonalTeam()->create();

    Settings::create([
      'clients_color' => '#0FA0FF',
      'projects_color' => '#F02050',
      'tasks_color' => '#30F030',
    ]);
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
      [
        'name' => 'In Progress',
        'color' => '#36A2EB',
      ],
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

    TaskStatus::insert([
      ['name' => 'Pending', 'color' => '#E70A1D', 'completed_field' => false],
      [
        'name' => 'In Progress',
        'color' => '#36A2EB',
        'completed_field' => false,
      ],
      ['name' => 'Completed', 'color' => '#2BFF10', 'completed_field' => true],
      ['name' => 'On Hold', 'color' => '#FFA500', 'completed_field' => false],
      ['name' => 'Cancelled', 'color' => '#808080', 'completed_field' => false],
      ['name' => 'Review', 'color' => '#FFD700', 'completed_field' => false],
      ['name' => 'Testing', 'color' => '#8A2BE2', 'completed_field' => false],
    ]);

    TaskPriority::insert([
      ['name' => 'Low', 'color' => '#FFCE56'],
      ['name' => 'Medium', 'color' => '#00FF00'],
      ['name' => 'High', 'color' => '#FF1493'],
      ['name' => 'Urgent', 'color' => '#FF1500'],
    ]);

    $projects = Project::get();
    $projects->each(function ($project) {
      $phase1 = Phase::factory()->create([
        'name' => 'Phase 1',
        'project_id' => $project->id,
      ]);
      $phase2 = Phase::factory()->create([
        'name' => 'Phase 2',
        'project_id' => $project->id,
      ]);
      $phase3 = Phase::factory()->create([
        'name' => 'Phase 3',
        'project_id' => $project->id,
      ]);

      $milestone1 = Milestone::factory()->create([
        'name' => 'Milestone 1',
        'project_id' => $project->id,
        'phase_id' => $phase1->id,
      ]);
      $milestone2 = Milestone::factory()->create([
        'name' => 'Milestone 2',
        'project_id' => $project->id,
        'phase_id' => $phase2->id,
      ]);

      $milestone3 = Milestone::factory()->create([
        'name' => 'Milestone 3',
        'project_id' => $project->id,
        'phase_id' => $phase3->id,
      ]);
    });

    $this->call(DataSeeder::class);
    $this->call(PayrollSeeder::class);
    $this->call(EmployeeProfileSeeder::class);

    // Create a role
    // $role = Role::create(['name' => 'admin']);
    // Role::create(['name' => 'developer']);
    // Role::create(['name' => 'employee']);

    // // Create a permission
    // $permission = Permission::create(['name' => 'Everything !']);

    // // Give a permission to a role
    // $role->givePermissionTo($permission);
  }
}
