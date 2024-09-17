# mailutils

mailutils provides a framework for email clients in CiviCRM. It is typically
used in combination with other extensions like [supportcase](https://github.com/greenpeace-cee/supportcase).

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.4+
* CiviCRM 5.57+
* [at.greenpeace.casetools](https://github.com/greenpeace-cee/at.greenpeace.casetools) 0.1-beta.1+

## Installation (Web UI)

This extension has not yet been published for installation via the web UI.

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl mailutils@https://github.com/greenpeace-cee/mailutils/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/greenpeace-cee/mailutils.git
cv en mailutils
```

## Core Patch

CiviCRM versions before 5.76 require the core patch located in `GP-9999.patch`
to be applied. Depending on your CMS and path layout, some variation of this
should work:

```bash
cd <site-root>/sites/all/modules/civicrm
patch -p1 < ../../../default/files/civicrm/ext/mailutils/GP-9999.patch
```

## Configuration

Mailutils expands the "Mail Accounts" feature in CiviCRM. To get started, first
set up a **IMAP** mail account with Email-to-Activity processing. Refer
to the [relevant section of the CiviCRM System Administrator Guide](https://docs.civicrm.org/sysadmin/en/latest/setup/civimail/inbound/#autofiling-email-activities-via-emailprocessor)
for details. Make sure to keep "Skip emails which do not have a Case ID or Case hash"
and "Do not create new contacts when filing emails" unchecked.

After configuring the Mail Account in CiviCRM, you need to perform some additional
configuration in `mailutils` so the extension knows how to process and send
emails for this account. There is no UI for this at the moment, but you can
use API4 to create the setting record. Using the command-line, your configuration
may look like this:

```bash
cv api4 MailutilsSetting.create +v mail_setting_id=1 +v support_case_category_id:name=without_category +v from_email_address_id=1 +v smtp_server=example.com +v smtp_port=587 +v smtp_username=user@example.com +v smtp_password=secret
```

* `mail_setting_id`: This is the ID of the mail account you configured in the previous step.
  If you want to configure multiple mail accounts, you'll need to repeat this step with each ID.
* `support_case_category_id:name`: If you want to use this extension with the `supportcase`
  extension, setting this value causes inbound emails to be processed as Support Cases and filed
  under the provided Support Case Category. Custom categories can be created via the corresponding
  Option Group. Support Case Categories can be used in various ways depending on how you organize
  your public email addresses - for example, you may have separate categories (and email addresses)
  for donor support and press requests.
* `from_email_address_id`: This is the sender name and address (or rather the corresponding Option Group value)
  that will be used when sending emails in this inbox. You may need to create the From Email Address via
  Administer > Communications > FROM Email Addresses.
* `smtp_server`, `smtp_username` and `smtp_password`: This SMTP server will be used to send emails.
  TLS support is currently required for all connections (not StartTLS).
* `mailutils_template_id`: ID of the default template that should be used when composing emails
  in this inbox. Templates can be managed via Mailings > Mailutils > Templates.

### Settings

#### `mailutils_default_location_type_id`

To alter the default location type mailutils uses when adding *new* email
addresses, you can change the `mailutils_default_location_type_id` setting.
This is useful if you want to use a dedicated location type for support-only
addresses which you may not want (or be allowed) to use for other purposes.
If no value is set, the global default location type will be used.

## Usage

This extension provides a framework that can be used by other extensions. Users
will mostly interact with extensions like `supportcase` that use this framework.
