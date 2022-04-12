<?php
namespace Civi\Api4\Action\MailutilsMessage;

use Civi\Api4\Generic\BasicBatchAction;
use Civi\Mailutils\Utils\EmailImap;

/**
 * Copy message from one folder to another via IMAP
 */
class CopyMessage extends BasicBatchAction {

  /**
   * Folder in which to look for the message
   *
   * @var string
   * @required
   */
  protected $sourceFolder;

  /**
   * Destination folder
   *
   * @var string
   * @required
   */
  protected $destinationFolder;

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
      'message' => 'Email successfully copied!',
      'id' => $mailutilsMessage['id'],
      'message_id' => $mailutilsMessage['message_id'],
      'subject' => $mailutilsMessage['subject'],
      'mail_setting_id' => $mailutilsMessage['mail_setting_id'],
    ];

    try {
      $imap = EmailImap::getInstance($mailutilsMessage['mail_setting_id']);
      $imap->setCurrentFolder($this->sourceFolder);
      $imap->copyMessage($mailutilsMessage['message_id'], $this->destinationFolder);
    } catch (\Exception $e) {
      $result['status'] = 'error';
      $result['message'] = 'Error copying message.  ' . $e->getMessage();
    }

    return $result;
  }

}
