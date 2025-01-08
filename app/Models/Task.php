<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'description', 'project_id'];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function assignedTo()
  {
    return $this->belongsToMany(User::class, 'task_user');
  }
}
