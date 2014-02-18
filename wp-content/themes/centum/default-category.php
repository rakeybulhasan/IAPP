<?php
/**
 * The main template file.
 * @package WordPress
 */

get_header(); ?>


    <!-- 960 Container -->
    <div class="container">

        <div class="sixteen columns">

        </div>
    </div>
    <!-- 960 Container / End -->

    <!-- 960 Container -->
<div class="container">
<?php
if (ot_get_option('blog_layout') == 'left-sidebar') {
    get_sidebar('category');
}
?>
    <!-- Blog Posts
        ================================================== -->
    <div class="twelve columns">


        <div id="page-title">
            <?php $bredcrumbs = ot_get_option('centum_breadcrumbs'); ?>
            <h1 <?php if ($bredcrumbs == 'yes') echo 'class="has-breadcrumbs"';?>>
                <?php
                global $post;
                printf('' . single_cat_title('', false) . ''); ?>
                <?php $subtitle = get_post_meta($post->ID, 'incr_subtitle', true);
                if ($subtitle) {
                    echo ' <span>/ ' . $subtitle . '</span>';
                } ?>
            </h1>

            <?php if (ot_get_option('centum_breadcrumbs') == 'yes') echo dimox_breadcrumbs();?>
            <div id="bolded-line"></div>

        </div>
        <!-- Page Title / End -->

        <!-- Post -->
        <?php
        $wp_query = new WP_Query(array('paged' => get_query_var('paged'), 'post_status' => 'publish', 'cat' => get_query_var('cat'), 'posts_per_page' => get_option('posts_per_page')));
        ?>

        <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

            <div class="cat_landing" id="post-<?php the_ID(); ?>">

                <?php
                if ((function_exists('get_post_format') && 'video' == get_post_format($post->ID))) {
                    $videoembed = get_post_meta($post->ID, 'incr_video_embed', true);
                    if ($videoembed) {
                        echo '<div class="embed video-cont">' . $videoembed . '</div>';
                    } else {
                        global $wp_embed;
                        $videolink = get_post_meta($post->ID, 'incr_video_link', true);

                        $post_embed = $wp_embed->run_shortcode('[embed  width="600" height="360"]' . $videolink . '[/embed]');
                        echo '<div class="embed video-cont">' . $post_embed . '</div>';
                    }
                }


                if ((function_exists('get_post_format') && 'gallery' == get_post_format($post->ID))) {
                    $ids = get_post_meta($post->ID, 'pp_gallery_slider', TRUE);
                    $args = array(
                        'post_type' => 'attachment',
                        'post_status' => 'inherit',
                        'post_mime_type' => 'image',
                        'post__in' => explode(",", $ids),
                        'posts_per_page' => '-1',
                        'orderby' => 'post__in'
                    );

                    $images_array = get_posts($args);

                    // continued from above ...
                    if ($images_array) {
                        ?>
                        <div class="flexslider subpage">
                            <ul class="slides">
                                <?php foreach ($images_array as $image) {
                                    $attachment = wp_get_attachment_image_src($image->ID, 'large');
                                    $thumb = wp_get_attachment_image_src($image->ID, 'post-thumbnail');
                                    ?>
                                    <li>
                                        <div class="picture">
                                            <a href="<?php echo $attachment[0] ?>" rel="image-gallery"
                                               title="<?php echo $image->post_title; ?>">
                                                <img src="<?php echo $thumb[0] ?>"
                                                     alt="<?php echo $image->post_title; ?>"/>

                                                <div class="image-overlay-zoom"></div>
                                            </a>
                                        </div>
                                    </li>

                                <?php } ?>
                            </ul>
                        </div>
                    <?php
                    }
                } ?>

                <?php if (function_exists('get_post_format')) { ?>
                    <a class="post-icon standard"
                       href="<?php the_permalink(); ?>"><?php the_post_thumbnail('category-thumb'); ?> </a>
                <?php } else { ?>
                    <a class="post-icon standard"
                       href="<?php the_permalink(); ?>"><?php the_post_thumbnail('category-thumb'); ?> </a>
                <?php } ?>
                <div class="post-content">
                    <div class="post-title">
                        <h2>
                            <a href="<?php the_permalink(); ?>"
                               title="<?php printf(esc_attr__('Permalink to %s', 'purepress'), the_title_attribute('echo=0')); ?>"
                               rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                    </div>


                    <div class="post-description">
                        <?php the_excerpt() ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="post-entry"><?php  _e('Read more', 'purepress'); ?></a>
                </div>
            </div>
            <!-- Post -->
        <?php endwhile; // End the loop. Whew.  ?>


        <div class="">
            <ul class="td_paging tops_pagination">
                <?php if (function_exists('wp_pagenavi')) :
                    wp_pagenavi(array('query' => $wp_query));
                endif;
                ?>

            </ul>
            <?php  wp_reset_postdata();   ?>

        </div>

    </div> <!-- eof eleven column -->


<?php
if (ot_get_option('blog_layout') != 'left-sidebar')
    get_sidebar();


get_footer();

?>