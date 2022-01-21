<?php

use Civi\Api4\MailutilsTemplate;
use CRM_Mailutils_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Mailutils_Form_MailutilsTemplate extends CRM_Core_Form {

  protected $_id = NULL;

  /**
   * @var array|mixed
   */
  private $_values;

  public function buildQuickForm() {
    $this->add('hidden', 'id', $this->_id);
    $this->add(
      'text',
      'name',
      'Name',
      [
        'class' => 'huge'
      ],
      TRUE
    );
    $templateCategories = MailutilsTemplate::getFields(FALSE)
      ->setLoadOptions(TRUE)
      ->addSelect('options')
      ->addWhere('name', '=', 'template_category_id')
      ->execute()
      ->first()['options'];
    $this->add(
      'select',
      'template_category_id',
      'Template Category',
      $templateCategories,
      TRUE
    );

    $supportCaseCategories = MailutilsTemplate::getFields(FALSE)
      ->setLoadOptions(TRUE)
      ->addSelect('options')
      ->addWhere('name', '=', 'support_case_category_id')
      ->execute()
      ->first()['options'];
    $this->add(
      'select',
      'support_case_category_id',
      'Support Case Category',
      ['' => 'All'] + $supportCaseCategories
    );

    $this->add(
      'textarea',
      'message',
      'Message',
      [
        'class' => 'huge'
      ],
      TRUE
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setDefaultValues() {
    return $this->_values;
  }

  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Positive');
    $this->_values = $this->get('values');
    if (!is_array($this->_values)) {
      $this->_values = [];
      if (!empty($this->_id)) {
        $mailutilsTemplate = MailutilsTemplate::get(FALSE)
          ->addWhere('id', '=', $this->_id)
          ->execute()
          ->first();
        $this->_values = [
          'id' => $mailutilsTemplate['id'],
          'name' => $mailutilsTemplate['name'],
          'template_category_id' => $mailutilsTemplate['template_category_id'],
          'support_case_category_id' => $mailutilsTemplate['support_case_category_id'],
          'message' => $mailutilsTemplate['message'],
        ];
      }
      $this->set('values', $this->_values);
    }

  }

  public function postProcess() {
    $params = $this->exportValues();
    if (!empty($params['id'])) {
      $this->_id = $params['id'];
      MailutilsTemplate::update(FALSE)
        ->addWhere('id', '=', $this->_id)
        ->addValue('name', $params['name'])
        ->addValue('template_category_id', $params['template_category_id'])
        ->addValue('support_case_category_id', $params['support_case_category_id'] ?? 'NULL')
        ->addValue('message', $params['message'])
        ->execute();
    }
    else {
      $mailutilsTemplate = MailutilsTemplate::create(FALSE)
        ->addValue('name', $params['name'])
        ->addValue('template_category_id', $params['template_category_id'])
        ->addValue('support_case_category_id', $params['support_case_category_id'] ?? 'NULL')
        ->addValue('message', $params['message'])
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
