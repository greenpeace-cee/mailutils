<?php
namespace Civi\Api4\Action\MailutilsMessage;

use Civi\Api4\Generic\BasicBatchAction;
use Civi\Mailutils\Utils\EmailImap;

/**
 * Delete message permanently via IMAP
 */
class DeletePermanently extends BasicBatchAction {

  /**
   * Folder in which to look for the message
   *
   * @var string
   * @required
   */
  protected $folder;

  public function __construct($entityName, $actionName) {
    parent::__construct($entityName, $actionName, '*');
  }

  /**
   * Deletes permanently emails form email server by imap
   *
   * @param $mailutilsMessage
   * @return array
   */
  protected function doTask($mailutilsMessage) {
    $result = [
      'status' => 'success',
      'message' => 'Email successfully deleted!',
      'id' => $mailutilsMessage['id'],
      'message_id' => $mailutilsMessage['message_id'],
      'subject' => $mailutilsMessage['subject'],
      'mail_setting_id' => $mailutilsMessage['mail_setting_id'],
    ];

    try {
      $imap = EmailImap::getInstance($mailutilsMessage['mail_setting_id']);
      $imap->setCurrentFolder($this->folder);
      $imap->deletePermanently($mailutilsMessage['message_id']);
    } catch (\Exception $e) {
      $result['status'] = 'error';
      $result['message'] = 'Error via deleting message.  ' . $e->getMessage();
      return $result;
    }

    //TODO: How we need handle errors?
    //TODO: do we need delete this MailutilsMessage row from db? Or set flag is_deleted = 1?

    return $result;
  }

}
