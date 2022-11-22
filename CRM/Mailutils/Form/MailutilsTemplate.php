<?php

use Civi\Api4\MailutilsTemplate;
use CRM_Mailutils_ExtensionUtil as E;

class CRM_Mailutils_Form_MailutilsTemplate extends CRM_Core_Form {

  protected $_id = NULL;
  private $mailutilsTemplate = NULL;

  public function buildQuickForm() {
    $templateCategories = MailutilsTemplate::getFields(FALSE)
      ->setLoadOptions(TRUE)
      ->addSelect('options')
      ->addWhere('name', '=', 'template_category_id')
      ->execute()
      ->first()['options'];
    $supportCaseCategories = MailutilsTemplate::getFields(FALSE)
      ->setLoadOptions(TRUE)
      ->addSelect('options')
      ->addWhere('name', '=', 'support_case_category_id')
      ->execute()
      ->first()['options'];

    $this->add('hidden', 'id', $this->_id);
    $this->add('text', 'name', 'Name', ['class' => 'huge'], TRUE);
    $this->add('select', 'template_category_id', 'Template Category', $templateCategories, TRUE);
    $this->add('select', 'support_case_category_id', 'Support Case Category', ['' => 'All'] + $supportCaseCategories);
    $this->add('wysiwyg', 'message', 'Message', ['class' => 'huge'], TRUE);
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
        'class' => 'cancel',
      ]
    ]);

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->assign('startSmartyEscapeWord', CRM_Mailutils_Utils_MessageHandler::START_SMARTY_ESCAPE_WORD);
    $this->assign('endSmartyEscapeWord', CRM_Mailutils_Utils_MessageHandler::END_SMARTY_ESCAPE_WORD);
    parent::buildQuickForm();
  }

  public function cancelAction() {
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/mailutils/template'));
  }

  public function setDefaultValues() {
    $defaultValues = [];

    if (!empty($this->mailutilsTemplate)) {
      $defaultValues['id'] = $this->mailutilsTemplate['id'];
      $defaultValues['name'] = $this->mailutilsTemplate['name'];
      $defaultValues['template_category_id'] = $this->mailutilsTemplate['template_category_id'];
      $defaultValues['support_case_category_id'] = $this->mailutilsTemplate['support_case_category_id'];
      $defaultValues['message'] = $this->mailutilsTemplate['message'];
    }

    return $defaultValues;
  }

  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive');

    if (!empty($this->_id)) {
      $this->mailutilsTemplate = MailutilsTemplate::get(FALSE)
        ->addWhere('id', '=', $this->_id)
        ->execute()
        ->first();
    }
  }

  public function postProcess() {
    $params = $this->exportValues();
    $preparedMessage = CRM_Mailutils_Utils_MessageHandler::prepareToSaveMessage($params['message']);

    if (!empty($params['id'])) {
      $this->_id = $params['id'];
      MailutilsTemplate::update(FALSE)
        ->addWhere('id', '=', $this->_id)
        ->addValue('name', $params['name'])
        ->addValue('template_category_id', $params['template_category_id'])
        ->addValue('support_case_category_id', $params['support_case_category_id'] ?? 'NULL')
        ->addValue('message', $preparedMessage)
        ->execute();
    }
    else {
      $mailutilsTemplate = MailutilsTemplate::create(FALSE)
        ->addValue('name', $params['name'])
        ->addValue('template_category_id', $params['template_category_id'])
        ->addValue('support_case_category_id', $params['support_case_category_id'] ?? 'NULL')
        ->addValue('message', $preparedMessage)
        ->execute()
        ->first();
      $this->_id = $mailutilsTemplate['id'];
    }
    parent::postProcess();
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/mailutils/template'));
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    $elementNames[] = 'id';
    return $elementNames;
  }

}
