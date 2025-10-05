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
    Schema::create('campaign_contact', function (Blueprint $table) {
      $table->id();
      $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
      $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
      $table
        ->enum('status', ['pending', 'sent', 'delivered', 'opened', 'clicked', 'responded', 'unsubscribed', 'bounced'])
        ->default('pending');
      $table->timestamp('sent_at')->nullable();
      $table->timestamp('delivered_at')->nullable();
      $table->timestamp('opened_at')->nullable();
      $table->timestamp('clicked_at')->nullable();
      $table->timestamp('responded_at')->nullable();
      $table->integer('open_count')->default(0);
      $table->integer('click_count')->default(0);
      $table->json('tracking_data')->nullable(); // Additional tracking information
      $table->timestamps();

      $table->unique(['campaign_id', 'contact_id']);
      $table->index(['campaign_id', 'status']);
      $table->index(['contact_id', 'status']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('campaign_contact');
  }
};
