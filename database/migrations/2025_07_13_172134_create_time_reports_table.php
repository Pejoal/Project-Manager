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
    Schema::create('time_reports', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('project_id')->constrained()->onDelete('cascade');
      $table->date('start_date');
      $table->date('end_date');
      $table->decimal('total_hours', 8, 2); // Up to 999,999.99 hours
      $table->json('breakdown'); // Store detailed breakdown by task/day
      $table->enum('period_type', ['daily', 'weekly', 'monthly', 'custom'])->default('weekly');
      $table->timestamps();

      $table->index(['user_id', 'project_id']);
      $table->index(['start_date', 'end_date']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('time_reports');
  }
};
