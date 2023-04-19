<?php

namespace Civi\Mailutils;

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

/**
 * Test subject normalizer
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
      ['Re: Foo bar', 'Foo bar'],
      ['Re: Re: Foo bar', 'Foo bar'],
      ['Re:Re: Foo bar', 'Foo bar'],
      ['Re: Fwd: Foo bar baz', 'Foo bar baz'],
      ['AW: Fwd: Re: Foo bar', 'Foo bar'],
      ['AW: Fwd: Re: Foo bar Re: Baz', 'Foo bar Re: Baz'],
    ];
  }

  /**
   * Test that the subject normalizer is stripping prefixes correctly
   *
   * @dataProvider subjectProvider
   *
   * @param $raw
   * @param $normalized
   */
  public function testSubjectNormalizer($raw, $normalized) {
    $this->assertEquals($normalized, SubjectNormalizer::normalize($raw));
  }
}
