<?php
use CRM_Mailutils_ExtensionUtil as E;

class CRM_Mailutils_BAO_MailutilsSetting extends CRM_Mailutils_DAO_MailutilsSetting {

  /**
   * Create a new MailutilsSetting based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Mailutils_DAO_MailutilsSetting|NULL
   *
  public static function create($params) {
    $className = 'CRM_Mailutils_DAO_MailutilsSetting';
    $entityName = 'MailutilsSetting';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
