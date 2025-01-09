<?php namespace Database\Factories;

use App\Models\TaskStatus;
use App\Models\TaskPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
  public function definition()
  {
    return [
      'name' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'status_id' => TaskStatus::inRandomOrder()->first()->id,
      'priority_id' => TaskPriority::inRandomOrder()->first()->id,
      'project_id' => \App\Models\Project::factory(),
    ];
  }
}
