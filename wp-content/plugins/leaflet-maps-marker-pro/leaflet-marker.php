<?php
/*
    Edit marker - Leaflet Maps Marker Plugin
*/
//info prevent file from being accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) == 'leaflet-marker.php') { die ("Please do not access this file directly. Thanks!<br/><a href='http://www.mapsmarker.com/go'>www.mapsmarker.com</a>"); }
?>
<div class="wrap">
<?php 
include('inc' . DIRECTORY_SEPARATOR . 'admin-header.php'); 
global $wpdb, $current_user;
$lmm_options = get_option( 'leafletmapsmarker_options' );

//info: set custom marker icon dir/url
if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
	$defaults_marker_icon_dir = LEAFLET_PLUGIN_ICONS_DIR;
	$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
} else {
	$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
	$defaults_marker_icon_url = htmlspecialchars($lmm_options['defaults_marker_icon_url']);
}
//info: set marker shadow url
if ( $lmm_options['defaults_marker_icon_shadow_url_status'] == 'default' ) {
	if ( $lmm_options['defaults_marker_icon_shadow_url'] == NULL ) {
		$marker_shadow_url = '';
	} else {
		$marker_shadow_url = LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker-shadow.png';
	}
} else {
	$marker_shadow_url = htmlspecialchars($lmm_options['defaults_marker_icon_shadow_url']);
}
$current_editor = get_option( 'leafletmapsmarker_editor' );
$new_editor = isset($_GET['new_editor']) ? $_GET['new_editor'] : '';
$current_editor_css = ($current_editor == 'simplified') ? 'display:none;' : '';
//info: workaround - select shortcode on input focus doesnt work on iOS
global $wp_version;
if ( version_compare( $wp_version, '3.4', '>=' ) ) {
	 $is_ios = wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] );
	 $shortcode_select = ( $is_ios ) ? '' : 'onfocus="this.select();" readonly="readonly"';
} else {
	 $shortcode_select = '';
}
//info: check gpx url for validity
function lmm_isValidURL( $url ) {
	if (preg_match("|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i", $url)) {
		return true;
	} else {
		return false;
	}
}
$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');
$addtoLayer = isset($_GET['addtoLayer']) ? intval($_GET['addtoLayer']) : (isset($_POST['layer']) ? intval($_POST['layer']) : '');
$layername = isset($_GET['Layername']) ? stripslashes($_GET['Layername']) : '';
$layer_show_button = ($addtoLayer != NULL && $addtoLayer != 0) ? "<a class='button-secondary lmm-nav-secondary' href=" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=" . $addtoLayer .">" . __('edit assigned layer','lmm') . "</a>&nbsp;&nbsp;&nbsp;" : "";
$oid = isset($_POST['id']) ? intval($_POST['id']) : (isset($_GET['id']) ? intval($_GET['id']) : '');
$lat_check = isset($_POST['lat']) ? $_POST['lat'] : (isset($_GET['lat']) ? $_GET['lat'] : '');
$lon_check = isset($_POST['lon']) ? $_POST['lon'] : (isset($_GET['lon']) ? $_GET['lon'] : '');
$markerid = isset($_GET['markerid']) ? $_GET['markerid'] : ''; //info: for switcheditor-js-forward
//info: functions for capability checks
function lmm_check_capability_edit($createdby) {
	global $current_user;
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	if ( current_user_can( $lmm_options[ 'capabilities_edit_others' ]) ) {
		return true;
	}
	if ( current_user_can( $lmm_options[ 'capabilities_edit' ]) && ( $current_user->user_login == $createdby) ) {
		return true;
	}
	return false;
}
function lmm_check_capability_delete($createdby) {
	global $current_user;
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	if ( current_user_can( $lmm_options[ 'capabilities_delete_others' ]) ) {
		return true;
	}
	if ( current_user_can( $lmm_options[ 'capabilities_delete' ]) && ( $current_user->user_login == $createdby) ) {
		return true;
	}
	return false;
}

if (!empty($action)) {
	$markernonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : (isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '');
	if (! wp_verify_nonce($markernonce, 'marker-nonce') ) die('<br/>'.__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').'');
	$layer = isset($_POST['layer']) ? intval($_POST['layer']) : 0;
	if ($action == 'add') {
		if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
			global $current_user;
			//info: set values for wms checkboxes status
			$wms_checkbox = isset($_POST['wms']) ? '1' : '0';
			$wms2_checkbox = isset($_POST['wms2']) ? '1' : '0';
			$wms3_checkbox = isset($_POST['wms3']) ? '1' : '0';
			$wms4_checkbox = isset($_POST['wms4']) ? '1' : '0';
			$wms5_checkbox = isset($_POST['wms5']) ? '1' : '0';
			$wms6_checkbox = isset($_POST['wms6']) ? '1' : '0';
			$wms7_checkbox = isset($_POST['wms7']) ? '1' : '0';
			$wms8_checkbox = isset($_POST['wms8']) ? '1' : '0';
			$wms9_checkbox = isset($_POST['wms9']) ? '1' : '0';
			$wms10_checkbox = isset($_POST['wms10']) ? '1' : '0';
			$openpopup_checkbox = isset($_POST['openpopup']) ? '1' : '0';
			$panel_checkbox = isset($_POST['panel']) ? '1' : '0';
			$markername_quotes = str_replace("\\","/", str_replace("\"", "'", $_POST['markername'])); //info: backslash breaks geojson
			$popuptext = preg_replace("/\t/", " ", $_POST['popuptext']); //info: tabs break geojson
			$address = str_replace("\\","/", preg_replace("/\t/", " ", $_POST['address'])); //info: tabs break geojson
			$gpx_panel_checkbox = isset($_POST['gpx_panel']) ? '1' : '0';
			if ($_POST['kml_timestamp'] == NULL) {
				$result = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, updatedby, updatedon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, address, gpx_url, gpx_panel) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', $_POST['lat']), str_replace(',', '.', $_POST['lon']), $_POST['icon'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $address, trim($_POST['gpx_url']), $gpx_panel_checkbox );
			} else if ($_POST['kml_timestamp'] != NULL) {
				$result = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, updatedby, updatedon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, kml_timestamp, address, gpx_url, gpx_panel) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', $_POST['lat']), str_replace(',', '.', $_POST['lon']), $_POST['icon'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $_POST['kml_timestamp'], $address, trim($_POST['gpx_url']), $gpx_panel_checkbox );
			}
			$wpdb->query( $result );
			$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
			echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $wpdb->insert_id . '&status=published"; </script> ';
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
		}
	} elseif ($action == 'edit') {
		$createdby_check = $wpdb->get_var( 'SELECT createdby FROM '.$table_name_markers.' WHERE id='.$oid );
		if (lmm_check_capability_edit($createdby_check) == TRUE) {
			if ( ($lat_check != NULL) && ($lon_check != NULL) ) {
				global $current_user;
				//info: set values for wms checkboxes status
				$wms_checkbox = isset($_POST['wms']) ? '1' : '0';
				$wms2_checkbox = isset($_POST['wms2']) ? '1' : '0';
				$wms3_checkbox = isset($_POST['wms3']) ? '1' : '0';
				$wms4_checkbox = isset($_POST['wms4']) ? '1' : '0';
				$wms5_checkbox = isset($_POST['wms5']) ? '1' : '0';
				$wms6_checkbox = isset($_POST['wms6']) ? '1' : '0';
				$wms7_checkbox = isset($_POST['wms7']) ? '1' : '0';
				$wms8_checkbox = isset($_POST['wms8']) ? '1' : '0';
				$wms9_checkbox = isset($_POST['wms9']) ? '1' : '0';
				$wms10_checkbox = isset($_POST['wms10']) ? '1' : '0';
				$openpopup_checkbox = isset($_POST['openpopup']) ? '1' : '0';
				$panel_checkbox = isset($_POST['panel']) ? '1' : '0';
				$markername_quotes = str_replace("\\","/", str_replace("\"", "'", $_POST['markername'])); //info: backslash breaks geojson
				$popuptext = preg_replace("/\t/", " ", $_POST['popuptext']); //info: tabs break geojson
				$address = str_replace("\\","/", preg_replace("/\t/", " ", $_POST['address'])); //info: tabs break geojson
				$gpx_panel_checkbox = isset($_POST['gpx_panel']) ? '1' : '0';
				if ($_POST['kml_timestamp'] == NULL) {
					$result = $wpdb->prepare( "UPDATE $table_name_markers SET markername = %s, basemap = %s, layer = %d, lat = %s, lon = %s, icon = %s, popuptext = %s, zoom = %d, openpopup = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %s, overlays_custom2 = %s, overlays_custom3 = %s, overlays_custom4 = %s, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d, address = %s, gpx_url = %s, gpx_panel = %d WHERE id = %d", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', $_POST['lat']), str_replace(',', '.', $_POST['lon']), $_POST['icon'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $address, trim($_POST['gpx_url']), $gpx_panel_checkbox, $oid );
				} else if ($_POST['kml_timestamp'] != NULL) {
					$result = $wpdb->prepare( "UPDATE $table_name_markers SET markername = %s, basemap = %s, layer = %d, lat = %s, lon = %s, icon = %s, popuptext = %s, zoom = %d, openpopup = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %s, overlays_custom2 = %s, overlays_custom3 = %s, overlays_custom4 = %s, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d, kml_timestamp = %s, address = %s, gpx_url = %s, gpx_panel = %d WHERE id = %d", $markername_quotes, $_POST['basemap'], $layer, str_replace(',', '.', $_POST['lat']), str_replace(',', '.', $_POST['lon']), $_POST['icon'], $popuptext, $_POST['zoom'], $openpopup_checkbox, $_POST['mapwidth'], $_POST['mapwidthunit'], $_POST['mapheight'], $panel_checkbox, $current_user->user_login, current_time('mysql',0), $_POST['controlbox'], $_POST['overlays_custom'], $_POST['overlays_custom2'], $_POST['overlays_custom3'], $_POST['overlays_custom4'], $wms_checkbox, $wms2_checkbox, $wms3_checkbox, $wms4_checkbox, $wms5_checkbox, $wms6_checkbox, $wms7_checkbox, $wms8_checkbox, $wms9_checkbox, $wms10_checkbox, $_POST['kml_timestamp'], $address, trim($_POST['gpx_url']), $gpx_panel_checkbox, $oid );
				}
				$wpdb->query( $result );
				$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
				echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . intval($_POST['id']) . '&status=updated"; </script> ';
			} else {
				echo '<p><div class="error" style="padding:10px;">' . __('Error: coordinates cannot be empty!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
			}
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Error: your user does not have the permission to edit markers from other users!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
		}
	} elseif ($action == 'delete') {
		$createdby_check = $wpdb->get_var( 'SELECT createdby FROM '.$table_name_markers.' WHERE id='.$oid );
		if (lmm_check_capability_edit($createdby_check) == TRUE) {
			if (!empty($oid)) {
				$result = $wpdb->prepare( "DELETE FROM $table_name_markers WHERE id = %d", $oid );
				$wpdb->query( $result );
				$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
				//info: delete qr code cache image
				if ( file_exists(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $oid . '.png') ) {
					unlink(LEAFLET_PLUGIN_QR_DIR . DIRECTORY_SEPARATOR . 'marker-' . $oid . '.png');
				}
				echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully deleted','lmm') . '</div><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('list all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
			}
		} else {
			echo '<p><div class="error" style="padding:10px;">' . __('Error: your user does not have the permission to delete markers from other users!','lmm') . '</div><br/><a href="javascript:history.back();" class=\'button-secondary lmm-nav-secondary\' >' . __('Go back to form','lmm') . '</a></p>';
		}
	} elseif ($action == 'duplicate') {	
		global $current_user;	
		$result = $wpdb->get_row( $wpdb->prepare('SELECT * FROM '.$table_name_markers.' WHERE id= %d',$oid), ARRAY_A);
		if ($result['kml_timestamp'] == NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, updatedby, updatedon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, address, gpx_url, gpx_panel) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $result['markername'], $result['basemap'], $result['layer'], $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['address'], $result['gpx_url'], $result['gpx_panel'] );
		} else if ($result['kml_timestamp'] != NULL) {
			$sql_duplicate = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, updatedby, updatedon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, kml_timestamp, address, gpx_url, gpx_panel) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $result['markername'], $result['basemap'], $result['layer'], $result['lat'], $result['lon'], $result['icon'], $result['popuptext'], $result['zoom'], $result['openpopup'], $result['mapwidth'], $result['mapwidthunit'], $result['mapheight'], $result['panel'], $current_user->user_login, current_time('mysql',0), $current_user->user_login, current_time('mysql',0), $result['controlbox'], $result['overlays_custom'], $result['overlays_custom2'], $result['overlays_custom3'], $result['overlays_custom4'], $result['wms'], $result['wms2'], $result['wms3'], $result['wms4'], $result['wms5'], $result['wms6'], $result['wms7'], $result['wms8'], $result['wms9'], $result['wms10'], $result['kml_timestamp'], $result['address'], $result['gpx_url'], $result['gpx_panel'] );
		}
		$wpdb->query( $sql_duplicate );
		$wpdb->query( "OPTIMIZE TABLE $table_name_markers" );
		echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $wpdb->insert_id . '&status=duplicated"; </script> ';
	} elseif ($action == 'switcheditor') {
		if ($new_editor == 'advanced') {
			update_option( 'leafletmapsmarker_editor', $new_editor );
			if ( $markerid != NULL ) {
				echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $markerid . '&status=advanced"; </script> ';
			} else {
				echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&status=advanced"; </script> ';
			}
		} else if ($new_editor == 'simplified') {
			update_option( 'leafletmapsmarker_editor', $new_editor );
			if ( $markerid != NULL ) {
				echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id=' . $markerid . '&status=simplified"; </script> ';
			} else {
				echo '<script> window.location="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&status=simplified"; </script> ';
			}
		}
	}
} else { //info: !empty($action) 2/3
	//info: get icons list
	$iconlist = array();
	$dir = opendir($defaults_marker_icon_dir);
	while ($file = readdir($dir)) {
	if ($file === false)
		break;
	if ($file != "." and $file != "..")
		if (!is_dir($dir.$file) and substr($file, count($file)-5, 4) == '.png')
			$iconlist[] = $file;
	}
	closedir($dir);
	sort($iconlist);
	
	//info: get layers list
	$layerlist = $wpdb->get_results('SELECT * FROM '.$table_name_layers.' WHERE id>0', ARRAY_A);

	$isedit = isset($_GET['id']);
	if (!$isedit) {
		$id = '';
		$markername = '';
		$basemap = $lmm_options[ 'standard_basemap' ];
		$layer = ($lmm_options[ 'defaults_marker_default_layer' ] == '0') ? '' : intval($lmm_options[ 'defaults_marker_default_layer' ]);
		$lat = floatval($lmm_options[ 'defaults_marker_lat' ]);
		$lon = floatval($lmm_options[ 'defaults_marker_lon' ]);
		$icon = ($lmm_options[ 'defaults_marker_icon' ] == NULL) ? '' : $lmm_options[ 'defaults_marker_icon' ];
		$popuptext = '';
		$zoom = intval($lmm_options[ 'defaults_marker_zoom' ]);
		$openpopup = $lmm_options[ 'defaults_marker_openpopup' ];
		$mapwidth = intval($lmm_options[ 'defaults_marker_mapwidth' ]);
		$mapwidthunit = $lmm_options[ 'defaults_marker_mapwidthunit' ];
		$mapheight = intval($lmm_options[ 'defaults_marker_mapheight' ]);
		$panel = $lmm_options[ 'defaults_marker_panel' ];
		$mcreatedby = '';
		$mcreatedon = '';
		$mupdatedby = '';
		$mupdatedon = '';
		$controlbox = $lmm_options[ 'defaults_marker_controlbox' ];
		$overlays_custom = ( (isset($lmm_options[ 'defaults_marker_overlays_custom_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom_active' ] == 1 ) ) ? '1' : '0';
		$overlays_custom2 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom2_active' ] == 1 ) ) ? '1' : '0';
		$overlays_custom3 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom3_active' ] == 1 ) ) ? '1' : '0';
		$overlays_custom4 = ( (isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_overlays_custom4_active' ] == 1 ) ) ? '1' : '0';
		$wms = ( (isset($lmm_options[ 'defaults_marker_wms_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms_active' ] == 1 ) ) ? '1' : '0';
		$wms2 = ( (isset($lmm_options[ 'defaults_marker_wms2_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms2_active' ] == 1 ) ) ? '1' : '0';
		$wms3 = ( (isset($lmm_options[ 'defaults_marker_wms3_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms3_active' ] == 1 ) ) ? '1' : '0';
		$wms4 = ( (isset($lmm_options[ 'defaults_marker_wms4_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms4_active' ] == 1 ) ) ? '1' : '0';
		$wms5 = ( (isset($lmm_options[ 'defaults_marker_wms5_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms5_active' ] == 1 ) ) ? '1' : '0';
		$wms6 = ( (isset($lmm_options[ 'defaults_marker_wms6_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms6_active' ] == 1 ) ) ? '1' : '0';
		$wms7 = ( (isset($lmm_options[ 'defaults_marker_wms7_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms7_active' ] == 1 ) ) ? '1' : '0';
		$wms8 = ( (isset($lmm_options[ 'defaults_marker_wms8_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms8_active' ] == 1 ) ) ? '1' : '0';
		$wms9 = ( (isset($lmm_options[ 'defaults_marker_wms9_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms9_active' ] == 1 ) ) ? '1' : '0';
		$wms10 = ( (isset($lmm_options[ 'defaults_marker_wms10_active' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_wms10_active' ] == 1 ) ) ? '1' : '0';
		$kml_timestamp = '';
		$address = '';
		$gpx_url = '';
		$gpx_panel = 0;
	} else {
		$id = intval($_GET['id']);
		$row = $wpdb->get_row('SELECT markername,basemap,layer,lat,lon,icon,popuptext,zoom,openpopup,mapwidth,mapwidthunit,mapheight,panel,createdby,createdon,updatedby,updatedon,controlbox,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,kml_timestamp,address,gpx_url,gpx_panel FROM '.$table_name_markers.' WHERE id='.$id, ARRAY_A);
		$markername = esc_js(htmlspecialchars($row['markername']));
		$basemap = $row['basemap'];
		$layer = $row['layer'];
		$lat = $row['lat'];
		$lon = $row['lon'];
		$icon = $row['icon'];
		$popuptext = $row['popuptext'];
		$zoom = $row['zoom'];
		$openpopup = $row['openpopup'];
		$mapwidth = $row['mapwidth'];
		$mapwidthunit = $row['mapwidthunit'];
		$mapheight = $row['mapheight'];
		$panel = $row['panel'];
		$mcreatedby = $row['createdby'];
		$mcreatedon = $row['createdon'];
		$mupdatedby = $row['updatedby'];
		$mupdatedon = $row['updatedon'];
		$controlbox = $row['controlbox'];
		$overlays_custom = $row['overlays_custom'];
		$overlays_custom2 = $row['overlays_custom2'];
		$overlays_custom3 = $row['overlays_custom3'];
		$overlays_custom4 = $row['overlays_custom4'];
		$wms = $row['wms'];
		$wms2 = $row['wms2'];
		$wms3 = $row['wms3'];
		$wms4 = $row['wms4'];
		$wms5 = $row['wms5'];
		$wms6 = $row['wms6'];
		$wms7 = $row['wms7'];
		$wms8 = $row['wms8'];
		$wms9 = $row['wms9'];
		$wms10 = $row['wms10'];
		$kml_timestamp = $row['kml_timestamp'];
		$address = htmlspecialchars($row['address']);
		$gpx_url = $row['gpx_url'];
		$gpx_panel = $row['gpx_panel'];
	}

	//info: check if marker exists - part 1
	if ($lat === NULL) {
		$error_marker_not_exists = sprintf( esc_attr__('Error: a marker with the ID %1$s does not exist!','lmm'), htmlspecialchars($_GET['id']));
		echo '<p><div class="error" style="padding:10px;">' . $error_marker_not_exists . '</div></p>';
		echo '<p><a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_markers\'>' . __('list all markers','lmm') . '</a>&nbsp;&nbsp;&nbsp;<a class=\'button-secondary lmm-nav-secondary\' href=\'' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker\'>' . __('add new marker','lmm') . '</a></p>';
	} else {
		$edit_status = isset($_GET['status']) ? $_GET['status'] : '';
		if ( $edit_status == 'updated') {
			echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully updated','lmm') . '</div>';
		} else if ( $edit_status == 'published') {
			echo '<p><div class="updated" style="padding:10px;">' . __('Marker has been successfully published','lmm') . '</div>';
		} else if ( $edit_status == 'simplified') {
			echo '<p><div class="updated" style="padding:10px;">' . __('You successfully switched to the simplified editor.','lmm') . '</div>';
		} else if ( $edit_status == 'advanced') {
			echo '<p><div class="updated" style="padding:10px;">' . __('You successfully switched to the advanced editor.','lmm') . '</div>';
		} else if ( $edit_status == 'duplicated') {
			echo '<p><div class="updated" style="padding:10px;">' . __('The marker has been duplicated successfully.','lmm') . '</div>';
		}
	?>

	<?php $nonce= wp_create_nonce('marker-nonce'); ?>
	<form method="post">
	<?php wp_nonce_field('marker-nonce'); ?>
	<input type="hidden" name="id" value="<?php echo $id ?>" />
	<input type="hidden" name="action" value="<?php echo ($isedit ? 'edit' : 'add') ?>" />
	<input type="hidden" id="basemap" name="basemap" value="<?php echo $basemap ?>" />
	<input type="hidden" id="overlays_custom" name="overlays_custom" value="<?php echo $overlays_custom ?>" />
	<input type="hidden" id="overlays_custom2" name="overlays_custom2" value="<?php echo $overlays_custom2 ?>" />
	<input type="hidden" id="overlays_custom3" name="overlays_custom3" value="<?php echo $overlays_custom3 ?>" />
	<input type="hidden" id="overlays_custom4" name="overlays_custom4" value="<?php echo $overlays_custom4 ?>" />
	<?php
	$noncelink = wp_create_nonce('marker-nonce');
	if ($current_editor == 'simplified') {
		echo '<div id="editmodeswitch" style="float:right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<div style="float:right;"><a style="text-decoration:none;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=switcheditor&new_editor=advanced&_wpnonce=' . $noncelink . '&markerid=' . $id . '" onclick="return confirm(\'' . esc_attr__('Please note that unsaved input will not be passed to the new editor! Please click "OK" to switch the editor anyway or "Cancel" to go back and save first.','lmm') . '\')"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="Editor-Switch-Icon" style="margin:-2px 0 0 5px;" /></div>' . __('switch to advanced editor','lmm') . '</a></div>';
	} else if ($current_editor == 'advanced') {
		echo '<div id="editmodeswitch" style="float:right;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<div style="float:right;"><a style="text-decoration:none;" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&action=switcheditor&new_editor=simplified&_wpnonce=' . $noncelink . '&markerid=' . $id . '" onclick="return confirm(\'' . esc_attr__('Please note that unsaved input will not be passed to the new editor! Please click "OK" to switch the editor anyway or "Cancel" to go back and save first.','lmm') . '\')"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-editorswitch.png" width="24" height="24" alt="Editor-Switch-Icon" style="margin:-2px 0 0 5px;" /></div>' . __('switch to simplified editor','lmm') . '</a></div>';
	}
	?>

	<h3 style="font-size:23px;"><?php ($isedit === true) ? _e('Edit marker','lmm') : _e('Add new marker','lmm') ?>
	<?php 
		if ($isedit === true) {	echo '"' . stripslashes($markername) . '" (ID '.$id.')'; }
		if (lmm_check_capability_edit($mcreatedby) == TRUE) {
			if ($isedit === true) { $button_text = __('update','lmm'); } else { $button_text = __('publish','lmm'); }
			echo '<input style="font-weight:bold;margin-left:10px;" type="submit" name="marker" class="submit button-primary" value="' . $button_text . '" />';
		} else {
			if ($isedit === true) { 
				echo '<span style="font-size:13px;margin-left:20px;">' . __('Your user does not have the permission to update this marker!','lmm') . '</span>';
			} else { 
				$button_text = __('publish','lmm'); 
				echo '<input style="font-weight:bold;margin-left:10px;" type="submit" name="marker" class="submit button-primary" value="' . $button_text . '" />';
			}
		}
	?>
	</h3>

	<table class="widefat">
		<?php if ($isedit === true) { ?>
		<tr>
			<td style="width:230px;" class="lmm-border"><label for="shortcode"><strong><?php _e('Shortcode and API links','lmm') ?></strong></label></td>
			<td class="lmm-border"><input id="shortcode" style="width:206px;background:#f3efef;" type="text" value="[<?php echo $lmm_options[ 'shortcode' ]; ?> marker=&quot;<?php echo $id?>&quot;]" <?php echo $shortcode_select; ?>>
			<?php
				if ($current_editor == 'simplified') {
					echo '<div id="apilinkstext" style="display:inline;"><a tabindex="123" style="cursor:pointer;">' . __('show API links','lmm') . '</a></div>';
					echo '<span id="apilinks" style="display:none;">';
				}
			?>
			<a tabindex="125" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '' ?>"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" /> KML</a> <a tabindex="126" href="http://www.mapsmarker.com/kml" target="_blank" title="<?php esc_attr_e('Click here for more information on how to use as KML in Google Earth or Google Maps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="127" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" /> <?php _e('Fullscreen','lmm'); ?></a> <span title="<?php esc_attr_e('Open standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="128" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-Logo" /> <?php _e('QR code','lmm'); ?></a> <span title="<?php esc_attr_e('Create QR code image for standalone map in fullscreen mode','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="129" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp&full=yes&full_icon_url=yes' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoJSON</a> <a tabindex="130" href="http://www.mapsmarker.com/geojson" target="_blank" title="<?php esc_attr_e('Click here for more information on how to integrate GeoJSON into external websites or apps','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="131" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-georss.png" width="14" height="14" alt="GeoJSON-Logo" /> GeoRSS</a> <a tabindex="132" href="http://www.mapsmarker.com/georss" target="_blank" title="<?php esc_attr_e('Click here for more information on how to subscribe to new markers via GeoRSS','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="133" href="<?php echo LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '' ?>" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" /> Wikitude</a> <a tabindex="134" href="http://www.mapsmarker.com/wikitude" target="_blank" title="<?php esc_attr_e('Click here for more information on how to display in Wikitude Augmented-Reality browser','lmm') ?>"> <img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-question-mark.png" width="12" height="12" border="0"/></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a tabindex="134" href="http://www.mapsmarker.com/mapsmarker-api" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/icon-menu-page.png" width="16" height="16" alt="Mapsmarker-Logo" /> MapsMarker API</a>
			<?php
				if ($current_editor == 'simplified') {
					echo '</span>';
				}
			?>
			<br><small><?php _e('Use this shortcode in posts or pages on your website or one of the API links for embedding in external websites or apps','lmm') ?></small>
				</td>
		</tr>
		<?php } ?>
		<tr>
			<td style="width:230px;" class="lmm-border"><label for="markername"><strong><?php _e('Marker name','lmm') ?></strong></label></td>
			<td class="lmm-border"><input <?php if (get_option('leafletmapsmarker_update_info') == 'hide') { echo 'autofocus'; } ?> style="width:640px;" type="text" id="markername" name="markername" value="<?php echo stripslashes($markername) ?>" /></td>
		</tr>
		<tr>
			<td class="lmm-border"><label for="address"><strong><?php _e('Location','lmm') ?></strong></label><br/><br/><a tabindex="99" href="http://code.google.com/intl/de-AT/apis/maps/documentation/places/autocomplete.html" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/powered-by-google.png" width="104" height="16" style="<?php echo $css_whitelabel = ($lmm_options['misc_whitelabel_backend'] == 'enabled') ? 'display:none' : '' ?>" /></a></td>
			<td class="lmm-border"><label for="address"><?php _e('Please select a place or an address','lmm') ?></label> <?php if (current_user_can('activate_plugins')) { echo '<span style="' . $current_editor_css . '"><a tabindex="100" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#google">(' . __('Settings','lmm') . ')</a></span>'; } ?><br/>
				<input style="width: 640px;" type="text" id="address" name="address" value="<?php echo stripslashes(htmlspecialchars($address)); ?>" />
				<div style="margin-top:5px;<?php echo $current_editor_css; ?>">
				<?php _e('or paste coordinates here','lmm') ?> -
				<?php _e('latitude','lmm') ?>: <input style="width: 100px;" type="text" id="lat" name="lat" value="<?php echo $lat; ?>" />
				<?php _e('longitude','lmm') ?>: <input style="width: 100px;" type="text" id="lon" name="lon" value="<?php echo $lon; ?>" />
				</div>
			</td>
		</tr>
		<tr>
			<td class="lmm-border"><p style="margin-bottom:0px;"><label for="mapwidth"><strong><?php _e('Map size','lmm') ?></strong></label><br/>
				<?php _e('Width','lmm') ?>:
				<input size="3" maxlength="4" type="text" id="mapwidth" name="mapwidth" value="<?php echo $mapwidth ?>" />
				<input id="mapwidthunit_px" type="radio" name="mapwidthunit" value="px" <?php checked($mapwidthunit, 'px'); ?>>
				<label for="mapwidthunit_px">px</label>&nbsp;&nbsp;&nbsp;
				<input id="mapwidthunit_percent" type="radio" name="mapwidthunit" value="%" <?php checked($mapwidthunit, '%'); ?>><label for="mapwidthunit_percent">%</label><br/>
				<?php _e('Height','lmm') ?>:
				<input size="3" maxlength="4" type="text" id="mapheight" name="mapheight" value="<?php echo $mapheight ?>" />px
				<br/><br/>
				<label for="zoom"><strong><?php _e('Zoom','lmm') ?></strong> <img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('You can also change zoom level by clicking on + or - on preview map or using your mouse wheel'); ?>" width="12" height="12" border="0"/></label>&nbsp;<input style="width: 30px;" type="text" id="zoom" name="zoom" value="<?php echo $zoom ?>" />
				<small>
				<?php 
				echo '<span style="' . $current_editor_css . '"><br>' . __('Global maximum zoom level','lmm') . ': ';
				if (current_user_can('activate_plugins')) { 
					echo '<a title="' . esc_attr__('If the native maximum zoom level of a basemap is lower, tiles will be upscaled automatically.','lmm') . '" tabindex="111" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#lmm-mapdefaults-section1">' . intval($lmm_options['global_maxzoom_level']) . '</a>'; 
				} else {
					echo intval($lmm_options['global_maxzoom_level']);
				}
				?>
				</span>
				</small>
				<br/><br/>
				<label for="layer"><strong><?php _e('Layer','lmm') ?></strong></label>
				<?php if ($addtoLayer == NULL) { //info: addtoLayer part1/3 ?>
				<select id="layer" name="layer">
					<option value="0">
					<?php _e('not assigned to a layer','lmm') ?>
					</option>
					<?php
						foreach ($layerlist as $row) {
							if ($row['multi_layer_map'] == 0) {
								echo '<option value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . '>' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . ')</option>';
							} else {
								echo '<option title="' . esc_attr__('This is a multi-layer map - markers cannot be assigned to this layer directly','lmm') . '" value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . ' disabled="disabled">' . stripslashes(htmlspecialchars($row['name'])) . ' (ID ' . $row['id'] . '/MLM)</option>';
							}
						}
					?>
				</select>
				<br>
				<small> <?php echo $layereditlink = ($layer != 0) ? "<a href=\"" . LEAFLET_WP_ADMIN_URL . "admin.php?page=leafletmapsmarker_layer&id=".$layer."\">" . __('edit layer','lmm') . " (ID ".$layer.")</a> " . __('or','lmm') . "" : "" ?> <a tabindex="121" href="<?php LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer">
				<?php _e('add new layer','lmm') ?>
				</a></small>
				<?php } else { //info: addtoLayer part2/3 ?>
				<input type="hidden" name="layer" value="<?php echo $addtoLayer ?>" />
				<a href="<?php LEAFLET_WP_ADMIN_URL ?>admin.php?page=leafletmapsmarker_layer&id=<?php echo $addtoLayer ?>"><?php echo htmlspecialchars($layername) ?> (ID <?php echo $addtoLayer ?>)</a>
				<?php } //info: addtoLayer part3/3 ?>

				<br/><br/>
				<div style="float:right;"><?php _e('display panel','lmm') ?>&nbsp;&nbsp;<input type="checkbox" name="gpx_panel" id="gpx_panel" <?php checked($gpx_panel, 1 ); ?>></div>
				<label for="gpx_url"><strong><?php _e('URL to GPX track','lmm') ?></strong></label><br/>
				<?php
					if ($gpx_url != NULL) {
						//info: load gpx_content
						$gpx_content_array = wp_remote_post( $gpx_url, array( 'sslverify' => false, 'timeout' => 10 ) );
					}
					if ($gpx_url != NULL) {
						if ( !lmm_isValidURL( $gpx_url ) ) {
							echo '<div class="error" style="padding:10px;">' . __('The URL to the GPX file you entered seems to be invalid (it has to start with http for example)!','lmm') . '</div>';
						} else if ($gpx_content_array['body'] == NULL) {

							echo '<div class="error" style="padding:10px;">' . sprintf(__('The GPX file at %s could not be found!','lmm'), $gpx_url) . '</div>';
						}
					}
				?>
				<input style="width:229px;" type="text" id="gpx_url" name="gpx_url" value="<?php echo $gpx_url ?>" /><br/>
				<?php if (current_user_can('upload_files')) { echo '<small><span style="color:#21759B;cursor:pointer;" onMouseOver="this.style.color=\'#D54E21\'" onMouseOut="this.style.color=\'#21759B\'" id="upload_gpx_file">' . __('add','lmm') . '</span> |'; } ?>
				<a tabindex="117" href="http://www.mapsmarker.com/gpx-convert" target="_blank" title="<?php esc_attr_e('Click here for a tutorial on how to convert a non-GPX-track file into a GPX track file','lmm'); ?>"><?php _e('convert','lmm'); ?></a> | 
				<a tabindex="118" href="http://www.mapsmarker.com/gpx-merge" target="_blank" title="<?php esc_attr_e('Click here for a tutorial on how to merge multiple GPX-track files into one GPX track file','lmm'); ?>"><?php _e('merge','lmm'); ?></a>
				<?php if (current_user_can('activate_plugins')) { echo ' | <a tabindex="116" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#mapdefaults-section19">' . __('settings','lmm') . '</a>'; } ?>
				<?php if ($gpx_url != NULL) { $fitbounds_css = 'display:inline;'; } else { $fitbounds_css = 'display:none;'; }
				echo '<span id="gpx_fitbounds_link" style="color:#21759B;cursor:pointer;' . $fitbounds_css . '" onMouseOver="this.style.color=\'#D54E21\'" onMouseOut="this.style.color=\'#21759B\'" class="gpxfitbounds"> | ' . __('fit bounds','lmm') . ' <img title="' . esc_attr__('Attention: when you save the map, the position of the marker will be used for the map center!','lmm') . '" src="' .  LEAFLET_PLUGIN_URL . 'inc/img/icon-exclamation.png" width="14" height="14" border="0"/></small></span>'; ?>
				</p>
				<div style="<?php echo $current_editor_css; ?>">
				<p><br/>
				<strong><?php _e('Controlbox for basemaps/overlays','lmm') ?>:</strong></label><br/>
				<input id="controlbox_hidden" type="radio" name="controlbox" value="0" <?php checked($controlbox, 0); ?>><label for="controlbox_hidden"><?php _e('hidden','lmm') ?></label><br/>
				<input id="controlbox_collapsed" type="radio" name="controlbox" value="1" <?php checked($controlbox, 1); ?>><label for="controlbox_collapsed"><?php _e('collapsed','lmm') ?></label><br/>
				<input id="controlbox_expanded" type="radio" name="controlbox" value="2" <?php checked($controlbox, 2); ?>><label for="controlbox_expanded"><?php _e('expanded','lmm') ?></label>
				<br/><br/>
				<strong><?php _e('Display panel','lmm') ?></strong>&nbsp;&nbsp;<input type="checkbox" name="panel" id="panel" <?php checked($panel, 1 ); ?>><br/>
				<small><?php _e('If checked, panel on top of map is displayed','lmm') ?></small>
				<br/><br/>
				<?php
				global $wp_version;
				if ( version_compare( $wp_version, '3.3', '>=' ) ) { ?>
				<script type="text/javascript">
					var $j = jQuery.noConflict();
					$j(function() {
					$j("#kml_timestamp").datetimepicker({
						dateFormat: 'yy-mm-dd',
						changeMonth: true,
						changeYear: true,
						timeText: '<?php esc_attr_e('Time','lmm'); ?>',
						hourText: '<?php esc_attr_e('Hour','lmm'); ?>',
						minuteText: '<?php esc_attr_e('Minute','lmm'); ?>',
						secondText: '<?php esc_attr_e('Second','lmm'); ?>',
						currentText: '<?php esc_attr_e('Now','lmm'); ?>',
						closeText: '<?php esc_attr_e('Add','lmm'); ?>',
						timeFormat: 'hh:mm:ss',
						showSecond: true,
					});});
				</script>
				<?php }; ?>
				<label for="kml_timestamp"><strong><?php _e('Timestamp for KML animation','lmm') ?>:</strong></label> <a tabindex="104" href="http://www.mapsmarker.com/kml-timestamp" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-question-mark.png" title="<?php esc_attr_e('Click here for more information on animations in KML/Google Earth','lmm'); ?>" width="12" height="12" border="0"/></a><br/>
				<input type="text" id="kml_timestamp" name="kml_timestamp" value="<?php echo $kml_timestamp ; ?>" style="width:145px;background-image:url(<?php echo LEAFLET_PLUGIN_URL; ?>inc/img/icon-calendar.png);background-position:123px center;background-repeat:no-repeat;" /><br/>
				<small><?php _e('If empty, marker creation date will be used','lmm') ?></small>
				<?php
					if (current_user_can('activate_plugins')) {
						if ( $lmm_options['misc_backlinks'] == 'show' ) {
							echo '<br/><br/><strong>' . __('Hide MapsMarker.com backlinks','lmm') .'</strong>';
							echo '<br/><small><a tabindex="110" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#misc">' . __('Please visit Settings / Misc to disable MapsMarker.com backlinks','lmm') . '</a></small>';
						}
					}
				?>
				<?php if (current_user_can('activate_plugins')) { ?>
				<br/><br/>
				<strong><?php _e('Minimap settings','lmm'); ?></strong><br/>
				<small><a tabindex="110" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#mapdefaults-section17"><?php _e('Please visit Settings / Maps / Minimap settings','lmm'); ?></a></small>
				<?php } ?>
				</p>
				</div>
			</td>
			<td id="wmscheckboxes" style="padding-bottom:5px;" class="lmm-border">
				<?php
				echo '<div id="lmm" style="float:left;width:' . $mapwidth.$mapwidthunit . ';">'.PHP_EOL;
				//info: panel for marker name and API URLs
				$panel_state = ($panel == 1) ? 'block' : 'none';
				echo '<div id="lmm-panel" class="lmm-panel" style="display:' . $panel_state . '; background: ' . addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]) . ';">'.PHP_EOL;
				echo '<div class="lmm-panel-api">';
					if ( (isset($lmm_options[ 'defaults_marker_panel_directions' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_directions' ] == 1 ) ) {
							//info: Google language localization (directions)
							if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
								$google_language = '';
							} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
								if ( defined('WPLANG') ) { $google_language = '&hl=' . substr(WPLANG, 0, 2); } else { $google_language =  '&hl=en'; }
							} else {
								$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
							}
							//info: build directions provider links
							if ($lmm_options['directions_provider'] == 'googlemaps') {
								if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = urlencode($lmm_options['google_maps_base_domain_custom']); }
								if ((isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 )) { $yours_transport_type_icon = 'icon-walk.png'; } else { $yours_transport_type_icon = 'icon-car.png'; }
								if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
								$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
								$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
								$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
								$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
								echo '<a tabindex="105" href="http://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
							} else if ($lmm_options['directions_provider'] == 'yours') {
								if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'motorcar') { $yours_transport_type_icon = 'icon-car.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'bicycle') { $yours_transport_type_icon = 'icon-bicycle.png'; } else if ($lmm_options[ 'directions_yours_type_of_transport' ] == 'foot') { $yours_transport_type_icon = 'icon-walk.png'; }
								echo '<a tabindex="105" href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $yours_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
							} else if ($lmm_options['directions_provider'] == 'osrm') {
								echo '<a tabindex="105" href="http://map.project-osrm.org/?hl=' . $lmm_options[ 'directions_osrm_language' ] . '&loc=' . $lat . ',' . $lon . '&df=' . $lmm_options[ 'directions_osrm_units' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" /></a>';
							} else if ($lmm_options['directions_provider'] == 'ors') {
								if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Pedestrian') { $ors_transport_type_icon = 'icon-walk.png'; } else if ($lmm_options[ 'directions_ors_route_preferences' ] == 'Bicycle') { $ors_transport_type_icon = 'icon-bicycle.png'; } else { $ors_transport_type_icon = 'icon-car.png'; }
								echo '<a tabindex="105" href="http://openrouteservice.org/index.php?end=' . $lon . ',' . $lat . '&pref=' . $lmm_options[ 'directions_ors_route_preferences' ] . '&lang=' . $lmm_options[ 'directions_ors_language' ] . '&noMotorways=' . $lmm_options[ 'directions_ors_no_motorways' ] . '&noTollways=' . $lmm_options[ 'directions_ors_no_tollways' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/' . $ors_transport_type_icon . '" width="14" height="14" class="lmm-panel-api-images" /></a>';
							} else if ($lmm_options['directions_provider'] == 'bingmaps') {
								if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
								echo '<a tabindex="105" href="http://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-car.png" width="14" height="14" class="lmm-panel-api-images" /></a>';
							}
					}
					if ( (isset($lmm_options[ 'defaults_marker_panel_kml' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_kml' ] == 1 ) ) {
					echo '<a tabindex="106" href="' . LEAFLET_PLUGIN_URL . 'leaflet-kml.php?marker=' . $id . '&name=' . $lmm_options[ 'misc_kml' ] . '" style="text-decoration:none;" title="' . esc_attr__('Export as KML for Google Earth/Google Maps','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-kml.png" width="14" height="14" alt="KML-Logo" class="lmm-panel-api-images" /></a>';
					}
					if ( (isset($lmm_options[ 'defaults_marker_panel_fullscreen' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_fullscreen' ] == 1 ) ) {
					echo '<a tabindex="107" href="' . LEAFLET_PLUGIN_URL . 'leaflet-fullscreen.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Open standalone map in fullscreen mode','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-fullscreen.png" width="14" height="14" alt="Fullscreen-Logo" class="lmm-panel-api-images" /></a>';
					}
					if ( (isset($lmm_options[ 'defaults_marker_panel_qr_code' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_qr_code' ] == 1 ) ) {
					echo '<a tabindex="108" href="' . LEAFLET_PLUGIN_URL . 'leaflet-qr.php?marker=' . $id . '" target="_blank" title="' . esc_attr__('Create QR code image for standalone map in fullscreen mode','lmm') . '"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-qr-code.png" width="14" height="14" alt="QR-code-logo" class="lmm-panel-api-images" /></a>';
					}
					if ( (isset($lmm_options[ 'defaults_marker_panel_geojson' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_geojson' ] == 1 ) ) {
					echo '<a tabindex="109" href="' . LEAFLET_PLUGIN_URL . 'leaflet-geojson.php?marker=' . $id . '&callback=jsonp&full=yes&full_icon_url=yes" style="text-decoration:none;" title="' . esc_attr__('Export as GeoJSON','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-json.png" width="14" height="14" alt="GeoJSON-Logo" class="lmm-panel-api-images" /></a>';
					}
					if ( (isset($lmm_options[ 'defaults_marker_panel_georss' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_georss' ] == 1 ) ) {
					echo '<a tabindex="110" href="' . LEAFLET_PLUGIN_URL . 'leaflet-georss.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as GeoRSS','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-georss.png" width="14" height="14" alt="GeoRSS-Logo" class="lmm-panel-api-images" /></a>';
					}
					if ( (isset($lmm_options[ 'defaults_marker_panel_wikitude' ] ) == TRUE ) && ( $lmm_options[ 'defaults_marker_panel_wikitude' ] == 1 ) ) {
					echo '<a tabindex="111" href="' . LEAFLET_PLUGIN_URL . 'leaflet-wikitude.php?marker=' . $id . '" style="text-decoration:none;" title="' . esc_attr__('Export as ARML for Wikitude Augmented-Reality browser','lmm') . '" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-wikitude.png" width="14" height="14" alt="Wikitude-Logo" class="lmm-panel-api-images" /></a>';
					}
				echo '</div>'.PHP_EOL;
				echo '<div id="lmm-panel-text" class="lmm-panel-text" style="' . addslashes($lmm_options[ 'defaults_marker_panel_paneltext_css' ]) . '">' . (($markername == NULL) ? __('if set, markername will be displayed here','lmm') : stripslashes($markername)) . '</div>'.PHP_EOL;
				?>
				</div> <!--end lmm-panel-->
				<div id="selectlayer" style="height:<?php echo $mapheight; ?>px;"></div>
				<?php $gpx_panel_state = ($gpx_panel == 1) ? 'block' : 'none'; ?>
				<div id="gpx-panel-selectlayer" class="gpx-panel" style="display:<?php echo $gpx_panel_state; ?>; background: <?php echo addslashes($lmm_options[ 'defaults_marker_panel_background_color' ]); ?>;">
				<?php
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') { $gpx_unit_distance = 'km'; $gpx_unit_elevation = 'm'; } else { $gpx_unit_distance = 'mi'; $gpx_unit_elevation = 'ft'; }
				if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
					$gpx_metadata_name = __('Track name','lmm') . ': <span class="gpx-name"></span>';
				} else { $gpx_metadata_name = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
					$gpx_metadata_start = __('Start','lmm') . ': <span class="gpx-start"></span>';
				} else { $gpx_metadata_start = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
					$gpx_metadata_end = __('End','lmm') . ': <span class="gpx-end"></span>';
				} else { $gpx_metadata_end = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
					$gpx_metadata_distance = __('Distance','lmm') . ': <span class="gpx-distance"></span> ' . $gpx_unit_distance;
				} else { $gpx_metadata_distance = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
					$gpx_metadata_duration_moving = __('Moving time','lmm') . ': <span class="gpx-duration-moving"></span> ';
				} else { $gpx_metadata_duration_moving = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
					$gpx_metadata_duration_total = __('Duration','lmm') . ': <span class="gpx-duration-total"></span> ';
				} else { $gpx_metadata_duration_total = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
					$gpx_metadata_avpace = '&#216;&nbsp;' . __('Pace','lmm') . ': <span class="gpx-avpace"></span>/' . $gpx_unit_distance;
				} else { $gpx_metadata_avpace = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
					$gpx_metadata_avhr = '&#216;&nbsp;' . __('Heart rate','lmm') . ': <span class="gpx-avghr"></span>';
				} else { $gpx_metadata_avhr = NULL; }
				if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
					$gpx_metadata_elevation_title = __('Elevation','lmm') . ':';
				} else { $gpx_metadata_elevation_title = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
					$gpx_metadata_elev_gain = '+<span class="gpx-elevation-gain"></span>' . $gpx_unit_elevation;
				} else { $gpx_metadata_elev_gain = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
					$gpx_metadata_elev_loss = '-<span class="gpx-elevation-loss"></span>' . $gpx_unit_elevation;
				} else { $gpx_metadata_elev_loss = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
					$gpx_metadata_elev_net = '(' . __('net','lmm') . ': <span class="gpx-elevation-net"></span>' . $gpx_unit_elevation . ')';
				} else { $gpx_metadata_elev_net = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
					$gpx_metadata_elev_full = '<br/>' . __('Full elevation data','lmm') . ':<br/><span class="gpx-elevation-full"></span>';
				} else { $gpx_metadata_elev_full = NULL; }
				if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
					$gpx_metadata_hr_full = '<br/>' . __('Full heart rate data','lmm') . ':<br/><span class="gpx-heartrate-full"></span>';
				} else { $gpx_metadata_hr_full = NULL; }
				$gpx_metadata_elevation_array = array($gpx_metadata_elevation_title, $gpx_metadata_elev_gain, $gpx_metadata_elev_loss, $gpx_metadata_elev_net);
				$gpx_metadata_elevation = implode(' ',$gpx_metadata_elevation_array);
				if ($gpx_metadata_elevation == '   ') { $gpx_metadata_elevation = NULL; } //info: for no trailing |
				$gpx_metadata_array_all = array($gpx_metadata_name, $gpx_metadata_start, $gpx_metadata_end, $gpx_metadata_distance, $gpx_metadata_duration_moving, $gpx_metadata_duration_total, $gpx_metadata_avpace, $gpx_metadata_avhr, $gpx_metadata_elevation, $gpx_metadata_elev_full, $gpx_metadata_hr_full);

				$gpx_metadata_array_not_null = array();
				foreach ($gpx_metadata_array_all as $key => $value) {
					if (is_null($value) === false) {
						$gpx_metadata_array_not_null[$key] = $value;
					}
				}
				$gpx_metadata = implode(' | ',$gpx_metadata_array_not_null);
				echo $gpx_metadata;
				?>
				</div>

				</div><!--end mapsmarker div-->
				
				<span style="display:none;" id="selectlayer-popuptext-hidden"><?php echo do_shortcode(stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$popuptext))); ?></span>
				<?php
				if ($lmm_options['directions_popuptext_panel'] == 'yes') {
					if ($lmm_options['directions_provider'] == 'googlemaps') {
						if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = urlencode($lmm_options['google_maps_base_domain_custom']); }
						if ( $address != NULL ) { $google_from = urlencode($address); } else { $google_from = $lat . ',' . $lon; }
						$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
						$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
						$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
						$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
						$directionslink = "http://" . $gmaps_base_domain_directions . "/maps?daddr=" . $google_from . "&t=" . $lmm_options[ 'directions_googlemaps_map_type' ] . "&layer=" . $lmm_options[ 'directions_googlemaps_traffic' ] . "&doflg=" . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . "&om=" . $lmm_options[ 'directions_googlemaps_overview_map' ];
					} else if ($lmm_options['directions_provider'] == 'yours') {
						$directionslink = "http://www.yournavigation.org/?tlat=" . $lat . "&tlon=" . $lon . "&v=" . $lmm_options[ 'directions_yours_type_of_transport' ] . "&fast=" . $lmm_options[ 'directions_yours_route_type' ] . "&layer=" . $lmm_options[ 'directions_yours_layer' ];
					} else if ($lmm_options['directions_provider'] == 'osrm') {
						$directionslink = "http://map.project-osrm.org/?hl=" . $lmm_options[ 'directions_osrm_language' ] . "&loc=" . $lat . "," . $lon . "&df=" . $lmm_options[ 'directions_osrm_units' ];
					} else if ($lmm_options['directions_provider'] == 'ors') {
						$directionslink = "http://openrouteservice.org/index.php?end=" . $lon . "," . $lat . "&pref=" . $lmm_options[ 'directions_ors_route_preferences' ] . "&lang=" . $lmm_options[ 'directions_ors_language' ] . "&noMotorways=" . $lmm_options[ 'directions_ors_no_motorways' ] . "&noTollways=" . $lmm_options[ 'directions_ors_no_tollways' ];
					} else if ($lmm_options['directions_provider'] == 'bingmaps') {
						if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
						$directionslink = "http://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos." . $lat . "_" . $lon . $bing_to;
					}		
					$mpopuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;" : "";		
					echo '<span id="selectlayer-popuptext-dlink-hidden" style="display:none;"><div style="' . $mpopuptext_css . '">' . stripslashes(htmlspecialchars($address)) . ' <a href="' . $directionslink . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">(' . __('Directions','lmm') . ')</a></div></span>';
				} ?>
				
				<div style="float:right;margin-top:10px;<?php echo $current_editor_css; ?>"><p><strong><?php _e('WMS layers','lmm') ?></strong> <?php if (current_user_can('activate_plugins')) { echo '<a tabindex="101" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#wms">(' . __('Settings','lmm') . ')</a>'; } ?></p>
				<?php
				//info: define available wms layers (for markers and layers)
				if ( (isset($lmm_options[ 'wms_wms_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms" name="wms"';
					if ($wms == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms">' . htmlspecialchars_decode($lmm_options[ 'wms_wms_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms2_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms2_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms2" name="wms2"';
					if ($wms2 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms2">' . htmlspecialchars_decode($lmm_options[ 'wms_wms2_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms3_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms3_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms3" name="wms3"';
					if ($wms3 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms3">' . htmlspecialchars_decode($lmm_options[ 'wms_wms3_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms4_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms4_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms4" name="wms4"';
					if ($wms4 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms4">' . htmlspecialchars_decode($lmm_options[ 'wms_wms4_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms5_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms5_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms5" name="wms5"';
					if ($wms5 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms5">' . htmlspecialchars_decode($lmm_options[ 'wms_wms5_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms6_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms6_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms6" name="wms6"';
					if ($wms6 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms6">' . htmlspecialchars_decode($lmm_options[ 'wms_wms6_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms7_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms7_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms7" name="wms7"';
					if ($wms7 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms7">' . htmlspecialchars_decode($lmm_options[ 'wms_wms7_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms8_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms8_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms8" name="wms8"';
					if ($wms8 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms8">' . htmlspecialchars_decode($lmm_options[ 'wms_wms8_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms9_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms9_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms9" name="wms9"';
					if ($wms9 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms9">' . htmlspecialchars_decode($lmm_options[ 'wms_wms9_name' ]) . ' </label><br/>';
				}
				if ( (isset($lmm_options[ 'wms_wms10_available' ] ) == TRUE ) && ( $lmm_options[ 'wms_wms10_available' ] == 1 ) ) {
					echo '<input type="checkbox" id="wms10" name="wms10"';
					if ($wms10 == 1) { echo ' checked="checked"'; }
					echo '/>&nbsp;<label for="wms10">' . htmlspecialchars_decode($lmm_options[ 'wms_wms10_name' ]) . ' </label>';
				}
				?>
			</div>
			</td>
		</tr>
		<tr>
			<td class="lmm-border"><label for="default_icon"><strong><?php _e('Icon', 'lmm') ?></strong></label>
				<br/>
				<br/>
				<?php
				if ($current_editor == 'simplified') {
					echo '<div id="mapiconscollection" style="display:none;">';
				} ?>
				<a tabindex="122" title="Maps Icons Collection - http://mapicons.nicolasmollet.com" href="http://mapicons.nicolasmollet.com" target="_blank"><img src="<?php echo LEAFLET_PLUGIN_URL ?>inc/img/logo-mapicons.png" width="88" heigh="31" style="<?php echo $css_whitelabel = ($lmm_options['misc_whitelabel_backend'] == 'enabled') ? 'display:none' : '' ?>" /></a><br/>
				<small>
				<?php
				$mapicons_admin = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and upload the new icons to the directory %2$s/','lmm'), '<a tabindex="112" href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a>', $defaults_marker_icon_url);
				$mapicons_user = sprintf( __('If you want to use different icons, please visit the %1$s (offering more than 700 compatible icons) and ask your WordPress admin to upload the new icons to the directory %2$s/','lmm'), '<a tabindex="113" href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a>', $defaults_marker_icon_url);
				$upload_icons_button_text = '<br/><br/>' . __('You can also upload the icons by clicking the button "upload new icon"','lmm');
				if (current_user_can('activate_plugins')) { echo $mapicons_admin . $upload_icons_button_text; } else { echo $mapicons_user . $upload_icons_button_text; }
				if (is_multisite()) {
				$mapicons_directory = sprintf( __('As you are running your blog within a WordPress Multisite installation, please use this icon directory on your server: %2$s/','lmm'), '<a tabindex="114" href="http://mapicons.nicolasmollet.com" target="_blank">Map Icons Collection</a>', $defaults_marker_icon_dir);
				echo '<br/><br/>' . $mapicons_directory;
				}
				?>
				<?php
				if ($current_editor == 'simplified') {
					echo '</div>';
				} ?>
				</small>
			</td>
			<td class="lmm-border"><?php
				if ($current_editor == 'simplified') {
					echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" width="32" height="37" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input id="default_icon" style="margin:1px 0 0 1px;" onchange="updateicon(this.value);" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
					if ($icon != NULL) {
						echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="' . $icon . '"><img src="' . $defaults_marker_icon_url . '/' . $icon . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" title="' . esc_attr__('filename','lmm') . ': ' . $icon . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . $icon .'" alt="' . $icon . '" /></label><br/><input id="' . $icon . '" style="margin:1px 0 0 1px;" onchange="updateicon(this.value);" type="radio" name="icon" value="' . $icon . '" checked="" /></div>';
						echo '<div id="moreiconslink" style="display:block;margin:15px 0 0 45px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
						echo '<div id="moreicons" style="display:none;">';
						foreach ($iconlist as $row) {
							echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="'.$row.'"><img src="' . $defaults_marker_icon_url . '/' . $row . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" /></label><br/><input id="'.$row.'" style="margin:1px 0 0 1px;" onchange="updateicon(this.value);" type="radio" name="icon" value="'.$row.'"/></div>';
						}
					} else {
						echo '<div id="moreiconslink" style="display:block;margin:15px 0 0 45px;"><a style="cursor:pointer;">' . __('show more icons','lmm') . '</a></div>';
						echo '<div id="moreicons" style="display:none;">';
						foreach ($iconlist as $row) {
							echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="'.$row.'"><img src="' . $defaults_marker_icon_url . '/' . $row . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" /></label><br/><input id="'.$row.'" style="margin:1px 0 0 1px;" onchange="updateicon(this.value);" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'/></div>';
						}
					}
					echo '<div style="text-align:center;float:left;line-height:0px;margin:23px 10px;" id="showlessicons"><a style="cursor:pointer;">' . __('show fewer icons','lmm') . '</a></div>';
					if (current_user_can('upload_files')) {
						echo '<input type="button" id="upload-new-icon" value="' . esc_attr__('upload new icon','lmm') . '" style="margin-top:7px;" />';
					}
					$noncelink_uploadicon = wp_create_nonce('icon-upload-nonce');
					echo '</div>';
				} else if ($current_editor == 'advanced') {
					echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png' . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" title="' . esc_attr__('filename','lmm') . ': marker.png, ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_default" alt="default.png" /></label><br/><input id="default_icon" style="margin:1px 0 0 1px;" onchange="updateicon(this.value);" type="radio" name="icon" value="" ' . ($icon == NULL ? ' checked' : '') . '/></div>';
					foreach ($iconlist as $row) {
						echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="'.$row.'"><img src="' . $defaults_marker_icon_url . '/' . $row . '" title="' . esc_attr__('filename','lmm') . ': ' . $row . ', ' . esc_attr__('CSS classname','lmm') . ': lmm_marker_icon_' . substr($row, 0, -4) . '" alt="' . $row . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" /></label><br/><input id="'.$row.'" style="margin:1px 0 0 1px;" onchange="updateicon(this.value);" type="radio" name="icon" value="'.$row.'"'.($row == $icon ? ' checked' : '').'></div>';
					}
					if (current_user_can('upload_files')) {
						echo '<input type="button" id="upload-new-icon" value="' . esc_attr__('upload new icon','lmm') . '" style="margin-top:7px;" />';
					}
					$noncelink_uploadicon = wp_create_nonce('icon-upload-nonce');
				} ?>
				<script type="text/javascript">
				jQuery(function($) {
				$(document).ready(function(){
					var info = $("<div id='uploader' style='overflow: hidden' />");
					info.html('<iframe width="450" height="120" scrolling="no" src = "<?php echo LEAFLET_PLUGIN_URL . 'inc/icon-upload.php?_wpnonceicon=' . $noncelink_uploadicon; ?>" />')
						info.wpdialog({
							title : '<?php esc_attr_e('Upload new icon','lmm'); ?>',
							dialogClass: 'wp-dialog',
							open: function(event, ui) {
									$(".ui-dialog-title").css('color','#000000');
									$(".ui-icon-closethick").css('display','none');
									$("#uploader").css('background','#fff');
								},
							width : 450,
							height : 120,
							modal : true,
							autoOpen : false,
							draggable : false,
							closeOnEscape : true
						});
						$(document).on('click', '#upload-new-icon', function(){
							info.wpdialog('open');
							return false;
						});
					});
					});
				</script></div>
			</td>
		</tr>
		<tr>
			<td class="lmm-border"><label for="popuptext"><strong><?php _e('Popup text','lmm') ?></strong></label>
			<br /><br />
			<?php _e('open popup','lmm') ?>&nbsp;&nbsp;<input type="checkbox" name="openpopup" id="openpopup" <?php checked($openpopup, 1 ); ?>>
			<br/><small>
			<?php _e('If unchecked, the popup will only be visible after clicking on the marker on marker- or layer-maps.','lmm') ?>
			</small>
			<br /><br />
			<?php _e('add directions link','lmm') ?>&nbsp;&nbsp;<?php if ($lmm_options['directions_popuptext_panel'] == 'yes') { echo '<input type="checkbox" name="add-directions" id="add-directions" checked="checked" disabled="disabled">'; } else { echo '<input type="checkbox" name="add-directions" id="add-directions" disabled="disabled">'; } ?>
			<br/><small><a tabindex="119" href="<?php echo LEAFLET_WP_ADMIN_URL; ?>admin.php?page=leafletmapsmarker_settings#lmm-directions"><?php _e('Please visit Settings to change this globally','lmm'); ?></a></small>
			</p>
			</td>
			<td class="lmm-border">
			<?php
				if ( version_compare( $wp_version, '3.3', '>=' ) )
				{
					$settings = array(
							'wpautop' => true,
							'tinymce' => array(
							'theme_advanced_buttons1' => 'bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,outdent,indent,blockquote,|,link,unlink,|,ltr,rtl',
							'theme' => 'advanced',
							'height' => '300',
							'content_css' => LEAFLET_PLUGIN_URL . 'inc/css/leafletmapsmarker-admin-tinymce.php',
							'theme_advanced_statusbar_location' => 'bottom',
							'setup' => 'function(ed) {
									ed.onKeyDown.add(function(ed, e) {
										marker._popup.setContent(ed.getContent());
									});
								}'
							 ),
							'quicktags' => array(
								'buttons' => 'strong,em,link,block,del,ins,img,code,close'));
					wp_editor( stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$popuptext)), 'popuptext', $settings);
				}
				else //info: for WP 3.0, 3.1. 3.2
				{
					if (function_exists( 'wp_tiny_mce' ) ) {
						add_filter( 'teeny_mce_before_init', create_function( '$a', '
						$a["theme_advanced_buttons1"] = "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,|,outdent,indent,blockquote,|,bullist,numlist,|,link,unlink,image,|,code";
						$a["theme"] = "advanced";
						$a["skin"] = "wp_theme";
						$a["height"] = "250";
						$a["width"] = "640";
						$a["onpageload"] = "";
						$a["mode"] = "exact";
						$a["elements"] = "popuptext";
						$a["editor_selector"] = "mceEditor";
						$a["plugins"] = "inlinepopups";
						$a["forced_root_block"] = "p";
						$a["force_br_newlines"] = true;
						$a["force_p_newlines"] = false;
						$a["convert_newlines_to_brs"] = true;
						$a["theme_advanced_statusbar_location"] = "bottom";
						return $a;'));
						wp_tiny_mce(true);
					}
				echo '<textarea id="popuptext" name="popuptext">' . stripslashes($popuptext) . '</textarea>';
				}
			?>
			<small>
				<?php
				$max_popup_image_size_note = sprintf( esc_attr__('Note: if you add an image, its width gets reduced to %1$spx to fit in popup - its height gets reduced by the according ratio automatically!','lmm'), intval($lmm_options['defaults_marker_popups_image_max_width']));
				echo $max_popup_image_size_note;
				if (current_user_can('activate_plugins')) { echo ' <span style="' . $current_editor_css . '"><a tabindex="102" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#mapdefaults-section6" title="' . esc_attr__('can be changed at section "Default values for marker popups"','lmm') . '">(' . __('Settings','lmm') . ')</a></span>'; }
				?>
				</small>
			</td>
		</tr>
		<?php if ($mcreatedby != NULL) {?>
		<tr style="<?php echo $current_editor_css; ?>">
			<td class="lmm-border"><small><strong><?php _e('Audit','lmm') ?></strong></small></td>
			<td class="lmm-border"><small>
				<?php
				echo __('Marker added by','lmm') . ' ';
				echo $mcreatedby . ' - ' . $mcreatedon;
				if ($mupdatedon != $mcreatedon) {
					echo ', ' . __('last update by','lmm');
					echo ' ' . $mupdatedby . ' - ' . $mupdatedon;
				}; ?>
				</small></td>
		</tr>
		<?php }; ?>
	</table>

	<table><tr><td>
	<?php
		if ($isedit === true) { $edit_button_css = ''; } else { $edit_button_css = 'margin-top:17px;'; }
		if (lmm_check_capability_edit($mcreatedby) == TRUE) {
			if ($isedit === true) { $button_text = __('update','lmm'); } else { $button_text = __('publish','lmm'); }
			echo '<input style="font-weight:bold;' . $edit_button_css . '" type="submit" name="marker" class="submit button-primary" value="' . $button_text . '" />';
		} else {
			if ($isedit === true) { 
				echo __('Your user does not have the permission to update this marker!','lmm');
			} else { 
				$button_text = __('publish','lmm'); 
				echo '<input style="font-weight:bold;' . $edit_button_css . '" type="submit" name="marker" class="submit button-primary" value="' . $button_text . '" />';
			}
		}
	?>
	</form>
	</td>
	<?php if ($isedit) { ?>
	<td>
		<?php
			if (lmm_check_capability_edit($mcreatedby) == TRUE) {
				echo '<form method="post">';
				wp_nonce_field('marker-nonce');
				echo '<input type="hidden" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="duplicate" />';
				echo '<div class="submit" style="margin:0 0 0 40px;">';
				echo '<input class="submit button-secondary lmm-nav-secondary" type="submit" name="marker" value="' . __('duplicate', 'lmm') . '" />';
				echo '</div></form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to duplicate this marker!','lmm') . '</span>';
			}
		?>
	</td>
	<td>
		<?php
			if (lmm_check_capability_delete($mcreatedby) == TRUE) {
				echo '<form method="post">';
				wp_nonce_field('marker-nonce');
				echo '<input type="hidden" name="id" value="' . $id . '" />';
				echo '<input type="hidden" name="action" value="delete" />';
				$confirm = sprintf( esc_attr__('Do you really want to delete marker %1$s (ID %2$s)?','lmm'), $markername, $id);
				echo '<div class="submit" style="margin:0 0 0 40px;">';
				echo '<input class="submit button-secondary lmm-nav-secondary" style="color:#FF0000;" type="submit" name="marker" value="' . __('delete', 'lmm') . '" onclick="return confirm(\'' . $confirm . '\')" />';
				echo '</div></form>';
			} else {
				echo '<span style="margin-left:20px;">' . __('Your user does not have the permission to delete this marker!','lmm') . '</span>';
			}
		?>
	</td>
	<?php } ?>
	</div>
	</tr></table>
	<!--wrap-->
	<script type="text/javascript">
	/* //<![CDATA[ */
	var marker,selectlayer,googleLayer_roadmap,googleLayer_satellite,googleLayer_hybrid,googleLayer_terrain,bingaerial,bingaerialwithlabels,bingroad,osm_mapnik,mapquest_osm,mapquest_aerial,ogdwien_basemap,ogdwien_satellite,cloudmade,cloudmade2,cloudmade3,mapbox,mapbox2,mapbox3,custom_basemap,custom_basemap2,custom_basemap3,empty_basemap,overlays_custom,overlays_custom2,overlays_custom3,overlays_custom4,wms,wms2,wms3,wms4,wms5,wms6,wms7,wms8,wms9,wms10,layersControl;
	(function($) {
		selectlayer = new L.Map("selectlayer", { dragging: <?php echo $lmm_options['misc_map_dragging'] ?>, touchZoom: <?php echo $lmm_options['misc_map_touchzoom'] ?>, scrollWheelZoom: <?php echo $lmm_options['misc_map_scrollwheelzoom'] ?>, doubleClickZoom: <?php echo $lmm_options['misc_map_doubleclickzoom'] ?>, boxzoom: <?php echo $lmm_options['map_interaction_options_boxzoom'] ?>, trackResize: <?php echo $lmm_options['misc_map_trackresize'] ?>, worldCopyJump: <?php echo $lmm_options['map_interaction_options_worldcopyjump'] ?>, closePopupOnClick: <?php echo $lmm_options['misc_map_closepopuponclick'] ?>, keyboard: <?php echo $lmm_options['map_keyboard_navigation_options_keyboard'] ?>, keyboardPanOffset: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardpanoffset']) ?>, keyboardZoomOffset: <?php echo intval($lmm_options['map_keyboard_navigation_options_keyboardzoomoffset']) ?>, inertia: <?php echo $lmm_options['map_panning_inertia_options_inertia'] ?>, inertiaDeceleration: <?php echo intval($lmm_options['map_panning_inertia_options_inertiadeceleration']) ?>, inertiaMaxSpeed: <?php echo intval($lmm_options['map_panning_inertia_options_inertiamaxspeed']) ?>, zoomControl: <?php echo $lmm_options['misc_map_zoomcontrol'] ?>, crs: <?php echo $lmm_options['misc_projections'] ?>, fullscreenControl: <?php echo $lmm_options['map_fullscreen_button'] ?> });

		<?php
		if ( $lmm_options['misc_backlinks'] == 'show' ) {
			$attrib_prefix_affiliate = ($lmm_options['affiliate_id'] == NULL) ? 'go' : intval($lmm_options['affiliate_id']) . '.html';
			$attrib_prefix = '<a tabindex=\"115\" href=\"http://www.mapsmarker.com/' . $attrib_prefix_affiliate . '\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker for WordPress - helping you to share your favorite spots and tracks','lmm') . '\">MapsMarker.com</a> (<a href=\"http://www.leafletjs.com\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker is based on the javascript library Leaflet maintained by Vladimir Agafonkin and Cloudmade','lmm') . '\">Leaflet</a>/<a href=\"http://mapicons.nicolasmollet.com\" target=\"_blank\" title=\"' . esc_attr__('Leaflet Maps Marker uses icons from the Maps Icons Collection maintained by Nicolas Mollet','lmm') . '\">icons</a>/<a href=\"http://www.visualead.com/go\" target=\"_blank\" rel=\"nofollow\" title=\"' . esc_attr__('Visual QR codes for fullscreen maps are created by Visualead.com','lmm') . '\">QR</a>)';
		} else {
			$attrib_prefix = '';
		}
		$osm_editlink = ($lmm_options['misc_map_osm_editlink'] == 'show') ? '&nbsp;(<a href=\"http://www.openstreetmap.org/edit?editor=' . $lmm_options['misc_map_osm_editlink_editor'] . '&amp;lat=' . $lat . '&amp;lon=' . $lon . '&zoom=' . $zoom . '\" target=\"_blank\" title=\"' . esc_attr__('help OpenStreetMap.org to improve map details','lmm') . '\">' . __('edit','lmm') . '</a>)' : '';
		$attrib_osm_mapnik = __("Map",'lmm').': &copy; <a tabindex=\"118\" href=\"http://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
		$attrib_mapquest_osm = __("Map",'lmm').': Tiles Courtesy of <a tabindex=\"118\" href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'inc/img/logo-mapquest.png\" style=\"display:inline;\" /> - &copy; <a tabindex=\"119\" href=\"http://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>' . $osm_editlink;
		$attrib_mapquest_aerial = __("Map",'lmm').': <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"' . LEAFLET_PLUGIN_URL . 'inc/img/logo-mapquest.png\" style=\"display:inline;\" />, Portions Courtesy NASA/JPL-Caltech and U.S. Depart. of Agriculture, Farm Service Agency';
		$attrib_ogdwien_basemap = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\" >data.wien.gv.at</a>)';
		$attrib_ogdwien_satellite = __("Map",'lmm').': ' . __("City of Vienna","lmm") . ' (<a href=\"http://data.wien.gv.at\" target=\"_blank\">data.wien.gv.at</a>)';
		$attrib_cloudmade = __("Map",'lmm').': &copy; <a href=\"http://www.openstreetmap.org/copyright\" target=\"_blank\">' . __('OpenStreetMap contributors','lmm') . '</a>, Imagery &copy; <a href=\"http://cloudmade.com\" target=\"_blank\">CloudMade</a>';
		$attrib_custom_basemap = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap_attribution' ]);
		$attrib_custom_basemap2 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap2_attribution' ]);
		$attrib_custom_basemap3 = __("Map",'lmm').': ' . addslashes($lmm_options[ 'custom_basemap3_attribution' ]);
		?>
		selectlayer.attributionControl.setPrefix("<?php echo $attrib_prefix; ?>");

		<?php $maxzoom = intval($lmm_options['global_maxzoom_level']); ?>
		osm_mapnik = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {mmid: 'osm_mapnik', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 0, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_osm_mapnik; ?>", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		mapquest_osm = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {mmid: 'mapquest_osm', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 18, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_mapquest_osm; ?>", subdomains: ['otile1','otile2','otile3','otile4'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		mapquest_aerial = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png", {mmid: 'mapquest_aerial', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 11, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_mapquest_aerial; ?>", subdomains: ['otile1','otile2','otile3','otile4'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		googleLayer_roadmap = new L.Google("ROADMAP", {mmid: 'googleLayer_roadmap', detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		googleLayer_satellite = new L.Google("SATELLITE", {mmid: 'googleLayer_satellite', detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		googleLayer_hybrid = new L.Google("HYBRID", {mmid: 'googleLayer_hybrid', detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		googleLayer_terrain = new L.Google("TERRAIN", {mmid: 'googleLayer_terrain', detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		<?php if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) { ?>
		bingaerial = new L.BingLayer("<?php echo $lmm_options[ 'bingmaps_api_key' ]; ?>", {mmid: 'bingaerial', type: 'Aerial', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		bingaerialwithlabels = new L.BingLayer("<?php echo $lmm_options[ 'bingmaps_api_key' ]; ?>", {mmid: 'bingaerialwithlabels', type: 'AerialWithLabels', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		bingroad = new L.BingLayer("<?php echo $lmm_options[ 'bingmaps_api_key' ]; ?>", {mmid: 'bingroad', type: 'Road', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		<?php }; ?>
		ogdwien_basemap = new L.TileLayer("http://{s}.wien.gv.at/wmts/fmzk/pastell/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_basemap', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 11, attribution: "<?php echo $attrib_ogdwien_basemap; ?>", subdomains: ['maps','maps1', 'maps2', 'maps3'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		ogdwien_satellite = new L.TileLayer("http://{s}.wien.gv.at/wmts/lb/farbe/google3857/{z}/{y}/{x}.jpeg", {mmid: 'ogdwien_satellite', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 11, attribution: "<?php echo $attrib_ogdwien_satellite; ?>", subdomains: ['maps','maps1', 'maps2', 'maps3'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		//info: create Cloudmade TileURLs
		<?php
		$cloudmade_tileurl = "http://{s}.tile.cloudmade.com/" . $lmm_options[ 'cloudmade_api_key' ] . "/" . $lmm_options[ 'cloudmade_styleid' ] . "/256/{z}/{x}/{y}.png";
		$cloudmade2_tileurl = "http://{s}.tile.cloudmade.com/" . $lmm_options[ 'cloudmade2_api_key' ] . "/" . $lmm_options[ 'cloudmade2_styleid' ] . "/256/{z}/{x}/{y}.png";
		$cloudmade3_tileurl = "http://{s}.tile.cloudmade.com/" . $lmm_options[ 'cloudmade3_api_key' ] . "/" . $lmm_options[ 'cloudmade3_styleid' ] . "/256/{z}/{x}/{y}.png";
		?>
		var cloudmade = new L.TileLayer("<?php echo $cloudmade_tileurl; ?>", {mmid: 'cloudmade', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_cloudmade; ?>", subdomains: ['a','b','c'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var cloudmade2 = new L.TileLayer("<?php echo $cloudmade2_tileurl; ?>", {mmid: 'cloudmade2', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_cloudmade; ?>", subdomains: ['a','b','c'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var cloudmade3 = new L.TileLayer("<?php echo $cloudmade3_tileurl; ?>", {mmid: 'cloudmade3', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: 19, minZoom: 1, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo $attrib_cloudmade; ?>", subdomains: ['a','b','c'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		//info: MapBox basemaps
		var mapbox = new L.TileLayer("http://{s}.tiles.mapbox.com/v3/<?php echo $lmm_options[ 'mapbox_user' ]; ?>.<?php echo $lmm_options[ 'mapbox_map' ]; ?>/{z}/{x}/{y}.png", {mmid: 'mapbox', minZoom: <?php echo intval($lmm_options[ 'mapbox_minzoom' ]); ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'mapbox_maxzoom' ]); ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'mapbox_attribution' ]); ?>", subdomains: ['a','b','c','d'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var mapbox2 = new L.TileLayer("http://{s}.tiles.mapbox.com/v3/<?php echo $lmm_options[ 'mapbox2_user' ]; ?>.<?php echo $lmm_options[ 'mapbox2_map' ]; ?>/{z}/{x}/{y}.png", {mmid: 'mapbox2', minZoom: <?php echo intval($lmm_options[ 'mapbox2_minzoom' ]); ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'mapbox2_maxzoom' ]); ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'mapbox_attribution' ]); ?>", subdomains: ['a','b','c','d'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var mapbox3 = new L.TileLayer("http://{s}.tiles.mapbox.com/v3/<?php echo $lmm_options[ 'mapbox3_user' ]; ?>.<?php echo $lmm_options[ 'mapbox3_map' ]; ?>/{z}/{x}/{y}.png", {mmid: 'mapbox3', minZoom: <?php echo intval($lmm_options[ 'mapbox3_minzoom' ]); ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'mapbox3_maxzoom' ]); ?>, errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", attribution: "<?php echo addslashes($lmm_options[ 'mapbox3_attribution' ]); ?>", subdomains: ['a','b','c','d'], detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		//info: check if subdomains are set for custom basemaps
		<?php
		$custom_basemap_subdomains = ((isset($lmm_options[ 'custom_basemap_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$custom_basemap2_subdomains = ((isset($lmm_options[ 'custom_basemap2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$custom_basemap3_subdomains = ((isset($lmm_options[ 'custom_basemap3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'custom_basemap3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'custom_basemap3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$error_tile_url_custom_basemap = ($lmm_options['custom_basemap_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_custom_basemap2 = ($lmm_options['custom_basemap2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_custom_basemap3 = ($lmm_options['custom_basemap3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		?>
		var custom_basemap = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap_tileurl' ] ?>", {mmid: 'custom_basemap', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap_tms' ] ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap; ?>"<?php echo $custom_basemap_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var custom_basemap2 = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap2_tileurl' ] ?>", {mmid: 'custom_basemap2', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap2_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap2_tms' ] ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap2; ?>"<?php echo $custom_basemap2_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap2_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap2_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var custom_basemap3 = new L.TileLayer("<?php echo $lmm_options[ 'custom_basemap3_tileurl' ] ?>", {mmid: 'custom_basemap3', maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'custom_basemap3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'custom_basemap3_minzoom' ]) ?>, tms: <?php echo $lmm_options[ 'custom_basemap3_tms' ] ?>, <?php echo $error_tile_url_custom_basemap; ?>attribution: "<?php echo $attrib_custom_basemap3; ?>"<?php echo $custom_basemap3_subdomains ?>, continuousWorld: <?php echo $lmm_options[ 'custom_basemap3_continuousworld_enabled' ] ?>, noWrap: <?php echo $lmm_options[ 'custom_basemap3_nowrap_enabled' ] ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		var empty_basemap = new L.TileLayer("", {mmid: 'empty_basemap'});

		//info: check if subdomains are set for custom overlays
		<?php
		$overlays_custom_subdomains = ((isset($lmm_options[ 'overlays_custom_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$overlays_custom2_subdomains = ((isset($lmm_options[ 'overlays_custom2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$overlays_custom3_subdomains = ((isset($lmm_options[ 'overlays_custom3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$overlays_custom4_subdomains = ((isset($lmm_options[ 'overlays_custom4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'overlays_custom4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'overlays_custom4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$error_tile_url_overlays_custom = ($lmm_options['overlays_custom_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_overlays_custom2 = ($lmm_options['overlays_custom2_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_overlays_custom3 = ($lmm_options['overlays_custom3_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		$error_tile_url_overlays_custom4 = ($lmm_options['overlays_custom4_errortileurl'] == 'true') ? 'errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", ' : '';
		?>

		overlays_custom = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom_tileurl' ] ?>", {olid: 'overlays_custom', tms: <?php echo $lmm_options[ 'overlays_custom_tms' ] ?>, <?php echo $error_tile_url_overlays_custom; ?>attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom_attribution' ]) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom_minzoom' ]) ?><?php echo $overlays_custom_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		overlays_custom2 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom2_tileurl' ] ?>", {olid: 'overlays_custom2', tms: <?php echo $lmm_options[ 'overlays_custom2_tms' ] ?>, <?php echo $error_tile_url_overlays_custom2; ?>attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom2_attribution' ]) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom2_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom2_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom2_minzoom' ]) ?><?php echo $overlays_custom2_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		overlays_custom3 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom3_tileurl' ] ?>", {olid: 'overlays_custom3', tms: <?php echo $lmm_options[ 'overlays_custom3_tms' ] ?>, <?php echo $error_tile_url_overlays_custom3; ?>attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom3_attribution' ]) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom3_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom3_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom3_minzoom' ]) ?><?php echo $overlays_custom3_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		overlays_custom4 = new L.TileLayer("<?php echo $lmm_options[ 'overlays_custom4_tileurl' ] ?>", {olid: 'overlays_custom4', tms: <?php echo $lmm_options[ 'overlays_custom4_tms' ] ?>, <?php echo $error_tile_url_overlays_custom4; ?>attribution: "<?php echo addslashes($lmm_options[ 'overlays_custom4_attribution' ]) ?>", opacity: <?php echo floatval($lmm_options[ 'overlays_custom4_opacity' ]) ?>, maxZoom: <?php echo $maxzoom; ?>, maxNativeZoom: <?php echo intval($lmm_options[ 'overlays_custom4_maxzoom' ]) ?>, minZoom: <?php echo intval($lmm_options[ 'overlays_custom4_minzoom' ]) ?><?php echo $overlays_custom4_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});

		//info: check if subdomains are set for wms layers
		<?php
		$wms_subdomains = ((isset($lmm_options[ 'wms_wms_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms2_subdomains = ((isset($lmm_options[ 'wms_wms2_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms2_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms2_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms3_subdomains = ((isset($lmm_options[ 'wms_wms3_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms3_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms3_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms4_subdomains = ((isset($lmm_options[ 'wms_wms4_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms4_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms4_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms5_subdomains = ((isset($lmm_options[ 'wms_wms5_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms5_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms5_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms6_subdomains = ((isset($lmm_options[ 'wms_wms6_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms6_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms6_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms7_subdomains = ((isset($lmm_options[ 'wms_wms7_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms7_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms7_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms8_subdomains = ((isset($lmm_options[ 'wms_wms8_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms8_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms8_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms9_subdomains = ((isset($lmm_options[ 'wms_wms9_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms9_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms9_subdomains_names' ], ENT_QUOTES) . "]" :  "";
		$wms10_subdomains = ((isset($lmm_options[ 'wms_wms10_subdomains_enabled' ]) == TRUE ) && ($lmm_options[ 'wms_wms10_subdomains_enabled' ] == 'yes' )) ? ", subdomains: [" . htmlspecialchars_decode($lmm_options[ 'wms_wms10_subdomains_names' ], ENT_QUOTES) . "]" :  "";

		//info: define wms legends
		$wms_attribution = addslashes($lmm_options[ 'wms_wms_attribution' ]) . ( ($lmm_options[ 'wms_wms_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms2_attribution = addslashes($lmm_options[ 'wms_wms2_attribution' ]) . ( ($lmm_options[ 'wms_wms2_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms2_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms3_attribution = addslashes($lmm_options[ 'wms_wms3_attribution' ]) . ( ($lmm_options[ 'wms_wms3_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms3_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms4_attribution = addslashes($lmm_options[ 'wms_wms4_attribution' ]) . ( ($lmm_options[ 'wms_wms4_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms4_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms5_attribution = addslashes($lmm_options[ 'wms_wms5_attribution' ]) . ( ($lmm_options[ 'wms_wms5_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms5_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms6_attribution = addslashes($lmm_options[ 'wms_wms6_attribution' ]) . ( ($lmm_options[ 'wms_wms6_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms6_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms7_attribution = addslashes($lmm_options[ 'wms_wms7_attribution' ]) . ( ($lmm_options[ 'wms_wms7_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms7_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms8_attribution = addslashes($lmm_options[ 'wms_wms8_attribution' ]) . ( ($lmm_options[ 'wms_wms8_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms8_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms9_attribution = addslashes($lmm_options[ 'wms_wms9_attribution' ]) . ( ($lmm_options[ 'wms_wms9_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms9_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		$wms10_attribution = addslashes($lmm_options[ 'wms_wms10_attribution' ]) . ( ($lmm_options[ 'wms_wms10_legend_enabled' ] == 'yes' ) ? ' (<a href="' . $lmm_options[ 'wms_wms10_legend' ] . '" target=&quot;_blank&quot;>' . __('Legend','lmm') . '</a>)' : '') . '';
		?>

		//info: define wms layers
		wms = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms_baseurl' ] ?>", {wmsid: 'wms', layers: '<?php echo addslashes($lmm_options[ 'wms_wms_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms_format' ])?>', attribution: '<?php echo $wms_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms_version' ])?>'<?php echo $wms_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms2 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms2_baseurl' ] ?>", {wmsid: 'wms2', layers: '<?php echo addslashes($lmm_options[ 'wms_wms2_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms2_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms2_format' ])?>', attribution: '<?php echo $wms2_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms2_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms2_version' ])?>'<?php echo $wms2_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms3 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms3_baseurl' ] ?>", {wmsid: 'wms3', layers: '<?php echo addslashes($lmm_options[ 'wms_wms3_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms3_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms3_format' ])?>', attribution: '<?php echo $wms3_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms3_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms3_version' ])?>'<?php echo $wms3_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms4 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms4_baseurl' ] ?>", {wmsid: 'wms4', layers: '<?php echo addslashes($lmm_options[ 'wms_wms4_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms4_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms4_format' ])?>', attribution: '<?php echo $wms4_attribution ?>', transparent: '<?php echo $lmm_options[ 'wms_wms4_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms4_version' ])?>'<?php echo $wms4_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms5 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms5_baseurl' ] ?>", {wmsid: 'wms5', layers: '<?php echo addslashes($lmm_options[ 'wms_wms5_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms5_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms5_format' ])?>', attribution: '<?php echo $wms5_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms5_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms5_version' ])?>'<?php echo $wms5_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms6 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms6_baseurl' ] ?>", {wmsid: 'wms6', layers: '<?php echo addslashes($lmm_options[ 'wms_wms6_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms6_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms6_format' ])?>', attribution: '<?php echo $wms6_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms6_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms6_version' ])?>'<?php echo $wms6_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms7 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms7_baseurl' ] ?>", {wmsid: 'wms7', layers: '<?php echo addslashes($lmm_options[ 'wms_wms7_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms7_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms7_format' ])?>', attribution: '<?php echo $wms7_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms7_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms7_version' ])?>'<?php echo $wms7_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms8 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms8_baseurl' ] ?>", {wmsid: 'wms8', layers: '<?php echo addslashes($lmm_options[ 'wms_wms8_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms8_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms8_format' ])?>', attribution: '<?php echo $wms8_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms8_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms8_version' ])?>'<?php echo $wms8_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms9 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms9_baseurl' ] ?>", {wmsid: 'wms9', layers: '<?php echo addslashes($lmm_options[ 'wms_wms9_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms9_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms9_format' ])?>', attribution: '<?php echo $wms9_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms9_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms9_version' ])?>'<?php echo $wms9_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		wms10 = new L.TileLayer.WMS("<?php echo $lmm_options[ 'wms_wms10_baseurl' ] ?>", {wmsid: 'wms10', layers: '<?php echo addslashes($lmm_options[ 'wms_wms10_layers' ])?>', styles: '<?php echo addslashes($lmm_options[ 'wms_wms10_styles' ])?>', format: '<?php echo addslashes($lmm_options[ 'wms_wms10_format' ])?>', attribution: '<?php echo $wms10_attribution; ?>', transparent: '<?php echo $lmm_options[ 'wms_wms10_transparent' ]?>', errorTileUrl: "<?php echo LEAFLET_PLUGIN_URL ?>inc/img/error-tile-image.png", version: '<?php echo addslashes($lmm_options[ 'wms_wms10_version' ])?>'<?php echo $wms10_subdomains ?>, detectRetina: <?php echo $lmm_options['map_retina_detection'] ?>});
		//info: controlbox - define basemaps

		layersControl = new L.Control.Layers(
		{
		<?php
			$basemaps_available = "";
			if ( (isset($lmm_options[ 'controlbox_osm_mapnik' ]) == TRUE ) && ($lmm_options[ 'controlbox_osm_mapnik' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_osm_mapnik' ]) . "': osm_mapnik,";
			if ( (isset($lmm_options[ 'controlbox_mapquest_osm' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_osm' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_mapquest_osm' ]) . "': mapquest_osm,";
			if ( (isset($lmm_options[ 'controlbox_mapquest_aerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapquest_aerial' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_mapquest_aerial' ]) . "': mapquest_aerial,";
			if ( (isset($lmm_options[ 'controlbox_googleLayer_roadmap' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_roadmap' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_roadmap' ]) . "': googleLayer_roadmap,";
			if ( (isset($lmm_options[ 'controlbox_googleLayer_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_satellite' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_satellite' ]) . "': googleLayer_satellite,";
			if ( (isset($lmm_options[ 'controlbox_googleLayer_hybrid' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_hybrid' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_hybrid' ]) . "': googleLayer_hybrid,";
			if ( (isset($lmm_options[ 'controlbox_googleLayer_terrain' ]) == TRUE ) && ($lmm_options[ 'controlbox_googleLayer_terrain' ] == 1 ) )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_googleLayer_terrain' ]) . "': googleLayer_terrain,";
			if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
				if ( (isset($lmm_options[ 'controlbox_bingaerial' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerial' ] == 1 ) )
					$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_bingaerial' ]) . "': bingaerial,";
				if ( (isset($lmm_options[ 'controlbox_bingaerialwithlabels' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingaerialwithlabels' ] == 1 ) )
					$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_bingaerialwithlabels' ]) . "': bingaerialwithlabels,";
				if ( (isset($lmm_options[ 'controlbox_bingroad' ]) == TRUE ) && ($lmm_options[ 'controlbox_bingroad' ] == 1 ) )
					$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_bingroad' ]) . "': bingroad,";
			};
			if ( (((isset($lmm_options[ 'controlbox_ogdwien_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_basemap' ] == 1 )) && ((($lat <= '48.326583')  && ($lat >= '48.114308')) && (($lon <= '16.55056')  && ($lon >= '16.187325')) )) || ($basemap == 'ogdwien_basemap') )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_ogdwien_basemap' ]) . "': ogdwien_basemap,";
			if ( (((isset($lmm_options[ 'controlbox_ogdwien_satellite' ]) == TRUE ) && ($lmm_options[ 'controlbox_ogdwien_satellite' ] == 1 )) && ((($lat <= '48.326583')  && ($lat >= '48.114308')) && (($lon <= '16.55056')  && ($lon >= '16.187325')) )) || ($basemap == 'ogdwien_satellite') )
				$basemaps_available .= "'" . addslashes($lmm_options[ 'default_basemap_name_ogdwien_satellite' ]) . "': ogdwien_satellite,";
			if ( (isset($lmm_options[ 'controlbox_cloudmade' ]) == TRUE ) && ($lmm_options[ 'controlbox_cloudmade' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'cloudmade_name' ])."': cloudmade,";
			if ( (isset($lmm_options[ 'controlbox_cloudmade2' ]) == TRUE ) && ($lmm_options[ 'controlbox_cloudmade2' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'cloudmade2_name' ])."': cloudmade2,";
			if ( (isset($lmm_options[ 'controlbox_cloudmade3' ]) == TRUE ) && ($lmm_options[ 'controlbox_cloudmade3' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'cloudmade3_name' ])."': cloudmade3,";
			if ( (isset($lmm_options[ 'controlbox_mapbox' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'mapbox_name' ])."': mapbox,";
			if ( (isset($lmm_options[ 'controlbox_mapbox2' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox2' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'mapbox2_name' ])."': mapbox2,";
			if ( (isset($lmm_options[ 'controlbox_mapbox3' ]) == TRUE ) && ($lmm_options[ 'controlbox_mapbox3' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'mapbox3_name' ])."': mapbox3,";
			if ( (isset($lmm_options[ 'controlbox_custom_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap_name' ])."': custom_basemap,";
			if ( (isset($lmm_options[ 'controlbox_custom_basemap2' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap2' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap2_name' ])."': custom_basemap2,";
			if ( (isset($lmm_options[ 'controlbox_custom_basemap3' ]) == TRUE ) && ($lmm_options[ 'controlbox_custom_basemap3' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'custom_basemap3_name' ])."': custom_basemap3,";
			if ( (isset($lmm_options[ 'controlbox_empty_basemap' ]) == TRUE ) && ($lmm_options[ 'controlbox_empty_basemap' ] == 1 ) )
				$basemaps_available .= "'".addslashes($lmm_options[ 'empty_basemap_name' ])."': empty_basemap,";
			//info: needed for IE7 compatibility
			echo substr($basemaps_available, 0, -1);
		?>
		},

		//info: controlbox - add available overlays
		{
		<?php
			$overlays_custom_available = '';
			if ( ((isset($lmm_options[ 'overlays_custom' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom' ] == 1 )) || ($overlays_custom == 1) )
				$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom_name' ])."': overlays_custom,";
			if ( ((isset($lmm_options[ 'overlays_custom2' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom2' ] == 1 )) || ($overlays_custom2 == 1) )
				$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom2_name' ])."': overlays_custom2,";
			if ( ((isset($lmm_options[ 'overlays_custom3' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom3' ] == 1 )) || ($overlays_custom3 == 1) )
				$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom3_name' ])."': overlays_custom3,";
			if ( ((isset($lmm_options[ 'overlays_custom4' ] ) == TRUE ) && ( $lmm_options[ 'overlays_custom4' ] == 1 )) || ($overlays_custom4 == 1) )
				$overlays_custom_available .= "'".addslashes($lmm_options[ 'overlays_custom4_name' ])."': overlays_custom4,";
			//info: needed for IE7 compatibility
			echo substr($overlays_custom_available, 0, -1);
		?>
		},

		{
		<?php if ( ($controlbox == '0') || ($controlbox == '1') ) {
				echo 'collapsed: true';
			} else if ($controlbox == '2') {
				echo 'collapsed: false';
			}
		?>
		}); //info open layer control box by default on all devices on backend

		selectlayer.setView(new L.LatLng(<?php echo $lat . ', ' . $lon; ?>), <?php echo $zoom ?>);
		selectlayer.addLayer(<?php echo $basemap; ?>)
		//info: controlbox - add active overlays on marker level
		<?php
			if ( (isset($overlays_custom) == TRUE) && ($overlays_custom == 1) )
				echo ".addLayer(overlays_custom)";
			if ( (isset($overlays_custom2) == TRUE) && ($overlays_custom2 == 1) )
				echo ".addLayer(overlays_custom2)";
			if ( (isset($overlays_custom3) == TRUE) && ($overlays_custom3 == 1) )
				echo ".addLayer(overlays_custom3)";
			if ( (isset($overlays_custom4) == TRUE) && ($overlays_custom4 == 1) )
				echo ".addLayer(overlays_custom4)";
		?>

		//info: controlbox - add active overlays on marker level
		<?php
			if ( $wms == 1 )
				echo ".addLayer(wms)";
			if ( $wms2 == 1 )
				echo ".addLayer(wms2)";
			if ( $wms3 == 1 )
				echo ".addLayer(wms3)";
			if ( $wms4 == 1 )
				echo ".addLayer(wms4)";
			if ( $wms5 == 1 )
				echo ".addLayer(wms5)";
			if ( $wms6 == 1 )
				echo ".addLayer(wms6)";
			if ( $wms7 == 1 )
				echo ".addLayer(wms7)";
			if ( $wms8 == 1 )
				echo ".addLayer(wms8)";
			if ( $wms9 == 1 )
				echo ".addLayer(wms9)";
			if ( $wms10 == 1 )
				echo ".addLayer(wms10)";
		?>
		.addControl(layersControl);

		<?php //info: add minimap
		if ($lmm_options['minimap_status'] != 'hidden') {
			echo 'var osm_mapnik_minimap = new L.TileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_osm_mapnik . '", detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			echo 'var mapquest_osm_minimap = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 18, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_mapquest_osm . '", subdomains: ["otile1","otile2","otile3","otile4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			echo 'var mapquest_aerial_minimap = new L.TileLayer("http://{s}.mqcdn.com/tiles/1.0.0/sat/{z}/{x}/{y}.png", {maxZoom: ' . $maxzoom . ', maxNativeZoom: 11, minZoom: 1, errorTileUrl: "' . LEAFLET_PLUGIN_URL . 'inc/img/error-tile-image.png", attribution: "' . $attrib_mapquest_aerial . '", subdomains: ["otile1","otile2","otile3","otile4"], detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			echo 'var googleLayer_roadmap_minimap = new L.Google("ROADMAP", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			echo 'var googleLayer_satellite_minimap = new L.Google("SATELLITE", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			echo 'var googleLayer_hybrid_minimap = new L.Google("HYBRID", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			echo 'var googleLayer_terrain_minimap = new L.Google("TERRAIN", {detectRetina: ' . $lmm_options['map_retina_detection'] . '});'.PHP_EOL;
			//info: bing minimaps
			if ( isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL ) ) {
				echo 'var bingaerial_minimap = new L.BingLayer("' . $lmm_options[ 'bingmaps_api_key' ] . '", {type: "Aerial", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
				echo 'var bingaerialwithlabels_minimap = new L.BingLayer("' . $lmm_options[ 'bingmaps_api_key' ] . '", {type: "AerialWithLabels", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
				echo 'var bingroad_minimap = new L.BingLayer("' . $lmm_options[ 'bingmaps_api_key' ] . '", {type: "Road", maxZoom: ' . $maxzoom . ', maxNativeZoom: 19, minZoom: 1});'.PHP_EOL;
			};
			if ($lmm_options['minimap_zoomLevelFixed'] != NULL) { $zoomlevelfixed =  'zoomLevelFixed: ' . intval($lmm_options['minimap_zoomLevelFixed']) . ','; } else { $zoomlevelfixed = ''; }
			if ($lmm_options['minimap_basemap'] == 'automatic') {
				if ($basemap == 'osm_mapnik') {
					$minimap_basemap = 'osm_mapnik_minimap';
				} else if ($basemap == 'mapquest_osm') {
					$minimap_basemap = 'mapquest_osm_minimap';
				} else if ($basemap == 'mapquest_aerial') {
					$minimap_basemap = 'mapquest_aerial_minimap';
				} else if ($basemap == 'googleLayer_roadmap') {
					$minimap_basemap = 'googleLayer_roadmap_minimap';
				} else if ($basemap == 'googleLayer_satellite') {
					$minimap_basemap = 'googleLayer_satellite_minimap';
				} else if ($basemap == 'googleLayer_hybrid') {
					$minimap_basemap = 'googleLayer_hybrid_minimap';
				} else if ($basemap == 'googleLayer_terrain') {
					$minimap_basemap = 'googleLayer_terrain_minimap';
				} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerial')){
					$minimap_basemap = 'bingaerial_minimap';
				} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingaerialwithlabels')){
					$minimap_basemap = 'bingaerialwithlabels_minimap';
				} else if ( (isset($lmm_options['bingmaps_api_key']) && ($lmm_options['bingmaps_api_key'] != NULL )) && ($basemap == 'bingroad')){
					$minimap_basemap = 'bingroad_minimap';
				} else {
					$minimap_basemap = 'osm_mapnik_minimap';
				}
			} else {
				$minimap_basemap = $lmm_options['minimap_basemap'];
			}
			echo "var miniMap = new L.Control.MiniMap(" . $minimap_basemap . ", {position: '" . $lmm_options['minimap_position'] . "', width: " . intval($lmm_options['minimap_width']) . ", height: " . intval($lmm_options['minimap_height']) . ", zoomLevelOffset: " . intval($lmm_options['minimap_zoomLevelOffset']) . ", " . $zoomlevelfixed . " zoomAnimation: " . $lmm_options['minimap_zoomAnimation'] . ", toggleDisplay: " . $lmm_options['minimap_toggleDisplay'] . ", autoToggleDisplay: " . $lmm_options['minimap_autoToggleDisplay'] . "}).addTo(selectlayer);".PHP_EOL;
		} ?>

		//info: gpx tracks
		<?php if ( ($gpx_url != NULL) && (lmm_isValidURL( $gpx_url)) ) {
			$gpx_track_color = '#' . str_replace('#', '', $lmm_options['gpx_track_color']);
			$gpx_startIconUrl = ($lmm_options['gpx_startIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png' : trim($lmm_options['gpx_startIconUrl']);
			$gpx_endIconUrl = ($lmm_options['gpx_endIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png' : trim($lmm_options['gpx_endIconUrl']);
			$gpx_shadowUrl = ($lmm_options['gpx_shadowUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png' : trim($lmm_options['gpx_shadowUrl']);
			if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
					$gpx_metadata_name_js = 'if (gpx.get_name() != undefined) { _c("gpx-name").innerHTML = gpx.get_name(); } else { _c("gpx-name").innerHTML = "n/a"; }';
			} else { $gpx_metadata_name_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
				$gpx_metadata_start_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-start").innerHTML = gpx.get_start_time().toDateString() + ", " + gpx.get_start_time().toLocaleTimeString(); } else { _c("gpx-start").innerHTML = "n/a"; }';
			} else { $gpx_metadata_start_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
				$gpx_metadata_end_js = 'if (gpx.get_end_time() != undefined) { _c("gpx-end").innerHTML = gpx.get_end_time().toDateString() + ", " + gpx.get_end_time().toLocaleTimeString(); } else { _c("gpx-end").innerHTML = "n/a"; }';
			} else { $gpx_metadata_end_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = (gpx.get_distance()/1000).toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
				} else {
					$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = gpx.get_distance_imp().toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
				}
			} else { $gpx_metadata_distance_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
				$gpx_metadata_duration_moving_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-moving").innerHTML = gpx.get_duration_string(gpx.get_moving_time()); } else { _c("gpx-duration-moving").innerHTML = "n/a"; }';
			} else { $gpx_metadata_duration_moving_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
				$gpx_metadata_duration_total_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-total").innerHTML = gpx.get_duration_string(gpx.get_total_time()); } else { _c("gpx-duration-total").innerHTML = "n/a"; }';
			} else { $gpx_metadata_duration_total_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
				} else {
				$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace_imp(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
				}
			} else { $gpx_metadata_avpace_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
				$gpx_metadata_avhr_js = 'if (isNaN(gpx.get_average_hr())) { _c("gpx-avghr").innerHTML = "n/a"; } else { _c("gpx-avghr").innerHTML = gpx.get_average_hr() + "bpm"; }';
			} else { $gpx_metadata_avhr_js = ''; }
			if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
				$gpx_metadata_elevation_title_js = '';
			} else { $gpx_metadata_elevation_title_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.get_elevation_gain().toFixed(0);';
				} else {
					$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);';
				}
			} else { $gpx_metadata_elev_gain_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.get_elevation_loss().toFixed(0);';
				} else {
					$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);';
				}
			} else { $gpx_metadata_elev_loss_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.get_elevation_gain().toFixed(0) - gpx.get_elevation_loss().toFixed(0);';
				} else {
					$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.to_ft(gpx.get_elevation_gain() - gpx.get_elevation_loss()).toFixed(0);';
				}
			} else { $gpx_metadata_elev_net_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data();';
				} else {
					$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data_imp();';
				}
			} else { $gpx_metadata_elev_full_js = ''; }
			if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
				if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
					$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data();';
				} else {
					$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data_imp();';
				}
			} else { $gpx_metadata_hr_full_js = ''; }

			echo 'function display_gpx_selectlayer() {
						var gpx_panel = document.getElementById("gpx-panel-selectlayer");
						var gpx_url = "'.$gpx_url.'";

						function _c(c) { return gpx_panel.querySelectorAll("."+c)[0]; }

					   var gpx_track = new L.GPX(gpx_url, {
						gpx_content: "'.esc_js($gpx_content_array['body']).'",
						async: true,
						max_point_interval: ' . intval($lmm_options['gpx_max_point_interval']) . ',
						marker_options: {
							startIconUrl: "' . $gpx_startIconUrl . '",
							endIconUrl: "' . $gpx_endIconUrl . '",
							shadowUrl: "' . $gpx_shadowUrl . '",
							iconSize: [' . $lmm_options['gpx_iconSize_x'] . ', ' . $lmm_options['gpx_iconSize_y'] . '],
							shadowSize: [' . $lmm_options['gpx_shadowSize_x'] . ', ' . $lmm_options['gpx_shadowSize_y'] . '],
							iconAnchor: [' . $lmm_options['gpx_iconAnchor_x'] . ', ' . $lmm_options['gpx_iconAnchor_y'] . '],
						shadowAnchor: [' . $lmm_options['gpx_shadowAnchor_x'] . ', ' . $lmm_options['gpx_shadowAnchor_y'] . '],
						className: "lmm_gpx_icons"
						},
						polyline_options: {
							color: "' . $gpx_track_color . '",
							weight: ' . intval($lmm_options['gpx_track_weight']) . ',
							opacity: ' . floatval($lmm_options['gpx_track_opacity']) . ',
							smoothFactor: ' . floatval($lmm_options['gpx_track_smoothFactor']) . ',
							clickable: ' . $lmm_options['gpx_track_clickable'] . ',
							noClip: ' . $lmm_options['gpx_track_noClip'] . '
						},
						}).addTo(selectlayer);
						gpx_track.on("gpx_loaded", function(e) {
								var gpx = e.target;
								' . $gpx_metadata_name_js . '
								' . $gpx_metadata_start_js . '
								' . $gpx_metadata_end_js . '
								' . $gpx_metadata_distance_js . '
								' . $gpx_metadata_duration_moving_js . '
								' . $gpx_metadata_duration_total_js . '
								' . $gpx_metadata_avpace_js . '
								' . $gpx_metadata_avhr_js . '
								' . $gpx_metadata_elev_gain_js . '
								' . $gpx_metadata_elev_loss_js . '
								' . $gpx_metadata_elev_net_js . '
								' . $gpx_metadata_elev_full_js . '
								' . $gpx_metadata_hr_full_js . '
						});
				}
				display_gpx_selectlayer();'.PHP_EOL;
		}
		?>

		//info: add scale control
		<?php if ( $lmm_options['map_scale_control'] == 'enabled' ) { ?>
		L.control.scale({position:'<?php echo $lmm_options['map_scale_control_position'] ?>', maxWidth: <?php echo intval($lmm_options['map_scale_control_maxwidth']) ?>, metric: <?php echo $lmm_options['map_scale_control_metric'] ?>, imperial: <?php echo $lmm_options['map_scale_control_imperial'] ?>, updateWhenIdle: <?php echo $lmm_options['map_scale_control_updatewhenidle'] ?>}).addTo(selectlayer);
		<?php }; ?>
		marker = new L.Marker(new L.LatLng(<?php echo $lat . ", " . $lon; ?>),{ <?php if ($lmm_options[ 'defaults_marker_icon_title' ] == 'show') { $markername_title = htmlspecialchars_decode($markername); echo "title: '$markername_title', "; }; ?> opacity: <?php echo floatval($lmm_options[ 'defaults_marker_icon_opacity' ]) ?>, alt: "<?php echo $markername_title; ?>"});
		<?php 
		if ($icon == NULL) {
			echo "marker.options.icon = new L.Icon({iconUrl: '" . LEAFLET_PLUGIN_URL . "leaflet-dist/images/marker.png',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_default'});".PHP_EOL;
			} else {
			echo "marker.options.icon = new L.Icon({iconUrl: '" . $defaults_marker_icon_url . "/" . $icon . "',iconSize: [" . intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]) . "],iconAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]) . "],popupAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]) . "],shadowUrl: '" . $marker_shadow_url . "',shadowSize: [" . intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]) . "],shadowAnchor: [" . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]) . ", " . intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]) . "],className: 'lmm_marker_icon_" . substr($icon, 0, -4) . "'});".PHP_EOL;
		} 
		?>
		
		<?php if ( ($popuptext == NULL) && ($lmm_options['directions_popuptext_panel'] == 'no') ) { ?>
		marker.options.clickable = false;
		<?php }?>
		selectlayer.addLayer(marker);
		<?php
		if ($controlbox == '0') { echo "$('.leaflet-control-layers').hide();"; }

		if ($lmm_options['directions_popuptext_panel'] == 'yes') {
		 $directions_settings_link = ( (current_user_can('activate_plugins')) && ($current_editor == 'advanced') ) ? ' (<a tabindex="103" href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_settings#directions" title="' . esc_attr__('change directions settings','lmm') . '">' . __('Settings','lmm') . '</a>)' : '';
		 if ($address == NULL) {
			$google_from = $lat . ',' . $lon;
			$address = esc_attr__('if set, address will be displayed here','lmm');
		} else {
			$google_from = urlencode($address);
		}
		 $address = (($address == NULL) ? esc_attr__('if set, address will be displayed here','lmm') : $address);
		 $popuptext_css = ($popuptext != NULL) ? "border-top:1px solid #f0f0e7;padding-top:5px;margin-top:5px;clear:both;" : "";
		 $popuptext = $popuptext . '<div style="' . $popuptext_css . '">' . $address . ' ';

		if ($lmm_options['directions_provider'] == 'googlemaps') {
			if ( isset($lmm_options['google_maps_base_domain_custom']) && ($lmm_options['google_maps_base_domain_custom'] == NULL) ) { $gmaps_base_domain_directions = $lmm_options['google_maps_base_domain']; } else { $gmaps_base_domain_directions = urlencode($lmm_options['google_maps_base_domain_custom']); }
			$avoidhighways = (isset($lmm_options[ 'directions_googlemaps_route_type_highways' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_highways' ] == 1 ) ? '&dirflg=h' : '';
			$avoidtolls = (isset($lmm_options[ 'directions_googlemaps_route_type_tolls' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_tolls' ] == 1 ) ? '&dirflg=t' : '';
			$publictransport = (isset($lmm_options[ 'directions_googlemaps_route_type_public_transport' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_public_transport' ] == 1 ) ? '&dirflg=r' : '';
			$walking = (isset($lmm_options[ 'directions_googlemaps_route_type_walking' ] ) == TRUE ) && ( $lmm_options[ 'directions_googlemaps_route_type_walking' ] == 1 ) ? '&dirflg=w' : '';
			//info: Google language localization (directions)
			if ($lmm_options['google_maps_language_localization'] == 'browser_setting') {
				$google_language = '';
			} else if ($lmm_options['google_maps_language_localization'] == 'wordpress_setting') {
				if ( defined('WPLANG') ) { $google_language = '&hl=' . substr(WPLANG, 0, 2); } else { $google_language =  '&hl=en'; }
			} else {
			$google_language = '&hl=' . $lmm_options['google_maps_language_localization'];
			}
			$popuptext = $popuptext . '(<a href="http://' . $gmaps_base_domain_directions . '/maps?daddr=' . $google_from . '&t=' . $lmm_options[ 'directions_googlemaps_map_type' ] . '&layer=' . $lmm_options[ 'directions_googlemaps_traffic' ] . '&doflg=' . $lmm_options[ 'directions_googlemaps_distance_units' ] . $avoidhighways . $avoidtolls . $publictransport . $walking . $google_language . '&om=' . $lmm_options[ 'directions_googlemaps_overview_map' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
		} else if ($lmm_options['directions_provider'] == 'yours') {
				$popuptext = $popuptext . '(<a href="http://www.yournavigation.org/?tlat=' . $lat . '&tlon=' . $lon . '&v=' . $lmm_options[ 'directions_yours_type_of_transport' ] . '&fast=' . $lmm_options[ 'directions_yours_route_type' ] . '&layer=' . $lmm_options[ 'directions_yours_layer' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>';
		} else if ($lmm_options['directions_provider'] == 'osrm') {
			$popuptext = $popuptext . '(<a href="http://map.project-osrm.org/?hl=' . $lmm_options[ 'directions_osrm_language' ] . '&loc=' . $lat . ',' . $lon . '&df=' . $lmm_options[ 'directions_osrm_units' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
		} else if ($lmm_options['directions_provider'] == 'ors') {
			$popuptext = $popuptext . '(<a href="http://openrouteservice.org/index.php?end=' . $lon . ',' . $lat . '&pref=' . $lmm_options[ 'directions_ors_route_preferences' ] . '&lang=' . $lmm_options[ 'directions_ors_language' ] . '&noMotorways=' . $lmm_options[ 'directions_ors_no_motorways' ] . '&noTollways=' . $lmm_options[ 'directions_ors_no_tollways' ] . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
		} else if ($lmm_options['directions_provider'] == 'bingmaps') {
		if ( $address != NULL ) { $bing_to = '_' . urlencode($address); } else { $bing_to = ''; }
			$popuptext = $popuptext . '(<a href="http://www.bing.com/maps/default.aspx?v=2&rtp=pos___e_~pos.' . $lat . '_' . $lon . $bing_to . '" target="_blank" title="' . esc_attr__('Get directions','lmm') . '">' . __('Directions','lmm') . '</a>)';
		}
		$popuptext = $popuptext . $directions_settings_link . '</div>';
		}
		?>

		<?php
		if ($lmm_options['directions_popuptext_panel'] == 'yes') {
			echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML+document.getElementById("selectlayer-popuptext-dlink-hidden").innerHTML,';
		} else {
			echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML,';
		}?>
			{ maxWidth: <?php echo intval($lmm_options['defaults_marker_popups_maxwidth']) ?>, minWidth: <?php echo intval($lmm_options['defaults_marker_popups_minwidth']) ?>, maxHeight: <?php echo intval($lmm_options['defaults_marker_popups_maxheight']) ?>, autoPan: <?php echo $lmm_options['defaults_marker_popups_autopan'] ?>, closeButton: <?php echo $lmm_options['defaults_marker_popups_closebutton'] ?>, autoPanPadding: new L.Point(<?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_x']) ?>, <?php echo intval($lmm_options['defaults_marker_popups_autopanpadding_y']) ?>)})<?php  if ($openpopup == 1) { echo '.openPopup()'; } ?>;
		//info: load wms layer when checkbox gets checked
		$('#wmscheckboxes input:checkbox').click(function(el) {
			if(el.target.checked) {
				selectlayer.addLayer(window[el.target.id]);
			} else {
				selectlayer.removeLayer(window[el.target.id]);
			}

		});
		//info: update basemap when chosing from control box
		selectlayer.on('layeradd', function(e) {
		if (e.layer.options != undefined) { //needed for gpx
			if(e.layer.options.mmid) {
				selectlayer.attributionControl._attributions = [];
				$('#basemap').val(e.layer.options.mmid);
			}
		}
		});
		//info: when custom overlay gets checked from control box update hidden field
		selectlayer.on('layeradd', function(e) {
		if (e.layer.options != undefined) { //needed for gpx
			if(e.layer.options.olid) {
				$('#'+e.layer.options.olid).attr('value', '1');
			}
		}
		});
		//info: when custom overlay gets unchecked from control box update hidden field
		selectlayer.on('layerremove', function(e) {
			if(e.layer.options.olid) {
				$('#'+e.layer.options.olid).attr('value', '0');
			}
		});
		selectlayer.on('moveend', function(e) { document.getElementById('zoom').value = selectlayer.getZoom();});
		selectlayer.on('click', function(e) {
		  selectlayer.setView(e.latlng,selectlayer.getZoom());
		  document.getElementById('lat').value = e.latlng.lat.toFixed(6);
		  document.getElementById('lon').value = e.latlng.lng.toFixed(6);
		  marker.setLatLng(e.latlng);
		<?php 
		if ($popuptext != NULL) { 
			if ($lmm_options['directions_popuptext_panel'] == 'yes') {
				echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML+document.getElementById("selectlayer-popuptext-dlink-hidden").innerHTML,';
			} else {
				echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML,';
			}
			echo '{maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})';
			if ($openpopup == 1) { echo '.openPopup()'; }
		} 
		?>
		});
		var mapElement = $('#selectlayer'), mapWidth = $('#mapwidth'), mapHeight = $('#mapheight'), popupText = $('#popuptext'), lat = $('#lat'), lon = $('#lon'), panel = $('#lmm-panel'), gpxpanel = $('#gpx-panel-selectlayer'), gpxpanelcheckbox = $('#gpx_panel'), lmm = $('#lmm'), gpx_fitbounds_link = $('#gpx_fitbounds_link'), markername = $('#markername'), zoom = $('#zoom');
		//info: bugfix causing maps not to show up in WP 3.0 and errors in WP <3.3
		<?php if ( version_compare( $wp_version, '3.3', '>=' ) ) { ?>
		//info: change zoom level when changing form field
		zoom.on('blur', function(e) {
			if(isNaN(zoom.val())) {
					alert('<?php esc_attr_e('Invalid format! Please only use numbers!','lmm') ?>');
			} else {
			selectlayer.setZoom(zoom.val());
			}
		});
		markername.on('blur', function(e) {
			if( markername.val() ){
				document.getElementById('lmm-panel-text').innerHTML = markername.val();
			} else {
				document.getElementById('lmm-panel-text').innerHTML = '&nbsp;';
			};
		});
		<?php } ?>
		mapWidth.blur(function() {
			if(!isNaN(mapWidth.val())) {
				lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				selectlayer.invalidateSize();
			}
		});
		$('input:radio[name=mapwidthunit]').click(function() {
				lmm.css("width",mapWidth.val()+$('input:radio[name=mapwidthunit]:checked').val());
				selectlayer.invalidateSize();
		});
		mapHeight.blur(function() {
			if(!isNaN(mapHeight.val())) {
				mapElement.css("height",mapHeight.val()+"px");
				selectlayer.invalidateSize();
			}
		});
		//info: show/hide panel for markername & API URLs
		$('input:checkbox[name=panel]').click(function() {
			if($('input:checkbox[name=panel]').is(':checked')) {
				panel.css("display",'block');
			} else {
				panel.css("display",'none');
			}
		});
		<?php //info: upload to media library
		$gpx_track_color = '#' . str_replace('#', '', $lmm_options['gpx_track_color']);
		$gpx_startIconUrl = ($lmm_options['gpx_startIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-start.png' : trim($lmm_options['gpx_startIconUrl']);
		$gpx_endIconUrl = ($lmm_options['gpx_endIconUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-end.png' : trim($lmm_options['gpx_endIconUrl']);
		$gpx_shadowUrl = ($lmm_options['gpx_shadowUrl'] == NULL) ? LEAFLET_PLUGIN_URL . 'leaflet-dist/images/gpx-icon-shadow.png' : trim($lmm_options['gpx_shadowUrl']);
		if ( (isset($lmm_options[ 'gpx_metadata_name' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_name' ] == 1 ) ) {
				$gpx_metadata_name_js = 'if (gpx.get_name() != undefined) { _c("gpx-name").innerHTML = gpx.get_name(); } else { _c("gpx-name").innerHTML = "n/a"; }';
		} else { $gpx_metadata_name_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_start' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_start' ] == 1 ) ) {
			$gpx_metadata_start_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-start").innerHTML = gpx.get_start_time().toDateString() + ", " + gpx.get_start_time().toLocaleTimeString(); } else { _c("gpx-start").innerHTML = "n/a"; }';
		} else { $gpx_metadata_start_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_end' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_end' ] == 1 ) ) {
			$gpx_metadata_end_js = 'if (gpx.get_end_time() != undefined) { _c("gpx-end").innerHTML = gpx.get_end_time().toDateString() + ", " + gpx.get_end_time().toLocaleTimeString(); } else { _c("gpx-end").innerHTML = "n/a"; }';
		} else { $gpx_metadata_end_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_distance' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_distance' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = (gpx.get_distance()/1000).toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			} else {
				$gpx_metadata_distance_js = 'if (gpx.get_distance() != "0") { _c("gpx-distance").innerHTML = gpx.get_distance_imp().toFixed(2); } else { _c("gpx-distance").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_distance_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_moving' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_moving' ] == 1 ) ) {
			$gpx_metadata_duration_moving_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-moving").innerHTML = gpx.get_duration_string(gpx.get_moving_time()); } else { _c("gpx-duration-moving").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_moving_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_duration_total' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_duration_total' ] == 1 ) ) {
			$gpx_metadata_duration_total_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-duration-total").innerHTML = gpx.get_duration_string(gpx.get_total_time()); } else { _c("gpx-duration-total").innerHTML = "n/a"; }';
		} else { $gpx_metadata_duration_total_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avpace' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avpace' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			} else {
			$gpx_metadata_avpace_js = 'if (gpx.get_start_time() != undefined) { _c("gpx-avpace").innerHTML = gpx.get_duration_string(gpx.get_moving_pace_imp(), true); } else { _c("gpx-avpace").innerHTML = "n/a"; }';
			}
		} else { $gpx_metadata_avpace_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_avhr' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_avhr' ] == 1 ) ) {
			$gpx_metadata_avhr_js = 'if (isNaN(gpx.get_average_hr())) { _c("gpx-avghr").innerHTML = "n/a"; } else { _c("gpx-avghr").innerHTML = gpx.get_average_hr() + "bpm"; }';
		} else { $gpx_metadata_avhr_js = ''; }
		if ( ((isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 )) || ((isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 )) ) {
			$gpx_metadata_elevation_title_js = '';
		} else { $gpx_metadata_elevation_title_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_gain' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_gain' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.get_elevation_gain().toFixed(0);';
			} else {
				$gpx_metadata_elev_gain_js = '_c("gpx-elevation-gain").innerHTML = gpx.to_ft(gpx.get_elevation_gain()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_gain_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_loss' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_loss' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_loss_js = '_c("gpx-elevation-loss").innerHTML = gpx.to_ft(gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_loss_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_net' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_net' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.get_elevation_gain().toFixed(0) - gpx.get_elevation_loss().toFixed(0);';
			} else {
				$gpx_metadata_elev_net_js = '_c("gpx-elevation-net").innerHTML  = gpx.to_ft(gpx.get_elevation_gain() - gpx.get_elevation_loss()).toFixed(0);';
			}
		} else { $gpx_metadata_elev_net_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_elev_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_elev_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data();';
			} else {
				$gpx_metadata_elev_full_js = '_c("gpx-elevation-full").innerHTML    = gpx.get_elevation_data_imp();';
			}
		} else { $gpx_metadata_elev_full_js = ''; }
		if ( (isset($lmm_options[ 'gpx_metadata_hr_full' ]) == TRUE ) && ($lmm_options[ 'gpx_metadata_hr_full' ] == 1 ) ) {
			if ($lmm_options[ 'gpx_metadata_units' ] == 'metric') {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data();';
			} else {
				$gpx_metadata_hr_full_js = '_c("gpx-heartrate-full").innerHTML    = gpx.get_heartrate_data_imp();';
			}
		} else { $gpx_metadata_hr_full_js = ''; }

		//info: transient for gpx proxy
		$transient_proxy = get_transient( 'leafletmapsmarkerpro_transient_proxy' );
		if ( $transient_proxy === FALSE ) {
			$rand_number = substr(md5('123'.rand()), 0, 8);
			set_transient( 'leafletmapsmarkerpro_transient_proxy', $rand_number, 60*10 );
			$transient_proxy = get_transient( 'leafletmapsmarkerpro_transient_proxy' );
		}

		if ( version_compare( $wp_version, '3.5', '>=' ) ) {
			echo "var custom_uploader;
			$('#upload_gpx_file').click(function(e) {
				e.preventDefault();
				if (custom_uploader) {
					custom_uploader.open();
					return;
				}
				custom_uploader = wp.media.frames.file_frame = wp.media({
					title: '" . esc_attr__('Upload GPX track','lmm') . "',
					frame: 'select',
					library: { type: 'text/gpx' },
					button: {
						text: '" . esc_attr__('Insert GPX track','lmm') . "'
					},
					multiple: false
				});
				//info: when a file is selected, grab the URL and set it as the text field's value
				custom_uploader.on('select', function() {
					attachment = custom_uploader.state().get('selection').first().toJSON();
					$('#gpx_url').val(attachment.url);
					gpxpanelcheckbox.attr('checked','checked');
					gpxpanel.css('display','block');
					gpx_fitbounds_link.css('display','inline');

					$.ajax({
						url: '" . LEAFLET_PLUGIN_URL . "inc/proxy.php?url='+attachment.url+'&transient=" . $transient_proxy . "',
						dataType: 'text',
						type: 'POST'
					}).done(function(data) {
							var gpx_panel = document.getElementById('gpx-panel-selectlayer');
								function _c(c) { return gpx_panel.querySelectorAll('.'+c)[0]; }
							var gpx_track = new L.GPX(attachment.url, {
								gpx_content: data,
								async: true,
								max_point_interval: " .  intval($lmm_options['gpx_max_point_interval']) . ",
								marker_options: {
									startIconUrl: '" . $gpx_startIconUrl . "',
									endIconUrl: '" . $gpx_endIconUrl . "',
									shadowUrl: '" . $gpx_shadowUrl . "',
									iconSize: [" .  $lmm_options['gpx_iconSize_x'] . ", " .  $lmm_options['gpx_iconSize_y'] . "],
									shadowSize: [" .  $lmm_options['gpx_shadowSize_x'] . ", " .  $lmm_options['gpx_shadowSize_y'] . "],
									iconAnchor: [" .  $lmm_options['gpx_iconAnchor_x'] . ", " .  $lmm_options['gpx_iconAnchor_y'] . "],
									shadowAnchor: [" .  $lmm_options['gpx_shadowAnchor_x'] . ", " .  $lmm_options['gpx_shadowAnchor_y'] . "],
									className: 'lmm_gpx_icons'
								},
								polyline_options: {
									color: '" . $gpx_track_color . "',
									weight: " . intval($lmm_options['gpx_track_weight']) . ",
									opacity: " . floatval($lmm_options['gpx_track_opacity']) . ",
									smoothFactor: " . floatval($lmm_options['gpx_track_smoothFactor']) . ",
									clickable: " . $lmm_options['gpx_track_clickable'] . ",
									noClip: " . $lmm_options['gpx_track_noClip'] . "
								},
								}).addTo(selectlayer);
							gpx_track.on('gpx_loaded', function(e) {
									var gpx = e.target;
									selectlayer.fitBounds(e.target.getBounds(), { padding: [25,25] });
									" . $gpx_metadata_name_js . "
									" . $gpx_metadata_start_js . "
									" . $gpx_metadata_end_js . "
									" . $gpx_metadata_distance_js . "
									" . $gpx_metadata_duration_moving_js . "
									" . $gpx_metadata_duration_total_js . "
									" . $gpx_metadata_avpace_js . "
									" . $gpx_metadata_avhr_js . "
									" . $gpx_metadata_elev_gain_js . "
									" . $gpx_metadata_elev_loss_js . "
									" . $gpx_metadata_elev_net_js . "
									" . $gpx_metadata_elev_full_js . "
									" . $gpx_metadata_hr_full_js . "
							});
						});
				});
				custom_uploader.open();
			});";
		} else { //info: WP <3.5
			echo "jQuery(document).ready(function() {
				jQuery('#upload_gpx_file').click(function() {
					formfield = jQuery('#gpx_url').attr('name');
					tb_show('', 'media-upload.php?tab=library&post_mime_type=text%2Fgpx&amp;TB_iframe=true');
					jQuery('#TB_overlay').css('z-index','1000');
					jQuery('#TB_window').css('z-index','10000');
					return false;
				});
				window.send_to_editor = function(html) {
					gpxurl = jQuery(html).attr('href');
					jQuery('#gpx_url').val(gpxurl);
					tb_remove();
				}
				 });";
		} ?>

		//info: show/hide gpx panel
		$('input:checkbox[name=gpx_panel]').click(function() {
			if($('input:checkbox[name=gpx_panel]').is(':checked')) {
				gpxpanel.css("display",'block');
			} else {
				gpxpanel.css("display",'none');
			}
		});
		//info: show fitbounds link on focus
		$('#gpx_url').focus(function() {
			gpx_fitbounds_link.css("display",'inline');
		});		
		//info: fit gpx map bounds on click
		$('.gpxfitbounds').click(function(e){
			var current_gpx_url = $('#gpx_url').val();
			$.ajax({
				url: '<?php echo LEAFLET_PLUGIN_URL; ?>inc/proxy.php?url='+current_gpx_url+'&transient=<?php echo $transient_proxy; ?>',
				dataType: 'text',
				type: 'POST'
			}).done(function(data) {

				var gpx_panel = document.getElementById('gpx-panel-selectlayer');
				function _c(c) { return gpx_panel.querySelectorAll('.'+c)[0]; }
				var gpx_track = new L.GPX(gpx_url, {
					gpx_content: data,
					async: true,
					max_point_interval: <?php echo intval($lmm_options['gpx_max_point_interval']); ?>,
					marker_options: {
						startIconUrl: "<?php echo $gpx_startIconUrl; ?>",
						endIconUrl: "<?php echo $gpx_endIconUrl; ?>",
						shadowUrl: "<?php echo $gpx_shadowUrl; ?>",
						iconSize: [<?php echo $lmm_options['gpx_iconSize_x']; ?>, <?php echo $lmm_options['gpx_iconSize_y']; ?>],
						shadowSize: [<?php echo $lmm_options['gpx_shadowSize_x']; ?>, <?php echo $lmm_options['gpx_shadowSize_y']; ?>],
						iconAnchor: [<?php echo $lmm_options['gpx_iconAnchor_x']; ?>, <?php echo $lmm_options['gpx_iconAnchor_y']; ?>],
						shadowAnchor: [<?php echo $lmm_options['gpx_shadowAnchor_x']; ?>, <?php echo $lmm_options['gpx_shadowAnchor_y']; ?>],
						className: 'lmm_gpx_icons'
					},
					polyline_options: {
						color: "<?php echo $gpx_track_color; ?>",
						weight: <?php echo intval($lmm_options['gpx_track_weight']); ?>,
						opacity: <?php echo floatval($lmm_options['gpx_track_opacity']); ?>,
						smoothFactor: <?php echo floatval($lmm_options['gpx_track_smoothFactor']); ?>,
						clickable: <?php echo $lmm_options['gpx_track_clickable']; ?>,
						noClip: <?php echo $lmm_options['gpx_track_noClip']; ?>
					},
					}).addTo(selectlayer);

					gpx_track.on('gpx_loaded', function(e) {
						var gpx = e.target;
						selectlayer.fitBounds(e.target.getBounds(), { padding: [25,25] } );
						<?php echo $gpx_metadata_name_js; ?>
						<?php echo $gpx_metadata_start_js; ?>
						<?php echo $gpx_metadata_end_js; ?>
						<?php echo $gpx_metadata_distance_js; ?>
						<?php echo $gpx_metadata_duration_moving_js; ?>
						<?php echo $gpx_metadata_duration_total_js; ?>
						<?php echo $gpx_metadata_avpace_js; ?>
						<?php echo $gpx_metadata_avhr_js; ?>
						<?php echo $gpx_metadata_elev_gain_js; ?>
						<?php echo $gpx_metadata_elev_loss_js; ?>
						<?php echo $gpx_metadata_elev_net_js; ?>
						<?php echo $gpx_metadata_elev_full_js; ?>
						<?php echo $gpx_metadata_hr_full_js; ?>
					});
			});
		});
		//info: open/close popup
		$('input:checkbox[name=openpopup]').click(function() {
			if($('input:checkbox[name=openpopup]').is(':checked')) {
				marker.openPopup();
			} else {
				marker.closePopup();
			}
		});
		//info: check if lat is a number
		$('input:text[name=lat]').blur(function(e) {
			if(isNaN(lat.val())) {
					alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
			}
		});
		//info: check if lon is a number
		$('input:text[name=lon]').blur(function(e) {
			if(isNaN(lon.val())) {
					alert('<?php esc_attr_e('Invalid format! Please only use numbers and a . instead of a , as decimal separator!','lmm') ?>');
			}
		});
		//info: dynamic update of control box status
		$('input:radio[name=controlbox]').click(function() {
			if($('input:radio[name=controlbox]:checked').val() == 0) {
				$('.leaflet-control-layers').hide();
			}
			if($('input:radio[name=controlbox]:checked').val() == 1) {
				$('.leaflet-control-layers').show();
				layersControl._collapse();
			}
			if($('input:radio[name=controlbox]:checked').val() == 2) {
				$('.leaflet-control-layers').show();
				layersControl._expand();
			}
		});
		//info: show all API links on click on simplified editor
		$('#apilinkstext').click(function(e) {
				$('#apilinkstext').hide();
				$('#apilinks').show('fast');
		});
		//info: show all icons on click on simplified editor
		$('#moreiconslink').click(function(e) {
				$('#moreiconslink').hide();
				$('#moreicons').show('fast');
				$('#mapiconscollection').show('fast');
		});
		//info: show less icons on click on simplified editor
		$('#showlessicons').click(function(e) {
				$('#moreicons').hide();
				$('#mapiconscollection').hide();
				$('#moreiconslink').show('fast');
		});
		//info: sets map center to new marker position when entering lat/lon manually
		$('input:text[name=lat],input:text[name=lon]').blur(function(e) {
			var markerLocation = new L.LatLng(lat.val(),lon.val());
			marker.closePopup();
			marker.setLatLng(markerLocation);
			selectlayer.setView(markerLocation, selectlayer.getZoom());
			if($('input:radio[name=openpopup]:checked').val() == 1) {
				marker.openPopup();
			}
		});
	})(jQuery)
	//info: update marker icon upon click
	function updateicon(newicon) {
		if(newicon) {
			marker.setIcon(new L.Icon({iconUrl: '<?php echo $defaults_marker_icon_url . '/' ?>' + newicon,iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],shadowUrl: '<?php echo $marker_shadow_url; ?>',shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],className: 'lmm_marker_icon_default'}));
		}
		if(!newicon) {
			marker.setIcon(new L.Icon({iconUrl: '<?php echo LEAFLET_PLUGIN_URL . '/leaflet-dist/images/marker.png' ?>',iconSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconsize_y' ]); ?>],iconAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_iconanchor_y' ]); ?>],popupAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_popupanchor_y' ]); ?>],shadowUrl: '<?php echo $marker_shadow_url; ?>',shadowSize: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowsize_y' ]); ?>],shadowAnchor: [<?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_x' ]); ?>, <?php echo intval($lmm_options[ 'defaults_marker_icon_shadowanchor_y' ]); ?>],className: 'lmm_marker_icon_<?php echo substr($icon, 0, -4); ?>'}));
		}
	}
	//info: Google address autocomplete
	gLoader = function(){
		function initAutocomplete() {
			var input = document.getElementById('address');
			<?php if ($lmm_options[ 'google_places_bounds_status' ] == 'enabled') { ?>
			var defaultBounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(<?php echo floatval($lmm_options[ 'google_places_bounds_lat1' ]) ?>, <?php echo floatval($lmm_options[ 'google_places_bounds_lon1' ]) ?>),
				new google.maps.LatLng(<?php echo floatval($lmm_options[ 'google_places_bounds_lat2' ]) ?>, <?php echo floatval($lmm_options[ 'google_places_bounds_lon2' ]) ?>));
			<?php }?>
			var autocomplete = new google.maps.places.Autocomplete(input<?php if ($lmm_options[ 'google_places_bounds_status' ] == 'enabled') { echo ', {bounds: defaultBounds}'; } ?>);
			input.onfocus = function(){
			<?php if ($lmm_options[ 'google_places_search_prefix_status' ] == 'enabled' ) { ?>
			input.value = "<?php echo addslashes($lmm_options[ 'google_places_search_prefix' ]); ?>";
			<?php } ?>
			};
			google.maps.event.addListener(autocomplete, 'place_changed', function() {
				var place = autocomplete.getPlace();
				var map = selectlayer;
				var markerLocation = new L.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
				marker.setLatLng(markerLocation);
				map.setView(markerLocation, selectlayer.getZoom());
				document.getElementById('lat').value = place.geometry.location.lat().toFixed(6);
				document.getElementById('lon').value = place.geometry.location.lng().toFixed(6);
				
				<?php 
				if ($popuptext != NULL) { 
					if ($lmm_options['directions_popuptext_panel'] == 'yes') {
						echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML+document.getElementById("selectlayer-popuptext-dlink-hidden").innerHTML,';
					} else {
						echo 'marker.bindPopup(document.getElementById("selectlayer-popuptext-hidden").innerHTML,';
					}
					echo '{maxWidth: ' . intval($lmm_options['defaults_marker_popups_maxwidth']) . ', minWidth: ' . intval($lmm_options['defaults_marker_popups_minwidth']) . ', maxHeight: ' . intval($lmm_options['defaults_marker_popups_maxheight']) . ', autoPan: ' . $lmm_options['defaults_marker_popups_autopan'] . ', closeButton: ' . $lmm_options['defaults_marker_popups_closebutton'] . ', autoPanPadding: new L.Point(' . intval($lmm_options['defaults_marker_popups_autopanpadding_x']) . ', ' . intval($lmm_options['defaults_marker_popups_autopanpadding_y']) . ')})';
					if ($openpopup == 1) { echo '.openPopup()'; }
				} 
				?>
			 });
			var input = document.getElementById('address');
			google.maps.event.addDomListener(input, 'keydown',
			function(e) {
							if (e.keyCode == 13) {
											if (e.preventDefault) {
															e.preventDefault();
											} else { //info:  Since the google event handler framework does not handle early IE versions, we have to do it by our self. :-(
															e.cancelBubble = true;
															e.returnValue = false;
											}
							}
			});
		}
		return{
		autocomplete:initAutocomplete
		}
	}();
	gLoader.autocomplete();
	/* //]]> */
	</script>
<?php 
	} //info: check if marker exists - part 2 
} //info: !empty($action) 3/3
include('inc' . DIRECTORY_SEPARATOR . 'admin-footer.php');
?>