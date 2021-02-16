<?php
use CRM_Mailutils_ExtensionUtil as E;
use Civi\Mailutils\SubjectNormalizer;

class CRM_Mailutils_BAO_MailutilsMessage extends CRM_Mailutils_DAO_MailutilsMessage {

  /**
   * Create a new MailutilsMessage based on array-data
   *
   * @param array $params key-value pairs
   *
   * @return CRM_Mailutils_DAO_MailutilsMessage|NULL
   * @throws \CRM_Core_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public static function create($params) {
    $className = 'CRM_Mailutils_DAO_MailutilsMessage';
    $entityName = 'MailutilsMessage';
    $hook = empty($params['id']) ? 'create' : 'edit';

    if (empty($params['subject_normalized']) && !empty($params['subject'])) {
      $params['subject_normalized'] = SubjectNormalizer::normalize(
        $params['subject']
      );
    }

    if (empty($params['id']) && empty($params['mailutils_thread_id'])) {
      $params['mailutils_thread_id'] = CRM_Mailutils_BAO_MailutilsThread::resolveThreadByMessage($params);
    }

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }

}
