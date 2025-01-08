<?php namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
  public function definition()
  {
    return [
      'name' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'project_id' => \App\Models\Project::factory(),
    ];
  }
}
