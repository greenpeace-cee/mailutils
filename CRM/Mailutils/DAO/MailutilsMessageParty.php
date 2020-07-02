<?php

/**
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 *
 * Generated from /Users/patrick/buildkit/build/coredev/web/sites/default/files/civicrm/ext/mailutils/xml/schema/CRM/Mailutils/003-MailutilsMessageParty.xml
 * DO NOT EDIT.  Generated by CRM_Core_CodeGen
 * (GenCodeChecksum:5e1618872d90e73351e546d75b7ea171)
 */

/**
 * Database access object for the MailutilsMessageParty entity.
 */
class CRM_Mailutils_DAO_MailutilsMessageParty extends CRM_Core_DAO {

  /**
   * Static instance to hold the table name.
   *
   * @var string
   */
  public static $_tableName = 'civicrm_mailutils_message_party';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   *
   * @var bool
   */
  public static $_log = TRUE;

  /**
   * Unique MailutilsMessageParty ID
   *
   * @var int
   */
  public $id;

  /**
   * FK to Mailutils Message
   *
   * @var int
   */
  public $mailutils_message_id;

  /**
   * FK to Contact
   *
   * @var int
   */
  public $contact_id;

  /**
   * Message Party Type (From, To, CC, BCC)
   *
   * @var int
   */
  public $party_type_id;

  /**
   * Party name
   *
   * @var string
   */
  public $name;

  /**
   * Party email
   *
   * @var string
   */
  public $email;

  /**
   * Class constructor.
   */
  public function __construct() {
    $this->__table = 'civicrm_mailutils_message_party';
    parent::__construct();
  }

  /**
   * Returns localized title of this entity.
   */
  public static function getEntityTitle() {
    return ts('Mailutils Message Parties');
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
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'mailutils_message_id', 'civicrm_mailutils_message', 'id');
      Civi::$statics[__CLASS__]['links'][] = new CRM_Core_Reference_Basic(self::getTableName(), 'contact_id', 'civicrm_contact', 'id');
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
          'description' => CRM_Mailutils_ExtensionUtil::ts('Unique MailutilsMessageParty ID'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_message_party.id',
          'table_name' => 'civicrm_mailutils_message_party',
          'entity' => 'MailutilsMessageParty',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessageParty',
          'localizable' => 0,
        ],
        'mailutils_message_id' => [
          'name' => 'mailutils_message_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Mailutils_ExtensionUtil::ts('FK to Mailutils Message'),
          'where' => 'civicrm_mailutils_message_party.mailutils_message_id',
          'table_name' => 'civicrm_mailutils_message_party',
          'entity' => 'MailutilsMessageParty',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessageParty',
          'localizable' => 0,
          'FKClassName' => 'CRM_Mailutils_DAO_MailutilsMessage',
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => CRM_Mailutils_ExtensionUtil::ts('FK to Contact'),
          'required' => FALSE,
          'where' => 'civicrm_mailutils_message_party.contact_id',
          'table_name' => 'civicrm_mailutils_message_party',
          'entity' => 'MailutilsMessageParty',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessageParty',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ],
        'party_type_id' => [
          'name' => 'party_type_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Party Type'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Message Party Type (From, To, CC, BCC)'),
          'required' => TRUE,
          'where' => 'civicrm_mailutils_message_party.party_type_id',
          'table_name' => 'civicrm_mailutils_message_party',
          'entity' => 'MailutilsMessageParty',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessageParty',
          'localizable' => 0,
          'html' => [
            'type' => 'Select',
          ],
          'pseudoconstant' => [
            'optionGroupName' => 'mailutils_party_type',
            'optionEditPath' => 'civicrm/admin/options/mailutils_party_type',
          ],
        ],
        'name' => [
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Party Name'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Party name'),
          'required' => FALSE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_message_party.name',
          'table_name' => 'civicrm_mailutils_message_party',
          'entity' => 'MailutilsMessageParty',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessageParty',
          'localizable' => 0,
          'html' => [
            'type' => 'Text',
          ],
        ],
        'email' => [
          'name' => 'email',
          'type' => CRM_Utils_Type::T_STRING,
          'title' => CRM_Mailutils_ExtensionUtil::ts('Party Email'),
          'description' => CRM_Mailutils_ExtensionUtil::ts('Party email'),
          'required' => TRUE,
          'maxlength' => 255,
          'size' => CRM_Utils_Type::HUGE,
          'where' => 'civicrm_mailutils_message_party.email',
          'table_name' => 'civicrm_mailutils_message_party',
          'entity' => 'MailutilsMessageParty',
          'bao' => 'CRM_Mailutils_DAO_MailutilsMessageParty',
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
    $r = CRM_Core_DAO_AllCoreTables::getImports(__CLASS__, 'mailutils_message_party', $prefix, []);
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
    $r = CRM_Core_DAO_AllCoreTables::getExports(__CLASS__, 'mailutils_message_party', $prefix, []);
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
