<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    for ($i = 0; $i < 100; $i++) {
      Task::factory()->create([
        'created_at' => now()->subMonths(rand(0, 11)),
      ]);
    }
  }
}
