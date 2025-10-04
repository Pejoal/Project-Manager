<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Client extends Model
{
  use HasFactory, LogsActivity;

  protected $fillable = ['name', 'email', 'phone'];
  protected $guarded = [];

  public function projects()
  {
    return $this->belongsToMany(Project::class);
  }

  public function getActivitylogOptions(): LogOptions
  {
    return LogOptions::defaults()
      ->logOnly(['name', 'email', 'phone', 'address'])
      ->logOnlyDirty()
      ->dontSubmitEmptyLogs()
      ->setDescriptionForEvent(fn(string $eventName) => "Client has been {$eventName}");
  }
}
