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
        <?php $categoryId = get_query_var('cat');
        $categoryParent = get_category($categoryId);?>
        <div class="twelve columns">
            <div class="item-description related">
                <p><?php echo $categoryParent->category_description;?></p>
            </div>
            <br>
            <br>
        </div>

        <?php
        $categoryId = get_query_var('cat');
        $categories = get_categories(array('parent' => $categoryId, 'hide_empty' => 0));
        $count = count($categories);
        foreach ($categories as $category) {
            $parent_id = $category->term_id;
            $title = $category->cat_name;
            $description = $category->category_description;
            ?>
            <div class="<?php if ($count == 2) {
                echo 'two_category';
            } elseif ($count == 1) {
                echo 'two_category';
            } elseif ($count == 3) {
                echo 'three_category';
            } elseif ($count == 4) {
                echo 'four_category';
            } elseif ($count == 5) {
                echo 'five_category';
            } else {
                echo 'five_category';
            }?> columns">
                <div class="picture"><a href="<?php echo get_category_link($category->term_id) ?>">

                        <img src="<?php if ($count == 1) {
                            echo z_taxonomy_image_url($parent_id, 'medium');
                        } elseif ($count == 2) {
                            echo z_taxonomy_image_url($parent_id, 'medium');
                        } elseif ($count == 3) {
                            echo z_taxonomy_image_url($parent_id, 'medium');
                        } elseif ($count == 4) {
                            echo z_taxonomy_image_url($parent_id, 'thumb');
                        } elseif ($count == 5) {
                            echo z_taxonomy_image_url($parent_id, 'category-thumb');
                        } else {
                            echo z_taxonomy_image_url($parent_id, 'category-thumb');
                        }?>"/>

                        <div class=""></div>
                </div>
                <div class="item-description related" style="min-height: 140px">
                    <h5><a href="<?php echo get_category_link($parent_id) ?>"> <?php echo $title; ?></a></h5>

                    <p>
                        <?php if ($count == 2) {
                            $text_limit = 150;
                        } elseif ($count == 1) {
                            $text_limit = 150;
                        } elseif ($count == 3) {
                            $text_limit = 120;
                        } elseif ($count == 4) {
                            $text_limit = 60;
                        } elseif ($count == 5) {
                            $text_limit = 60;
                        } else {
                            $text_limit = 60;
                        }?>
                        <?php $excerpt = character_limiter($description, $text_limit);

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
        <?php }?>
    </div> <!-- eof eleven column -->
<?php
if (ot_get_option('blog_layout') != 'left-sidebar')
    get_sidebar();

get_footer();

?>