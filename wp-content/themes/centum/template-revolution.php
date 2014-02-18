<?php
/**
 * Template Name: Page with Revo Slider
 *
 * A custom page template without sidebar.
 *
 * The "Template Name:" bit above allows this to be selectable
 * from a dropdown menu on the edit page screen.
 *
 * @package WordPress
 * @subpackage purepress
 * @since purepress 1.0
 */

get_header();

$slider = get_post_meta($post->ID, 'incr_page_revolution', true);
if($slider) {
	putRevSlider($slider);
}

get_template_part( 'content', 'page' ); 

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);

if($sidebar_side != "left-sidebar") {
	get_sidebar();
}

get_footer(); 

?>