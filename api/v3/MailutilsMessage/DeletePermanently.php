<?php

/**
 * MailutilsMessage.DeletePermanently API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_mailutils_message_delete_permanently_spec(&$spec) {
  $spec['id'] = array(
    'name'         => 'id',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'MailutilsMessage ID',
  );
  $spec['folder'] = array(
    'name'         => 'folder',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Folder',
    'description'  => 'Folder in which the message should be deleted',
  );
}

/**
 * MailutilsMessage.DeletePermanently API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_mailutils_message_delete_permanently($params) {
  $results = \Civi\Api4\MailutilsMessage::deletePermanently(FALSE)
    ->setFolder($params['folder'])
    ->addWhere('id', '=', $params['id'])
    ->execute();
  return civicrm_api3_create_success($results);
}
