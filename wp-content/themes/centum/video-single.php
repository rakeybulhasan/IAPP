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

<!-- 960 Container -->
<div class="container">

    <div class="sixteen columns">

        <!-- Page Title -->

        <!-- Page Title / End -->

    </div>
</div>
<!-- 960 Container / End -->


<!-- 960 Container -->
<div class="container">

    <?php
    $ID= get_the_ID();
    get_sidebar();

    ?>
    <!-- Blog Posts
        ================================================== -->
    <div class="twelve columns">
        <div id="page-title">
            <h2><?php
                echo get_the_title($ID);
                ?></h2>

            <div class="clear"></div>

            <div id="bolded-line"></div>

            </div>
        <?php while (have_posts()) : the_post(); ?>

                <div class="twelve">

                    <div class="picture">
                    <?php the_content();?>
                    </div>

                    <div class="item-description alt">
                        <h5><?php the_title()?></h5>
                        <p></p>
                    </div>

                </div>

            <?php

            wp_reset_postdata();
            ?>
        <?php endwhile; // End the loop. Whew.  ?>
        <?php //} ?>
    </div>
