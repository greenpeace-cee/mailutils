<?php

/**
 * MailutilsMessage.CopyMessage API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_mailutils_message_copy_message_spec(&$spec) {
  $spec['id'] = array(
    'name'         => 'id',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'MailutilsMessage ID',
  );
  $spec['source_folder'] = array(
    'name'         => 'source_folder',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Source Folder',
    'description'  => 'Folder in which the message is currently located',
  );
  $spec['destination_folder'] = array(
    'name'         => 'destination_folder',
    'api.required' => 1,
    'type'         => CRM_Utils_Type::T_STRING,
    'title'        => 'Destination Folder',
    'description'  => 'Folder to which the message should be copied',
  );
}

/**
 * MailutilsMessage.CopyMessage API
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
function civicrm_api3_mailutils_message_copy_message($params) {
  $results = \Civi\Api4\MailutilsMessage::copyMessage(FALSE)
    ->setSourceFolder($params['source_folder'])
    ->setDestinationFolder($params['destination_folder'])
    ->addWhere('id', '=', $params['id'])
    ->execute();
  return civicrm_api3_create_success($results);
}
