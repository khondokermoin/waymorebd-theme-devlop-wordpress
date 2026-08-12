<?php
/**
 * Product card — matches the reference screenshots.
 *
 *  ┌───────────────────────────────┐
 *  │ [Best Selling]     [Save 3%]  │  orange top-left · GREEN top-right
 *  │                               │
 *  │        product image          │  white bg, object-contain
 *  │                               │
 *  │ Title (2 lines max)           │
 *  │ ৳1,650  ৳1,700                │  orange price + struck grey original
 *  │ ┌──────── Add To Cart ──────┐ │  ORANGE OUTLINE button
 *  │ └───────────────────────────┘ │
 *  │  …becomes  [ −  1  + ]  once  │  solid orange ends, white centre
 *  └───────────────────────────────┘
 *
 * The stepper is server-rendered when the item is already in the cart and
 * swapped in by JS (isdb-qty) right after WooCommerce's AJAX add-to-cart.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/content-product.php
 *
 * @package isdb-custom
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

$pid       = $product->get_id();
$permalink = $product->get_permalink();

// Save % — supports simple and variable.
$discount = 0;
$reg      = (float) $product->get_regular_price();
$cur      = (float) $product->get_price();
if ( $product instanceof WC_Product_Variable ) {
	$reg = (float) $product->get_variation_regular_price( 'min', true );
	$cur = (float) $product->get_variation_price( 'min', true );
}
if ( $product->is_on_sale() && $reg > 0 && $cur > 0 && $reg > $cur ) {
	$discount = round( ( ( $reg - $cur ) / $reg ) * 100 );
}

$is_bestseller = ( $product->get_total_sales() > 15 );

// Current cart state for this product (drives stepper vs. add button).
$cart_map  = function_exists( 'isdb_cart_qty_map' ) ? isdb_cart_qty_map() : array();
$in_cart   = isset( $cart_map[ $pid ] );
$cart_key  = $in_cart ? $cart_map[ $pid ]['key'] : '';
$cart_qty  = $in_cart ? $cart_map[ $pid ]['qty'] : 0;

// A plain stepper only makes sense for simple products bought from the loop.
$simple_add = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
?>
<li <?php wc_product_class( 'product style-6 group relative flex flex-col justify-between rounded-card border border-brand-line bg-white p-2 transition duration-200 hover:shadow-[3px_3px_8px_rgba(0,0,0,0.16)]', $product ); ?>>

	<!-- Badges -->
	<?php if ( $is_bestseller ) : ?>
		<span class="absolute left-2 top-2 z-20 rounded bg-brand-primary px-2 py-1 text-[10px] font-semibold leading-none text-white"><?php esc_html_e( 'Best Selling', 'isdb-custom' ); ?></span>
	<?php endif; ?>
	<?php if ( $discount > 0 ) : ?>
		<span class="absolute right-2 top-2 z-20 rounded bg-[#22c55e] px-2 py-1 text-[10px] font-semibold leading-none text-white"><?php printf( esc_html__( 'Save %s%%', 'isdb-custom' ), esc_html( $discount ) ); ?></span>
	<?php endif; ?>
	<?php if ( ! $product->is_in_stock() ) : ?>
		<span class="absolute left-2 top-9 z-20 rounded bg-brand-dark px-2 py-1 text-[10px] font-semibold leading-none text-white"><?php esc_html_e( 'Stock Out', 'isdb-custom' ); ?></span>
	<?php endif; ?>

	<div>
		<!-- Image -->
		<div class="product-media w-full overflow-hidden bg-white">
			<a href="<?php echo esc_url( $permalink ); ?>" class="flex h-[190px] items-center justify-center sm:h-[220px]" tabindex="-1" aria-hidden="true">
				<?php echo $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'block max-h-full w-auto max-w-full object-contain transition duration-300 group-hover:scale-[1.04]' ) ); ?>
			</a>
		</div>

		<!-- Title -->
		<h3 class="mt-2 line-clamp-2 min-h-[2.6rem] px-1 text-[13px] font-medium leading-snug text-brand-title sm:text-[15px]">
			<a href="<?php echo esc_url( $permalink ); ?>" class="transition after:absolute after:inset-0 after:z-10 hover:text-brand-primary"><?php echo esc_html( $product->get_name() ); ?></a>
		</h3>

		<!-- Price -->
		<div class="mt-1.5 flex flex-wrap items-baseline gap-x-2 px-1
			text-[15px] font-bold text-brand-primary
			[&_del]:text-[13px] [&_del]:font-normal [&_del]:text-slate-400
			[&_ins]:text-brand-primary [&_ins]:no-underline">
			<?php echo $product->get_price_html(); ?>
		</div>
	</div>

	<!-- Cart control -->
	<?php // Bestsellers get a "Buy Now" express-checkout button beside Add to Cart. ?>
	<?php $show_buy_now = $simple_add && $is_bestseller; ?>
	<div class="isdb-cart-ctl relative z-20 mt-2.5 <?php echo $show_buy_now ? 'flex items-stretch gap-2' : ''; ?>" data-product="<?php echo esc_attr( $pid ); ?>">

		<!-- Add to cart (orange outline) -->
		<div class="isdb-add-wrap <?php echo $show_buy_now ? 'flex-1 min-w-0' : ''; ?> <?php echo $in_cart && $simple_add ? 'hidden' : ''; ?>
			[&_.button]:flex [&_.button]:w-full [&_.button]:items-center [&_.button]:justify-center [&_.button]:gap-2
			[&_.button]:rounded-card [&_.button]:border [&_.button]:border-brand-primary [&_.button]:bg-white
			[&_.button]:px-3 [&_.button]:py-2.5 [&_.button]:text-[13px] [&_.button]:font-semibold
			[&_.button]:text-brand-primary [&_.button]:no-underline [&_.button]:transition
			[&_.button:hover]:bg-brand-primary [&_.button:hover]:text-white
			[&_.added_to_cart]:hidden
			[&_.loading]:opacity-60">
			<?php woocommerce_template_loop_add_to_cart(); ?>
		</div>

		<!-- Quantity stepper (shown once the item is in the cart) -->
		<?php if ( $simple_add ) : ?>
			<div class="isdb-step-wrap <?php echo $show_buy_now ? 'flex-1 min-w-0' : ''; ?> <?php echo $in_cart ? '' : 'hidden'; ?> flex items-stretch overflow-hidden rounded-card border border-brand-primary">
				<button type="button"
					class="isdb-qty-btn isdb-step-minus flex w-11 flex-none items-center justify-center bg-brand-primary text-white transition hover:bg-brand-hover"
					data-key="<?php echo esc_attr( $cart_key ); ?>"
					data-qty="<?php echo esc_attr( max( 0, $cart_qty - 1 ) ); ?>"
					aria-label="<?php esc_attr_e( 'Decrease quantity', 'isdb-custom' ); ?>">
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/></svg>
				</button>

				<span class="isdb-step-val flex flex-1 items-center justify-center bg-white py-2.5 text-sm font-semibold text-brand-title"><?php echo esc_html( $cart_qty ); ?></span>

				<button type="button"
					class="isdb-qty-btn isdb-step-plus flex w-11 flex-none items-center justify-center bg-brand-primary text-white transition hover:bg-brand-hover"
					data-key="<?php echo esc_attr( $cart_key ); ?>"
					data-qty="<?php echo esc_attr( $cart_qty + 1 ); ?>"
					aria-label="<?php esc_attr_e( 'Increase quantity', 'isdb-custom' ); ?>">
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
				</button>
			</div>
		<?php endif; ?>

		<!-- Buy Now (express checkout) — bestsellers only. JS adds via AJAX then
		     sends the shopper straight to Checkout; the buy_now flag lets the
		     server redirect to Checkout too when JS is unavailable. -->
		<?php if ( $show_buy_now ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'add-to-cart' => $pid, 'buy_now' => 1 ), wc_get_checkout_url() ) ); ?>"
				class="wmb-buy-now flex flex-1 min-w-0 items-center justify-center gap-1 rounded-card bg-brand-primary px-3 py-2.5 text-center text-[13px] font-semibold text-white no-underline transition hover:bg-brand-hover"
				data-product_id="<?php echo esc_attr( $pid ); ?>"
				rel="nofollow">
				<?php esc_html_e( 'Buy Now', 'isdb-custom' ); ?>
			</a>
		<?php endif; ?>
	</div>
</li>
