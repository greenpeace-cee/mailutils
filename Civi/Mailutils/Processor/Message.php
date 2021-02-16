<?php

namespace Civi\Mailutils\Processor;

use Civi\Api4\MailutilsMessage;
use Civi\Api4\MailutilsMessageParty;

class Message {

  protected $mail;
  protected $activityId;

  public function __construct(\ezcMail $mail, int $activityId) {
    $this->mail = $mail;
    $this->activityId = $activityId;
  }

  public function process() {
    // TODO: extract into a shared ezcMail-to-MailutilsMessage library since we'll probably need this for outgoing too?
    $body = [];
    foreach ($this->mail->fetchParts() as $part) {
      if ($part instanceof \ezcMailText) {
        $body[] = [
          'headers' => $part->headers->getCaseSensitiveArray(),
          'text'    => $part->text,
        ];
      }
    }
    // TODO: parse Delivered-To header and set mail_setting_id (with alias handling)
    $message = MailutilsMessage::create()
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
      ->setCheckPermissions(FALSE)
      ->execute()
      ->first();

    // extract all involved parties and store as message parties
    foreach (['from', 'to', 'cc', 'bcc'] as $partyTypeValue => $partyType) {
      $parties = $this->mail->{$partyType};
      if (!is_array($parties)) {
        // 'from' contains a single ezcMailAddress, make it an array too
        $parties = [$parties];
      }

      foreach ($parties as $party) {
        // TODO: set contact_id
        // TODO: remove ugly $partyTypeValue hack once API4 properly supports pseudoconstants in some way
        MailutilsMessageParty::create()
          ->addValue('party_type_id', $partyTypeValue + 1)
          ->addValue('name', $party->name)
          ->addValue('email', $party->email)
          ->addValue('mailutils_message_id', $message['id'])
          ->execute();
      }
    }

  }

  protected function stripBrackets(string $value) {
    return str_replace(['<', '>'], '', $value);
  }

}
