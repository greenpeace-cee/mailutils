<?php
return [
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_party_type',
    'entity'  => 'OptionGroup',
    'cleanup' => 'never',
    'params'  => [
      'version'   => 3,
      'name'      => 'mailutils_party_type',
      'title'     => 'Mailutils Party Type',
      'data_type' => 'Integer',
      'is_active' => 1,
    ]
  ],
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_party_type_from',
    'entity'  => 'OptionValue',
    'cleanup' => 'never',
    'params'  => [
      'version'         => 3,
      'option_group_id' => 'mailutils_party_type',
      'value'           => 1,
      'name'            => 'from',
      'label'           => 'From',
      'is_active'       => 1,
    ]
  ],
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_party_type_to',
    'entity'  => 'OptionValue',
    'cleanup' => 'never',
    'params'  => [
      'version'         => 3,
      'option_group_id' => 'mailutils_party_type',
      'value'           => 2,
      'name'            => 'to',
      'label'           => 'To',
      'is_active'       => 1,
    ]
  ],
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_party_type_cc',
    'entity'  => 'OptionValue',
    'cleanup' => 'never',
    'params'  => [
      'version'         => 3,
      'option_group_id' => 'mailutils_party_type',
      'value'           => 3,
      'name'            => 'cc',
      'label'           => 'CC',
      'is_active'       => 1,
    ]
  ],
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_party_type_bcc',
    'entity'  => 'OptionValue',
    'cleanup' => 'never',
    'params'  => [
      'version'         => 3,
      'option_group_id' => 'mailutils_party_type',
      'value'           => 4,
      'name'            => 'bcc',
      'label'           => 'BCC',
      'is_active'       => 1,
    ]
  ],
];
