<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned extends Mailable
{
  use Queueable, SerializesModels;

  public $task;
  public $user;
  public $isNew;

  public function __construct(Task $task, User $user, bool $isNew)
  {
    $this->task = $task;
    $this->user = $user;
    $this->isNew = $isNew;
  }

  public function build()
  {
    return $this->subject(
      $this->isNew ? 'New Task Assigned' : 'Task Updated'
    )->view('emails.task_assigned');
  }
}
