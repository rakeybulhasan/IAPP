<?php
/**
 * The main template file.
 * @package WordPress
 */
get_header();


get_template_part( 'content', 'single' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar('single');

get_footer();

?>