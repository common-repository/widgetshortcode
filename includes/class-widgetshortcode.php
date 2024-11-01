<?php
/*
 * WidgetShortcode main class
 * Provides mains functions for the plugin
 *
 * @package WidgetShortcode
 */

class WidgetShortcode {

    private static $instance = null;

    /**
     * Create / return an instance of this class.
     *
     * @return	WidgetShortcode
     */
    public static function get_instance(): WidgetShortcode {
        return self::$instance ?? self::$instance = new self();
    }

    private function __construct() {
        add_shortcode( 'widget',   [$this, 'shortcode'] );
        add_action( 'plugins_loaded',   [$this, 'i18n'], 5 );
        add_action( 'widgets_init',     [$this, 'arbitrary_sidebar'], 20 );
        add_action( 'in_widget_form',   [$this, 'in_widget_form'], 10, 3 );

        add_action('init', function(){
            if(current_user_can('manage_options') && isset($_REQUEST['widgetshortcode_maintenance'])) {
                retrieve_widgets(TRUE);
            }
        },-1);
    }

    /**
     * Load translation files
     */
    function i18n() {
        load_plugin_textdomain( 'widgetshortcode', false, '/languages' );
    }

    /**
     * Output a widget using 'widget' shortcode. Requires the widget ID.
     * You can overwrite widget args: before_widget, before_title, after_title, after_widget
     *
     * @example [widget id="text-1"]
     */
    public function shortcode( $atts, $content = null ) {
        $atts['echo'] = false;
        return $this->do_widget( $atts );
    }

    /**
     * Register arbitrary widget area
     *
     * You can use WidgetShortcode on widgets in any area
     * But this special sidebar does not render anywhere in the front-end
     */
    function arbitrary_sidebar() {
        register_sidebar( [
            'name' => __( 'WidgetShortcode', 'widgetshortcode' ),
            'description'	=> __( 'This widget area is not displayed on frontend and the can be used for [widget] shortcode.', 'widgetshortcode' ),
            'id' => 'widgetshortcode_arbitrary',
            'before_widget' => '',
            'after_widget'	=> '',
        ] );
    }

    /**
     * Show the shortcode for the widget
     */
    function in_widget_form( $widget, $return, $instance ) {
        ?>
        <p>
            <h3><?php echo __( 'Shortcode', 'widgetshortcode' );?></h3>
            <?php if($widget->number == '__i__') {
                __( 'Please edit & save the widget to continue', 'widgetshortcode' );
            } else { ?>
                <input type="text" value="<?php echo esc_attr( '[widget id="'. $widget->id .'"]' );?>" readonly="readonly" class="widefat" onclick="this.select();" />
            <?php } ?>
        </p>
    <?php
    }

    /**
     * Return an array of all widgets as the key, their position as the value
     * @return array
     */
    function get_widgets_map(): array {
        $sidebars_widgets = wp_get_sidebars_widgets();
        $widgets_map = [];
        if ( ! empty( $sidebars_widgets ) )
            foreach ( $sidebars_widgets as $position => $widgets )
                if ( ! empty( $widgets) )
                    foreach ( $widgets as $widget )
                        $widgets_map[$widget] = $position;

        return $widgets_map;
    }

    /**
     * Get widget options
     */
    public function get_widget_options( $widget_id ) {
        global $wp_registered_widgets;
        if ( isset( $wp_registered_widgets[$widget_id] ) ) {
            preg_match( '/-(\d+)$/', $widget_id, $number );
            $options = get_option( $wp_registered_widgets[$widget_id]['callback'][0]->option_name );
            $instance = $options[$number[1]];
        }

        return $instance ?? [];
    }

    /**
     * Display a widget
     *
     * @param mixed args
     * @return string
     */
    function do_widget( $args ): string {

        global $_wp_sidebars_widgets, $wp_registered_widgets, $wp_registered_sidebars;

        extract( shortcode_atts( [
            'id' => '',
            'css_class' => '',
            'title' => true, /* whether to display the widget title */
            'container_tag' => 'div',
            'container_class' => 'widget %2$s',
            'container_id' => '%1$s',
            'title_tag' => 'h2',
            'title_class' => 'widgettitle',
            'echo' => true
        ], $args, 'widget' ) );

        /*
         * @note: for backward compatibility: allow overriding widget args through the shortcode parameters
         */
        $widget_args = shortcode_atts( [
            'before_widget' => '<' . tag_escape($container_tag) . ' id="' . esc_attr($container_id) . '" class="' . esc_attr($container_class) . ' ' . esc_attr($css_class) . '">',
            'before_title' => '<' . tag_escape($title_tag) . ' class="' . esc_attr($title_class) . '">',
            'after_title' => '</' . tag_escape($title_tag) . '>',
            'after_widget' => '</' . tag_escape($container_tag) . '>',
        ], $args );

        extract( $widget_args );

        if ( empty( $id ) || ! isset( $wp_registered_widgets[$id] ) ) {
            return '';
        }

        // get the widget instance options
        preg_match( '/-(\d+)$/', $id, $number );
        $options = ( ! empty( $wp_registered_widgets ) && ! empty( $wp_registered_widgets[$id] ) ) ? get_option( $wp_registered_widgets[$id]['callback'][0]->option_name ) : [];
        $instance = $options[$number[1]] ?? [];
        $class = get_class( $wp_registered_widgets[$id]['callback'][0] );

        // maybe the widget is removed or de-registered
        if ( ! $class ) {
            return '';
        }

        /* build the widget args that needs to be filtered through dynamic_sidebar_params */
        $params = [
            0 => [
                'name' => '',
                'id' => '',
                'description' => '',
                'before_widget' => $before_widget,
                'before_title' => $before_title,
                'after_title' => $after_title,
                'after_widget' => $after_widget,
                'widget_id' => $id,
                'widget_name' => $wp_registered_widgets[$id]['name']
            ],
            1 => [
                'number' => $number[0]
            ]
        ];

        // If possible, use sidebar's parameters
        $widgets_map = $this->get_widgets_map();
        if ( isset( $widgets_map[$id] ) ) {
            $params[0]['name'] = $wp_registered_sidebars[$widgets_map[$id]]['name'];
            $params[0]['id'] = $wp_registered_sidebars[$widgets_map[$id]]['id'];
            $params[0]['description'] = $wp_registered_sidebars[$widgets_map[$id]]['description'];
        }

        $params = apply_filters( 'dynamic_sidebar_params', $params );

        $show_title = ( '0' === $title || 'no' === $title || false === $title ) ? false : true;
        if ( ! $show_title ) {
            $params[0]['before_title'] = '<!-- widgetshortcode_before_title -->';
            $params[0]['after_title'] = '<!-- widgetshortcode_after_title -->';
        } elseif ( is_string( $title ) && strlen( $title ) > 0 ) {
            $instance['title'] = $title;
        }

        // Substitute HTML id and class attributes into before_widget
        $classname_ = '';
        foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
            if ( is_string( $cn ) ) {
                $classname_ .= '_' . $cn;
            } elseif ( is_object($cn) ) {
                $classname_ .= '_' . get_class($cn);
            }
        }

        $classname_ = ltrim( $classname_, '_' );
        $classname_ .= ' widgetshortcode';

        /* adds area-{AREA} classname to the widget, indicating the widget's original location */
        if ( isset( $widgets_map[$id] ) ) {
            $classname_ .= ' area-' . $widgets_map[$id];
        }

        $params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $id, $classname_ );

        // render the widget
        ob_start();
        echo '<!-- WidgetShortcode -->';
        the_widget( $class, $instance, $params[0] );
        echo '<!-- /WidgetShortcode -->';
        $content = ob_get_clean();

        // Suppress the title
        if ( ! $show_title ) {
            $content = preg_replace( '/<!-- widgetshortcode_before_title -->(.*?)<!-- widgetshortcode_after_title -->/', '', $content );
        }

        if ( $echo !== true ) {
            return $content;
        }

        // @todo shortcode should never print
        echo $content;
        return $content;
    }

    /**
     * Return an array of widgets in the format of [$id => $label]
     *
     * @return array
     */
    function get_widgets_list(): array {
        global $wp_registered_widgets;

        $widgets = [];
        $all_widgets = $this->get_widgets_map();
        if ( ! empty( $all_widgets ) ) {
            foreach ( $all_widgets as $id => $position ) {
                if ( $position == 'widgetshortcode_arbitrary' ) {
                    $title = isset( $wp_registered_widgets[$id]['name'] ) ? $wp_registered_widgets[$id]['name'] : '';
                    $options = $this->get_widget_options( $id );
                    if ( isset( $options['title'] ) && ! empty( $options['title'] ) ) {
                        $title = join( ': ', [$title, $options['title']] );
                    }
                    $widgets[] = [
                        'value' => $id,
                        'label' => $title,
                    ];
                }
            }
        }

        return $widgets;
    }

    public static function maintenance() {
        update_option('widgetshortcode_maintenance', WIDGETSHORTCODE_VER);
        retrieve_widgets(TRUE);
    }
}