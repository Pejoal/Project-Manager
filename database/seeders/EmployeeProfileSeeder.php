<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Database\Seeder;

class EmployeeProfileSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get all users
    $users = User::all();

    foreach ($users as $user) {
      // Skip if employee profile already exists
      if ($user->employeeProfile) {
        continue;
      }

      // Create employee profile with German settings
      EmployeeProfile::create([
        'user_id' => $user->id,
        'employee_id' => 'EMP' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
        'base_hourly_rate' => rand(15, 50), // Random hourly rate between €15-€50
        'overtime_rate_multiplier' => 1.5, // German standard
        'standard_hours_per_day' => 8,
        'standard_hours_per_week' => 40,
        'payment_method' => 'bank_transfer',
        'bank_account_number' => 'DE' . str_pad(rand(1000000000, 9999999999), 20, '0', STR_PAD_LEFT),
        'bank_name' => 'Deutsche Bank',
        'tax_id' => 'DE' . rand(100000000, 999999999),
        'tax_exemptions' => null,
        'is_active' => true,
        'hire_date' => now()->subMonths(rand(1, 24)),
        'termination_date' => null,
      ]);
    }

    $this->command->info('Created employee profiles for ' . $users->count() . ' users');
  }
}
