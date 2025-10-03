<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('payslips', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->string('payslip_number')->unique();
      $table->date('pay_period_start');
      $table->date('pay_period_end');
      $table->date('pay_date');
      $table->decimal('regular_hours', 8, 2)->default(0);
      $table->decimal('overtime_hours', 8, 2)->default(0);
      $table->decimal('regular_rate', 8, 2)->default(0);
      $table->decimal('overtime_rate', 8, 2)->default(0);
      $table->decimal('gross_regular_pay', 10, 2)->default(0);
      $table->decimal('gross_overtime_pay', 10, 2)->default(0);
      $table->decimal('gross_total_pay', 10, 2)->default(0);
      $table->json('tax_deductions')->nullable();
      $table->decimal('total_tax_deductions', 10, 2)->default(0);
      $table->json('other_deductions')->nullable();
      $table->decimal('total_other_deductions', 10, 2)->default(0);
      $table->decimal('net_pay', 10, 2)->default(0);
      $table->json('bonuses')->nullable();
      $table->decimal('total_bonuses', 10, 2)->default(0);
      $table->string('status')->default('draft');
      $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
      $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->json('metadata')->nullable();
      $table->timestamps();

      $table->index(['user_id', 'pay_period_start']);
      $table->index(['status']);
      $table->index(['pay_date']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('payslips');
  }
};
