<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from mailutils/xml/schema/CRM/Mailutils/002-MailutilsTemplate.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:749244f93c26b31d46ec6416cbbf6928)
 */

/**
 * Database access object for the MailutilsTemplate entity.
 */
class CRM_Mailutils_DAO_MailutilsTemplate extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_mailutils_template';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique MailutilsTemplate ID
   *
   * @var int
   */
  public $id;

  /**
   * Name
   *
   * @var string
   */
  public $name;

  /**
   * Template Category
   *
   * @var int
   */
  public $template_category_id;

  /**
   * Message
   *
   * @var text
   */
  public $message;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_mailutils_template';
    parent::__construct();
  }

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Mailutils_ExtensionUtil::ts('Unique MailutilsTemplate ID'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_template.id',
          'table_name' => 'civicrm_mailutils_template',
          'entity' => 'MailutilsTemplate',
          'bao' => 'CRM_Mailutils_DAO_MailutilsTemplate',
          'localizable' => 0,
        ],
        'name' => [
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Name'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Name'),
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_template.name',
          'table_name' => 'civicrm_mailutils_template',
          'entity' => 'MailutilsTemplate',
          'bao' => 'CRM_Mailutils_DAO_MailutilsTemplate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'template_category_id' => [
          'name' => 'template_category_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Template Category'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Template Category'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_template.template_category_id',
          'table_name' => 'civicrm_mailutils_template',
          'entity' => 'MailutilsTemplate',
          'bao' => 'CRM_Mailutils_DAO_MailutilsTemplate',
          'localizable' => 0,
          'html' => [
            'type' => 'Select',
          ],
          'pseudoconstant' => [
            'optionGroupName' => 'mailutils_template_category',
            'optionEditPath' => 'civicrm/admin/options/mailutils_template_category',
          ],
        ],
        'message' => [
          'name' => 'message',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Message'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Message'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_template.message',
          'table_name' => 'civicrm_mailutils_template',
          'entity' => 'MailutilsTemplate',
          'bao' => 'CRM_Mailutils_DAO_MailutilsTemplate',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
      ];
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'fields_callback', Civi::$statics[__CLASS__]['fields']);
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key (as used in fields()).
   *
   * @return array
   *   Array(string $name => string $uniqueName).
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table
   *
   * @return string
   */
  public static function getTableName() {
    return self::$_tableName;
  }

  /**
   * Returns if this table needs to be logged
   *
   * @return bool
   */
  public function getLog() {
    return self::$_log;
  }

  /**
   * Returns the list of fields that can be imported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &import($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mailutils_template', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of fields that can be exported
   *
   * @param bool $prefix
   *
   * @return array
   */
  public static function &export($prefix = FALSE) {
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mailutils_template', $prefix, []);
    return $r;
  }

  /**
   * Returns the list of indices
   *
   * @param bool $localize
   *
   * @return array
   */
  public static function indices($localize = TRUE) {
    $indices = [];
    return ($localize && !empty($indices)) ? CRM_Core_DAO_AllCoreTables::multilingualize(__CLASS__, $indices) : $indices;
  }

}
