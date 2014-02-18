<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package WordPress
 * @subpackage purepress
 * @since purepress 1.0
 */
?>

<!--  Page Title -->

	<!-- 960 Container Start -->
	<div class="container">
		<div class="sixteen columns">

		</div>
	</div>
	<!-- 960 Container End -->

<!-- Page Title End -->

<!-- 960 Container -->
<div class="container">
	<?php

	$sidebar_side = get_post_meta($post->ID, 'incr_sidebar_layout', true);

	?>

<!-- Blog Posts
	================================================== -->
	<div class="twelve columns <?php if($sidebar_side == "left-sidebar") { echo "left-sb"; } ?>">
        <div id="page-title">
            <?php $bredcrumbs = ot_get_option('centum_breadcrumbs'); ?>
            <h1 <?php if($bredcrumbs == 'yes') echo 'class="has-breadcrumbs"';?>>
                <?php the_title(); ?>
                <?php $subtitle  = get_post_meta($post->ID, 'incr_subtitle', true);
                if ( $subtitle) {
                    echo ' <span>/ '.$subtitle.'</span>';
                } ?>
            </h1>

            <?php if(ot_get_option('centum_breadcrumbs') == 'yes') echo dimox_breadcrumbs() ;?>
            <div id="bolded-line"></div>
        </div>

            <?php

            $galleryTerms = get_terms("gallery_category");
            foreach ($galleryTerms as $term) {

                ?>

                <div id="portfolio-wrapperd">

                    <div class="twelve columns">
                        <div class="headline low-margin" style="margin-right: 10px">
                            <h4><?php echo $term->name?></h4>
                        </div>
                    </div>
                    <br>
                    <?php
                    $args = array(
                        'post_type' => 'photo_gallery',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'gallery_category',
                                'field' => 'slug',
                                'terms' => $term->slug,
                            )
                        )
                    );
                    $posts = get_posts($args);
                    $i=1;
                    foreach ($posts as $post) {
                        setup_postdata($post);

                        $galleryImages = get_group('gallery');

                        $image = get_image('gallery_photo',1,1,1,NULL,NULL,NULL,'thumb');
                        $galleryImage = $galleryImages[1];

                        ?>
                        <div class="three columns interior-design architecture real-estate" style="155px; margin-left: 5px; margin-bottom: 5px">

                            <div class="picture">
                                <a href="<?php the_permalink() ?>" rel="" title="<?php the_title() ?>">
                                    <?php echo $image;?>
                                </a>
                            </div>

                            <div class="item-description alt">
                                <h5> <a href="<?php the_permalink() ?>" rel="" title="<?php the_title() ?>"><?php the_title() ?></a></h5>
                                <p></p>
                            </div>

                        </div>
                        <?php if($i%4==0){
                            echo '<div style="clear: both"></div>';
                        }?>

                    <?php
                        $i++;
                    }
                    wp_reset_postdata();
                    ?>

                </div>
            <?php } ?>
            <!-- Post -->

        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Photo Gallery sidebar')) : endif; ?>
	</div> <!-- eof eleven column -->

