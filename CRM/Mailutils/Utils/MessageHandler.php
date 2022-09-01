<?php

class CRM_Mailutils_Utils_MessageHandler {

  const START_SMARTY_ESCAPE_WORD = '__MAGIC__';
  const END_SMARTY_ESCAPE_WORD = '__END_MAGIC__';

  /**
   * Decode html entities inside START_WORD and END_WORD at the string
   * Uses to escape smarty code
   * It prevents smarty errors
   *
   * @param $message
   * @return string
   */
  public static function prepareMessage($message) {
    if (empty($message)) {
      return '';
    }

    $regExp = '/' . self::START_SMARTY_ESCAPE_WORD .'.[^' . self::START_SMARTY_ESCAPE_WORD .'][^' . self::END_SMARTY_ESCAPE_WORD .']*' . self::END_SMARTY_ESCAPE_WORD .'/m';
    preg_match_all($regExp, $message, $matches,PREG_PATTERN_ORDER);

    if (!empty($matches[0])) {
      foreach ($matches[0] as $match) {
        $decodedString = html_entity_decode($match, ENT_QUOTES);
        $message = str_replace($match, $decodedString, $message);
      }
    }

    return $message;
  }

}
