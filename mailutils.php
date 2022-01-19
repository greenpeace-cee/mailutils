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
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function mailutils_civicrm_xmlMenu(&$files) {
  _mailutils_civix_civicrm_xmlMenu($files);
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
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function mailutils_civicrm_managed(&$entities) {
  _mailutils_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function mailutils_civicrm_caseTypes(&$caseTypes) {
  _mailutils_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function mailutils_civicrm_angularModules(&$angularModules) {
  _mailutils_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function mailutils_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mailutils_civix_civicrm_alterSettingsFolders($metaDataFolders);
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

/**
 * Implements hook_civicrm_thems().
 */
function mailutils_civicrm_themes(&$themes) {
  _mailutils_civix_civicrm_themes($themes);
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
