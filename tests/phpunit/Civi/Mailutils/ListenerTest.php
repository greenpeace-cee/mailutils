<?php

namespace Civi\Mailutils;

use Civi\Api4\Email;
use Civi\Api4\LocationType;

/**
 * Test processing and (meta)data storage of inbound email messages
 *
 * @group headless
 */
class ListenerTest extends BaseTest {

  /**
   * Test that email location type is set according to the
   * mailutils_default_location_type_id setting
   */
  public function testEmailLocationType() {
    // ensure location_type_id 1 is the default
    LocationType::update()
      ->addWhere('id', '=', 1)
      ->addValue('is_default', 1)
      ->execute();

    $this->assertEquals(
      0,
      $this->countEmail('my@example.com'),
      'email should not exist before processing messages'
    );

    $this->processEmailFiles(['sample.txt']);
    $this->assertEquals(
      1,
      $this->countEmail('my@example.com', 1),
      'new email should be created with default location_type_id when mailutils_default_location_type_id is not set'
    );

    Email::delete()
      ->addWhere('email', '=', 'my@example.com')
      ->execute();

    // set a default mailutils location type
    \Civi::settings()->set('mailutils_default_location_type_id', '2');
    $this->processEmailFiles(['sample.txt']);
    $this->assertEquals(
      1,
      $this->countEmail('my@example.com', 2),
      'new email should be created with mailutils_default_location_type_id'
    );

    // change location type to "Main"
    Email::update()
      ->addWhere('email', '=', 'my@example.com')
      ->addValue('location_type_id', 1)
      ->execute();

    $this->processEmailFiles(['sample.txt']);
    $this->assertEquals(
      1,
      $this->countEmail('my@example.com', 1),
      'existing email should keep its location_type_id even if mailutils_default_location_type_id is set'
    );
  }

  private function countEmail($email, $locationTypeId = NULL) {
    $result = Email::get()
      ->selectRowCount()
      ->addWhere('email', '=', $email);
    if (!is_null($locationTypeId)) {
      $result->addWhere('location_type_id', '=', $locationTypeId);
    }
    return $result->execute()->count();
  }

}
