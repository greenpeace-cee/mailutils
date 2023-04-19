<?php

namespace Civi\Mailutils\Processor;

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test case subject cutter
 *
 * @group headless
 */
class SubjectNormalizerTest extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  public function setUpHeadless() {
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  public function tearDown(): void {
    parent::tearDown();
  }

  public function subjectProvider() {
    return [
      ['Foo bar', 'Foo bar'],
      ['Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Enim', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Enim'],
      ['Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Eni💩', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Eni💩'],
      ['Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut 💩 labore et dolore magna aliqua. Enim', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut 💩 labore et dolore magna aliqua. E…'],
      ['Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Enim ad', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Eni…'],
      ['', '(no subject)'],
      ['Lorem <> ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Enim', 'Lorem <> ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna al…'],
    ];
  }

  /**
   * Test that the case subject generator cuts subjects correctly
   *
   * @dataProvider subjectProvider
   *
   * @param $raw
   * @param $cut
   */
  public function testCaseSubject($raw, $cut) {
    $this->assertEquals($cut, SupportCase::getCaseSubject($raw));
  }
}
