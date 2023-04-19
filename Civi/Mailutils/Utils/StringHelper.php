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
      return false;
    }

    if (preg_match('//u', $string) === 1) {
      return true;
    }

    return false;
  }

}
