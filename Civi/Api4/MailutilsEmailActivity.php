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
          'data_type' => 'Array',
          'description' => 'CC (Carbon Copy) of the email',
        ],
        [
          'name' => 'bcc',
          'data_type' => 'Array',
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
      $query = Activity::get($check_permissions)
        ->addSelect(
          'activity_date_time',
          'message.subject',
          'message.body',
          'from.name',
          'from.email',
          'to.name',
          'to.email',
          'GROUP_CONCAT(cc.email) AS cc_emails',
          'GROUP_CONCAT(cc.name) AS cc_names',
          'GROUP_CONCAT(bcc.email) AS bcc_emails',
          'GROUP_CONCAT(bcc.name) AS bcc_names'
        )
        ->addJoin(
          'MailutilsMessage AS message',
          'INNER',
          ['message.activity_id', '=', 'id']
        )
        ->addJoin('MailutilsMessageParty AS from',
          'LEFT',
          ['from.mailutils_message_id', '=', 'message.id'],
          ['from.party_type_id:name', '=', "'from'"]
        )
        ->addJoin('MailutilsMessageParty AS to',
          'LEFT',
          ['to.mailutils_message_id', '=', 'message.id'],
          ['to.party_type_id:name', '=', "'to'"]
        )
        ->addJoin('MailutilsMessageParty AS cc',
          'LEFT',
          ['cc.mailutils_message_id', '=', 'message.id'],
          ['cc.party_type_id:name', '=', "'cc'"]
        )
        ->addJoin('MailutilsMessageParty AS bcc',
          'LEFT',
          ['bcc.mailutils_message_id', '=', 'message.id'],
          ['bcc.party_type_id:name', '=', "'bcc'"]
        )
        ->addGroupBy('id');

      $action_params = $action->getParams();

      if (isset($action_params['where'])) {
        foreach ($action_params['where'] as $filter) {
          if ($filter[0] === 'id') {
            $query->addWhere('id', $filter[1], $filter[2]);
            break;
          }
        }
      }

      if (isset($action_params['limit'])) {
        $query->setLimit($action_params['limit']);
      }

      $activities = (array) $query->execute();

      foreach ($activities as &$activity) {
        $from_name = $activity['from.name'];
        $from_email = $activity['from.email'];
        $activity['from'] = "$from_name <$from_email>";

        $to_name = $activity['to.name'];
        $to_email = $activity['to.email'];
        $activity['to'] = "$to_name <$to_email>";

        $activity['cc'] = [];

        if (!empty($activity['cc_emails'])) {
          foreach ($activity['cc_emails'] as $i => $cc_email) {
            $cc_name = $activity['cc_names'][$i];
            $activity['cc'][] = "$cc_name <$cc_email>";
          }
        }

        $activity['bcc'] = [];

        if (!empty($activity['bcc_emails'])) {
          foreach ($activity['bcc_emails'] as $i => $bcc_email) {
            $bcc_name = $activity['bcc_names'][$i];
            $activity['bcc'][] = "$bcc_name <$bcc_email>";
          }
        }

        $activity['subject'] = $activity['message.subject'];
        $activity['date'] = $activity['activity_date_time'];
        $activity['body'] = json_decode($activity['message.body'], TRUE)[0]['text'];
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
