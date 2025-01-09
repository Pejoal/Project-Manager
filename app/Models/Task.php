<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
  use HasFactory;

  protected $guarded = [];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function assignedTo()
  {
    return $this->belongsToMany(User::class, 'task_user');
  }

  public function status()
  {
    return $this->belongsTo(TaskStatus::class);
  }

  public function priority()
  {
    return $this->belongsTo(TaskPriority::class);
  }
}
