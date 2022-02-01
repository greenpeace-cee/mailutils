<?php
use CRM_Mailutils_ExtensionUtil as E;

class CRM_Mailutils_BAO_MailutilsThread extends CRM_Mailutils_DAO_MailutilsThread {

  /**
   * Find or create a thread for a message
   *
   * @param array $params
   *
   * @return int thread ID
   * @throws \Civi\API\Exception\UnauthorizedException
   * @throws \CRM_Core_Exception
   */
  public static function resolveThreadByMessage(array $params) {
    $subject_normalized = CRM_Utils_Type::escape(
      $params['subject_normalized'],
      'String'
    );
    // the goal is to build a query in the form of:
    // SELECT ... WHERE criteria1 UNION SELECT ... WHERE criteria2 UNION ...
    // each SELECT is an element in $parts. the order in $parts is significant -
    // the topmost match is the most accurate one.
    $parts = [];

    // base query for finding a matching thread
    $baseQueryWithoutSubject = "SELECT
                      DISTINCT thread.id
                  FROM
                      civicrm_mailutils_message message
                  JOIN
                      civicrm_mailutils_message_party party
                  ON
                      party.mailutils_message_id = message.id
                  JOIN
                      civicrm_mailutils_thread thread
                  ON
                      thread.id = message.mailutils_thread_id
                  ";

    // normalized subject must match for (almost) all thread matching strategies
    $baseQuery = "{$baseQueryWithoutSubject}
                  WHERE
                      message.subject_normalized = '{$subject_normalized}'
                  ";
    $parts = [];

    if (!empty($params['in_reply_to'])) {
      // In-Reply-To:
      // If In-Reply-To is set, look for a message with a matching Message-Id
      // in this scenario, the sender does not need to be part of the thread.
      $in_reply_to = CRM_Utils_Type::escape(
        $params['in_reply_to'],
        'String'
      );
      $parts[] = $baseQuery . "AND message.message_id = '{$in_reply_to}'";
    }

    if (!empty($params['message_id'])) {
      // Reverse In-Reply-To:
      // Since we can't guarantee the order in which we process emails, let's
      // also look for messages with an In-Reply-To that matches the current
      // Message-Id. As with In-Reply-To, the sender does not need to be part of
      // the thread
      $message_id = CRM_Utils_Type::escape(
        $params['message_id'],
        'String'
      );
      $parts[] = $baseQuery . "AND message.in_reply_to = '{$message_id}'";
    }

    if (!empty($params['from_email'])) {
      // match any messages with the same From address (+ $baseQuery criteria)
      $from_party_type = CRM_Utils_Type::escape(
        CRM_Core_PseudoConstant::getKey(
          'CRM_Mailutils_BAO_MailutilsMessageParty',
          'party_type_id',
          'from'
        ),
        'Integer'
      );
      $from_email = CRM_Utils_Type::escape(
        $params['from_email'],
        'String'
      );
      $parts[] = $baseQuery . "AND party.party_type_id = {$from_party_type} AND party.email = '{$from_email}'";
    }

    $headers = json_decode($params['headers'], TRUE) ?? [];
    if (!empty($params['in_reply_to']) && !empty($headers['Auto-Submitted']) && is_array($headers['Auto-Submitted']) && in_array('auto-replied', $headers['Auto-Submitted'])) {
      $in_reply_to = CRM_Utils_Type::escape(
        $params['in_reply_to'],
        'String'
      );
      $parts[] = $baseQueryWithoutSubject . "AND message.message_id = '{$in_reply_to}'";
    }

    // TODO: match based on References header

    $thread_id = NULL;
    if (count($parts) > 0) {
      // UNION tends to be faster than many ORs, plus we get the right order
      $query = implode("\nUNION\n", $parts);
      $result = CRM_Core_DAO::executeQuery($query);
      $thread_id = $result->fetchValue();
    }

    if (empty($thread_id)) {
      // no matching threads, create a new one
      $thread_id = \Civi\Api4\MailutilsThread::create(FALSE)
        ->execute()
        ->first()['id'];
    }
    return $thread_id;
  }

}
