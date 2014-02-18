<?php
/**
 * The main template file.
 * @package WordPress
 */


get_header(); ?>


<!-- 960 Container -->
<div class="container">

	<div class="sixteen columns">

	</div>
</div>
<!-- 960 Container / End -->

<!-- 960 Container -->
<div class="container">
<?php
if(ot_get_option('blog_layout') == 'left-sidebar'){
	get_sidebar();
}
?>
<!-- Blog Posts
	================================================== -->
	<div class="twelve columns">

        <!-- Page Title -->
        <div id="page-title">
            <h1> <?php printf(__('Category: <span>%s</span>', 'purepress'), '' . single_cat_title('', false) . ''); ?></h1>
            <div id="bolded-line"></div>
        </div>
        <!-- Page Title / End -->

		<!-- Post -->

		<?php while (have_posts()) : the_post(); ?>

            <div class="five columns portfolio-item interior-design architecture real-estate" style="width: 329px;">

                <div class="picture">
                    <a href="<?php the_permalink() ?>" rel="" title="<?php the_title() ?>">
                        <?php the_content(); ?>
                </div>

                <div class="item-description alt">
                    <h5><?php the_title() ?></h5> </a>

                </div>

            </div>
			<!-- Post -->
		<?php endwhile; // End the loop. Whew.  ?>



		<div class="pagination">
<?php if(function_exists('wp_pagenavi')) :
         wp_pagenavi();
    else:
         if ($wp_query->max_num_pages > 1) : ?>
            <nav id="nav-below" class="navigation">
                <div class="nav-previous"><?php next_posts_link(__('&larr; Older posts', 'purepress')); ?></div>
                <div class="nav-next"><?php previous_posts_link(__('Newer posts &rarr;', 'purepress')); ?></div>
            </nav><!-- #nav-below -->
         <?php endif;
    endif; ?>

</div>

</div> <!-- eof eleven column -->


<?php
if(ot_get_option('blog_layout') != 'left-sidebar')
	get_sidebar();


get_footer();

?>