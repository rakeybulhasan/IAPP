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
                <?php
                global $wpdb;
                global $post;
                $options = get_option('ecb_theme_options');
                $cat_id = $options['news-and-notice'];
              $cat_name= get_term_by('id', $cat_id, 'category');
                echo $cat_name->name;
               ?>
                <?php $subtitle  = get_post_meta($post->ID, 'incr_subtitle', true);
                if ( $subtitle) {
                    echo ' <span>/ '.$subtitle.'</span>';
                } ?>
            </h1>

            <?php if(ot_get_option('centum_breadcrumbs') == 'yes') echo dimox_breadcrumbs() ;?>
            <div id="bolded-line"></div>
        </div>

            <?php
            global $wpdb;
            global $post;
            $options = get_option('ecb_theme_options');

            $cat_id = $options['news-and-notice'];
            $wp_query = new WP_Query(array('orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => '10', 'post_status' => 'publish', 'cat' => $cat_id));

            while ( $wp_query->have_posts() ) : $wp_query->the_post();


                ?>
                <div class="post">
                    <a class="post-icon standard" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('small-thumb'); ?> </a>
                    <div class="post-content">
                        <div class="post-title"><h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2></div>
                        <div class="post-description">
                            <?php the_excerpt()?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="post-entry"><?php  _e('Read more', 'purepress'); ?></a>
                    </div>
                </div>

            <?php endwhile; ?>
            <!-- Post -->
            <div class="pagination">
                <?php if(function_exists('wp_pagenavi')) :
                    wp_pagenavi();
                else:
                    if ($wp_query->max_num_pages > 1) : ?>
                        <nav id="nav-below" class="navigation">
                            <div class="nav-previous"><?php next_posts_link(__('&larr; Older posts', 'purepress')); ?></div>
                            <div class="nav-next"><?php previous_posts_link(__('Newer posts &rarr;', 'purepress')); ?></div>
                        </nav><!-- #nav-below -->
                    <?php endif;
                endif; ?>

            </div>
<!--
            <ul class="pagination">
                <a href="blog.html#"><li class="current">1</li></a>
                <a href="blog.html#"><li>2</li></a>
                <a href="blog.html#"><li>3</li></a>
            </ul>-->


	</div> <!-- eof eleven column -->

