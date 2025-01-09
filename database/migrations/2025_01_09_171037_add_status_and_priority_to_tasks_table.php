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
    Schema::table('tasks', function (Blueprint $table) {
      $table
        ->foreignId('status_id')
        ->constrained('task_statuses')
        ->onDelete('cascade');
      $table
        ->foreignId('priority_id')
        ->constrained('task_priorities')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('tasks', function (Blueprint $table) {
      $table->dropForeign(['status_id']);
      $table->dropForeign(['priority_id']);
      $table->dropColumn(['status_id', 'priority_id']);
    });
  }
};
