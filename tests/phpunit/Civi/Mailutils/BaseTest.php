<?php

namespace Civi\Mailutils;

use Civi\Test;
use Civi\Test\Api3TestTrait;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use PHPUnit\Framework\TestCase;

/**
 * Base test for inbound email processing
 *
 * @group headless
 */
abstract class BaseTest extends TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  use Api3TestTrait;

  const FIXTURE_SUBJECT = 'Re: Sample message';

  const FIXTURE_PATH = __DIR__ . '/../../../fixtures';

  public function setUpHeadless() {
    return Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function setUp(): void {
    parent::setUp();
    $domain = $this->callAPISuccess('Domain', 'getvalue', [
      'return' => 'id',
      'options' => ['limit' => 1],
    ]);
    \CRM_Utils_File::cleanDir(self::FIXTURE_PATH . '/maildir');
    mkdir(self::FIXTURE_PATH . '/maildir');
    $this->callAPISuccess('MailSettings', 'create', [
      'name' => 'local',
      'protocol' => 'Localdir',
      'source' => self::FIXTURE_PATH . '/maildir',
      'domain_id' => $domain,
      'activity_status' => 'Completed',
    ]);
  }

  public function tearDown(): void {
    \CRM_Utils_File::cleanDir(self::FIXTURE_PATH . '/maildir');
    parent::tearDown();
  }

  protected function processEmailFiles(array $files) {
    foreach ($files as $file) {
      copy(self::FIXTURE_PATH . '/maildir_template/' . $file, self::FIXTURE_PATH . '/maildir/' . $file);
    }
    return $this->callAPISuccess('job', 'fetch_activities');
  }

}
