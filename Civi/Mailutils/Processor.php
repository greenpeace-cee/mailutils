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

    $mailutilsSettings = MailutilsSetting::get(FALSE)
      ->addWhere('mail_setting_id', '=', $mailSettingId)
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
    $caseId = NULL;
    if (!empty($this->mailSettings['support_case_category_id'])) {
      $case = new SupportCase($this->activityId, $this->mailSettings['support_case_category_id']);
      $caseId = $case->process();
    }
    if (!empty($this->mailSettings['advanced_config']['postprocessor']['sqltasks'])) {
      foreach ($this->mailSettings['advanced_config']['postprocessor']['sqltasks'] as $taskId) {
        try {
          civicrm_api3('Sqltask', 'execute', [
            'id'          => $taskId,
            'input_val'   => json_encode([
              'type'                     => 'postprocessor',
              'activity_id'              => $this->activityId,
              'support_case_category_id' => $this->mailSettings['support_case_category_id'],
              'case_id'                  => $caseId,
            ]),
            'log_to_file' => 1,
            'check_permissions' => FALSE,
          ]);
        } catch (\CiviCRM_API3_Exception $e) {
          Civi::log('mailutils')->error("Error executing post-processor SQL Task {$taskId}: {$e->getMessage()}");
        }
      }
    }
  }

}
