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
    get_sidebar();
}
?>
    <!-- Blog Posts
        ================================================== -->
    <div class="twelve columns">
        <!-- Page Title -->

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

        <!-- Page Title / End -->
        <?php $categoryId = get_query_var('cat');
        $categoryParent = get_category($categoryId);?>
        <div class="post" style="border-bottom: none">
            <div class="item-description related">
                <?php echo apply_filters('the_content', $categoryParent->category_description);?>
            </div>

        </div>

        <?php

        $query = new WP_Query(array('post__in' => get_option('sticky_posts'), 'post_status' => 'publish', 'cat' => get_query_var('cat')));

        while ($query->have_posts()) : $query->the_post();
            $exception_id = get_the_ID();
            ?>
            <div class="post">
                <?php if (has_post_thumbnail()) {
                    ?>
                    <div class="" style="float: right; margin: 0 0 5px 5px;border: 3px solid #F4F4F4; padding: 2px">
                        <a class="" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumb');?></a>
                    </div>
                <?php } ?>
                <div class="post-title"><h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2></div>

                <div class="post-description">
                    <?php the_excerpt()?>
                </div>
                <a href="<?php the_permalink(); ?>" class="post-entry"><?php _e('Read more', 'purepress')?></a>

                <div class="clear_both"></div>
            </div>
        <?php endwhile; ?>
        <?php  wp_reset_postdata();   ?>
        <?php

        $wp_query = new WP_Query(array('posts_per_page' => get_option('posts_per_page'), 'post__not_in' => get_option('sticky_posts'), 'post_status' => 'publish', 'cat' => get_query_var('cat'), 'paged' => get_query_var('paged')));

        while ($wp_query->have_posts()) : $wp_query->the_post();

            $thumb = wp_get_attachment_image_src($wp_query->ID, 'post-thumbnail');
            ?>
            <div class="post" style="border-bottom: none;margin-bottom: 20px">
                <div class="">
                    <div style="float: left; width: auto; margin-right: 10px"><a class=""
                                                                                 href="<?php the_permalink(); ?>"><?php the_post_thumbnail('small-thumb');?></a>
                    </div>
                    <div class="post-title">
                        <h2><a href="<?php the_permalink(); ?>"><?php echo $thumb;?><?php the_title();?></a></h2>
                    </div>

                </div>
            </div>

        <?php endwhile; ?>
        <!-- Post -->
        <div class="">
            <ul class="td_paging tops_pagination">
                <?php if (function_exists('wp_pagenavi')) :
                    wp_pagenavi(array('query' => $wp_query));
                endif;
                ?>

            </ul>
            <?php  wp_reset_postdata();   ?>

        </div>
    </div>


<?php
if (ot_get_option('blog_layout') != 'left-sidebar')
    get_sidebar();


get_footer();

?>