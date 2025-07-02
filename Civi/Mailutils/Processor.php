<?php

namespace Civi\Mailutils;

use Civi\Api4;
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

    $mailutilsSettings = Api4\MailutilsSetting::get(FALSE)
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

    if (empty($this->mailSettings['advanced_config'])) return;

    // --- Process advanced configuration ------------------------------------------------------- //

    $advancedConfig = $this->mailSettings['advanced_config'];

    // Call configured SQL Tasks
    if (!empty($advancedConfig['postprocessor']['sqltasks'])) {
      foreach ($advancedConfig['postprocessor']['sqltasks'] as $taskId) {
        self::callSQLTask($taskId, [
          'type'                     => 'postprocessor',
          'activity_id'              => $this->activityId,
          'support_case_category_id' => $this->mailSettings['support_case_category_id'],
          'case_id'                  => $caseId,
        ]);
      }
    }

    // Link the email activity to a matching case
    if (!empty($advancedConfig['case_type'])) {
      $contactIds = self::findAssociatedContactIds($this->activityId);

      $cases = Api4\CiviCase::get(FALSE)
        ->addSelect('id', 'contact.contact_id')
        ->addJoin(
          'CaseContact AS contact',
          'INNER',
          ['contact.case_id', '=', 'id']
        )
        ->addWhere('case_type_id:name', '=', $advancedConfig['case_type'])
        ->addWhere('contact.contact_id', 'IN', $contactIds)
        ->execute();

      // If there is not exactly one matching case, the activity needs to be assigned manually
      if ($cases->count() === 1) {
        $case = $cases->first();

        Api4\CaseActivity::create(FALSE)
          ->addValue('case_id', $case['id'])
          ->addValue('activity_id', $this->activityId)
          ->execute();

        Api4\Activity::update(FALSE)
          ->addValue('target_contact_id', $case['contact.contact_id'])
          ->addWhere('id', '=', $this->activityId)
          ->execute();
      }
    }
  }

  private static function callSQLTask($taskId, $taskInputParams = []) {
    try {
      civicrm_api3('Sqltask', 'execute', [
        'id'          => $taskId,
        'input_val'   => json_encode($taskInputParams),
        'log_to_file' => 1,
        'check_permissions' => FALSE,
      ]);
    } catch (\CiviCRM_API3_Exception $ex) {
      $errMsg = $ex->getMessage();
      Civi::log('mailutils')->error("Error executing post-processor SQL Task $taskId: $errMsg");
    }
  }

  private static function extractEmailAddresses($string) {
    $matches = [];
    preg_match_all('/[a-z0-9_\-\.]+@[a-z0-9_\-\.]+\.[a-z0-9]+/', strtolower($string), $matches);

    return $matches[0];
  }

  private static function findAssociatedContactIds($activityId) {
    $message = Api4\MailutilsMessage::get(FALSE)
      ->addSelect('id', 'subject')
      ->addWhere('activity_id', '=', $activityId)
      ->execute()
      ->first();

    $emailBlacklist = Api4\Setting::get(FALSE)
      ->addSelect('mailutils_case_assignment_email_blacklist')
      ->execute()
      ->first()['value'];

    $messagePartyQuery = Api4\MailutilsMessageParty::get(FALSE)
      ->addSelect('contact_id')
      ->addJoin(
        'MailutilsMessage AS message',
        'INNER',
        ['message.id', '=', 'mailutils_message_id']
      )
      ->addWhere('message.id', '=', $message['id']);

    foreach ($emailBlacklist as $emailAddr) {
      $messagePartyQuery->addWhere('email', 'NOT LIKE', str_replace('*', '%', $emailAddr));
    }

    $messageParties = (array) $messagePartyQuery->execute();

    $subjectEmailAddresses = self::extractEmailAddresses($message['subject']);

    $additionalMsgPartyQuery = Api4\Email::get(FALSE)
      ->addSelect('contact_id')
      ->addWhere('email', 'IN', $subjectEmailAddresses);

    foreach ($emailBlacklist as $emailAddr) {
      $additionalMsgPartyQuery->addWhere('email', 'NOT LIKE', str_replace('*', '%', $emailAddr));
    }

    $additionalMessageParties = (array) $additionalMsgPartyQuery->execute();

    $contactIds = array_map(
      fn ($mp) => (int) $mp['contact_id'],
      array_merge($messageParties, $additionalMessageParties)
    );

    return array_unique($contactIds);
  }

}
