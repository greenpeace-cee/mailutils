<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from mailutils/xml/schema/CRM/Mailutils/004-MailutilsMessage.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:989d07fd865bc92c8fbab6789decb0f1)
 */

/**
 * Database access object for the MailutilsMessage entity.
 */
class CRM_Mailutils_DAO_MailutilsMessage extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_mailutils_message';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique MailutilsMessage ID
   *
   * @var int
   */
  public $id;

  /**
   * FK to Activity
   *
   * @var int
   */
  public $activity_id;

  /**
   * FK to Mail Setting
   *
   * @var int
   */
  public $mail_setting_id;

  /**
   * FK to Mailutils Thread
   *
   * @var int
   */
  public $mailutils_thread_id;

  /**
   * Subject
   *
   * @var string
   */
  public $subject;

  /**
   * Normalized Subject
   *
   * @var string
   */
  public $subject_normalized;

  /**
   * Message ID
   *
   * @var string
   */
  public $message_id;

  /**
   * Message ID this message is in reply to
   *
   * @var string
   */
  public $in_reply_to;

  /**
   * Mail Envelope headers (JSON)
   *
   * @var text
   */
  public $headers;

  /**
   * Mail content (JSON)
   *
   * @var text
   */
  public $body;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_mailutils_message';
    parent::__construct();
  }

  /**
   * Returns foreign keys and entity references.
   *
   * @return array
   *   [CRM_Core_Reference_Interface]
   */
  public static function getReferenceColumns() {
    if (!isset(Civi::$statics[__CLASS__]['links'])) {
      Civi::$statics[__CLASS__]['links'] = static::createReferenceColumns(__CLASS__);
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'activity_id', 'civicrm_activity', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'mail_setting_id', 'civicrm_mail_settings', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'mailutils_thread_id', 'civicrm_mailutils_thread', 'id');
      CRM_Core_DAO_AllCoreTables::invoke(__CLASS__, 'links_callback', Civi::$statics[__CLASS__]['links']);
    }
    return Civi::$statics[__CLASS__]['links'];
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
          'description' => CRM_Mailutils_ExtensionUtil::ts('Unique MailutilsMessage ID'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_message.id',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
        ],
        'activity_id' => [
          'name' => 'activity_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Mailutils_ExtensionUtil::ts('FK to Activity'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_message.activity_id',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'FKClassName' => 'CRM_Activity_DAO_Activity',
        ],
        'mail_setting_id' => [
          'name' => 'mail_setting_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Mailutils_ExtensionUtil::ts('FK to Mail Setting'),
          'where' => 'civicrm_mailutils_message.mail_setting_id',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'FKClassName' => 'CRM_Core_DAO_MailSettings',
        ],
        'mailutils_thread_id' => [
          'name' => 'mailutils_thread_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Mailutils_ExtensionUtil::ts('FK to Mailutils Thread'),
          'where' => 'civicrm_mailutils_message.mailutils_thread_id',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'FKClassName' => 'CRM_Mailutils_DAO_MailutilsThread',
        ],
        'subject' => [
          'name' => 'subject',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Subject'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Subject'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_message.subject',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'subject_normalized' => [
          'name' => 'subject_normalized',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Subject (Normalized)'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Normalized Subject'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_message.subject_normalized',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'message_id' => [
          'name' => 'message_id',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Message ID'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Message ID'),
          'required' => TRUE,
          'maxlength' => 995,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_message.message_id',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'in_reply_to' => [
          'name' => 'in_reply_to',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('In Reply To'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Message ID this message is in reply to'),
          'maxlength' => 995,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_message.in_reply_to',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'headers' => [
          'name' => 'headers',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Headers'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Mail Envelope headers (JSON)'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_message.headers',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'body' => [
          'name' => 'body',
          'type' => CRM_Utils_Type::T_TEXT,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Body'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Mail content (JSON)'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_message.body',
          'table_name' => 'civicrm_mailutils_message',
          'entity' => 'MailutilsMessage',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessage',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mailutils_message', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mailutils_message', $prefix, []);
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
