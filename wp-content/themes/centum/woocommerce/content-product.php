<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 * @modified    purethemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 3 );
$columns_class = "";
switch ($woocommerce_loop['columns']) {
	case 2:
		$columns_class = "six";
		break;
	case 3:
		$columns_class = "four";
		break;
	case 4:
		$columns_class = "three";
		break;

	default:
		# code...
		break;
}
// Ensure visibility
if ( ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array( 'columns', $columns_class,'isotope-item');
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'alpha';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'omega';
?>
<li <?php post_class( $classes ); ?> >
	<div class="shop-item">
		<figure >
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
		<figcaption class="item-description">
				<a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a>
		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>
			<div class="cart-button-wrapper"><?php do_action( 'woocommerce_after_shop_loop_item' ); ?></div>
			</figcaption>
		</figure>
	</div>
</li>
