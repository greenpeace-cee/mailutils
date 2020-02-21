<?php
use CRM_Mailutils_ExtensionUtil as E;

class CRM_Mailutils_Page_ContactThreadList extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('Emails'));
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    $threads = \Civi\Api4\MailutilsThread::get()
      ->setSelect([
        'mailutils_messages.subject',
        'mailutils_messages.headers',
        'mailutils_messages.body',
      ])
      ->addWhere(
        'involved_contact_id',
        '=',
        CRM_Utils_Request::retrieve('cid', 'Integer')
      )
      ->setLimit(25)
      ->execute();
    $this->assign('threads', $threads);
    parent::run();
  }

}
