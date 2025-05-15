<?php

use Civi\Api4;

class CRM_Mailutils_Utils_MessageRenderer {

  public static function render($activity_id) {
    $email = Api4\MailutilsEmailActivity::get(FALSE)
      ->addSelect('*')
      ->addWhere('id', '=', $activity_id)
      ->execute()
      ->first();

    if (is_null($email)) return NULL;

    $smarty = CRM_Core_Smarty::singleton();

    $renderedMessage = $smarty->fetchWith('CRM/Mailutils/EmailActivityDetails.tpl', [
      'from'    => htmlentities($email['from']),
      'to'      => htmlentities($email['to']),
      'cc'      => htmlentities($email['cc']),
      'bcc'     => htmlentities($email['bcc']),
      'subject' => htmlentities($email['subject']),
      'date'    => htmlentities($email['date']),
      'body'    => nl2br(trim($email['body'])),
    ]);

    return CRM_Utils_String::purifyHTML($renderedMessage);
  }

}
