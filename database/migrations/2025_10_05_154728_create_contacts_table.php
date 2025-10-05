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
    Schema::create('contacts', function (Blueprint $table) {
      $table->id();
      $table->string('first_name');
      $table->string('last_name');
      $table->string('email')->unique();
      $table->string('phone')->nullable();
      $table->string('mobile')->nullable();
      $table->string('company')->nullable();
      $table->string('job_title')->nullable();
      $table->string('department')->nullable();
      $table->text('address')->nullable();
      $table->string('city')->nullable();
      $table->string('state')->nullable();
      $table->string('country')->nullable();
      $table->string('postal_code')->nullable();
      $table->string('website')->nullable();
      $table->string('linkedin')->nullable();
      $table->string('twitter')->nullable();
      $table->enum('type', ['customer', 'prospect', 'partner', 'vendor', 'other'])->default('prospect');
      $table->enum('status', ['active', 'inactive', 'do_not_contact'])->default('active');
      $table->text('notes')->nullable();
      $table->json('custom_fields')->nullable();
      $table->json('tags')->nullable();
      $table->json('communication_preferences')->nullable(); // Email, phone, SMS preferences
      $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamp('last_contacted_at')->nullable();
      $table->date('birthday')->nullable();
      $table->timestamps();

      $table->index(['type', 'status']);
      $table->index(['assigned_to', 'status']);
      $table->index('company');
      $table->fullText(['first_name', 'last_name', 'email', 'company']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('contacts');
  }
};
