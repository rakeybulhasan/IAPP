<?php
/**
 * SHORTCODES
 */
function pp_separator($atts) {
    extract(shortcode_atts(array(), $atts));
    return '<div style="margin:15px 0px;"></div>';
}
add_shortcode('separator', 'pp_separator');


function pureslideshow($atts,$content = null) {
    extract(shortcode_atts(array(), $atts));
    $images =  explode(";", $content);
    $output = '<div class="flexslider"><ul class="slides"> ';
    foreach($images as $img){
        if(!empty($img)) $output .= '<li><img src="'.$img.'"/></li>';
    }
    $output .= '</div> </ul> ';
    return $output;
}
add_shortcode('pureslideshow', 'pureslideshow');



// new shortcodes
function pp_column($atts, $content = null) {
    extract( shortcode_atts( array(
        'width' => '1/2',
        'place' => 'alpha',
        'custom_class' => ''
        ), $atts ) );

    switch ( $width ) {
        case "full" :
        $w = "columns sixteen";
        break;

        case "1/2" :
        $w = "columns eight";
        break;

        case "1/3" :
        $w = "column one-third";
        break;

        case "2/3" :
        $w = "column two-thirds";
        break;

        case "1/4" :
        $w = "four columns";
        break;

        case "3/4" :
        $w = "twelve columns";
        break;

        case "one" : $w = "one columns"; break;
        case "two" : $w = "two columns"; break;
        case "three" : $w = "three columns"; break;
        case "four" : $w = "four columns"; break;
        case "five" : $w = "five columns"; break;
        case "six" : $w = "six columns"; break;
        case "seven" : $w = "seven columns"; break;
        case "eight" : $w = "eight columns"; break;
        case "nine" : $w = "nine columns"; break;
        case "ten" : $w = "ten columns"; break;
        case "eleven" : $w = "eleven columns"; break;
        case "twelve" : $w = "twelve columns"; break;
        case "thirteen" : $w = "thirteen columns"; break;
        case "fourteen" : $w = "fourteen columns"; break;
        case "fifteen" : $w = "fifteen columns"; break;
        case "sixteen" : $w = "sixteen columns"; break;


        default :
        $w = 'columns sixteen';
    }
    switch ( $place ) {
        case "last" :
        $p = "omega";
        break;

        case "center" :
        $p = "alpha omega";
        break;

        case "none" :
        $p = " ";
        break;

        case "first" :
        $p = "alpha";
        break;
        default :
        $p = 'alpha';
    }

    $column = '<div class="'.$w.' '.$custom_class.' ';
    $column .= $p.'">'.do_shortcode( $content ).'</div>';
    if($place=='last') $column .= '<br class="clear" />';
    return $column;
}
add_shortcode('column', 'pp_column');


function pp_button($atts, $content = null) {
    extract(shortcode_atts(array(
        "url" => '',
        "color" => 'color',
        "customcolor" => '',
        "size" => 'medium',
        "iconcolor" => 'white',
        "icon" => ''

        ), $atts));

    $output = '<a class="button '.$size.' '.$color.'" href="'.$url.'" ';
    if(!empty($customcolor)) { $output .= 'style="background-color:'.$customcolor.'"'; }
    $output .= '>';
    if(!empty($icon)) { $output .= '<i class="'.$icon.' mini-'.$iconcolor.'"></i>'; }
    $output .= $content.'</a>';

    return $output;
}
add_shortcode('button', 'pp_button');


function etdc_tab_group( $atts, $content ) {
    $GLOBALS['tab_count'] = 0;
    do_shortcode( $content );
    $count = 0;
    if( is_array( $GLOBALS['tabs'] ) ) {
        foreach( $GLOBALS['tabs'] as $tab ) {
            $count++;
            if($tab['icon']) { $tabs[] = '<li><a href="#tab'.$count.'"><i class="'.$tab['icon'].'"></i> '.$tab['title'].'</a></li>'; }
            else { $tabs[] = '<li><a href="#tab'.$count.'">'.$tab['title'].'</a></li>'; }
            $panes[] = '<div class="tab-content" id="tab'.$count.'">'.$tab['content'].'</div>';
        }
        $return = "\n".'<ul class="tabs-nav">'.implode( "\n", $tabs ).'</ul>'."\n".'<div class="tabs-container">'.implode( "\n", $panes ).'</div>'."\n";
    }
    return $return;
}


function etdc_tab( $atts, $content ) {
    extract(shortcode_atts(array(
        'title' => 'Tab %d',
        'icon' => ''
        ), $atts));

    $x = $GLOBALS['tab_count'];
    $GLOBALS['tabs'][$x] = array( 'title' => sprintf( $title, $GLOBALS['tab_count'] ), 'icon' => $icon, 'content' =>  do_shortcode( $content ) );
    $GLOBALS['tab_count']++;
}
add_shortcode( 'tabgroup', 'etdc_tab_group' );
add_shortcode( 'tab', 'etdc_tab' );




function pp_accordion( $atts, $content ) {
    extract(shortcode_atts(array(
        'title' => 'Tab'
        ), $atts));
    return '<h5 class="acc-trigger"><a href="#">'.$title.'</a></h5><div class="acc-container"><div class="content">'.do_shortcode( $content ).'</div></div>';
}

add_shortcode( 'accordion', 'pp_accordion' );


function pp_box($atts, $content = null) {
    extract(shortcode_atts(array(
        "type" => ''
        ), $atts));
    return '<div class="notification closeable '.$type.'"><p>'.do_shortcode( $content ).'</p><a href="#" class="close"></a></div>';
}
add_shortcode('box', 'pp_box');


/*function breadcrumbs() {
    return dimox_breadcrumbs();
}
add_shortcode('breadcrumbs', 'breadcrumbs');
*/
function recent_pf($atts) {
 extract(shortcode_atts(array('limit'=>'4', 'columns' => '4', 'fullpage' => 'no', 'order' => 'DESC', 'orderby'=> 'date', 'filters' => '', 'lightbox' => 'no'), $atts));

 $output = '';
 $counter = 0;
     if($filters){
        $filterstemparray = explode(',', $filters);
        if (count($filterstemparray)>1) {
            $filtersarray = $filterstemparray;

        } else {
            $filtersarray = $filterstemparray[0];
        }
    };
    if($filters=="all" || empty($filters)) {
         $wp_query = new WP_Query(
            array(
                'post_type' => array('portfolio'),
                'showposts' => $limit,
                'orderby' => $orderby,
                'order' => $order
                ));
    } else {
       $wp_query = new WP_Query(
        array(
            'post_type' => array('portfolio'),
            'showposts' => $limit,
            'orderby' => $orderby,
            'order' => $order,
            'tax_query' => array(
                array(
                    'taxonomy' => 'filters',
                    'field' => 'slug',
                    'terms' => $filtersarray
                    )
                ),
            )
            );
    }
 if ( $wp_query->have_posts() ):
    while( $wp_query->have_posts() ) : $wp_query->the_post();
   global $post;

        $counter++;
            if ($counter == 1) {
                $class = 'alpha';
            } else if( $counter % $columns == 0) {
                $class = 'omega';
            } else if ( $counter % $columns == 1 ) {
                $class = 'alpha';
            } else if ($counter == $limit) {
                $class = 'omega';
            } else {
                $class = '';
            }

        $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($wp_query->post->ID), 'large');
        $excerpt = get_the_excerpt();
        $short_excerpt = string_limit_words($excerpt,11);

        if($fullpage == "yes") {
            $output .='<div class="four columns '.$class.'">';
        } else {
            $output .='<div class="four columns">';
        }

        $id = $wp_query->post->ID;

        $type = get_post_meta($id, 'incr_pf_type', true);
        $videothumbtype = ot_get_option('portfolio_videothumb');

        if($type == 'video' && $videothumbtype == 'video') {
            global $wp_embed;
            $videolink = get_post_meta($id, 'incr_pfvideo_link', true);
            $post_embed = $wp_embed->run_shortcode('[embed  width="220" height="147"]'.$videolink.'[/embed]') ;
            $output .= '<div class="picture recent_video">'.$post_embed.'</div>';
        } else {
            if ( has_post_thumbnail()) {
                if($lightbox == 'yes') {
                    $thumbbig = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
                    $output .='<div class="picture"><a rel="image" href="'. $thumbbig[0] .'">'.get_the_post_thumbnail($post->ID,'portfolio-thumb').'<div class="image-overlay-zoom"></div></a></div>';
                } else {
                    $output .='<div class="picture"><a  href="'. get_permalink().'">'.get_the_post_thumbnail($post->ID,'portfolio-thumb').'<div class="image-overlay-link"></div></a></div>';
                }

            }
        }
        $output .=' <div class="item-description"><h5><a href="'. get_permalink().'">'.get_the_title().'</a></h5>';

        $output .='<p>'.$short_excerpt.'</p>';

        $output .='</div></div>';
    endwhile;  // close the Loop
endif;
            //$output .= '<br class="clear" />';
return $output;
}
add_shortcode('recent_pf', 'recent_pf');


function purelist($atts, $content = null) {
    extract(shortcode_atts(array(
        "type" => ''
        ), $atts));
    return '<div class="'.$type.'">'.$content.'</div>';
}
add_shortcode('list', 'purelist');



    function pp_notice( $atts, $content ) {
        extract(shortcode_atts(array(
            'title' => 'Notice'
            ), $atts));
        return '<div class="large-notice"><h2>'.$title.'</h2>'.do_shortcode( $content ).'</div>';
    }

    add_shortcode( 'notice', 'pp_notice' );

    function pp_clients( $atts, $content ) {
        extract(shortcode_atts(array(), $atts));
        return '<div class="client-list">'.do_shortcode( $content ).'</div>';
    }

    add_shortcode( 'clients', 'pp_clients' );


    function pp_headline( $atts, $content ) {
        extract(shortcode_atts(array(
            'margin' => 'no-margin','htype' => 'h3'), $atts));
        return '<div class="headline '.$margin.'"><'.$htype.'>'.do_shortcode( $content ).'</'.$htype.'></div>';
    }

    add_shortcode( 'headline', 'pp_headline' );


    function pp_feature( $atts, $content ) {
        extract(shortcode_atts(array(
            'title' => '',
            'icon' => '',
            'color' => 'gray'
        ), $atts));


        $output = '<div class="icon-box">';
        $output .= '<i class="'.$icon. ' ' .$color.'" style="margin-left: -10px;"></i>';
        $output .= '<h3>'.$title.'</h3><p>'.do_shortcode( $content ).'</p></div>';
        return $output;

    }

    add_shortcode( 'feature', 'pp_feature' );



    function recent_blog($atts) {
     extract(shortcode_atts(array('limit' => '3', 'number' => '3','fullpage' => 'no'), $atts));

    $number = $limit;
    $wp_query = new WP_Query(
    array(
    'post_type' => array('post'),
    'showposts' => $number
    ));

    $output = '';
    $counter = 0;
    if ( $wp_query->have_posts() ):

    $output = '';
    while( $wp_query->have_posts() ) : $wp_query->the_post();
    $counter++;

    switch ($counter) {
        case '1':
        $class = 'alpha';
        break;
        case $number:
        $class = 'omega';
        break;
        default:
        $class = ' ';
        break;
    }
    global $post;

    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
    $excerpt = get_the_excerpt();
    $short_excerpt = string_limit_words($excerpt,13);
    if($fullpage == "yes") {
        $output .='<div class="four columns portfolio-item '.$class.'">';
    } else {
        $output .='<div class="four columns portfolio-item">';
    }

    if ((get_post_format(  $wp_query->post->ID ) == 'video' )) {
        global $wp_embed;
        $videolink = get_post_meta( $wp_query->post->ID, 'incr_video_link', true);
        $post_embed = $wp_embed->run_shortcode('[embed  width="220" height="147"]'.$videolink.'[/embed]') ;
        $output .='<div class="picture recent_video">'.$post_embed.'</div>';
    } else {
        if ( has_post_thumbnail()) {
            $output .='<div class="picture"><a href="'. get_permalink().'">'.get_the_post_thumbnail($post->ID,'portfolio-thumb').'<div class="image-overlay-link"></div></a></div>';
        }
    }
    $output .=' <div class="item-description"><h5><a href="'. get_permalink().'">'.get_the_title().'</a></h5><p>'.$short_excerpt.'</p></div></div>';
    endwhile;  // close the Loop
    endif;
    $output .= '<br class="clear" />';
    return $output;
    }
    add_shortcode('recent_blog', 'recent_blog');




    function mag_testimonials($atts,$content ) {
       extract(shortcode_atts(array( 'title'=>"Testimonials"), $atts));
       $output = '<div class="headline no-margin"><h3>'.$title.'</h3></div> <div class="testimonials-carousel" data-autorotate="3000"><ul class="carousel">'.do_shortcode( $content ).'</ul></div>';
       return $output;
   }
   add_shortcode('testimonials', 'mag_testimonials');

   function pp_testimonial($atts, $content ) {
       extract(shortcode_atts(array( 'author'=>"", 'job' =>''), $atts));

       $output = '<li class="testimonial"><div class="testimonials">'.$content.'</div><div class="testimonials-bg"></div><div class="testimonials-author">'.$author;
       if($job) $output .= ', <span>'.$job.'</span>';
       $output .= '</div></li>';
       return $output;
   }
   add_shortcode('testimonial', 'pp_testimonial');


   function pp_social_icon($atts) {
    extract(shortcode_atts(array(
        "service" => 'facebook',
        "url" => ''
        ), $atts));
    $imagename = strtolower($service);
    $imageurl = get_template_directory_uri();
    $title = ucfirst($service);
    if($service == 'rss') $title = "RSS";
    if($service == 'linkedin') $title = "LinkedIn";
    if($service == 'lastfm') $title = "LastFM";
    if($service == 'youtube') $title = "YouTube";
    if($service == 'feedburner') $title = "FeedBurner";
    if($service == 'wordpress') $title = "WordPress";
    if($service == 'sharethis') $title = "ShareIt";
    if($service == 'deviantart') $title = "DeviantArt";
    if($service == 'googleplus') $title = "Google+";

    $output = '<a href="'.$url.'" rel="tooltip" title="'.$title.'" class="'.$imagename.'">'.$service.'</a>';
    return $output;
}
add_shortcode('social_icon', 'pp_social_icon');


function pp_social_icons($atts,$content ) {
   extract(shortcode_atts(array( 'title'=>"Testimonials"), $atts));
   $output = '<div id="social" class="tooltips">'.do_shortcode( $content ).'</div>';
   return $output;
}
add_shortcode('social_icons', 'pp_social_icons');


function pp_team($atts, $content ) {
   extract(shortcode_atts(array(
    'photo'=>"",
    'link' => "",
    'name'=>"",
    'job' =>'',
    'twitter' =>'',
    'facebook' =>'',
    'digg' =>'',
    'vimeo' =>'',
    'linkedin' =>'',
    'youtube' =>'',
    'skype' =>'',
    ), $atts));
    $output = '';
   if($photo) {
        if($link) {
            $output .= '<a href="'.$link.'"><img src="'.$photo.'" alt=""/></a>';
        } else {
           $output .= '<img src="'.$photo.'" alt=""/>';
        }
    }
   $output .= '<div class="team-name"><h5>'.$name.'</h5><span>'.$job.'</span></div>';
   $output .= '<div class="team-about"><p>'.do_shortcode( $content ).'</p></div>';
   if($twitter || $facebook || $digg || $vimeo || $youtube || $skype || $linkedin ) $output .= '<ul class="social-icons about">';
   if($twitter) $output .= ' <li class="twitter"><a href="'.$twitter.'">Twitter</a></li>';
   if($facebook) $output .= ' <li class="facebook"><a href="'.$facebook.'">Facebook</a></li>';
   if($linkedin) $output .= ' <li class="linkedin"><a href="'.$linkedin.'">LinkedIn</a></li>';
   if($vimeo) $output .= ' <li class="vimeo"><a href="'.$vimeo.'">Vimeo</a></li>';
   if($youtube) $output .= ' <li class="youtube"><a href="'.$youtube.'">Youtube</a></li>';
   if($skype) $output .= ' <li class="skype"><a href="'.$skype.'">Skype</a></li>';
   if($twitter || $facebook || $digg || $vimeo || $youtube || $skype ) $output .= '</ul>';
   return $output;
}
add_shortcode('team', 'pp_team');




   function pp_pricing_table($atts, $content) {
    extract(shortcode_atts(array(
        "color" => '',
        "header" => '',
        "price" => '',
        "per" => ''
        ), $atts));


    $output = '<div class="pricing-table">';
    $output .= '<div class="color-'.$color.'">';

    $output .= '<h3>'.$header.'</h3>';
    $output .= '<h4><span class="price">'.$price.'</span>';
    if($per) $output .= '<span class="time">'.$per.'</span>';
    $output .= '</h4>';
    $output .= do_shortcode( $content );
    $output .= '</div>​</div>​';
    return $output;
}
add_shortcode('pricing_table', 'pp_pricing_table');

function pp_pricing_wrapper($atts, $content) {
    extract(shortcode_atts(array(
        "number" => '4',

        ), $atts));

    switch ($number) {
        case '2':
        $tables = 'two';
        break;
        case '3':
        $tables = 'three';
        break;
        case '4':
        $tables = 'four';
        break;
        case '5':
        $tables = 'five';
        break;

        default:
        $tables = 'four';
        break;
    }
    $output = '<div class="'.$tables.'-tables">';


    $output .= do_shortcode( $content );
    $output .= '</div>​';
    return $output;
}
add_shortcode('pricing_wrapper', 'pp_pricing_wrapper');


function pp_pricing_check($atts) {
    extract(shortcode_atts(array(
        "check" => 'yes',
        ), $atts));
    $output .= '<span class="pricing_check '.$check.'"></span>​';
    return $output;
}
add_shortcode('pricing_check', 'pp_pricing_check');



function fn_googleMaps($atts, $content = null) {
   extract(shortcode_atts(array(
      "width" => '100%',
      "height" => '250px',
      "address" => 'New York, United States'
      ), $atts));
   $output ='
   <div id="googlemaps" class="google-map google-map-full" style="height:'.$height.'; width:'.$width.'"></div>
   <script src="http://maps.google.com/maps/api/js?sensor=true"></script>
   <script src="'.get_template_directory_uri().'/js/jquery.gmap.min.js"></script>
   <script type="text/javascript">
   jQuery("#googlemaps").gMap({
    maptype: "ROADMAP",
    scrollwheel: false,
    zoom: 13,
    markers: [
    {
        address: \''.$address.'\',
        html: "",
        popup: false,
    }
    ],
});
</script>';
return $output;
}
add_shortcode("googlemap", "fn_googleMaps");


//toggle shortcode
 function pp_toggle( $atts, $content = null ) {
 extract( shortcode_atts(
 array(
 'title' => 'Click To Open'),
 $atts ) );
 return '<h5 class="toggle-trigger"><a href="#">'. $title .'</a></h5><div class="toggle-container"><div class="content">' . do_shortcode($content) . '</div></div>';
 }
 add_shortcode('toggle', 'pp_toggle');

?>