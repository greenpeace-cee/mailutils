<?php

use Civi\Mailutils\Upgrade\MailutilsTemplateUpgrade;
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

  public function upgrade_1100() {
    $this->ctx->log->info('Applying update 1100');
    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_mailutils_template` LIKE 'support_case_category_id';");
    if (!$column_exists) {
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_mailutils_template` ADD COLUMN `support_case_category_id` int unsigned NULL COMMENT 'Support Case Category'");
      $logging = new CRM_Logging_Schema();
      $logging->fixSchemaDifferences();
    }
    return TRUE;
  }

  public function upgrade_1101() {
    $this->ctx->log->info('Applying update 1101');
    $column_exists = CRM_Core_DAO::singleValueQuery("SHOW COLUMNS FROM `civicrm_mailutils_setting` LIKE 'advanced_config';");
    if (!$column_exists) {
      CRM_Core_DAO::executeQuery("ALTER TABLE `civicrm_mailutils_setting` ADD COLUMN `advanced_config` text COMMENT 'Advanced Configuration Options as JSON'");
      $logging = new CRM_Logging_Schema();
      $logging->fixSchemaDifferences();
    }
    return TRUE;
  }

  /**
   * Replaces '\n' to '<br>' at the 'message' field
   * to the all of exist MailutilsTemplate items
   *
   * @return bool
   */
  public function upgrade_1102() {
    $this->ctx->log->info('Applying update 1102. Replaces me');
    MailutilsTemplateUpgrade::updateMessages([
      "\n" => '<br>',
      "\r" => '',
    ]);

    return TRUE;
  }

}
