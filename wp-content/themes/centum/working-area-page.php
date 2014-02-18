<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage purepress
 * @since purepress 1.0
 */
?>

<!--  Page Title -->

	<!-- 960 Container Start -->
	<div class="container">
		<div class="sixteen columns">

		</div>
	</div>
	<!-- 960 Container End -->

<!-- Page Title End -->

<!-- 960 Container -->
<div class="container">
	<?php

	$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);

	?>

<!-- Blog Posts
	================================================== -->
	<div class="twelve columns <?php if($sidebar_side == "left-sidebar") { echo "left-sb"; } ?>">
        <div id="page-title">
            <?php $bredcrumbs = ot_get_option('centum_breadcrumbs'); ?>
            <h1 <?php if($bredcrumbs == 'yes') echo 'class="has-breadcrumbs"';?>>
                <?php the_title(); ?>
                <?php $subtitle  = get_post_meta($post->ID, 'incr_subtitle', true);
                if ( $subtitle) {
                    echo ' <span>/ '.$subtitle.'</span>';
                } ?>
            </h1>

            <?php if(ot_get_option('centum_breadcrumbs') == 'yes') echo dimox_breadcrumbs() ;?>
            <div id="bolded-line"></div>
        </div>
        <?php while (have_posts()) : the_post(); ?>
            <!-- Post -->
        <div class="twelve columns boleted_list">

                <?php the_content() ?>

            <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Special comments for project')) : endif; ?>

        </div>

        <?php endwhile; // End the loop. Whew.  ?>
        <div class="twelve columns" style="margin-top: 20px">
            <div class="five columns">
                <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Content Left Sidebar')) : endif; ?>
            </div>
            <div class="five columns" style="float: right">
                <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Content Right Sidebar')) : endif; ?>
            </div>

        </div>
	</div> <!-- eof eleven column -->

