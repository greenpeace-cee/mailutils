<?php

namespace Civi\Mailutils;

use Civi\Core\Event\GenericHookEvent;
use Civi\Core\Event\PreEvent;
use Civi\Mailutils\Wrapper\ActivityWrapper;

class Listener {

  /**
   * Array of emails for which the emailProcessorContact hook was called without
   * a contact_id, i.e. new emails.
   *
   * Used for location_type_id post-processing.
   *
   * @var array
   */
  static $newEmails = [];

  /**
   * Process certain pre events
   *
   * @param \Civi\Core\Event\PreEvent $event
   */
  public static function pre(PreEvent $event) {
    if ($event->action == 'create' && $event->entity == 'Activity') {

    }
    else if ($event->action == 'create' && $event->entity == 'Email') {
      if (empty($event->params['email'])) {
        return;
      }
      if (in_array($event->params['email'], self::$newEmails)) {
        // this is a new email. should we update location_type_id?
        if (!empty(\Civi::settings()->get('mailutils_default_location_type_id'))) {
          // default location type for new emails is set, overwrite it
          $event->params['location_type_id'] = \Civi::settings()->get('mailutils_default_location_type_id');
        }
        $key = array_search($event->params['email'], self::$newEmails);
        unset(self::$newEmails[$key]);
      }
    }
  }

  /**
   * Process email events
   *
   * @param \Civi\Core\Event\GenericHookEvent $event
   *
   * @throws \Exception
   */
  public static function emailProcessor(GenericHookEvent $event) {
    if ($event->type == 'activity') {
      if (!is_array($event->result) || empty($event->result['id'])) {
        Civi::log()->warning('Skipping inbound email without activity');
        return;
      }
      $processor = new Processor($event->mail, $event->result['id'], $event->mailSettings->id);
      $processor->process();
    }
  }

  /**
   * Process contact match from emailProcessor
   *
   * @param \Civi\Core\Event\GenericHookEvent $event
   */
  public static function emailProcessorContact(GenericHookEvent $event) {
    // TODO: implement custom contact matching logic?
    if (is_null($event->contactID)) {
      // this is a new, unmatched email. store for post-processing in _pre hook.
      self::$newEmails[] = $event->email;
    }
  }

  /**
   * Process apiWrappers
   *
   * @param \Civi\Core\Event\GenericHookEvent $event
   */
  public static function apiWrappers(GenericHookEvent $event) {
    $params = $event->getHookValues();
    if (!empty($params['1']) && !empty($params['1']['entity']) && !empty($params['1']['action']) && !empty($params['1']['wrappers']) ) {
      if ($params['1']['entity'] === 'Activity' && $params['1']['action'] === 'create') {
        $params['1']['wrappers'][] = new ActivityWrapper();
      }
    }
  }

}
