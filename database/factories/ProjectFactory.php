<?php

namespace Database\Factories;

use App\Models\ProjectPriority;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
  public function definition()
  {
    return [
      'name' => $this->faker->company,
      'description' => $this->faker->catchPhrase,
      'slug' => $this->faker->slug,
      'status_id' => ProjectStatus::inRandomOrder()->first()->id,
      'priority_id' => ProjectPriority::inRandomOrder()->first()->id,
    ];
  }
}
