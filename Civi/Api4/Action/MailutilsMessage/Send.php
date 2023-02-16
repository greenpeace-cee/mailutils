<?php

namespace Civi\Api4\Action\MailutilsMessage;

use Civi\Api4\Activity;
use Civi\Api4\Generic\Result;
use Civi\Api4\MailutilsMessage;
use Civi\Mailutils\MessageParser;
use Civi\Mailutils\SubjectNormalizer;

/**
 * Send a message
 *
 * @method $this setMessaegId(int $cid) Set MailutilsMessage ID  (required)
 * @method int getMessageId() Get MailutilsMessage ID
 */
class Send extends \Civi\Api4\Generic\AbstractAction {

  /**
   * ID of MailutilsMessage
   *
   * @var int
   * @required
   */
  protected $messageId;

  /**
   * @param \Civi\Api4\Generic\Result $result
   *
   * @throws \API_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public function _run(Result $result) {
    $message = $this->getMessage();
    $this->validateMessage($message);

    $options = new \ezcMailComposerOptions();
    $options->stripBccHeader = TRUE;
    $options->automaticImageInclude = FALSE;

    $mail = new \ezcMailComposer($options);
    $charset = 'utf-8';
    $mail->charset = $charset;
    $mail->subjectCharset = $charset;
    $mail->subject = $message['subject'];
    $mail->messageId = '<' . $message['message_id'] . '>';

    foreach ($message['mailutils_message_parties'] as $messageParty) {
      switch ($messageParty['party_type_id:name']) {
        case 'from':
          $mail->from = new \ezcMailAddress($messageParty['email'], $messageParty['name'], $charset);
          break;

        case 'to':
          $mail->addTo(new \ezcMailAddress($messageParty['email'], $messageParty['name'], $charset));
          break;

        case 'cc':
          $mail->addCc(new \ezcMailAddress($messageParty['email'], $messageParty['name'], $charset));
          break;

        case 'bcc':
          $mail->addBcc(new \ezcMailAddress($messageParty['email'], $messageParty['name'], $charset));
          break;
      }
    }

    if (!empty($message['in_reply_to'])) {
      $mail->setHeader('In-Reply-To', $message['in_reply_to'], $charset);
    }
    $headers = json_decode($message['headers'], TRUE);
    foreach ($headers as $name => $value) {
      $mail->setHeader($name, $value, $charset);
    }

    $body = json_decode($message['body'], TRUE);
    if (empty($body)) {
      $body = [
        'html' => $message['activity_details'],
        'text' => $message['activity_details'],
      ];
    }

    $mail->htmlText = $body['html'];
    $mail->plainText = $body['text'];

    $attachments = civicrm_api3('Attachment', 'get', [
      'return' => ['name', 'path'],
      'entity_table' => 'civicrm_activity',
      'entity_id' => $message['activity_id'],
    ]);

    foreach ($attachments['values'] as $attachment) {
      $mail->addFileAttachment($attachment['path']);
    }

    $mail->build();

    $options = new \ezcMailSmtpTransportOptions();
    $options->connectionType = \ezcMailSmtpTransport::CONNECTION_TLS;
    $transport = new \ezcMailSmtpTransport(
      $message['mailutils_setting.smtp_server'],
      $message['mailutils_setting.smtp_username'],
      $message['mailutils_setting.smtp_password'],
      $message['mailutils_setting.smtp_port'],
      $options
    );
    $body = MessageParser::getBody($mail);
    $headers = $mail->headers->getCaseSensitiveArray();
    $send = TRUE;
    if (defined('CIVICRM_MAIL_LOG') && !defined('CIVICRM_MAIL_LOG_AND_SEND')) {
      $send = FALSE;

      \Civi::log('mailutils')->debug(
        'Simulating sending of MailutilsMessage ID=' . $message['id'],
        ['headers' => $headers, 'body' => $body]
      );
      if (defined('CIVICRM_MAIL_LOG_AND_SEND')) {
        $send = TRUE;
      }
    }
    if ($send) {
      try {
        $transport->send($mail);
      }
      catch (\Exception $e) {
        \Civi::log()->error('Error sending MailutilsMessage ID=' . $message['id'] . ': ' . $e->getMessage());
        throw $e;
      }
    }
    // refresh body after sending; mailPart headers are only set after sending
    $body = MessageParser::getBody($mail);
    MailutilsMessage::update(FALSE)
      ->addWhere('id', '=', $message['id'])
      ->addValue('body', json_encode($body))
      ->addValue('headers', json_encode($headers))
      ->execute();


    $emptyAttachments = [];
    Activity::update(FALSE)
      ->addWhere('id', '=', $message['activity.id'])
      ->addValue('status_id:name', 'Completed')
      ->addValue('details', \CRM_Utils_Mail_Incoming::formatMailPart($mail->body, $emptyAttachments))
      ->execute();

    $result[] = ['message_status' => 'sent'];
  }

  private function getMessage() {
    return MailutilsMessage::get(FALSE)
      ->addSelect('*', 'mail_settings.*', 'mailutils_setting.*', 'activity.*')
      ->setJoin([
        ['MailSettings AS mail_settings', 'LEFT', NULL, ['mail_setting_id', '=', 'mail_settings.id']],
        ['MailutilsSetting AS mailutils_setting', 'INNER', NULL, ['mail_setting_id', '=', 'mailutils_setting.mail_setting_id']],
        ['Activity AS activity', 'LEFT', NULL, ['activity_id', '=', 'activity.id']],
      ])
      ->addChain('mailutils_message_parties', \Civi\Api4\MailutilsMessageParty::get(FALSE)
        ->addSelect('*', 'party_type_id:name')
        ->addWhere('mailutils_message_id', '=', '$id')
      )
      ->addWhere('id', '=', $this->messageId)
      ->execute()
      ->first();
  }

  private function validateMessage($message) {
    $draftStatus = \CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'status_id', 'Draft');

    if ($message['activity.status_id'] != $draftStatus) {
      throw new \API_Exception(
        'Cannot send message when activity status is not "Draft".',
        3001
      );
    }

    if (empty($message['mail_setting_id'])) {
      throw new \API_Exception(
        'Cannot fetch SMTP settings without mail_setting_id. (Patch to add global SMTP support welcome!)',
        3002
      );
    }

    if (empty($message['subject'])) {
      throw new \API_Exception('Subject is required field.', 3003);
    }

    if (empty(SubjectNormalizer::normalize($message['subject']))) {
      throw new \API_Exception('Subject is required field.', 3003);
    }

    if (empty($message['body'])) {
      throw new \API_Exception('Email body is required field.', 3004);
    } else {
      $body = json_decode($message['body'], TRUE);
      if (empty($body['html'])) {
        throw new \API_Exception('Cannot read email body', 3005);
      }
    }

    if (!empty($message['mailutils_message_parties'])) {
      $isFromEmailsExist = false;
      $isToEmailsExist = false;

      foreach ($message['mailutils_message_parties'] as $email) {
        if ($email['party_type_id:name'] === 'to') {
          $isToEmailsExist = true;
        }
        if ($email['party_type_id:name'] === 'from') {
          $isFromEmailsExist = true;
        }
      }

      if (!$isFromEmailsExist) {
        throw new \API_Exception('"From email" is required field.', 3007);
      }

      if (!$isToEmailsExist) {
        throw new \API_Exception('"To email" is required field.', 3008);
      }
    } else {
      throw new \API_Exception('Cannot find "To" and "From" emails.', 3006);
    }
  }

}
