<?php

class CRM_Mailutils_Utils_MessageHandler {

  const START_SMARTY_ESCAPE_WORD = '__MAGIC__';
  const END_SMARTY_ESCAPE_WORD = '__END_MAGIC__';

  /**
   * Prepares message to save in database.
   * It saves with:
   *  - smarty escape words
   *  - new lines inside smarty code
   * Decode html entities inside 'START_WORD' and 'END_WORD' at the string
   * Uses to escape smarty code
   * It prevents smarty errors
   *
   * @param $message
   * @return string
   */
  public static function prepareToSaveMessage($message) {
    if (empty($message)) {
      return '';
    }

    preg_match_all(self::getRegExpression(), $message, $matches, PREG_PATTERN_ORDER);

    if (!empty($matches[0])) {
      foreach ($matches[0] as $match) {
        $decodedString = html_entity_decode($match, ENT_QUOTES);
        $message = str_replace($match, $decodedString, $message);
      }
    }

    return $message;
  }

  /**
   * @return string
   */
  public static function getRegExpression(): string {
    return'/' . self::START_SMARTY_ESCAPE_WORD .'.[^' . self::START_SMARTY_ESCAPE_WORD .'][^' . self::END_SMARTY_ESCAPE_WORD .']*' . self::END_SMARTY_ESCAPE_WORD .'/m';
  }

  /**
   * Prepares message to execute
   * Removes:
   *  - smarty escape words
   *  - new lines inside smarty code
   *
   * @param $message
   * @return string
   */
  public static function prepareToExecuteMessage($message): string {
    if (empty($message)) {
      return '';
    }

    preg_match_all(self::getRegExpression(), $message, $matches, PREG_PATTERN_ORDER);

    if (!empty($matches[0])) {
      foreach ($matches[0] as $match) {
        $fixedString = str_replace([
          '&nbsp;',
          ' ',
          '<br>',
          '<br/>',
          '<br />',
          '<p>',
          '</p>',
          '\r\n',
          '\n',
          '
',// is is not a format issue it is new line, TODO: can it replace to some code?
        ],'', $match);
        $message = str_replace($match, $fixedString, $message);
      }
    }

    $message = str_replace([self::END_SMARTY_ESCAPE_WORD, self::START_SMARTY_ESCAPE_WORD,], '', $message);;

    return $message;
  }

}
