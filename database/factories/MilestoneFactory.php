<?php namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
  public function definition()
  {
    return [
      'name' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'project_id' => \App\Models\Project::factory(),
      'phase_id' => \App\Models\Phase::factory(),
    ];
  }
}
