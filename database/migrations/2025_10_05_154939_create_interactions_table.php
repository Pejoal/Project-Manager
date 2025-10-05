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
      $table
        ->enum('type', ['call', 'email', 'meeting', 'task', 'note', 'sms', 'chat', 'social_media', 'other'])
        ->default('note');
      $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
      $table->string('subject')->nullable();
      $table->text('description');
      $table->datetime('interaction_date');
      $table->integer('duration_minutes')->nullable(); // For calls and meetings
      $table
        ->enum('outcome', [
          'successful',
          'no_answer',
          'busy',
          'voicemail',
          'email_bounced',
          'meeting_scheduled',
          'follow_up_required',
          'other',
        ])
        ->nullable();
      $table->text('follow_up_notes')->nullable();
      $table->datetime('follow_up_date')->nullable();
      $table->json('attendees')->nullable(); // For meetings
      $table->json('attachments')->nullable();

      // Polymorphic relationship - can be linked to contact, lead, opportunity, or ticket
      $table->morphs('interactable');

      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Who performed the interaction
      $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
      $table->timestamps();

      // Don't add morphs index manually as it's auto-created
      $table->index(['type', 'interaction_date']);
      $table->index(['user_id', 'interaction_date']);
      $table->index('follow_up_date');
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
