<?php
/**
 * Checkout payment — Way More BD (PAYMENT METHODS ONLY)
 *
 * The Place Order button + terms + process nonce that core bundles here have
 * been moved to the bottom of the right column (see form-checkout.php), so the
 * layout reads: Payment method → Coupon → Totals → Notes → Terms → Place Order.
 *
 * ── LOAD-BEARING ─────────────────────────────────────────────────────────
 * Keeps `#payment.woocommerce-checkout-payment` — that div IS the AJAX
 * fragment WooCommerce replaces on update_order_review. The moved Place Order
 * block still lives inside <form name="checkout"> and still carries the
 * woocommerce-process-checkout-nonce, so submission is unchanged.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/checkout/payment.php
 *
 * @package isdb-custom
 * @version 10.9.0
 * @var array $available_gateways
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods" aria-label="<?php esc_attr_e( 'Payment methods', 'woocommerce' ); ?>">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li>';
				wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ), 'notice' ); // phpcs:ignore
				echo '</li>';
			}
			?>
		</ul>
	<?php endif; ?>
</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
