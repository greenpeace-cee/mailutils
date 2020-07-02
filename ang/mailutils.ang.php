<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// \https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules/n
return [
  'js' => [
    'ang/mailutils.js',
    'ang/mailutils/*.js',
    'ang/mailutils/*/*.js',
    'ang/mailutils/*/*/*.js',
  ],
  'css' => [
    'css/*.css',
    'ang/mailutils.css',
  ],
  'partials' => [
   'ang/mailutils',
  ],
  'requires' => ['crmUi', 'crmUtil', 'ngRoute', 'api4'],
  'settings' => [],
];
