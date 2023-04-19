<?php

namespace Civi\Mailutils\Wrapper;

use API_Wrapper;
use CRM_Core_PseudoConstant;
use Civi\Mailutils\Utils\StringHelper;

class ActivityWrapper implements API_Wrapper {

  /**
   * Interface for interpreting api input.
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    if (!empty($apiRequest['params']['subject']) && $this->isActivityHasInboundEmailType($apiRequest)) {
      $apiRequest['params']['subject'] = StringHelper::smartCutString($apiRequest['params']['subject'], 255);
    }

    return $apiRequest;
  }

  /**
   * @param $apiRequest
   * @return bool
   */
  private function isActivityHasInboundEmailType($apiRequest) {
    if (empty($apiRequest['params']['activity_type_id'])) {
      return false;
    }

    $activityType = 'Inbound Email';
    $currentActivityType = (string) $apiRequest['params']['activity_type_id'];

    if ($currentActivityType === $activityType) {
      return true;
    }

    $inboundEmailId = (string) CRM_Core_PseudoConstant::getKey('CRM_Activity_BAO_Activity', 'activity_type_id', $activityType);
    if (empty($inboundEmailId)) {
      return false;
    }

    if ($currentActivityType === $inboundEmailId) {
      return true;
    }

    return false;
  }

  /**
   * Interface for interpreting api output.
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

}
