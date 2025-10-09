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
    Schema::create('interactions', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->foreignId('campaign_id')->nullable()->constrained()->onDelete('set null');

      // Polymorphic relationship to various entities (contacts, leads, etc.)
      $table->morphs('interactable');

      $table
        ->enum('type', ['call', 'email', 'meeting', 'note', 'sms', 'social_media', 'webinar', 'demo', 'other'])
        ->default('note');
      $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
      $table->string('subject')->nullable();
      $table->text('description');
      $table->datetime('interaction_date');
      $table->integer('duration_minutes')->nullable(); // For calls and meetings
      $table->enum('outcome', ['positive', 'neutral', 'negative'])->nullable();

      // Follow-up tracking
      $table->boolean('follow_up_required')->default(false);
      $table->text('follow_up_notes')->nullable();
      $table->datetime('follow_up_date')->nullable();
      $table->datetime('follow_up_completed_at')->nullable();

      // Additional data
      $table->json('attendees')->nullable(); // For meetings
      $table->json('attachments')->nullable();
      $table->json('contact_details')->nullable(); // Phone, email used

      $table->timestamps();

      // Indexes
      $table->index(['type', 'interaction_date']);
      $table->index(['direction', 'interaction_date']);
      $table->index(['user_id', 'interaction_date']);
      $table->index(['follow_up_date']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('interactions');
  }
};
