<?php
namespace App\Listeners;

use App\Events\ActivityLogged;
use App\Models\Activity;

class LogActivity
{
  public function handle(ActivityLogged $event)
  {
    Activity::create([
      'user_id' => $event->user->id,
      'type' => $event->type,
      'description' => $event->description,
      'subject_id' => $event->subject->id,
      'subject_type' => get_class($event->subject),
    ]);
  }
}
