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
    Schema::create('support_tickets', function (Blueprint $table) {
      $table->id();
      $table->string('ticket_number')->unique(); // AUTO-generated unique number
      $table->string('subject');
      $table->text('description');
      $table->enum('status', ['open', 'in_progress', 'pending_customer', 'resolved', 'closed'])->default('open');
      $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
      $table->enum('type', ['bug', 'feature_request', 'question', 'complaint', 'other'])->default('question');
      $table->enum('source', ['email', 'phone', 'chat', 'web_form', 'social_media'])->default('web_form');
      $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
      $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamp('first_response_at')->nullable();
      $table->timestamp('resolved_at')->nullable();
      $table->timestamp('closed_at')->nullable();
      $table->integer('satisfaction_rating')->nullable(); // 1-5 scale
      $table->text('satisfaction_comment')->nullable();
      $table->json('tags')->nullable();
      $table->json('custom_fields')->nullable();
      $table->timestamps();

      $table->index(['status', 'priority']);
      $table->index(['assigned_to', 'status']);
      $table->index(['contact_id', 'status']);
      $table->index('created_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('support_tickets');
  }
};
