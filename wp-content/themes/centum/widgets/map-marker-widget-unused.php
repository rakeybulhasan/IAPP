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
        $title = apply_filters( 'widget_title', $instance['title'] );

        $type = $instance['type'];
        $markerId = $instance['markerId'];
        $layerId = $instance['layerId'];

        var_dump($type, $markerId, $layerId);

        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];

        if ($type == 'layer') {
            $shortCode = $this->getLayerShortCode($layerId);
        } elseif ($type == 'marker') {
            $shortCode = $this->getMarkerLayerCode($markerId);
        }

        if ($shortCode) {
            echo apply_filters("the_content", $shortCode);
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }

        $type = $instance[ 'type' ];
        $layerId = $instance[ 'layerId' ];
        $markerId = $instance[ 'markerId' ];
        $postId = $instance[ 'postId' ];

        $layers = $this->getLayersList();
        $markers = $this->getMarkersList();

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type:' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
                <option value="layer"<?php if($type == 'layer') echo 'selected="selected"' ?>>Layer</option>
                <option value="marker"<?php if($type == 'marker') echo 'selected="selected"' ?>>Marker</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'layerId' ); ?>"><?php _e( 'Layer:' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'layerId' ); ?>" name="<?php echo $this->get_field_name( 'layerId' ); ?>">
                <?php foreach ($layers as $id => $name) { $selected = ($layerId == $id) ? 'selected="selected"' : '' ?>
                    <option value="<?php echo $id?>" <?php echo $selected ?>><?php echo $name?></option>
                <?php } ?>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'markerId' ); ?>"><?php _e( 'Layer:' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'markerId' ); ?>" name="<?php echo $this->get_field_name( 'markerId' ); ?>">
                <?php foreach ($markers as $id => $name) { $selected = ($markerId == $id) ? 'selected="selected"' : '' ?>
                    <option value="<?php echo $id?>" <?php echo $selected ?>><?php echo $name?></option>
                <?php } ?>
            </select>
        </p>

    <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        $instance['type'] = ( ! empty( $new_instance['type'] ) ) ? strip_tags( $new_instance['type'] ) : '';
        $instance['layerId'] = ( ! empty( $new_instance['layerId'] ) ) ? strip_tags( $new_instance['layerId'] ) : '';
        $instance['markerId'] = ( ! empty( $new_instance['markerId'] ) ) ? strip_tags( $new_instance['markerId'] ) : '';

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