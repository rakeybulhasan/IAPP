<?php
header('Content-Type: text/html; charset=UTF-8');
//info: construct path to wp-load.php and get $wp_path
while(!is_file('wp-load.php')){
  if(is_dir('../')) chdir('../');
  else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
//info: security check
$wpnonceicon = isset($_GET['_wpnonceicon']) ? $_GET['_wpnonceicon'] : '';
if (! wp_verify_nonce($wpnonceicon, 'icon-upload-nonce') ) { die(__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').''); };
require_once( ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'file.php' );
?>
<!DOCTYPE html>
<html>
<head>
<title><?php esc_attr_e('upload new icon','lmm'); ?></title>
<style type="text/css" media="screen">
.button-primary {
    -moz-text-blink: none;
    -moz-text-decoration-color: -moz-use-text-color;
    -moz-text-decoration-line: none;
    -moz-text-decoration-style: solid;
    background-color: #21759B;
    background-image: linear-gradient(to bottom, #2A95C5, #21759B);
    border-bottom-color: #1E6A8D;
    border-left-color-ltr-source: physical;
    border-left-color-rtl-source: physical;
    border-left-color-value: #21759B;
    border-right-color-ltr-source: physical;
    border-right-color-rtl-source: physical;
    border-right-color-value: #21759B;
    border-top-color: #21759B;
    box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset;
    color: #FFFFFF;
    text-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
    -moz-box-sizing: border-box;
    border-bottom-left-radius: 3px;
    border-bottom-right-radius: 3px;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    border-left-style-ltr-source: physical;
    border-left-style-rtl-source: physical;
    border-left-style-value: solid;
    border-left-width-ltr-source: physical;
    border-left-width-rtl-source: physical;
    border-left-width-value: 1px;
    border-right-style-ltr-source: physical;
    border-right-style-rtl-source: physical;
    border-right-style-value: solid;
    border-right-width-ltr-source: physical;
    border-right-width-rtl-source: physical;
    border-right-width-value: 1px;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
    border-top-style: solid;
    border-top-width: 1px;
    cursor: pointer;
    display: inline-block;
    font-size: 12px;
    height: 24px;
    line-height: 23px;
    margin-bottom: 0;
    margin-left: 0;
    margin-right: 0;
    margin-top: 0;
    padding-bottom: 1px;
    padding-left: 10px;
    padding-right: 10px;
    padding-top: 0;
    white-space: nowrap;
    border-bottom-color: #DFDFDF;
    border-left-color-ltr-source: physical;
    border-left-color-rtl-source: physical;
    border-left-color-value: #DFDFDF;
    border-right-color-ltr-source: physical;
    border-right-color-rtl-source: physical;
    border-right-color-value: #DFDFDF;
    border-top-color: #DFDFDF;
}
</style>
</head>
<body>
<?php 
//info: set custom marker icon dir/url
$lmm_options = get_option( 'leafletmapsmarker_options' );
if ( $lmm_options['defaults_marker_custom_icon_url_dir'] == 'no' ) {
	$defaults_marker_icon_dir = LEAFLET_PLUGIN_ICONS_DIR;
} else {
	$defaults_marker_icon_dir = htmlspecialchars($lmm_options['defaults_marker_icon_dir']);
}
if (is_writeable($defaults_marker_icon_dir)) {
	echo '<span style="font-size:14px;">' . __('Please select icon to upload (allowed file types: png, gif)','lmm') . '</span>';
	echo '<form enctype="multipart/form-data" action="" method="post" style="margin-top:3px;">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />';
	echo '<input type="file" name="uploadFile"/>';
	echo '<input type="submit" name="upload-submit" class="button-primary" value="' . esc_attr__('upload','lmm') . '"/>';
	echo '</form>';
	if ( isset($_FILES['uploadFile']['name']) && ($_FILES['uploadFile']['name'] == TRUE) ){
		if ( ($_FILES['uploadFile']['type'] == 'image/png') || ($_FILES['uploadFile']['type'] == 'image/gif') ) {
			WP_Filesystem();
			global $wp_filesystem;
			$wp_filesystem->put_contents(
			$defaults_marker_icon_dir . DIRECTORY_SEPARATOR . basename($_FILES['uploadFile']['name']),
			file_get_contents($_FILES['uploadFile']['tmp_name']),
			FS_CHMOD_FILE);
			echo '<span style="font-size:14px;color:green;font-weight:bold;">' . sprintf(__('Upload successful - <a href="%1$s" target="_top">please reload page</a>','lmm'), LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker') . '</span>';
		} else {
			echo '<span style="font-size:14px;color:red;font-weight:bold;">' . __('Upload failed - unsupported file type!','lmm') . '</span>';
		}
	}
} else {
	echo '<span style="font-size:14px;color:red;font-weight:bold;">' . sprintf(__('Marker icon directory %s is not writable - please set permissions via FTP with CHMOD command to 755','lmm'), $defaults_marker_icon_dir) . '</span>';
}
?>
</body>
</html>