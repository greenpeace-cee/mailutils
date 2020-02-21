<?php

namespace Civi\Api4\Action\MailutilsThread;

/**
 * @inheritDoc
 */
class Get extends \Civi\Api4\Generic\DAOGetAction {

  /**
   * @inheritDoc
   */
  protected function getObjects() {
    $where = $this->getWhere();
    $involved_contact_clause = NULL;
    foreach ($where as $key => $clause) {
      if ($clause[0] == 'involved_contact_id') {
        $involved_contact_clause = $clause;
        unset($where[$key]);
      }
    }
    $this->setWhere($where);

    if (!is_null($involved_contact_clause)) {
      $thread_ids = $this->getThreadIdsByContact($involved_contact_clause);
      if (count($thread_ids) == 0) {
        // no threads were found for this contact
        // 'id IN (0)' will ensure we don't return any messages
        $thread_ids[] = 0;
      }
      $this->addWhere('id', 'IN', $thread_ids);
    }
    return parent::getObjects();
  }

  private function getThreadIdsByContact($clause) {
    $threads = [];
    $activities = \Civi\Api4\Activity::get()
      ->setSelect([
        'mailutils_messages.mailutils_thread_id',
      ])
      ->addWhere('activity_contacts.contact_id', $clause[1], $clause[2])
      ->addWhere('mailutils_messages.id', 'IS NOT NULL')
      ->execute();
    foreach ($activities as $activity) {
      $threads = array_merge(
        $threads,
        array_column($activity['mailutils_messages'], 'mailutils_thread_id')
      );
    }
    return array_unique($threads, SORT_REGULAR);
  }

}
