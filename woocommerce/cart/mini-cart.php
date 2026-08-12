<?php
/**
 * Mini-cart (slide-over drawer) — matches the reference screenshots.
 *
 * Layout: the container (.widget_shopping_cart_content) is a full-height flex
 * column (see app.css). Cart items SCROLL in the middle; the "You May Also
 * Like" rail + Total + Checkout live in a FIXED footer pinned to the bottom, so
 * they never float up when the cart is short.
 *
 * Quantity buttons reuse the shared .isdb-qty-btn handler (functions.php), which
 * posts to isdb_checkout_qty then fires wc_fragment_refresh — so this whole
 * template re-renders through WooCommerce's own fragment system.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/cart/mini-cart.php
 *
 * @package isdb-custom
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' );

$free_ship = function_exists( 'isdb_free_shipping_progress' ) ? isdb_free_shipping_progress() : null;
?>

<?php if ( ! WC()->cart->is_empty() ) : ?>

	<!-- ═══════════ SCROLLABLE: items ═══════════ -->
	<div class="wmb-mini-scroll min-h-0 flex-1 overflow-y-auto px-5 py-4">

		<?php if ( $free_ship ) : ?>
			<div class="mb-3 rounded-card bg-brand-bg p-3">
				<?php if ( $free_ship['reached'] ) : ?>
					<p class="m-0 text-[13px] font-semibold text-emerald-700">✓ <?php esc_html_e( 'You\'ve unlocked FREE shipping!', 'isdb-custom' ); ?></p>
				<?php else : ?>
					<p class="m-0 text-[13px] text-brand-body"><?php
						printf(
							/* translators: %s: remaining amount until free shipping */
							esc_html__( 'You\'re %s from free shipping', 'isdb-custom' ),
							'<span class="font-bold text-brand-title">' . wp_kses_post( $free_ship['remaining_html'] ) . '</span>'
						);
					?></p>
				<?php endif; ?>
				<div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
					<div class="h-full rounded-full bg-brand-primary transition-all" style="width: <?php echo esc_attr( $free_ship['pct'] ); ?>%"></div>
				</div>
			</div>
		<?php endif; ?>

		<ul class="woocommerce-mini-cart m-0 list-none space-y-2 p-0">
			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

					$qty        = (int) $cart_item['quantity'];
					$unit       = (float) $_product->get_price();
					$is_gift    = ( $unit <= 0 );
					$single_out = $_product->is_sold_individually();
					$max        = $_product->get_max_purchase_quantity();
					$at_max     = ( $max > 0 && $qty >= $max );
					?>
					<li class="isdb-mini-item flex gap-2.5 rounded-card border border-[#e0e0e0] bg-white p-2.5 transition">

						<!-- Thumbnail -->
						<a href="<?php echo esc_url( $product_permalink ); ?>" class="h-12 w-12 flex-none overflow-hidden rounded border border-[#e0e0e0] bg-white [&_img]:h-full [&_img]:w-full [&_img]:object-contain">
							<?php echo wp_kses_post( $thumbnail ); ?>
						</a>

						<!-- Middle -->
						<div class="min-w-0 flex-1">
							<a href="<?php echo esc_url( $product_permalink ); ?>" class="line-clamp-1 text-[13px] font-medium text-brand-title transition hover:text-brand-primary">
								<?php echo wp_kses_post( $product_name ); ?>
							</a>

							<?php if ( $is_gift ) : ?>
								<span class="mt-1 inline-block rounded bg-brand-primary px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white"><?php esc_html_e( 'Gift', 'isdb-custom' ); ?></span>
							<?php endif; ?>

							<?php
							$item_data = wc_get_formatted_cart_item_data( $cart_item );
							if ( $item_data ) {
								echo '<div class="mt-0.5 text-[11px] leading-tight text-slate-500 [&_p]:m-0">' . $item_data . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>

							<!-- Stepper + line math -->
							<div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
								<?php if ( $single_out || $is_gift ) : ?>
									<span class="text-[12px] font-semibold text-brand-title"><?php echo esc_html( $qty ); ?></span>
								<?php else : ?>
									<div class="inline-flex items-center overflow-hidden rounded border border-[#ddd]">
										<button type="button"
											class="isdb-qty-btn flex h-6 w-6 items-center justify-center text-brand-title transition hover:bg-brand-soft hover:text-brand-primary"
											data-key="<?php echo esc_attr( $cart_item_key ); ?>"
											data-qty="<?php echo esc_attr( $qty - 1 ); ?>"
											aria-label="<?php esc_attr_e( 'Decrease quantity', 'isdb-custom' ); ?>">
											<svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14"/></svg>
										</button>
										<span class="min-w-[1.5rem] px-0.5 text-center text-[12px] font-semibold text-brand-title"><?php echo esc_html( $qty ); ?></span>
										<button type="button"
											class="isdb-qty-btn flex h-6 w-6 items-center justify-center text-brand-title transition hover:bg-brand-soft hover:text-brand-primary disabled:opacity-40"
											data-key="<?php echo esc_attr( $cart_item_key ); ?>"
											data-qty="<?php echo esc_attr( $qty + 1 ); ?>"
											<?php disabled( $at_max ); ?>
											aria-label="<?php esc_attr_e( 'Increase quantity', 'isdb-custom' ); ?>">
											<svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
										</button>
									</div>
								<?php endif; ?>

								<span class="text-[11px] text-slate-500">
									&times; <?php echo wp_kses_post( wc_price( $unit ) ); ?>
									= <span class="font-semibold text-brand-title"><?php echo wp_kses_post( wc_price( $unit * $qty ) ); ?></span>
								</span>
							</div>
						</div>

						<!-- Remove (WooCommerce AJAX remove link) -->
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a href="%s" class="remove remove_from_cart_button h-6 w-6 flex-none self-start rounded text-slate-300 transition hover:bg-rose-50 hover:text-rose-600 flex items-center justify-center" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg></a>',
								esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
								esc_attr__( 'Remove this item', 'woocommerce' ),
								esc_attr( $product_id ),
								esc_attr( $cart_item_key ),
								esc_attr( $_product->get_sku() )
							),
							$cart_item_key
						);
						?>
					</li>
					<?php
				}
			}
			?>
		</ul>

		<?php do_action( 'woocommerce_mini_cart_contents' ); ?>
	</div>

	<!-- ═══════════ PINNED FOOTER: cross-sell + total + checkout ═══════════ -->
	<div class="wmb-mini-foot flex-none border-t border-[#e0e0e0] bg-white px-5 py-4">

		<!-- You May Also Like -->
		<?php
		$in_cart_ids = array();
		foreach ( WC()->cart->get_cart() as $ci ) {
			$in_cart_ids[] = $ci['product_id'];
		}
		$suggest = function_exists( 'isdb_best_sellers' ) ? isdb_best_sellers( 10 ) : array();
		$rail    = array();
		foreach ( $suggest as $sp ) {
			if ( $sp instanceof WC_Product && $sp->is_visible() && $sp->is_in_stock() && ! in_array( $sp->get_id(), $in_cart_ids, true ) ) {
				$rail[] = $sp;
			}
			if ( count( $rail ) >= 6 ) {
				break;
			}
		}
		if ( $rail ) : ?>
			<div class="mb-4">
				<p class="m-0 mb-2.5 text-[13px] font-bold text-brand-title"><?php esc_html_e( 'You May Also Like', 'isdb-custom' ); ?></p>
				<div class="-mx-1 flex snap-x gap-2.5 overflow-x-auto px-1 pb-1">
					<?php foreach ( $rail as $rp ) : ?>
						<div class="w-[140px] flex-none snap-start rounded-card border border-[#e0e0e0] bg-white p-2">
							<a href="<?php echo esc_url( $rp->get_permalink() ); ?>" class="block">
								<span class="flex h-[64px] items-center justify-center overflow-hidden">
									<?php echo $rp->get_image( 'woocommerce_thumbnail', array( 'class' => 'max-h-full w-auto max-w-full object-contain' ) ); ?>
								</span>
								<span class="mt-1.5 line-clamp-2 block text-[11px] leading-tight text-brand-title"><?php echo esc_html( $rp->get_name() ); ?></span>
							</a>
							<span class="mt-1 block text-[12px] font-bold text-brand-primary"><?php echo wp_kses_post( $rp->get_price_html() ); ?></span>
							<?php // href = product page (fail-safe); JS intercepts for the AJAX add. Never ?add-to-cart= (that would redirect). ?>
							<a href="<?php echo esc_url( $rp->get_permalink() ); ?>"
								data-quantity="1" data-product_id="<?php echo esc_attr( $rp->get_id() ); ?>"
								class="add_to_cart_button ajax_add_to_cart mt-1.5 flex items-center justify-center gap-1 rounded border border-brand-primary bg-white py-1 text-[11px] font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white">
								+ <?php esc_html_e( 'Add', 'isdb-custom' ); ?>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Total + checkout -->
		<div class="flex items-center justify-between border-t border-[#e0e0e0] pt-3">
			<span class="text-sm font-semibold text-brand-title"><?php esc_html_e( 'Total:', 'isdb-custom' ); ?></span>
			<span class="text-lg font-extrabold text-brand-title"><?php echo wp_kses_post( isdb_cart_subtotal_html() ); ?></span>
		</div>

		<?php // No "View Cart" — the drawer IS the cart; /cart/ redirects to checkout (see functions.php). ?>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
			class="mt-3 block rounded-card bg-brand-primary py-3 text-center text-sm font-bold uppercase tracking-wide text-white transition hover:bg-brand-hover">
			<?php esc_html_e( 'Checkout', 'isdb-custom' ); ?>
		</a>
	</div>

<?php else : ?>

	<div class="flex min-h-0 flex-1 flex-col items-center justify-center px-5 py-16 text-center">
		<span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-bg text-slate-400">
			<svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
		</span>
		<p class="mt-4 text-base font-semibold text-brand-title"><?php esc_html_e( 'Your cart is empty', 'isdb-custom' ); ?></p>
		<p class="mt-1 text-sm text-slate-500"><?php esc_html_e( 'Let\'s find something for your kitchen.', 'isdb-custom' ); ?></p>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"
			class="mt-6 rounded-card bg-brand-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-hover">
			<?php esc_html_e( 'Browse Products', 'isdb-custom' ); ?>
		</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
