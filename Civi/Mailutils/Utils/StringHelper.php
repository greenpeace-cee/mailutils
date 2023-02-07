<?php

namespace Civi\Mailutils\Utils;

class StringHelper {

  /**
   * @param $string
   * @param $options
   * @return string
   */
  public static function searchAndReplace($string, $options): string {
    if (empty($string) || empty($options)) {
      return '';
    }

    $searchStrings  = [];
    $replaceStrings = [];

    foreach ($options as $searchString => $replaceString) {
      $searchStrings[] = $searchString;
      $replaceStrings[] = $replaceString;
    }

    return str_replace($searchStrings, $replaceStrings, $string);
  }

  /**
   * @param $string
   * @return boolean
   */
  public static function isValidUTF8Format($string) {
    if (empty($string)) {
      return FALSE;
    }

    if (preg_match('//u', $string) === 1) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Cut off string with ellipsis if length is exceeded.
   * Prevents cutting in the middle of an HTML entities.
   *
   * @param $string
   * @param $cutLength
   * @return string
   */
  public static function smartCutString($string, $cutLength = 255) {
    if (empty($string)) {
      return '';
    }

    if (self::getEncodedLength($string) <= $cutLength) {
      return $string;
    }

    // we can't use a naive mb_substr implementation because of encoding madness
    // since we also don't want to cut in the middle of an HTML entity, we'll need
    // to loop from [$cutLength - 1] to 0 and see how many non-encoded characters we need to
    // remove in order to reach a length of [$cutLength] when encoded
    for ($i = $cutLength - 1; $i >= 0; $i--) {
      $string = mb_substr($string, 0, $i);
      // we need to replicate CRM_Utils_API_HTMLInputCoder's behaviour
      // by replacing <> with the corresponding HTML entity as it will affect
      // the final string length when persisting to DB
      if (self::getEncodedLength($string) <= $cutLength - 1) {
        // the encoded string is now below the DB size limit, append
        // ellipsis and return
        return $string . '…';
      }
    }

    return '';
  }

  /**
   * @param $string
   * @return int
   */
  private static function getEncodedLength($string) {
    return mb_strlen(str_replace(['<', '>'], ['&lt;', '&gt;'], $string));
  }

}
