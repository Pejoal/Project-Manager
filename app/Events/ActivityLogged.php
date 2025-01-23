<?php
namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ActivityLogged
{
  use SerializesModels;

  public $user;
  public $type;
  public $description;
  public $subject;

  public function __construct($user, $type, $description, $subject)
  {
    $this->user = $user;
    $this->type = $type;
    $this->description = $description;
    $this->subject = $subject;
  }
}
