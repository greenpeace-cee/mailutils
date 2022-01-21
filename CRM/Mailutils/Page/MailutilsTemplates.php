<?php

use Civi\Api4\MailutilsTemplate;
use CRM_Mailutils_ExtensionUtil as E;

class CRM_Mailutils_Page_MailutilsTemplates extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    // CRM_Utils_System::setTitle(E::ts('MailutilsTemplates'));

    $mailutilsTemplates = MailutilsTemplate::get(FALSE)
      ->addSelect('*', 'template_category_id:label', 'support_case_category_id:label')
      ->addOrderBy('template_category_id:label', 'ASC')
      ->addOrderBy('support_case_category_id:label', 'ASC')
      ->addOrderBy('name', 'ASC')
      ->execute();
    foreach ($mailutilsTemplates as $id => $mailutilsTemplate) {
      $mailutilsTemplates[$id]['template_category'] = $mailutilsTemplate['template_category_id:label'];
      $mailutilsTemplates[$id]['support_case_category'] = $mailutilsTemplate['support_case_category_id:label'];
    }
    $this->assign('mailutilsTemplates', $mailutilsTemplates);

    parent::run();
  }

}
