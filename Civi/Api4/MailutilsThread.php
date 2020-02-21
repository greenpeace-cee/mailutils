<?php
namespace Civi\Api4;

/**
 * MailutilsThread entity.
 *
 * Provided by the Mail Utilities extension.
 *
 * @package Civi\Api4
 */
class MailutilsThread extends Generic\DAOEntity {
  public static function get() {
    return new \Civi\Api4\Action\MailutilsThread\Get(__CLASS__, __FUNCTION__);
  }
}
