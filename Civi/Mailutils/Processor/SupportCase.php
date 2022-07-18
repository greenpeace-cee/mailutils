<?php

namespace Civi\Mailutils\Processor;

use Civi\Api4\Activity;
use Civi\Api4\ActivityContact;
use Civi\Api4\MailutilsMessage;
use Civi\Api4\MailutilsMessageParty;

class SupportCase {

  protected $activityId;
  protected $supportCaseCategoryId;

  public function __construct(int $activityId, int $supportCaseCategoryId) {
    $this->activityId = $activityId;
    $this->supportCaseCategoryId = $supportCaseCategoryId;
  }

  public function process() {
    $activity = Activity::get(FALSE)
      ->addWhere('id', '=', $this->activityId)
      ->addChain('contact', ActivityContact::get(FALSE)
        ->addWhere('record_type_id', '=', 2)
        ->addWhere('activity_id', '=', '$id'),
        0)
      ->addChain('message', MailutilsMessage::get(FALSE)
        ->addWhere('activity_id', '=', '$id'),
        0)
      ->execute()
      ->first();
    $case_id = $this->getSupportCaseForThread($activity['message']['mailutils_thread_id']);
    if (empty($case_id)) {
      // this is a new thread, create a case
      $categoryField = \CRM_Core_BAO_CustomField::getCustomFieldID('category', \CRM_Supportcase_Install_Entity_CustomGroup::CASE_DETAILS, TRUE);

      $case = civicrm_api3('Case', 'create', [
        'contact_id'   => $activity['contact']['contact_id'],
        'case_type_id' => 'support_case',
        'subject'      => $this->getCaseSubject($activity['subject']),
        'start_date'   => $activity['activity_date_time'],
        'status_id'    => 'Open',
        $categoryField => $this->supportCaseCategoryId,
        'medium_id'    => \CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'medium_id', 'email'),
      ]);
      $case_id = $case['id'];
    }
    else {
      // re-open case
      civicrm_api3('Case', 'create', [
        'id' => $case_id,
        'status_id'    => 'Open',
        'track_status_change' => TRUE,
      ]);
    }
    civicrm_api3('Activity', 'fileoncase', [
      'id' => $this->activityId,
      'case_id' => $case_id,
    ]);
    return $case_id;
  }

  /**
   * Get case subject based on the email subject
   *
   * Case subject only allows 128 characters, cut off with ellipsis if length is
   * exceeded
   *
   * @param $subject
   *
   * @return string
   */
  public static function getCaseSubject($subject): string {
    if (empty($subject)) {
      return '(no subject)';
    }

    $encodedLength = mb_strlen(str_replace(['<', '>'], ['&lt;', '&gt;'], $subject));
    if ($encodedLength <= 128) {
      return $subject;
    }

    // we can't use a naive mb_substr implementation because of encoding madness
    // since we also don't want to cut in the middle of an HTML entity, we'll need
    // to loop from 127 to 0 and see how many non-encoded characters we need to
    // remove in order to reach a length of 128 when encoded
    for ($i = 127; $i >= 0; $i--) {
      $subject = mb_substr($subject, 0, $i);
      // we need to replicate CRM_Utils_API_HTMLInputCoder's behaviour
      // by replacing <> with the corresponding HTML entity as it will affect
      // the final string length when persisting to DB
      $encodedLength = mb_strlen(str_replace(['<', '>'], ['&lt;', '&gt;'], $subject));
      if ($encodedLength <= 127) {
        // the encoded subject string is now below the DB size limit, append
        // ellipsis and return
        return $subject . '…';
      }
    }
    // this should never happen™
    return '(no subject)';
  }

  private static function getSupportCaseForThread($threadId) {
    return \CRM_Core_DAO::singleValueQuery(
      "SELECT   case_activity.case_id AS case_id
        FROM     civicrm_mailutils_message message
        JOIN     civicrm_activity activity ON activity.id = message.activity_id
        JOIN     civicrm_case_activity case_activity ON case_activity.activity_id = activity.id
        WHERE    message.mailutils_thread_id = %1
        ORDER BY activity.activity_date_time DESC
        LIMIT 1",
    [1 => [$threadId, 'Integer']]);
  }

}
