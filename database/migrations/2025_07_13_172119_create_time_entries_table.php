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
    Schema::create('time_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('task_id')->constrained()->onDelete('cascade');
      $table->foreignId('project_id')->constrained()->onDelete('cascade');
      $table->datetime('start_time');
      $table->datetime('end_time')->nullable();
      $table->text('description')->nullable();
      $table->boolean('is_running')->default(false);
      $table->integer('duration_minutes')->default(0); // Calculated field
      $table->timestamps();

      $table->index(['user_id', 'task_id']);
      $table->index(['project_id', 'start_time']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('time_entries');
  }
};
