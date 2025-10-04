<?php

namespace App\Console\Commands;

use App\Models\PayrollSettings;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Console\Command;

class PayrollGenerateTimeEntries extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'payroll:generate-time-entries
                          {--force : Force generation even if auto-generation is disabled}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Generate time entries from tasks with start_datetime and end_datetime';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $settings = PayrollSettings::current();

    // Check if auto-generation is enabled
    if (!$settings->auto_generate_time_entries && !$this->option('force')) {
      $this->error('Auto time entry generation is disabled in payroll settings.');
      $this->info('Use --force option to generate anyway.');
      return Command::FAILURE;
    }

    $this->info('Fetching tasks without time entries...');

    // Get tasks with start_datetime and end_datetime but no corresponding time entries
    $tasksWithoutEntries = Task::whereNotNull('start_datetime')
      ->whereNotNull('end_datetime')
      ->whereDoesntHave('timeEntries')
      ->with(['assignedTo.employeeProfile', 'project'])
      ->get();

    if ($tasksWithoutEntries->isEmpty()) {
      $this->info('No tasks found that need time entries.');
      return Command::SUCCESS;
    }

    $this->info("Found {$tasksWithoutEntries->count()} tasks without time entries.");

    $generatedCount = 0;
    $skippedCount = 0;

    $progressBar = $this->output->createProgressBar($tasksWithoutEntries->count());
    $progressBar->start();

    foreach ($tasksWithoutEntries as $task) {
      foreach ($task->assignedTo as $user) {
        // Skip if user doesn't have an employee profile
        if (!$user->employeeProfile) {
          $skippedCount++;
          continue;
        }

        try {
          TimeEntry::create([
            'user_id' => $user->id,
            'task_id' => $task->id,
            'project_id' => $task->project_id,
            'start_datetime' => $task->start_datetime,
            'end_datetime' => $task->end_datetime,
            'hourly_rate' => $user->employeeProfile->base_hourly_rate,
            'description' => __('payroll.time_entries.auto_generated_from_task', ['task' => $task->name]),
          ]);

          $generatedCount++;
        } catch (\Exception $e) {
          $this->error(
            "\nFailed to create time entry for task '{$task->name}' and user '{$user->name}': {$e->getMessage()}"
          );
          $skippedCount++;
        }
      }

      $progressBar->advance();
    }

    $progressBar->finish();
    $this->newLine(2);

    // Log activity if running in authenticated context (e.g., via scheduler with a system user)
    // For CLI commands, we skip activity logging unless there's a specific system user

    $this->info("✓ Successfully generated {$generatedCount} time entries.");

    if ($skippedCount > 0) {
      $this->warn("⚠ Skipped {$skippedCount} entries (missing employee profiles or errors).");
    }

    return Command::SUCCESS;
  }
}
