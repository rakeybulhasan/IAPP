<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 * @modified    purethemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="notification closeable error">
<p><?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?></p>
</div>
<?php do_action('woocommerce_cart_is_empty'); ?>

<p><a class="button color" href="<?php echo get_permalink(woocommerce_get_page_id('shop')); ?>"><?php _e( '&larr; Return To Shop', 'woocommerce' ) ?></a></p>