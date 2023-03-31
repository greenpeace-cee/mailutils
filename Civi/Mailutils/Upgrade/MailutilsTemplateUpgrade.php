<?php

namespace Civi\Mailutils\Upgrade;

use Civi\Api4\MailutilsTemplate;
use Civi\Mailutils\Utils\StringHelper;

class MailutilsTemplateUpgrade {

  /**
   * Replaces some string to another at the 'message' field
   * to the all of exist MailutilsTemplate items
   *
   * @param $replaceOptions
   * @return void
   */
  public static function updateMessages($replaceOptions) {
    $mailutilsTemplates = MailutilsTemplate::get(FALSE)->execute();

    foreach ($mailutilsTemplates as $mailutilsTemplate) {
      $message = $mailutilsTemplate['message'];
      $updatedMessage = StringHelper::searchAndReplace($mailutilsTemplate['message'], $replaceOptions);

      if ($updatedMessage !== $message) {
        MailutilsTemplate::update(FALSE)
          ->addWhere('id', '=', $mailutilsTemplate['id'])
          ->addValue('message', $updatedMessage)
          ->execute();
      }
    }
  }

}
