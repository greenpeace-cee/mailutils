<?php
use CRM_Mailutils_ExtensionUtil as E;

class CRM_Mailutils_BAO_MailutilsTemplate extends CRM_Mailutils_DAO_MailutilsTemplate {

  /**
   * Create a new MailutilsTemplate based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Mailutils_DAO_MailutilsTemplate|NULL
   *
  public static function create($params) {
    $className = 'CRM_Mailutils_DAO_MailutilsTemplate';
    $entityName = 'MailutilsTemplate';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
