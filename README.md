# mailutils

mailutils adds a number of backend features and APIs required to implement
email client-like functionality in CiviCRM. mailutils itself does not come
with a fully-implemented mail client - it provides a framework that can be
used to implement one.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.39+

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

This extension currently requires the core patch located in `GP-9999.patch`
to be applied. To apply the patch, you can use the following command:

```bash
cd <site-root>/sites/all/modules/civicrm
patch -p1 < <site-root>/sites/default/files/civicrm/ext/mailutils/GP-9999.patch
```

## Configuration

Mailutils expands the "Mail Accounts" feature in CiviCRM. To get started, first
[set up a mail account with Email-to-Activity processing](https://docs.civicrm.org/sysadmin/en/latest/setup/civimail/#email-to-activity-processing).
Note that mailutils only supports IMAP.

Next, create the From address you want to use when sending outbound email for
this mail account. Go to **Administer » Communications » FROM Email Addresses** to
do so.

Finally, you need to add a few more mailutils-specific settings to the Mail Account.
mailutils allows you to specify settings like SMTP servers on a mail account level,
rather than using global outbound email settings.

In mailutils, this configuration is tracked in the `MailutilsSetting` entity.
It can be configured using API4. The following parameters are supported:

Required:
* `mail_setting_id.name`: Name of the mail account that was previously created
* `smtp_server`: SMTP server hostname or IP
* `smtp_port`: SMTP server port; typically 465. Only SMTP servers providing SSL/TLS are supported
* `smtp_username`*: SMTP username
* `smtp_password`: SMTP password
* `from_email_address_id`: ID of From address to be used when sending mail

Optional:
* `support_case_category_id:name`: Support Case Category associated with this mail account
  (when used with the [supportcase](https://github.com/greenpeace-cee/supportcase) extension.)
* `mailutils_template_id:name`: name of the default template that will be used for messages
  from this mail account. This is typically used for things like headers/footers.
* `advanced_config`: JSON object containing advanced configuration. Supports the following properties:
  * `postprocessor.sqltasks`: Accepts an array of SQL Task IDs. The tasks will be called after an
    inbound email is processed.

Here's an example API4 call using the `cv` command-line tool to create a configuration
for a Mail Account called "Example":

    cv api4 MailutilsSetting.create +v mail_setting_id.name=Example +v support_case_category_id:name=without_category +v from_email_address_id=1 +v smtp_server=example.com +v smtp_port=465 +v smtp_username=user@example.com +v smtp_password=secret

### Settings

#### `mailutils_default_location_type_id` (optional)

To alter the default location type mailutils uses when adding *new* email
addresses, you can change the `mailutils_default_location_type_id` setting.
This is useful if you want to use a dedicated location type for support-only
addresses which you may not want (or be allowed) to use for other purposes.
If no value is set, the global default location type will be used.

