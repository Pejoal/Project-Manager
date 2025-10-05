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
    Schema::create('leads', function (Blueprint $table) {
      $table->id();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->unique();
      $table->string('phone')->nullable();
      $table->string('company')->nullable();
      $table->string('job_title')->nullable();
      $table->enum('status', ['new', 'contacted', 'qualified', 'unqualified', 'converted', 'lost'])->default('new');
      $table
        ->enum('source', [
          'website',
          'referral',
          'social_media',
          'email_campaign',
          'phone_call',
          'trade_show',
          'advertisement',
          'other',
        ])
        ->default('website');
      $table->integer('score')->default(0); // Lead scoring
      $table->decimal('estimated_value', 15, 2)->nullable();
      $table->text('notes')->nullable();
      $table->json('custom_fields')->nullable();
      $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $table->unsignedBigInteger('converted_contact_id')->nullable(); // Will add FK later
      $table->timestamp('last_contacted_at')->nullable();
      $table->timestamp('converted_at')->nullable();
      $table->timestamps();

      $table->index(['status', 'created_at']);
      $table->index(['assigned_to', 'status']);
      $table->index('score');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('leads');
  }
};
