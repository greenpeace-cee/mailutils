<?php

require_once 'mailutils.civix.php';
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
    'hook_civicrm_greenpeaceEmailProcessor',
    'Civi\Mailutils\Listener::emailProcessor',
    PHP_INT_MAX - 1
  );
  Civi::dispatcher()->addListener(
    'hook_civicrm_emailProcessorContact',
    'Civi\Mailutils\Listener::emailProcessorContact',
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
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function mailutils_civicrm_postInstall() {
  _mailutils_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function mailutils_civicrm_uninstall() {
  _mailutils_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function mailutils_civicrm_enable() {
  _mailutils_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function mailutils_civicrm_disable() {
  _mailutils_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function mailutils_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mailutils_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function mailutils_civicrm_entityTypes(&$entityTypes) {
  _mailutils_civix_civicrm_entityTypes($entityTypes);
}

function mailutils_civicrm_permission(&$permissions) {
  $permissions += [
    'access mailutils' => [
      E::ts('Access Mail Utilities'),
      E::ts('Grants the necessary permissions to access the CiviCRM mailutils extension'),
    ],
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
