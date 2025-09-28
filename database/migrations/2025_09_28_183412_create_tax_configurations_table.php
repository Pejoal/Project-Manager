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
    Schema::create('tax_configurations', function (Blueprint $table) {
      $table->id();
      $table->string('name'); // e.g., "Federal Income Tax", "State Tax", "Social Security"
      $table->string('type'); // flat, percentage, progressive
      $table->decimal('rate', 5, 4)->nullable(); // For flat rate or single percentage
      $table->json('brackets')->nullable(); // For progressive tax brackets
      $table->decimal('minimum_income', 10, 2)->default(0);
      $table->decimal('maximum_income', 10, 2)->nullable();
      $table->boolean('is_active')->default(true);
      $table->string('applies_to')->default('all'); // all, employees, contractors
      $table->integer('priority')->default(0); // Order of calculation
      $table->text('description')->nullable();
      $table->timestamps();

      $table->index(['is_active', 'priority']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('tax_configurations');
  }
};
