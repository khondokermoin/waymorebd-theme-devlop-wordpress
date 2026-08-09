<?php
/**
 * Cross-sells (cart page) — Way More BD.
 *
 * Uses the products WooCommerce passes in ($cross_sells, set per product under
 * Product → Linked Products → Cross-sells). If none are configured, it FALLS
 * BACK to best-sellers not already in the cart — so "You may also like" is
 * never empty (upsell always live).
 *
 * Overrides: wp-content/plugins/woocommerce/templates/cart/cross-sells.php
 *
 * @package isdb-custom
 * @version 9.6.0
 * @var WC_Product[] $cross_sells
 */

defined( 'ABSPATH' ) || exit;

$cross_sells = is_array( $cross_sells ) ? $cross_sells : array();

// Fallback: suggest best-sellers not already in the cart.
if ( empty( $cross_sells ) && function_exists( 'isdb_best_sellers' ) ) {
	$in_cart = array();
	foreach ( WC()->cart->get_cart() as $ci ) {
		$in_cart[] = $ci['product_id'];
	}
	foreach ( isdb_best_sellers( 12 ) as $bp ) {
		if ( $bp instanceof WC_Product && $bp->is_visible() && $bp->is_in_stock() && ! in_array( $bp->get_id(), $in_cart, true ) ) {
			$cross_sells[] = $bp;
		}
		if ( count( $cross_sells ) >= 4 ) {
			break;
		}
	}
}

if ( empty( $cross_sells ) ) {
	return;
}

global $product, $post;
$orig_product = $product;
$orig_post    = $post;
?>
<div class="cross-sells">
	<h2>You may also like</h2>
	<ul class="products columns-4">
		<?php foreach ( $cross_sells as $cross_sell ) :
			$post    = get_post( $cross_sell->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$product = $cross_sell;                        // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			setup_postdata( $post );
			wc_get_template_part( 'content', 'product' );
		endforeach; ?>
	</ul>
</div>
<?php
$product = $orig_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
$post    = $orig_post;    // phpcs:ignore WordPress.WP.GlobalVariablesOverride
wp_reset_postdata();
