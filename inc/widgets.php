<?php
/**
 * Eventbrite theme widgets
 *
 * @package eventbrite-parent
 * @author  Voce Communications
 */


/**
 * Widget that displays the register/ticket call to action button
 */
if ( class_exists( 'Voce_Eventbrite_API' ) && ! class_exists( 'Eventbrite_Register_Ticket_Widget' ) ) {
class Eventbrite_Register_Ticket_Widget extends WP_Widget {

	/**
	 * Create the widget
	 */
	function __construct() {
		$widget_ops = array( 'classname' => 'widget_register_ticket', 'description' => __( 'Display a Register/Ticket button for your Featured Eventbrite Event', 'eventbrite-parent' ) );
		parent::__construct( 'register-ticket', __( 'Eventbrite: Register/Ticket Button', 'eventbrite-parent' ), $widget_ops );
		$this->alt_option_name = 'widget_register_ticket';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	/**
	 * Display function for widget
	 * @param type $args
	 * @param type $instance
	 * @return type
	 */
	function widget($args, $instance) {
		if ( !Voce_Eventbrite_API::get_auth_service() )
			return;

		$cache = wp_cache_get('widget_register_ticket', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo wp_kses( $cache[ $args['widget_id'] ], wp_kses_allowed_html( 'post' ) );
			return;
		}

		if ( ! $featured_events_setting = Voce_Settings_API::GetInstance()->get_setting( 'featured-event-ids', Eventbrite_Settings::eventbrite_group_key() , array() ) ) {
			return;
		}

		ob_start();

		$featured_event_id = array_shift( $featured_events_setting );
		?>
		<p class="text-center">
			<a class="btn btn-full" href="<?php echo esc_url( sprintf( 'http://www.eventbrite.com/event/%1$s/?ref=wporgcta', $featured_event_id['id'] ) ); ?>" target="_blank"><?php echo esc_html( eb_get_call_to_action() ); ?></a>
		</p>
		<?php

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_register_ticket', $cache, 'widget');
	}

	/**
	 * Delete widget cache
	 */
	function flush_widget_cache() {
		wp_cache_delete('widget_register_ticket', 'widget');
	}
}
}
