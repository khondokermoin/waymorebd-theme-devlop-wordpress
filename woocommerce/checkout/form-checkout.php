<?php
/**
 * Checkout form — Way More BD (reference layout)
 *
 *   LEFT                                RIGHT
 *   │ Order review  (items + qty)       │ Payment method (cards)
 *   │ Shipping / Billing details        │ Coupon accordion
 *                                       │ Totals
 *                                       │ Special notes
 *                                       │ Terms + PLACE ORDER
 *
 * Section headings carry an orange left rule (│) like the reference.
 *
 * ── LOAD-BEARING (do not remove) ─────────────────────────────────────────
 *   do_action('woocommerce_before_checkout_form', $checkout)   login + coupon
 *   <form name="checkout" class="checkout woocommerce-checkout">
 *   #customer_details                                          address AJAX
 *   .woocommerce-checkout-review-order-table  (root of review-order.php)
 *   .woocommerce-checkout-payment             (root of payment.php)
 *       -> update_order_review replaces BOTH by class; they may live in
 *          different columns, but both must exist.
 *   woocommerce_checkout_payment() emits #place_order AND the
 *   woocommerce-process-checkout-nonce — never hand-build that button.
 *   do_action('woocommerce_after_checkout_form', $checkout)    gateway JS
 *
 * NOTE: functions.php detaches woocommerce_order_review / woocommerce_checkout_payment
 * from the woocommerce_checkout_order_review action so we can position them.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/checkout/form-checkout.php
 *
 * @package isdb-custom
 * @version 9.4.0
 * @var WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form_cart_notices' );

$checkout = WC()->checkout();

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="wmb-checkout checkout-area">

	<?php // Guest prompt bar — mirrors the reference's "Have any account?" strip. ?>
	<?php if ( ! is_user_logged_in() ) : ?>
		<div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-card border border-[#e0e0e0] bg-white px-4 py-3">
			<span class="text-[14px] text-brand-body"><?php esc_html_e( 'Have any account? please login or register', 'isdb-custom' ); ?></span>
			<span class="flex flex-none gap-2">
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
					class="rounded-card border border-[#ddd] px-5 py-2 text-[13px] font-semibold text-brand-title transition hover:border-brand-primary hover:text-brand-primary"><?php esc_html_e( 'Login', 'isdb-custom' ); ?></a>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
					class="rounded-card bg-brand-primary px-5 py-2 text-[13px] font-semibold text-white transition hover:bg-brand-hover"><?php esc_html_e( 'Register', 'isdb-custom' ); ?></a>
			</span>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

		<div class="lg:grid lg:grid-cols-[1fr,420px] lg:items-start lg:gap-5">

			<!-- ═══════════════ LEFT ═══════════════ -->
			<div class="space-y-4">

				<!-- Order review (items) -->
				<section class="rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5">
					<h2 class="wmb-sec-head"><?php esc_html_e( 'Order review', 'isdb-custom' ); ?></h2>
					<?php
					do_action( 'woocommerce_checkout_before_order_review' );

					// review-order.php — root keeps .woocommerce-checkout-review-order-table
					if ( function_exists( 'woocommerce_order_review' ) ) {
						woocommerce_order_review();
					}

					do_action( 'woocommerce_checkout_after_order_review' );
					?>
				</section>

				<!-- Customer details -->
				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="space-y-4" id="customer_details">
						<section class="c-personal-details-box rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</section>

						<?php // Only render the "Billing Address" toggle card when WC actually collects a shipping address (avoids an empty box). ?>
						<?php if ( WC()->cart->needs_shipping_address() ) : ?>
							<section class="rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5">
								<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							</section>
						<?php else : ?>
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						<?php endif; ?>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>
			</div>

			<!-- ═══════════════ RIGHT ═══════════════ -->
			<div class="mt-4 space-y-4 lg:mt-0 lg:sticky lg:top-4">

				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

				<!-- 1. Payment method (methods only; place order is at the bottom) -->
				<section class="rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5">
					<h2 id="order_review_heading" class="wmb-sec-head"><?php esc_html_e( 'Payment method', 'isdb-custom' ); ?></h2>
					<div id="order_review" class="woocommerce-checkout-review-order">
						<?php
						if ( function_exists( 'woocommerce_checkout_payment' ) ) {
							woocommerce_checkout_payment();
						}
						?>
					</div>
				</section>

				<!-- 2. Coupon -->
				<?php if ( wc_coupons_enabled() ) : ?>
					<section class="rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5" x-data="{ open:false }">
						<button type="button" @click="open = !open" class="flex w-full items-center justify-between">
							<span class="wmb-sec-head !mb-0"><?php esc_html_e( 'Have any coupon or gift voucher?', 'isdb-custom' ); ?></span>
							<svg class="h-4 w-4 text-slate-400 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
						</button>
						<div x-show="open" x-cloak
							x-transition:enter="transition ease-out duration-200"
							x-transition:enter-start="opacity-0 -translate-y-1"
							x-transition:enter-end="opacity-100 translate-y-0"
							class="mt-3">
							<form class="checkout_coupon flex flex-col gap-2 sm:flex-row" method="post">
								<input type="text" name="coupon_code" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Enter coupon code', 'isdb-custom' ); ?>"
									class="input-text flex-1" />
								<button type="submit" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"
									class="flex-none rounded bg-brand-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-hover">
									<?php esc_html_e( 'Apply', 'woocommerce' ); ?>
								</button>
							</form>
						</div>
					</section>
				<?php endif; ?>

				<!-- 3. Order summary (Sub total / Delivery / Total) — live AJAX fragment -->
				<section class="rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5">
					<?php isdb_render_checkout_totals(); ?>
				</section>

				<!-- 4. Special notes (fixed size) -->
				<?php
				$order_fields = $checkout->get_checkout_fields( 'order' );
				if ( ! empty( $order_fields ) ) :
					?>
					<section class="woocommerce-additional-fields rounded-card border border-[#e0e0e0] bg-white p-4 sm:p-5">
						<h2 class="wmb-sec-head"><?php esc_html_e( 'Special notes', 'isdb-custom' ); ?> <span class="text-[12px] font-normal text-slate-500"><?php esc_html_e( '(Optional)', 'isdb-custom' ); ?></span></h2>
						<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>
						<div class="woocommerce-additional-fields__field-wrapper wmb-notes">
							<?php foreach ( $order_fields as $key => $field ) : ?>
								<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
							<?php endforeach; ?>
						</div>
						<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
					</section>
				<?php endif; ?>

				<!-- 5. Terms + Place Order (moved out of payment.php; nonce preserved) -->
				<div class="wmb-submit">
					<?php wc_get_template( 'checkout/terms.php' ); ?>

					<div class="form-row place-order">
						<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
						<?php
						$order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'woocommerce' ) );
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_order_button_html',
							'<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>'
						);
						?>
						<?php do_action( 'woocommerce_review_order_after_submit' ); ?>
						<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
					</div>
				</div>

				<!-- Security reassurance -->
				<div class="flex items-start gap-2.5 rounded-card border border-[#e0e0e0] bg-white p-3.5">
					<svg class="h-5 w-5 flex-none text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
					<p class="m-0 text-[11px] leading-relaxed text-slate-500">
						<?php
						printf(
							/* translators: %s: bold "Cash on Delivery" phrase */
							esc_html__( 'Secured with 256-bit SSL encryption. %s available — pay only when your order arrives.', 'isdb-custom' ),
							'<span class="font-semibold text-brand-title">' . esc_html__( 'Cash on Delivery', 'isdb-custom' ) . '</span>'
						);
						?>
					</p>
				</div>
			</div>
		</div>
	</form>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>
