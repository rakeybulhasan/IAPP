<div class="four columns">
	<div class="blog-sidebar">
		<?php

		$sidebar = get_post_meta($post->ID, "incr_sidebar_set", $single = true);
		if ($sidebar) {
			if (!function_exists('dynamic_sidebar') || !dynamic_sidebar($sidebar)) :
				?>
			<?php
			endif;
		}?>

		<?php
		if (!$sidebar) {
			if (!dynamic_sidebar('sidebar')) :
				?>
			<?php endif;
		    } // end primary widget area   
		    ?>

		</div>
    <div class="four columns" style="margin:25px 0">


        <ul class="project-info">
            <?php while (have_posts()) : the_post(); ?>
                <!-- Post -->
                <?php
                $post_id= get_the_ID();

                $location=get_post_meta($post_id, "location", $single = true);

                ?>
                <li><strong>Location:</strong> <?php  echo $location;?></li>
                <li><strong>Date:</strong> <?php  the_date()?></li>

            <?php endwhile; // End the loop. Whew.  ?>

        </ul>


    </div>

	</div>

