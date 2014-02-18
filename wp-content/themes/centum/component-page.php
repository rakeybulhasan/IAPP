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
        <?php
        $pages = get_pages('child_of='.$post->ID.'&sort_column=post_date&sort_order=desc');
        $count = 0;
?>
        <?php
        if(!empty($pages)){
        ?>
        <ul class="tabs-nav">
       <?php

            foreach($pages as $page)
        {
            ?>

                <li class=""><a href="#tab<?php echo $page->ID?>"><?php echo $page->post_title?></a></li>

        <?php
        }
        ?>
        </ul>

        <div class="tabs-container">
            <?php foreach($pages as $page)
            {
            $content = $page->post_content;

            $content1 = apply_filters('the_content', $content);
            ?>
            <div id="tab<?php echo $page->ID?>" class="tab-content">
                <?php echo  $content1; ?>
            </div>
            <?php
            }
            ?>

        </div>

        <?php } ?>
        <?php while (have_posts()) : the_post(); ?>
            <!-- Post -->

                <?php the_content() ?>

        <?php endwhile; // End the loop. Whew.  ?>

	</div> <!-- eof eleven column -->

