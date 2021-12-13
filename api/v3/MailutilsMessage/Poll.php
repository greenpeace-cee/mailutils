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
    'type'         => CRM_Utils_Type::T_BOOLEAN,
    'title'        => 'Polling interval in seconds',
    'description'  => 'How often should we poll for new emails?',
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
  while (true) {
    civicrm_api3('Job', 'fetch_activities');
    sleep($params['interval']);
  }
}
