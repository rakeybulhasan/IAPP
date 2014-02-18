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

            <?php

            $itisme= get_pages($ID);
            //$lineage=$itisme->post_name;
            //$parentID=$itisme->post_parent;
            ?>
            </div>
        <?php while (have_posts()) : the_post(); ?>
            <?php
            $galleryTerms = get_terms("gallery_category");
            $args = array(
                'post_type' => 'photo_gallery',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'gallery_category',
                        'field' => 'slug',
                        'terms' => $term->slug,
                    )
                )
            );

            setup_postdata($post);

            $galleryImages = get_group('gallery');

            foreach ($galleryImages as $key => $galleryImage) {
                $imageUrl = $galleryImage['gallery_photo'][1]['original'];
                $imageThumb = get_image('gallery_photo',$key,1,1,NULL,NULL,NULL,'thumb');
                $caption = $galleryImage['gallery_title'][1];
                ?>
                <div class="three columns portfolio-item interior-design architecture real-estate" style="width: 155px">

                    <div class="picture">
                        <a href="<?php echo $imageUrl?>" rel="image" title="<?php echo $caption; ?>">
                            <?php echo $imageThumb?>
                            <div class="image-overlay-zoom"></div>
                        </a>
                    </div>

                    <div class="item-description alt">
                        <h5><?php echo $caption;?></h5>

                        <p></p>
                    </div>

                </div>

            <?php
            }
            wp_reset_postdata();
            ?>
        <?php endwhile; // End the loop. Whew.  ?>
        <?php //} ?>
    </div>
