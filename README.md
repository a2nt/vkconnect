# Vk Connect Integration Module

## Maintainer Contact
 * Anton Fedianin
   <tony (at) twma (dot) pro>
   <https://twma.pro>

## Requirements
 * SilverStripe 3.2

## Overview

The module provides a **basic** interface for implementing the Vk PHP SDK
on your SilverStripe website. The Vk SDK allows users to login to your
website using their Vk account details, creating a single sign-on within
the existing SilverStripe member system.

### What it provides

* Loads the VK PHP SDK.

* Provides $VkLoginLink template variable to generate a link to login to
VK.com Upon clicking the link and being redirected back to your application
the SilverStripe `Member::currentUser()` will be populated with a `Member`
instance linked to the users VK profile.

```
<% with CurrentMember %>
	$Name $Avatar(small)
<% end_with %>
```

## Installation

```
composer require "a2nt/silverstripe-vkconnect" "dev-master"
```

[Register your website / application](https://vk.com/dev) with VK.

Set your configuration through the SilverStripe Config API. For example I keep
my configuration in `mysite/_config/vkconnect.yml` file:

```
VkControllerExtension:
  app_id: 'MyAppID'
  api_secret: 'Secret'
```

Update the database by running `/dev/build` to add the additional fields to
the `Member` table and make sure you `?flush=1` when you reload your website.

```
<a href="$VkLoginLink">Login via Vk</a>
```

You can also access the Vk PHP SDK in your PHP code..

```php
// https://developers.Vk.com/docs/php/VkSession/4.0.0
$session = Controller::curr()->getVkSession();
```

For more information about what you can do through the SDK see:

https://vk.com/dev/manuals

### Options

All the following values are set either via the Config API like follows

  Config::inst()->update('VkControllerExtension', '$option', '$value')

Or (more recommended) through the YAML API

  VkControllerExtension:
    option: value

### app_id

Your app id. Found on the Vk Developer Page.

### api_secret

Vk API secret. Again, from your Application page.

### create_member

  Optional, default: true

Whether or not to create a `Member` record in the database with the users
information. If you disable this, ensure your code uses $CurrentVkMember
rather than $Member. Other access functionality (such as admin access) will not
work.

### member_groups

  Optional, default ''

A list of group codes to add the user. For instance if you want every member who
joins through Vk to be added to a group `Vk Members` set the
following:

  VkControllerExtension:
    member_groups:
      - Vk_members

### permissions

  Optional, default 'email'

A list of permission codes you want from the user. Permission codes are listed
on [developers.Vk.com](https://developers.Vk.com/docs/reference/login).

Ensure you include email in your list if you require `create_member`.

### Vk_fields

  Default 'email','first_name','last_name'

A list of fields you want to retrieve from Vk for the user. Available fields are listed
on [developers.Vk.com](https://developers.Vk.com/docs/graph-api/reference/user).

Ensure you include email in your list if you require `create_member`.

### sync_member_details

  Optional, default true

Flag as to whether to replace user information (such as name) in your database
with the values from Vk.

## License

Released under the BSD-3-Clause License.

