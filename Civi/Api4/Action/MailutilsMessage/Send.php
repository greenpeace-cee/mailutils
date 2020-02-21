<?php

namespace Civi\Api4\Action\MailutilsMessage;

use Civi\Api4\Activity;
use Civi\Api4\Generic\Result;
use Civi\Api4\MailutilsMessage;

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
    $message = MailutilsMessage::get()
      ->setSelect([
        'mail_setting_id',
        'subject',
        'message_id',
        'in_reply_to',
        'headers',
        'body',
        'activity.id',
        'activity.status_id',
        'mailutils_message_parties.party_type_id',
        'mailutils_message_parties.name',
        'mailutils_message_parties.email',
      ])
      ->addWhere('id', '=', $this->messageId)
      ->execute()
      ->first();
    $draftStatus = \CRM_Core_PseudoConstant::getKey(
      'CRM_Activity_BAO_Activity',
      'status_id',
      'Draft'
    );

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

    // TODO: send email, process results

    Activity::update()
      ->addWhere('id', '=', $message['activity.id'])
      ->addValue('status_id', \CRM_Core_PseudoConstant::getKey(
        'CRM_Activity_BAO_Activity',
        'status_id',
        'Completed'
      ))
      ->execute();

    $result[] = ['message_status' => 'sent'];
  }

}
