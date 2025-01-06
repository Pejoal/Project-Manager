<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->withPersonalTeam()->create();

    User::factory()
      ->withPersonalTeam()
      ->create([
        'name' => 'Pejoal Hanna',
        'username' => 'pejoal',
        'email' => 'pejoal.official@gmail.com',
      ]);

    User::factory()
      ->create([
        'name' => 'Test User',
        'username' => 'test',
        'email' => 'test@example.com',
      ]);
  }
}
