<?php

namespace Civi\Api4\Action\MailutilsTemplate;

use Civi\Api4\Generic\Result;
use CRM_Mailutils_Utils_MessageHandler;

class GetSmartyEscapeWord extends \Civi\Api4\Generic\AbstractAction {

  /**
   * @param \Civi\Api4\Generic\Result $result
   */
  public function _run(Result $result) {
    $result[] = [
      'start' => CRM_Mailutils_Utils_MessageHandler::START_SMARTY_ESCAPE_WORD,
      'end' => CRM_Mailutils_Utils_MessageHandler::END_SMARTY_ESCAPE_WORD,
    ];
  }

}
