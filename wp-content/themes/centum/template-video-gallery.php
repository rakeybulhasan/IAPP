<?php
/**
 * Template Name: Video gallery Template
 * The Video gallery template file.
 * @package WordPress
 */
get_header();


get_template_part( 'video-gallery', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar();


get_footer();

?>