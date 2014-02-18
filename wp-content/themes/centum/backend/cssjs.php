<?php


add_action('wp_footer', 'custom_stylesheet_content');

function custom_stylesheet_content() {
 $ltopmar = ot_get_option( 'logo_top_margin' );
 $lbotmar = ot_get_option( 'logo_bottom_margin' );
 $taglinemar = ot_get_option( 'tagline_margin' );
 $logofont = ot_get_option('incr_logo_typo',array());
 global $post;
 global $google_array;

 ?>
 <style type="text/css">

 <?php if(ot_get_option('incr_fonts_on') == 'yes')  {  ?>
  body { font-family: '<?php echo str_replace("+", " ", ot_get_option( 'incr_body_font')); ?>'; }
  h1, h2, h3, h4, h5, h6 { font-family: '<?php echo str_replace("+", " ", ot_get_option( 'incr_h_font')); ?>'; }
  <?php } ?>

  #logo {
    <?php if ( isset( $ltopmar[0] ) && $ltopmar[1] ) { echo 'margin-top:'.$ltopmar[0].$ltopmar[1].';'; } ?>
    <?php if ( isset( $lbotmar[0] ) && $lbotmar[1] ) { echo 'margin-bottom:'.$lbotmar[0].$lbotmar[1].';'; } ?>
  }

  #tagline {
    <?php if ( isset( $taglinemar[0] ) && $taglinemar[1] ) { echo 'margin-top:'.$taglinemar[0].$taglinemar[1].';'; } ?>
  }

  #header {
    min-height: <?php echo ot_get_option( 'centum_minhh','100' );?>px;
  }


  <?php
  if(ot_get_option('centum_blog_icons') =="disable") { ?>
    .post-icon { display: none; }
    .post-content { margin: 22px 0 0; }
    <?php }

    if(ot_get_option('color_on') =="yes") { ?>
      #footer {
        background: <?php echo ot_get_option( 'f_color' ); ?>;
        color:  <?php echo ot_get_option( 'f_text_color' ); ?>;
      }
      #footer-bottom {
        background: <?php echo ot_get_option( 'bgf_color' ); ?>;
        color:  <?php echo ot_get_option( 'bgf_text_color' ); ?>;
      }

      #footer h5 {
        border-bottom: 1px solid <?php echo ot_get_option( 'f_header_border_color' ); ?>;
        color: <?php echo ot_get_option( 'f_header_color' ); ?>;

      }
  <?php } ?>

    <?php  if(ot_get_option('incr_logofonts_on') =="yes") { ?>
      h2.logo,
      h1.logo {
        font-family: <?php echo str_replace("+", " ",  $logofont['font-family']); ?>;
      }
      h2.logo a,
      h1.logo a {
        color: <?php echo $logofont['font-color']; ?>;
        font-family: <?php  echo str_replace("+", " ",  $logofont['font-family']); ?>;
        font-style: <?php echo $logofont['font-style']; ?>;
        font-variant: <?php echo $logofont['font-variant']; ?>;
        font-weight: <?php echo $logofont['font-weight']; ?>;
        font-size: <?php echo $logofont['font-size']; ?>;
      }
    <?php }

    if (ot_get_option('incr_main_color') != '#2da0ce') { ?>
      .selected, .selected:hover, #backtotop a:hover,.feature-circle.blue,.prev:hover, .next:hover,.mr-rotato-prev:hover, .mr-rotato-next:hover,.flex-direction-nav a:hover,
      .post .flex-direction-nav a:hover, .project .flex-direction-nav a:hover { background-color: <?php echo ot_get_option('incr_main_color'); ?>; }
    <?php }

    if (ot_get_option('incr_menuborder_color') != '#555555') {?>

    <?php } if (ot_get_option('incr_headers_color') != '#444444') { ?>
      h1, h2, h3, h4, h5, h6 {
        color:  <?php echo ot_get_option('incr_headers_color'); ?>
      }
    <?php } if (ot_get_option('incr_linkhover_color') != '#888888') { ?>
     a:hover, a:focus { color: <?php echo ot_get_option('incr_linkhover_color'); ?>; }

    <?php } if (ot_get_option('incr_link_color') != '#3f8faf') {?>
      a, a:visited { color:  <?php echo ot_get_option('incr_link_color'); ?>; }

    <?php }

    $bodysize = ot_get_option('incr_body_size');
    if ($bodysize) {  ?>
        body { font-size: <?php echo $bodysize[0].$bodysize[1]; ?> }
    <?php }


     $custom_main_color = get_theme_mod('centum_main_color','#72b626'); ?>
      #navigation ul li a:hover,
      #navigation ul li:hover > a,
      #bolded-line,
      .button.gray:hover,
      .button.light:hover,
      .price_slider_wrapper .ui-slider-horizontal .ui-slider-range,
      .button.color,
      .onsale,
      input[type="submit"] {
        background: <?php echo $custom_main_color;?>;
      }
      .blog-sidebar .widget #twitter-blog li a,
      a, a:hover,
      .testimonials-author,
      .shop-item span.price,
      a.post-entry {
        color: <?php echo $custom_main_color;?>
      }

      #navigation > div > ul > li.current-menu-item > a,
      .pricing-table .color-3 h3, .color-3 .sign-up,
      .flex-direction-nav .flex-prev:hover,
      .flex-direction-nav .flex-next:hover,
      .tp-leftarrow:hover,
      .tp-rightarrow:hover,
      #scroll-top-top a,
      .quantity .plus:hover,#content .quantity .plus:hover,.quantity .minus:hover,#content .quantity .minus:hover,
      .post-icon {
        background-color:<?php echo $custom_main_color; ?>;
      }

      .mr-rotato-prev:hover,
      .mr-rotato-next:hover,
      li.current,
      .tagcloud a:hover {
        background-color: <?php echo $custom_main_color; ?>;
        border-color: <?php echo $custom_main_color; ?>;
      }

      #filters a:hover,
      .selected,
      .wp-pagenavi .current,
      .pagination .current,
      #portfolio-navi a:hover {
        background-color: <?php echo $custom_main_color; ?> !important;
        border: 1px solid <?php echo $custom_main_color; ?> !important;
      }
      .pricing-table .color-3 h4 {
        background-color:<?php echo $custom_main_color; ?>;
        opacity:0.8
      }

       <?php
        $custom_overlay_color = get_theme_mod('centum_overlay_color','#000000');
        $custom_overlay_opacity = get_theme_mod('centum_overlay_opacity','0.7');
       ?>
       .image-overlay-link, .image-overlay-zoom {
        background-color: rgba(<?php echo hex2RGB($custom_overlay_color, true) ?>,<?php echo $custom_overlay_opacity?>);
       }

      <?php

      echo ot_get_option( 'incr_custom_css' );  ?>
      </style>
      <?php

}   //eof custom_stylesheet_content




function custom_js() {
 ?>
 <script type="text/javascript">
 <?php echo ot_get_option( 'incr_analytics' );   ?>
 </script>
 <?php
}


//add_action('wp_footer', 'custom_js');
?>