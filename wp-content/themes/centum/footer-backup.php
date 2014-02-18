</div>
</div>
<!-- Wrapper / End -->


<!-- Footer Start -->
<div id="footer">

	<!-- 960 Container -->
	<div class="container">

		<div class="four columns">
			 <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 1st Column')) : endif; ?>
		</div>

		<div class="four columns">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 2nd Column')) : endif; ?>
		</div>


		<div class="four columns">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 3rd Column')) : endif; ?>
		</div>

		<div class="four columns">
			<?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 4th Column')) : endif; ?>
		</div>


        <div class="sixteen columns" style="text-align: center">
            <?php  $important_link = get_terms("wing_logo");
            foreach ($important_link as $term) { ?>
                <?php
                $args = array(
                    'post_type' => 'important_link',
                    'posts_per_page' => 8,
                    'post_status'      => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'wing_logo',
                            'field' => 'slug',
                            'terms' => $term->slug,
                        )
                    )
                );
                $posts = get_posts($args);
                $i=1;
                foreach ($posts as $post) {
                    setup_postdata($post);


                    $image = get_image('wings_logo',1,1,1,NULL,NULL,NULL);
                   $url= get_post_meta($post->ID, "improtant_link_url_improtant_link_url", true);
                    ?>
                <div class="wings-logo">
                    <?php if(!empty($url)){?>
                    <a href="<?php echo $url;?>" target="_blank">
                        <?php echo $image;?>
                    </a>
                        <?php } else{?>
                        <?php echo $image;?>
                        <?php }?>
                </div>

                    <?php
                    $i++;
                }
                wp_reset_postdata();
                ?>

            <?php } ?>

        </div>
        <div class="sixteen columns">
			<div id="footer-bottom">
				<?php $copyrights = ot_get_option('copyrights' );  echo $copyrights?>
			</div>
		</div>

	</div>
	<!-- 960 Container End -->

</div>
<!-- Footer End -->


<?php wp_footer();

?>

</body>
</html>