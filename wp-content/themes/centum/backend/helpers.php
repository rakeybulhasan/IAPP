<?php

// admin scripts

function add_video_wmode_transparent($html, $url, $attr) {

    if ( strpos( $html, "<embed src=" ) !== false )
     { return str_replace('</param><embed', '</param><param name="wmode" value="opaque"></param><embed wmode="opaque" ', $html); }
 elseif ( strpos ( $html, 'feature=oembed' ) !== false )
     { return str_replace( 'feature=oembed', 'feature=oembed&wmode=opaque', $html ); }
 else
     { return $html; }
}
add_filter( 'embed_oembed_html', 'add_video_wmode_transparent', 10, 3);



add_filter( 'wp_get_attachment_link', 'gallery_prettyPhoto');
function gallery_prettyPhoto ($content) {
    // add checks if you want to add prettyPhoto on certain places (archives etc).
    $pattern ="/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
    $replacement = '<a$1href=$2$3.$4$5 rel="image-gallery" $6>';
    $content = preg_replace($pattern, $replacement, $content);
    return $content;
    //return str_replace("<a", "<a rel='image-gallery'", $content);

}

function load_my_script() {
    global $pagenow;
    if (is_admin() && $pagenow == 'post-new.php' OR $pagenow == 'post.php') {
       if ( ! did_action( 'wp_enqueue_media' ) )
        wp_enqueue_media();
    wp_register_style('centum-css', get_template_directory_uri() . '/backend/css/centum.admin.css');
    wp_register_script('centum-scripts', get_template_directory_uri() . '/backend/js/script.js');
    wp_enqueue_style('centum-css');
    wp_enqueue_script('centum-scripts');
}
}
add_action('admin_enqueue_scripts', 'load_my_script');

function new_excerpt_more($more) {
    global $post;
    return '';
}

add_filter('excerpt_more', 'new_excerpt_more');

function string_limit_words($string, $word_limit) {
    $words = explode(' ', $string, ($word_limit + 1));
    if (count($words) > $word_limit) {
        array_pop($words);
        //add a ... at last article when more than limit word count
        return implode(' ', $words) ;
    } else {
        //otherwise
        return implode(' ', $words);
    }
}

function num_posts_portfolio($query){
    $showpost = ot_get_option('portfolio_showpost','20');
    if ($query->is_main_query() && $query->is_post_type_archive('portfolio') && !is_admin())
        $query->set('posts_per_page', $showpost);
}

add_action('pre_get_posts', 'num_posts_portfolio');


function filter_ot_recognized_font_families( $array, $field_id ) {


  if ( $field_id == 'incr_logo_typo' ) {

    global $google_fonts;
    $array = array( );
    foreach ($google_fonts as $font) {
      $array[$font['value']] = $font['label'];
  }

}

return $array;

}
add_filter( 'ot_recognized_font_families', 'filter_ot_recognized_font_families', 10, 2 );




add_filter('next_posts_link_attributes', 'posts_link_attributes_1');
add_filter('previous_posts_link_attributes', 'posts_link_attributes_2');

function posts_link_attributes_1() {
    return 'class="prev"';
}
function posts_link_attributes_2() {
    return 'class="next"';
}

function magnovus_menu_fb(){ ?>

<ul id="nav" class="main-menu">
    <?php
    wp_list_pages(array(
                'depth' => 2, //number of tiers, 0 for unlimited
                'exclude' => '', //comma seperated IDs of pages you want to exclude
                'title_li' => '', //must override it to empty string so that it does not break our nav
                'sort_column' => 'post_title', //see documentation for other possibilites
                'sort_order' => 'ASC', //ASCending or DESCending
                ));
                ?>
            </ul>

            <?php
        }



        function get_plusones($url) {

            $args = array(
                'method' => 'POST',
                'headers' => array(
                // setup content type to JSON
                    'Content-Type' => 'application/json'
                    ),
            // setup POST options to Google API
                'body' => json_encode(array(
                    'method' => 'pos.plusones.get',
                    'id' => 'p',
                    'method' => 'pos.plusones.get',
                    'jsonrpc' => '2.0',
                    'key' => 'p',
                    'apiVersion' => 'v1',
                    'params' => array(
                        'nolog'=>true,
                        'id'=> $url,
                        'source'=>'widget',
                        'userId'=>'@viewer',
                        'groupId'=>'@self'
                        )
                    )),
             // disable checking SSL sertificates
                'sslverify'=>false
                );

    // retrieves JSON with HTTP POST method for current URL
            $json_string = wp_remote_post("https://clients6.google.com/rpc", $args);

            if (is_wp_error($json_string)){
        // return zero if response is error
                return "0";
            } else {
                $json = json_decode($json_string['body'], true);
        // return count of Google +1 for requsted URL
                return intval( $json['result']['metadata']['globalCounts']['count'] );
            }
        }
/**
 * related post with category
 * @param: int $limit limit of posts
 * @param: bool $catName echo category name
 * @param: string $title string before all entries
 * Example: echo fb_cat_related_posts();
 */
if (!function_exists('fb_get_cat_related_posts')) {

    function fb_get_cat_related_posts($limit = 5, $catName = TRUE) {
        if (!is_single())
            return;
        $limit = (int) $limit;
        $output = '';

        $category = get_the_category();
        $category = (int) $category[0]->cat_ID;
        if ($catName)
            $output .= __('Categories: ','purepress') . get_cat_name($category) . ' ';

        $args = array(
            'numberposts' => $limit,
            'category' => $category,
            );


        $the_query = new WP_Query($args);
        echo "<ul>";
        while ($the_query->have_posts()) : $the_query->the_post();
        ?>
        <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if (has_post_thumbnail()) { ?>
            <div class="thumb-container">
                <a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'purepress'), the_title_attribute('echo=0')); ?>" rel="bookmark">
                    <?php the_post_thumbnail('small-thumb'); ?>
                </a>
                <a class="comments-link" href="<?php comments_link(); ?>"><?php echo get_comments_number(); ?></a>
            </div>
            <?php } ?>
            <div class="post-container <?php
            if (!has_post_thumbnail()) {
                echo "no-thumb";
            }
            ?> ">
            <h5 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'purepress'), the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title(); ?></a></h5>
            <a class="author-link" href="<?php echo get_author_posts_url(get_the_author_meta('ID' )); ?>"><?php the_author_meta('display_name'); ?></a>
            <?php printf('<a href="%1$s" class="published-time" title="%2$s" rel="bookmark">%3$s</a>', get_permalink(), esc_attr(get_the_time()), get_the_date()); ?>
        </div>
    </li><!-- #post-## -->
    <?php
        endwhile; // End the loop. Whew.
        echo "</ul>";
        wp_reset_postdata();
    }

}




function rarst_twitter_user( $username, $field, $display = false ) {
    $interval = 3600;
    $cache = get_option('rarst_twitter_user');
    $url = 'http://api.twitter.com/1/users/show.json?screen_name='.urlencode($username);

    if ( false == $cache )
        $cache = array();

// if first time request add placeholder and force update
    if ( !isset( $cache[$username][$field] ) ) {
        $cache[$username][$field] = NULL;
        $cache[$username]['lastcheck'] = 0;
    }

// if outdated
    if( $cache[$username]['lastcheck'] < (time()-$interval) ) {

// holds decoded JSON data in memory
        static $memorycache;

        if ( isset($memorycache[$username]) ) {
            $data = $memorycache[$username];
        }
        else {
            $result = wp_remote_retrieve_body(wp_remote_request($url));
            $data = json_decode( $result );
            if ( is_object($data) )
                $memorycache[$username] = $data;
        }

        if ( is_object($data) ) {
// update all fields, known to be requested
            foreach ($cache[$username] as $key => $value)
                if( isset($data->$key) )
                    $cache[$username][$key] = $data->$key;

                $cache[$username]['lastcheck'] = time();
            }
            else {
                $cache[$username]['lastcheck'] = time()+60;
            }

            update_option( 'rarst_twitter_user', $cache );
        }

        if ( false != $display )
            echo $cache[$username][$field];
        return $cache[$username][$field];
    }


    function diww_fb_fan_count($fb_id) {
       $link = json_decode(file_get_contents('http://graph.facebook.com/'.$fb_id.' '));
       echo $link->likes;
   }



   function feed_subscribers($url) {
    $feed_url = 'http://feeds.feedburner.com/'.$url;

    $count = get_transient('feed_count');
    if ($count != false) return $count;
    $count = 0;
    $data  = wp_remote_get('http://feedburner.google.com/api/awareness/1.0/GetFeedData?uri='.$feed_url.'');
    if (is_wp_error($data)) {
        return 'error';
    }else {
        $body = wp_remote_retrieve_body($data);
        $xml = new SimpleXMLElement($body);
        $status = $xml->attributes();
        if ($status == 'ok') {
            $count = $xml->feed->entry->attributes()->circulation;
        } else {
            $count = 300; // fallback number
        }
    }
    set_transient('feed_count', $count, 60*60*24); // 24 hour cache
    echo $count;
}


/**
 * get RSS readers count
 * @param string Feedburner id
 * @return int
 */
function getRssCount($feedburnerID)
{
    $url = "https://feedburner.google.com/api/awareness/1.0/GetFeedData?uri=". $feedburner_id;
    $data = wtf_api_call($url);
    if($data != false){
        try {
            $xml = new SimpleXMLElement($data);
            $count = $xml->feed->entry['circulation'];
        } catch (Exception $e) {
            return '0';
        }
        return $count;
    }
    return '0';
}

function wtf_api_call($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error){
            return $error;
        }
        return $data;
    } else {
                //cURL disabled on server
        return false;
    }
}




//gd rating function to get rating in ajax call

function gd_get_rating_ajax($post) {
    $rating = GDSRDatabase::get_post_data($post->ID);
    $uservoters = $rating->user_voters;
    $visitorvoters = $rating->visitor_voters;
    $votes = $uservoters + $visitorvotes;

    $uservotes = $rating->user_votes;
    $visitorvotes = $rating->visitor_votes;

    if ($votes > 0) $finalrating = number_format(($visitorvotes + $uservotes) / ($visitorvoters + $uservoters), 1);

    return GDSRRender::render_static_stars("magnovus", "20", "5", $finalrating, "", "", 1);

}


/**
 * http://bavotasan.com/2012/a-better-wp_link_pages-for-wordpress/
 */
function custom_wp_link_pages( $args = '' ) {
    $defaults = array(
        'before' => '<p id="post-pagination">',
        'after' => '</p>',
        'text_before' => '',
        'text_after' => '',
        'next_or_number' => 'number',
        'nextpagelink' => __( 'Next page','purepress' ),
        'previouspagelink' => __( 'Previous page','purepress' ),
        'pagelink' => '%',
        'echo' => 1
        );

    $r = wp_parse_args( $args, $defaults );
    $r = apply_filters( 'wp_link_pages_args', $r );
    extract( $r, EXTR_SKIP );

    global $page, $numpages, $multipage, $more, $pagenow;

    $output = '';
    if ( $multipage ) {
        if ( 'number' == $next_or_number ) {
            $output .= $before;
            for ( $i = 1; $i < ( $numpages + 1 ); $i = $i + 1 ) {
                $j = str_replace( '%', $i, $pagelink );
                $output .= ' ';
                if ( $i != $page || ( ( ! $more ) && ( $page == 1 ) ) )
                    $output .= _wp_link_page( $i );
                else
                    $output .= '<span class="current-post-page">';

                $output .= $text_before . $j . $text_after;
                if ( $i != $page || ( ( ! $more ) && ( $page == 1 ) ) )
                    $output .= '</a>';
                else
                    $output .= '</span>';
            }
            $output .= $after;
        } else {
            if ( $more ) {
                $output .= $before;
                $i = $page - 1;
                if ( $i && $more ) {
                    $output .= _wp_link_page( $i );
                    $output .= $text_before . $previouspagelink . $text_after . '</a>';
                }
                $i = $page + 1;
                if ( $i <= $numpages && $more ) {
                    $output .= _wp_link_page( $i );
                    $output .= $text_before . $nextpagelink . $text_after . '</a>';
                }
                $output .= $after;
            }
        }
    }

    if ( $echo )
        echo $output;

    return $output;
}



function body_sidebar_class($classes) {
    $options = get_option('magnovus');
    $layout = $options['sidebarside'];
    $classes[] = 'sb'.$layout;
    return $classes;
}

add_filter('body_class', 'body_sidebar_class');

function body_scheme_class($classes) {
    $style = get_theme_mod( 'centum_layout_style', 'boxed' ) ;
    $scheme = get_theme_mod( 'centum_scheme_switch', 'light' ) ;

    $classes[] = $style.' '.$scheme;
    return $classes;
}

add_filter('body_class', 'body_scheme_class');

function thumb_class($classes) {
    global $post;

    if(has_post_thumbnail($post->ID)) {
        $classes[] = "has-thumbnail";
    }
    return $classes;
}
add_filter('post_class','thumb_class');

function video_class($classes) {
    global $post;
    $type  = get_post_meta($post->ID, 'incr_pf_type', true);
    if(get_post_format( $post->ID ) == 'video' ) {
        $classes[] = "video-cont";
    }
    return $classes;
}
add_filter('post_class','video_class');


function bm_displayArchives() {
    global $month, $wpdb, $wp_version;

    // a mysql query to get the list of distinct years and months that posts have been created
    $sql = 'SELECT
    DISTINCT YEAR(post_date) AS year,
    MONTH(post_date) AS month,
    count(ID) as posts
    FROM ' . $wpdb->posts . '
    WHERE post_status="publish"
    AND post_type="post"
    AND post_password=""
    GROUP BY YEAR(post_date),
    MONTH(post_date)
    ORDER BY post_date DESC';

    // use get_results to do a query directly on the database
    $archiveSummary = $wpdb->get_results($sql);

    // if there are any posts
    if ($archiveSummary) {
        // loop through the posts
        foreach ($archiveSummary as $date) {
            // reset the query variable
            unset ($bmWp);
            // create a new query variable for the current month and year combination
            $bmWp = new WP_Query('year=' . $date->year . '&monthnum=' . zeroise($date->month, 2) . '&posts_per_page=50');

            // if there are any posts for that month display them
            if ($bmWp->have_posts()) {
                // display the archives heading
                $url = get_month_link($date->year, $date->month);
                $text = $month[zeroise($date->month, 2)] . ' ' . $date->year;

                echo get_archives_link($url, $text, '', '<h3>', '</h3>');
                echo '<ul class="circle_list">';

                // display an unordered list of posts for the current month
                while ($bmWp->have_posts()) {
                    $bmWp->the_post();
                    echo '<li><a href="' . get_permalink($bmWp->post) . '" title="' . esc_html($text, 1) . '">' . wptexturize($bmWp->post->post_title) . '</a></li>';
                }

                echo '</ul>';
            }
        }
    }
}

//class Walker_Nav_Menu_Dropdown extends Walker_Nav_Menu {
//    function start_lvl($output, $depth) {
//        $indent = str_repeat("\t", $depth); // don't output children opening tag (`<ul>`)
//    }
//
//    function end_lvl($output, $depth) {
//        $indent = str_repeat("\t", $depth); // don't output children closing tag
//    }
//
//    function start_el($output, $item, $depth, $args) {
//        // add spacing to the title based on the depth
//        $item->title = str_repeat("&nbsp;", $depth * 4).$item->title;
//
//        parent::start_el($output, $item, $depth, $args);
//
//        // no point redefining this method too, we just replace the li tag...
//        $output = str_replace('<li', '<option value="' . $item->url . '"', $output);
//    }
//
//    function end_el($output, $item, $depth) {
//        $output .= "</option>\n"; // replace closing </li> with the option tag
//    }
//}

class Walker_Nav_Menu_Dropdown extends Walker_Nav_Menu{
	var $to_depth = -1;
    function start_lvl(&$output, $depth){
      $output .= '</option>';
  }

  function end_lvl(&$output, $depth){
      $indent = str_repeat("\t", $depth); // don't output children closing tag
  }

  function start_el(&$output, $item, $depth, $args){
      $indent = ( $depth ) ? str_repeat( "&nbsp;", $depth * 4 ) : '';
      $class_names = $value = '';
      $classes = empty( $item->classes ) ? array() : (array) $item->classes;
      $classes[] = 'menu-item-' . $item->ID;
      $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
      $class_names = ' class="' . esc_attr( $class_names ) . '"';
      $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
      $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
      $value = ' value="'. $item->url .'"';
      $output .= '<option'.$id.$value.$class_names.'>';
      $item_output = $args->before;
      $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
      $output .= $indent.$item_output;
  }

  function end_el(&$output, $item, $depth){
      if(substr($output, -9) != '</option>')
      		$output .= "</option>"; // replace closing </li> with the option tag

      }
  }

  /****** Add Thumbnails in Manage Posts/Pages List ******/
  if ( !function_exists('AddThumbColumn') && function_exists('add_theme_support') ) {
    // for post and page
    add_theme_support('post-thumbnails', array( 'post', 'page' ) );
    function AddThumbColumn($cols) {
        $cols['thumbnail'] = __('Thumbnail','purepress');
        return $cols;
    }
    function AddThumbValue($column_name, $post_id) {
        $width = (int) 60;
        $height = (int) 60;
        if ( 'thumbnail' == $column_name ) {
            // thumbnail of WP 2.9
            $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
            // image from gallery
            $attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
            if ($thumbnail_id)
                $thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
            elseif ($attachments) {
                foreach ( $attachments as $attachment_id => $attachment ) {
                    $thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
                }
            }
            if ( isset($thumb) && $thumb ) {
                echo $thumb;
            } else {
                echo __('None','purepress');
            }
        }
    }
    // for posts
    add_filter( 'manage_posts_columns', 'AddThumbColumn' );
    add_action( 'manage_posts_custom_column', 'AddThumbValue', 10, 2 );
    // for pages
    add_filter( 'manage_pages_columns', 'AddThumbColumn' );
    add_action( 'manage_pages_custom_column', 'AddThumbValue', 10, 2 );
}


add_filter('user_contactmethods', 'my_user_contactmethods');

function my_user_contactmethods($user_contactmethods){

  $user_contactmethods['twitter'] = 'Twitter Username';
  $user_contactmethods['facebook'] = 'Facebook Username';
  $user_contactmethods['googleplus'] = 'Google Plus Profile ID';
  $user_contactmethods['flickr'] = 'Flickr';

  return $user_contactmethods;
}

function custom_password_form($form) {
  $subs = array(
    '#<form(.*?)>#' => '<form$1 class="passwordform">',
    );

  echo preg_replace(array_keys($subs), array_values($subs), $form);
}

add_filter('the_password_form', 'custom_password_form');


function mag_display_authors($role, $number){
    global $post;
    $my_users = new WP_User_Query(array(
       'role' => $role,
       'number' => $number
       ));
    $total_authors = $my_users->total_users;
    $authors = $my_users->get_results();
    if (!empty($authors))	 { ?>
    <ul class="author-list">
        <?php
                                        // loop through each author
        foreach($authors as $author){
            $author_info = get_userdata($author->ID);
            ?>
            <li>
                <?php echo get_avatar( $author->ID, 90 ); ?>
                <div class="author-data">
                    <h4><a href="<?php echo get_author_posts_url($author->ID); ?>"><?php echo $author_info->display_name; ?></a> - <?php echo count_user_posts( $author->ID ); ?> <?php _e(' posts', 'purepress'); ?></h4>
                    <?php if($author_info->description){ ?><p><?php echo $author_info->description; ?></p><?php } ?>
                    <?php $latest_post = new WP_Query( "author=$author->ID&post_count=1" );
                    if (!empty($latest_post->post)){ ?>
                    <p><strong><?php _e('Latest Article:', 'purepress'); ?></strong>
                        <a href="<?php echo get_permalink($latest_post->post->ID) ?>">
                            <?php echo get_the_title($latest_post->post->ID) ;?>
                        </a>
                        <br/><a class="moretag" href="<?php echo get_author_posts_url($author->ID); ?> "><?php _e('Read', 'purepress'); ?> <?php echo $author_info->display_name; ?> <?php _e('posts', 'purepress'); ?></a>
                    </p>
                    <?php } //endif ?>

                </div>
            </li>
            <?php
        }
        ?>
    </ul> <!-- .author-list -->
    <?php }
}

//twitter function
add_action( 'init', 'centum_twitter_api' );
function centum_twitter_api() {
    global $cb;
    $consumer_key = ot_get_option('pp_twitter_ck');
    $consumer_secret = ot_get_option('pp_twitter_cs');
    $access_token = ot_get_option('pp_twitter_at');
    $access_secret = ot_get_option('pp_twitter_ts');
    require_once('codebird.php');
    Codebird::setConsumerKey( $consumer_key, $consumer_secret );
    $cb = Codebird::getInstance();
    $cb->setToken( $access_token, $access_secret );
}


function short_title($after = '', $length) {
	$mytitle = explode(' ', get_the_title(), $length);
	if (count($mytitle)>=$length) {
		array_pop($mytitle);
		$mytitle = implode(" ",$mytitle). $after;
	} else {
		$mytitle = implode(" ",$mytitle);
	}
	return $mytitle;
}

add_filter( 'the_category', 'add_nofollow_cat' );
function add_nofollow_cat( $text) {
    $strings = array('rel="category"', 'rel="category tag"', 'rel="whatever may need"');
    $text = str_replace('rel="category tag"', "", $text);
    return $text;
}



function dimox_breadcrumbs() {

  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = ''; // delimiter between crumbs
  $home =  __('Home', 'purepress'); // text for the 'Home' link
  $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<li class="current_element">'; // tag before the current crumb
  $after = '</li>'; // tag after the current crumb

  global $post;
  $homeLink = home_url();
  $output = '';
  if (is_home() || is_front_page()) {

    if ($showOnHome == 1) $output .= '<ul id="breadcrumbs"></li><li><a href="' . $homeLink . '"></i>' . $home . '</a></li></ul>';

} else {

    $output .= '<ul id="breadcrumbs"><li><a href="' . $homeLink . '">' . $home . '</a>' . $delimiter . '</li> ';

      if ( is_category() ) {
          $thisCat = get_category(get_query_var('cat'), false);
          if ($thisCat->parent != 0) {
              $categoriesList = array_filter(explode(PHP_EOL, get_category_parents($thisCat->parent, TRUE, PHP_EOL)));

              $output .= '<li>' . implode("</li>" . $delimiter . "<li>", $categoriesList) .'</li>';
          }
          $output .= $before . single_cat_title('', false) . $after;

  } elseif ( is_search() ) {
      $output .= $before . 'Search results for "' . get_search_query() . '"' . $after;

  } elseif ( is_day() ) {
      $output .= '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . '</li> ';
      $output .= '<li><a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . '</li> ';
      $output .= $before . get_the_time('d') . $after;

  } elseif ( is_month() ) {
      $output .= '<li><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' </li>';
      $output .= $before . get_the_time('F') . $after;

  } elseif ( is_year() ) {
      $output .= $before . get_the_time('Y') . $after;

  } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        $output .= '<li><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li>';
        if ($showCurrent == 1) $output .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
    } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
          $categoriesList = array_filter(explode(PHP_EOL, get_category_parents($cat, TRUE, PHP_EOL)));

          $output .= '<li>' . implode("</li>" . $delimiter . "<li>", $categoriesList) .'</li>';
        //$output .= $cats;
        if ($showCurrent == 1) $output .= $before . get_the_title() . $after;
    }

} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
  $post_type = get_post_type_object(get_post_type());
  $output .= $before . $post_type->labels->singular_name . $after;

} elseif ( is_attachment() ) {
  $parent = get_post($post->post_parent);
  $cat = get_the_category($parent->ID); $cat = $cat[0];
  $output .= get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
  $output .= '<li><a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a></li>';
  if ($showCurrent == 1) $output .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

} elseif ( is_page() && !$post->post_parent ) {
  if ($showCurrent == 1) $output .= $before . get_the_title() . $after;

} elseif ( is_page() && $post->post_parent ) {
  $parent_id  = $post->post_parent;
  $breadcrumbs = array();
  while ($parent_id) {
    $page = get_page($parent_id);
    $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
    $parent_id  = $page->post_parent;
}
$breadcrumbs = array_reverse($breadcrumbs);
for ($i = 0; $i < count($breadcrumbs); $i++) {
    $output .= $breadcrumbs[$i];
    if ($i != count($breadcrumbs)-1) $output .= ' ' . $delimiter . ' ';
}
if ($showCurrent == 1) $output .= ' ' . $delimiter . ' ' . $before . get_the_title() . $after;

} elseif ( is_tag() ) {
  $output .= $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;

} elseif ( is_author() ) {
   global $author;
   $userdata = get_userdata($author);
   $output .= $before . 'Articles posted by ' . $userdata->display_name . $after;

} elseif ( is_404() ) {
  $output .= $before . 'Error 404' . $after;
}

if ( get_query_var('paged') ) {
  if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $output .= ' (';
      $output .= __('Page','purepress') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) $output .= ')';
}

$output .= '</ul>';
return $output;
}
} // end dimox_breadcrumbs()


function recent_porfolios() {
    global $post;
    $exclude = $post->ID;
    rewind_posts();

    // Create a new WP_Query() object
    $wpcust = new WP_Query(
        array(
            'post_type' => array('portfolio'),
            'post__not_in' => array($exclude),
            'showposts' => '4' ) // or 10 etc. however many you want
        );

        // the $wpcust-> variable is used to call the Loop methods. not sure if required
    if ( $wpcust->have_posts() ):
        while( $wpcust->have_posts() ) : $wpcust->the_post();
    ?>

    <div class="four columns ">
        <?php if ( has_post_thumbnail()) { ?>
        <div class="picture">
            <?php $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large'); ?>
            <a  href="<?php the_permalink(); ?>"> <?php the_post_thumbnail('portfolio-thumb'); ?><div class="image-overlay-link"></div></a>
        </div>
        <?php } ?>
        <div class="item-description related">
            <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
            <p><?php
            $excerpt = get_the_excerpt();
            echo string_limit_words($excerpt,11);
            ?> </p>
        </div>
    </div>


    <?php
        endwhile;  // close the Loop
        endif;
        wp_reset_query(); // reset the Loop

} // end of list_all_posttypes() function


function filter_next_post_link($link) {
    $link = str_replace("rel=", 'class="next" rel=', $link);
    return $link;
}
add_filter('next_post_link', 'filter_next_post_link');

function filter_previous_post_link($link) {
    $link = str_replace("rel=", 'class="prev" rel=', $link);
    return $link;
}
add_filter('previous_post_link', 'filter_previous_post_link');



function incr_number_to_width($width) {
    switch ($width) {
        case '1':
        return "one";
        break;
        case '2':
        return "two";
        break;
        case '3':
        return "three";
        break;
        case '4':
        return "four";
        break;
        case '5':
        return "five";
        break;
        case '6':
        return "six";
        break;
        case '7':
        return "seven";
        break;
        case '8':
        return "eight";
        break;
        case '9':
        return "nine";
        break;
        case '10':
        return "ten";
        break;
        case '11':
        return "eleven";
        break;
        case '12':
        return "twelve";
        break;

        default:
        return "four";
        break;
    }
}


function my_tag_cloud_args($in){
    return 'smallest=13&largest=13&number=25&orderby=name&unit=px';
}
add_filter( 'widget_tag_cloud_args', 'my_tag_cloud_args');


add_action ('admin_menu', 'themedemo_admin');
function themedemo_admin() {
    add_theme_page( 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
}



/**
 * Convert a hexa decimal color code to its RGB equivalent
 *
 * @param string $hexStr (hexadecimal color value)
 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
 */
function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
}


add_action( 'customize_register', 'themename_customize_register' );


function themename_customize_register($wp_customize) {


    // color section
  $wp_customize->add_section( 'centum_color_settings', array(
    'title'          => 'Main color',
    'priority'       => 35,
    ) );

  $wp_customize->add_setting( 'centum_main_color', array(
    'default'        => '#72B626',
    'transport' =>'postMessage'
    ) );

  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'centum_main_color', array(
    'label'   => 'Color Setting',
    'section' => 'colors',
    'settings'   => 'centum_main_color',
    )));

  $wp_customize->add_setting( 'centum_overlay_color', array(
    'default'        => '#000',
    'transport' =>'postMessage'
    ));

  $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'centum_overlay_color', array(
    'label'   => 'Overlay\'s Color',
    'section' => 'colors',
    'settings'   => 'centum_overlay_color',
    )));

  $wp_customize->add_setting( 'centum_overlay_opacity', array(
    'default'  => '0.7'

    ));
  $wp_customize->add_control( 'centum_overlay_opacity', array(
    'label'    => 'Select overlay opacity',
    'section'  => 'colors',
    'settings' => 'centum_overlay_opacity',
    'type'     => 'select',
    'choices'    => array(
        '0.1' => '0.1',
        '0.2' => '0.2',
        '0.3' => '0.3',
        '0.4' => '0.4',
        '0.5' => '0.5',
        '0.6' => '0.6',
        '0.7' => '0.7',
        '0.8' => '0.8',
        '0.9' => '0.9',
        '1' => '1',
        )
    ));

    // eof color section

    // bof layout section
  $wp_customize->add_section( 'centum_layout_settings', array(
    'title'          => 'Layout type',
    'priority'       => 36,
    ));

  $wp_customize->add_setting( 'centum_layout_style', array(
    'default'  => 'boxed',
    'transport' => 'postMessage'
    ));
  $wp_customize->add_control( 'centum_layout_choose', array(
    'label'    => 'Select layout',
    'section'  => 'centum_layout_settings',
    'settings' => 'centum_layout_style',
    'type'     => 'select',
    'choices'    => array(
        'boxed' => 'Boxed',
        'wide' => 'Wide',
        )
    ));

  $wp_customize->add_setting( 'centum_scheme_switch', array(
    'default'  => 'light',
    'transport' => 'postMessage'
    ));
  $wp_customize->add_control( 'centum_scheme', array(
    'label'    => 'Select main scheme color',
    'section'  => 'centum_layout_settings',
    'settings' => 'centum_scheme_switch',
    'type'     => 'select',
    'choices'    => array(
        'light' => 'Light',
        'dark' => 'Dark',
        )
    ));
   // eof layout section

  $wp_customize->add_setting( 'centum_tagline_switch', array(
    'default'  => 'show',
    'transport' => 'postMessage'
    ));
  $wp_customize->add_control( 'centum_tagline_switcher', array(
     'settings' => 'centum_tagline_switch',
     'label'    => __( 'Display Tagline','purepress' ),
     'section'  => 'title_tagline',
     'type'     => 'select',
     'choices'    => array(
        'show' => 'Show',
        'hide' => 'Hide',
        )
     ));


  if ( $wp_customize->is_preview() && !is_admin() )
    add_action( 'wp_footer', 'centum_customize_preview', 21);
}


function centum_customize_preview() {
    ?>
    <script type="text/javascript">
    ( function( $ ){
        function hex2rgb(hex) {
            if (hex[0]=="#") hex=hex.substr(1);
            if (hex.length==3) {
                var temp=hex; hex='';
                temp = /^([a-f0-9])([a-f0-9])([a-f0-9])$/i.exec(temp).slice(1);
                for (var i=0;i<3;i++) hex+=temp[i]+temp[i];
            }
        var triplets = /^([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i.exec(hex).slice(1);
        return {
            red: parseInt(triplets[0],16),
            green: parseInt(triplets[1],16),
            blue: parseInt(triplets[2],16)
        }
    }


    wp.customize('centum_main_color',function( value ) {
        value.bind(function(to) {
           $('#bolded-line, .button.color, input[type="button"]').css('background', to);
           $('body #filters a.selected,.pricing-table .color-3 h3, .color-3 .sign-up,.pricing-table .color-3 h4,#navigation  ul > li.current-menu-ancestor > a, #navigation > div > ul > li.current-menu-item > a, .flex-direction-nav .flex-prev:hover, .flex-direction-nav .flex-next:hover, #scroll-top-top a, .post-icon').css('background-color', to);
           $('.mr-rotato-prev:hover, .mr-rotato-next:hover,li.current, .tags a:hover').css('background-color',to).css('border-color',to);
           $('#filters a:hover, .selected, #portfolio-navi a:hover ').css('background-color',to).css('border-color',to);
           $('.testimonials-author, body .page:not(.home) a:not(.button), body .post:not(.home) a:not(.button)').css('color',to);

           $('#navigation ul li a, #navigation ul li > a, .button.gray').hover(function(){$(this).css('background', to);},function(){$(this).css('background', '#303030');} );
           $('.button.light').hover(function(){ $(this).css('background', to); }, function(){ $(this).css('background', '#AAAAAA'); });
           $('.flex-direction-nav .flex-prev, .flex-direction-nav .flex-next, .tp-leftarrow, .tp-rightarrow').hover(function(){$(this).css('background-color', to);},function(){$(this).css('background-color', 'rgba(0,0,0,0.6)');});

           $('.post-meta a,.acc-trigger a,.tabs-nav li a').css('color','#888');
           $('a.sign-up').css('color','#fff');
           $('.post-title h1 a, .post-title h2 a, .acc-trigger.active a,.tabs-nav li.active a').css('color','#404040');

       });
});

wp.customize('centum_overlay_color',function( value ) {
    value.bind(function(to) {
        var hex = to;
        var rgb = hex2rgb(to);

        //  alert(''+rgb.red+','+rgb.green+','+rgb.blue+',0.7');
        $('.image-overlay-link, .image-overlay-zoom').css('background-color', 'rgba('+rgb.red+','+rgb.green+','+rgb.blue+',<?php echo get_theme_mod( 'centum_overlay_opacity', '0.7' ) ?>)');
    });
});

wp.customize('centum_layout_style',function( value ) {
    value.bind(function(to) {
        var $style;
        if($('body').hasClass('dark')) { $style = 'dark' }
            if($('body').hasClass('light')) { $style = 'light' }

                $('#layout').attr('href', '<?php echo get_template_directory_uri(); ?>/css/' + $style + to + '.css');
            $('body').removeClass('wide').removeClass('boxed').addClass(to);
        });
});

wp.customize('centum_scheme_switch',function( value ) {
    value.bind(function(to) {
        var $style;
        if($('body').hasClass('boxed')) { $scheme = 'boxed' }
            if($('body').hasClass('wide')) { $scheme = 'wide' }

                $('#layout').attr('href', '<?php echo get_template_directory_uri(); ?>/css/'  + to + $scheme + '.css');
            $('body').removeClass('light').removeClass('dark').addClass(to);
        });
});

wp.customize('centum_tagline_switch',function( value ) {
    value.bind(function(to) {
        if(to === 'hide') { $('#tagline').hide(); } else { $('#tagline').show(); }
    });
});


//.image-overlay-link, .image-overlay-zoom
} )( jQuery )
</script>
<?php
}