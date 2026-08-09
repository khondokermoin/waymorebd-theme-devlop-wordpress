<?php
/**
 * Order received / Thank-you — Way More BD (checkout/thankyou.php)
 *
 * Two moments, one page (matches the reference):
 *   1. A "sweet alert" success modal auto-opens once per order (session-guarded).
 *   2. Behind it, the shared order TRACKER: status timeline → products →
 *      order summary → shipping details (isdb_render_order_tracker()).
 *
 * All WooCommerce thankyou hooks are preserved (payment plugins depend on them).
 * functions.php removes woocommerce_order_details_table from the woocommerce_thankyou
 * hook on this page so the default tables don't duplicate the custom tracker.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/checkout/thankyou.php
 *
 * @package isdb-custom
 * @version 8.1.0
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="woocommerce-order wmb-thankyou">

	<?php if ( $order ) : ?>

		<?php do_action( 'woocommerce_before_thankyou', $order->get_id() ); ?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<!-- Failed: reassure + clear recovery path (reduce panic) -->
			<div class="mx-auto max-w-xl rounded-card bg-white p-8 text-center ring-1 ring-rose-100">
				<span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 text-rose-600">
					<svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 9v4m0 4h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L14.7 3.9a2 2 0 00-3.4 0z"/></svg>
				</span>
				<h1 class="mt-4 text-2xl font-bold text-brand-title">Payment didn't go through</h1>
				<p class="mt-2 text-brand-body"><?php esc_html_e( 'No charge was made. Please try again — your cart is safe.', 'isdb-custom' ); ?></p>
				<div class="mt-6 flex flex-wrap justify-center gap-3">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="rounded-card bg-brand-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-hover"><?php esc_html_e( 'Pay again', 'woocommerce' ); ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="rounded-card border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-stone-50"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

		<?php else : ?>

			<?php
			$order_no   = $order->get_order_number();
			$placed_on  = wc_format_datetime( $order->get_date_created(), 'd M Y' );
			$total_html = $order->get_formatted_order_total();
			$first_name = $order->get_billing_first_name();
			?>

			<div
				x-data="{ open:false }"
				x-init="$nextTick(() => { const k='wmb_seen_<?php echo (int) $order->get_id(); ?>'; if (sessionStorage.getItem(k)!=='1'){ open=true; sessionStorage.setItem(k,'1'); } }); $watch('open', v => { document.body.style.overflow = v ? 'hidden' : '' })">

				<!-- ═══════════════ SWEET-ALERT SUCCESS MODAL ═══════════════ -->
				<div x-show="open" x-cloak class="fixed inset-0 z-[95] flex items-center justify-center p-4"
					x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
					x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
					<div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open=false"></div>

					<div class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white text-center shadow-2xl"
						@keydown.escape.window="open=false"
						x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-3" x-transition:enter-end="opacity-100 scale-100 translate-y-0">

						<button type="button" @click="open=false" aria-label="<?php esc_attr_e( 'Close', 'isdb-custom' ); ?>" class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
							<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
						</button>

						<div class="px-7 pb-7 pt-9">
							<span class="inline-flex items-center gap-1.5 rounded-full bg-brand-primary/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-brand-primary">
								<span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span> <?php esc_html_e( 'Confirmed', 'isdb-custom' ); ?>
							</span>

							<span class="wmb-modal__icon mx-auto mt-5 flex h-24 w-24 items-center justify-center rounded-full bg-brand-primary/10">
								<svg class="h-14 w-14 text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
							</span>

							<h2 class="mt-5 text-2xl font-extrabold text-brand-title"><?php esc_html_e( 'Order Placed!', 'isdb-custom' ); ?></h2>
							<p class="mt-2 text-sm leading-relaxed text-brand-body"><?php esc_html_e( 'Your order has been received and is being processed. A confirmation is on its way.', 'isdb-custom' ); ?></p>

							<div class="mt-5 divide-y divide-slate-100 rounded-xl border border-slate-100 bg-brand-bg text-left">
								<div class="flex items-center justify-between px-4 py-2.5">
									<span class="text-[13px] text-slate-500"><?php esc_html_e( 'Order No', 'isdb-custom' ); ?></span>
									<span class="text-[13px] font-bold text-brand-title">#<?php echo esc_html( $order_no ); ?></span>
								</div>
								<div class="flex items-center justify-between px-4 py-2.5">
									<span class="text-[13px] text-slate-500"><?php esc_html_e( 'Total', 'isdb-custom' ); ?></span>
									<span class="text-[13px] font-bold text-brand-title"><?php echo wp_kses_post( $total_html ); ?></span>
								</div>
								<div class="flex items-center justify-between px-4 py-2.5">
									<span class="text-[13px] text-slate-500"><?php esc_html_e( 'Placed on', 'isdb-custom' ); ?></span>
									<span class="text-[13px] font-bold text-brand-title"><?php echo esc_html( $placed_on ); ?></span>
								</div>
							</div>

							<button type="button" @click="open=false" class="mt-6 w-full rounded-xl bg-brand-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-hover">
								<?php esc_html_e( 'View Order Tracker', 'isdb-custom' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<!-- ═══════════════ TRACKER HERO ═══════════════ -->
			<div class="mx-auto max-w-3xl overflow-hidden rounded-2xl bg-brand-dark px-6 py-8 text-center text-white sm:px-10">
				<span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-white/90">
					<svg class="h-4 w-4 text-brand-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
					<?php esc_html_e( 'Thank you', 'isdb-custom' ); ?><?php echo $first_name ? ', ' . esc_html( $first_name ) : ''; ?>!
				</span>
				<h1 class="mt-4 text-2xl font-extrabold tracking-tight text-white sm:text-3xl"><?php esc_html_e( 'Track Your Order', 'isdb-custom' ); ?></h1>
				<p class="mt-2 text-sm text-white/70"><?php esc_html_e( 'Order', 'isdb-custom' ); ?> <span class="font-bold text-white">#<?php echo esc_html( $order_no ); ?></span> · <?php esc_html_e( 'Placed on', 'isdb-custom' ); ?> <?php echo esc_html( $placed_on ); ?></p>
			</div>

			<!-- ═══════════════ TRACKER BODY (shared) ═══════════════ -->
			<div class="mt-6">
				<?php isdb_render_order_tracker( $order ); ?>
			</div>

			<!-- Functional WC hooks (order-details table removed in functions.php; gateways/plugins still fire) -->
			<div class="mx-auto mt-2 max-w-3xl empty:hidden">
				<?php
				do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
				do_action( 'woocommerce_thankyou', $order->get_id() );
				?>
			</div>

			<!-- Cross-sell carousel: warmest re-buy moment (real product cards) -->
			<?php
			$purchased = array();
			foreach ( $order->get_items() as $item ) {
				$purchased[] = $item->get_product_id();
			}
			$suggestions = function_exists( 'isdb_best_sellers' ) ? isdb_best_sellers( 16 ) : array();
			$cards       = array();
			foreach ( $suggestions as $sp ) {
				if ( $sp instanceof WC_Product && $sp->is_visible() && $sp->is_in_stock() && ! in_array( $sp->get_id(), $purchased, true ) ) {
					$cards[] = $sp;
				}
				if ( count( $cards ) >= 12 ) {
					break;
				}
			}
			if ( $cards && function_exists( 'isdb_render_product_carousel' ) ) :
				?>
				<section class="mx-auto mt-14 max-w-5xl">
					<div class="text-center">
						<h2 class="text-xl font-bold text-brand-title"><?php esc_html_e( 'Complete your kitchen', 'isdb-custom' ); ?></h2>
						<p class="mt-1 text-sm text-slate-500"><?php esc_html_e( 'Popular picks other customers love — add to your next order.', 'isdb-custom' ); ?></p>
					</div>
					<div class="mt-6">
						<?php isdb_render_product_carousel( $cards, 12, 5000 ); ?>
					</div>
				</section>
			<?php endif; ?>

			<!-- Post-purchase reassurance + next actions (high-contrast CTAs) -->
			<div class="mx-auto mt-14 max-w-3xl overflow-hidden rounded-2xl bg-brand-dark p-8 text-center text-white">
				<span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-primary/15 text-brand-primary">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
				</span>
				<p class="mt-4 text-lg font-bold">You're all set — we've got it from here.</p>
				<p class="mx-auto mt-1 max-w-lg text-sm text-white/70">That's our Customer Support Promise — real humans, quick replies. We'll keep you posted as your order moves.</p>
				<div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
					<a href="<?php echo esc_url( isdb_track_order_url() ); ?>" class="rounded-xl bg-brand-primary px-7 py-3 text-sm font-bold !text-white shadow-lg shadow-black/20 transition hover:bg-brand-hover"><?php esc_html_e( 'Track Your Order', 'isdb-custom' ); ?></a>
					<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="rounded-xl bg-white px-7 py-3 text-sm font-bold !text-brand-dark transition hover:bg-white/90"><?php esc_html_e( 'Continue Shopping', 'isdb-custom' ); ?></a>
				</div>
			</div>

		<?php endif; ?>

	<?php else : ?>

		<div class="mx-auto max-w-xl text-center">
			<p class="text-brand-body"><?php echo esc_html( apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.', 'woocommerce' ), null ) ); ?></p>
		</div>

	<?php endif; ?>
</div>
