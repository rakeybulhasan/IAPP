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
<div class="container" style="margin-top: 30px;">

    <?php get_sidebar();?>
    <!-- Standard Structure -->
    <div class="twelve columns <?php if ($sidebar_side == "left-sidebar") {
        echo "left-sb";
    } ?>">
        <div class="twelve columns boleted_list">

            <?php
            global $wpdb;
            global $post;
            $options = get_option('ecb_theme_options');

            $cat_id = $options['welcome-message'];

            $wp_query = new WP_Query(array('orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => '1', 'post_status' => 'publish', 'cat' => $cat_id));

            while ($wp_query->have_posts()) : $wp_query->the_post();

                the_content(); ?>

            <?php endwhile; ?>


        </div>


        <div class="twelve columns">
            <!-- Headline -->
            <div class="headline" style="margin-top: 5px;"><h3><?php _e('Project Components', 'purepress')?></h3></div>
        </div>
        <div class="twelve columns">
            <?php
            global $wpdb;
            global $post;
            $options = get_option('ecb_theme_options');

            $cat_id = $options['component'];
            $cat_id_icl = icl_object_id($cat_id, 'category', false, ICL_LANGUAGE_CODE);
            $order = $options['category_sorting'];
            $orderby = array_filter(array_map('trim', explode(PHP_EOL, $order)));

            if (ICL_LANGUAGE_CODE != 'en') {
                $orderby = translate_category_slugs($orderby);
            }

            $args = array(
                'type' => 'post',
                'child_of' => '',
                'parent' => $cat_id_icl,
                'orderby' => '',
                'order' => 'asc',
                'hide_empty' => 0,
                'taxonomy' => 'category'

            );

            $parent_categories = get_categories($args);

            $parent_categories = sort_category_as($parent_categories, $orderby);
            foreach ($parent_categories as $category) {
                $parent_id = $category->term_id;

                $title = $category->cat_name;
                $description = $category->category_description;
                ?>
                <?php if (!empty($parent_id)) { ?>
                    <div class="three columns">
                        <div class="picture"><img style="height: 120px!important;"
                                                  src="<?php echo z_taxonomy_image_url($parent_id, 'category-thumb'); ?>"/>

                            <div class="image-overlay-link child_category_list">

                                <ul class="check_list child_menu">
                                    <?php  $child_categories = get_categories(array('hide_empty' => 0, 'parent' => $parent_id));
                                    foreach ($child_categories as $child_category) {
                                        ?>
                                        <li><a href="<?php echo get_category_link($child_category->term_id) ?>"
                                               style="color: #ffffff"> <?php echo $child_category->cat_name;?></a></li>
                                    <?php
                                    }
                                    ?>

                                </ul>
                            </div>
                        </div>
                        <div class="item-description related term_excerpt">
                            <h5><a href="<?php echo get_category_link($parent_id) ?>"> <?php echo $title; ?></a></h5>

                            <p>

                                <?php $excerpt = character_limiter($description, 60);

                                echo $excerpt;
                                ?>
                            </p>

                        </div>
                        <div>
                            <?php  if ((substr($excerpt, -7)) == '&#8230;') { ?>

                                <a href="<?php echo get_category_link($parent_id) ?>"
                                   class="post-entry"><?php _e('Read more', 'purepress')?></a>
                            <?php }?>
                        </div>
                    </div>
                <?php
                }

            }

            ?>

        </div>

    </div>
</div>