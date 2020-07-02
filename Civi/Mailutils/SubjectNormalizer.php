<?php

namespace Civi\Mailutils;

class SubjectNormalizer {

  /**
   * Match common subject line prefixes like "Re:" or "Fwd:"
   * Based on https://stackoverflow.com/questions/9153629/regex-code-for-removing-fwd-re-etc-from-email-subject#comment81160171_11640925
   */
  const PREFIX_REGEX = '/^([\[\(] *)?(RE?S?|FYI|RIF|I|FS|VB|RV|ENC|ODP|PD|YNT|ILT|SV|VS|VL|AW|WG|ΑΠ|ΣΧΕΤ|ΠΡΘ|תגובה|הועבר|主题|转发|FWD?) *([-:;)\]][ :;\])-]*|$)|\]+ *$/im';


  /**
   * Normalize a subject line by stripping common prefixes and trimming
   *
   * @param string $subject
   *
   * @return string
   */
  public static function normalize(string $subject) {
    // remove matching prefixes one at a time until none are left
    do {
      $subject = trim(
        preg_replace(
          SubjectNormalizer::PREFIX_REGEX,
          '',
          $subject,
          -1,
          $count
        )
      );
    } while ($count > 0);
    return $subject;
  }
}
