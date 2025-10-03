<?php namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Phase;
use App\Models\Project;
use App\Models\TaskStatus;
use App\Models\TaskPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
  public function definition()
  {
    $project = Project::inRandomOrder()->first();
    if (!$project) {
      throw new \Exception('No projects found');
    }
    $phase = Phase::where('project_id', $project->id)->inRandomOrder()->first();
    if (!$phase) {
      throw new \Exception('No phases found for project ID ' . $project->id);
    }
    $milestone = Milestone::where('phase_id', $phase->id)->inRandomOrder()->first();
    if (!$milestone) {
      throw new \Exception('No milestones found for phase ID ' . $phase->id);
    }

    // Generate random start datetime between -3 months and now
    $startDate = $this->faker->dateTimeBetween('-3 months', 'now');

    // Create a clone to avoid modifying the original $startDate
    $endMax = (clone $startDate)->modify('+8 hours');

    // Generate random end datetime between $startDate and $endMax
    $endDate = $this->faker->dateTimeBetween($startDate, $endMax);

    return [
      'name' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'status_id' => TaskStatus::inRandomOrder()->first()->id,
      'priority_id' => TaskPriority::inRandomOrder()->first()->id,
      'start_datetime' => $startDate,
      'end_datetime' => $endDate,
      'project_id' => $project->id,
      'phase_id' => $phase->id,
      'milestone_id' => $milestone->id,
    ];
  }

  /**
   * Configure the factory to assign random users after creation.
   */
  public function configure()
  {
    return $this->afterCreating(function ($task) {
      // Assign 1-3 random users to each task
      $userCount = rand(1, 3);
      $users = User::inRandomOrder()->limit($userCount)->pluck('id');

      if ($users->isNotEmpty()) {
        $task->assignedTo()->attach($users);
      }
    });
  }
}
