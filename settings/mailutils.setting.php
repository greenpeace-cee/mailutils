<?php

return [
  'mailutils_default_location_type_id' => [
    'name'        => 'mailutils_default_location_type_id',
    'type'        => 'Integer',
    'html_type'   => 'text',
    'default'     => NULL,
    'add'         => '1.0',
    'title'       => ts('Default location type for new emails added by mailutils'),
    'is_domain'   => 1,
    'is_contact'  => 0,
    'description' => ts('This determines the location type used for new email addresses added by mailutils.'),
  ],
  'mailutils_case_assignment_email_blacklist' => [
    'name'        => 'mailutils_case_assignment_email_blacklist',
    'type'        => 'String',
    'serialize'   => CRM_Core_DAO::SERIALIZE_JSON,
    'html_type'   => 'select',
    'default'     => '[]',
    'add'         => '1.0',
    'title'       => ts('Case assignment email address blacklist'),
    'is_domain'   => 1,
    'is_contact'  => 0,
    'description' => ts('A list of email addresses (or address patterns) that will be skipped when emails are automatically assigned to cases'),
  ],
];
