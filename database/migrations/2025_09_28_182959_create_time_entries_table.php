<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('time_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('task_id')->constrained()->onDelete('cascade');
      $table->foreignId('project_id')->constrained()->onDelete('cascade');
      $table->dateTime('start_datetime');
      $table->dateTime('end_datetime');
      $table->decimal('hours_worked', 8, 2)->default(0);
      $table->decimal('hourly_rate', 8, 2)->default(0);
      $table->decimal('gross_amount', 10, 2)->default(0);
      $table->text('description')->nullable();
      $table->boolean('is_approved')->default(false);
      $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamp('approved_at')->nullable();
      $table->timestamps();

      $table->index(['user_id', 'start_datetime']);
      $table->index(['project_id', 'start_datetime']);
      $table->index(['is_approved']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('time_entries');
  }
};
