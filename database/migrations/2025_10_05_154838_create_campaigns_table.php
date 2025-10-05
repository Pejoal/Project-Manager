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
    Schema::create('campaigns', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->text('description')->nullable();
      $table
        ->enum('type', ['email', 'social_media', 'webinar', 'trade_show', 'direct_mail', 'telemarketing', 'other'])
        ->default('email');
      $table->enum('status', ['draft', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
      $table->decimal('budget', 15, 2)->nullable();
      $table->decimal('actual_cost', 15, 2)->default(0);
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->integer('target_audience_size')->default(0);
      $table->integer('actual_audience_size')->default(0);
      $table->integer('leads_generated')->default(0);
      $table->integer('opportunities_created')->default(0);
      $table->decimal('revenue_generated', 15, 2)->default(0);
      $table->json('goals')->nullable(); // Campaign objectives
      $table->json('channels')->nullable(); // Email, Social, etc.
      $table->json('demographics')->nullable(); // Target demographics
      $table->text('content')->nullable();
      $table->json('attachments')->nullable();
      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();

      $table->index(['status', 'start_date']);
      $table->index(['type', 'status']);
      $table->index('created_by');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('campaigns');
  }
};
