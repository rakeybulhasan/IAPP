<?php
add_action( 'widgets_init', 'important_link' );
function important_link() { return register_widget('important_links'); }


class important_links extends WP_Widget {
    /** constructor */
    function important_links() {
          $widget_ops = array('description' => __( 'Important Link list for sidebar') );
        parent::WP_Widget( 'important_links', $name = 'Important Link', $widget_ops );
    }
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
        $shownum = apply_filters('widget_text', $instance['show-num'], $instance);
        echo $before_widget;
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        }
        ?>

        <ul class="links-list-alt">
            <?php  $important_link = get_terms("important_link");
            foreach ($important_link as $term) { ?>
                <?php
                $args = array(
                    'post_type' => 'important_link',
                    'posts_per_page' => $shownum,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'important_link',
                            'field' => 'slug',
                            'terms' => $term->slug,
                        )
                    )
                );
                $posts = get_posts($args);
                $i=1;
                foreach ($posts as $post) {
                    setup_postdata($post);

                    ?>

                <li><a href="<?php echo get_post_meta($post->ID, "improtant_link_url_improtant_link_url", true);?>" target="_blank">
                        <?php echo get_the_title($post->ID);?>
                    </a>
                </li>



                    <?php
                    $i++;
                }
                wp_reset_postdata();
                ?>

            <?php } ?>

        </ul>
  <?php
        echo $after_widget;
    }


    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['show-num'] = strip_tags($new_instance['show-num']);
        $instance['icl_language'] = $new_instance['icl_language'];
        return $instance;
    }


    function form($instance) {
        $instance = wp_parse_args((array) $instance, array(
            'title' => '',
            'show-num' => '',
            'icl_language' => 'multilingual',
            'icl_converted_from' => -1));
        $title = strip_tags($instance['title']);
        $shownum = esc_textarea($instance['show-num']);
        $language = $instance['icl_language'];

        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <p>
            <label><?php _e('Number of posts to show'); ?>:
                <input type="text" size="3" value="<?php echo esc_attr($shownum); ?>" name="<?php echo $this->get_field_name('show-num'); ?>" id="<?php echo $this->get_field_id('show-num'); ?>"><br>
            </label>
        </p>
        <?php
    }
}

?>