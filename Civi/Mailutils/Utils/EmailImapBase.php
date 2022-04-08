<?php

namespace Civi\Mailutils\Utils;

use Civi\Api4\MailSettings;
use Civi\Api4\MailutilsSetting;
use ezcMailParser;

abstract class EmailImapBase {

  /**
   * The connection instances
   */
  protected static $instances = [];
  protected $connection;
  protected $mailutilsSetting;
  protected $mailSetting;
  protected $availableFolders;
  protected $selectedFolder;
  const DEFAULT_FOLDER = '[Gmail]/All Mail';

  /**
   * @param $mailSettingId
   * @return EmailImap
   * @throws \Exception
   */
  public static function getInstance($mailSettingId): EmailImap {
    if (!isset(self::$instances[$mailSettingId])) {
      self::$instances[$mailSettingId] = new static($mailSettingId);
    }

    return self::$instances[$mailSettingId];
  }

  /**
   * @param $mailSettingId
   * @throws \Exception
   */
  private function __construct($mailSettingId) {
    if (empty($mailSettingId)) {
      throw new \Exception("Empty imap connection settings");
    }

    $this->mailutilsSetting = MailutilsSetting::get()
      ->addWhere('mail_setting_id', '=', $mailSettingId)
      ->setLimit(1)
      ->execute()
      ->first();

    if (empty($this->mailutilsSetting)) {
      throw new \Exception("Cannot get MailutilsSetting by 'mail_setting_id'");
    }

    $this->mailSetting = MailSettings::get()
      ->addWhere('id', '=', $mailSettingId)
      ->setLimit(1)
      ->execute()
      ->first();
    if (empty($this->mailSetting)) {
      throw new \Exception("Cannot get MailSettings by 'mail_setting_id'");
    }

    $this->connection = \CRM_Mailing_MailStore::getStore($this->mailSetting['name']);
    $this->availableFolders = $this->connection->_transport->listMailboxes();
    $this->setCurrentFolder(EmailImapBase::DEFAULT_FOLDER);
  }

  /**
   * @return array
   */
  public function getAvailableFolders() {
    return $this->availableFolders;
  }

  /**
   * Get all emails in current folder
   *
   * @return array
   * @throws \ezcBaseFileNotFoundException
   */
  public function getAllEmailsFromCurrentFolder(): array {
    $preparedEmails = [];
    $rawEmails = $this->connection->_transport->fetchAll();
    $parser = new ezcMailParser();
    $emails = $parser->parseMail($rawEmails);

    foreach ($emails as $email) {
      $preparedEmails[] = [
        'subject' => $email->subject,
        'messageId' => $email->messageId,
      ];
    }

    return $preparedEmails;
  }

  /**
   * @return array
   */
  public function getCountEmailsAndSizeFromCurrentFolder() {
    $countEmails = null;
    $sizeEmails = null;
    $this->connection->_transport->status($countEmails, $sizeEmails);

    return [
      'countEmails' => $countEmails,
      'sizeEmails' => $sizeEmails,
    ];
  }

  /**
   * @param $folderName
   * @throws \Exception
   */
  public function setCurrentFolder($folderName) {
    if (!in_array($folderName, $this->getAvailableFolders())) {
      throw new \Exception("The folder doesn't exist");
    }
    $this->connection->_transport->selectMailbox($folderName);
    $this->selectedFolder = $folderName;
  }

  public function getCurrentFolder() {
    return $this->selectedFolder;
  }

  /**
   * @param $messageId
   * @return int
   */
  protected function findEmailIdByMessageId($messageId) {
    $rawEmails = $this->connection->_transport->searchMailbox( 'HEADER Message-ID "' . $messageId . '"' );
    $emailIds = $rawEmails->getMessageNumbers();

    if (empty($emailIds) || !is_array($emailIds)) {
      throw new \Exception("Cannot found any email by message id:" . $messageId);
    }

    if (count($emailIds) > 1) {
      $message = 'Found more than one email with the same message id.';
      $message .= 'Message id:' . $messageId;
      $message .= 'Found ' . count($emailIds) . ' emails.';
      throw new \Exception($message);
    }

    if (empty($emailIds[0])) {
      throw new \Exception("Cannot retrieve email id by message id:" . $messageId);
    }

    $emailId = $emailIds[0];

    $this->doubleCheckEmailId($messageId, $emailId);

    return $emailId;
  }

  /**
   * Get by email id data and compare if message id the same
   *
   * @param $messageId
   * @param $emailId
   * @return void
   * @throws \ezcBaseFileNotFoundException
   */
  protected function doubleCheckEmailId($messageId, $emailId) {
    $rawEmails = $this->connection->_transport->fetchByMessageNr($emailId);
    $parser = new ezcMailParser();
    $emails = $parser->parseMail($rawEmails);

    foreach ($emails as $email) {
      if ($email->messageId !== $messageId) {
        throw new \Exception("Double check is failed! Message id:" . $messageId);
      }
    }
  }

  protected function __clone() {}
  protected function __wakeup(){}

}
