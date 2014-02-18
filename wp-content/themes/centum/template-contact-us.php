<?php
/**
* Template Name: Contact Us Template
 * The Project Detail template file.
 * @package WordPress
 */



get_header();


get_template_part( 'contact-us', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);



get_footer();

?>