<?php

namespace Civi\Mailutils\Processor;

use Civi\Api4\Activity;
use Civi\Api4\ActivityContact;
use Civi\Api4\MailutilsMessage;
use Civi\Api4\MailutilsMessageParty;
use Civi\Mailutils\MessageParser;

class Message {

  protected $mail;
  protected $activityId;
  protected $mailSettings;

  public function __construct(\ezcMail $mail, int $activityId, array $mailSettings) {
    $this->mail = $mail;
    $this->activityId = $activityId;
    $this->mailSettings = $mailSettings;
  }

  public function process() {
    $body = MessageParser::getBody($this->mail);

    $message = MailutilsMessage::create(TRUE)
      ->addValue('activity_id', $this->activityId)
      ->addValue('message_id', $this->stripBrackets(
        $this->mail->getHeader('message-id')
      ))
      ->addValue('in_reply_to', $this->stripBrackets(
        $this->mail->getHeader('in-reply-to')
      ))
      ->addValue('headers', json_encode(
        $this->mail->headers->getCaseSensitiveArray()
      ))
      ->addValue('body', json_encode($body))
      ->addValue('subject', $this->mail->subject)
      ->addValue('mail_setting_id', $this->mailSettings['id'])
      ->setCheckPermissions(FALSE)
      ->execute()
      ->first();

    // extract all involved parties and store as message parties
    foreach (['from', 'to', 'cc', 'bcc'] as $partyType) {
      $parties = $this->mail->{$partyType};
      if (!is_array($parties)) {
        // 'from' contains a single ezcMailAddress, make it an array too
        $parties = [$parties];
      }

      foreach ($parties as $party) {
        MailutilsMessageParty::create(TRUE)
          ->addValue('party_type_id:name', $partyType)
          ->addValue('name', $party->name)
          ->addValue('email', $party->email)
          ->addValue('mailutils_message_id', $message['id'])
          ->addValue('contact_id', $this->getPartyContactId($partyType, $party->email))
          ->execute();
      }
    }

    // adjust activity date to include seconds
    if (!empty($this->mail->getHeader('Date'))) {
      Activity::update(FALSE)
        ->addWhere('id', '=', $this->activityId)
        ->addValue(
          'activity_date_time',
          date('YmdHis', strtotime($this->mail->getHeader('Date')))
        )
        ->execute();
    }
  }

  /**
   * @throws \Exception
   */
  protected function getPartyContactId($partyType, $email) {
    $activityContact = ActivityContact::get(FALSE)
      ->addSelect('contact_id')
      ->setJoin([
        ['Email AS email', 'INNER', NULL, ['contact_id', '=', 'email.contact_id']],
      ])
      ->addWhere('activity_id', '=', $this->activityId)
      ->addWhere('email.email', '=', $email)
      ->addWhere('record_type_id:name', '=', ($partyType == 'from' ? 'Activity Source' : 'Activity Targets'))
      ->execute()
      ->first();
    if (empty($activityContact['contact_id'])) {
      throw new \Exception("Unable to determine party contact ID: partyType={$partyType}, activityId={$activityId}, email={$email}");
    }
    return $activityContact['contact_id'];
  }

  protected function stripBrackets(string $value) {
    return str_replace(['<', '>'], '', $value);
  }

}
