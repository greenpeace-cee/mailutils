<?php

namespace Civi\Mailutils;

use Civi\Api4\MailutilsMessage;

/**
 * Test processing and (meta)data storage of inbound email messages
 *
 * @group headless
 */
class ProcessorTest extends BaseTest {

  /**
   * Test that inbound messages are processed and stored
   */
  public function testInboundMessage() {
    copy(self::FIXTURE_PATH . '/maildir_template/sample.txt', self::FIXTURE_PATH . '/maildir/sample.txt');
    $this->callAPISuccess('job', 'fetch_activities');
    $activity = $this->callAPISuccess('Activity', 'get', [
      'subject' => self::FIXTURE_SUBJECT,
    ]);
    $this->assertEquals(
      1,
      $activity['count'],
      'Should create activity for inbound email'
    );
    $activity = reset($activity['values']);
    $this->assertContains(
      'Message body',
      $activity['details'],
      'Activity should have details with message body'
    );

    $mailutilsMessages = MailutilsMessage::get()
      ->addWhere('activity_id', '=', $activity['id'])
      ->execute();
    $this->assertEquals(1, $mailutilsMessages->count());
    $message = $mailutilsMessages->first();
    // TODO: in_reply_to
    $this->assertEquals(self::FIXTURE_SUBJECT, $message['subject']);
    $this->assertEquals('Sample message', $message['subject_normalized']);
    $this->assertEquals('abc.def.fhi@example.com', $message['message_id']);
    $this->assertEquals('{"Delivered-To":["my@example.com"],"Received":["by 10.2.13.84 with SMTP id 1234567890;        Wed, 19 Dec 2018 10:01:11 +0100 (CET)"],"Return-Path":"<>","From":"my@example.com","To":"b.2.1.aaaaaaaaaaaaaaaa@example.com","Subject":"=?UTF-8?Q?Re:_Sample_message?=","Message-ID":"<abc.def.fhi@example.com>","Date":"Wed, 19 Dec 2018 10:01:07 +0100","MIME-Version":["1.0"],"Content-Type":"text\/plain; charset=utf-8; format=flowed","Content-Language":["en-US"],"Content-Transfer-Encoding":"8bit"}', $message['headers']);
    $this->assertEquals('[{"headers":{"Content-Type":"text\/plain; charset=utf-8; format=flowed","Content-Transfer-Encoding":"8bit"},"text":"Message body"}]', $message['body']);
  }

  /**
   * Test that inbound messages are threaded correctly
   */
  public function testThread() {
    copy(self::FIXTURE_PATH . '/maildir_template/sample.txt', self::FIXTURE_PATH . '/maildir/sample.txt');
    $this->callAPISuccess('job', 'fetch_activities');
    $threads = \Civi\Api4\MailutilsThread::get()
      ->addWhere('mailutils_messages.subject', '=', self::FIXTURE_SUBJECT)
      ->execute();
    $this->assertEquals(1, $threads->count());
    $thread = $threads->first();
    // TODO: fetch response and test that it's added to the same thread
  }

  /**
   * Test that involved parties of inbound messages are stored
   */
  public function testMessageParties() {
    copy(self::FIXTURE_PATH . '/maildir_template/sample.txt', self::FIXTURE_PATH . '/maildir/sample.txt');
    $this->callAPISuccess('job', 'fetch_activities');
    $parties = \Civi\Api4\MailutilsMessageParty::get()
      ->addWhere('mailutils_message.subject', '=', self::FIXTURE_SUBJECT)
      ->setLimit(25)
      ->execute();
    $this->assertEquals(2, $parties->count(), 'Should have two involved parties');
    // test that From is extracted
    $from = \Civi\Api4\MailutilsMessageParty::get()
      ->addWhere('mailutils_message.subject', '=', self::FIXTURE_SUBJECT)
      ->addWhere('party_type_id:name', '=', 'from')
      ->execute()
      ->first();
    $this->assertEquals('my@example.com', $from['email']);
    // Test that To is extracted
    $to = \Civi\Api4\MailutilsMessageParty::get()
      ->addWhere('mailutils_message.subject', '=', self::FIXTURE_SUBJECT)
      ->addWhere('party_type_id:name', '=', 'to')
      ->execute()
      ->first();
    $this->assertEquals('b.2.1.aaaaaaaaaaaaaaaa@example.com', $to['email']);
    // TODO: test contact_id
    // TODO: test CC, BCC
  }

}
