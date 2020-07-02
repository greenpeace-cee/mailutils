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
];
