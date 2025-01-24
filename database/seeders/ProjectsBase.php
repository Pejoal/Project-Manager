<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Milestone;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class ProjectsBase extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    for ($i = 0; $i < 12; $i++) {
      $client = Client::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);

      $project = Project::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);

      $project->clients()->attach($client->id);
    }

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
  }
}
