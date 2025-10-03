<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\PayrollSettings;
use App\Models\TaxConfiguration;
use App\Models\User;

class PayrollSeeder extends Seeder
{
  public function run(): void
  {
    // Create payroll-related permissions
    $permissions = [
      // Time Entries
      'view time entries',
      'create time entries',
      'edit time entries',
      'delete time entries',
      'approve time entries',
      'view own time entries',
      'edit own time entries',
      'delete own time entries',

      // Employee Profiles
      'view employee profiles',
      'create employee profiles',
      'edit employee profiles',
      'delete employee profiles',
      'activate employee profiles',

      // Payslips
      'view payslips',
      'generate payslips',
      'approve payslips',
      'delete payslips',
      'mark payslips as paid',

      // Payroll Management
      'view payroll dashboard',
      'manage payroll settings',
      'view payroll reports',
      'generate time entries automatically',

      // Tax Configurations
      'view tax configurations',
      'create tax configurations',
      'edit tax configurations',
      'delete tax configurations',
    ];

    foreach ($permissions as $permission) {
      Permission::firstOrCreate(['name' => $permission]);
    }

    // Create or update admin role with all permissions
    $adminRole = Role::firstOrCreate(['name' => 'admin']);
    $adminRole->syncPermissions(Permission::all());

    // Create employee role with limited permissions
    $employeeRole = Role::firstOrCreate(['name' => 'employee']);
    $employeeRole->syncPermissions([
      'view own time entries',
      'create time entries',
      'edit own time entries',
      'delete own time entries',
    ]);

    // Create manager role with moderate permissions
    $managerRole = Role::firstOrCreate(['name' => 'manager']);
    $managerRole->syncPermissions([
      'view time entries',
      'view own time entries',
      'create time entries',
      'edit own time entries',
      'delete own time entries',
      'view employee profiles',
      'view payroll reports',
    ]);

    // Create German Payroll Settings (Default)
    $this->createGermanPayrollSettings();

    // Create US Tax Configurations (Alternative system)
    $this->createUSTaxConfigurations();

    // Assign admin role to existing users who might be administrators
    $adminUsers = User::whereIn('email', ['pejoal.official@gmail.com', 'test@example.com'])->get();

    foreach ($adminUsers as $user) {
      if (!$user->hasRole('admin')) {
        $user->assignRole('admin');
      }
    }

    $this->command->info('Payroll system seeded successfully!');
    $this->command->info('Created roles: admin, employee, manager');
    $this->command->info('Created ' . count($permissions) . ' permissions');
    $this->command->info('Created German payroll settings (default)');
    $this->command->info('Created tax configurations for both German and US systems');
  }

  /**
   * Create German payroll settings and tax configurations
   */
  private function createGermanPayrollSettings(): void
  {
    PayrollSettings::firstOrCreate(
      ['id' => 1],
      [
        'company_name' => config('app.name', 'Your Company'),
        'company_address' => 'Musterstraße 123, 10115 Berlin, Germany',
        'company_tax_id' => 'DE123456789',
        'pay_period' => 'monthly',
        'pay_day' => 28, // Last working day of month (typically 28th)
        'default_hourly_rate' => 15.0,
        'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        'standard_start_time' => '09:00:00',
        'standard_end_time' => '17:00:00',
        'break_duration_minutes' => 30, // German standard
        'auto_calculate_overtime' => true,
        'require_approval_for_overtime' => true,
        'auto_generate_time_entries' => true,
        'currency' => 'EUR',
        'timezone' => 'Europe/Berlin',
      ]
    );

    // German Tax Configurations
    $germanTaxes = [
      [
        'name' => 'German Income Tax (Lohnsteuer)',
        'type' => 'progressive',
        'rate' => null,
        'brackets' => [
          ['min' => 0, 'max' => 10908, 'rate' => 0], // Tax-free allowance (Grundfreibetrag)
          ['min' => 10909, 'max' => 15999, 'rate' => 14], // Entry zone
          ['min' => 16000, 'max' => 62809, 'rate' => 24], // First progression zone
          ['min' => 62810, 'max' => 277825, 'rate' => 42], // Second progression zone
          ['min' => 277826, 'max' => null, 'rate' => 45], // Top tax rate (Reichensteuer)
        ],
        'minimum_income' => 0,
        'maximum_income' => null,
        'is_active' => true,
        'applies_to' => 'all',
        'priority' => 1,
        'description' => 'German progressive income tax (2024 rates)',
      ],
      [
        'name' => 'Solidarity Surcharge (Solidaritätszuschlag)',
        'type' => 'percentage',
        'rate' => 5.5,
        'brackets' => null,
        'minimum_income' => 18130, // Only applies above exemption limit
        'maximum_income' => null,
        'is_active' => true,
        'applies_to' => 'all',
        'priority' => 2,
        'description' => 'Solidarity surcharge on income tax (5.5% of income tax)',
      ],
      [
        'name' => 'Pension Insurance (Rentenversicherung)',
        'type' => 'percentage',
        'rate' => 9.3, // Employee share (18.6% total, split 50/50)
        'brackets' => null,
        'minimum_income' => 520, // Minijob threshold
        'maximum_income' => 90600, // 2024 contribution ceiling West Germany
        'is_active' => true,
        'applies_to' => 'employees',
        'priority' => 3,
        'description' => 'Employee contribution to German pension insurance',
      ],
      [
        'name' => 'Unemployment Insurance (Arbeitslosenversicherung)',
        'type' => 'percentage',
        'rate' => 1.3, // Employee share (2.6% total, split 50/50)
        'brackets' => null,
        'minimum_income' => 520,
        'maximum_income' => 90600,
        'is_active' => true,
        'applies_to' => 'employees',
        'priority' => 4,
        'description' => 'Employee contribution to unemployment insurance',
      ],
      [
        'name' => 'Health Insurance (Krankenversicherung)',
        'type' => 'percentage',
        'rate' => 7.3, // Employee share (14.6% base rate, split 50/50)
        'brackets' => null,
        'minimum_income' => 520,
        'maximum_income' => 62100, // 2024 contribution ceiling
        'is_active' => true,
        'applies_to' => 'employees',
        'priority' => 5,
        'description' => 'Employee contribution to health insurance',
      ],
      [
        'name' => 'Long-term Care Insurance (Pflegeversicherung)',
        'type' => 'percentage',
        'rate' => 1.7, // Employee share (3.4% total for childless over 23)
        'brackets' => null,
        'minimum_income' => 520,
        'maximum_income' => 62100,
        'is_active' => true,
        'applies_to' => 'employees',
        'priority' => 6,
        'description' => 'Employee contribution to long-term care insurance',
      ],
      [
        'name' => 'Church Tax (Kirchensteuer)',
        'type' => 'percentage',
        'rate' => 8.0, // 8% in most states, 9% in Bavaria and Baden-Württemberg
        'brackets' => null,
        'minimum_income' => 0,
        'maximum_income' => null,
        'is_active' => false, // Disabled by default as it's optional
        'applies_to' => 'all',
        'priority' => 7,
        'description' => 'Optional church tax (8% of income tax in most German states)',
      ],
    ];

    foreach ($germanTaxes as $config) {
      TaxConfiguration::firstOrCreate(['name' => $config['name']], $config);
    }

    $this->command->info('✓ Created German payroll settings and tax configurations');
  }

  /**
   * Create US tax configurations (alternative system)
   */
  private function createUSTaxConfigurations(): void
  {
    $usTaxes = [
      [
        'name' => 'US Federal Income Tax',
        'type' => 'progressive',
        'rate' => null,
        'brackets' => [
          ['min' => 0, 'max' => 11600, 'rate' => 10],
          ['min' => 11601, 'max' => 47150, 'rate' => 12],
          ['min' => 47151, 'max' => 100525, 'rate' => 22],
          ['min' => 100526, 'max' => 191950, 'rate' => 24],
          ['min' => 191951, 'max' => 243725, 'rate' => 32],
          ['min' => 243726, 'max' => 609350, 'rate' => 35],
          ['min' => 609351, 'max' => null, 'rate' => 37],
        ],
        'minimum_income' => 0,
        'maximum_income' => null,
        'is_active' => false, // Disabled by default (German is default)
        'applies_to' => 'all',
        'priority' => 101,
        'description' => 'US Federal Income Tax (2024 brackets - single filer)',
      ],
      [
        'name' => 'US Social Security Tax',
        'type' => 'percentage',
        'rate' => 6.2,
        'brackets' => null,
        'minimum_income' => 0,
        'maximum_income' => 168600, // 2024 wage base limit
        'is_active' => false,
        'applies_to' => 'all',
        'priority' => 102,
        'description' => 'Social Security tax on wages up to the wage base limit',
      ],
      [
        'name' => 'US Medicare Tax',
        'type' => 'percentage',
        'rate' => 1.45,
        'brackets' => null,
        'minimum_income' => 0,
        'maximum_income' => null,
        'is_active' => false,
        'applies_to' => 'all',
        'priority' => 103,
        'description' => 'Medicare tax on all wages',
      ],
      [
        'name' => 'US Additional Medicare Tax',
        'type' => 'percentage',
        'rate' => 0.9,
        'brackets' => null,
        'minimum_income' => 200000, // Single filer threshold
        'maximum_income' => null,
        'is_active' => false,
        'applies_to' => 'all',
        'priority' => 104,
        'description' => 'Additional Medicare tax on high earners (over $200,000)',
      ],
      [
        'name' => 'US State Income Tax (Sample)',
        'type' => 'percentage',
        'rate' => 5.0,
        'brackets' => null,
        'minimum_income' => 0,
        'maximum_income' => null,
        'is_active' => false,
        'applies_to' => 'all',
        'priority' => 105,
        'description' => 'Sample state income tax (rate varies by state)',
      ],
      [
        'name' => 'US Federal Unemployment Tax (FUTA)',
        'type' => 'percentage',
        'rate' => 0.6,
        'brackets' => null,
        'minimum_income' => 0,
        'maximum_income' => 7000, // Federal unemployment wage base
        'is_active' => false,
        'applies_to' => 'employees',
        'priority' => 106,
        'description' => 'Federal Unemployment Tax Act (FUTA)',
      ],
    ];

    foreach ($usTaxes as $config) {
      TaxConfiguration::firstOrCreate(['name' => $config['name']], $config);
    }

    $this->command->info('✓ Created US tax configurations (disabled by default)');
  }
}
