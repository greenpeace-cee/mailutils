<?php

require_once 'mailutils.civix.php';
use Civi\Api4;
use CRM_Mailutils_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function mailutils_civicrm_config(&$config) {
  _mailutils_civix_civicrm_config($config);

  if (isset(Civi::$statics[__FUNCTION__])) {
    return;
  }
  Civi::$statics[__FUNCTION__] = 1;

  // register replacement hooks and let them run as early as possible
  Civi::dispatcher()->addListener(
    'hook_civicrm_pre',
    'Civi\Mailutils\Listener::pre',
    PHP_INT_MAX - 1
  );
  Civi::dispatcher()->addListener(
    'hook_civicrm_emailProcessor',
    'Civi\Mailutils\Listener::emailProcessor',
    PHP_INT_MAX - 1
  );
  Civi::dispatcher()->addListener(
    'hook_civicrm_emailProcessorContact',
    'Civi\Mailutils\Listener::emailProcessorContact',
    PHP_INT_MAX - 1
  );
  Civi::dispatcher()->addListener(
    'hook_civicrm_apiWrappers',
    'Civi\Mailutils\Listener::apiWrappers',
    PHP_INT_MAX - 1
  );
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function mailutils_civicrm_install() {
  _mailutils_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function mailutils_civicrm_enable() {
  _mailutils_civix_civicrm_enable();
}

function mailutils_civicrm_permission(&$permissions) {
  $permissions['access mailutils'] = [
    'label' => E::ts('Access Mail Utilities'),
    'description' => E::ts('Grants the necessary permissions to access the CiviCRM mailutils extension'),
  ];
}

function mailutils_civicrm_navigationMenu(&$menu) {
  _mailutils_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('Mailutils'),
    'name' => 'Mailutils',
    'permission' => 'access mailutils',
    'operator' => 'OR',
    'separator' => 2,
  ));
  _mailutils_civix_insert_navigation_menu($menu, 'Mailings/Mailutils', array(
    'label' => E::ts('Templates'),
    'name' => 'Mailutils_Templates',
    'url' => 'civicrm/mailutils/template',
    'permission' => 'access mailutils',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _mailutils_civix_navigationMenu($menu);
}

/**
 * Implements hook_coreResourceList
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_coreResourceList
 */
function mailutils_civicrm_coreResourceList(&$list, $region) {
  CRM_Core_Resources::singleton()->addStyleFile('mailutils', 'css/mailutils.css', 0, 'page-header');
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_buildForm
 */
function mailutils_civicrm_buildForm($form_name, &$form) {
  switch ($form_name) {
    case 'CRM_Activity_Form_ActivityView': {
      $activity_template_data = $form->getTemplateVars('values');
      $activity_id = $activity_template_data['id'];
      $email_display = CRM_Mailutils_Utils_MessageRenderer::render($activity_id);

      if (is_null($email_display)) return;

      $activity_template_data['details'] = $email_display;
      $form->assign('values', $activity_template_data);

      break;
    }

    case 'CRM_Case_Form_ActivityView': {
      $activity_template_data = $form->getTemplateVars('report');
      $activity_id = $form->getTemplateVars('activityID');
      $email_display = CRM_Mailutils_Utils_MessageRenderer::render($activity_id);

      if (is_null($email_display)) return;

      $field_index = array_find_key(
        $activity_template_data['fields'],
        fn ($field) => $field['name'] === 'Details'
      );

      $activity_template_data['fields'][$field_index]['value'] = $email_display;
      $form->assign('report', $activity_template_data);

      break;
    }

    case 'CRM_Fastactivity_Form_View': {
      $activity_template_data = $form->getTemplateVars('activity');
      $activity_id = $activity_template_data['activityId'];
      $email_display = CRM_Mailutils_Utils_MessageRenderer::render($activity_id);

      if (is_null($email_display)) return;

      $activity_template_data['details'] = $email_display;
      $form->assign('activity', $activity_template_data);

      break;
    }
  }
}
