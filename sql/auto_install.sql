-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from schema.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--


-- +--------------------------------------------------------------------+
-- | Copyright CiviCRM LLC. All rights reserved.                        |
-- |                                                                    |
-- | This work is published under the GNU AGPLv3 license with some      |
-- | permitted exceptions and without any warranty. For full license    |
-- | and copyright information, see https://civicrm.org/licensing       |
-- +--------------------------------------------------------------------+
--
-- Generated from drop.tpl
-- DO NOT EDIT.  Generated by CRM_Core_CodeGen
--
-- /*******************************************************
-- *
-- * Clean up the exisiting tables
-- *
-- *******************************************************/

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `civicrm_mailutils_message_party`;
DROP TABLE IF EXISTS `civicrm_mailutils_message`;
DROP TABLE IF EXISTS `civicrm_mailutils_thread`;

SET FOREIGN_KEY_CHECKS=1;
-- /*******************************************************
-- *
-- * Create new tables
-- *
-- *******************************************************/

-- /*******************************************************
-- *
-- * civicrm_mailutils_thread
-- *
-- * CiviCRM mailutils thread container
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mailutils_thread` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MailutilsThread ID' 
,
        PRIMARY KEY (`id`)
 
 
 
)    ;

-- /*******************************************************
-- *
-- * civicrm_mailutils_message
-- *
-- * CiviCRM mailutils message container
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mailutils_message` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MailutilsMessage ID',
     `activity_id` int unsigned NOT NULL   COMMENT 'FK to Activity',
     `mail_setting_id` int unsigned    COMMENT 'FK to Mail Setting',
     `mailutils_thread_id` int unsigned    COMMENT 'FK to Mailutils Thread',
     `subject` varchar(255) NULL   COMMENT 'Subject',
     `subject_normalized` varchar(255) NULL   COMMENT 'Normalized Subject',
     `message_id` varchar(995) NOT NULL   COMMENT 'Message ID',
     `in_reply_to` varchar(995)    COMMENT 'Message ID this message is in reply to',
     `headers` text NOT NULL   COMMENT 'Mail Envelope headers (JSON)',
     `body` text NOT NULL   COMMENT 'Mail content (JSON)' 
,
        PRIMARY KEY (`id`)
 
 
,          CONSTRAINT FK_civicrm_mailutils_message_activity_id FOREIGN KEY (`activity_id`) REFERENCES `civicrm_activity`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_mailutils_message_mail_setting_id FOREIGN KEY (`mail_setting_id`) REFERENCES `civicrm_mail_settings`(`id`) ON DELETE SET NULL,          CONSTRAINT FK_civicrm_mailutils_message_mailutils_thread_id FOREIGN KEY (`mailutils_thread_id`) REFERENCES `civicrm_mailutils_thread`(`id`) ON DELETE SET NULL  
)    ;

-- /*******************************************************
-- *
-- * civicrm_mailutils_message_party
-- *
-- * CiviCRM mailutils message parties
-- *
-- *******************************************************/
CREATE TABLE `civicrm_mailutils_message_party` (


     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Unique MailutilsMessageParty ID',
     `mailutils_message_id` int unsigned    COMMENT 'FK to Mailutils Message',
     `contact_id` int unsigned NULL   COMMENT 'FK to Contact',
     `party_type_id` int unsigned NOT NULL   COMMENT 'Message Party Type (From, To, CC, BCC)',
     `name` varchar(255) NULL   COMMENT 'Party name',
     `email` varchar(255) NOT NULL   COMMENT 'Party email' 
,
        PRIMARY KEY (`id`)
 
 
,          CONSTRAINT FK_civicrm_mailutils_message_party_mailutils_message_id FOREIGN KEY (`mailutils_message_id`) REFERENCES `civicrm_mailutils_message`(`id`) ON DELETE CASCADE,          CONSTRAINT FK_civicrm_mailutils_message_party_contact_id FOREIGN KEY (`contact_id`) REFERENCES `civicrm_contact`(`id`) ON DELETE SET NULL  
)    ;

 