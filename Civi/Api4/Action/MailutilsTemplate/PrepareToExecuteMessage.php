<?php

namespace Civi\Api4\Action\MailutilsTemplate;

use Civi\Api4\Generic\Result;
use CRM_Mailutils_Utils_MessageHandler;

class PrepareToExecuteMessage extends \Civi\Api4\Generic\AbstractAction {

  /**
   * Message of the MailutilsTemplate entity
   *
   * @var string
   * @required
   */
  protected $message;

  /**
   * @param \Civi\Api4\Generic\Result $result
   */
  public function _run(Result $result) {
    $result[] = [
      'preparedToExecuteMessage' => CRM_Mailutils_Utils_MessageHandler::prepareToExecuteMessage($this->message),
    ];
  }

}
