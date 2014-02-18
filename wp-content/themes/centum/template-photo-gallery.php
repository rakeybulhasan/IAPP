<?php
/**
* Template Name: Photo Gallery Template
 * The News Notice template file.
 * @package WordPress
 */
get_header();


get_template_part( 'photo', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar();


get_footer();

?>