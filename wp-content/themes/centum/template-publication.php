<?php
/**
* Template Name: Publications Template
 * The Publications template file.
 * @package WordPress
 */
get_header();


get_template_part( 'publication', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar('publication');


get_footer();

?>