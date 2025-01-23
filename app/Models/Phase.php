<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
  use HasFactory;
  protected $guarded = [];

  public function project()
  {
    return $this->belongsTo(Project::class);
  }

  public function milestones()
  {
    return $this->hasMany(Milestone::class)->orderBy('order', 'ASC');
  }

  public function tasks()
  {
    return $this->hasMany(Task::class)->orderBy('order', 'ASC');
  }
}
