<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('tax_configurations', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->enum('type', ['flat', 'percentage', 'progressive'])->default('percentage');
      $table->decimal('rate', 8, 2)->nullable();
      $table->json('brackets')->nullable();
      $table->decimal('minimum_income', 10, 2)->default(0);
      $table->decimal('maximum_income', 10, 2)->nullable();
      $table->boolean('is_active')->default(true);
      $table->enum('applies_to', ['all', 'employees', 'contractors'])->default('all');
      $table->integer('priority')->default(1);
      $table->text('description')->nullable();
      $table->timestamps();

      $table->index(['is_active', 'priority']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('tax_configurations');
  }
};
