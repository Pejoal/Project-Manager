<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
  public function up()
  {
    Schema::create('projects', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->text('description')->nullable();
      $table->string('slug')->unique();
      $table->timestamps();
    });

    Schema::create('client_project', function (Blueprint $table) {
      $table->id();
      $table->foreignId('client_id')->constrained()->onDelete('cascade');
      $table->foreignId('project_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('projects');
    Schema::dropIfExists('client_project');
  }
}
