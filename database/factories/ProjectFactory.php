<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
  public function definition()
  {
    return [
      'name' => $this->faker->company,
      'description' => $this->faker->catchPhrase,
      'slug' => $this->faker->slug,
    ];
  }
}
