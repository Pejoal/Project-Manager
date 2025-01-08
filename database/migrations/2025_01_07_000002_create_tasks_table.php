<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
  public function up()
  {
    Schema::create('tasks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('project_id')->constrained()->onDelete('cascade');
      $table->string('name');
      $table->text('description')->nullable();
      $table->timestamps();
    });

    Schema::create('task_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('task_id')->constrained()->onDelete('cascade');
      $table->foreignId('user_id')->constrained()->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('task_user');
    Schema::dropIfExists('tasks');
  }
}
