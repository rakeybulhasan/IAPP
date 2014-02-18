<?php
/**
* Template Name: Working Area Template
 * The Working Area template file.
 * @package WordPress
 */
get_header();


get_template_part( 'working-area', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar('working-area');


get_footer();

?>