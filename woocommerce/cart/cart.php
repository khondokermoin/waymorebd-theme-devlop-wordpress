<?php
/**
 * Cart page — Way More BD (cart/cart.php)
 *
 * Psychology-led, low-friction cart:
 *   • progress steps (goal-gradient — "you're almost there")
 *   • per-item savings badge + strikethrough (loss aversion / anchoring)
 *   • genuine low-stock note (scarcity — only when truly low & managed)
 *   • auto-updating quantity stepper (removes the "Update cart" hunt)
 *   • continue-shopping link (kills the trapped feeling)
 * All WooCommerce hooks, nonces and field names (cart[key][qty], update_cart,
 * coupon, woocommerce-cart-nonce) are preserved — only the markup is custom.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/cart/cart.php
 *
 * @package isdb-custom
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$cart_count = WC()->cart->get_cart_contents_count();
$shop_url   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<!-- Progress steps: Cart → Details → Payment (commitment + goal-gradient) -->
<nav class="mb-7 flex items-center justify-center gap-2 text-[11px] font-bold uppercase tracking-wide sm:gap-3 sm:text-xs" aria-label="<?php esc_attr_e( 'Checkout progress', 'isdb-custom' ); ?>">
	<span class="flex items-center gap-2 text-brand-primary">
		<span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-primary text-white">1</span> <?php esc_html_e( 'Cart', 'isdb-custom' ); ?>
	</span>
	<span class="h-px w-6 bg-slate-300 sm:w-10"></span>
	<span class="flex items-center gap-2 text-slate-400">
		<span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-500">2</span> <?php esc_html_e( 'Details', 'isdb-custom' ); ?>
	</span>
	<span class="h-px w-6 bg-slate-300 sm:w-10"></span>
	<span class="flex items-center gap-2 text-slate-400">
		<span class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-500">3</span> <?php esc_html_e( 'Payment', 'isdb-custom' ); ?>
	</span>
</nav>

<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
	<h1 class="text-2xl font-extrabold tracking-tight text-brand-title sm:text-3xl">
		<?php esc_html_e( 'Your Cart', 'isdb-custom' ); ?> <span class="text-base font-semibold text-slate-400">(<?php echo esc_html( $cart_count ); ?> <?php echo esc_html( _n( 'item', 'items', $cart_count, 'isdb-custom' ) ); ?>)</span>
	</h1>
	<a href="<?php echo esc_url( $shop_url ); ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-primary transition hover:underline">
		<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
		<?php esc_html_e( 'Continue shopping', 'isdb-custom' ); ?>
	</a>
</div>

<div class="lg:grid lg:grid-cols-[1fr,380px] lg:items-start lg:gap-8">

	<!-- ═══════════ ITEMS ═══════════ -->
	<div>
		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<div class="divide-y divide-slate-100 overflow-hidden rounded-2xl bg-white ring-1 ring-slate-100">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

						// Anchoring / loss-aversion: per-line savings.
						$reg       = (float) $_product->get_regular_price();
						$cur       = (float) $_product->get_price();
						$item_save = ( $reg > $cur && $cur > 0 ) ? ( $reg - $cur ) * (int) $cart_item['quantity'] : 0;

						// Scarcity: genuine low stock only (managed & <= 5).
						$sq        = $_product->get_stock_quantity();
						$low_stock = ( $_product->managing_stock() && is_numeric( $sq ) && $sq > 0 && $sq <= 5 );
						?>
						<div class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> flex gap-4 p-4">

							<!-- Thumbnail -->
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="block h-20 w-20 flex-none overflow-hidden rounded-xl bg-brand-bg ring-1 ring-slate-100 [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key ) ); ?>
							</a>

							<!-- Name / price / qty / remove -->
							<div class="min-w-0 flex-1">
								<div class="text-sm font-semibold text-brand-title">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a class="transition hover:text-brand-primary" href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

									echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification text-xs text-amber-600">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
									}
									?>
								</div>

								<div class="mt-1 text-sm text-slate-500">
									<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>

								<?php if ( $low_stock ) : ?>
									<p class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-rose-600">
										<span class="relative flex h-1.5 w-1.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span><span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-rose-500"></span></span>
										<?php printf( esc_html__( 'Only %s left — order soon', 'isdb-custom' ), esc_html( $sq ) ); ?>
									</p>
								<?php endif; ?>

								<div class="mt-3 flex items-center gap-4">
									<!-- Quantity stepper (auto-updates; keeps cart[key][qty]) -->
									<div class="wmb-qty inline-flex items-center overflow-hidden rounded-lg border border-[#ddd] [&_.quantity]:contents [&_input.qty]:h-9 [&_input.qty]:w-12 [&_input.qty]:border-0 [&_input.qty]:bg-transparent [&_input.qty]:p-0 [&_input.qty]:text-center [&_input.qty]:text-sm [&_input.qty]:font-bold [&_input.qty]:text-brand-title [&_input.qty]:focus:ring-0">
										<button type="button" class="wmb-qminus flex h-9 w-9 flex-none items-center justify-center text-brand-title transition hover:bg-brand-soft hover:text-brand-primary" aria-label="<?php esc_attr_e( 'Decrease quantity', 'isdb-custom' ); ?>">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/></svg>
										</button>
										<?php
										if ( $_product->is_sold_individually() ) {
											$product_quantity = sprintf( '<span class="quantity"><span class="qty px-3 text-sm font-bold text-brand-title">1</span><input type="hidden" name="cart[%s][qty]" value="1" /></span>', $cart_item_key );
										} else {
											$product_quantity = woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $_product->get_max_purchase_quantity(),
													'min_value'    => '0',
													'product_name' => $_product->get_name(),
												),
												$_product,
												false
											);
										}
										echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
										<button type="button" class="wmb-qplus flex h-9 w-9 flex-none items-center justify-center text-brand-title transition hover:bg-brand-soft hover:text-brand-primary" aria-label="<?php esc_attr_e( 'Increase quantity', 'isdb-custom' ); ?>">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
										</button>
									</div>

									<!-- Remove (functional AJAX) -->
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="remove inline-flex items-center gap-1 text-xs font-medium text-slate-400 transition hover:text-rose-600" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>%s</a>',
											esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
											esc_attr__( 'Remove this item', 'woocommerce' ),
											esc_attr( $product_id ),
											esc_attr( $cart_item_key ),
											esc_attr( $_product->get_sku() ),
											esc_html__( 'Remove', 'woocommerce' )
										),
										$cart_item_key
									);
									?>
								</div>
							</div>

							<!-- Line subtotal + savings badge -->
							<div class="flex flex-none flex-col items-end gap-1.5 text-right">
								<span class="text-sm font-bold text-brand-title">
									<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>
								<?php if ( $item_save > 0 ) : ?>
									<span class="rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700"><?php esc_html_e( 'Save', 'isdb-custom' ); ?> <?php echo wp_kses_post( wc_price( $item_save ) ); ?></span>
								<?php endif; ?>
							</div>
						</div>
						<?php
					}
				}

				do_action( 'woocommerce_cart_contents' );
				?>

				<!-- Coupon + update -->
				<div class="flex flex-col gap-3 bg-brand-bg/60 p-4 sm:flex-row sm:items-center sm:justify-between">
					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon flex gap-2">
							<label for="coupon_code" class="sr-only"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
							<input type="text" name="coupon_code" class="h-10 rounded-lg border-slate-200 text-sm focus:border-brand-primary focus:ring-brand-primary" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Have a coupon? Enter code', 'woocommerce' ); ?>" />
							<button type="submit" class="button flex-none rounded-lg bg-brand-primary px-4 text-sm font-bold text-white transition hover:bg-brand-hover" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="wmb-update-cart button inline-flex items-center gap-1.5 self-start text-sm font-semibold text-slate-500 transition hover:text-brand-primary" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
						<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
						<?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
					</button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>

		<!-- Cross-sells (upsell without leaving the cart) -->
		<div class="mt-8 [&_h2]:mb-4 [&_h2]:text-lg [&_h2]:font-bold [&_h2]:text-brand-title [&_ul.products]:grid [&_ul.products]:grid-cols-2 [&_ul.products]:gap-4 [&_ul.products]:list-none [&_ul.products]:p-0 sm:[&_ul.products]:grid-cols-3">
			<?php woocommerce_cross_sell_display(); ?>
		</div>
	</div>

	<!-- ═══════════ TOTALS (sticky) ═══════════ -->
	<aside class="mt-8 lg:mt-0 lg:sticky lg:top-24">
		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
		<?php woocommerce_cart_totals(); ?>
	</aside>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var form = document.querySelector('.woocommerce-cart-form');
	if (!form) { return; }
	var timer;
	function autoUpdate() {
		clearTimeout(timer);
		timer = setTimeout(function () {
			if (!form.querySelector('input[name="update_cart"][type="hidden"]')) {
				var h = document.createElement('input');
				h.type = 'hidden'; h.name = 'update_cart'; h.value = '1';
				form.appendChild(h);
			}
			if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); }
		}, 700);
	}
	form.querySelectorAll('.wmb-qty').forEach(function (box) {
		var input = box.querySelector('input.qty');
		if (!input) { return; }
		var minus = box.querySelector('.wmb-qminus');
		var plus  = box.querySelector('.wmb-qplus');
		if (minus) { minus.addEventListener('click', function () {
			var min = parseInt(input.min, 10) || 0;
			input.value = Math.max(min, (parseInt(input.value, 10) || 0) - 1);
			autoUpdate();
		}); }
		if (plus) { plus.addEventListener('click', function () {
			var max = parseInt(input.max, 10);
			var next = (parseInt(input.value, 10) || 0) + 1;
			input.value = max ? Math.min(max, next) : next;
			autoUpdate();
		}); }
		input.addEventListener('change', autoUpdate);
	});
});
</script>

<?php do_action( 'woocommerce_after_cart' ); ?>
