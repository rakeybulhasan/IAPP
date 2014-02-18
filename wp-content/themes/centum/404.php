<?php
/**
 * The main template file.
 * @package WordPress
 */
get_header(); ?>
<div id="page-title">
	<!-- 960 Container Start -->
	<div class="container">
		<div class="sixteen columns">
			<h2>
				404
				<span>/<?php _e( ' Not Found ', 'purepress' ); ?></span>

			</h2>
			<div id="bolded-line"></div>
		</div>
	</div>
	<!-- 960 Container End -->
</div>
<!-- Page Title End -->

<!-- 960 Container -->
<div class="container">
	<?php

	$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);
	if($sidebar_side == "left-sidebar") {
		get_sidebar();
	}
	?>
<!-- Blog Posts
	================================================== -->
	<div class="eleven columns">
		<!-- Post -->
		<div class="post page post404" >
			<p>
				<?php _e('Apologies, but no results were found for the requested archive.', 'purepress'); ?>
					<ul class="tabs-nav">
                                    <li class="active"><a href="#tab1"><?php _e('Posts by date', 'purepress'); ?></a></li>
                                    <li><a href="#tab2"><?php _e( 'By title', 'purepress' ); ?></a></li>
                                    <li><a href="#tab3"><?php _e( 'By subject', 'purepress' ); ?></a></li>
                                </ul>
                                <div class="tabs-container">
                                    <div id="tab1" class="tab-content">
                                        <?php bm_displayArchives(); ?>
                                    </div>
                                    <div id="tab2" class="tab-content">
                                        <ul class="circle_list">
                                            <?php wp_get_archives(array('type'=> 'alpha','limit' => '50', 'format' => 'html', 'show_post_count' => true )); ?>
                                        </ul>
                                    </div>
                                    <div id="tab3" class="tab-content">
                                        <ul class="circle_list">
                                            <?php wp_list_categories(array('pad_counts'=> trure, 'title_li' => '')); ?>
                                        </ul>
                                    </div>
                                </div>
			</p>
		</div>
		<!-- Post -->

	</div> <!-- eof eleven column -->
	<?php
	$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);

	if($sidebar_side != "left-sidebar") {
		get_sidebar();
	}

	get_footer();

	?>