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
    $this->connection->_transport->delete($emailId);
    $this->connection->_transport->expunge($emailId);
  }

}
