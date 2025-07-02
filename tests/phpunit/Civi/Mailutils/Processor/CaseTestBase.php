<?php

namespace Civi\Mailutils\Processor;

use Civi\Api4;
use Civi\Test;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;

/**
 * Base class for email to case assignment
 *
 * @group headless
 */
class CaseTestBase extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  protected $defaultSiteEmailAddressId;
  protected $domainId;

  private static $_fixturePath;

  public function setUpHeadless() {
    return Test::headless()
      ->installMe(__DIR__)
      ->apply(TRUE);
  }

  public function setUp(): void {
    parent::setUp();

    Api4\Setting::set(FALSE)
      ->addValue('enable_components', [
        "CiviCampaign",
        "CiviCase",
        "CiviContribute",
        "CiviEvent",
        "CiviMail",
        "CiviMember",
        "CiviPledge",
        "CiviReport"
      ])
      ->execute();

    $this->domainId = Api4\Domain::get(FALSE)
      ->addSelect('id')
      ->setLimit(1)
      ->execute()
      ->first()['id'];

    $this->defaultSiteEmailAddressId = Api4\SiteEmailAddress::get(FALSE)
      ->addSelect('id')
      ->addWhere('domain_id', '=', $this->domainId)
      ->addWhere('is_default', '=', TRUE)
      ->setLimit(1)
      ->execute()
      ->first()['id'];
  }

  protected static function fixturePath($rel_path) {
    if (empty(self::$_fixturePath)) {
      $extension = Api4\Extension::get(FALSE)
        ->addSelect('path')
        ->addWhere('key', '=', 'mailutils')
        ->execute()
        ->first();

      self::$_fixturePath = realpath($extension['path'] . '/tests/fixtures');
    }

    return self::$_fixturePath . '/' . preg_replace('#^/#', '', $rel_path);
  }

  protected static function renderEmail($template_file, $target_file, $substitute) {
    $template = file_get_contents($template_file);
    $rendered = str_replace(array_keys($substitute), array_values($substitute), $template);
    file_put_contents($target_file, $rendered);
  }

}
