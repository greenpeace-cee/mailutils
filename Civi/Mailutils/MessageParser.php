<?php

namespace Civi\Mailutils;

class MessageParser {

  /**
   * Extract message body from provided eczMail instance
   *
   * @param \ezcMail $mail
   *
   * @return array
   */
  public static function getBody(\ezcMail $mail): array {
    $body = [];
    foreach ($mail->fetchParts() as $part) {
      if ($part instanceof \ezcMailText) {
        $body[] = [
          'headers' => $part->headers->getCaseSensitiveArray(),
          'text'    => $part->text,
        ];
      }
    }
    return $body;
  }

}
