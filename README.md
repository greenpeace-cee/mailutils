# mailutils

mailutils adds various essential email client features to CiviCRM:

* Reply or forward messages
* Threading
* Search

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.2+
* CiviCRM 5.21+

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

## Configuration

Mailutils expands the "Mail Accounts" feature in CiviCRM. To get started, first
set up a mail account (preferably IMAP) with Email-to-Activity processing.
Additionally, we recommend setting up SMTP for the mail account. When a mail
account is configured with SMTP, Mailutils will use the configured SMTP server
rather than the global SMTP server CiviCRM is configured with. This ensures
that outbound email is added to the "Sent" folder and thus visible to other
mail clients.

### Settings

#### `mailutils_default_location_type_id`

To alter the default location type mailutils uses when adding *new* email
addresses, you can change the `mailutils_default_location_type_id` setting.
This is useful if you want to use a dedicated location type for support-only
addresses which you may not want (or be allowed) to use for other purposes.
If no value is set, the global default location type will be used.

## Usage

(* FIXME: Where would a new user navigate to get started? What changes would they see? *)

## Known Issues

### Missing Features

* Token support in emails
* Message templates
* Attachments

