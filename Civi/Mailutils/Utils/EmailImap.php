<?php

namespace Civi\Mailutils\Utils;

class EmailImap extends EmailImapBase {

  /**
   * Deletes(set flag) email by message id
   * It searches email only in current folder($this->getCurrentFolder())
   *
   * @param $messageId
   * @throws \Exception
   */
  public function delete($messageId) {
    $emailId = $this->findEmailIdByMessageId($messageId);
    $this->connection->_transport->delete($emailId);
  }

  /**
   * Deletes permanently email by message id
   * It searches email only in current folder($this->getCurrentFolder())
   *
   * @param $messageId
   * @throws \Exception
   */
  public function deletePermanently($messageId) {
    $emailId = $this->findEmailIdByMessageId($messageId);
    if (!$this->connection->_transport->delete($emailId)) {
      throw new \Exception("Error while deleting message {$messageId}");
    }
    $this->connection->_transport->expunge();
  }

  /**
   * Copy message $messageId from the current mailbox to $destination
   *
   * @param $messageId
   * @param $destination
   *
   * @throws \Exception
   */
  public function copyMessage($messageId, $destination) {
    $emailId = $this->findEmailIdByMessageId($messageId);
    if (!$this->connection->_transport->copyMessages($emailId, $destination)) {
      throw new \Exception("Error while copying message {$messageId} to {$destination}");
    }
  }

}
