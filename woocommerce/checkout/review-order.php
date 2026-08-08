<?php
/**
 * Checkout order review — Way More BD (ITEMS ONLY)
 *
 * Item cards with a qty stepper. The totals summary (Sub total / Delivery /
 * Total) is rendered separately in the RIGHT column (isdb_render_checkout_totals
 * in functions.php) so the layout matches the reference.
 *
 * ── LOAD-BEARING ─────────────────────────────────────────────────────────
 * Root keeps class `woocommerce-checkout-review-order-table` — WC's
 * update_order_review AJAX does $('.woocommerce-checkout-review-order-table')
 * .replaceWith(html). The qty stepper posts to isdb_checkout_qty then fires
 * update_checkout, which refreshes this AND the .wmb-order-totals fragment.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/checkout/review-order.php
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table">

	<ul class="isdb-review-items m-0 list-none space-y-3 p-0">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$qty        = (int) $cart_item['quantity'];
				$single_out = $_product->is_sold_individually();
				$max        = $_product->get_max_purchase_quantity();
				$at_max     = ( $max > 0 && $qty >= $max );
				?>
				<li class="isdb-review-item flex gap-3 rounded border border-[#e0e0e0] bg-white p-2.5 transition">

					<div class="h-14 w-14 flex-none overflow-hidden rounded border border-[#e0e0e0] bg-white [&_img]:h-full [&_img]:w-full [&_img]:object-cover">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key ) ); ?>
					</div>

					<div class="min-w-0 flex-1">
						<p class="line-clamp-2 text-[13px] font-semibold leading-snug text-brand-title">
							<?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?>
						</p>

						<?php
						$item_data = wc_get_formatted_cart_item_data( $cart_item );
						if ( $item_data ) {
							echo '<div class="mt-0.5 text-[11px] leading-tight text-slate-500 [&_p]:m-0">' . $item_data . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>

						<?php if ( $single_out ) : ?>
							<span class="mt-1.5 inline-block text-[11px] font-semibold text-slate-500">Qty: 1</span>
						<?php else : ?>
							<div class="isdb-qty mt-1.5 inline-flex items-center overflow-hidden rounded border border-[#ddd]">
								<button type="button" class="isdb-qty-btn flex h-7 w-7 items-center justify-center text-brand-title transition hover:bg-brand-soft hover:text-brand-primary disabled:opacity-40"
									data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="<?php echo esc_attr( $qty - 1 ); ?>" aria-label="Decrease quantity">
									<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/></svg>
								</button>
								<span class="min-w-[2rem] px-1 text-center text-[13px] font-bold text-brand-title"><?php echo esc_html( $qty ); ?></span>
								<button type="button" class="isdb-qty-btn flex h-7 w-7 items-center justify-center text-brand-title transition hover:bg-brand-soft hover:text-brand-primary disabled:opacity-40"
									data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="<?php echo esc_attr( $qty + 1 ); ?>" <?php disabled( $at_max ); ?> aria-label="Increase quantity">
									<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
								</button>
							</div>
						<?php endif; ?>
					</div>

					<div class="flex flex-none flex-col items-end justify-between">
						<span class="text-sm font-bold text-brand-primary">
							<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<button type="button" class="isdb-qty-btn mt-1 text-[11px] font-medium text-slate-400 transition hover:text-rose-600"
							data-key="<?php echo esc_attr( $cart_item_key ); ?>" data-qty="0" aria-label="Remove item">Remove</button>
					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</ul>
</div>
