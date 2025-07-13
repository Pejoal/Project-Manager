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
    Schema::create('work_logs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('task_id')->constrained()->onDelete('cascade');
      $table->foreignId('project_id')->constrained()->onDelete('cascade');
      $table->date('date');
      $table->decimal('hours_worked', 5, 2); // Up to 999.99 hours
      $table->text('description')->nullable();
      $table
        ->enum('work_type', ['development', 'design', 'testing', 'meeting', 'research', 'documentation', 'other'])
        ->default('development');
      $table->timestamps();

      $table->unique(['user_id', 'task_id', 'date']);
      $table->index(['project_id', 'date']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('work_logs');
  }
};
