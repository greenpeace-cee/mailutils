<?php
return [
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_template_category',
    'entity'  => 'OptionGroup',
    'cleanup' => 'never',
    'params'  => [
      'version'   => 3,
      'name'      => 'mailutils_template_category',
      'title'     => 'Mailutils Template Category',
      'data_type' => 'Integer',
      'is_active' => 1,
    ]
  ],
  [
    'module'  => 'mailutils',
    'name'    => 'mailutils_template_category_headers_footers',
    'entity'  => 'OptionValue',
    'cleanup' => 'never',
    'params'  => [
      'version'         => 3,
      'option_group_id' => 'mailutils_template_category',
      'value'           => 1,
      'name'            => 'headers_and_footers',
      'label'           => 'Headers/Footers',
      'is_active'       => 1,
    ]
  ],
];
