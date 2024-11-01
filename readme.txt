=== WidgetShortcode ===
Contributors: JaworskiMatt, rsusanto
Plugin Name: WidgetShortcode
Donate link: https://widgetshortcode.com
Tags: widget, shortcode, widgetshortcode, widget shortcode, widgets, shortcodes
Requires PHP: 7.4
Requires at least: 6.0
Tested up to: 6.1.1
Stable tag: 1.1.0
License: GPLv2 or later

Adds a [widget] shortcode which enables you to output widgets anywhere you like.

== Description ==

WidgetShortcode is a WordPress plugin allowing you to **turn any widget into a WordPress shortcode** or **display a widget with a dedicated block**. The shortcodes can then be used in posts, pages, and more.

It also has a dedicated block, making it easier to select a widget to show in the Block Editor.

It works great in the **classic WordPress editor**, but **also in the block editor** (for widgets that don’t have a Gutenberg block) and other page builders. This way you are not limited to only sidebars and your choice of editing tools is not based on the block functionality of the plugins.

===  Automatic mode - WidgetShortcode block ===

This block can render any widget located in the WidgetShortcode position. First add widgets to the dedicated widget area. Then place the WidgetShortcode block, click it and select one of the widgets from the drop-down list.

=== Manual mode - the shortcode ===

The plugin adds a shortcode to every widget. You can copy this shortcode and paste it anywhere in WordPress. This alternative to automatic mode can be used if:
* You are not using the block editor
* You prefer to use a different block to render the shortcode
* You want to use one of the advanced parameters

=== Manual mode - advanced parameters ===

You can use additional parameters below to override HTML, CSS classes and ids. This section is for **advanced users**!

* `title`
  * Overrides the widget title
  * Set to `0` (zero) to disable the title
* `title_tag`
  * HTML tag to use for the widget title wrapper
  * Default: `h2`
* `title_class`
  * CSS class for the widget title wrapper
  * Default: `widgettitle`
* `container_tag`
  * HTML tag to use as the widget container
  * Default: `div`
* `container_class`
  * CSS classname added to the widget container
  * Default: `widget %2$s`
  * Uses `sprintf()` and can crash your website if used incorrectly
* `container_id`
  * HTML ID attribute for the widget container
  * Default: `%1$s`
  * Uses `sprintf()` and can crash your website if used incorrectly

== Special thanks ==

* [shazdeh](https://profiles.wordpress.org/shazdeh/) - for the original code
* [rsusanto](https://profiles.wordpress.org/rsusanto/) - for JavaScript debugging
* Helen Bedd - for testing
* [WordFence](https://wordpress.org/plugins/wordfence/) and [Lana Codes](https://lana.codes/) - for insights into the original plugin's vulnerabilities

== Changelog ==

= 1.1.0 =
* Fixed the WidgetShortcode block crashing on WordPress 6+
* Refactored WidgetSHortcode block deprecations to work with WordPress 6.2

= 1.0.2 =
* **2023-02-12**
* Attempt to **rebuild widget and sidebar database** with `retrieve_widgets(TRUE)` upon **activation** and with a custom `$_REQUEST` parameter

= 1.0.1 =
* **2023-02-12**
* Improved **PHP 8** compatibility

= 1.0.0 =
* **2023-02-13**
* Forked from [Widget Shortcode](https://wordpress.org/plugins/widget-shortcode)
* Fixed a [known XSS vulnerability](https://wpscan.com/vulnerability/5117b2e9-75b5-459a-b22a-b0e1b0744bd3)
* Improved code quality, new code standards, PHP 8.2 compatibility
* Renamed internal classes, constants, widget position, script handles etc. to avoid conflicts with old Widget Shortcode
* Release on WordPress.org