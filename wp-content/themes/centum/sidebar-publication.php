<div class="four columns">
	<div class="blog-sidebar">
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Language Selector Sidebar')) : endif; ?>
		<?php

		$sidebar = get_post_meta($post->ID, "incr_sidebar_set", $single = true);
		if ($sidebar) {
			if (!function_exists('dynamic_sidebar') || !dynamic_sidebar($sidebar)) :
				?>
			<?php
			endif;
		}?>

		<?php
		if (!$sidebar) {
			if (!dynamic_sidebar('sidebar')) :
				?>
			<?php endif;
		    } // end primary widget area   
		    ?>
		</div>
    <?php
    global $post;
    wp_reset_postdata();

    $cats =  get_query_var('cat');
    $args = array(
        'orderby'=> 'name',
        'parent' => $cats,
        'hide_empty'    => 0
    );
    $categories = get_categories($args);

    ?>


    <div class="widget">
        <div class="headline no-margin"><h4><?php _e('Categories', 'purepress')?></h4></div>
        <ul class="links-list-alt">

            <?php foreach ($categories as $category) { ?>
                <li>
                    <a href="<?php echo get_category_link($category->term_id) ?>" rel="" target="">
                        <?php echo $category->name;?>
                    </a>
                </li>
            <?php
            }
            ?>
        </ul>

    </div>
	</div>

