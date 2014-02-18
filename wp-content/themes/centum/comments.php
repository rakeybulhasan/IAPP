<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.  The actual display of comments is
 * handled by a callback to purepress_comment which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage purepress
 * @since purepress 1.0
 */
?>
<?php if (have_comments()) : ?>
       <!-- STARKERS NOTE: The following h3 id is left intact so that comments can be referenced on the page -->
       <div class="headline margin"> <h4 id="comments-title"><?php
        printf(_n('Comments <span>(%1$s)</span>', 'Comments <em>(%1$s)</em>', get_comments_number(), 'purepress'), number_format_i18n(get_comments_number()), '' . get_the_title() . '');
        ?></h4></div>

    <?php endif; ?>
<div class="comments-sec">
    <?php if (post_password_required()) : ?>
    <p><?php _e('This post is password protected. Enter the password to view any comments.', 'purepress'); ?></p></div>
    <?php
        /* Stop the rest of comments.php from being processed,
         * but don't kill the script entirely -- we still have
         * to fully load the template.
         */
        return;
        endif;
        ?>

        <?php if (have_comments()) : ?>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through?  ?>
        <?php previous_comments_link(__('&larr; Older Comments', 'purepress')); ?>
        <?php next_comments_link(__('Newer Comments &rarr;', 'purepress')); ?>
    <?php endif; // check for comment navigation ?>

        <ol class="commentlist">
        <?php
            /* Loop through and list the comments. Tell wp_list_comments()
             * to use purepress_comment() to format the comments.
             * If you want to overload this in a child theme then you can
             * define purepress_comment() and that will be used instead.
             * See purepress_comment() in purepress/functions.php for more.
             */
            wp_list_comments(array('callback' => 'purepress_comment'));
            ?>
        </ol>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : // Are there comments to navigate through?  ?>
        <?php previous_comments_link(__('&larr; Older Comments', 'purepress')); ?>
        <?php next_comments_link(__('Newer Comments &rarr;', 'purepress')); ?>
    <?php endif; // check for comment navigation ?>

    <?php
    else : // or, if we don't have comments:

        /* If there are no comments and comments are closed,
         * let's leave a little note, shall we?
         */
        if (!comments_open()) :
            ?>
        <p><?php _e('Comments are closed.', 'purepress'); ?></p>
    <?php endif; // end ! comments_open() ?>

<?php endif; // end have_comments() ?>
</div>


<!-- Add Comment -->
<div class="comments-sec">
    <?php comment_form(); ?>
</div>