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
    Schema::table('projects', function (Blueprint $table) {
      $table->foreignId('status_id')->nullable()->constrained('project_statuses')->nullOnDelete();
      $table->foreignId('priority_id')->nullable()->constrained('project_priorities')->nullOnDelete();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('projects', function (Blueprint $table) {
      $table->dropForeign(['status_id']);
      $table->dropForeign(['priority_id']);
      $table->dropColumn(['status_id', 'priority_id']);
    });
  }
};
