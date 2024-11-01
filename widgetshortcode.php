<?php
/**
 * Plugin Name: WidgetShortcode
 * Plugin URI: https://WidgetShortcode.com
 * Description: Output widgets using a simple shortcode.
 * Author: Matt Jaworski
 * Version: 1.1.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: widgetshortcode
 * Domain Path: /languages
 *
 * We are Open Source. You can redistribute and/or modify this software under the terms of the GNU General Public License (version 2 or later)
 * as published by the Free Software Foundation. See the GNU General Public License or the LICENSE file for more details.
 * This software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY.
 */

defined( 'ABSPATH' ) || die;
define( 'WIDGETSHORTCODE_VER', '1.0.3');
define( 'WIDGETSHORTCODE_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'WIDGETSHORTCODE_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

include WIDGETSHORTCODE_DIR . 'includes/class-widgetshortcode.php';
include WIDGETSHORTCODE_DIR . 'includes/class-widgetshortcode-tinymce.php';
include WIDGETSHORTCODE_DIR . 'includes/class-widgetshortcode-gutenberg.php';


register_activation_hook(__FILE__, function(){
    $maintenance = get_option('widgetshortcode_maintenance');
    if(!version_compare($maintenance, WIDGETSHORTCODE_VER, '>=')) {
        WidgetShortcode::maintenance();
    }
});

WidgetShortcode::get_instance();
WidgetShortcode_TinyMCE::get_instance();
WidgetShortcode_Gutenberg::get_instance();