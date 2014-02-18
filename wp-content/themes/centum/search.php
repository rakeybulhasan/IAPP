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
            <h1><?php
                $searchQuery = get_search_query();
                $template = __('Search Results %s','purepress');
                $for = __('for','purepress');

                printf( __( $template, 'purepress' ),

                    $searchQuery == ""? "" :$for. ' "' .$searchQuery.'"' );

                ?></h1>
            <div id="bolded-line"></div>
        </div>
        <!-- Page Title / End -->


        <?php
        while ( have_posts() ) : the_post();
            ?>
        <div class="post" style="border-bottom: none;margin-bottom: 20px">
            <div class="">
            <div style="float: left; width: auto; margin-right: 10px"> <a class="" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('small-thumb');?></a></div>
            <div class="post-title"><h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2></div>
            <div class="post-description">
                <?php the_excerpt(); ?>
            </div>
        </div>
    </div>
        <?php endwhile; ?>
    </div> <!-- eof eleven column -->


<?php
if(ot_get_option('blog_layout') != 'left-sidebar')
    get_sidebar();


get_footer();

?>