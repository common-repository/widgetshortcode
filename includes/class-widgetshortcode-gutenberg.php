<?php
/**
 * Gutenberg compatibility for WidgetShortcode plugin
 *
 * @package WidgetShortcode
 * @since 0.3.4
 */
class WidgetShortcode_Gutenberg {

	private static $instance = null;

	/**
	 * Create / return an instance of this class.
	 *
	 * @return WidgetShortcode_Gutenberg
	 */
	public static function get_instance(): WidgetShortcode_Gutenberg {
		return self::$instance ?? self::$instance = new self();
	}

	private function __construct() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		add_action( 'init', [$this, 'register_block'] );
	}

	function register_block() {
		wp_register_script(
			'widgetshortcode-gutenberg',
			WIDGETSHORTCODE_URL . 'assets/block.js',
			['wp-blocks', 'wp-i18n', 'wp-element', 'wp-components'],
			filemtime( WIDGETSHORTCODE_DIR . 'assets/block.js' ),
			true
		);

		$widgets = WidgetShortcode::get_instance()->get_widgets_list();
		array_unshift( $widgets, ['value' => '', 'label' => ''] );
		wp_localize_script( 'widgetshortcode-gutenberg', 'widgetShortcodeGutenberg', ['widgets' => $widgets,] );

		register_block_type( 'widgetshortcode/block', [
			'editor_script' => 'widgetshortcode-gutenberg',
			'render_callback' => [$this, 'render_callback'],
			'attributes' => [
				'id' => [
					'default' => '',
					'type' => 'string',
				],
				'className' => [
					'default' => '',
					'type' => 'string',
				],
			],
		]);
	}

	/**
	 * Render the widget preview in Gutenberg window
	 *
	 * @return string
	 */
	function render_callback( $atts, $content ) {
		if ( ! isset( $atts['id'] ) || empty( $atts['id'] ) ) {
			return
            '<p style="border:solid 1px #666666;background:white;padding:5px;">'
            .'WidgetShortcode'
            .'<br/>'
            .'<small>'
            . __( 'Add widget(s) to the WidgetShortcode area and save.', 'widgetshortcode' )
            .'<br/>'
            . __( 'Click here and select a widget you want to show in this block.', 'widgetshortcode' )
            . '</small></p>';
		}

		return WidgetShortcode::get_instance()->do_widget( [
			'echo' => false,
			'id' => $atts['id'],
			'css_class' => $atts['className'],
		] );
	}
}