<?php
/**
 * The main template file.
 * @package WordPress
 */
get_header();

?>
<!-- Content
	================================================== -->

	<!-- 960 Container -->
	<div class="container">

		<div class="sixteen columns">

			<!-- Page Title -->
			<div id="page-title">
				<h1><?php the_title(); ?>
					<?php $subtitle  = get_post_meta($post->ID, 'incr_subtitle', true);
					if ( $subtitle) {
						echo ' <span>/ '.$subtitle.'</span>';
					} ?></h1>

					<!-- Portfolio Navi -->
					<div id="portfolio-navi">
						<?php next_post_link('%link'); ?><?php if (!get_adjacent_post(false, '', false)) { echo '<a href="#" class="next off">End</a>' ;} ?>
						<?php previous_post_link('%link'); ?>  <?php if (!get_adjacent_post(false, '', true)) { echo '<a href="#" class="prev off">End</a>' ;} ?>
					</div>
					<div class="clear"></div>

					<div id="bolded-line"></div>
				</div>
				<!-- Page Title / End -->

			</div>
		</div>
		<!-- 960 Container / End -->

		<?php while (have_posts()) : the_post(); ?>

		<?php

		$type  = get_post_meta($post->ID, 'incr_pf_type', true);

		if($type == 'video') {
			$videoembed = get_post_meta($post->ID, 'incr_pfvideo_embed', true);
			if($videoembed) {
				echo '<div class="container"><div class="sixteen columns video-cont">'.$videoembed.'</div></div>';
			} else {
				global $wp_embed;
				$videolink = get_post_meta($post->ID, 'incr_pfvideo_link', true);
				$post_embed = $wp_embed->run_shortcode('[embed width="940" height="530"]'.$videolink.'[/embed]') ;
				echo '<div class="container"><div class="sixteen columns video-cont">'.$post_embed.'</div></div>';
			}
		} else {
			// Check what to display above post title (image,vide, slideshow)
            $ids = get_post_meta($post->ID, 'pp_gallery_slider', TRUE);
            if(empty($ids)) {
			$excluded = get_post_meta($post->ID, 'centum_exluded_photos', true);
			$args = array(
            'numberposts' => -1, // Using -1 loads all posts
            'orderby' => 'menu_order', // This ensures images are in the order set in the page media manager
            'order'=> 'ASC',
            'post_mime_type' => 'image', // Make sure it doesn't pull other resources, like videos
            'post_parent' => $post->ID, // Important part - ensures the associated images are loaded
            'post_status' => null,
            'post_type' => 'attachment',
            'post__not_in' => $excluded
            );

			$images = get_children( $args );
                    // continued below ...

                    	// continued from above ...
			if($images){ ?>
			<!-- 960 Container -->
			<div class="container">

				<!-- Slider -->
				<div class="sixteen columns">
					<div class="flexslider home loadingflex">
						<ul class="slides">
							<?php foreach($images as $image){
								$attachment = wp_get_attachment_image_src($image->ID, 'full');
								$thumb = wp_get_attachment_image_src($image->ID, 'portfolio-main');
								?>
								<li>
									<a href="<?php echo $attachment[0] ?>" rel="image-gallery" title="<?php echo $image->post_title; ?>" >
										<img src="<?php echo $thumb[0] ?>" alt="<?php echo $image->post_title; ?>" />
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>

				</div>
				<!-- End 960 Container -->
				<?php }

			 } else { //empty ids
                    $ids = get_post_meta($post->ID, 'pp_gallery_slider', TRUE);
                    $args = array(
                    'post_type' => 'attachment',
                    'post_status' => 'inherit',
                    'post_mime_type' => 'image',
                    'post__in' => explode( ",", $ids),
                    'posts_per_page' => '-1',
                    'orderby' => 'post__in'
                    );
                    $images_array = get_posts( $args );
                       if ( $images_array ) {  ?>
                        <div class="container">
                        <!-- Slider -->
                        <div class="sixteen columns">
                            <div class="flexslider home loadingflex">
                                <ul class="slides">
                         <?php foreach( $images_array as $images ) : setup_postdata($images); ?>
                         <!-- 960 Container -->
                            <?php
                                $attachment = wp_get_attachment_image_src($images->ID, 'full');
                                $thumb = wp_get_attachment_image_src($images->ID, 'portfolio-main');
                            ?>
                            <li>
                                <a href="<?php echo $attachment[0] ?>" rel="image-gallery" title="<?php echo $images->post_title; ?>" >
                                    <img src="<?php echo $thumb[0] ?>" alt="<?php echo $images->post_title; ?>" />
                                </a>
                            </li>
                        <?php endforeach;  ?>
                        </ul>
                        </div>
                    </div>

                </div>
                <!-- End 960 Container -->
                    <?php
                    } //eof if type
                    wp_reset_query();
             }
            } //eof if type ?>

			<!-- 960 Container -->
			<div class="container" style="margin-top: 30px;">
				<?php
				$width  = get_post_meta($post->ID, 'incr_pf_full', true);
				$client = get_post_meta($post->ID, 'incr_pf_client', true);
				$date = get_post_meta($post->ID, 'incr_pf_date', true);
				$link = get_post_meta($post->ID, 'incr_pf_link', true);
				?>
				<?php if ($width == 'none' || empty($width)) { ?>
				<div class="four columns">
        		<?php
                    $metafields = ot_get_option( 'centum_pf_meta', array() );
                    if (!empty( $metafields ) ) {
                    echo '<ul class="project-info">'; $i = 0;
                      foreach( $metafields as $metafield ) {
                        if($metafield['type'] == "text") {
                            $i++;
                           $field_id = "incr_pf_".sanitize_title($metafield['title']);
                           $title= $metafield['title'];
                           if (function_exists('icl_register_string')) {
                            icl_register_string('Portfolio meta title'.$i,'metapf'.$i, $title);
                            }
                           $value = get_post_meta($post->ID, $field_id, true);
                           if($value){
                            if (function_exists('icl_register_string')) {
                                echo "<li><strong>" . icl_t('Flex slider title'.$i,'metapf'.$i, $title) .":</strong>";
                            } else {
                               echo "<li><strong>".$title.":</strong>";
                            }
                               echo " ".$value."</li>";
                           }
                       }
                       if($metafield['type'] == "filters") {
                        $terms = get_the_terms( $post->ID , 'filters' );
                            if($terms) {
                              $title= $metafield['title'];
                               echo "<li><strong>".$title.":</strong> ";

                               foreach ( $terms as $term ) {
                                    echo '<a href="'.get_term_link($term->slug, 'filters').'">'.$term->name."</a> ";
                                }
                               echo "</li>";
                           }
                       }
                       if($metafield['type'] == "dateofp") {
                              $title= $metafield['title'];
                                echo "<li><strong>Date:</strong> ";
                                echo  get_the_date();
                                echo "</li>";
                        }

                        if($metafield['type'] == "link") {
                              $text= $metafield['title'];
                              $link = get_post_meta($post->ID, 'incr_pf_link', true);
                                echo '<li style="margin-top:20px"><a href="'.$link.'" class="button color launch">'.$text.'</a></li>';
                        }

                		}
                		echo '</ul>';
				}
                ?>



				</div>
				<?php } ?>
				<div class="<?php if ($width == 'full') {echo "sixteen"; } else { echo "twelve"; }?> columns tooltips">
					<?php the_content() ?>
				</div>

			</div>
			<!-- End 960 Container -->
		<?php endwhile; // End the loop. Whew.  ?>

		<!-- 960 Container -->
		<div class="container">

			<div class="sixteen columns">
				<!-- Headline -->
				<div class="headline" style="margin-top: 5px;"><h3><?php _e('Recent Projects', 'purepress'); ?></h3></div>
			</div>

			<?php recent_porfolios(); ?>

		</div>
		<!-- 960 Container / End -->


	</div>
	<!-- Wrapper / End -->

	<?php
	get_footer();
	?>