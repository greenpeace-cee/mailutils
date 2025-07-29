<?php

namespace Civi\Mailutils\Processor;

use Civi\Api4;
use CRM_Utils_File;

/**
 * Test processing of inbound email messages associated with VIP cases
 *
 * @group headless
 */
class VIPCaseTest extends CaseTestBase {

  private $vipContact;

  public function setUp(): void {
    parent::setUp();

    // Ignore email addresses matching *@example.com during processing
    Api4\Setting::set(FALSE)
      ->addValue('mailutils_case_assignment_email_blacklist', json_encode(['*@example.com']))
      ->execute();

    // Create a VIP case type
    Api4\CaseType::create(FALSE)
      ->addValue('name', 'vip')
      ->addValue('title', 'VIP')
      ->addValue('definition', [])
      ->execute();

    // Create a VIP contact
    $this->vipContact = Api4\Contact::create(FALSE)
      ->addValue('first_name', 'Vip')
      ->addValue('last_name', 'Contact')
      ->execute()
      ->first();

    $vip_email = Api4\Email::create(FALSE)
      ->addValue('contact_id', $this->vipContact['id'])
      ->addValue('location_type_id:name', 'Home')
      ->addValue('email', 'vip.contact@gmail.com')
      ->execute()
      ->first();

    $this->vipContact['email'] = $vip_email['email'];

    // Create a dedicated mail account for VIP emails
    $vip_mail_account = Api4\MailSettings::create(FALSE)
      ->addValue('name', 'VIP')
      ->addValue('domain_id', $this->domainId)
      ->addValue('protocol:name', 'Localdir')
      ->addValue('source', self::fixturePath('/maildir_vip'))
      ->addValue('activity_assignees', 'from')
      ->addValue('activity_source', 'from')
      ->addValue('activity_status', 'Completed')
      ->addValue('activity_targets', 'to,cc,bcc')
      ->addValue('activity_type_id:name', 'Inbound Email')
      ->addValue('is_contact_creation_disabled_if_no_match', FALSE)
      ->addValue('is_non_case_email_skipped', FALSE)
      ->execute()
      ->first();

    $vip_mailutils_settings = Api4\MailutilsSetting::create(FALSE)
      ->addValue('mail_setting_id', $vip_mail_account['id'])
      ->addValue('from_email_address_id', $this->defaultSiteEmailAddressId)
      ->addValue('mailutils_template_id', NULL)
      ->addValue('smtp_server', NULL)
      ->addValue('smtp_port', NULL)
      ->addValue('smtp_username', NULL)
      ->addValue('smtp_password', NULL)
      ->addValue('support_case_category_id', NULL)
      ->addValue('advanced_config', [ 'case_type' => 'vip' ])
      ->execute()
      ->first();

    // Reset local email directory
    CRM_Utils_File::cleanDir(self::fixturePath('/maildir_vip'));
    mkdir(self::fixturePath('/maildir_vip'));
  }

  public function tearDown(): void {
    CRM_Utils_File::cleanDir(self::fixturePath('/maildir_vip'));

    parent::tearDown();
  }

  public function testLinkEmailToVIPCase() {
    $message_id = bin2hex(random_bytes(8));

    // Create a VIP case for a contact
    $case = Api4\CiviCase::create(FALSE)
      ->addValue('case_type_id.name', 'vip')
      ->addValue('contact_id', [$this->vipContact['id']])
      ->addValue('status_id:name', 'Open')
      ->execute()
      ->first();

    // Render email template and move it to the source directory of the mail account
    self::renderEmail(
      self::fixturePath('/maildir_template/email_tmpl.txt'),
      self::fixturePath('/maildir_vip/email.txt'),
      [
        '<message_id>' => "<$message_id>",
        '<recipient>'  => 'vip@example.com',
        '<cc>'         => $this->vipContact['email'],
        '<date>'       => date('D, j M Y H:i:s O'),
        '<subject>'    => mb_encode_mimeheader('Test VIP message', 'UTF-8', 'Q'),
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

    // Assert VIP contact is activity target
    $activity = Api4\Activity::get(FALSE)
      ->addSelect('target_contact_id')
      ->addWhere('id', '=', $case_activity['activity_id'])
      ->execute()
      ->first();

    $this->assertEquals([$this->vipContact['id']], $activity['target_contact_id']);
  }

  public function testGetEmailAddressFromSubject() {
    $message_id = bin2hex(random_bytes(8));

    // Create a VIP case for a contact
    $case = Api4\CiviCase::create(FALSE)
      ->addValue('case_type_id.name', 'vip')
      ->addValue('contact_id', [$this->vipContact['id']])
      ->addValue('status_id:name', 'Open')
      ->execute()
      ->first();

    // Render email template and move it to the source directory of the mail account
    $vip_email = $this->vipContact['email'];

    self::renderEmail(
      self::fixturePath('/maildir_template/email_tmpl.txt'),
      self::fixturePath('/maildir_vip/email.txt'),
      [
        '<message_id>' => "<$message_id>",
        '<recipient>'  => 'vip@example.com',
        '<cc>'         => '',
        '<date>'       => date('D, j M Y H:i:s O'),
        // The subject of this message contains the only occurrence of the contact's email address
        '<subject>'    => mb_encode_mimeheader("Test VIP message about <$vip_email>", 'UTF-8', 'Q'),
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

    // Assert VIP contact is activity target
    $activity = Api4\Activity::get(FALSE)
      ->addSelect('target_contact_id')
      ->addWhere('id', '=', $case_activity['activity_id'])
      ->execute()
      ->first();

    $this->assertEquals([$this->vipContact['id']], $activity['target_contact_id']);
  }

}
