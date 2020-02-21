<?php

/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 * $Id$
 *
 */


namespace Civi\Api4\Service\Spec\Provider;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;

class MailutilsThreadSpecProvider implements Generic\SpecProviderInterface {

  /**
   * @inheritDoc
   */
  public function modifySpec(RequestSpec $spec) {
    $sourceContactField = new FieldSpec('involved_contact_id', 'MailutilsThread', 'Integer');
    $sourceContactField->setFkEntity('Contact');

    $spec->addFieldSpec($sourceContactField);
  }

  /**
   * @inheritDoc
   */
  public function applies($entity, $action) {
    return $entity === 'MailutilsThread' && $action === 'get';
  }

}
