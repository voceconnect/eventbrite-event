<?php
/**
 * Template for single event page
 *
 * @package eventbrite-event
 */

get_header();

$events = eb_api_get_featured_events();
$event = array_shift( $events );
$event_date_timespan = eventbrite_event_get_event_date_timespan( $event );
?>
			<div class="row">
				<div class="span8">
					<div class="left-col">
						<div class="well">

							<?php if ( have_posts() ) : while( have_posts() ) : the_post() ?>
								<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

								<div class="event-intoduction">
									<?php if ( ! empty( $event->logo_url ) ) : ?>
										<img class="event-logo" src="<?php echo esc_url( $event->logo_url ); ?>"/>
									<?php endif; ?>

									<?php if ( ! empty( $event->name->text ) ) : ?>
										<h2 class="event-title"><?php echo esc_html( $event->name->text ); ?></h2>
									<?php endif; ?>

									<?php if ( ! is_wp_error( $event_date_timespan ) ) : ?>
										<span class="event-timespan"><?php echo esc_html( $event_date_timespan ); ?></span>
									<?php endif; ?>

									<a class="event-link" href="<?php echo esc_url( eventbrite_event_get_eb_event_url( $event, 'wporgevent' ) ); ?>"><?php _e( 'More Information &rarr;', 'eventbrite-venue' ); ?></a>

									<?php if ( $event ) : ?>
										<a href="<?php echo esc_url( eventbrite_event_get_eb_event_url( $event, 'wporgevent' ) ); ?>" class="event-register btn"><?php echo esc_html( eb_get_call_to_action() ); ?></a>
									<?php endif; ?>

								</div><!--.event-intoduction-->

								<div class="event-details">
									<!-- Event description block with description and image from eventbrite -->
									<div class="event-description">
										<?php if ( ! empty( $event->description->html ) ) : ?>
											<p>
											<?php echo wp_kses( $event->description->html, wp_kses_allowed_html( 'post' ) ); ?>
											</p>
										<?php endif; ?>
										<!-- Post Entry class for the wordpress content -->
										<div class="post-entry">
											<?php the_content(); ?>
											<div class="clr"></div>
										</div>
									</div>
								</div>

								<?php if ( comments_open() || '0' != get_comments_number() ) : ?>
									<hr/>
									<div class="post-<?php the_ID(); ?>-comments"><?php comments_template(); ?></div>
								<?php endif; ?>

								</div> <!-- end post -->
							<?php endwhile; endif; ?>

						</div>
					</div>
				</div>
				<?php get_sidebar(); ?>
			</div>
<?php
get_footer();
