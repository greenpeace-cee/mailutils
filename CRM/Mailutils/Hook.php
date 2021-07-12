<?php

class CRM_Mailutils_Hook extends CRM_Utils_Hook {
  public function invokeViaUF(
    $numParams,
    &$arg1, &$arg2, &$arg3, &$arg4, &$arg5, &$arg6,
    $fnSuffix
  ) {
    throw new Exception('Not implemented');
  }

  public static function greenpeaceEmailProcessor($type, &$params, $mail, &$result, $mailSettings) {
    return self::singleton()
      ->invoke(['type', 'params', 'mail', 'result', 'mailSettings'], $type, $params, $mail, $result, $mailSettings, self::$_nullObject, 'civicrm_greenpeaceEmailProcessor');
  }
}
