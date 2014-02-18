<?php
add_action( 'widgets_init', 'notice' );
function notice() { return register_widget('notices'); }


class notices extends WP_Widget {
    /** constructor */
    function notices() {
          $widget_ops = array('description' => __( 'News and Notice list for sidebar') );
        parent::WP_Widget( 'notices', $name = 'News and Notice', $widget_ops );
    }
    function widget($args, $instance) {
        extract($args);

        echo $before_widget;
        ?>

            <div class="headline no-margin"><h4>News and Notice</h4></div>


                <!-- Large Notice -->
                <div style="padding: 10px" class="large-notice">

                    <?php

                    $category_name= 'news-and-notice';
                    $wp_query = new WP_Query(array('orderby' => 'date', 'order' => 'DESC', 'posts_per_page' => '1', 'post_status' => 'publish', 'category_name' => $category_name));

                    while ( $wp_query->have_posts() ) : $wp_query->the_post();

                        ?>
               <div class="" ><h2><a href="<?php the_permalink(); ?>"><?php the_excerpt();?></a></h2></div>

                    <?php endwhile; ?>
      </div>


  <?php
        echo $after_widget;
    }

}

?>