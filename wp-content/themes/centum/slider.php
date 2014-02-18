<!-- 960 Container -->

<div class="container">

	<!-- Flexslider -->
	<div class="sixteen columns">
       	<section class="slider">
            <?php
            global $wpdb;
            global $post;
            $options = get_option('ecb_theme_options');
            $pageId = $options['banner_gallery'];
            $gallery = getBannerByGroup($pageId)?>
            <div class="flexslider home">
                <ul class="slides">
                    <?php foreach ($gallery['images'] as $post) {
                        setup_postdata($post);
                        ?>
                        <li>
                            <?php echo wp_get_attachment_image($post->ID, 'banner')?>
                            <div class="slide-caption">
                                <h3><?php echo $post->post_title?></h3>
                                <p><?php echo $post->post_content?></p>
                            </div>

                        </li>
                    <?php } wp_reset_postdata();?>
                </ul>
            </div>
		</section>
	</div>
	<!-- Flexslider / End -->

</div>
<!-- 960 Container / End -->
