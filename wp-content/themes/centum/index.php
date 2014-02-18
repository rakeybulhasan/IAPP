<?php
/**
 * The main template file.
 * @package WordPress
 */
get_header();


if(is_front_page()) {

	$slider_on  = ot_get_option( 'slider_on' );
	$slider_type =  ot_get_option( 'incr_slider_home' );

	if ($slider_type == "flex") {
		$slides = ot_get_option( 'mainslider', array() );
		if ( $slider_on == 'yes' && !empty( $slides )) {
			get_template_part('slider-theme');
		}else{

            get_template_part('slider');
	}

    }

	if ($slider_type == "revolution") {
		if ( $slider_on == 'yes') {
			echo '<section class="slider">'; putRevSlider(ot_get_option( 'incr_revo_slider' )); echo "</section>";
		}

	}
}

get_template_part('home-page');

//get_sidebar();

get_footer();

?>