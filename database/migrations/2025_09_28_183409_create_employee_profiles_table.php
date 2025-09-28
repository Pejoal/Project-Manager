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
    Schema::create('employee_profiles', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->string('employee_id')->unique()->nullable();
      $table->decimal('base_hourly_rate', 8, 2)->default(0);
      $table->decimal('overtime_rate_multiplier', 3, 2)->default(1.5);
      $table->integer('standard_hours_per_day')->default(8);
      $table->integer('standard_hours_per_week')->default(40);
      $table->string('payment_method')->default('bank_transfer'); // bank_transfer, cash, check
      $table->string('bank_account_number')->nullable();
      $table->string('bank_name')->nullable();
      $table->string('tax_id')->nullable();
      $table->json('tax_exemptions')->nullable(); // Store tax exemption details
      $table->boolean('is_active')->default(true);
      $table->date('hire_date')->nullable();
      $table->date('termination_date')->nullable();
      $table->timestamps();

      $table->index(['user_id']);
      $table->index(['is_active']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('employee_profiles');
  }
};
