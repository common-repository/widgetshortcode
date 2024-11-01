( function( wp ) {
	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var ServerSideRender = wp.serverSideRender;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var SelectControl = wp.components.SelectControl;

	wp.blocks.registerBlockType( 'widgetshortcode/block', {
		title: __( 'WidgetShortcode', 'widgetshortcode' ),
		icon: 'welcome-widgets-menus',
		category: 'widgets',
		attributes : {
			id: {
				default : '',
			},
		},
		// Display block preview and UI
		edit( props ) {
			return el( 'div', {}, [
				el( ServerSideRender, {
					block: "widgetshortcode/block",
					attributes:  props.attributes
				} ),
				el( InspectorControls, {}, [
					el( SelectControl, {
						value : props.attributes.id,
						label : __( 'Widget from the WidgetShortcode area', 'widgetshortcode' ),
						options : widgetShortcodeGutenberg.widgets,
						onChange : function( id ) {
							props.setAttributes( { id } );
						},
					} )
				] )
			] )
		},
		save() {
			// nothing to see here, ServerSideRender handles this
			return null;
		},
	} );
}(
	window.wp
) );
