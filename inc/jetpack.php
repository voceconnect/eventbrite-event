<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Eventbrite_Event
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function eventbrite_event_setup_infinite_scroll() {

	add_theme_support( 'infinite-scroll', array(
		'container' => 'content',
		'render'    => 'eventbrite_event_infinite_scroll_render',
	) );

}
add_action( 'after_setup_theme', 'eventbrite_event_setup_infinite_scroll' );

/**
 * Callback for rendering posts during infinite scroll.
 */
function eventbrite_event_infinite_scroll_render() {

	while ( have_posts() ) {
		the_post();
		get_template_part( 'tmpl/post-loop' );
	}

}
