<?php
/**
* Template Name: Component Template
 * The Component template file.
 * @package WordPress
 */
get_header();
get_template_part('slider');

get_template_part( 'component', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar('component');


get_footer();

?>