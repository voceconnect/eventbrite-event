<?php
/**
 * Template for post meta
 *
 * @package Eventbrite_Event
 */
?>
<?php
/* translators: used between list items, there is a space after the comma */
$category_list = get_the_category_list( __( ', ', 'eventbrite-event' ) );

/* translators: used between list items, there is a space after the comma */
$tag_list = get_the_tag_list( '', __( ', ', 'eventbrite-event' ) );

if ( '' != $tag_list ) {
	$meta_text = __( 'This entry was posted in %1$s and tagged %2$s.', 'eventbrite-event' );
} else {
	$meta_text = __( 'This entry was posted in %1$s.', 'eventbrite-event' );
}

edit_post_link(
    __( 'Edit', 'eventbrite-event' ),
    '<p class="edit-link">',
    '</p>'
);

if ( !empty( $category_list ) || !empty( $tag_list ) ) {
    echo '<p class="post-meta">';
    printf(
    	$meta_text,
    	$category_list,
    	$tag_list
    );
    echo '</p>';
}
