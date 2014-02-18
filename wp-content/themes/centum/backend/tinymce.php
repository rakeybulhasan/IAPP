<?php

add_action('init', 'pptinymce_buttons');
function pptinymce_buttons() {

	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
		return;
	}

	if ( get_user_option('rich_editing') == 'true' ) {
		add_filter( 'mce_external_plugins', 'add_plugin' );
		add_filter( 'mce_buttons_3', 'register_button' );
	}
}
/**
Register Button
*/

function register_button( $buttons ) {
	array_push( $buttons, "headline","ppcolumns","ppbutton", "pptabs", "ppaccordion", "pptoggle", "ppboxes", "ppnotice", "feature",  "list", "ppslideshow",'ppsocial','columns' );
	return $buttons;
}
/**
Register TinyMCE Plugin
*/

function add_plugin( $plugin_array ) {
	$plugin_array['purethemesmce'] = get_template_directory_uri() . '/backend/tinymcebuttons.js';
	return $plugin_array;
}
?>