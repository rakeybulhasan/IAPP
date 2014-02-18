<?php
/*
    Import/Export Standalone - Leaflet Maps Marker Plugin
	Check cell data type: http://phpexcel.codeplex.com/discussions/403466
*/
//info: construct path to wp-load.php
while(!is_file('wp-load.php')) {
	if(is_dir('..' . DIRECTORY_SEPARATOR)) chdir('..' . DIRECTORY_SEPARATOR);
	else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
//info: check if plugin is active (didnt use is_plugin_active() due to problems reported by users)
function lmm_is_plugin_active( $plugin ) {
	$active_plugins = get_option('active_plugins');
	$active_plugins = array_flip($active_plugins);
	if ( isset($active_plugins[$plugin]) || lmm_is_plugin_active_for_network( $plugin ) ) { return true; }
}
function lmm_is_plugin_active_for_network( $plugin ) {
	if ( !is_multisite() )
		return false;
	$plugins = get_site_option( 'active_sitewide_plugins');
	if ( isset($plugins[$plugin]) )
				return true;
	return false;
}
if (!lmm_is_plugin_active('leaflet-maps-marker-pro/leaflet-maps-marker.php') ) {
	echo sprintf(__('The plugin "Leaflet Maps Marker" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), antispambot(get_bloginfo('admin_email')) );
} else {
	$import_export_standalone_nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
	if (! wp_verify_nonce($import_export_standalone_nonce, 'import-export-standalone-nonce') ) die("".__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm')."");

	global $wpdb, $current_user;
	$table_name_markers = $wpdb->prefix.'leafletmapsmarker_markers';
	$table_name_layers = $wpdb->prefix.'leafletmapsmarker_layers';
	$lmm_options = get_option( 'leafletmapsmarker_options' );
	$action_iframe = isset($_GET['action_iframe']) ? $_GET['action_iframe'] : '';
	$action_standalone  = isset($_POST['action_standalone']) ? $_POST['action_standalone'] : '';

	//info: set custom marker icon dir/url
	if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
		$defaults_marker_icon_dir = LEAFLET_PLUGIN_ICONS_DIR;
		$defaults_marker_icon_url = LEAFLET_PLUGIN_ICONS_URL;
	} else {
		$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
		$defaults_marker_icon_url = htmlspecialchars($lmm_options['defaults_marker_icon_url']);
	}
	require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PHPExcel.php';
	if ($action_standalone == NULL) {
		echo '<!DOCTYPE html>
				<head>
				<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
				<title>Import/Export for Leaflet Maps Marker Pro</title>
				<style type="text/css">
					body { font-family: sans-serif;	padding:0 0 0 5px; margin:0px; font-size: 12px;	line-height: 1.4em; }
					a {	color: #21759B;	text-decoration: none; }
					a:hover, a:active, a:focus { color: #D54E21; }
					td {padding:5px 5px 5px 0;}
					error { font-weight:bold;color:red; }
				</style>
				</head>
				<body><p style="margin:0.5em 0 0 0;">';

		//info: get available caching methods for import/export prepare forms
		if ( function_exists('sqlite_open') ){ //info: SQLite2
			$caching_sqlite2_disabled = '';
			$caching_sqlite2_disabled_css = '';
		} else {
			$caching_sqlite2_disabled = 'disabled="disabled"';
			$caching_sqlite2_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( class_exists('SQLite3',FALSE) === TRUE ) { //info:SQLite3
			$caching_sqlite3_disabled = '';
			$caching_sqlite3_disabled_css = '';
		} else {
			$caching_sqlite3_disabled = 'disabled="disabled"';
			$caching_sqlite3_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( function_exists('apc_store') && (apc_sma_info() === TRUE) ) { //info: APC
			$caching_apc_disabled = '';
			$caching_apc_disabled_css = '';
		} else {
			$caching_apc_disabled = 'disabled="disabled"';
			$caching_apc_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( function_exists('memcache_add') ) { //info: Memcache
			$caching_memcache_disabled = '';
			$caching_memcache_disabled_css = '';
		} else {
			$caching_memcache_disabled = 'disabled="disabled"';
			$caching_memcache_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( function_exists('wincache_ucache_add') ) { //info: Wincache
			$caching_wincache_disabled = '';
			$caching_wincache_disabled_css = '';
		} else {
			$caching_wincache_disabled = 'disabled="disabled"';
			$caching_wincache_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip) === TRUE ) { //info: MemoryGZip
			$caching_memorygzip_disabled = '';
			$caching_memorygzip_disabled_css = '';
		} else {
			$caching_memorygzip_disabled = 'disabled="disabled"';
			$caching_memorygzip_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_discISAM) === TRUE ) { //info: DiscISAM
			$caching_discisam_disabled = '';
			$caching_discisam_disabled_css = '';
		} else {
			$caching_discisam_disabled = 'disabled="disabled"';
			$caching_discisam_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp) === TRUE ) { //info: PHPTemp
			$caching_phptemp_disabled = '';
			$caching_phptemp_disabled_css = '';
		} else {
			$caching_phptemp_disabled = 'disabled="disabled"';
			$caching_phptemp_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( function_exists('igbinary_serialize') ) { //info: Igbinary
			$caching_igbinary_disabled = '';
			$caching_igbinary_disabled_css = '';
		} else {
			$caching_igbinary_disabled = 'disabled="disabled"';
			$caching_igbinary_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}
		if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized) === TRUE ) { //info: MemorySerialized
			$caching_memoryserialized_disabled = '';
			$caching_memoryserialized_disabled_css = '';
		} else {
			$caching_memoryserialized_disabled = 'disabled="disabled"';
			$caching_memoryserialized_disabled_css = 'style="color:#CCCCCC;" title="' . esc_attr__('this caching method is currently not available on your server','lmm') . '"';
		}

		if ($action_iframe == 'import') {
			/**********************************
			*         import form             *
			**********************************/
			echo '<table><tr><td><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-import.png" width="32" height="32" alt="import"></td>';
			echo '<td><h3 style="font-size:20px;margin:0px;"> ' . __('prepare import','lmm') . '</h3></td></tr></table>';
			echo '
			<script>
			function lmm_check_file_extension()	{
				str=document.getElementById("import-file").value.toUpperCase();
				suffix=".CSV";
				suffix2=".XLS";
				suffix3=".XLSX";
				suffix4=".ODS";
				if(!(str.indexOf(suffix, str.length - suffix.length) !== -1
							|| str.indexOf(suffix2, str.length - suffix2.length) !== -1
							|| str.indexOf(suffix3, str.length - suffix3.length) !== -1
							|| str.indexOf(suffix4, str.length - suffix4.length) !== -1)
					){
					alert("' . sprintf(esc_attr__('Error: file type not allowed - allowed file types: %1$s','lmm'), 'csv, xls, xlsx, ods') . '");
					document.getElementById("import-file").value="";
				}
			}
			</script>
			<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="action_standalone" value="import" />
			<table>
				<tr>
					<td colspan="2">
					' . __('Download import template files','lmm') . ': ';
					if (extension_loaded('zip')) {
						echo '<a href="http://www.mapsmarker.com/import-template-xlsx">.xlsx (Excel2007)</a>, <a href="http://www.mapsmarker.com/import-template-xls">.xls (Excel5)</a>, <a href="http://www.mapsmarker.com/import-template-ods">.ods (OpenOffice/LibreOffice)</a>, <a href="http://www.mapsmarker.com/import-template-csv">.csv</a><br/>';
					} else {
						echo '<a href="http://www.mapsmarker.com/import-template-xls">.xls (Excel5)</a>, <a href="http://www.mapsmarker.com/import-template-csv">.csv</a><br/>';
					}
					echo '<a href="javascript:back();">' . __('If you want to bulk update existing markers, please make an export first!','lmm') . '</a>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Please select import file','lmm') . '</td>
					<td>
						<input id="import-file" name="import-file" type="file" size="50" onchange="lmm_check_file_extension()" /><br/>';
						if (extension_loaded('zip')) {
							echo sprintf(__('supported formats: %1$s','lmm'), 'xlsx, xls, ods, csv') . ' ' . __('(with semicolons as delimiters)','lmm');
						} else {
							echo sprintf(__('supported formats: %1$s','lmm'), 'xls, csv') . ' ' . __('(with semicolons as delimiters)','lmm') . '<br/>';
							echo ' <span style="background:yellow;padding:2px;">' . __('The PHP extension php_zip is not enabled on your server - this means that .xlsx or .ods files cannot be handled. Please contact your admin for more details.','lmm') . '</span>';
						}
					echo '</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which geocoding option should be used?','lmm') . '</td>
					<td>
						<input id="geocoding-on" type="radio" name="geocoding-option" value="geocoding-on" checked="checked" /> <label for="geocoding-on"> ' . __('use address for geocoding (latitude and longitude values will get overwritten by geocoding results)','lmm') . '</label><br/>
						<input id="geocoding-off" type="radio" name="geocoding-option" value="geocoding-off" /> <label for="geocoding-off"> ' . __('do not use address for geocoding (address, latitude and longitude values will be imported as given)','lmm') . '</label><br/>

						<p id="show-more-gmapsbusiness" style="margin:5px 0 0 24px;"><a href="#" onclick="document.getElementById(\'gmapsbusiness-more-options\').style.display = \'block\';document.getElementById(\'show-more-gmapsbusiness\').style.display = \'none\';">' . sprintf(__('Please note: Google Maps API allows up to %1$s geocoding requests per day and IP-address! Click here if you have a Google Maps API for Business account which allows up to %2$s geocoding requests per day','lmm'), '2.500', '100.000') . '</a></p>
						<div id="gmapsbusiness-more-options" style="display:none;">
						<p style="margin:5px 0 0 24px;">
						' . sprintf(__('To use your <a href="%1$s" target="_blank">Google Maps API for business</a>-account, please fill in the fields below - more details at %2$s','lmm'), 'http://www.google.com/enterprise/mapsearth/products/mapsapi.html?rd=1#','<a href="https://developers.google.com/maps/documentation/business/webservices/auth" target="_blank">https://developers.google.com/maps/documentation/business/webservices/auth</a>') . '<br/>
						</p>
						<p style="margin:5px 0 0 24px;"><label for="gmapsbusiness-client" style="margin-right:12px;">client ID</label> <input id="gmapsbusiness-client" type="input" name="gmapsbusiness-client" value="" style="width:250px;" /></label></span></p>
						<p style="margin:5px 0 0 24px;"><label for="gmapsbusiness-signature" style="margin-right:4px;">signature</label> <input id="gmapsbusiness-signature" type="input" name="gmapsbusiness-signature" value="" style="width:250px;" /></label></span></p>
						<p style="margin:5px 0 0 24px;"><label for="gmapsbusiness-channel" style="margin-right:12px;">channel</label> <input id="gmapsbusiness-channel" type="input" name="gmapsbusiness-channel" value="" style="width:250px;" /></label></span></p>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which audit option should be used?','lmm') . '</td>
					<td>
						<input id="audit-on" type="radio" name="audit-option" value="audit-on" checked="checked" /> <label for="audit-on"> ' . sprintf(__('use current userlogin (%1$s) and current timestamp for createdby/createdon on new markers and updatedby/updatedon on marker updates','lmm'), $current_user->user_login) . '</label><br/>
						<input id="audit-off" type="radio" name="audit-option" value="audit-off" /> <label for="audit-off"> ' . __('import values for createdby/createdon and updatedby/updatedon as given (no changes will be made to import file)','lmm') . '</label>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which caching method should be used?','lmm') . '</td>
					<td>
						<input id="caching-auto" type="radio" name="caching-method" value="auto" checked="checked" /> <label for="caching-auto">' . __('automatic','lmm') . '</label>

						<a href="#" id="show-more-link" onclick="document.getElementById(\'caching-options-more\').style.display = \'block\';document.getElementById(\'show-more-link\').style.display = \'none\';"> - ' . __('show more options','lmm') . '</a>
						<div id="caching-options-more" style="display:none;">
						<span ' . $caching_sqlite2_disabled_css . '><input id="caching-sqlite2" type="radio" name="caching-method" value="sqlite2" ' . $caching_sqlite2_disabled . ' /> <label for="caching-sqlite2">SQLite2 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('low','lmm')) . ')</label></span><br/>

						<span ' . $caching_sqlite3_disabled_css . '><input id="caching-sqlite3" type="radio" name="caching-method" value="sqlite3" ' . $caching_sqlite3_disabled . ' /> <label for="caching-sqlite3">SQLite3 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('very low','lmm')) . ')</label></span><br/>

						<span ' . $caching_apc_disabled_css . '><input id="caching-apc" type="radio" name="caching-method" value="apc" ' . $caching_apc_disabled . ' /> <label for="caching-apc">APC <a href="http://pecl.php.net/package/APC" title="http://pecl.php.net/package/APC" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-apc-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-apc-timeout" type="input" name="caching-apc-timeout" value="600" style="width:30px;" ' . $caching_apc_disabled . ' /></label></span><br/>

						<span ' . $caching_memcache_disabled_css . '><input id="caching-memcache" type="radio" name="caching-method" value="memcache" ' . $caching_memcache_disabled . ' /> <label for="caching-memcache">Memcache <a href="http://memcached.org/" title="http://memcached.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-memcache-server" style="margin-left:24px;">' . __('server','lmm') . ' </label> <input id="caching-memcache-server" type="input" name="caching-memcache-server" value="localhost" style="width:150px;" ' . $caching_memcache_disabled . ' /></label>
						<label for="caching-memcache-port" style="margin-left:5px;">' . __('port','lmm') . ' </label> <input id="caching-memcache-port" type="input" name="caching-memcache-port" value="11211" style="width:49px;" ' . $caching_memcache_disabled . ' /></label>
						<label for="caching-memcache-timeout" style="margin-left:5px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-memcache-timeout" type="input" name="caching-memcache-timeout" value="600" style="width:31px;" ' . $caching_memcache_disabled . ' /></label></span><br/>

						<span ' . $caching_wincache_disabled_css . '><input id="caching-wincache" type="radio" name="caching-method" value="wincache" ' . $caching_wincache_disabled . ' /> <label for="caching-wincache">Wincache <a href="http://sourceforge.net/projects/wincache/" title="http://sourceforge.net/projects/wincache/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-wincache-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-wincache-timeout" type="input" name="caching-wincache-timeout" value="600" style="width:31px;" ' . $caching_wincache_disabled . ' /></label></span><br/>

						<span ' . $caching_memorygzip_disabled_css . '><input id="caching-memorygzip" type="radio" name="caching-method" value="memorygzip" ' . $caching_memorygzip_disabled . ' /> <label for="caching-memorygzip">MemoryGZIP (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label></span><br/>

						<span ' . $caching_discisam_disabled_css . '><input id="caching-discisam" type="radio" name="caching-method" value="discisam" ' . $caching_discisam_disabled . ' /> <label for="caching-discisam">DiscISAM (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')' . $caching_discisam_disabled . '</label><br/>
						<label for="caching-discisam-directory" style="margin-left:24px;">' . __('custom directory for temp files (instead of php temp directory)','lmm') . ' </label>:<br/>
						<input style="margin-left:24px;width:300px;" id="caching-discisam-directory" type="input" name="caching-discisam-directory" value="" ' . $caching_discisam_disabled . ' /></label></span><br/>

						<span ' . $caching_phptemp_disabled_css . '><input id="caching-phptemp" type="radio" name="caching-method" value="phptemp" ' . $caching_phptemp_disabled . ' /> <label for="caching-phptemp">phpTemp ' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label><br/>
						<label for="caching-phptemp-filesize" style="margin-left:24px;">' . __('maximum temporary file size in MB','lmm') . ' </label> <input id="caching-phptemp-filesize" type="input" name="caching-phptemp-filesize" value="8" style="width:30px;" ' . $caching_phptemp_disabled . ' /></label></span><br/>

						<span ' . $caching_igbinary_disabled_css . '><input id="caching-igbinary" type="radio" name="caching-method" value="igbinary" ' . $caching_igbinary_disabled . ' /> <label for="caching-igbinary">igbinary <a href="http://pecl.php.net/package/igbinary" title="http://pecl.php.net/package/igbinary" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('high','lmm')) . ')</label></span><br/>

						<span ' . $caching_memoryserialized_disabled_css . '><input id="caching-memoryserialized" type="radio" name="caching-method" value="memoryserialized" ' . $caching_memoryserialized_disabled . ' /> <label for="caching-memoryserialized">Memory serialized (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('high','lmm'), __('high','lmm')) . ')' . $caching_memoryserialized_disabled . '</label></span><br/>

						<input id="caching-memory" type="radio" name="caching-method" value="memory" /> <label for="caching-memory">Memory <a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" title="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very high','lmm'), __('very high','lmm')) . ')</label><br/>

						<input type="checkbox" name="setReadDataOnly" id="setReadDataOnly"/> <label for="setReadDataOnly"> ' . __('further reduce memory usage for xlsx/xls/ods input files by only importing linktext for hyperlinks','lmm') . '</a>
						</div>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Test mode','lmm') . '</td>
					<td>
						<input id="test-mode-on" type="radio" name="test-mode" value="test-mode-on" checked="checked" /> <label for="test-mode-on"> ' . __('on (check import file only - no changes will be made to database)','lmm') . '</label><br/>
						<input id="test-mode-off" type="radio" name="test-mode" value="test-mode-off" /> <label for="test-mode-off"> ' . __('off (save changes to database)','lmm') . '</label>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input style="font-weight:bold;" type="submit" name="submit" class="submit button-primary" value="' . esc_attr__('start import','lmm') . '" /></td>
				</tr>
			</table>
			</form>';
		//info: end ($action_iframe == 'import')
		} else if ($action_iframe == 'export') {
			/**********************************
			*         export form             *
			**********************************/
			echo '<table><tr><td><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-export.png" width="32" height="32" alt="export"></td>';
			echo '<td><h3 style="font-size:20px;margin:0px;"> ' . __('prepare export','lmm') . '</h3></td></tr></table>';
			$layerlist = $wpdb->get_results('SELECT id,name,multi_layer_map FROM '.$table_name_layers, ARRAY_A);
			$markercount_all = $wpdb->get_var('SELECT count(*) FROM '.$table_name_markers.'');
			$iconlist = $wpdb->get_results('SELECT distinct(icon) FROM '.$table_name_markers, ARRAY_A);

			if (extension_loaded('zip')) {
				$export_disabled = '';
				$export_disabled_info = '';
			} else {
				$export_disabled = 'disabled="disabled"';
				$export_disabled_info = ' <span style="background:yellow;padding:2px;">' . __('The PHP extension php_zip is not enabled on your server - this means that .xlsx or .ods files cannot be handled. Please contact your admin for more details.','lmm') . '</span>';
			}
			echo '
			<form method="post">
			<input type="hidden" name="action_standalone" value="export" />
			<table>
				<tr>
					<td>' . __('Which markers should be selected?','lmm') . '</td>
					<td>
						<select id="filter-layer" name="filter-layer">
						<option value="select-all">' . sprintf(__('all %1$s markers','lmm'), $markercount_all) . '</option>';
						foreach ($layerlist as $row) {
							$markercount = $wpdb->get_var('SELECT count(*) FROM '.$table_name_layers.' as l INNER JOIN '.$table_name_markers.' AS m ON l.id=m.layer WHERE l.id='.$row['id']);
							if ($row['multi_layer_map'] == 0) {
								echo '<option value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . '>' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('layer','lmm') . ' ID ' . $row['id'] . ' - ' . sprintf(__('%1$s markers','lmm'), $markercount) . ')</option>';
							} else {
								echo '<option title="' . esc_attr__('This is a multi-layer map - markers cannot be exported from this layer directly','lmm') . '" value="' . $row['id'] . '"' . ($row['id'] == $layer ? ' selected="selected"' : '') . ' disabled="disabled">' . stripslashes(htmlspecialchars($row['name'])) . ' (' . __('layer','lmm') . ' ID ' . $row['id'] . '/MLM)</option>';
							}
						}
						echo '
						</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>' . __('How many markers should be exported?','lmm') . '</td>
					<td>
						' . sprintf(__('Please select range - from %1$s to %2$s markers','lmm'), '<input type="text" id="limit-from" name="limit-from" value="0" style="width:50px;" />', '<input type="text" id="limit-to" name="limit-to" value="100" style="width:50px;" />') . '
					</td>
				</tr>
				<tr>
					<td>' . __('Optional 1 - selected markers must have:','lmm') . '</td>
					<td>
						<p style="margin:0;">' . sprintf(__('%1$s in the marker name','lmm'), '<input type="text" id="filter-markername" name="filter-markername" style="width:200px;" />') . '<input id="filter-operator1-and" type="radio" name="filter-operator1" value="AND" checked="checked"/>
						<label for="filter-operator1-and">' . __('and','lmm') . '</label> <input id="filter-operator1-or" type="radio" name="filter-operator1" value="OR" /> <label for="filter-operator1-or">' . __('or','lmm') . '</label>
						' . sprintf(__('%1$s in the popup text','lmm'), ' <input type="text" id="filter-popuptext" name="filter-popuptext" style="width:200px;" />') . '
					</td>
				</tr>
				<tr>
					<td>' . __('Optional 2 - selected markers must NOT have:','lmm') . '</td>
					<td>
						<p style="margin:0;">' . sprintf(__('%1$s in the marker name','lmm'), '<input type="text" id="filter-exclude-markername" name="filter-exclude-markername" style="width:200px;" />') . '<input id="filter-operator2-and" type="radio" name="filter-operator2" value="AND" checked="checked"/>
						<label for="filter-operator2-and">' . __('and','lmm') . '</label> <input id="filter-operator2-or" type="radio" name="filter-operator2" value="OR" /> <label for="filter-operator2-or">' . __('or','lmm') . '</label>
						' . sprintf(__('%1$s in the popup text','lmm'), ' <input type="text" id="filter-exclude-popuptext" name="filter-exclude-popuptext" style="width:200px;" />') . '
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Optional 3 - selected markers must have the following icon:','lmm') . '</td>
					<td>
						<input id="filter-any-icon" type="radio" name="filter-icon" value="icon-any" checked="checked" /> <label for="filter-any-icon">' . __('export markers with any icon','lmm') . '</label> <a href="#" id="show-icons-link" onclick="document.getElementById(\'more-icons\').style.display = \'block\';document.getElementById(\'show-icons-link\').style.display = \'none\';"> - ' . __('show used icons','lmm') . '</a>
						<div id="more-icons" style="display:none;">';
						foreach ($iconlist as $row) {
							if ($row['icon'] == NULL) {
								echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="default_icon"><img src="' . LEAFLET_PLUGIN_URL . 'leaflet-dist/images/marker.png" width="32" height="37" title="' . esc_attr__('filename','lmm') . ': marker.png" alt="default.png" /></label><br/><input id="default_icon" style="margin:1px 0 0 1px;" type="radio" name="filter-icon" value="" /></div>';
							} else {
								echo '<div style="text-align:center;float:left;line-height:0px;margin-bottom:3px;"><label for="'.$row['icon'].'"><img src="' . $defaults_marker_icon_url . '/' . $row['icon'] . '" title="' . esc_attr__('filename','lmm') . ': ' . $row['icon'] . '" alt="' . $row['icon'] . '" width="' . $lmm_options['defaults_marker_icon_iconsize_x'] . '" height="' . $lmm_options['defaults_marker_icon_iconsize_y'] . '" /></label><br/><input id="'.$row['icon'].'" style="margin:1px 0 0 1px;" type="radio" name="filter-icon" value="'.$row['icon'].'"/></div>';
							}
						}
					echo '</div>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which export format should be used?','lmm') . '</td>
					<td>';
						//info: needed if excel2007 is not supported
						if ($export_disabled == NULL) {
							$default_export_format_exel2007 = 'checked="checked"';
							$default_export_format_exel5 = '';
						} else {
							$default_export_format_exel2007 = '';
							$default_export_format_exel5 = 'checked="checked"';
						}
					echo '<input id="export-exel2007" type="radio" name="export-format" value="exel2007" ' . $export_disabled . ' ' . $default_export_format_exel2007 . ' /> <label for="export-exel2007">Excel2007 (.xlsx) - ' . sprintf(__('compatible with OpenOffice %1$s and LibreOffice %2$s','lmm'), '3.0+', '3.6+') . '</label> ' . $export_disabled_info . '<br/>
						<input id="export-excel5" type="radio" name="export-format" value="excel5" ' . $default_export_format_exel5 . ' /> <label for="export-excel5">Excel5 (.xls)</label><br/>
						<input id="export-csv" type="radio" name="export-format" value="csv" /> <label for="export-csv">CSV (.csv)</label>
					</td>
				</tr>
				<tr>
					<td valign="top">' . __('Which caching method should be used?','lmm') . '</td>
					<td>
						<input id="caching-auto" type="radio" name="caching-method" value="auto" checked="checked" /> <label for="caching-auto">' . __('automatic','lmm') . '</label>

						<a href="#" id="show-more-link" onclick="document.getElementById(\'caching-options-more\').style.display = \'block\';document.getElementById(\'show-more-link\').style.display = \'none\';"> - ' . __('show more options','lmm') . '</a>
						<div id="caching-options-more" style="display:none;">
						<span ' . $caching_sqlite2_disabled_css . '><input id="caching-sqlite2" type="radio" name="caching-method" value="sqlite2" ' . $caching_sqlite2_disabled . ' /> <label for="caching-sqlite2">SQLite2 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('low','lmm')) . ')</label></span><br/>

						<span ' . $caching_sqlite3_disabled_css . '><input id="caching-sqlite3" type="radio" name="caching-method" value="sqlite3" ' . $caching_sqlite3_disabled . ' /> <label for="caching-sqlite3">SQLite3 <a href="http://www.sqlite.org/" title="http://www.sqlite.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very low','lmm'), __('very low','lmm')) . ')</label></span><br/>

						<span ' . $caching_apc_disabled_css . '><input id="caching-apc" type="radio" name="caching-method" value="apc" ' . $caching_apc_disabled . ' /> <label for="caching-apc">APC <a href="http://pecl.php.net/package/APC" title="http://pecl.php.net/package/APC" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-apc-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-apc-timeout" type="input" name="caching-apc-timeout" value="600" style="width:30px;" ' . $caching_apc_disabled . ' /></label></span><br/>

						<span ' . $caching_memcache_disabled_css . '><input id="caching-memcache" type="radio" name="caching-method" value="memcache" ' . $caching_memcache_disabled . ' /> <label for="caching-memcache">Memcache <a href="http://memcached.org/" title="http://memcached.org/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-memcache-server" style="margin-left:24px;">' . __('server','lmm') . ' </label> <input id="caching-memcache-server" type="input" name="caching-memcache-server" value="localhost" style="width:150px;" ' . $caching_memcache_disabled . ' /></label>
						<label for="caching-memcache-port" style="margin-left:5px;">' . __('port','lmm') . ' </label> <input id="caching-memcache-port" type="input" name="caching-memcache-port" value="11211" style="width:49px;" ' . $caching_memcache_disabled . ' /></label>
						<label for="caching-memcache-timeout" style="margin-left:5px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-memcache-timeout" type="input" name="caching-memcache-timeout" value="600" style="width:31px;" ' . $caching_memcache_disabled . ' /></label></span><br/>

						<span ' . $caching_wincache_disabled_css . '><input id="caching-wincache" type="radio" name="caching-method" value="wincache" ' . $caching_wincache_disabled . ' /> <label for="caching-wincache">Wincache <a href="http://sourceforge.net/projects/wincache/" title="http://sourceforge.net/projects/wincache/" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('low','lmm'), __('medium','lmm')) . ')<br/>
						<label for="caching-wincache-timeout" style="margin-left:24px;">' . __('timeout in seconds','lmm') . ' </label> <input id="caching-wincache-timeout" type="input" name="caching-wincache-timeout" value="600" style="width:31px;" ' . $caching_wincache_disabled . ' /></label></span><br/>

						<span ' . $caching_memorygzip_disabled_css . '><input id="caching-memorygzip" type="radio" name="caching-method" value="memorygzip" ' . $caching_memorygzip_disabled . ' /> <label for="caching-memorygzip">MemoryGZIP (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label></span><br/>

						<span ' . $caching_discisam_disabled_css . '><input id="caching-discisam" type="radio" name="caching-method" value="discisam" ' . $caching_discisam_disabled . ' /> <label for="caching-discisam">DiscISAM (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')' . $caching_discisam_disabled . '</label><br/>
						<label for="caching-discisam-directory" style="margin-left:24px;">' . __('custom directory for temp files (instead of php temp directory)','lmm') . ' </label>:<br/>
						<input style="margin-left:24px;width:300px;" id="caching-discisam-directory" type="input" name="caching-discisam-directory" value="" ' . $caching_discisam_disabled . ' /></label></span><br/>

						<span ' . $caching_phptemp_disabled_css . '><input id="caching-phptemp" type="radio" name="caching-method" value="phptemp" ' . $caching_phptemp_disabled . ' /> <label for="caching-phptemp">phpTemp ' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('medium','lmm')) . ')</label><br/>
						<label for="caching-phptemp-filesize" style="margin-left:24px;">' . __('maximum temporary file size in MB','lmm') . ' </label> <input id="caching-phptemp-filesize" type="input" name="caching-phptemp-filesize" value="8" style="width:30px;" ' . $caching_phptemp_disabled . ' /></label></span><br/>

						<span ' . $caching_igbinary_disabled_css . '><input id="caching-igbinary" type="radio" name="caching-method" value="igbinary" ' . $caching_igbinary_disabled . ' /> <label for="caching-igbinary">igbinary <a href="http://pecl.php.net/package/igbinary" title="http://pecl.php.net/package/igbinary" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('medium','lmm'), __('high','lmm')) . ')</label></span><br/>

						<span ' . $caching_memoryserialized_disabled_css . '><input id="caching-memoryserialized" type="radio" name="caching-method" value="memoryserialized" ' . $caching_memoryserialized_disabled . ' /> <label for="caching-memoryserialized">Memory serialized (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('high','lmm'), __('high','lmm')) . ')' . $caching_memoryserialized_disabled . '</label></span><br/>

						<input id="caching-memory" type="radio" name="caching-method" value="memory" /> <label for="caching-memory">Memory <a href="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" title="http://www.php.net/manual/en/ini.core.php#ini.memory-limit" target="_blank"><img src="' . LEAFLET_PLUGIN_URL . 'inc/img/icon-menu-external.png" width="10" height="10"/></a> (' . sprintf(__('Memory usage: %1$s, performance: %2$s','lmm'), __('very high','lmm'), __('very high','lmm')) . ')</label>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2"><input style="font-weight:bold;" type="submit" name="submit" class="submit button-primary" value="' . esc_attr__('start export','lmm') . '" /></td>
				</tr>
			</table>
			</form>';
		} //info: ($action_iframe == 'import')
		echo '</p></body></html>';

	//info: end ($action_standalone == NULL)
	} else {
		/**********************************
		*         start action            *
		**********************************/
		//info: start PHPExcel - shared settings for import and export
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		date_default_timezone_set('Europe/London');

		//info: prepare caching - http://phpexcel.codeplex.com/discussions/234150
		$user_cache = $_POST['caching-method'];
		if ($user_cache == 'auto') {
			if ( function_exists('sqlite_open') ){ //info: SQLite2
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (SQLite2)';
			} else if ( class_exists('SQLite3',FALSE) === TRUE ) { //info:SQLite3
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (SQLite3)';
			} else if ( function_exists('apc_store') && (apc_sma_info() === TRUE) ) { //info: APC
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_APC;
				$cacheSettings = array( 'cacheTime' => 600 );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$cache_method_for_log = 'automatic (APC)';
			} else if ( function_exists('memcache_add') ) { //info: Memcache
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
				$cacheSettings = array( 'memcacheServer' => 'localhost', 'memcachePort' => 11211, 'cacheTime' => 600 );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$cache_method_for_log = 'automatic (Memcache)';
			} else if ( function_exists('wincache_ucache_add') ) { //info: Wincache
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_wincache;
				$cacheSettings = array( 'cacheTime' => 600 );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$cache_method_for_log = 'automatic (Wincache)';
			} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip) === TRUE ) { //info: MemoryGZip
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (MemoryGZip)';
			} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_discISAM) === TRUE ) { //info: DiscISAM
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (DiscISAM)';
			} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp) === TRUE ) { //info: PHPTemp
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
				$cacheSettings = array( 'memoryCacheSize'  => '8MB' );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
				$cache_method_for_log = 'automatic (PHPTemp)';
			} else if ( function_exists('igbinary_serialize') ) { //info: Igbinary
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_igbinary;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (Igbinary)';
			} else if ( PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized) === TRUE ) { //info: MemorySerialized
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (MemorySerialized)';
			} else { //info: Cache in Memory
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
				$cache_method_for_log = 'automatic (Memory)';
			}
		//info: perpare custom cache selection
		} else if ($user_cache == 'sqlite2') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'SQLite2';
		} else if ($user_cache == 'sqlite3') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'SQLite3';
		} else if ($user_cache == 'apc') {
			$caching_apc_timeout = intval($_POST['caching-apc-timeout']);
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_APC;
			$cacheSettings = array( 'cacheTime' => $caching_apc_timeout );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'APC';
		} else if ($user_cache == 'memcache') {
			$caching_memcache_server = trim($_POST['caching-memcache-server']);
			$caching_memcache_port = intval($_POST['caching-memcache-port']);
			$caching_memcache_timeout = intval($_POST['caching-memcache-timeout']);
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_memcache;
			$cacheSettings = array( 'memcacheServer' => $caching_memcache_server, 'memcachePort' => $caching_memcache_port, 'cacheTime' => $caching_memcache_timeout );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'Memcache';
		} else if ($user_cache == 'wincache') {
			$caching_wincache_timeout = intval($_POST['caching-wincache-timeout']);
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_wincache;
			$cacheSettings = array( 'cacheTime' => $caching_wincache_timeout );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'Wincache';
		} else if ($user_cache == 'memorygzip') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'MemoryGZip';
		} else if ($user_cache == 'discisam') {
			$caching_discisam_directory = trim($_POST['caching-discisam-directory']);
			if ($caching_discisam_directory == NULL) {
				$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			} else {
				$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
				$cacheSettings = array( 'dir'  => $caching_discisam_directory );
				PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			}
			$cache_method_for_log = 'DiscISAM';
		} else if ($user_cache == 'phptemp') {
			$caching_phptemp_filesize = intval($_POST['caching-phptemp-filesize']) . 'MB';
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
			$cacheSettings = array( 'memoryCacheSize'  => $caching_phptemp_filesize );
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
			$cache_method_for_log = 'PHPTemp';
		} else if ($user_cache == 'igbinary') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_igbinary;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'Igbinary';
		} else if ($user_cache == 'memoryserialized') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
			$cache_method_for_log = 'MemorySerialized';
		} else if ($user_cache == 'memory') {
			$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory;
			$cache_method_for_log = 'Memory';
			PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
		}

		$objPHPExcel = new PHPExcel();

		//info: function for geocoding
		$geocoding_option = isset($_POST['geocoding-option']) ? $_POST['geocoding-option'] : '';
		function lmm_getLatLng($address) {
			//info: Google Maps for Business parameters
			if ($_POST['gmapsbusiness-client'] == NULL)  { $gmapsbusiness_client = ''; } else { $gmapsbusiness_client = '&client=' . $_POST['gmapsbusiness-client']; }
			if ($_POST['gmapsbusiness-signature'] == NULL) { $gmapsbusiness_signature = ''; } else { $gmapsbusiness_signature = '&signature=' . $_POST['gmapsbusiness-signature']; }
			if ($_POST['gmapsbusiness-channel'] == NULL) { $gmapsbusiness_channel = ''; } else { $gmapsbusiness_channel = '&channel=' . $_POST['gmapsbusiness-channel']; }
			$url = 'http://maps.googleapis.com/maps/api/geocode/xml?address=' . urlencode($address) . '&sensor=false' . $gmapsbusiness_client . $gmapsbusiness_signature . $gmapsbusiness_channel;
			$xml_raw = wp_remote_post( $url, array( 'sslverify' => false, 'timeout' => 10 ) );
			$xml = simplexml_load_string($xml_raw['body']);

			$response = array();
			$statusCode = $xml->status;
			if ( ($statusCode != false) && ($statusCode != NULL) ) {
				if ($statusCode == 'OK') {
					$latDom = $xml->result[0]->geometry->location->lat;
					$lonDom = $xml->result[0]->geometry->location->lng;
					$addressDom = $xml->result[0]->formatted_address;
					if ($latDom != NULL) {
						$response = array (
							'success' 	=> true,
							'lat' 		=> $latDom,
							'lon' 		=> $lonDom,
							'address'	=> $addressDom
						);
						return $response;
					}
				} else if ($statusCode == 'OVER_QUERY_LIMIT') { //info: wait 1.5sec and try again once
					usleep(1500000); 
					$xml_raw = wp_remote_post( $url, array( 'sslverify' => false, 'timeout' => 10 ) );
					$xml = simplexml_load_string($xml_raw['body']);
					
					$response = array();
					$statusCode = $xml->status;
					
					if ( ($statusCode != false) && ($statusCode != NULL) ) {
						if ($statusCode == 'OK') {
							$latDom = $xml->result[0]->geometry->location->lat;
							$lonDom = $xml->result[0]->geometry->location->lng;
							$addressDom = $xml->result[0]->formatted_address;
							if ($latDom != NULL) {
								$response = array (
									'success' 	=> true,
									'lat' 		=> $latDom,
									'lon' 		=> $lonDom,
									'address'	=> $addressDom
								);
								return $response;
							}
						}
					}
				}
			}
			$response = array (
				'success' => false,
				'message' => $statusCode
			);
			return $response;
		}

		if ($action_standalone == 'import') {
			/**********************************
			*       import action             *
			**********************************/
			echo '<!DOCTYPE html>
					<head>
					<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
					<title>Running import for Leaflet Maps Marker Pro</title>
					<style type="text/css">
						body { font-family: sans-serif;	padding:0 0 0 5px; margin:0px; font-size: 12px;	line-height: 1.4em; }
						a {	color: #21759B;	text-decoration: none; }
						a:hover, a:active, a:focus { color: #D54E21; }
						td {padding:5px 5px 5px 0;}
						.success { font-weight:bold;color:#00cc33; }
						.warning { font-weight:bold;color:#ff6600; }
						.error { font-weight:bold;color:red; }
						hr { margin:2px 0; color: #aeadad; }
					</style>
					<script>
					function show_results() {
						document.getElementById("detailed-results").style.cssText = "display:block;"
						document.getElementById("expand-results").style.cssText = "display:none;"
					}
					</script>
					</head>
					<body><p style="margin:0.5em 0 0 0;">';
			if ($_FILES['import-file']['error'] == 0) {
				echo date('H:i:s') . ' ' . __('Begin of run','lmm') . '<br/>';
				$test_mode = $_POST['test-mode'];
				if ($test_mode == 'test-mode-on') {
					echo date('H:i:s') . ' <span class="success">' . __('Info: test mode is on - checking import file only - no changes will be made to the database','lmm') . '</span><br/>';
				}
				echo date('H:i:s') . ' ' . sprintf(__('Import file %1$s was saved to PHP temp directory (size: %2$s KB)','lmm'), $_FILES['import-file']['name'], floor($_FILES['import-file']['size']/1000)) . '<br/>';

				function lmm_get_php_memory_limit() {
					$php_memory_limit = ini_get('memory_limit');
					if (preg_match('/^(\d+)(.)$/', $php_memory_limit, $matches)) {
						if ($matches[2] == 'M') {
							return $matches[1] . 'MB';
						} else if ($matches[2] == 'K') {
							return $matches[1] / 1024 . 'MB';
						}
					} else {
						return __('not available','lmm');
					}
				}
				echo date('H:i:s') . ' ' . sprintf(__('Current memory usage: %1$s MB (memory limit: %2$s)','lmm'), (memory_get_usage(true) / 1024 / 1024), lmm_get_php_memory_limit()) . '<br/>';
				if ($cache_method_for_log != 'Memory') {
					echo date('H:i:s') . ' ' . sprintf(__('Enabling caching method %1$s to optimize memory usage','lmm'), $cache_method_for_log) . '<br/>';
				}
				$import_file_extension = strtoupper(pathinfo($_FILES['import-file']['name'], PATHINFO_EXTENSION));
				if ($import_file_extension == 'CSV') {
					$objReader = PHPExcel_IOFactory::createReader('CSV');
					$objReader->setDelimiter(';');
				} else if ($import_file_extension == 'XLS') {
					$objReader = PHPExcel_IOFactory::createReader('Excel5');
				} else if ($import_file_extension == 'XLSX') {
					$objReader = PHPExcel_IOFactory::createReader('Excel2007');
				} else if ($import_file_extension == 'ODS') {
					$objReader = PHPExcel_IOFactory::createReader('OOCalc');
				}

				//info: load only first sheet to reduce memory usage - no supported by CSV
				if ($import_file_extension != 'CSV') {
					$existing_worksheets = $objReader->listWorksheetNames($_FILES['import-file']['tmp_name']);
					$objWorksheet = $objReader->setLoadSheetsOnly($existing_worksheets[0]);
				}
				//info: ignore styles/hyperlinks for xlsx/xls/ods import files to further reduce memory usage
				if (isset($_POST['setReadDataOnly'])) {
					$objReader->setReadDataOnly(true);
				}
				$objPHPExcel = $objReader->load($_FILES['import-file']['tmp_name']);
				$objWorksheet = $objPHPExcel->getActiveSheet();

				//info: check if header row exists
				if ( (strtolower($objWorksheet->getCellByColumnAndRow(0, 1)->getValue()) == 'id') && (strtolower($objWorksheet->getCellByColumnAndRow(36, 1)->getValue()) == 'gpx_panel') ) {
					$highestRow = $objWorksheet->getHighestRow();
					$highestColumn = $objWorksheet->getHighestColumn();
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					echo date('H:i:s') . ' ' . sprintf(__('Starting the processing of %1$s rows (skipping header row)','lmm'), $highestRow - 1) . '<br/>';
					echo '<hr noshade size="1" style="color:#000000;" />';
					echo '<div id="detailed-results" style="display:none;">';
					echo '<hr noshade size="1" />';

					$layerlist = $wpdb->get_results('SELECT id FROM '.$table_name_layers, ARRAY_A);
					function lmm_does_layer_id_exist($layerlist, $needle) {
						foreach ($layerlist as $key => $single_layer) {
								if ($single_layer['id'] == $needle) {
									return true;
								}
						}
						return false;
					}

					//info: prepare stats
					$stats_created = array();
					$stats_updated = array();
					$stats_warnings = array();
					$stats_errors = array();

					for ($row = 2; $row <= $highestRow; ++$row) {
						$marker_id_check = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
						/**********************************
						*       create marker             *
						**********************************/
						if ($marker_id_check == NULL) {
							echo date('H:i:s') . ' ' . sprintf(__('Processing row %1$s from import file - trying to create new marker','lmm'), $row) . '<br/>';
							//info: prepare markername (no quotes escaping needed)
							$markername = $objWorksheet->getCellByColumnAndRow(1, $row);
							//info: prepare popuptext
							if ($objWorksheet->getCellByColumnAndRow(2, $row)->getDataType() != 'null') {
								if ($import_file_extension == 'CSV') {
									$popuptext = str_replace("'", "\'", str_replace("\"", "'", stripslashes(preg_replace("/(\015\012)|(\015)|(\012)/","<br/>",$objWorksheet->getCellByColumnAndRow(2, $row)))));
								} else {
									if ($objWorksheet->getCellByColumnAndRow(2, $row)->hasHyperlink()) {
										$url = $objWorksheet->getCellByColumnAndRow(2, $row)->getHyperlink()->getUrl();
										$popuptext = '<a href="' . $url . '">' . $objWorksheet->getCellByColumnAndRow(2, $row) . '</a>';

									} else {
										$popuptext = stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$objWorksheet->getCellByColumnAndRow(2, $row)));
									}
								}
							} else {
								$popuptext = '';
							}
							//info: prepare openpopup
							if ($objWorksheet->getCellByColumnAndRow(3, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(3, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(3, $row) =='1') ) {
									$openpopup = $objWorksheet->getCellByColumnAndRow(3, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'openpopup', $row, $objWorksheet->getCellByColumnAndRow(3, $row), $lmm_options[ 'defaults_marker_openpopup' ]) . '</span><br/>';
									$stats_warnings[] = $row;
									$openpopup = $lmm_options[ 'defaults_marker_openpopup' ];
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'openpopup', $row, $objWorksheet->getCellByColumnAndRow(3, $row), $lmm_options[ 'defaults_marker_openpopup' ]) . '</span><br/>';
								$stats_warnings[] = $row;
								$openpopup = $lmm_options[ 'defaults_marker_openpopup' ];
							}
							//info: prepare address
							if ($objWorksheet->getCellByColumnAndRow(4, $row)->getDataType() != 'null') {
								$address = $objWorksheet->getCellByColumnAndRow(4, $row);
							} else {
								$address = '';
							}
							//info: prepare lat
							if ($objWorksheet->getCellByColumnAndRow(5, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(5, $row)->getValue() > 90 ) || ($objWorksheet->getCellByColumnAndRow(5, $row)->getValue() < -90 ) ) {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'lat', $row, $objWorksheet->getCellByColumnAndRow(5, $row), floatval($lmm_options[ 'defaults_marker_lat' ])) . '</span><br/>';
									$stats_warnings[] = $row;
									$lat = floatval($lmm_options[ 'defaults_marker_lat' ]);
								} else {
									$lat = $objWorksheet->getCellByColumnAndRow(5, $row);
								}
							} else {
								if ($geocoding_option == 'geocoding-on') {
									$lat = '';
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'lat', $row, $objWorksheet->getCellByColumnAndRow(5, $row), floatval($lmm_options[ 'defaults_marker_lat' ]));
									if ($import_file_extension == 'CSV') { echo ' ' . __('Please also check if you are using , and not . as comma separator in your import file!','lmm'); }
									echo '</span><br/>';
									$stats_warnings[] = $row;
									$lat = floatval($lmm_options[ 'defaults_marker_lat' ]);
								}
							}
							//info: prepare lon
							if ( $objWorksheet->getCellByColumnAndRow(6, $row)->getDataType() == 'n' ) {
								if ( ($objWorksheet->getCellByColumnAndRow(6, $row)->getValue() > 180 ) || ($objWorksheet->getCellByColumnAndRow(6, $row)->getValue() < -180 ) ) {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'lon', $row, $objWorksheet->getCellByColumnAndRow(6, $row), floatval($lmm_options[ 'defaults_marker_lon' ])) . '</span><br/>';
									$stats_warnings[] = $row;
									$lon = floatval($lmm_options[ 'defaults_marker_lon' ]);
								} else {
									$lon = $objWorksheet->getCellByColumnAndRow(6, $row);
								}
							} else {
								if ($geocoding_option == 'geocoding-on') {
									$lon = '';
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'lon', $row, $objWorksheet->getCellByColumnAndRow(6, $row), floatval($lmm_options[ 'defaults_marker_lon' ]));
									if ($import_file_extension == 'CSV') { echo ' ' . __('Please also check if you are using , and not . as comma separator in your import file!','lmm'); }
									echo '</span><br/>';
									$stats_warnings[] = $row;
									$lon = floatval($lmm_options[ 'defaults_marker_lon' ]);
								}
							}
							//info: prepare layer
							if ( $objWorksheet->getCellByColumnAndRow(7, $row)->getDataType() == 'n' ) {
								if (lmm_does_layer_id_exist($layerlist, $objWorksheet->getCellByColumnAndRow(7, $row)) == TRUE) {
									$layer = $objWorksheet->getCellByColumnAndRow(7, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: a layer with the ID %1$s as given in row %2$s does not exist - using default value 0 instead (marker will not be assigned to a layer)','lmm'), $objWorksheet->getCellByColumnAndRow(7, $row), $row) . '</span><br/>';
									$stats_warnings[] = $row;
									$layer = 0;
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'layer', $row, $objWorksheet->getCellByColumnAndRow(7, $row), intval($lmm_options[ 'defaults_marker_default_layer' ])) . '</span><br/>';
								$stats_warnings[] = $row;
								if ($lmm_options[ 'defaults_marker_default_layer' ] == '0') {
									$layer = 0;
								} else {
									$layer = intval($lmm_options[ 'defaults_marker_default_layer' ]);
								}
							}
							//info: prepare zoom
							if ($objWorksheet->getCellByColumnAndRow(8, $row)->getDataType() == 'n') {
								$zoom = $objWorksheet->getCellByColumnAndRow(8, $row);
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'zoom', $row, $objWorksheet->getCellByColumnAndRow(8, $row), intval($lmm_options[ 'defaults_marker_zoom' ])) . '</span><br/>';
								$stats_warnings[] = $row;
								$zoom = intval($lmm_options[ 'defaults_marker_zoom' ]);
							}
							//info: prepare icon
							if ($objWorksheet->getCellByColumnAndRow(9, $row)->getDataType() == 's') {
								$icon = $objWorksheet->getCellByColumnAndRow(9, $row);
								//info: check if icon exists on server
								if ( file_exists($defaults_marker_icon_dir . DIRECTORY_SEPARATOR . $icon) ) {
									$icon = $icon;
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: the icon "%1$s" could not be found in the marker icon directory at %2$s - using default icon instead','lmm'), $icon, $defaults_marker_icon_dir) . '</span><br/>';
									$icon = '';
									$stats_warnings[] = $row;
								}
							} else if ($objWorksheet->getCellByColumnAndRow(9, $row)->getDataType() == 'null') {
								$icon = '';
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'icon', $row, $objWorksheet->getCellByColumnAndRow(9, $row), $lmm_options[ 'defaults_marker_icon' ]) . '</span><br/>';
								$stats_warnings[] = $row;
							}
							//info: prepare mapwidth
							if ($objWorksheet->getCellByColumnAndRow(10, $row)->getDataType() == 'n') {
									$mapwidth = $objWorksheet->getCellByColumnAndRow(10, $row);
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'mapwidth', $row, $objWorksheet->getCellByColumnAndRow(10, $row), intval($lmm_options[ 'defaults_marker_mapwidth' ])) . '</span><br/>';
								$stats_warnings[] = $row;
								$mapwidth = intval($lmm_options[ 'defaults_marker_mapwidth' ]);
							}
							//info: prepare mapwidthunit
							if ($objWorksheet->getCellByColumnAndRow(11, $row)->getDataType() == 's') {
								if ( ($objWorksheet->getCellByColumnAndRow(11, $row) == 'px') || ($objWorksheet->getCellByColumnAndRow(11, $row) == '%') ) {
									$mapwidthunit = $objWorksheet->getCellByColumnAndRow(11, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'mapwidthunit', $row, $objWorksheet->getCellByColumnAndRow(11, $row), $lmm_options[ 'defaults_marker_mapwidthunit' ]) . '</span><br/>';
									$stats_warnings[] = $row;
									$mapwidthunit = $lmm_options[ 'defaults_marker_mapwidthunit' ];
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'mapwidthunit', $row, $objWorksheet->getCellByColumnAndRow(11, $row), $lmm_options[ 'defaults_marker_mapwidthunit' ]) . '</span><br/>';
								$stats_warnings[] = $row;
								$mapwidthunit = $lmm_options[ 'defaults_marker_mapwidthunit' ];
							}
							//info: prepare mapheight
							if ($objWorksheet->getCellByColumnAndRow(12, $row)->getDataType() == 'n') {
									$mapheight = $objWorksheet->getCellByColumnAndRow(12, $row);
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'mapheight', $row, $objWorksheet->getCellByColumnAndRow(12, $row), intval($lmm_options[ 'defaults_marker_mapheight' ])) . '</span><br/>';
								$stats_warnings[] = $row;
								$mapheight = intval($lmm_options[ 'defaults_marker_mapheight' ]);
							}
							//info: prepare basemap
							if (in_array($objWorksheet->getCellByColumnAndRow(13, $row), array('osm_mapnik','mapquest_osm','mapquest_aerial','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','cloudmade','cloudmade2','cloudmade3','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap'))) {
								$basemap = $objWorksheet->getCellByColumnAndRow(13, $row);
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'basemap', $row, $objWorksheet->getCellByColumnAndRow(13, $row), $lmm_options[ 'standard_basemap' ]) . '</span><br/>';
								$stats_warnings[] = $row;
								$basemap = $lmm_options[ 'standard_basemap' ];
							}
							//info: prepare panel
							if ($objWorksheet->getCellByColumnAndRow(14, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(14, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(14, $row) == '1') ) {
									$panel = $objWorksheet->getCellByColumnAndRow(14, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'panel', $row, $objWorksheet->getCellByColumnAndRow(14, $row), $lmm_options[ 'defaults_marker_panel' ]) . '</span><br/>';
									$stats_warnings[] = $row;
									$panel = $lmm_options[ 'defaults_marker_panel' ];
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'panel', $row, $objWorksheet->getCellByColumnAndRow(14, $row), $lmm_options[ 'defaults_marker_panel' ]) . '</span><br/>';
								$stats_warnings[] = $row;
								$panel = $lmm_options[ 'defaults_marker_panel' ];
							}
							//info: prepare controlbox
							if ($objWorksheet->getCellByColumnAndRow(15, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(15, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(15, $row) == '1') ) {
									$controlbox = $objWorksheet->getCellByColumnAndRow(15, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'controlbox', $row, $objWorksheet->getCellByColumnAndRow(15, $row), $lmm_options[ 'defaults_marker_controlbox' ]) . '</span><br/>';
									$stats_warnings[] = $row;
									$controlbox = $lmm_options[ 'defaults_marker_controlbox' ];
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'controlbox', $row, $objWorksheet->getCellByColumnAndRow(15, $row), $lmm_options[ 'defaults_marker_controlbox' ]) . '</span><br/>';
								$stats_warnings[] = $row;
								$controlbox = $lmm_options[ 'defaults_marker_controlbox' ];
							}
							//info: prepare createdby
							$audit_option = $_POST['audit-option'];
							if ($audit_option == 'audit-on') {
								$createdby = $current_user->user_login;
							} else {
								$createdby = $objWorksheet->getCellByColumnAndRow(16, $row);
							}
							//info: prepare createdon
							if ($audit_option == 'audit-on') {
								$createdon = current_time('mysql',0);
							} else {
								$createdon_format_check = date('Y-m-d H:i:s',strtotime($objWorksheet->getCellByColumnAndRow(17, $row)));
								if ($createdon_format_check != '1970-01-01 01:00:00') {
									$createdon = $createdon_format_check;
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'createdon', $row, $objWorksheet->getCellByColumnAndRow(17, $row), current_time('mysql',0)) . '</span><br/>';
									$stats_warnings[] = $row;
									$createdon = current_time('mysql',0);
								}
							}
							//info: prepare updatedby
							if ($audit_option == 'audit-on') {
								$updatedby = $current_user->user_login;
							} else {
								$updatedby = $objWorksheet->getCellByColumnAndRow(18, $row);
							}
							//info: prepare updatedon
							if ($audit_option == 'audit-on') {
								$updatedon = current_time('mysql',0);
							} else {
								$updatedon_format_check = date('Y-m-d H:i:s',strtotime($objWorksheet->getCellByColumnAndRow(19, $row)));
								if ($updatedon_format_check != '1970-01-01 01:00:00') {
									$updatedon = $updatedon_format_check;
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'updatedon', $row, $objWorksheet->getCellByColumnAndRow(19, $row), current_time('mysql',0)) . '</span><br/>';
									$stats_warnings[] = $row;
									$updatedon = current_time('mysql',0);
								}
							}
							//info: prepare kml_timestamp
							if ($objWorksheet->getCellByColumnAndRow(20, $row)->getDataType() != 'null') {
								$kml_timestamp_format_check = date('Y-m-d H:i:s',strtotime($objWorksheet->getCellByColumnAndRow(20, $row)));
								if ($kml_timestamp_format_check != '1970-01-01 01:00:00') {
									$kml_timestamp = $kml_timestamp_format_check;
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'kml_timestamp', $row, $objWorksheet->getCellByColumnAndRow(20, $row), '') . '</span><br/>';
									$stats_warnings[] = $row;
									$kml_timestamp = '';
								}
							} else {
								$kml_timestamp = '';
							}
							//info: prepare overlays_custom
							if ($objWorksheet->getCellByColumnAndRow(21, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(21, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(21, $row) == '1') ) {
									$overlays_custom = $objWorksheet->getCellByColumnAndRow(21, $row);
								} else {
									$overlays_custom_default = isset($lmm_options[ 'defaults_marker_overlays_custom_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom', $row, $objWorksheet->getCellByColumnAndRow(21, $row), $overlays_custom_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom = $overlays_custom_default;
								}
							} else {
								$overlays_custom_default = isset($lmm_options[ 'defaults_marker_overlays_custom_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom', $row, $objWorksheet->getCellByColumnAndRow(21, $row), $overlays_custom_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$overlays_custom = $overlays_custom_default;
							}
							//info: prepare overlays_custom2
							if ($objWorksheet->getCellByColumnAndRow(22, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(22, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(22, $row) == '1') ) {
									$overlays_custom2 = $objWorksheet->getCellByColumnAndRow(22, $row);
								} else {
									$overlays_custom2_default = isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom2', $row, $objWorksheet->getCellByColumnAndRow(22, $row), $overlays_custom2_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom2 = $overlays_custom2_default;
								}
							} else {
								$overlays_custom2_default = isset($lmm_options[ 'defaults_marker_overlays_custom2_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom2', $row, $objWorksheet->getCellByColumnAndRow(22, $row), $overlays_custom2_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$overlays_custom2 = $overlays_custom2_default;
							}
							//info: prepare overlays_custom3
							if ($objWorksheet->getCellByColumnAndRow(23, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(23, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(23, $row) == '1') ) {
									$overlays_custom3 = $objWorksheet->getCellByColumnAndRow(23, $row);
								} else {
									$overlays_custom3_default = isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom3', $row, $objWorksheet->getCellByColumnAndRow(23, $row), $overlays_custom3_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom3 = $overlays_custom3_default;
								}
							} else {
								$overlays_custom3_default = isset($lmm_options[ 'defaults_marker_overlays_custom3_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom3', $row, $objWorksheet->getCellByColumnAndRow(23, $row), $overlays_custom3_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$overlays_custom3 = $overlays_custom3_default;
							}
							//info: prepare overlays_custom4
							if ($objWorksheet->getCellByColumnAndRow(24, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(24, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(24, $row) == '1') ) {
									$overlays_custom4 = $objWorksheet->getCellByColumnAndRow(24, $row);
								} else {
									$overlays_custom4_default = isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom4', $row, $objWorksheet->getCellByColumnAndRow(24, $row), $overlays_custom4_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom4 = $overlays_custom4_default;
								}
							} else {
								$overlays_custom4_default = isset($lmm_options[ 'defaults_marker_overlays_custom4_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'overlays_custom4', $row, $objWorksheet->getCellByColumnAndRow(24, $row), $overlays_custom4_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$overlays_custom4 = $overlays_custom4_default;
							}
							//info: prepare wms
							if ($objWorksheet->getCellByColumnAndRow(25, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(25, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(25, $row) == '1') ) {
									$wms = $objWorksheet->getCellByColumnAndRow(25, $row);
								} else {
									$wms_default = isset($lmm_options[ 'defaults_marker_wms_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms', $row, $objWorksheet->getCellByColumnAndRow(25, $row), $wms_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms = $wms_default;
								}
							} else {
								$wms_default = isset($lmm_options[ 'defaults_marker_wms_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms', $row, $objWorksheet->getCellByColumnAndRow(25, $row), $wms_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms = $wms_default;
							}
							//info: prepare wms2
							if ($objWorksheet->getCellByColumnAndRow(26, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(26, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(26, $row) == '1') ) {
									$wms2 = $objWorksheet->getCellByColumnAndRow(26, $row);
								} else {
									$wms2_default = isset($lmm_options[ 'defaults_marker_wms2_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms2', $row, $objWorksheet->getCellByColumnAndRow(26, $row), $wms2_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms2 = $wms2_default;
								}
							} else {
								$wms2_default = isset($lmm_options[ 'defaults_marker_wms2_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms2', $row, $objWorksheet->getCellByColumnAndRow(26, $row), $wms2_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms2 = $wms2_default;
							}
							//info: prepare wms3
							if ($objWorksheet->getCellByColumnAndRow(27, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(27, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(27, $row) == '1') ) {
									$wms3 = $objWorksheet->getCellByColumnAndRow(27, $row);
								} else {
									$wms3_default = isset($lmm_options[ 'defaults_marker_wms3_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms3', $row, $objWorksheet->getCellByColumnAndRow(27, $row), $wms3_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms3 = $wms3_default;
								}
							} else {
								$wms3_default = isset($lmm_options[ 'defaults_marker_wms3_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms3', $row, $objWorksheet->getCellByColumnAndRow(27, $row), $wms3_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms3 = $wms3_default;
							}
							//info: prepare wms4
							if ($objWorksheet->getCellByColumnAndRow(28, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(28, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(28, $row) == '1') ) {
									$wms4 = $objWorksheet->getCellByColumnAndRow(28, $row);
								} else {
									$wms4_default = isset($lmm_options[ 'defaults_marker_wms4_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms4', $row, $objWorksheet->getCellByColumnAndRow(28, $row), $wms4_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms4 = $wms4_default;
								}
							} else {
								$wms4_default = isset($lmm_options[ 'defaults_marker_wms4_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms4', $row, $objWorksheet->getCellByColumnAndRow(28, $row), $wms4_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms4 = $wms4_default;
							}
							//info: prepare wms5
							if ($objWorksheet->getCellByColumnAndRow(29, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(29, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(29, $row) == '1') ) {
									$wms5 = $objWorksheet->getCellByColumnAndRow(29, $row);
								} else {
									$wms5_default = isset($lmm_options[ 'defaults_marker_wms5_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms5', $row, $objWorksheet->getCellByColumnAndRow(29, $row), $wms5_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms5 = $wms5_default;
								}
							} else {
								$wms5_default = isset($lmm_options[ 'defaults_marker_wms5_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms5', $row, $objWorksheet->getCellByColumnAndRow(29, $row), $wms5_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms5 = $wms5_default;
							}
							//info: prepare wms6
							if ($objWorksheet->getCellByColumnAndRow(30, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(30, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(30, $row) == '1') ) {
									$wms6 = $objWorksheet->getCellByColumnAndRow(30, $row);
								} else {
									$wms6_default = isset($lmm_options[ 'defaults_marker_wms6_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms6', $row, $objWorksheet->getCellByColumnAndRow(30, $row), $wms6_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms6 = $wms6_default;
								}
							} else {
								$wms6_default = isset($lmm_options[ 'defaults_marker_wms6_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms6', $row, $objWorksheet->getCellByColumnAndRow(30, $row), $wms6_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms6 = $wms6_default;
							}
							//info: prepare wms7
							if ($objWorksheet->getCellByColumnAndRow(31, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(31, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(31, $row) == '1') ) {
									$wms7 = $objWorksheet->getCellByColumnAndRow(31, $row);
								} else {
									$wms7_default = isset($lmm_options[ 'defaults_marker_wms7_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms7', $row, $objWorksheet->getCellByColumnAndRow(31, $row), $wms7_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms7 = $wms7_default;
								}
							} else {
								$wms7_default = isset($lmm_options[ 'defaults_marker_wms7_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms7', $row, $objWorksheet->getCellByColumnAndRow(31, $row), $wms7_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms7 = $wms7_default;
							}
							//info: prepare wms8
							if ($objWorksheet->getCellByColumnAndRow(32, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(32, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(32, $row) == '1') ) {
									$wms8 = $objWorksheet->getCellByColumnAndRow(32, $row);
								} else {
									$wms8_default = isset($lmm_options[ 'defaults_marker_wms8_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms8', $row, $objWorksheet->getCellByColumnAndRow(32, $row), $wms8_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms8 = $wms8_default;
								}
							} else {
								$wms8_default = isset($lmm_options[ 'defaults_marker_wms8_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms8', $row, $objWorksheet->getCellByColumnAndRow(32, $row), $wms8_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms8 = $wms8_default;
							}
							//info: prepare wms9
							if ($objWorksheet->getCellByColumnAndRow(33, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(33, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(33, $row) == '1') ) {
									$wms9 = $objWorksheet->getCellByColumnAndRow(33, $row);
								} else {
									$wms9_default = isset($lmm_options[ 'defaults_marker_wms9_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms9', $row, $objWorksheet->getCellByColumnAndRow(33, $row), $wms9_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms9 = $wms9_default;
								}
							} else {
								$wms9_default = isset($lmm_options[ 'defaults_marker_wms9_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms9', $row, $objWorksheet->getCellByColumnAndRow(33, $row), $wms9_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms9 = $wms9_default;
							}
							//info: prepare wms10
							if ($objWorksheet->getCellByColumnAndRow(34, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(34, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(34, $row) == '1') ) {
									$wms10 = $objWorksheet->getCellByColumnAndRow(34, $row);
								} else {
									$wms10_default = isset($lmm_options[ 'defaults_marker_wms10_active' ]) ? '1' : '0';
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms10', $row, $objWorksheet->getCellByColumnAndRow(34, $row), $wms10_default) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms10 = $wms10_default;
								}
							} else {
								$wms10_default = isset($lmm_options[ 'defaults_marker_wms10_active' ]) ? '1' : '0';
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'wms10', $row, $objWorksheet->getCellByColumnAndRow(34, $row), $wms10_default) . '</span><br/>';
								$stats_warnings[] = $row;
								$wms10 = $wms10_default;
							}
							//info: prepare gpx_url
							if ($objWorksheet->getCellByColumnAndRow(35, $row)->getDataType() != 'null') {
								$gpx_url = $objWorksheet->getCellByColumnAndRow(35, $row);
							} else {
								$gpx_url = '';
							}
							//info: prepare gpx_panel
							if ($objWorksheet->getCellByColumnAndRow(36, $row)->getDataType() == 'n') {
								if ( ($objWorksheet->getCellByColumnAndRow(36, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(36, $row) == '1') ) {
									$gpx_panel = $objWorksheet->getCellByColumnAndRow(36, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'gpx_panel', $row, $objWorksheet->getCellByColumnAndRow(36, $row), '0') . '</span><br/>';
									$stats_warnings[] = $row;
									$gpx_panel = '0';
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using default value %4$s instead','lmm'), 'gpx_panel', $row, $objWorksheet->getCellByColumnAndRow(36, $row), '0') . '</span><br/>';
								$stats_warnings[] = $row;
								$gpx_panel = '0';
							}

							//info: geocoding address if set
							if ($geocoding_option == 'geocoding-on') {
								$do_geocoding = lmm_getLatLng($address);
								if ($do_geocoding['success'] == true) {
									$lat = $do_geocoding['lat'];
									$lon = $do_geocoding['lon'];
									$address_from_import_file = $address;
									$address = $do_geocoding['address'];
									echo date('H:i:s') . ' <a name="' . $row . '"></a>' . sprintf(__('Geocoding result for address "%1$s" in row %2$s: "%3$s" (lat: %4$s, lon: %5$s)','lmm'), $address_from_import_file, $row, $address, $lat, $lon) . '<br/>';
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="error">' . sprintf(__('Error: geocoding for address "%1$s" in row %2$s failed (%3$s) - skipping row','lmm'), $address, $row, $do_geocoding['message']) . '</span><br/>';
									$stats_errors[] = $row;
								}
							}

							//info: only save to database if test mode is off
							if ($test_mode == 'test-mode-off') {
								if ( (isset($do_geocoding) && ($do_geocoding['success'] == true)) || ($geocoding_option == 'geocoding-off') ) {
									if ($kml_timestamp == NULL) {
										$query_add = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, updatedby, updatedon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, address, gpx_url, gpx_panel) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %d )", $markername, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel );
									} else {
										$query_add = $wpdb->prepare( "INSERT INTO $table_name_markers (markername, basemap, layer, lat, lon, icon, popuptext, zoom, openpopup, mapwidth, mapwidthunit, mapheight, panel, createdby, createdon, updatedby, updatedon, controlbox, overlays_custom, overlays_custom2, overlays_custom3, overlays_custom4, wms, wms2, wms3, wms4, wms5, wms6, wms7, wms8, wms9, wms10, kml_timestamp, address, gpx_url, gpx_panel) VALUES (%s, %s, %d, %s, %s, %s, %s, %d, %d, %d, %s, %d, %d, %s, %s, %s, %s, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %d, %s, %s, %s, %d )", $markername, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel );
									}
									$result_add = $wpdb->query( $query_add );
									if ($result_add == TRUE) {
									echo date('H:i:s') . ' <span class="success">' . sprintf(__('A marker with the ID %1$s has been successfully created','lmm'), '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$wpdb->insert_id.'" title="' . esc_attr__('edit marker','lmm') . '" target="_top">' . $wpdb->insert_id . '</a>') . '</span><br/>';
										$stats_created[] = $wpdb->insert_id;
									} else {
										echo date('H:i:s') . ' <span class="error">' . sprintf(__('Error: marker from row %1$s could not be created.','lmm'), $row) . '</span><br/>';
										$stats_errors[] = $row;
									}
								}
							} else {
								if ( (isset($do_geocoding) && ($do_geocoding['success'] == true)) || ($geocoding_option == 'geocoding-off') ) { //info: needed for true stats if geocoding fails
									$stats_created[] = $row;
								}
							}
						} else {
							/**********************************
							*       update marker             *
							**********************************/
							echo date('H:i:s') . ' ' . sprintf(__('Processing row %1$s from import file - trying to update marker ID %1$s','lmm'), '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$marker_id_check.'" title="' . esc_attr__('edit marker','lmm') . '" target="_top">' . $marker_id_check . '</a>') . '<br/>';
							$existing_marker_data = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name_markers WHERE id = %d", intval($marker_id_check)), ARRAY_A );
							if ($existing_marker_data['id'] != NULL) {
								//info: prepare markername (no quotes escaping needed)
								$markername = $objWorksheet->getCellByColumnAndRow(1, $row);
								//info: prepare popuptext update
								if ($objWorksheet->getCellByColumnAndRow(2, $row)->getDataType() != 'null') {
									if ($import_file_extension == 'CSV') {
										$popuptext = str_replace("'", "\'", str_replace("\"", "'", stripslashes(preg_replace("/(\015\012)|(\015)|(\012)/","<br/>",$objWorksheet->getCellByColumnAndRow(2, $row)))));
									} else {
										if ($objWorksheet->getCellByColumnAndRow(2, $row)->hasHyperlink()) {
											$url = $objWorksheet->getCellByColumnAndRow(2, $row)->getHyperlink()->getUrl();
											$popuptext = '<a href="' . $url . '">' . $objWorksheet->getCellByColumnAndRow(2, $row) . '</a>';

										} else {
											$popuptext = stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$objWorksheet->getCellByColumnAndRow(2, $row)));
										}
									}
								} else {
									$popuptext = '';
								}
								//info: prepare openpopup update
								if ($objWorksheet->getCellByColumnAndRow(3, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(3, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(3, $row) =='1') ) {
										$openpopup = $objWorksheet->getCellByColumnAndRow(3, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'openpopup', $row, $objWorksheet->getCellByColumnAndRow(3, $row), $existing_marker_data['openpopup']) . '</span><br/>';
										$stats_warnings[] = $row;
										$openpopup = $existing_marker_data['openpopup'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'openpopup', $row, $objWorksheet->getCellByColumnAndRow(3, $row), $existing_marker_data['openpopup']) . '</span><br/>';
									$stats_warnings[] = $row;
									$openpopup = $existing_marker_data['openpopup'];
								}
								//info: prepare address update
								if ($objWorksheet->getCellByColumnAndRow(4, $row)->getDataType() != 'null') {
									$address = $objWorksheet->getCellByColumnAndRow(4, $row);
								} else {
									$address = '';
								}
								//info: prepare lat update
								if ( $objWorksheet->getCellByColumnAndRow(5, $row)->getDataType() == 'n' ) {
									if ( ($objWorksheet->getCellByColumnAndRow(5, $row)->getValue() > 90 ) || ($objWorksheet->getCellByColumnAndRow(5, $row)->getValue() < -90 ) ) {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'lat', $row, $objWorksheet->getCellByColumnAndRow(5, $row), $existing_marker_data['lat']) . '</span><br/>';
										$stats_warnings[] = $row;
										$lat = $existing_marker_data['lat'];
									} else {
										$lat = $objWorksheet->getCellByColumnAndRow(5, $row);
									}
								} else {
									if ($geocoding_option == 'geocoding-on') {
										$lat = '';
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'lat', $row, $objWorksheet->getCellByColumnAndRow(5, $row), $existing_marker_data['lat']);
										if ($import_file_extension == 'CSV') { echo ' ' . __('Please also check if you are using , and not . as comma separator in your import file!','lmm'); }
										echo '</span><br/>';
										$stats_warnings[] = $row;
										$lat = $existing_marker_data['lat'];
									}
								}
								//info: prepare lon update
								if ( $objWorksheet->getCellByColumnAndRow(6, $row)->getDataType() == 'n' ) {
									if ( ($objWorksheet->getCellByColumnAndRow(6, $row)->getValue() > 180 ) || ($objWorksheet->getCellByColumnAndRow(6, $row)->getValue() < -180 ) ) {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'lon', $row, $objWorksheet->getCellByColumnAndRow(6, $row), $existing_marker_data['lon']);
										if ($import_file_extension == 'CSV') { echo ' ' . __('Please also check if you are using , and not . as comma separator in your import file!','lmm'); }
										echo '</span><br/>';
										$stats_warnings[] = $row;
										$lon = $existing_marker_data['lon'];
									} else {
										$lon = $objWorksheet->getCellByColumnAndRow(6, $row);
									}
								} else {
									if ($geocoding_option == 'geocoding-on') {
										$lon = '';
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'lon', $row, $objWorksheet->getCellByColumnAndRow(6, $row), $existing_marker_data['lon']) . '</span><br/>';
										$stats_warnings[] = $row;
										$lon = $existing_marker_data['lon'];
									}
								}
								//info: prepare layer update
								if ( $objWorksheet->getCellByColumnAndRow(7, $row)->getDataType() == 'n' ) {
									if (lmm_does_layer_id_exist($layerlist, $objWorksheet->getCellByColumnAndRow(7, $row)) == TRUE) {
										$layer = $objWorksheet->getCellByColumnAndRow(7, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: a layer with the ID %1$s as given in row %2$s does not exist - using current value %3$s instead','lmm'), $objWorksheet->getCellByColumnAndRow(7, $row), $row, $existing_marker_data['layer']) . '</span><br/>';
										$stats_warnings[] = $row;
										$layer = 0;
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'layer', $row, $objWorksheet->getCellByColumnAndRow(7, $row), $existing_marker_data['layer']) . '</span><br/>';
									$stats_warnings[] = $row;
									$layer = $existing_marker_data['layer'];
								}
								//info: prepare zoom update
								if ($objWorksheet->getCellByColumnAndRow(8, $row)->getDataType() == 'n') {
									$zoom = $objWorksheet->getCellByColumnAndRow(8, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'zoom', $row, $objWorksheet->getCellByColumnAndRow(8, $row), $existing_marker_data['zoom']) . '</span><br/>';
									$stats_warnings[] = $row;
									$zoom = $existing_marker_data['zoom'];
								}
								//info: prepare icon update
								if ($objWorksheet->getCellByColumnAndRow(9, $row)->getDataType() == 's') {
									$icon = $objWorksheet->getCellByColumnAndRow(9, $row);
									//info: check if icon exists on server
									if ( file_exists($defaults_marker_icon_dir . DIRECTORY_SEPARATOR . $icon) ) {
										$icon = $icon;
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: the icon "%1$s" could not be found in the marker icon directory at %2$s - using current icon %3$s instead','lmm'), $icon, $defaults_marker_icon_dir, $existing_marker_data['icon']) . '</span><br/>';
										$icon = $existing_marker_data['icon'];
										$stats_warnings[] = $row;
									}
								} else if ($objWorksheet->getCellByColumnAndRow(9, $row)->getDataType() == 'null') {
									$icon = '';
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'icon', $row, $objWorksheet->getCellByColumnAndRow(9, $row), $existing_marker_data['icon']) . '</span><br/>';
									$stats_warnings[] = $row;
								}
								//info: prepare mapwidth update
								if ($objWorksheet->getCellByColumnAndRow(10, $row)->getDataType() == 'n') {
										$mapwidth = $objWorksheet->getCellByColumnAndRow(10, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'mapwidth', $row, $objWorksheet->getCellByColumnAndRow(10, $row), $existing_marker_data['mapwidth']) . '</span><br/>';
									$stats_warnings[] = $row;
									$mapwidth = $existing_marker_data['mapwidth'];
								}
								//info: prepare mapwidthunit update
								if ($objWorksheet->getCellByColumnAndRow(11, $row)->getDataType() == 's') {
									if ( ($objWorksheet->getCellByColumnAndRow(11, $row) == 'px') || ($objWorksheet->getCellByColumnAndRow(11, $row) == '%') ) {
										$mapwidthunit = $objWorksheet->getCellByColumnAndRow(11, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'mapwidthunit', $row, $objWorksheet->getCellByColumnAndRow(11, $row), $existing_marker_data['mapwidthunit']) . '</span><br/>';
										$stats_warnings[] = $row;
										$mapwidthunit = $existing_marker_data['mapwidthunit'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'mapwidthunit', $row, $objWorksheet->getCellByColumnAndRow(11, $row), $existing_marker_data['mapwidthunit']) . '</span><br/>';
									$stats_warnings[] = $row;
									$mapwidthunit = $existing_marker_data['mapwidthunit'];
								}
								//info: prepare mapheight update
								if ($objWorksheet->getCellByColumnAndRow(12, $row)->getDataType() == 'n') {
										$mapheight = $objWorksheet->getCellByColumnAndRow(12, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'mapheight', $row, $objWorksheet->getCellByColumnAndRow(12, $row), $existing_marker_data['mapheight']) . '</span><br/>';
									$stats_warnings[] = $row;
									$mapheight = $existing_marker_data['mapheight'];
								}
								//info: prepare basemap update
								if (in_array($objWorksheet->getCellByColumnAndRow(13, $row), array('osm_mapnik','mapquest_osm','mapquest_aerial','googleLayer_roadmap','googleLayer_satellite','googleLayer_hybrid','googleLayer_terrain','bingaerial','bingaerialwithlabels','bingroad','ogdwien_basemap','ogdwien_satellite','cloudmade','cloudmade2','cloudmade3','mapbox','mapbox2','mapbox3','custom_basemap','custom_basemap2','custom_basemap3','empty_basemap'))) {
									$basemap = $objWorksheet->getCellByColumnAndRow(13, $row);
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'basemap', $row, $objWorksheet->getCellByColumnAndRow(13, $row), $existing_marker_data['basemap']) . '</span><br/>';
									$stats_warnings[] = $row;
									$basemap = $existing_marker_data['basemap'];
								}
								//info: prepare panel update
								if ($objWorksheet->getCellByColumnAndRow(14, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(14, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(14, $row) == '1') ) {
										$panel = $objWorksheet->getCellByColumnAndRow(14, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'panel', $row, $objWorksheet->getCellByColumnAndRow(14, $row), $existing_marker_data['panel']) . '</span><br/>';
										$stats_warnings[] = $row;
										$panel = $existing_marker_data['panel'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'panel', $row, $objWorksheet->getCellByColumnAndRow(14, $row), $existing_marker_data['panel']) . '</span><br/>';
									$stats_warnings[] = $row;
									$panel = $existing_marker_data['panel'];
								}
								//info: prepare controlbox update
								if ($objWorksheet->getCellByColumnAndRow(15, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(15, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(15, $row) == '1') ) {
										$controlbox = $objWorksheet->getCellByColumnAndRow(15, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'controlbox', $row, $objWorksheet->getCellByColumnAndRow(15, $row), $existing_marker_data['controlbox']) . '</span><br/>';
										$stats_warnings[] = $row;
										$controlbox = $existing_marker_data['controlbox'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'controlbox', $row, $objWorksheet->getCellByColumnAndRow(15, $row), $existing_marker_data['controlbox']) . '</span><br/>';
									$stats_warnings[] = $row;
									$controlbox = $existing_marker_data['controlbox'];
								}
								//info: prepare createdby update
								$audit_option = $_POST['audit-option'];
								if ($audit_option == 'audit-on') {
									$createdby = $current_user->user_login;
								} else {
									$createdby = $objWorksheet->getCellByColumnAndRow(16, $row);
								}
								//info: prepare createdon update
								if ($audit_option == 'audit-on') {
									$createdon = current_time('mysql',0);
								} else {
									$createdon_format_check = date('Y-m-d H:i:s',strtotime($objWorksheet->getCellByColumnAndRow(17, $row)));
									if ($createdon_format_check != '1970-01-01 01:00:00') {
										$createdon = $createdon_format_check;
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'createdon', $row, $objWorksheet->getCellByColumnAndRow(17, $row), $existing_marker_data['createdon']) . '</span><br/>';
										$stats_warnings[] = $row;
										$createdon = $existing_marker_data['createdon'];
									}
								}
								//info: prepare updatedby update
								if ($audit_option == 'audit-on') {
									$updatedby = $current_user->user_login;
								} else {
									$updatedby = $objWorksheet->getCellByColumnAndRow(18, $row);
								}
								//info: prepare updatedon update
								if ($audit_option == 'audit-on') {
									$updatedon = current_time('mysql',0);
								} else {
									$updatedon_format_check = date('Y-m-d H:i:s',strtotime($objWorksheet->getCellByColumnAndRow(19, $row)));
									if ($updatedon_format_check != '1970-01-01 01:00:00') {
										$updatedon = $updatedon_format_check;
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'updatedon', $row, $objWorksheet->getCellByColumnAndRow(19, $row), $existing_marker_data['updatedon']) . '</span><br/>';
										$stats_warnings[] = $row;
										$updatedon = $existing_marker_data['updatedon'];
									}
								}
								//info: prepare kml_timestamp update
								if ($objWorksheet->getCellByColumnAndRow(20, $row)->getDataType() != 'null') {
									$kml_timestamp_format_check = date('Y-m-d H:i:s',strtotime($objWorksheet->getCellByColumnAndRow(20, $row)));
									if ($kml_timestamp_format_check != '1970-01-01 01:00:00') {
										$kml_timestamp = $kml_timestamp_format_check;
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'kml_timestamp', $row, $objWorksheet->getCellByColumnAndRow(20, $row), $existing_marker_data['kml_timestamp']) . '</span><br/>';
										$stats_warnings[] = $row;
										$kml_timestamp = $existing_marker_data['kml_timestamp'];
									}
								} else {
									$kml_timestamp = $existing_marker_data['kml_timestamp'];
								}
								//info: prepare overlays_custom update
								if ($objWorksheet->getCellByColumnAndRow(21, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(21, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(21, $row) == '1') ) {
										$overlays_custom = $objWorksheet->getCellByColumnAndRow(21, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom', $row, $objWorksheet->getCellByColumnAndRow(21, $row), $existing_marker_data['overlays_custom']) . '</span><br/>';
										$stats_warnings[] = $row;
										$overlays_custom = $existing_marker_data['overlays_custom'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom', $row, $objWorksheet->getCellByColumnAndRow(21, $row), $existing_marker_data['overlays_custom']) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom = $existing_marker_data['overlays_custom'];
								}
								//info: prepare overlays_custom2 update
								if ($objWorksheet->getCellByColumnAndRow(22, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(22, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(22, $row) == '1') ) {
										$overlays_custom2 = $objWorksheet->getCellByColumnAndRow(22, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom2', $row, $objWorksheet->getCellByColumnAndRow(22, $row), $existing_marker_data['overlays_custom2']) . '</span><br/>';
										$stats_warnings[] = $row;
										$overlays_custom2 = $existing_marker_data['overlays_custom2'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom2', $row, $objWorksheet->getCellByColumnAndRow(22, $row), $existing_marker_data['overlays_custom2']) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom2 = $existing_marker_data['overlays_custom2'];
								}
								//info: prepare overlays_custom3 update
								if ($objWorksheet->getCellByColumnAndRow(23, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(23, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(23, $row) == '1') ) {
										$overlays_custom3 = $objWorksheet->getCellByColumnAndRow(23, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom3', $row, $objWorksheet->getCellByColumnAndRow(23, $row), $existing_marker_data['overlays_custom3']) . '</span><br/>';
										$stats_warnings[] = $row;
										$overlays_custom3 = $existing_marker_data['overlays_custom3'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom3', $row, $objWorksheet->getCellByColumnAndRow(23, $row), $existing_marker_data['overlays_custom3']) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom3 =$existing_marker_data['overlays_custom3'];
								}
								//info: prepare overlays_custom4 update
								if ($objWorksheet->getCellByColumnAndRow(24, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(24, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(24, $row) == '1') ) {
										$overlays_custom4 = $objWorksheet->getCellByColumnAndRow(24, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom4', $row, $objWorksheet->getCellByColumnAndRow(24, $row), $existing_marker_data['overlays_custom4']) . '</span><br/>';
										$stats_warnings[] = $row;
										$overlays_custom4 = $existing_marker_data['overlays_custom4'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'overlays_custom4', $row, $objWorksheet->getCellByColumnAndRow(24, $row), $existing_marker_data['overlays_custom4']) . '</span><br/>';
									$stats_warnings[] = $row;
									$overlays_custom4 = $existing_marker_data['overlays_custom4'];
								}
								//info: prepare wms update
								if ($objWorksheet->getCellByColumnAndRow(25, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(25, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(25, $row) == '1') ) {
										$wms = $objWorksheet->getCellByColumnAndRow(25, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms', $row, $objWorksheet->getCellByColumnAndRow(25, $row), $existing_marker_data['wms']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms = $existing_marker_data['wms'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms', $row, $objWorksheet->getCellByColumnAndRow(25, $row), $existing_marker_data['wms']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms = $existing_marker_data['wms'];
								}
								//info: prepare wms2 update
								if ($objWorksheet->getCellByColumnAndRow(26, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(26, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(26, $row) == '1') ) {
										$wms2 = $objWorksheet->getCellByColumnAndRow(26, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms2', $row, $objWorksheet->getCellByColumnAndRow(26, $row), $existing_marker_data['wms2']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms2 = $existing_marker_data['wms2'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms2', $row, $objWorksheet->getCellByColumnAndRow(26, $row), $existing_marker_data['wms2']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms2 = $existing_marker_data['wms2'];
								}
								//info: prepare wms3 update
								if ($objWorksheet->getCellByColumnAndRow(27, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(27, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(27, $row) == '1') ) {
										$wms3 = $objWorksheet->getCellByColumnAndRow(27, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms3', $row, $objWorksheet->getCellByColumnAndRow(27, $row), $existing_marker_data['wms3']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms3 = $existing_marker_data['wms3'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms3', $row, $objWorksheet->getCellByColumnAndRow(27, $row), $existing_marker_data['wms3']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms3 = $existing_marker_data['wms3'];
								}
								//info: prepare wms4 update
								if ($objWorksheet->getCellByColumnAndRow(28, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(28, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(28, $row) == '1') ) {
										$wms4 = $objWorksheet->getCellByColumnAndRow(28, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms4', $row, $objWorksheet->getCellByColumnAndRow(28, $row), $existing_marker_data['wms4']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms4 = $existing_marker_data['wms4'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms4', $row, $objWorksheet->getCellByColumnAndRow(28, $row), $existing_marker_data['wms4']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms4 = $existing_marker_data['wms4'];
								}
								//info: prepare wms5 update
								if ($objWorksheet->getCellByColumnAndRow(29, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(29, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(29, $row) == '1') ) {
										$wms5 = $objWorksheet->getCellByColumnAndRow(29, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms5', $row, $objWorksheet->getCellByColumnAndRow(29, $row), $existing_marker_data['wms5']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms5 = $existing_marker_data['wms5'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms5', $row, $objWorksheet->getCellByColumnAndRow(29, $row), $existing_marker_data['wms5']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms5 = $existing_marker_data['wms5'];
								}
								//info: prepare wms6 update
								if ($objWorksheet->getCellByColumnAndRow(30, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(30, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(30, $row) == '1') ) {
										$wms6 = $objWorksheet->getCellByColumnAndRow(30, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms6', $row, $objWorksheet->getCellByColumnAndRow(30, $row), $existing_marker_data['wms6']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms6 = $existing_marker_data['wms6'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms6', $row, $objWorksheet->getCellByColumnAndRow(30, $row), $existing_marker_data['wms6']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms6 = $existing_marker_data['wms6'];
								}
								//info: prepare wms7 update
								if ($objWorksheet->getCellByColumnAndRow(31, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(31, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(31, $row) == '1') ) {
										$wms7 = $objWorksheet->getCellByColumnAndRow(31, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms7', $row, $objWorksheet->getCellByColumnAndRow(31, $row), $existing_marker_data['wms7']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms7 = $existing_marker_data['wms7'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms7', $row, $objWorksheet->getCellByColumnAndRow(31, $row), $existing_marker_data['wms7']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms7 = $existing_marker_data['wms7'];
								}
								//info: prepare wms8 update
								if ($objWorksheet->getCellByColumnAndRow(32, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(32, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(32, $row) == '1') ) {
										$wms8 = $objWorksheet->getCellByColumnAndRow(32, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms8', $row, $objWorksheet->getCellByColumnAndRow(32, $row), $existing_marker_data['wms8']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms8 = $existing_marker_data['wms8'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms8', $row, $objWorksheet->getCellByColumnAndRow(32, $row), $existing_marker_data['wms8']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms8 = $existing_marker_data['wms8'];
								}
								//info: prepare wms9 update
								if ($objWorksheet->getCellByColumnAndRow(33, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(33, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(33, $row) == '1') ) {
										$wms9 = $objWorksheet->getCellByColumnAndRow(33, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms9', $row, $objWorksheet->getCellByColumnAndRow(33, $row), $existing_marker_data['wms9']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms9 = $existing_marker_data['wms9'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms9', $row, $objWorksheet->getCellByColumnAndRow(33, $row), $existing_marker_data['wms9']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms9 = $existing_marker_data['wms9'];
								}
								//info: prepare wms10 update
								if ($objWorksheet->getCellByColumnAndRow(34, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(34, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(34, $row) == '1') ) {
										$wms10 = $objWorksheet->getCellByColumnAndRow(34, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms10', $row, $objWorksheet->getCellByColumnAndRow(34, $row), $existing_marker_data['wms10']) . '</span><br/>';
										$stats_warnings[] = $row;
										$wms10 = $existing_marker_data['wms10'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'wms10', $row, $objWorksheet->getCellByColumnAndRow(34, $row), $existing_marker_data['wms10']) . '</span><br/>';
									$stats_warnings[] = $row;
									$wms10 = $existing_marker_data['wms10'];
								}
								//info: prepare gpx_url update
								if ($objWorksheet->getCellByColumnAndRow(35, $row)->getDataType() != 'null') {
									$gpx_url = $objWorksheet->getCellByColumnAndRow(35, $row);
								} else {
									$gpx_url = '';
								}
								//info: prepare gpx_panel update
								if ($objWorksheet->getCellByColumnAndRow(36, $row)->getDataType() == 'n') {
									if ( ($objWorksheet->getCellByColumnAndRow(36, $row) == '0') || ($objWorksheet->getCellByColumnAndRow(36, $row) == '1') ) {
										$gpx_panel = $objWorksheet->getCellByColumnAndRow(36, $row);
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'gpx_panel', $row, $objWorksheet->getCellByColumnAndRow(36, $row), $existing_marker_data['gpx_panel']) . '</span><br/>';
										$stats_warnings[] = $row;
										$gpx_panel = $existing_marker_data['gpx_panel'];
									}
								} else {
									echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="warning">' . sprintf(__('Warning: invalid value for %1$s in row %2$s (%3$s) - using current value %4$s instead','lmm'), 'gpx_panel', $row, $objWorksheet->getCellByColumnAndRow(36, $row), $existing_marker_data['gpx_panel']) . '</span><br/>';
									$stats_warnings[] = $row;
									$gpx_panel = $existing_marker_data['gpx_panel'];
								}

								//info: geocoding address if set
								$geocoding_option = $_POST['geocoding-option'];
								if ($geocoding_option == 'geocoding-on') {
									$do_geocoding = lmm_getLatLng($address);
									if ($do_geocoding['success'] == true) {
										$lat = $do_geocoding['lat'];
										$lon = $do_geocoding['lon'];
										$address_from_import_file = $address;
										$address = $do_geocoding['address'];
										echo date('H:i:s') . ' <a name="' . $row . '"></a>' . sprintf(__('Geocoding result for address "%1$s" in row %2$s: "%3$s" (lat: %4$s, lon: %5$s)','lmm'), $address_from_import_file, $row, $address, $lat, $lon) . '<br/>';
									} else {
										echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="error">' . sprintf(__('Error: geocoding for address "%1$s" in row %2$s failed (%3$s) - skipping row','lmm'), $address, $row, $do_geocoding['message']) . '</span><br/>';
										$stats_errors[] = $row;
									}
								}

								//info: only save to database if test mode is off
								if ($test_mode == 'test-mode-off') {
									if ( (isset($do_geocoding) && ($do_geocoding['success'] == true)) || ($geocoding_option == 'geocoding-off') ) {
										if ($kml_timestamp == NULL) {
											$query_update = $wpdb->prepare( "UPDATE $table_name_markers SET markername = %s, basemap = %s, layer = %d, lat = %s, lon = %s, icon = %s, popuptext = %s, zoom = %d, openpopup = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, createdby = %s, createdon = %s, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %s, overlays_custom2 = %s, overlays_custom3 = %s, overlays_custom4 = %s, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d, address = %s, gpx_url = %s, gpx_panel = %d WHERE id = %d", $markername, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $address, $gpx_url, $gpx_panel, $existing_marker_data['id'] );
										} else {
											$query_update = $wpdb->prepare( "UPDATE $table_name_markers SET markername = %s, basemap = %s, layer = %d, lat = %s, lon = %s, icon = %s, popuptext = %s, zoom = %d, openpopup = %d, mapwidth = %d, mapwidthunit = %s, mapheight = %d, panel = %d, createdby = %s, createdon = %s, updatedby = %s, updatedon = %s, controlbox = %d, overlays_custom = %s, overlays_custom2 = %s, overlays_custom3 = %s, overlays_custom4 = %s, wms = %d, wms2 = %d, wms3 = %d, wms4 = %d, wms5 = %d, wms6 = %d, wms7 = %d, wms8 = %d, wms9 = %d, wms10 = %d, kml_timestamp = %s, address = %s, gpx_url = %s, gpx_panel = %d WHERE id = %d", $markername, $basemap, $layer, str_replace(',', '.', $lat), str_replace(',', '.', $lon), $icon, $popuptext, $zoom, $openpopup, $mapwidth, $mapwidthunit, $mapheight, $panel, $createdby, $createdon, $updatedby, $updatedon, $controlbox, $overlays_custom, $overlays_custom2, $overlays_custom3, $overlays_custom4, $wms, $wms2, $wms3, $wms4, $wms5, $wms6, $wms7, $wms8, $wms9, $wms10, $kml_timestamp, $address, $gpx_url, $gpx_panel, $existing_marker_data['id'] );
										}
										$result_update = $wpdb->query( $query_update );
										if ($result_update == TRUE) {
										echo date('H:i:s') . ' <span class="success">' . sprintf(__('The marker with the ID %1$s has been successfully updated','lmm'), '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$existing_marker_data['id'].'" title="' . esc_attr__('edit marker','lmm') . '" target="_top">' . $existing_marker_data['id'] . '</a>') . '</span><br/>';
											$stats_updated[] = $existing_marker_data['id'];
										} else {
											echo date('H:i:s') . ' <span class="error">' . sprintf(__('Error: the marker width the ID %1$s from row %1$s could not be updated.','lmm'), $existing_marker_data['id'], $row) . '</span><br/>';
											$stats_errors[] = $row;
										}
									}
								} else {
									if ( (isset($do_geocoding) && ($do_geocoding['success'] == true)) || ($geocoding_option == 'geocoding-off') ) { //info: needed for true stats if geocoding fails
										$stats_updated[] = $row;
									}
								}
							} else {
								echo date('H:i:s') . ' <a name="' . $row . '"></a><span class="error">' . sprintf(__('Error: a marker with the ID %1$s does not exist - skipping row %2$s','lmm'), $marker_id_check, $row) . '</span><br/>';
								$stats_errors[] = $row;
							}
						}
					echo '<hr noshade size="1" />';
					}

					/**********************************
					*       show import stats         *
					**********************************/
					echo '</div>'; //info: div for detailed-results
					echo '<div id="expand-results" stye="display:block;"><a href="javascript:show_results();">&rArr; ' . __('Show detailed results for each row','lmm') . '</a><br/></div>';
					echo '<hr noshade size="1" style="color:#000000;" />';
					$stats_created_count = count($stats_created);
					if ($stats_created_count != 0) {
						$stats_created_linked = array();
						foreach($stats_created as $row) {
							if ($test_mode == 'test-mode-off') {
								$stats_created_linked[] = '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$row.'" title="' . esc_attr__('edit marker','lmm') . '" target="_top">' . $row . '</a>';
							} else {
								$stats_created_linked[] = '<a href="#'.$row.'" title="' . esc_attr__('jump to log for this row from import file','lmm') . '">' . $row . '</a>';
							}
						}
						$stats_created_imploded = implode(", ",$stats_created_linked);
						if ($test_mode == 'test-mode-off') {
							echo date('H:i:s') . ' <span class="success">' . sprintf(__('%1$s new markers created (IDs: %2$s)','lmm'), $stats_created_count, $stats_created_imploded) . '</span><br/>';
						} else {
							echo date('H:i:s') . ' <span class="success">' . sprintf(__('%1$s new markers would be created (rows: %2$s)','lmm'), $stats_created_count, $stats_created_imploded) . '</span><br/>';
						}
					}
					$stats_updated_count = count($stats_updated);
					if ($stats_updated_count != 0) {
						$stats_updated_linked = array();
						foreach($stats_updated as $row) {
								$stats_updated_linked[] = '<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker&id='.$row.'" title="' . esc_attr__('edit marker','lmm') . '" target="_top">' . $row . '</a>';
						}
						$stats_updated_imploded = implode(", ",$stats_updated_linked);
						if ($test_mode == 'test-mode-off') {
							echo date('H:i:s') . ' <span class="success">' . sprintf(__('%1$s markers updated (IDs: %2$s)','lmm'), $stats_updated_count, $stats_updated_imploded) . '</span><br/>';
						} else {
							echo date('H:i:s') . ' <span class="success">' . sprintf(__('%1$s markers would be updated (IDs: %2$s)','lmm'), $stats_updated_count, $stats_updated_imploded) . '</span><br/>';
						}
					}
					if (function_exists('array_unique')) { $stats_warnings_count = count(array_unique($stats_warnings)); } else { $stats_warnings_count = count($stats_warnings); } //info: fallback for PHP 5.2.*
					if ($stats_warnings_count != 0) {
						$stats_warnings_linked = array();
						foreach($stats_warnings as $row) {
								$stats_warnings_linked[] = '<a href="#' . $row . '" title="' . esc_attr__('jump to warning message','lmm') . '">' . $row . '</a>';
						}
						if (function_exists('array_unique')) { $stats_warnings_imploded = implode(", ",array_unique($stats_warnings_linked)); } else { $stats_warnings_imploded = implode(", ",$stats_warnings_linked); } //info: fallback for PHP 5.2.*
						echo date('H:i:s') . ' <span class="warning">' . sprintf(__('%1$s warnings (rows from importfile: %2$s)','lmm'), $stats_warnings_count, $stats_warnings_imploded) . '</span><br/>';
					}
					$stats_errors_count = count($stats_errors);
					if ($stats_errors_count != 0) {
						$stats_errors_linked = array();
						foreach($stats_errors as $row) {
								$stats_errors_linked[] = '<a href="#'.$row.'" title="' . esc_attr__('jump to error message','lmm') . '">' . $row . '</a>';
						}
						$stats_errors_imploded = implode(", ",$stats_errors_linked);
						echo date('H:i:s') . ' <span class="error">' . sprintf(__('%1$s errors (skipped rows from importfile: %2$s)','lmm'), $stats_errors_count, $stats_errors_imploded) . '</span><br/>';
					}

					if ($test_mode == 'test-mode-off') {
						echo date('H:i:s') .' ' . __('Import finished','lmm') . '<br/>';
					} else {
						if ( ($stats_errors_count == 0) && ($stats_warnings_count == 0) ) {
							echo date('H:i:s') .' ' . '<span class="success">' . __('Info: your import file is valid - no errors or warnings found.','lmm') . '</span><br/>';
						} else {
							if ($stats_errors_count != 0) {
								echo date('H:i:s') .' <span class="error">' . __('Errors found! Affected rows from import file would be skipped!','lmm') . '</span><br/>';
							}
							if ($stats_warnings_count != 0) {
								echo date('H:i:s') .' <span class="warning">' . __('Warnings found! Affected rows would be processed (if no additional errors were found) but default values might be used!','lmm') . '</span><br/>';
							}
						}
					}

				} else {
					echo date('H:i:s') . ' <span class="error">' . __('Import failed - header row not found or invalid','lmm') . '</span><br/>';
					if ($import_file_extension == 'CSV') {
						echo date('H:i:s') . ' <span class="error">' . __('Please also check if you are using semicolons (;) as delimiters in your import file!','lmm') . '</span><br/>';
					}
				}

				//info: cleanup to free memory
				$objPHPExcel->disconnectWorksheets();
				unset($objPHPExcel);

				echo date('H:i:s') . ' ' . sprintf(__('Current memory usage: %1$s MB','lmm'), (memory_get_usage(true) / 1024 / 1024)) . '<br/>';
				if (function_exists('memory_get_peak_usage')) {
					echo date('H:i:s') . ' ' . sprintf(__('Peak memory usage: %1$s','lmm'), (memory_get_peak_usage(true) / 1024 / 1024) . 'MB') . '<br/>';
				} else {
					echo date('H:i:s') . ' ' . sprintf(__('Peak memory usage: %1$s','lmm'), __('not available','lmm')) . '<br/>';
				}
				echo date('H:i:s') . ' ' . __('End of run','lmm') . '<br/><br/>';
				echo '<a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
				echo '</body></html>';
			} else if ($_FILES['import-file']['error'] == 1) {
				echo __('Error: the import file exceeds the upload_max_filesize directive in php.ini','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			} else if ($_FILES['import-file']['error'] == 2) {
				echo __('Error: the import file could not be uploaded - please check your php error logs for more details','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			} else if ($_FILES['import-file']['error'] == 3) {
				echo __('Error: the import file was only partially uploaded.','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			} else if ($_FILES['import-file']['error'] == 4) {
				echo __('Error: no file was uploaded','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			} else if ($_FILES['import-file']['error'] == 6) {
				echo __('Error: a temporary folder is missing on your server','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			} else if ($_FILES['import-file']['error'] == 7) {
				echo __('Error: failed to write to disk','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			} else if ($_FILES['import-file']['error'] == 8) {
				echo __('Error: a PHP extension stopped the import file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help','lmm');
				echo '<br/><br/><a href="javascript:back();">' . __('Go back to "prepare import"','lmm') . '</a>';
			}
		//info: end ($action_standalone == import)
		} else if ($action_standalone == 'export') {
			/**********************************
			*       export action             *
			**********************************/
			//info: prepare sql for layer filter
			if ( $_POST['filter-layer'] == 'select-all' ) {
				$filter_layer_sql = '(1=1)';
			} else {
				$filter_layer_sql = 'layer = ' . intval($_POST['filter-layer']);
			}
			//info: prepare sql for optional 1
			$filter_option1_sql = '(';
			if ( $_POST['filter-markername'] == NULL ) {
				$filter_option1_sql .= '(1=1)';
			} else {
				$filter_option1_sql .= 'markername LIKE "%' . mysql_real_escape_string($_POST['filter-markername']) . '%"';
			}
			if ( ($_POST['filter-markername'] == NULL) || ($_POST['filter-popuptext'] == NULL) ) {
					$filter_option1_sql .= ' AND '; //info: otherwise search for popuptext only returns all results
			} else {
					$filter_option1_sql .= ' ' . mysql_real_escape_string($_POST['filter-operator1']) . ' ';
			}
			if ( $_POST['filter-popuptext'] == NULL ) {
				$filter_option1_sql .= '(1=1)';
			} else {
				$filter_option1_sql .= 'popuptext LIKE "%' . mysql_real_escape_string($_POST['filter-popuptext']) . '%"';
			}
			$filter_option1_sql .= ')';
			//info: prepare sql for optional 2
			$filter_option2_sql = '(';
			if ( $_POST['filter-exclude-markername'] == NULL ) {
				$filter_option2_sql .= '(1=1)';
			} else {
				$filter_option2_sql .= 'markername NOT LIKE "%' . mysql_real_escape_string($_POST['filter-exclude-markername']) . '%"';
			}
			if ( ($_POST['filter-exclude-markername'] == NULL) || ($_POST['filter-exclude-popuptext'] == NULL) ) {
					$filter_option2_sql .= ' AND '; //info: otherwise search for popuptext only returns all results
			} else {
					$filter_option2_sql .= ' ' . mysql_real_escape_string($_POST['filter-operator2']) . ' ';
			}
			if ( $_POST['filter-exclude-popuptext'] == NULL ) {
				$filter_option2_sql .= '(1=1)';
			} else {
				$filter_option2_sql .= 'popuptext NOT LIKE "%' . mysql_real_escape_string($_POST['filter-exclude-popuptext']) . '%"';
			}
			$filter_option2_sql .= ')';
			//info: filter for marker icons
			if ( $_POST['filter-icon'] == 'icon-any' ) {
				$filter_icons_sql = '(1=1)';
			} else if ( $_POST['filter-icon'] == 'icon-any' ) {
				$filter_icons_sql = '(icon = "")';
			} else {
				$filter_icons_sql = '(icon = "' . mysql_real_escape_string($_POST['filter-icon']) . '")';
			}
			$filter_limit_from = intval($_POST['limit-from']);
			$filter_limit_to = intval($_POST['limit-to']);
			$export_rows = $wpdb->get_results("SELECT * FROM $table_name_markers WHERE $filter_layer_sql AND $filter_option1_sql AND $filter_option2_sql AND $filter_icons_sql LIMIT $filter_limit_from, $filter_limit_to", ARRAY_A);

			//info: set document properties
			global $user;
			$objPHPExcel->getProperties()->setCreator("$current_user->user_login")
								 ->setLastModifiedBy("$current_user->user_login")
								 ->setTitle("MapsMarkerPro Export")
								 ->setDescription("Marker export created with MapsMarkerPro (http://www.mapsmarker.com), using PHPExcel (http://phpexcel.codeplex.com)")
								 ->setKeywords("MapsMarkerPro PHPExcel");

			 //info: rename worksheet
			$objPHPExcel->getActiveSheet()->setTitle('MapsMarkerPro-Export');
			//info: set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			//info: activate autofilter
			$objPHPExcel->getActiveSheet()->setAutoFilter('A1:AK1');

			//info: add header data
			$headings = array('id','markername','popuptext','openpopup','address','lat','lon','layer','zoom','icon','mapwidth','mapwidthunit','mapheight','basemap','panel','controlbox','createdby','createdon','updatedby','updatedon','kml_timestamp','overlays_custom','overlays_custom2','overlays_custom3','overlays_custom4','wms','wms2','wms3','wms4','wms5','wms6','wms7','wms8','wms9','wms10','gpx_url','gpx_panel');
			$rowNumber = 1;
			$col = 'A';
			foreach($headings as $heading) {
			   $objPHPExcel->getActiveSheet()->setCellValue($col.$rowNumber,$heading);
			   $col++;
			}
			$rowNumber = 2;
			$array_count_total = count($export_rows);
			$array_count_current = 0;

			$export_format = $_POST['export-format'];

			while ($array_count_current < $array_count_total) {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$rowNumber,$export_rows[$array_count_current]['id']);
				if ($export_format != 'csv') {
					$objPHPExcel->getActiveSheet()->getStyle('B'.$rowNumber)->getAlignment()->setWrapText(true);
				}
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$rowNumber,stripslashes($export_rows[$array_count_current]['markername']));
				if ($export_format == 'csv') {
					$popuptext_prepare_escape1 = preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$export_rows[$array_count_current]['popuptext']);
					$popuptext_prepare_escape2 = str_replace("'", "'", $popuptext_prepare_escape1);
					$popuptext_prepare_escape3 = str_replace('"', '\'', $popuptext_prepare_escape2);
					$popuptext_escaped = $popuptext_prepare_escape3;
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber,$popuptext_escaped);
				} else {
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rowNumber)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$rowNumber,stripslashes(preg_replace('/(\015\012)|(\015)|(\012)/','<br/>',$export_rows[$array_count_current]['popuptext'])));
				}
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$rowNumber,$export_rows[$array_count_current]['openpopup']);
				if ($export_format == 'csv') {
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber,$export_rows[$array_count_current]['address']);
				} else {
					$objPHPExcel->getActiveSheet()->getStyle('C'.$rowNumber)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$rowNumber,stripslashes($export_rows[$array_count_current]['address']));
				}
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$rowNumber,$export_rows[$array_count_current]['lat']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$rowNumber,$export_rows[$array_count_current]['lon']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$rowNumber,$export_rows[$array_count_current]['layer']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$rowNumber,$export_rows[$array_count_current]['zoom']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$rowNumber,$export_rows[$array_count_current]['icon']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$rowNumber,$export_rows[$array_count_current]['mapwidth']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$rowNumber,$export_rows[$array_count_current]['mapwidthunit']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$rowNumber,$export_rows[$array_count_current]['mapheight']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$rowNumber,$export_rows[$array_count_current]['basemap']);
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$rowNumber,$export_rows[$array_count_current]['panel']);
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$rowNumber,$export_rows[$array_count_current]['controlbox']);
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$rowNumber,$export_rows[$array_count_current]['createdby']);
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$rowNumber,$export_rows[$array_count_current]['createdon']);
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$rowNumber,$export_rows[$array_count_current]['updatedby']);
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$rowNumber,$export_rows[$array_count_current]['updatedon']);
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$rowNumber,$export_rows[$array_count_current]['kml_timestamp']);
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$rowNumber,$export_rows[$array_count_current]['overlays_custom']);
				$objPHPExcel->getActiveSheet()->setCellValue('W'.$rowNumber,$export_rows[$array_count_current]['overlays_custom2']);
				$objPHPExcel->getActiveSheet()->setCellValue('X'.$rowNumber,$export_rows[$array_count_current]['overlays_custom3']);
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.$rowNumber,$export_rows[$array_count_current]['overlays_custom4']);
				$objPHPExcel->getActiveSheet()->setCellValue('Z'.$rowNumber,$export_rows[$array_count_current]['wms']);
				$objPHPExcel->getActiveSheet()->setCellValue('AA'.$rowNumber,$export_rows[$array_count_current]['wms2']);
				$objPHPExcel->getActiveSheet()->setCellValue('AB'.$rowNumber,$export_rows[$array_count_current]['wms3']);
				$objPHPExcel->getActiveSheet()->setCellValue('AC'.$rowNumber,$export_rows[$array_count_current]['wms4']);
				$objPHPExcel->getActiveSheet()->setCellValue('AD'.$rowNumber,$export_rows[$array_count_current]['wms5']);
				$objPHPExcel->getActiveSheet()->setCellValue('AE'.$rowNumber,$export_rows[$array_count_current]['wms6']);
				$objPHPExcel->getActiveSheet()->setCellValue('AF'.$rowNumber,$export_rows[$array_count_current]['wms7']);
				$objPHPExcel->getActiveSheet()->setCellValue('AG'.$rowNumber,$export_rows[$array_count_current]['wms8']);
				$objPHPExcel->getActiveSheet()->setCellValue('AH'.$rowNumber,$export_rows[$array_count_current]['wms9']);
				$objPHPExcel->getActiveSheet()->setCellValue('AI'.$rowNumber,$export_rows[$array_count_current]['wms10']);
				$objPHPExcel->getActiveSheet()->setCellValue('AJ'.$rowNumber,$export_rows[$array_count_current]['gpx_url']);
				$objPHPExcel->getActiveSheet()->setCellValue('AK'.$rowNumber,$export_rows[$array_count_current]['gpx_panel']);
				$rowNumber++;
				$array_count_current++;
			}

			//info: freeze pane so that the heading line will not scroll
			$objPHPExcel->getActiveSheet()->freezePane('A2');

			//info: set column widths
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(70);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(12);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(8);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(9);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setAutoSize(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(13);

			//info: prepare output file
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/download");
			header("Content-Type: application/octet-stream");
			$filename = 'MapsMarkerPro-Export-' . date("Y-m-d_H-i");

			$export_format = $_POST['export-format'];
			if ($export_format == 'csv') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
				$objWriter->setDelimiter(';');
				header("Content-Type: text/csv");
				header("Content-Disposition: attachment;filename=" . $filename . ".csv");
				header("Content-Transfer-Encoding: binary ");
			} else if ($export_format == 'excel5') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				header("Content-Transfer-Encoding: binary ");
				header("Content-Type: application/vnd.ms-excel");
				header("Content-Disposition: attachment;filename=" . $filename . ".xls");
			} else if ($export_format == 'exel2007') {
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header("Content-Disposition: attachment;filename=" . $filename . ".xlsx");
				header("Content-Transfer-Encoding: binary ");
			}
			$objWriter->save('php://output');

			//info: cleanup to free memory
			unset($export_rows);
			$objPHPExcel->disconnectWorksheets();
			unset($objPHPExcel);
		} //info: end (action_standalone == export)
	} //info: end (action_standalone != NULL) - shared code for import/export
} //info: end plugin active check
?>