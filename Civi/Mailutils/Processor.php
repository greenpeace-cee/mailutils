<?php

namespace Civi\Mailutils;

use Civi\Mailutils\Processor\Message;

class Processor {

  protected $mail;
  protected $activityId;

  public function __construct(\ezcMail $mail, int $activityId) {
    $this->mail = $mail;
    $this->activityId = $activityId;
  }

  public function process() {
    $meta = new Message($this->mail, $this->activityId);
    $meta->process();
  }

}
