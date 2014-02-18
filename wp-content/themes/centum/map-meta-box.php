<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function map_marker_add_custom_box() {

    $screens = array( 'post', 'page' );

    foreach ( $screens as $screen ) {

        add_meta_box(
            'map_marker_metabox',
            __( 'Map Options' ),
            'map_marker_inner_section',
            $screen
        );
    }
}
add_action( 'add_meta_boxes', 'map_marker_add_custom_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function map_marker_inner_section( $post ) {

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'map_marker_inner_section', 'map_marker_inner_section_nonce' );

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    $data = json_decode(get_post_meta( $post->ID, '_map_config', true ), true);

    $showMap = $data[ 'mapShow' ];
    $type = $data[ 'type' ];
    $layerId = $data[ 'layerId' ];
    $markerId = $data[ 'markerId' ];

    $layers = getLayersList();
    $markers = getMarkersList();

    ?>

    <p>
        <label for="map_marker_show"><?php _e( 'Show map in sidebar?' ); ?></label>
        <select id="map_marker_show" name="map_marker_show">
            <option value="no"<?php if($showMap == 'no') echo 'selected="selected"' ?>>No</option>
            <option value="yes"<?php if($showMap == 'yes') echo 'selected="selected"' ?>>Yes</option>
        </select>
    </p>

    <div id="map_marker_option_container" style="display: none">
    <p>
        <label for="map_marker_type"><?php _e( 'Type:' ); ?></label>
        <select id="map_marker_type" name="map_marker_type">
            <option value="layer"<?php if($type == 'layer') echo 'selected="selected"' ?>>Layer</option>
            <option value="marker"<?php if($type == 'marker') echo 'selected="selected"' ?>>Marker</option>
        </select>
    </p>

    <p id="layer_list">
        <label for="map_marker_layer_id"><?php _e( 'Layer:' ); ?></label>
        <select id="map_marker_layer_id" name="map_marker_layer_id">
            <?php foreach ($layers as $id => $name) { $selected = ($layerId == $id) ? 'selected="selected"' : '' ?>
                <option value="<?php echo $id?>" <?php echo $selected ?>><?php echo $name?></option>
            <?php } ?>
        </select>
    </p>

    <p id="marker_list">
        <label for="map_marker_marker_id"><?php _e( 'Marker:' ); ?></label>
        <select id="map_marker_marker_id" name="map_marker_marker_id">
            <?php foreach ($markers as $id => $name) { $selected = ($markerId == $id) ? 'selected="selected"' : '' ?>
                <option value="<?php echo $id?>" <?php echo $selected ?>><?php echo $name?></option>
            <?php } ?>
        </select>
    </p>

    </div>

    <script>
        jQuery(document).ready(function(){

            jQuery('#map_marker_show').change(function(){
                if (jQuery(this).val() == 'no') {
                    jQuery('#map_marker_option_container').hide();
                } else {
                    jQuery('#map_marker_option_container').slideDown();
                }
            }).trigger('change');

            jQuery('#map_marker_type').change(function(){
                if (jQuery(this).val() == 'layer') {
                    jQuery('#layer_list').show();
                    jQuery('#marker_list').hide();
                } else {
                    jQuery('#layer_list').hide();
                    jQuery('#marker_list').show();
                }
            }).trigger('change');

        });
    </script>

<?php
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function map_marker_save_postdata( $post_id ) {

    /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['map_marker_inner_section_nonce'] ) )
        return $post_id;

    $nonce = $_POST['map_marker_inner_section_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'map_marker_inner_section' ) )
        return $post_id;

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return $post_id;

    // Check the user's permissions.
    if ( 'page' == $_POST['post_type'] ) {

        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;

    } else {

        if ( ! current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }

    /* OK, its safe for us to save the data now. */

    // Sanitize user input.
    $data = json_encode(array(
        'mapShow' => sanitize_text_field( $_POST['map_marker_show'] ),
        'type' => sanitize_text_field( $_POST['map_marker_type'] ),
        'layerId' => sanitize_text_field( $_POST['map_marker_layer_id'] ),
        'markerId' => sanitize_text_field( $_POST['map_marker_marker_id'] ),
    ));

    // Update the meta field in the database.
    update_post_meta( $post_id, '_map_config', $data );
}
add_action( 'save_post', 'map_marker_save_postdata' );


function getLayersList()
{
    global $wpdb;
    $sql = "SELECT * FROM wp_leafletmapsmarker_layers WHERE id > 0";
    $result = $wpdb->get_results($sql);
    $layers = array();

    foreach ($result as $row) {
        $layers[$row->id] = $row->name;
    }

    return $layers;
}

function getMarkersList()
{
    global $wpdb;
    $sql = "SELECT * FROM wp_leafletmapsmarker_markers";
    $result = $wpdb->get_results($sql);
    $markers = array();

    foreach ($result as $row) {
        $markers[$row->id] = $row->markername;
    }

    return $markers;
}

function getLayerShortCode($layerId)
{
    return '[mapsmarker layer="'.$layerId.'"]';
}

function getMarkerLayerCode($markerId)
{
    return '[mapsmarker marker="'.$markerId.'"]';
}