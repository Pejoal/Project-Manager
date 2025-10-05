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
    Schema::create('opportunities', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->text('description')->nullable();
      $table->decimal('value', 15, 2);
      $table->integer('probability')->default(10); // Percentage 0-100
      $table
        ->enum('stage', [
          'prospecting',
          'qualification',
          'needs_analysis',
          'proposal',
          'negotiation',
          'closed_won',
          'closed_lost',
        ])
        ->default('prospecting');
      $table->date('expected_close_date');
      $table->date('actual_close_date')->nullable();
      $table->enum('type', ['new_business', 'existing_business', 'renewal'])->default('new_business');
      $table->string('lead_source')->nullable();
      $table->text('next_steps')->nullable();
      $table->json('competitors')->nullable();
      $table->text('loss_reason')->nullable();
      $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
      $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();

      $table->index(['stage', 'expected_close_date']);
      $table->index(['assigned_to', 'stage']);
      $table->index(['contact_id', 'stage']);
      $table->index('probability');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('opportunities');
  }
};
