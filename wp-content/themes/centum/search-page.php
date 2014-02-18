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
    $ID= get_the_ID();
    get_sidebar();

    ?>

    <?php

//    $sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);
//
    ?>
    <!-- Blog Posts
        ================================================== -->
    <div class="twelve columns <?php if($sidebar_side == "left-sidebar") { echo "left-sb"; } ?>">
        <div id="page-title">
            <h2><?php
                echo get_the_title($ID);
                ?></h2>


            <div class="clear"></div>

            <div id="bolded-line"></div>
        </div>

        <!-- Post -->

        <?php while (have_posts()) : the_post(); ?>

            <div <?php post_class('post-page'); ?> id="post-<?php the_ID(); ?>" >

                <?php
                // Check what to display above post title (image,vide, slideshow)
                global $shortname;
                $feat_type = get_post_meta($post->ID, 'incr_feattype', true);

                if(function_exists( 'get_post_format' ) && get_post_format( $post->ID ) != 'gallery' && get_post_format( $post->ID ) != 'video' && has_post_thumbnail()) {
                    $showthumb = get_post_meta($post->ID, 'incr_feattype', true);
                    if($showthumb!='hide_thumb'){
                        $thumbbig = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' )
                        ?>
                        <div class="post-img picture">
                            <a rel="image" href="<?php echo $thumbbig[0]; ?>" title="<?php the_title(); ?>">
                                <?php the_post_thumbnail(); ?>
                                <div class="image-overlay-zoom"></div>
                            </a>
                        </div>
                    <?php }
                } ?>

                <?php
                if (( function_exists( 'get_post_format' ) && 'video' == get_post_format( $post->ID ) )  ) {
                    $videoembed = get_post_meta($post->ID, 'incr_video_embed', true);
                    if($videoembed) {
                        echo '<div class="embed video-cont">'.$videoembed.'</div>';
                    } else {
                        global $wp_embed;
                        $videolink = get_post_meta($post->ID, 'incr_video_link', true);

                        $post_embed = $wp_embed->run_shortcode('[embed  width="600" height="360"]'.$videolink.'[/embed]') ;
                        echo '<div class="embed video-cont">'.$post_embed.'</div>';
                    }
                }

                if (( function_exists( 'get_post_format' ) && 'gallery' == get_post_format( $post->ID ) )  ) {
                    $ids = get_post_meta($post->ID, 'pp_gallery_slider', TRUE);
                    $args = array(
                        'post_type' => 'attachment',
                        'post_status' => 'inherit',
                        'post_mime_type' => 'image',
                        'post__in' => explode( ",", $ids),
                        'posts_per_page' => '-1',
                        'orderby' => 'post__in'
                    );


                    $images_array = get_posts( $args );

                    // continued from above ...
                    if($images_array){ ?>
                        <div class="flexslider subpage">
                            <ul class="slides">
                                <?php foreach($images_array as $image){
                                    $attachment = wp_get_attachment_image_src($image->ID, 'large');
                                    $thumb = wp_get_attachment_image_src($image->ID, 'portfolio-medium');
                                    ?>
                                    <li>
                                        <div class="picture">
                                            <a href="<?php echo $attachment[0] ?>"  rel="image-gallery" title="<?php echo $image->post_title; ?>" >
                                                <img src="<?php echo $thumb[0] ?>" alt="<?php echo $image->post_title; ?>" />
                                                <div class="image-overlay-zoom"></div>
                                            </a>
                                        </div>
                                    </li>
                                <?php	} ?>
                            </ul>
                        </div>
                    <?php }
                }
                ?>

                <div class="post-content" style="margin-left: 5px!important;">
                    <div class="post-title">
                        <h1>
                            <a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'purepress'), the_title_attribute('echo=0')); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h1>
                    </div>
                    <?php  $comments = ot_get_option('flext_comments'); ?>
                    <div class="post-meta">
                        <span><i class="mini-ico-calendar"></i><?php printf('<a href="%1$s" class="published-time" title="%2$s" rel="bookmark">%3$s</a>', get_permalink(), esc_attr(get_the_time()), get_the_date()); ?></span>
                        <span><i class="mini-ico-user"></i>By  <a class="author-link" href="<?php echo get_author_posts_url(get_the_author_meta('ID' )); ?>"><?php the_author_meta('display_name'); ?></a></span>
                        <?php if($comments != "yes") { ?>
                            <span><i class="mini-ico-comment"></i><?php  _e('With', 'purepress'); ?> <?php  comments_popup_link( __('no comments yet','purepress'), __('1 comment','purepress'), __('% comments','purepress'), 'comments-link', __('Comments are off for this post','purepress')); ?></span>	 <?php } ?>
                        <?php if (has_tag()) {
                            echo "<span class=\"tags\">";
                            the_tags('<i class="mini-ico-tag"></i> Tagged with: ', ',  ', ' ');
                            echo "</span>";
                        } ?>
                    </div>
                    <div class="post-description">
                        <?php the_content() ?>
                    </div>

                </div>
            </div>
            <!-- Post -->
        <?php endwhile; // End the loop. Whew.  ?>

        <?php //if($comments != "yes") { comments_template('', true); } ?>

    </div> <!-- eof eleven column -->
