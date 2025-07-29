<?php

namespace Civi\Mailutils\Processor;

use Civi\Api4;
use CRM_Utils_File;

/**
 * Test processing of inbound email messages associated with legacy cases
 *
 * @group headless
 */
class LegacyCaseTest extends CaseTestBase {

  private $legacyContact;

  public function setUp(): void {
    parent::setUp();

    // Ignore email addresses matching *@example.com during processing
    Api4\Setting::set(FALSE)
      ->addValue('mailutils_case_assignment_email_blacklist', json_encode(['*@example.com']))
      ->execute();

    // Create a legacy case type
    Api4\CaseType::create(FALSE)
      ->addValue('name', 'legacy')
      ->addValue('title', 'Legacy')
      ->addValue('definition', [])
      ->execute();

    // Create a legacy contact
    $this->legacyContact = Api4\Contact::create(FALSE)
      ->addValue('first_name', 'Legacy')
      ->addValue('last_name', 'Contact')
      ->execute()
      ->first();

    $legacy_email = Api4\Email::create(FALSE)
      ->addValue('contact_id', $this->legacyContact['id'])
      ->addValue('location_type_id:name', 'Home')
      ->addValue('email', 'legacy.contact@gmail.com')
      ->execute()
      ->first();

    $this->legacyContact['email'] = $legacy_email['email'];

    // Create a dedicated mail account for legacy emails
    $legacy_mail_account = Api4\MailSettings::create(FALSE)
      ->addValue('name', 'Legacy')
      ->addValue('domain_id', $this->domainId)
      ->addValue('protocol:name', 'Localdir')
      ->addValue('source', self::fixturePath('/maildir_legacy'))
      ->addValue('activity_assignees', 'from')
      ->addValue('activity_source', 'from')
      ->addValue('activity_status', 'Completed')
      ->addValue('activity_targets', 'to,cc,bcc')
      ->addValue('activity_type_id:name', 'Inbound Email')
      ->addValue('is_contact_creation_disabled_if_no_match', FALSE)
      ->addValue('is_non_case_email_skipped', FALSE)
      ->execute()
      ->first();

    $legacy_mailutils_settings = Api4\MailutilsSetting::create(FALSE)
      ->addValue('mail_setting_id', $legacy_mail_account['id'])
      ->addValue('from_email_address_id', $this->defaultSiteEmailAddressId)
      ->addValue('mailutils_template_id', NULL)
      ->addValue('smtp_server', NULL)
      ->addValue('smtp_port', NULL)
      ->addValue('smtp_username', NULL)
      ->addValue('smtp_password', NULL)
      ->addValue('support_case_category_id', NULL)
      ->addValue('advanced_config', [ 'case_type' => 'legacy' ])
      ->execute()
      ->first();

    // Reset local email directory
    CRM_Utils_File::cleanDir(self::fixturePath('/maildir_legacy'));
    mkdir(self::fixturePath('/maildir_legacy'));
  }

  public function tearDown(): void {
    CRM_Utils_File::cleanDir(self::fixturePath('/maildir_legacy'));

    parent::tearDown();
  }

  public function testLinkEmailToLegacyCase() {
    $message_id = bin2hex(random_bytes(8));

    // Create a legacy case for a contact
    $case = Api4\CiviCase::create(FALSE)
      ->addValue('case_type_id.name', 'legacy')
      ->addValue('contact_id', [$this->legacyContact['id']])
      ->addValue('status_id:name', 'Open')
      ->execute()
      ->first();

    // Render email template and move it to the source directory of the mail account
    self::renderEmail(
      self::fixturePath('/maildir_template/email_tmpl.txt'),
      self::fixturePath('/maildir_legacy/email.txt'),
      [
        '<message_id>' => "<$message_id>",
        '<recipient>'  => 'legacy@example.com',
        '<cc>'         => $this->legacyContact['email'],
        '<date>'       => date('D, j M Y H:i:s O'),
        '<subject>'    => mb_encode_mimeheader('Test legacy message', 'UTF-8', 'Q'),
      ]
    );

    // Process incoming emails
    civicrm_api3('Job', 'fetch_activities');

    // Assert email activity has been linked to case
    $case_activity = Api4\CaseActivity::get(FALSE)
      ->addSelect('activity_id', 'case_id')
      ->addJoin(
        'MailutilsMessage AS message',
        'INNER',
        ['message.activity_id', '=', 'activity_id']
      )
      ->addWhere('message.message_id', '=', $message_id)
      ->execute()
      ->first();

    $this->assertEquals($case['id'], $case_activity['case_id']);

    // Assert legacy contact is activity target
    $activity = Api4\Activity::get(FALSE)
      ->addSelect('target_contact_id')
      ->addWhere('id', '=', $case_activity['activity_id'])
      ->execute()
      ->first();

    $this->assertEquals([$this->legacyContact['id']], $activity['target_contact_id']);
  }

  public function testGetEmailAddressFromSubject() {
    $message_id = bin2hex(random_bytes(8));

    // Create a legacy case for a contact
    $case = Api4\CiviCase::create(FALSE)
      ->addValue('case_type_id.name', 'legacy')
      ->addValue('contact_id', [$this->legacyContact['id']])
      ->addValue('status_id:name', 'Open')
      ->execute()
      ->first();

    // Render email template and move it to the source directory of the mail account
    $legacy_email = $this->legacyContact['email'];

    self::renderEmail(
      self::fixturePath('/maildir_template/email_tmpl.txt'),
      self::fixturePath('/maildir_legacy/email.txt'),
      [
        '<message_id>' => "<$message_id>",
        '<recipient>'  => 'legacy@example.com',
        '<cc>'         => '',
        '<date>'       => date('D, j M Y H:i:s O'),
        // The subject of this message contains the only occurrence of the contact's email address
        '<subject>'    => mb_encode_mimeheader("Test legacy message about <$legacy_email>", 'UTF-8', 'Q'),
      ]
    );

    // Process incoming emails
    civicrm_api3('Job', 'fetch_activities');

    // Assert email activity has been linked to case
    $case_activity = Api4\CaseActivity::get(FALSE)
      ->addSelect('activity_id', 'case_id')
      ->addJoin(
        'MailutilsMessage AS message',
        'INNER',
        ['message.activity_id', '=', 'activity_id']
      )
      ->addWhere('message.message_id', '=', $message_id)
      ->execute()
      ->first();

    $this->assertEquals($case['id'], $case_activity['case_id']);

    // Assert legacy contact is activity target
    $activity = Api4\Activity::get(FALSE)
      ->addSelect('target_contact_id')
      ->addWhere('id', '=', $case_activity['activity_id'])
      ->execute()
      ->first();

    $this->assertEquals([$this->legacyContact['id']], $activity['target_contact_id']);
  }

}
