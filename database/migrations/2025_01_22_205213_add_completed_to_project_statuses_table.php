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
    Schema::table('project_statuses', function (Blueprint $table) {
      $table->boolean('completed_field')->default(false);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('project_statuses', function (Blueprint $table) {
      $table->dropColumn('completed_field');
    });
  }
};
