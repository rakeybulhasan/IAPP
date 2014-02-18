<?php

function register_map_marker_widget() {
    register_widget( 'MapMarker_Widget' );
}

add_action( 'widgets_init', 'register_map_marker_widget' );

class MapMarker_Widget extends WP_Widget {

    const MAP_LAYER_TABLE = 'wp_leafletmapsmarker_layers';
    const MAP_MARKER_TABLE = 'wp_leafletmapsmarker_markers';

    function __construct() {

        parent::__construct(
            'map_marker_widget', // Base ID
            __('Map Marker Widget', 'text_domain'), // Name
            array( 'description' => __( 'Custom Widget for map marker', 'text_domain' ), ) // Args
        );
    }

    public function widget( $args, $instance ) {

        extract($args);

        wp_reset_postdata();

        global $post;

        $data = json_decode(get_post_meta( $post->ID, '_map_config', true ), true);
        $showMap = $data[ 'mapShow' ];
        $type = $data[ 'type' ];
        $layerId = $data[ 'layerId' ];
        $markerId = $data[ 'markerId' ];

        if ($showMap == 'no') {
            return true;
        }

        if ($type == 'layer') {
            $shortCode = $this->getLayerShortCode($layerId);
        } elseif ($type == 'marker') {
            $shortCode = $this->getMarkerLayerCode($markerId);
        }
        echo $before_widge;
        if ($shortCode) {
            echo '<div class="widget">'.apply_filters("the_content", $shortCode).'</div>';
        }
   echo $after_widget;
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

    <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();

        return $instance;
    }

    private function getLayersList()
    {
        global $wpdb;
        $sql = "SELECT * FROM ".$this::MAP_LAYER_TABLE." WHERE id > 0";
        $result = $wpdb->get_results($sql);
        $layers = array();

        foreach ($result as $row) {
            $layers[$row->id] = $row->name;
        }

        return $layers;
    }

    private function getMarkersList()
    {
        global $wpdb;
        $sql = "SELECT * FROM ".$this::MAP_MARKER_TABLE." WHERE id > 0";
        $result = $wpdb->get_results($sql);
        $markers = array();

        foreach ($result as $row) {
            $markers[$row->id] = $row->markername;
        }

        return $markers;
    }

    private function getLayerShortCode($layerId)
    {
        return '[mapsmarker layer="'.$layerId.'"]';
    }

    private function getMarkerLayerCode($markerId)
    {
        return '[mapsmarker marker="'.$markerId.'"]';
    }

}