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
      $table->foreignId('milestone_id')->nullable()->constrained()->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('tasks', function (Blueprint $table) {
      $table->dropColumn('milestone_id');
    });
  }
};
