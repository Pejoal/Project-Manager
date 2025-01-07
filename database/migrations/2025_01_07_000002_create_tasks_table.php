
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
      $table->string('assigned_to')->nullable();
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('tasks');
  }
}

