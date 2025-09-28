<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('payroll_settings', function (Blueprint $table) {
      $table->id();
      $table->string('company_name');
      $table->string('company_address')->nullable();
      $table->string('company_tax_id')->nullable();
      $table->string('pay_period')->default('monthly'); // weekly, bi_weekly, monthly
      $table->integer('pay_day')->default(30); // Day of month for monthly, day of week for weekly
      $table->decimal('default_hourly_rate', 8, 2)->default(15.0);
      $table->json('working_days')->default('["monday","tuesday","wednesday","thursday","friday"]');
      $table->time('standard_start_time')->default('09:00:00');
      $table->time('standard_end_time')->default('17:00:00');
      $table->integer('break_duration_minutes')->default(60);
      $table->boolean('auto_calculate_overtime')->default(true);
      $table->boolean('require_approval_for_overtime')->default(true);
      $table->boolean('auto_generate_time_entries')->default(true);
      $table->string('currency', 3)->default('USD');
      $table->string('timezone')->default('UTC');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('payroll_settings');
  }
};
