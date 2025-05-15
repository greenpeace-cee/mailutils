<?php
namespace Civi\Api4;

/**
 * MailutilsEmailActivity entity.
 *
 * Provided by the Mail Utilities extension.
 *
 * @package Civi\Api4
 */
class MailutilsEmailActivity extends Generic\AbstractEntity {

  public static function getFields($check_permissions = TRUE) {
    return (new Generic\BasicGetFieldsAction(static::getEntityName(), __FUNCTION__, function($action) {
      return [
        [
          'name' => 'id',
          'data_type' => 'Integer',
          'description' => 'Unique identifier of the activity',
        ],
        [
          'name' => 'from',
          'data_type' => 'String',
          'description' => 'Name/address of the email sender',
        ],
        [
          'name' => 'to',
          'data_type' => 'String',
          'description' => 'Name/address of the email recipient',
        ],
        [
          'name' => 'date',
          'data_type' => 'String',
          'description' => 'Date of the email',
        ],
        [
          'name' => 'cc',
          'data_type' => 'String',
          'description' => 'CC (Carbon Copy) of the email',
        ],
        [
          'name' => 'bcc',
          'data_type' => 'String',
          'description' => 'BCC (Blind Carbon Copy) of the email',
        ],
        [
          'name' => 'subject',
          'data_type' => 'String',
          'description' => 'Subject of the email',
        ],
        [
          'name' => 'body',
          'data_type' => 'String',
          'description' => 'Email message body',
        ],
      ];
    }))->setCheckPermissions($check_permissions);
  }

  public static function get($check_permissions = TRUE) {
    return (new Generic\BasicGetAction(__CLASS__, __FUNCTION__, function($action) use ($check_permissions) {
      $activities = (array) Activity::get($check_permissions)
        ->addSelect('id', 'mailutils_message.headers', 'mailutils_message.body')
        ->addJoin(
          'MailutilsMessage AS mailutils_message',
          'LEFT',
          ['mailutils_message.activity_id', '=', 'id']
        )
        ->addWhere('activity_type_id:name', '=', 'Inbound Email')
        ->execute();

      foreach ($activities as &$activity) {
        $email_headers = json_decode($activity['mailutils_message.headers'], TRUE);
        $email_body = json_decode($activity['mailutils_message.body'], TRUE);

        $activity['from'] = $email_headers['From'];
        $activity['to'] = $email_headers['To'];
        $activity['date'] = $email_headers['Date'];
        $activity['cc'] = $email_headers['Cc'] ?? "";
        $activity['bcc'] = $email_headers['Bcc'] ?? "";
        $activity['subject'] = $email_headers['Subject'];
        $activity['body'] = $email_body[0]['text'];
      }

      return $activities;
    }))->setCheckPermissions($check_permissions);
  }

  public static function permissions() {
    return [
      'meta' => ['administer CiviCRM'],
      'default' => ['administer CiviCRM'],
      'get' => ['access CiviCRM'],
    ];
  }
}
