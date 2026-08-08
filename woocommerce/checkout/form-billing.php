<?php
/**
 * Checkout billing form — Way More BD
 *
 * Presented to the customer as "Shipping Address", because WooCommerce ships
 * to the billing address unless "different billing address" is ticked. The
 * FIELD NAMES stay WooCommerce's billing_* names so orders, emails and the
 * admin screens keep working exactly as core expects.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/checkout/form-billing.php
 *
 * @package isdb-custom
 * @var WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3 class="wmb-sec-head"><?php esc_html_e( 'Shipping Address', 'isdb-custom' ); ?></h3>

	<?php else : ?>

		<h3 class="wmb-sec-head"><?php esc_html_e( 'Shipping Address', 'isdb-custom' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields( 'billing' );

		foreach ( $fields as $key => $field ) {
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		}
		?>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>
