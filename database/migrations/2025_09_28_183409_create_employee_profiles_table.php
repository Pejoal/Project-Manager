<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('employee_profiles', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
      $table->string('employee_id')->unique();
      $table->decimal('base_hourly_rate', 8, 2)->default(0);
      $table->decimal('overtime_rate_multiplier', 3, 2)->default(1.5);
      $table->integer('standard_hours_per_day')->default(8);
      $table->integer('standard_hours_per_week')->default(40);
      $table->enum('payment_method', ['bank_transfer', 'cash', 'check'])->default('bank_transfer');
      $table->string('bank_account_number')->nullable();
      $table->string('bank_name')->nullable();
      $table->string('tax_id')->nullable();
      $table->json('tax_exemptions')->nullable();
      $table->boolean('is_active')->default(true);
      $table->date('hire_date')->nullable();
      $table->date('termination_date')->nullable();
      $table->timestamps();

      $table->index(['is_active']);
      $table->index(['hire_date']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('employee_profiles');
  }
};
