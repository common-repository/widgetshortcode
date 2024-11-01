<?php
/**
 * Shortcode generator for WidgetShortcode plugin
 *
 * @package WidgetShortcode
 * @since 0.3.4
 */
class WidgetShortcode_TinyMCE {

	private static $instance = null;

	/**
	 * Create / return an instance of this class.
	 *
	 * @return	WidgetShortcode_TinyMCE
	 */
	public static function get_instance(): WidgetShortcode_TinyMCE {
		return self::$instance ?? self::$instance = new self();
	}

	private function __construct() {
		add_filter( 'mce_external_plugins',     [$this, 'mce_external_plugins'] );
		add_filter( 'mce_buttons',              [$this, 'mce_buttons']          );
		add_action( 'admin_enqueue_scripts',    [$this, 'editor_parameters']    );
		add_action( 'wp_enqueue_scripts',       [$this, 'editor_parameters']    );
	}

	function mce_external_plugins( $plugins ) {
		$plugins['widgetShortcode'] = WIDGETSHORTCODE_URL . 'assets/tinymce.js';

		return $plugins;
	}

	function mce_buttons( $mce_buttons ) {
		array_push( $mce_buttons, 'separator', 'widgetShortcode' );
		return $mce_buttons;
	}

	function editor_parameters() {
		wp_localize_script( 'editor', 'widgetShortcodeTinyMCE', [
			'title' => __( 'WidgetShortcode', 'widgetshortcode' ),
			'widgets' => WidgetShortcode::get_instance()->get_widgets_list(),
			'image' => WIDGETSHORTCODE_URL . 'assets/widget-icon.png',
		] );
	}
}