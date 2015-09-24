=== pootle page builder ===

Contributors: pootlepress, nickburne, shramee
Plugin Name: pootle page builder
Plugin URI: http://www.pootlepress.com/page-builder
Tags: page builder, pagebuilder, pootlepress, pootle page builder, pootlepagebuilder, pootle pagebuilder, layout, layouts, layout builder, layout customizer, content builder, landing pages, landing page builder, site origin
Author URI: http://www.pootlepress.com
Author: PootlePress
Donate link:
Requires at least: 4.1.0
Tested up to: 4.3.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

= What is pootle page builder? =

pootle page builder helps you build compelling WordPress pages easily. No more boring, linear, article pages that are as limited as your posts. 

= What can I do with pootle page builder? =

With pootle page builder you can:

 * Add rows and columns to create the page you want
 * Customize the styles of cells (blocks)
 * Add full width rows, to create long sectional pages
 * Add background images to rows with effects such as parallax
 * Add autoplay background videos to rows
 * Do CSS customizations per cell/block, column or row (for advanced users)

= Is there anything else you can tell me about pootle page builder? =

We have made sure pootle page builder:

 * Feels as much like WordPress as possible making it easy to use for site owners.
 * Code is well written & fully optimized for performance.
 
The core version of pootle page builder has amazing features such as parallax and video backgrounds for row, but we will also be releasing some add-ons soon such as:

 * Portfolios - create amazing portfolio pages with stunning hover animations
 * WooCommerce - integrates page builder deeply into WooCommerce product pages, WooCommerce tab manager and more.
 * Photography - will give you power to add stunning fitlers and effects to your images.

We would love to hear any more add-ons you think would be useful.

= Can I see pootle page builder in action? =
 
 Yes! Watch this video.
[vimeo http://vimeo.com/131757773]

== Usage ==

Install and activate the plugin. In your WordPress dashboard simply add a new page to start using page builder.

== Installation ==

Installing "pootle page builder" can be done either by searching for "pootle page builder" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
2. Upload the ZIP file through the "Plugins > Add New > Upload" screen in your WordPress dashboard.
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Visit the settings screen and configure, as desired.

== Frequently Asked Questions ==

= Where can I get help & support =

For help & support please head over to http://docs.pootlepress.com where there are loads of helpful articles and you can submit a support ticket.

= How do I contribute? =

We encourage everyone to contribute their ideas, thoughts and code snippets. This can be done by forking the [repository over at GitHub](https://github.com/pootlepress/pootle-page-builder/).

== Screenshots ==

1. Edit page interface

2. Content block editor

3. Content block styling

4. Row display settings / Background image

5. Row display settings / Background video

6. Row display settings / Layout

== Upgrade Notice ==

= 0.1 =
* 2015-07-06
* Initial release. It's alive!

== Changelog ==

= 1.0.0 =
* 2015-09-15
 * New - New pootle page builder action hook `pootlepb_before_pb` executed before pb row on public end
 * New - New pootle page builder action hook `pootlepb_after_pb` executed after pb row on public end
 * New - Auto update for non wp.org hosted add-ons
 * New - All methods, functions and constants documented
 * New - File and class documentation blocks more descriptive
 * New - Add-on keys management page in settings
 * Tweak - Improved slider control to allow actual value as well as percentage of max
 * Tweak - Custom unit for slider ( px, em, ms etc. instead of % only )
 * Tweak - #pootle-page-builder on public end positioned relative

= 0.3.1 =
* 2015-08-19
 * Fix - Page builder ui js issues

= 0.3.0 =
* 2015-08-19
 * New - Add-on page
 * New - Chosen multi select fields supported for row settings panel and content editor panel
 * New - Radio fields supported for row settings panel and content editor panel
 * New - pootlepb_enqueue_admin_styles action to enqueue styles
 * New - pootlepb_prioritize_array to sort array items by priority key
 * New - Custom event pootlepb_admin_setup_row_buttons for adding row buttons
 * New - Add column and Remove Column buttons for row
 * New - Action hook pootlepb_add_to_panel_buttons to add buttons to add-to-pb-panel(besides add-row and prebuilt-set buttons)
 * New - Filter hook pootlepb_welcome_message to filter pootle pb welcome message
 * Tweak - Larger row dragging (jquery sortable) handle
 * Tweak - Updating chosen js library
 * Tweak - pootlepb_admin_content_block_title event provides access styles info even on update
 * Fix - Row BG overlay transparency instead of opacity
 * Fix - Updating slider with values in row settings panel and content editor panel
 * Fix - Slider control supports min, max, step and default in row settings panel and content editor panel
 * Fix - Placeholder for input fields in row settings panel and content editor panel

= 0.2.3 = 
* 2015-07-20
 * New - row panel and content panel support custom field type rendering via dynamic hook
 * Tweak - parallax disabled for mobile
 * Tweak - hide icons container until hover
 * Tweak - woocommerce link only active when woocommerce is activated
 * Tweak - force responsive image to display instead of background video for mobile
 * Fix - formatting and sizing in the visual editor
 * Fix - editor now full height of container
 * Fix - page builder content now saves when page title is empty 

= 0.2.2 =
* 2015-07-16
 * Fix - scrolling not working on mobile browsers
 * Fix - background video not working in some themes
 * Fix - editor not working in firefox
 * Fix - background video not autoplaying in safari
 * Fix - add media function now working correctly

= 0.2.1 =
* 2015-07-14
 * Tweak - improve content display when plugin de-activated
 * Tweak - more PHP 5.2.4 support
 * Fix - disable parallax for mobile devices to fix freezing pages on iOS
 * Fix - errors caused by servers running PHP 5.2.4

= 0.2.0 =
* 2015-07-10
 * Tweak - improved column gutter settings
 * Tweak - add slider field type to content edit panel
 * Tweak - global function pootlepb_stringify_attributes
 * Tweak - hard uninstall ( deleting pb data ) now a choice not compulsion
 * Tweak - content edit panel and row settings panel tabbing fields now based on index of tab
 * Tweak - content edit panel and row settings panel allow tabbing with priority settings
 * Tweak - content block attributes now filtered by pootlepb_content_block_attributes
 * Tweak - smart titles can now be changed by pootlepb_admin_content_block_title event on html
 * Tweak - content edit panel tabs now filtered by pootlepb_content_block_tabs
 * Tweak - content edit panel fields filtered by pootlepb_content_block_fields support new 'tab' key
 * Tweak - dynamic action hooks for content edit panel tabs pootlepb_content_block_{$tab}_tab
 * Tweak - row settings panel tabs now filtered by pootlepb_row_settings_tabs
 * Tweak - row settings panel fields filtered by pootlepb_row_settings_fields support new 'tab' key
 * Tweak - dynamic action hooks for row settings panel tabs pootlepb_row_settings_{$tab}_tab
 * Fix - WooCommerce Products item in main nav disappearing

= 0.1.1 =
* 2015-07-08
* Tweak - add new plugin icon (for WordPress.org)
* Tweak - optimization of multiple classes and operations for enhanced performance
* Fix - showing message in edit area before content blocks being displayed
* Fix - page defaulting back to page builder after being used as a page with default editor
* Fix - issue with color picker not updating in content block style options

= 0.1 =
* 2015-07-06
* Initial release. It's alive!
