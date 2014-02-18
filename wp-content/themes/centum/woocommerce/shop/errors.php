<?php
/**
 * Show error messages
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 * @modified    purethemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! $errors ) return;
?>
<div class="notification closeable error">
    <ul class="check-list">
       <?php foreach ( $errors as $error ) : ?>
       <li><?php echo wp_kses_post( $error ); ?></li>
   <?php endforeach; ?>
    </ul>
</div>