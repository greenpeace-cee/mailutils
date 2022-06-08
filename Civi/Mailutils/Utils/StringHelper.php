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

}
