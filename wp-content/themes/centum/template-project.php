<?php
/**
* Template Name: Project Detail Template
 * The Project Detail template file.
 * @package WordPress
 */
get_header();


get_template_part( 'project', 'page' );

$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);


get_sidebar('project');


get_footer();

?>