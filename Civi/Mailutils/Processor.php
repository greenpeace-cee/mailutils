<?php

namespace Civi\Mailutils;

use Civi\Api4\MailutilsSetting;
use Civi\Mailutils\Processor\Message;
use Civi\Mailutils\Processor\SupportCase;

class Processor {

  protected $mail;
  protected $activityId;
  protected $mailSettings;

  public function __construct(\ezcMail $mail, int $activityId, int $mailSettingId) {
    $this->mail = $mail;
    $this->activityId = $activityId;
    $this->mailSettings = civicrm_api3('MailSettings', 'getsingle', [
      'id' => $mailSettingId,
    ]);

    $mailutilsSettings = MailutilsSetting::get()
      ->addWhere('mail_setting_id', '=', $mailSettingId)
      ->setCheckPermissions(FALSE)
      ->execute()
      ->first();
    if (!empty($mailutilsSettings)) {
      unset($mailutilsSettings['id']);
      $this->mailSettings = array_merge($this->mailSettings, $mailutilsSettings);
    }
  }

  public function process() {
    $meta = new Message($this->mail, $this->activityId, $this->mailSettings);
    $meta->process();
    if (!empty($this->mailSettings['support_case_category_id'])) {
      $case = new SupportCase($this->activityId, $this->mailSettings['support_case_category_id']);
      $case->process();
    }
  }

}
