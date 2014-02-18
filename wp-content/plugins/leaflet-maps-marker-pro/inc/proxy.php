<?php
// ############################################################################
// Proxy for getting GPX/KML files from external domains
// based on Simple PHP Proxy: Get external HTML, JSON and more!
// @author Travis Webb (travis.webb@nbtsolutions.com, travis@traviswebb.com.com)
// Project Home - http://benalman.com/projects/php-simple-proxy/
// 
// Copyright (c) 2010 "Cowboy" Ben Alman, Dual licensed under the MIT 
// and GPL licenses. http://benalman.com/about/license/
// ############################################################################

class LMM_Proxy {

    static function go($url, $send_headers = array()) {

	$enable_jsonp    = false;
        $enable_native   = true;
        $valid_url_regex = '/.*/';

        if ( !$url ) {
          
          // Passed url not specified.
          $contents = 'ERROR: url not specified';
          $status = array( 'http_code' => 'ERROR' );
          
        } else if ( !preg_match( $valid_url_regex, $url ) ) {
          
          // Passed url doesn't match $valid_url_regex.
          $contents = 'ERROR: invalid url';
          $status = array( 'http_code' => 'ERROR' );
          
        } else {
          $ch = curl_init( $url );
          
          if ( strtolower($_SERVER['REQUEST_METHOD']) == 'post' ) {
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $_POST );
          }
          $send_cookies = (isset($_GET['send_cookies']) ? $_GET['send_cookies'] : '');
          if ( $send_cookies != NULL ) {
            $cookie = array();
            foreach ( $_COOKIE as $key => $value ) {
              $cookie[] = $key . '=' . $value;
            }
            if ( $_GET['send_session'] ) {
              $cookie[] = SID;
            }
            $cookie = implode( '; ', $cookie );
            
            curl_setopt( $ch, CURLOPT_COOKIE, $cookie );
          }
          
          if( !ini_get('safe_mode') && !ini_get('open_basedir') ){
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	  }
          curl_setopt( $ch, CURLOPT_HEADER, true );
          // Add HTTP Headers, at least mimic client's http accept
          if ( empty($send_headers)) {
            $send_headers = array();
            if ( ! empty($_SERVER['HTTP_ACCEPT'])) {
              $send_headers[] = 'Accept: '.$_SERVER['HTTP_ACCEPT'];
            }
          }
          if ( ! empty($send_headers)) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $send_headers);
          }
          curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
          
		  $curl_user_agent = (isset($_GET['send_cookies']) ? $_GET['send_cookies'] : $_SERVER['HTTP_USER_AGENT']);
          curl_setopt( $ch, CURLOPT_USERAGENT, $curl_user_agent );

          list( $header, $contents ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 );
          
          $status = curl_getinfo( $ch );
          //
          curl_close( $ch );
        }

        // Split header text into an array.
        $header_text = preg_split( '/[\r\n]+/', $header );

		$mode = (isset($_GET['mode']) ? $_GET['mode'] : '');
        if ( $mode == 'native' ) {
		
          if ( !$enable_native ) {
            $contents = 'ERROR: invalid mode';
            $status = array( 'http_code' => 'ERROR' );
          }
          
          // Propagate headers to response.
          foreach ( $header_text as $header ) {
            if ( preg_match( '/^(?:Content-Type|Content-Language|Set-Cookie):/i', $header ) ) {
              header( $header );
            }
          }
          return $contents;
          
        } else {
          
          // $data will be serialized into JSON data.
          $data = array();
          
          // Propagate all HTTP headers into the JSON data object.
		  $full_headers = (isset($_GET['full_headers']) ? $_GET['full_headers'] : '');
          if ( $full_headers != NULL ) {

            $data['headers'] = array();
            
            foreach ( $header_text as $header ) {
              preg_match( '/^(.+?):\s+(.*)$/', $header, $matches );
              if ( $matches ) {
                $data['headers'][ $matches[1] ] = $matches[2];
              }
            }
          }
          
          // Propagate all cURL request / response info to the JSON data object.
		  $full_status = (isset($_GET['full_status']) ? $_GET['full_status'] : '');
          if ( $full_status != NULL ) {
            $data['status'] = $status;
          } else {
            $data['status'] = array();
            $data['status']['http_code'] = $status['http_code'];
          }
          
          // Set the JSON data object contents, decoding it from JSON if possible.
          $decoded_json = json_decode( $contents );
          $data['contents'] = $decoded_json ? $decoded_json : $contents;
          
          // Generate appropriate content-type header.
          $is_xhr = strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
          //2do: add kml/gxp option - header( 'Content-type: application/' . ( $is_xhr ? 'json' : 'x-javascript' ) );
		  header( 'Content-type: text/xml' );
          
          // Get JSONP callback.
          $jsonp_callback = $enable_jsonp && isset($_GET['callback']) ? $_GET['callback'] : null;
          
          // Generate JSON/JSONP string
          $json = json_encode( $data );
          
          //echo ($jsonp_callback ? "$jsonp_callback($json)" : $json);

	  //output
	  if ($data['status']['http_code'] == '200') {
		$gpx_content = $data['contents'];
		echo $gpx_content;
	  } else {
		//2do: where & how o check
	  }   

        }
    }
}

//info: construct path to wp-load.php
while(!is_file('wp-load.php')) {
	if(is_dir('..' . DIRECTORY_SEPARATOR)) chdir('..' . DIRECTORY_SEPARATOR);
	else die('Error: Could not construct path to wp-load.php - please check <a href="http://mapsmarker.com/path-error">http://mapsmarker.com/path-error</a> for more details');
}
include( 'wp-load.php' );
function hide_email($email) { $character_set = '+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz'; $key = str_shuffle($character_set); $cipher_text = ''; $id = 'e'.rand(1,999999999); for ($i=0;$i<strlen($email);$i+=1) $cipher_text.= $key[strpos($character_set,$email[$i])]; $script = 'var a="'.$key.'";var b=a.split("").sort().join("");var c="'.$cipher_text.'";var d="";'; $script.= 'for(var e=0;e<c.length;e++)d+=b.charAt(a.indexOf(c.charAt(e)));'; $script.= 'document.getElementById("'.$id.'").innerHTML="<a href=\\"mailto:"+d+"\\">"+d+"</a>"'; $script = "eval(\"".str_replace(array("\\",'"'),array("\\\\",'\"'), $script)."\")"; $script = '<script type="text/javascript">/*<![CDATA[*/'.$script.'/*]]>*/</script>'; return '<span id="'.$id.'">[javascript protected email address]</span>'.$script; }
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
	echo sprintf(__('The plugin "Leaflet Maps Marker" is inactive on this site and therefore this API link is not working.<br/><br/>Please contact the site owner (%1s) who can activate this plugin again.','lmm'), hide_email(get_bloginfo('admin_email')) );
} else {

	//info: proxy sec check
	$referer_marker = LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_marker';
	$referer_layer = LEAFLET_WP_ADMIN_URL . 'admin.php?page=leafletmapsmarker_layer';
	$transient_proxy_get = (isset($_GET['transient']) ? $_GET['transient'] : '');
	$transient_proxy = get_transient( 'leafletmapsmarkerpro_transient_proxy' );

	if ( ((strpos($_SERVER['HTTP_REFERER'], $referer_marker) === 0) || (strpos($_SERVER['HTTP_REFERER'], $referer_layer) === 0)) && ($transient_proxy_get == $transient_proxy) ){ 
		if (isset($_GET['url'])) {
			$run_proxy = new LMM_Proxy();
			$run_proxy->go($_GET['url']);
		}
	} else {
		die("".__('Security check failed - please call this function from the according Leaflet Maps Marker admin page!','lmm').""); 
	}
}