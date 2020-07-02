<?php
use CRM_Mailutils_ExtensionUtil as E;
use Civi\Angular\AngularLoader;

class CRM_Mailutils_Page_ContactThreadTab extends CRM_Core_Page {
  public function run() {
    CRM_Core_Resources::singleton()->addScriptFile('mailutils', 'packages/moment.min.js');
    $loader = new AngularLoader();
    $loader->setPageName('civicrm/a');
    $loader->setModules(['mailutils']);
    $loader->load();
    parent::run();
  }
}
