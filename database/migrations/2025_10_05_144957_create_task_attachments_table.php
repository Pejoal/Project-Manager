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
    Schema::create('task_attachments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('task_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who uploaded
      $table->string('original_name');
      $table->string('filename'); // Stored filename
      $table->string('path'); // Storage path
      $table->string('mime_type');
      $table->unsignedBigInteger('size'); // File size in bytes
      $table->text('description')->nullable();
      $table->timestamps();

      $table->index(['task_id', 'created_at']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('task_attachments');
  }
};
