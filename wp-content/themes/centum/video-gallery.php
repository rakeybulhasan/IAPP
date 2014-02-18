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

    <!-- 960 Container -->
    <div class="twelve columns <?php if($sidebar_side == "left-sidebar") { echo "left-sb"; } ?>">
        <div id="page-title">
            <?php $bredcrumbs = ot_get_option('centum_breadcrumbs'); ?>
            <h1 <?php if($bredcrumbs == 'yes') echo 'class="has-breadcrumbs"';?>>
                <?php the_title(); ?>
                <?php $subtitle  = get_post_meta($post->ID, 'incr_subtitle', true);
                if ( $subtitle) {
                    echo ' <span> '.$subtitle.'</span>';
                } ?>
            </h1>

            <?php if(ot_get_option('centum_breadcrumbs') == 'yes') echo dimox_breadcrumbs() ;?>
            <div id="bolded-line"></div>
        </div>

        <?php

        $galleryTerms = get_terms("video_categories");
       //var_dump($galleryTerms);
        foreach ($galleryTerms as $term) { ?>

            <div id="portfolio-wrapperd">

                <div class="twelve columns">
                    <div class="headline low-margin">
                        <h4><?php echo $term->name?></h4>
                    </div>
                </div><br>
                <?php
                $args = array(
                    'post_type' => 'video_gallery',
                    'post_status'      => 'publish',
                    'posts_per_page'   => 2,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'video_categories',
                            'field' => 'slug',
                            'terms' => $term->slug,
                        )
                    )
                );
                $x = 1;
                $posts = get_posts($args);
                foreach ($posts as $post) {
                    setup_postdata($post);

                    $galleryImages = get_group('gallery');
                    $galleryImage = $galleryImages[1];

                    ?>
                    <div class="five columns portfolio-item interior-design architecture real-estate" style="width: 329px;">

                        <div class="picture">
                            <a href="<?php the_permalink() ?>" rel="" title="<?php the_title() ?>">
                            <?php the_content(); ?>
                        </div>

                        <div class="item-description alt">
                            <h5><?php the_title() ?></h5> </a>

                        </div>

                    </div>

                <?php
                    $x++;
                }
                wp_reset_postdata();

                ?>

                <?php if($term->count > 2){ ?>
                <div class="twelve columns" style="text-align: right">
                    <a href="<?php echo get_term_link($term->slug,'video_categories') ?>" class="post-entry"><?php _e('Read more', 'purepress')?></a>
                </div>
                <?php }?>

            </div>
        <?php } ?>


    </div>
    <!-- End 960 Container -->
