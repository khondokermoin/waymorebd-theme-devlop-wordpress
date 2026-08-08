<?php
/**
 * Cart page — Way More BD (cart/cart.php)
 * 2-column: items (left) + sticky totals (right). All WooCommerce hooks,
 * nonces, and field names preserved — only the markup/styling is custom.
 * Overrides: wp-content/plugins/woocommerce/templates/cart/cart.php
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<h1 class="mb-6 text-3xl font-bold tracking-tight text-slate-900">Your Cart</h1>

<div class="lg:grid lg:grid-cols-[1fr,380px] lg:items-start lg:gap-10">

	<!-- ITEMS -->
	<div>
		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<?php do_action( 'woocommerce_before_cart_table' ); ?>

			<div class="divide-y divide-slate-100 rounded-2xl bg-white ring-1 ring-slate-100">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
						?>
						<div class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> flex gap-4 p-4">

							<!-- Thumbnail -->
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="block h-20 w-20 flex-none overflow-hidden rounded-xl bg-stone-50 ring-1 ring-slate-100 [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
								<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key ) ); ?>
							</a>

							<!-- Name / price / qty / remove -->
							<div class="min-w-0 flex-1">
								<div class="text-sm font-semibold text-slate-800">
									<?php
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a class="transition hover:text-amber-600" href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}

									do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

									// Meta data + backorder notification.
									echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification text-xs text-amber-600">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
									}
									?>
								</div>

								<div class="mt-1 text-sm text-slate-500">
									<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>

								<div class="mt-3 flex items-center gap-4">
									<!-- Quantity (functional: preserves cart[key][qty] name) -->
									<div class="[&_input]:h-10 [&_input]:w-16 [&_input]:rounded-lg [&_input]:border-slate-200 [&_input]:text-center [&_input]:text-sm [&_.quantity]:inline-flex">
										<?php
										if ( $_product->is_sold_individually() ) {
											$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
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
									</div>

									<!-- Remove (functional AJAX) -->
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="remove text-sm font-medium text-slate-400 transition hover:text-rose-600" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">%s</a>',
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

							<!-- Line subtotal -->
							<div class="flex-none text-right text-sm font-bold text-slate-900">
								<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						</div>
						<?php
					}
				}

				do_action( 'woocommerce_cart_contents' );
				?>

				<!-- Actions row: coupon + update -->
				<div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:justify-between">
					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon flex gap-2">
							<label for="coupon_code" class="sr-only"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
							<input type="text" name="coupon_code" class="h-10 rounded-lg border-slate-200 text-sm focus:border-amber-500 focus:ring-amber-500" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
							<button type="submit" class="button rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition hover:bg-stone-50" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<button type="submit" class="button rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:opacity-50" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

					<?php do_action( 'woocommerce_cart_actions' ); ?>
					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</form>

		<!-- Cross-sells (upsell without leaving cart) -->
		<div class="mt-8 [&_ul.products]:grid [&_ul.products]:grid-cols-2 [&_ul.products]:gap-4 [&_ul.products]:list-none [&_ul.products]:p-0 sm:[&_ul.products]:grid-cols-3">
			<?php woocommerce_cross_sell_display(); ?>
		</div>
	</div>

	<!-- TOTALS (sticky) -->
	<aside class="mt-8 lg:mt-0 lg:sticky lg:top-24">
		<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
		<?php woocommerce_cart_totals(); ?>

		<!-- Trust reassurance -->
		<div class="mt-4 flex items-center justify-center gap-2 text-xs text-slate-400">
			<svg class="h-4 w-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1l7 3v5c0 4-3 7.5-7 9-4-1.5-7-5-7-9V4l7-3z" clip-rule="evenodd"/></svg>
			Secure checkout · Cash on Delivery available
		</div>
	</aside>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
