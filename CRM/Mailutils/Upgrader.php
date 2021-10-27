<?php
use CRM_Mailutils_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_Mailutils_Upgrader extends CRM_Mailutils_Upgrader_Base {

  public function upgrade_1000() {
    $this->ctx->log->info('Applying update 1000');
    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_mailutils_setting` LIKE 'from_email_address_id';");
    if (!$column_exists) {
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_mailutils_setting` ADD COLUMN `from_email_address_id` int unsigned NOT NULL COMMENT 'From Email Address'");
    }
    CRM_Core_DAO::executeQuery("
      ALTER TABLE `civicrm_mailutils_message`
      CHANGE COLUMN `headers` `headers` LONGTEXT NOT NULL COMMENT 'Mail Envelope headers (JSON)',
      CHANGE COLUMN `body` `body` LONGTEXT NOT NULL COMMENT 'Mail content (JSON)'"
    );
    $logging = new CRM_Logging_Schema();
    $logging->fixSchemaDifferences();
    return TRUE;
  }

}
