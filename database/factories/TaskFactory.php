<?php namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Phase;
use App\Models\Project;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
  public function definition()
  {
    $project = Project::inRandomOrder()->first();
    if (!$project) {
      throw new \Exception('No projects found');
    }
    $phase = Phase::where('project_id', $project->id)
      ->inRandomOrder()
      ->first();
    if (!$phase) {
      throw new \Exception('No phases found for project ID ' . $project->id);
    }
    $milestone = Milestone::where('phase_id', $phase->id)
      ->inRandomOrder()
      ->first();
    if (!$milestone) {
      throw new \Exception('No milestones found for phase ID ' . $phase->id);
    }

    return [
      'name' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'status_id' => TaskStatus::inRandomOrder()->first()->id,
      'priority_id' => TaskPriority::inRandomOrder()->first()->id,
      'project_id' => $project->id,
      'phase_id' => $phase->id,
      'milestone_id' => $milestone->id,
    ];
  }
}
