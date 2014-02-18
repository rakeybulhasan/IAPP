<?php
while(!is_file('wp-load.php')){
  if(is_dir('../')) chdir('../');
  else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
<title>Changelog for Leaflet Maps Marker Pro</title>
<style type="text/css">
body {
	font-family: sans-serif;
	padding:0 0 0 5px;
	margin:0px;
	font-size: 12px;
	line-height: 1.4em;
}
table {
	line-height:0.7em;
	font-size:12px;
	font-family:sans-serif;
}
td {
	line-height:1.1em;
}
.updated {
	padding:10px;
	background-color: #FFFFE0;
}
a {
	color: #21759B;
	text-decoration: none;
}
a:hover, a:active, a:focus {
	color: #D54E21;
}
hr {
	color: #E6DB55;
}
</style>
</head>
<body>
<?php
if (get_option('leafletmapsmarker_update_info') == 'show') {
	$lmm_version_old = get_option( 'leafletmapsmarker_version_pro_before_update' );
	$lmm_version_new = get_option( 'leafletmapsmarker_version_pro' );
/*******************************************************************************************************************************/
/* 2do: change verion numbers and date in first line on each update and add if ( ($lmm_version_old < 'x.x' ) ){ to old changelog
********************************************************************************************************************************
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.6') . '</strong> - ' . __('released on','lmm') . ' xx.11.2013 (<a href="http://www.mapsmarker.com/v1.6p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>

		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>

		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>

		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>

		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Known issues','lmm') . '</a></p></strong>
		<p>' . __('Although we tried hard, not all known issues could be fixed with this release:','lmm') . '</p>
		</td></tr>
		</table>'.PHP_EOL;
*******************************************************************************************************************************/

		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.5.1') . '</strong> - ' . __('released on','lmm') . ' 07.12.2013 (<a href="http://www.mapsmarker.com/v1.5.1p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		upgrade to leaflet.js v0.7.1 with 7 bugfixes (<a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#071-december-6-2013" target="_blank">detailed changelog</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		duplicate markers feature
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to use Google Maps API for Business for csv/xls/xlsx/ods import geocoding (which allows up to 100.000 instead of 2.500 requests per day)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		geocoding for csv/xls/xlsx/ods import: if Google Maps API returns error OVER_QUERY_LIMIT, wait 1.5sec and try again once
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized backend pages for WordPress 3.8/MP6 theme (re-added separator lines, reduce white space usage)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		geocoding for MapsMarker API requests: if Google Maps API returns error OVER_QUERY_LIMIT, wait 1.5sec and try again once
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		hardened SQL statements needed for fullscreen maps by additionally using prepared-statements
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		change main menu and admin bar entry from "Maps Marker" to "Maps Marker Pro" again to avoid confusion with lite version
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed link from main admin bar menu entry ("Maps Marker Pro") for better usability on mobile devices
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		broken terms of service and feedback links on Google marker maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		broken Google Adsense ad links on layer maps
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
		</td></tr>
		</table>'.PHP_EOL;

	if ( ( $lmm_version_old < '1.5' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.5') . '</strong> - ' . __('released on','lmm') . ' 01.12.2013 (<a href="http://www.mapsmarker.com/v1.5p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		upgrade to leaflet.js v0.7 with lots of improvements and bugfixes (more infos: <a href="http://leafletjs.com/2013/11/18/leaflet-0-7-released-plans-for-future.html" target="_blank">release notes</a> and <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md#07-november-18-2013" target="_blank">detailed changelog</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		global maximum zoom level (21) for all basemaps with automatic upscaling if native maximum zoom level is lower
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		improved accessibility by adding marker name as alt attribute for marker icon
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		compatibility with WordPress 3.8/MP6 (responsive admin template)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		HTML5 fullscreen updates: support for retina icon + different icon for on/off
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		cleaned up admin dashboard widget (showing blog post titles only)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		visualead QR code generation: API key needed for custom image url, added support for caching - see blog post for more details
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized license settings page for registering free 30-day-trials
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		maps break if the option worldCopyJump is set to true
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		toogle layers control image was not shown on mobile devices with retina display
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		undefined index message on pro plugin activation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fullscreen layer maps with no panel showed wrong layer center (thx Massimo!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		PHP warning message with debug enabled on license page when no license key was entered
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.4' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.4') . '</strong> - ' . __('released on','lmm') . ' 16.11.2013 (<a href="http://www.mapsmarker.com/v1.4p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		<a href="' . LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_import_export" target="_top">support for CSV/XLS/XLSX/ODS import and export for bulk additions and bulk updates of markers</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Norwegian (Bokmål) translation thanks to Inge Tang, <a href="http://drommemila.no" target="_blank">http://drommemila.no</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added a check if marker icon directory is writeable before trying to upload new icons
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		switched from curl() to wp_remote_post() on API geocoding calls for higher compatibility
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated markercluster codebase (<a href="https://github.com/Leaflet/Leaflet.markercluster/commits/master" target="_blank">using build from 13/11/2013</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		Improved error handling on metadata errors on bing maps - use console.log() instead of alert()
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		ensure zoom levels of google maps and leaflet maps stay in sync
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		remove zoomanim event handler in onRemove on google maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		alignment of panel and list marker icon images could be broken on certain themes
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		added fix for loading maps in woocommerce tabs (thx Glenn!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		default error tile image and map deleted image showed wrong www.mapsmarker.com url (ups)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		backslashes in map name and address broke GeoJSON output (and thus layer maps) - now replaced with /
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		tabs in popuptext (character literals) broke GeoJSON output (and thus layer maps) - now replaced with space
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, <a href="http://wp-translator.com" target="_blank">http://wp-translator.com</a>, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.3.1' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.3.1') . '</strong> - ' . __('released on','lmm') . ' 09.10.2013 (<a href="http://www.mapsmarker.com/v1.3.1p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		new options to set text color in marker cluster circles (thanks Simon!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed shortcode parsing in popup texts from layer maps completely
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		GeoJSON output for markers did not display marker name if parameter full was set to no
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		GeoJSON output could break if special characters were used in markername
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.3' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.3') . '</strong> - ' . __('released on','lmm') . ' 08.10.2013 (<a href="http://www.mapsmarker.com/v1.3p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for shortcodes in popup texts (with some limitations - <a href="http://www.mapsmarker.com/v1.3p" target="_blank">see release notes</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		set marker cluster colors in settings / map defaults / marker clustering settings
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		optimized marker and layer admin pages for mobile devices
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		notification about new pro versions now also works if access to plugin updates has expired
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized GeoJSON-mySQL-statement (less memory needed now on each execution)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized GeoJSON-output of directions link (using separate parameter dlink now)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized minimap toogle icon (with transition effect, thank robpvn!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed workaround for former incompatibility with jetpack plugin (has been fixed with jetpack 2.2)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		make custom update checker more consistent with how WP handles plugin updates (<a href="https://github.com/YahnisElsts/plugin-update-checker/commit/c3a8325c2d81be96c795aaf955aed44e1873f251" target="_blank">details</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		updated markercluster codebase (<a href="https://github.com/Leaflet/Leaflet.markercluster/commits/master" target="_blank">using build from 25/08/2013</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		tabs from address now get removed on edits as this breakes GeoJSON/layer maps (thx Chris!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		save button in settings was not accessible with certain languages active (thx Herbert!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		htmlspecialchars in marker name (< > &) were not shown correctly on hover text (thx fredel+devEdge!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		update class conflict with WordPress "quick edit" feature when debug bar plugin is active (<a href="https://github.com/YahnisElsts/plugin-update-checker/commit/2edd17e" target="_blank">details</a>)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		deleting layers when using custom capability settings was broken on layer edit page
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, <a href="http://wp-translator.com" target="_blank">http://wp-translator.com</a>, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.2.1' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.2.1') . '</strong> - ' . __('released on','lmm') . ' 14.09.2013 (<a href="http://www.mapsmarker.com/v1.2.1p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		<a title="click here for more information" href="http://www.mapsmarker.com/affiliateid" target="_blank">support for MapsMarker affiliate links instead of default backlinks - sign up as an affiliate and receive commissions up to 50% !</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		parsing of GeoJSON for layer maps is now up to 3 times faster by using JSON.parse instead of eval()
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		improved gpx backend proxy security by adding transients
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		using WordPress function antispambot() instead of own function hide_email() for API links
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		display gpx fitbounds-link already on focusing gpx url field (when pasting gpx URL manually)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		MapsMarker API - icon-parameter could not be set (always returned null) - thx Hovhannes!
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed broken settings page when plugin wp photo album plus was active (thx Martin!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Wikitude API was not accepted on registration if ar:name was empty (now using map type + id as fallback)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		plugin uninstall did not remove all database entries completely on multisite installations
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		incorrect warning on multisite installations to upgrade to latest free version before uninstalling
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Bosnian translation thanks to Kenan Dervišević, <a href="http://dkenan.com" target="_blank">http://dkenan.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese (zh_TW) translation thanks to jamesho Ho, <a href="http://outdooraccident.org" target="_blank">http://outdooraccident.org</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, <a href="http://wp-translator.com" target="_blank">http://wp-translator.com</a>, cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a> and Fabian Hurelle, <a href="http://hurelle.fr" target="_blank">http://hurelle.fr</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Indonesian translation thanks to Andy Aditya Sastrawikarta and Emir Hartato, <a href="http://whateverisaid.wordpress.com" target="_blank">http://whateverisaid.wordpress.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Polish translation thanks to Tomasz Rudnicki, <a href="http://www.kochambieszczady.pl" target="_blank">http://www.kochambieszczady.pl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.2' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.2') . '</strong> - ' . __('released on','lmm') . ' 31.08.2013 (<a href="http://www.mapsmarker.com/v1.2p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		support for displaying GPX tracks on marker and layer maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		option to whitelabel backend admin pages
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		advanced permission settings
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		optimized settings page (added direct links, return to last seen page after saving and full-text-search)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		removed visualead logo and backlink from QR code output pages
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		changed minimum required WordPress version from v3.0 to v3.3 (needed for tracks)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		increased database field for multi layer maps from 255 to 4000 (allowing you to add more layers to a multi layer map)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized marker and layer edit page (widened first column to better fit different browsers)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		allow custom icon upload only if user has the capability upload_files
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		optimized default backlinks and added QR-link to visualead
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		reduced maximum zoom level for bing maps to 19 as 21 is not supported worldwide
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		API does not break anymore if parameter type is not set to json or xml
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		marker icons in widgets were not aligned correctly on IE<9 on some themes
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		javascript errors on backend pages when clicking "show more" links
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Using W3 Total Cache >=v0.9.3 with active CDN no longer requires custom config
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		wrong image url on on backend edit pages resulting in 404 http request
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		wrong css url on on tools page resulting in 404 http request
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		plugin install failed if php_uname() had been disabled for security reasons (thx Stefan!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		Wikitude API was broken when multiple multi-layer-maps were selected
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		broken settings page when other plugins enqueued jQueryUI on all admin pages
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		undefined index error messages on recent marker widget with debug enabled
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Spanish/Mexico translation thanks to Victor Guevera, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Catalan translation thanks to Efraim Bayarri, <a href="http://replicantsfactory.com" target="_blank">http://replicantsfactory.com</a> and  Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Croatian translation thanks to Neven Pausic, <a href="http://www.airsoft-hrvatska.com" target="_blank">http://www.airsoft-hrvatska.com</a>, Alan Benic and Marijan Rajic, <a href="http://www.proprint.hr" target="_blank">http://www.proprint.hr</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, <a href="http://wp-translator.com" target="_blank">http://wp-translator.com</a> and cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Italian translation thanks to Luca Barbetti, <a href="http://twitter.com/okibone" target="_blank">http://twitter.com/okibone</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a> and <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Ukrainian translation thanks to Andrexj, <a href="http://all3d.com.ua" target="_blank">http://all3d.com.ua</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.1.2' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.1.2') . '</strong> - ' . __('released on','lmm') . ' 10.08.2013 (<a href="http://www.mapsmarker.com/v1.1.2p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		tweaked transparency for minimap toogle display (thx <a href="http://twitter.com/robpvn" target="_blank">@robpvn</a>!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		maps did not load correctly in (jquery ui) tabs (thx <a href="http://twitter.com/leafletjs" target="_blank">@leafletjs</a>!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		icon upload button got broken with WordPress 3.6
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		undefined index messages on license activation if debug is enabled
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		console warning message "Resource interpreted as script but transferred with MIME type text/plain."
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		preview of qr code image in settings was broken
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Catalan translation thanks to Efraim Bayarri, <a href="http://replicantsfactory.com" target="_blank">http://replicantsfactory.com</a> and  Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.1.1' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.1.1') . '</strong> - ' . __('released on','lmm') . ' 06.08.2013 (<a href="http://www.mapsmarker.com/v1.1.1p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to start an anonymous free 30-day-trial period
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Catalan translation thanks to Efraim Bayarri, <a href="http://replicantsfactory.com" target="_blank">http://replicantsfactory.com</a> and  Vicent Cubells, <a href="http://vcubells.net" target="_blank">http://vcubells.net</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Chinese translation thanks to John Shen, <a href="http://www.synyan.net" target="_blank">http://www.synyan.net</a> and ck
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Czech translation thanks to Viktor Kleiner and Vlad Kuzba, <a href="http://kuzbici.eu" target="_blank">http://kuzbici.eu</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated French translation thanks to Vincèn Pujol, <a href="http://www.skivr.com" target="_blank">http://www.skivr.com</a> and Rodolphe Quiedeville, <a href="http://rodolphe.quiedeville.org" target="_blank">http://rodolphe.quiedeville.org</a>, Fx Benard, <a href="http://wp-translator.com" target="_blank">http://wp-translator.com</a> and cazal cédric, <a href="http://www.cedric-cazal.com" target="_blank">http://www.cedric-cazal.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
		</td></tr>
		</table>'.PHP_EOL;
	}

	if ( ( $lmm_version_old < '1.1' ) && ( $lmm_version_old > '0' ) ) {
		echo '<p><hr noshade size="1"/></p>';
		echo '<p style="margin:0.5em 0 0 0;"><strong>' . sprintf(__('Changelog for version %s','lmm'), '1.1') . '</strong> - ' . __('released on','lmm') . ' 02.08.2013 (<a href="http://www.mapsmarker.com/v1.1p" target="_blank">' . __('blog post with more details about this release','lmm') . '</a>):</p>
		<table>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		upgraded leaflet.js ("the engine of this plugin") from v0.5.1 to v0.6.4 - please see <a href="http://leafletjs.com/2013/06/26/leaflet-0-6-released-dc-code-sprint-mapbox.html" target="_blank">blog post on leafletjs.com</a> and <a href="https://github.com/Leaflet/Leaflet/blob/master/CHANGELOG.md" target="_blank">full changelog</a> for more details
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		Leaflet Maps Marker Pro can now be tested on localhost installations without time limitation and on up to 25 domains on live installations
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		added option to switch update channel and download new beta releases (not advised on production sites!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		minimap now also supports bing maps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-new.png">
		</td><td>
		show compatibility warning if plugin "Dreamgrow Scrolled Triggered Box" is active (which is causing settings page to break)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		move scale control up when using Google basemaps in order not to hide the Google logo (thx Kendall!)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		reset option worldCopyJump to new default false instead of true (as advised by leaflet API docs)
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-changed.png">
		</td><td>
		using uglify v2 instead of v1 for javascript minification
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		minimaps caused main map to zoom change on move with low zoom
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		do not load Google Adsense ads on minimaps
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed warning message "constant SUHOSIN_PATCH not found"
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-fixed.png">
		</td><td>
		fixed warning message "Cannot modify header information" when plugin woocommerce is active
		</td></tr>
		<tr><td colspan="2">
		<p><strong>' . __('Translation updates','lmm') . '</a></p></strong>
		<p>' . sprintf(__('In case you want to help with translations, please visit the <a href="%1s" target="_blank">web-based translation plattform</a>','lmm'), 'http://translate.mapsmarker.com/projects/lmm') . '</p>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Bosnian translation thanks to Kenan Dervišević, <a href="http://dkenan.com" target="_blank">http://dkenan.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Croatian translation thanks to Neven Pausic, <a href="http://www.airsoft-hrvatska.com" target="_blank">http://www.airsoft-hrvatska.com</a>, Alan Benic and Marijan Rajic, <a href="http://www.proprint.hr" target="_blank">http://www.proprint.hr</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Dutch translation thanks to Patrick Ruers, <a href="http://www.stationskwartiersittard.nl" target="_blank">http://www.stationskwartiersittard.nl</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated German translation
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Korean translation thanks to Andy Park, <a href="http://wcpadventure.com" target="_blank">http://wcpadventure.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Latvian translation thanks to Juris Orlovs, <a href="http://lbpa.lv" target="_blank">http://lbpa.lv</a> and Eriks Remess <a href="http://geekli.st/Eriks" target="_blank">http://geekli.st/Eriks</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Romanian translation thanks to Arian, <a href="http://administrare-cantine.ro" target="_blank">http://administrare-cantine.ro</a> and Daniel Codrea, <a href="http://www.inadcod.com" target="_blank">http://www.inadcod.com</a>
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Slovak translation thanks to Zdenko Podobny
		</td></tr>
		<tr><td>
		<img src="' . LEAFLET_PLUGIN_URL .'inc/img/icon-changelog-translations.png">
		</td><td>
		updated Spanish translation thanks to Alvaro Lara, <a href="http://www.alvarolara.com" target="_blank">http://www.alvarolara.com</a>, Victor Guevara, <a href="http://1sistemas.net" target="_blank">http://1sistemas.net</a> and Ricardo Viteri, <a href="http://www.labviteri.com" target="_blank">http://www.labviteri.com</a>
		</td></tr>
		</table>'.PHP_EOL;
	}
	echo '</div>';
}
?>
</body>
</html>