<?php

/**
 * MailutilsMessage.Poll API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_mailutils_message_poll_spec(&$spec) {
  $spec['interval'] = array(
    'name'         => 'interval',
    'api.required' => 0,
    'api.default'  => 30,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'Polling interval in seconds',
    'description'  => 'How often should we poll for new emails?',
  );
  $spec['max_iterations'] = array(
    'name'         => 'max_iterations',
    'api.required' => 0,
    'api.default'  => 0,
    'type'         => CRM_Utils_Type::T_INT,
    'title'        => 'Maximum number of poll iterations',
    'description'  => 'How many times should we poll for new emails? 0 = forever',
  );
}

/**
 * MailutilsMessage.Poll API
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
function civicrm_api3_mailutils_message_poll($params) {
  for ($i = 0; ($i < $params['max_iterations'] || $params['max_iterations'] == 0); $i++) {
    civicrm_api3('Job', 'fetch_activities');
    sleep($params['interval']);
  }
  return civicrm_api3_create_success();
}
