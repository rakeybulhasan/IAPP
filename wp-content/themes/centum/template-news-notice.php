<?php
/**
* Template Name: News Notice Template
 * The News Notice template file.
 * @package WordPress
 */
get_header();


get_template_part( 'news-notice', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar('news-notice');


get_footer();

?>