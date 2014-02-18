<?php
add_action( 'widgets_init', 'google_map' );
function google_map() { return register_widget('google_maps'); }


class google_maps extends WP_Widget {

    /** constructor */
    function google_maps() {
          $widget_ops = array('description' => __( 'Google map for sidebar') );
        parent::WP_Widget( 'google_maps', $name = 'Google map', $widget_ops );
    }
    function widget($args, $instance) {
        extract($args);

        echo $before_widget;
        ?>
        <div class="widget">
            <?php
            wp_reset_postdata();
            global $wpdb, $post;

            $post_id=$post->ID;
            $query = "select * from wp_mappress_posts where postid = $post_id";
            $result = $wpdb->get_results($query);
            $mapid=$result[0]->mapid;

            echo do_shortcode('[mappress mapid='.$mapid.']')?>
        </div>
  <?php
        echo $after_widget;
    }

}

?>