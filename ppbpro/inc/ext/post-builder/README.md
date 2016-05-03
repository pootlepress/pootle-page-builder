# pootle page builder addon boilerplate
A structured, object oriented foundation to help you quickly navigate your way to making awesome pootle page builder add ons.

## Contents

Page builder add on boilerplate is structured as mentioned below:

* `ppb-post-builder.php` To tell WordPress that you got a plugin here, Fetches the required files and instantiates main plugin class
* `class-ppb-post-builder.php` Your main plugin class, adds admin and public hooks and initiates admin and public classes
* `README.md` The file that you’re currently reading.
* `assets` Contains client side scripts and styles
  * `front-end.js` jQuery script enqueued on front end
  * `front-end.css` Stylesheet enqueued on front end
* `inc` Contains server side scripts to include
  * `class-admin.php` Admin class Adds a sample tab and a sample field in both the panels Row settings panel and Content block panel
  * `class-public.php` Public class Adds sample styles to Row and Content block utilising sample input fields added by admin class
* `pp-api` The helps integrate with WC API Manager on http://pootlepress.com
  * `API Integration Files` Help updating paid add ons

## Features

* All classes, methods and properties are documented so that you know what you need to be changed.
* All action and filter hooks, admin as well as public, are registered by the main class.
* Demonstration of some pootle page builder actions and filters
  * A sample tab added to each panel, row settings panel and content block panel, via `pootlepb_row_settings_tabs` and `pootlepb_content_block_tabs` filters.
  * A sample field added to each panel, row settings panel and content block panel, via `pootlepb_row_settings_fields` and `pootlepb_content_block_fields` filter.
  * Modifying row and content block html attributes via `pootlepb_row_style_attributes` and `pootlepb_content_block_attributes` using the sample settings fields.

## License

The WordPress Plugin Boilerplate is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

## Just these two things to get started quickly

1. Change `ppb-post-builder.php` and `class-ppb-post-builder.php` to something like `your-awesome-addon.php` and `class-your-awesome-addon.php`
2. Do following search and replace through your entire project

Search For | Replace With
-----------|-------------
PPB_Post_Builder_Addon | Your_Awesome_Addon
Pootle Post Builder Addon | Your Awesome Addon
class-ppb-post-builder.php | class-your-awesome-addon.php
ppb-post-builder | ppb-your-awesome-addon

##Documentation

For pootle page builder developer documentation visit http://pootlepress.com/