<?php
/**
 * Global theme functions
 *
 * @package Eventbrite_Event
 */

function eventbrite_event_parent_setup(){

	/**
	 * Define the theme text domain and languages folder for i18n.
	 */
	load_theme_textdomain( 'eventbrite-event', get_template_directory() . '/languages' );

	/**
	 * Enable support for Custom Backgrounds.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => '#373737',
		'default-image' => get_template_directory_uri() . '/img/bg-main.png',
	) );

	/**
	 * Enable support for automatic feed links.
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	/**
	 * Suggest the Eventbrite plugin to users
	 */
	add_theme_support( 'theme-plugin-enhancements', array(
	    array(
	        'slug'    => 'eventbrite-services',
	        'name'    => 'Eventbrite Services',
	        'message' => __( 'The Eventbrite Services plugin is required to connect with Eventbrite.', 'eventbrite-event' ),
	    ),
	) );

	/**
	 * Register our two theme menus.
	 */
	register_nav_menus( array(
		'primary'   => __( 'Primary Menu', 'eventbrite-event' ),
		'secondary' => __( 'Secondary Menu', 'eventbrite-event' ),
	) );

}
add_action( 'after_setup_theme', 'eventbrite_event_parent_setup' );

/**
 * Global theme script enqueing
 *
 */
if ( ! function_exists( 'eventbrite_event_enqueue_scripts' ) ) {
	function eventbrite_event_enqueue_scripts() {

		// Main theme stylesheet
		wp_enqueue_style( 'eventbrite-event-style', get_stylesheet_uri() );

		// Google Fonts
		wp_enqueue_style( 'eventbrite-event-cutive' );
		wp_enqueue_style( 'eventbrite-event-raleway' );

		// Main theme script
		wp_enqueue_script( 'eventbrite-event-main', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), '20130915', true );

		// Bootstrap scripts
		wp_enqueue_script( 'eventbrite-event-carousel', get_template_directory_uri() . '/js/bootstrap/bootstrap-carousel.js', array(), '20130915', true );
		wp_enqueue_script( 'eventbrite-event-collapse', get_template_directory_uri() . '/js/bootstrap/bootstrap-collapse.js', array(), '20130915', true );
		wp_enqueue_script( 'eventbrite-event-tooltip', get_template_directory_uri() . '/js/bootstrap/bootstrap-tooltip.js', array(), '20130915', true );
		wp_enqueue_script( 'eventbrite-event-popover', get_template_directory_uri() . '/js/bootstrap/bootstrap-popover.js', array(), '20130915', true );

		// Modernizr
		wp_enqueue_script( 'eventbrite-event-modernizr', get_template_directory_uri() . '/js/libs/modernizr.min.js',     array(), '20140304', false );

		// Inline reply script for comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

	}
	add_action( 'wp_enqueue_scripts', 'eventbrite_event_enqueue_scripts' );
}

/**
 * Register Google Fonts
 */
function eventbrite_event_google_fonts() {
	$protocol = is_ssl() ? 'https' : 'http';

	/*	translators: If there are characters in your language that are not supported
		by Raleway, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Raleway font: on or off', 'eventbrite-event' ) ) {
		wp_register_style( 'eventbrite-event-raleway', "{$protocol}://fonts.googleapis.com/css?family=Raleway:400,800" );
	}

	/*	translators: If there are characters in your language that are not supported
		by Cutive, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Cutive font: on or off', 'eventbrite-event' ) ) {
		wp_register_style( 'eventbrite-event-cutive', "$protocol://fonts.googleapis.com/css?family=Cutive" );
	}
}
add_action( 'init', 'eventbrite_event_google_fonts' );

//sidebars
function eventbrite_event_register_sidebars() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'eventbrite-event' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Appears on posts and pages in the sidebar.', 'eventbrite-event' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'eventbrite_event_register_sidebars' );

/**
 * Custom comment callback template
 *
 * @param type $comment
 * @param type $args
 * @param type $depth
 */
function eventbrite_event_comment_template( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>
	<div <?php comment_class(); ?> id="div-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-id">
			<div class="author-info">
				<?php comment_author_link(); ?> <span><?php
				if ( $comment->comment_author_email == get_the_author_meta( 'email' ) )
					_e( 'responded', 'eventbrite-event' ); else
					_e( 'said', 'eventbrite-event' );
				?>:</span><br/>
				<small><?php printf( __( '%1$s at %2$s', 'eventbrite-event' ), get_comment_date(), get_comment_time() ); ?></small>
			</div>
			<div class="comment-text">
				<?php comment_text(); ?>
				<?php if( $comment->comment_approved == '0' ) : ?>
					<br />
					<em><?php _e( 'Your comment is awaiting moderation.', 'eventbrite-event' ); ?></em>
				<?php endif; ?>
				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<?php
}

/**
 * Get an excerpt for an event description. Strips tags and trims words.
 *
 * @param string $text
 * @param int $words number of words to return
 * @return string
 */
function eventbrite_event_get_event_excerpt( $text, $words = 40 ) {
	return wp_trim_words( strip_tags( $text ), $words );
}

/**
 * Format an event date with the given timezone and optional format.
 * Uses the blog's date format if $date_format is not specified.
 *
 * @param string $date Date string
 * @param string $timezone Timezone in 'Americas/New York' format
 * @param string $date_format
 * @return string
 */
function eventbrite_event_get_event_date( $date, $timezone, $date_format = '' ) {
	if ( ! $date_format )
		$date_format = get_option( 'date_format' );

	$tz = new DateTimeZone( $timezone );
	$dt = new DateTime( $date, $tz );

	return $dt->format( $date_format );
}

/**
 * Get the formatted price and name for tickets of an event
 *
 * @param array $tickets
 * @return array ticket price/name information
 */
function eventbrite_event_get_event_tickets( $tickets ) {
	$prices = array();

	foreach ( $tickets as $ticket ) {
		$price = $ticket->ticket->display_price;
		$name  = $ticket->ticket->name;

		if ( '0.00' == $price )
			$price = _x( 'free', 'ticket price', 'eventbrite-event' );
		else
			$price = sprintf( _x( '$%01.2f', 'ticket price', 'eventbrite-event' ), $price );

		$prices[] = compact( 'price', 'name' );
	}

	return $prices;
}

/**
 * Get a string describing the price for an event's ticket(s)
 *
 * @param array $tickets
 * @return string ticket price. or events with multiple tickets, the lowest
 * price followed by text noting higher priced tickets.
 */
function eventbrite_event_get_event_ticket_price_string( $tickets ) {
	$prices = array();
	$price_suffix = '';
	$currencies = array();
	$currency = '';

	foreach ( $tickets as $ticket ) {
		$decimal = substr( $ticket->ticket->display_price, -3, 1 ) ?: '.';    //find decimal delimiter
		$amount_parts = explode( $decimal, $ticket->ticket->display_price ); //split display_price into array around delimiter
		$amount_parts[0] = preg_replace( '/\D/', '', $amount_parts[0] );     //strip non-numeric formating from first half
		$prices[] = implode( '.', $amount_parts ) * 100;                     //rejoin with '.' as delimiter

		$currencies[] = $ticket->ticket->currency;
	}

	if ( 1 == count( $prices ) ) {
		if ( 0 == $prices[0] )
			return _x( 'Free', 'ticket price', 'eventbrite-event' );
		else
			$price = $prices[0];
	} else {
		$price = min( $prices );

		if ( 0 == $price )
			return _x( 'Free and up', 'ticket price', 'eventbrite-event' );

		$price_suffix = ' and up';
	}

	if ( 1 == count( array_unique( $currencies ) ) )
		$currency = ' ' . $currencies[0];

	return sprintf( _x( '%s%s%s', 'ticket price: price - currency - price suffix', 'eventbrite-event' ), number_format_i18n( $price / 100, 2 ), $currency, $price_suffix );
}

/**
 * Get a string representing the timespan of an event
 *
 * @param object $event
 * @return string
 */
function eventbrite_event_get_event_date_timespan( $event, $occurrence = 0 ) {
	if ( ! is_object( $event ) )
		return new WP_Error( 'event_not_set', esc_html__( "The event variable is expected to be an object." ) );

	try {
		$tz = new DateTimeZone( $event->start->timezone );
	} catch( Exception $e ) {
		return new WP_Error( 'bad_datetimezone', $e->getMessage() );
	}

	if ( 0 < (int) $occurrence && is_array( $event->repeat_schedule ) && isset( $event->repeat_schedule[$occurrence] ) ) {
		$event_occurrence  = $event->repeat_schedule[$occurrence];
		$event_start_date  = $event_occurrence->start_date;
		$event_end_date    = $event_occurrence->end_date;
	} else {
		$event_start_date = $event->start->local;
		$event_end_date   = $event->end->local;
	}

	try {
		$start_date = new DateTime( $event_start_date, $tz );
	} catch( Exception $e ) {
		return new WP_Error( 'bad_datetime', $e->getMessage() );
	}

	try {
		$end_date = new DateTime( $event_end_date, $tz );
	} catch( Exception $e ) {
		return new WP_Error( 'bad_datetime', $e->getMessage() );
	}

	if ( $start_date->format( 'mdY' ) === $end_date->format( 'mdY' ) ) {
		$date_format_start = 'l, F j, Y \f\r\o\m g:i A';
		$date_format_end   = '\t\o g:i A';
	} else {
		$date_format_start = 'l, F j, Y \a\t g:i A';
		$date_format_end   = '- l, F j, Y \a\t g:i A';
	}

	$time_zone_transitions = $tz->getTransitions();
	$time_zone_string      = $time_zone_transitions[0]['abbr'];

	return sprintf( _x( '%s %s (%s)', 'event timespan: statdate, end date, (time zone)', 'eventbrite-event' ), $start_date->format( $date_format_start ), $end_date->format( $date_format_end ), $time_zone_string );
}

/**
 * Get the events for month and year of the venue set in the admin
 *
 * @param int $month numeric value of the month
 * @param int $year year
 * @return type
 */
function eventbrite_event_get_monthly_events( $month, $year ) {
	$venue_id     = get_eventbrite_setting( 'venue-id', 'all' );
	$organizer_id = get_eventbrite_setting( 'organizer-id', 'all' );

	$venue_events = Voce_Eventbrite_API::get_user_events( array(
		'venue'     => $venue_id,
		'organizer' => $organizer_id
	) );

	$calendar_events = eventbrite_event_filter_events_by_month($month, $year, $venue_events);

	return $calendar_events;
}

/**
 * Builds the calendar control for the specified month and year
 *
 * @param int $month numeric value of the month
 * @param int $year year
 * @return type
 */
function eventbrite_event_get_calendar_of_events( $month, $year ) {
	$month_events = eventbrite_event_get_monthly_events( $month, $year );

	$calendar = Calendar::factory( $month, $year );

	$calendar->standard( 'today' )->standard( 'prev-next' );

	foreach ( $month_events as $month_event ) {

		$start_date = new DateTime( $month_event->event->start_date );
		$end_date   = new DateTime( $month_event->event->end_date );

		$start_time = $start_date->format( 'g:ia' );
		$end_time   = $end_date->format( 'g:ia' );

		$eb_event_url = eventbrite_event_get_eventbrite_event_event_url( $month_event->event, 'wpcalendar' );
		$wp_event_url = eventbrite_event_get_wp_event_url( $month_event->event );
		$event_popover_url = $eb_event_url;

		if ( $wp_event_url )
			$event_popover_url = $wp_event_url;

		$format_string = '%1$s - %2$s<a href="%3$s" data-toggle="popover"
			data-content="<a href=\'%8$s\' class=\'pull-right btn\'>%9$s</a><p><span>%1$s-%2$s</span>%5$s</p><p>%6$s</p>"
			data-original-title="%7$s">%4$s</a>';

		$output = sprintf( $format_string,
			esc_html( $start_time ),
			esc_html( $end_time ),
			esc_url( $event_popover_url ),
			esc_html( $month_event->event->title ),
			esc_html( eventbrite_event_get_event_ticket_price_string( $month_event->event->tickets ) ),
			esc_html( eventbrite_event_get_event_excerpt( $month_event->event->description, 20 ) ),
			esc_html( $month_event->event->title ),
			esc_url( $eventbrite_event_event_url ),
			__( 'Buy', 'eventbrite-event' )
		);

		$event = $calendar->event()
			->condition( 'timestamp', $start_date->format( 'U' ) )
			->title( esc_html( $month_event->event->title ) )
			->output ( $output );
		$calendar->attach( $event );

		$diff = date_diff( $start_date, $end_date );
		$days_diff = (int) $diff->format( '%a' );
		if ( $days_diff ) {

			$start_day = (int) $start_date->format( 'Ymd' );

			$event_title = $month_event->event->title . _x( ' - cont.', 'calendar', 'eventbrite-event' );

			$output = sprintf( $format_string,
				esc_html( $start_time ),
				esc_html( $end_time ),
				esc_url( $event_popover_url ),
				esc_html( $event_title ),
				esc_html( eventbrite_event_get_event_ticket_price_string( $month_event->event->tickets ) ),
				esc_html( eventbrite_event_get_event_excerpt( $month_event->event->description, 20 ) ),
				esc_html( $month_event->event->title ),
				esc_url( eventbrite_event_get_eventbrite_event_event_url( $month_event->event, 'wpcalendar' ) ),
				__( 'Buy', 'eventbrite-event' )
			);

			$counter = 0;
			while ( $counter < $days_diff ) {
				$counter += 1;
				$event = $calendar->event()
					->condition( 'timestamp', strtotime( $start_day + $counter ) )
					->title( esc_html( $month_event->event->title ) )
					->output( $output );

				$calendar->attach( $event );
			}
		}
	}

	return $calendar;
}

/**
 * Retrieve the event's Eventbrite URL, with the referrer value replaced
 *
 * @param object $event
 * @return string
 */
function eventbrite_event_get_eb_event_url( $event, $refer = 'wporglink' ) {
	$url = '';
	if ( empty( $event->url ) ) {
		return $url;
	} else {
		$url = $event->url;
	}

	if ( $refer )
		$url = add_query_arg( 'ref', $refer, $url );

	return $url;
}

/**
 * Get the page id set in the eventbrite settings
 *
 * @param string $type the type to get based on the setting name
 * @return mixed false if the page isn't set or doesn't exist or the url
 */
function eventbrite_event_get_page_id( $type ) {
	return get_eventbrite_setting( "{$type}-page-id", false );
}

/**
 * Function to get the page link for pages set in the eventbrite settings,
 * if a page is used with page_on_front we can still utilize the page's
 * original url for a base.
 *
 * An important use for this function is if a user sets the "Events" page as the
 * "page on front" to preserve the original link for use with single events.
 *
 * @param string $type the type to get based on the setting name
 * @return string
 */
function eventbrite_event_get_eventbrite_page_link( $ebpage ) {
	global $wp_rewrite;

	if ( ! $ebpage )
		return false;

	$rewrite = $wp_rewrite->get_page_permastruct();
	if ( ! empty( $rewrite ) && ( get_post_status( $ebpage ) == 'publish' ) ) {
		$event_page_link = str_replace( '%pagename%', get_page_uri( $ebpage ), $rewrite );
		$event_page_link = home_url( $event_page_link );
		$event_page_link = user_trailingslashit( $event_page_link, 'page' );
	} else {
		$event_page_link = home_url( '?page_id=' . $ebpage->ID );
	}

	return $event_page_link;
}

/**
 * Get the url for an Eventbrite selected page
 *
 * @param string $type the type to get based on the setting name
 * @return mixed false if the page isn't set or doesn't exist or the url
 */
function eventbrite_event_get_page_url( $type ) {
	$eb_page_id = eventbrite_event_get_page_id( $type );
	if ( ! $eb_page_id )
		return false;

	$eb_page = get_post( $eb_page_id );
	if ( ! $eb_page )
		return false;

	$eb_page_link = eventbrite_event_get_eventbrite_page_link( $eb_page );

	return $eb_page_link;
}

/**
 * Output a block of HTML like page-loop for a given page title
 */
function eventbrite_event_page_content_block( $page_title ) {
	$page = get_page_by_title( $page_title );

	if ( ! is_null( $page ) ) :
		setup_postdata( $page );
	?>
	<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
		<div class="post-entry">
			<?php the_content( __( 'Read the rest of this entry &raquo;', 'eventbrite-event' ) ); ?>
			<div class="clr"></div>
		</div> <!-- end post-entry -->
	</div> <!-- end post -->
	<?php
	wp_reset_postdata();
	endif;
}

/*
* Print formatted posted on string
*/
function eventbrite_event_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$update_time = '';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
		$update_time = '<time class="updated" datetime="%1$s">%2$s</time>';


	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	printf( __( '<span class="posted-date">Posted on <a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'eventbrite-event' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		$time_string
	);

	if ( !empty($update_time) ) {
		$update_time = sprintf( $update_time,
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		printf( __( ' <span class="updated-date">Updated on <a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'eventbrite-event' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			$update_time
		);
	}
}

function eventbrite_event_filter_events_by_month( $month, $year, $venue_events ) {
	$filtered_events = array();
	foreach( $venue_events as $venue_event ) {
		$start_time = strtotime( $venue_event->event->start_date );
		$date       = getdate( $start_time );
		if ( ( $date['mon'] == $month ) && ( $date['year'] == $year ) )
			$filtered_events[] = $venue_event;
	}
	return $filtered_events;
}

/**
 * Load the specified template if the currently queried object id matches the
 * given page id
 *
 * @param int $page_id the ID of the page to match
 * @param int $queried_object_id the currently queried object's id
 * @param string $template template path relative to theme dir
 */
function eventbrite_event_maybe_include_template( $page_id, $queried_object_id, $template ) {
	if ( $page_id && $page_id === $queried_object_id ) {
		do_action( 'eventbrite_event_template_redirect', $page_id, $queried_object_id, $template );
		include( get_stylesheet_directory() . '/' . $template );
		die();
	}
}

function eventbrite_event_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'eventbrite_event_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( 1 < count( $attachment_ids ) ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}

function eventbrite_event_caption_shortcode( $val, $attr, $content = null ) {
	extract( shortcode_atts( array(
		'id'      => '',
		'align'   => 'aligncenter',
		'width'   => '',
		'caption' => ''
	), $attr ) );

	if ( 1 > (int) $width || empty( $caption ) )
		return $val;

	$capid = '';
	if ( $id ) {
		$id = esc_attr( $id );
		$capid = 'id="figcaption_'. $id . '" ';
		$id = 'id="' . $id . '" aria-labelledby="figcaption_' . $id . '" ';
	}

	return '<figure ' . $id . 'class="wp-caption ' . esc_attr( $align ) . '" style="width: '
	. (int) $width . 'px">' . do_shortcode( $content ) . '<figcaption ' . $capid
	. 'class="wp-caption-text">' . $caption . '</figcaption></figure>';
}
add_filter( 'img_caption_shortcode', 'eventbrite_event_caption_shortcode', 10, 3 );

/**
 * Add eventbrite info to title
 * @param string $title
 * @return string
 */
function eventbrite_event_wp_title( $title ) {
	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	return $title;
}
add_filter( 'wp_title', 'eventbrite_event_wp_title', 10, 2 );

/**
 * Displays navigation to next/previous set of posts when applicable.
 */
function eventbrite_event_paging_nav( $args = array() ) {

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) )
		wp_parse_str( $url_parts[1], $query_args );

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	$default = array(
		'base'      => $pagenum_link,
		'format'    => $format,
		'total'     => $GLOBALS['wp_query']->max_num_pages,
		'current'   => $paged,
		'mid_size'  => 1,
		'add_args'  => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '&larr; Previous', 'eventbrite-event' ),
		'next_text' => __( 'Next &rarr;', 'eventbrite-event' ),
		'type'      => 'list'
	);

	$paginate_links_args = wp_parse_args( (array) $args, $default );
	$paginate_links_args = array_intersect_key( $paginate_links_args, $default );

	// Don't print empty markup if there's only one page.
	if ( $paginate_links_args['total'] < 2 )
		return;

	$links = paginate_links( $paginate_links_args );

	if ( $links ) :
	?>
	<div class="pagination pagination-centered">
		<?php echo $links; ?>
	</div>
	<?php
	endif;
}

function eventbrite_event_get_venue_address( $event ) {

	if ( ! $event || empty( $event->venue ) )
		return false;

	$venue      = $event->venue;
	$venue_info = array();

	// formulate full address to easily output
	$venue_full_add   = array();
	if ( ! empty( $venue->name ) )
		$venue_full_add['line-1'] = $venue->name;
	if ( ! empty( $venue->address->address_1 ) )
		$venue_full_add['line-2'] = $venue->address->address_1;
	if ( ! empty( $venue->address->address_2 ) )
		$venue_full_add['line-3'] = $venue->address->address_2;

	$venue_city_state = array();
	if ( ! empty( $venue->city ) )
		$venue_city_state[] = $venue->city;
	if ( ! empty( $venue->region ) )
		$venue_city_state[] = $venue->region;

	// ZIP info is missing from the new (v3) API at this point (July 8, 2014)
	// $venue_zip = ( ! empty( $venue->postal_code ) ) ? $venue->postal_code : '';
	// if ( $venue_city_state || $venue_zip )
	// 	$venue_full_add['line-4'] = implode( ', ', $venue_city_state ) . ' ' . $venue_zip;

	$venue_info['mailing-address'] = $venue_full_add;

	return $venue_info;
}

function eventbrite_event_get_venue_google_map_url( $event, $args = array() ) {
	$defaults = array(
		'zoom'   => '13',
		'size'   => '320x320',
		'sensor' => 'false'
	);

	if ( ! $event || empty( $event->venue ) )
		return false;

	$venue = $event->venue;

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	$google_map = false;
	if ( is_object( $venue ) ) {
		$lat  = isset( $venue->latitude ) ? $venue->latitude : false;
		$long = isset( $venue->longitude ) ? $venue->longitude : false;

		$parameters = array();
		if ( $lat && $long ) {
			$parameters[] = $lat;
			$parameters[] = $long;
		} else {
			if ( isset( $venue->address ) ) {
				$address = $venue->address;
				if ( isset( $venue->address_2 ) )
					$address .= ' ' . $venue->address_2;

				$parameters[] = $address;
			}
			if ( isset( $venue->city ) )
				$parameters[] = $venue->city;
			if ( isset( $venue->region ) )
				$parameters[] = $venue->region;
			if ( isset( $venue->postal_code ) )
				$parameters[] = $venue->postal_code;
		}

		if ( $parameters ) {
			$google_map = 'http://maps.googleapis.com/maps/api/staticmap';
			$location   = implode( ',', $parameters );
			$google_map = add_query_arg( 'center', $location, $google_map );
			$google_map = add_query_arg( 'zoom', $zoom, $google_map );
			$google_map = add_query_arg( 'size', $size, $google_map );
			$google_map = add_query_arg( 'markers', $location, $google_map );
			$google_map = add_query_arg( 'sensor', $sensor, $google_map );
		}
	}

	return $google_map;
}

/**
 * redirect to selected Eventbrite page templates
 */
function eventbrite_event_event_template_redirect() {
	if ( class_exists( 'Voce_Eventbrite_API' ) && Voce_Eventbrite_API::get_auth_service() ) {
		$dynamic_pages = eventbrite_event_get_dynamic_pages();
		if ( $dynamic_pages ) {
			foreach ( $dynamic_pages as $key => $template ) {
				$queried_object_id = get_queried_object_id();
				eventbrite_event_maybe_include_template( get_eventbrite_setting( "{$key}-page-id", false ), $queried_object_id, $template );
			}
		}
	}
}
add_action( 'template_redirect', 'eventbrite_event_event_template_redirect' );

function eventbrite_event_set_theme_single_event() {
	// Only allow a single featured event
	add_filter( 'eventbrite-settings_single-featured-event' , '__return_true' );
}
add_action( 'admin_init', 'eventbrite_event_set_theme_single_event' );


/**
 * Register the widgets used by the theme, if available in the activated Eventbrite plugin.
 */
function eventbrite_event_event_register_widgets() {
	if ( class_exists( 'Eventbrite_Introduction_Widget' ) ) {
		register_widget( 'Eventbrite_Introduction_Widget' );
	}
}
add_action( 'widgets_init', 'eventbrite_event_event_register_widgets' );

/**
 * Suggested default pages for the event theme
 * @param type $default_pages
 * @return array
 */
function eventbrite_event_event_default_pages( $default_pages ) {
	$event_pages = array(
		'attend-event' => array(
			'title' => __( 'Attend Event', 'eventbrite-event' )
		),
		'event-info' => array(
			'title' => __( 'Event Info', 'eventbrite-event' )
		),
	);

	$event_pages = array_merge( $default_pages, $event_pages );

	return $event_pages;
}
add_filter( 'eventbrite_default_pages', 'eventbrite_event_event_default_pages' );

/**
 * dynamic pages
 *
 * @return array
 */
function eventbrite_event_get_dynamic_pages() {
	return array(
		'event-info' => 'template-single-event.php',
		'attend-event' => 'template-attend-event.php',
	);
}

/**
 * Get the WordPress event info URL
 *
 * @param object $event
 * @return string
 */
function eventbrite_event_get_wp_event_url( $event ) {
	$events_page_url = eventbrite_event_get_page_url( 'event-info' );
	if ( ! $events_page_url )
		return '';

	return $events_page_url;
}

/**
 * Filter to handle the customized (events/posts) search template query, forcing no paging when search "events" to allow
 * "events" paging and halving posts_per_page when initial searching
 * @global WP_Object $wp_query
 * @param string $search
 * @param WP_Object $query
 * @return string
 */
function eventbrite_event_multi_event_search( $search, &$query ) {
   global $wp_query;

   if ( is_search() && ! is_admin() ) {
       if ( isset( $_REQUEST['type'] ) ) {
           // Force no paging so a 404 does not occur when paging through "events"
           if ( 'events' == $_REQUEST['type'] ) {
               $wp_query->is_paged = false;
           }
       } else {
           // Only display half the results on the initial search
           $query->query_vars['posts_per_page'] = ceil( get_option( 'posts_per_page' ) / 2 );
       }
   }

   return $search;
}
add_filter( 'posts_search', 'eventbrite_event_multi_event_search', 10 , 2 );

/**
 * Set the content width global.
 */
 if ( ! isset( $content_width ) ) {
	$content_width = 705;
 }

/**
 * Adds support for a custom header image.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Require our Theme Plugin Enhancements class.
 */
require get_template_directory() . '/inc/plugin-enhancements.php';

/**
 * Load our Eventbrite theme options.
 */
require get_template_directory() . '/inc/theme-options.php';

/**
 * Load Eventbrite widgets.
 */
require get_template_directory() . '/inc/widgets.php';
