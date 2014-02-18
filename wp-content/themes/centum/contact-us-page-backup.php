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

            <div id="page-title">
                <h2><?php the_title(); ?></h2>


                <div class="clear"></div>

                <div id="bolded-line"></div>
            </div>

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
    <div class="twelve columns">
        <?php if(ICL_LANGUAGE_CODE == 'en'){?>
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Contact us form sidebar for en')) : endif; ?>
        <?php }else{?>
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Contact us form sidebar for bn')) : endif; ?>
        <?php }?>
    <br>


    </div>
