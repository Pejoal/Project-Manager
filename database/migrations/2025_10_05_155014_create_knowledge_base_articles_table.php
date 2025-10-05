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
    Schema::create('knowledge_base_articles', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('slug')->unique();
      $table->text('content');
      $table->text('excerpt')->nullable();
      $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
      $table->json('categories')->nullable();
      $table->json('tags')->nullable();
      $table->integer('view_count')->default(0);
      $table->decimal('rating', 3, 2)->default(0); // Average rating 0-5
      $table->integer('rating_count')->default(0);
      $table->boolean('is_featured')->default(false);
      $table->integer('sort_order')->default(0);
      $table->json('attachments')->nullable();
      $table->json('related_articles')->nullable(); // Array of article IDs
      $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
      $table->timestamp('published_at')->nullable();
      $table->timestamps();

      $table->index(['status', 'published_at']);
      $table->index(['is_featured', 'sort_order']);
      $table->index('author_id');
      $table->fullText(['title', 'content', 'excerpt']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('knowledge_base_articles');
  }
};
