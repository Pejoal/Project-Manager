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

        // Create initial payroll settings
        PayrollSettings::firstOrCreate(
            ['id' => 1],
            [
                'company_name' => config('app.name', 'Your Company'),
                'company_address' => '123 Business Street, City, State 12345',
                'company_tax_id' => '12-3456789',
                'pay_period' => 'monthly',
                'pay_day' => 30,
                'default_hourly_rate' => 15.00,
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'standard_start_time' => '09:00:00',
                'standard_end_time' => '17:00:00',
                'break_duration_minutes' => 60,
                'auto_calculate_overtime' => true,
                'require_approval_for_overtime' => true,
                'auto_generate_time_entries' => true,
                'currency' => 'USD',
                'timezone' => 'UTC',
            ]
        );

        // Create sample tax configurations
        $taxConfigurations = [
            [
                'name' => 'Federal Income Tax',
                'type' => 'progressive',
                'rate' => null,
                'brackets' => [
                    ['min' => 0, 'max' => 10275, 'rate' => 10],
                    ['min' => 10276, 'max' => 41775, 'rate' => 12],
                    ['min' => 41776, 'max' => 89450, 'rate' => 22],
                    ['min' => 89451, 'max' => 190750, 'rate' => 24],
                    ['min' => 190751, 'max' => 364200, 'rate' => 32],
                    ['min' => 364201, 'max' => 462500, 'rate' => 35],
                    ['min' => 462501, 'max' => null, 'rate' => 37],
                ],
                'minimum_income' => 0,
                'maximum_income' => null,
                'is_active' => true,
                'applies_to' => 'all',
                'priority' => 1,
                'description' => 'US Federal Income Tax (2024 brackets)',
            ],
            [
                'name' => 'Social Security Tax',
                'type' => 'percentage',
                'rate' => 6.2,
                'brackets' => null,
                'minimum_income' => 0,
                'maximum_income' => 160200, // 2024 wage base limit
                'is_active' => true,
                'applies_to' => 'all',
                'priority' => 2,
                'description' => 'Social Security tax on wages up to the wage base limit',
            ],
            [
                'name' => 'Medicare Tax',
                'type' => 'percentage',
                'rate' => 1.45,
                'brackets' => null,
                'minimum_income' => 0,
                'maximum_income' => null,
                'is_active' => true,
                'applies_to' => 'all',
                'priority' => 3,
                'description' => 'Medicare tax on all wages',
            ],
            [
                'name' => 'Additional Medicare Tax',
                'type' => 'percentage',
                'rate' => 0.9,
                'brackets' => null,
                'minimum_income' => 200000, // Single filer threshold
                'maximum_income' => null,
                'is_active' => true,
                'applies_to' => 'all',
                'priority' => 4,
                'description' => 'Additional Medicare tax on high earners',
            ],
            [
                'name' => 'State Income Tax',
                'type' => 'percentage',
                'rate' => 5.0,
                'brackets' => null,
                'minimum_income' => 0,
                'maximum_income' => null,
                'is_active' => false, // Disabled by default as it varies by state
                'applies_to' => 'all',
                'priority' => 5,
                'description' => 'Sample state income tax (adjust rate based on your state)',
            ],
            [
                'name' => 'Unemployment Insurance',
                'type' => 'percentage',
                'rate' => 0.6,
                'brackets' => null,
                'minimum_income' => 0,
                'maximum_income' => 7000, // Federal unemployment wage base
                'is_active' => true,
                'applies_to' => 'employees',
                'priority' => 6,
                'description' => 'Federal Unemployment Tax Act (FUTA)',
            ],
        ];

        foreach ($taxConfigurations as $config) {
            TaxConfiguration::firstOrCreate(
                ['name' => $config['name']],
                $config
            );
        }

        // Assign admin role to existing users who might be administrators
        $adminUsers = User::whereIn('email', [
            'pejoal.official@gmail.com',
            'test@example.com'
        ])->get();

        foreach ($adminUsers as $user) {
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }

        $this->command->info('Payroll system seeded successfully!');
        $this->command->info('Created roles: admin, employee, manager');
        $this->command->info('Created ' . count($permissions) . ' permissions');
        $this->command->info('Created initial payroll settings');
        $this->command->info('Created ' . count($taxConfigurations) . ' tax configurations');
    }
}
