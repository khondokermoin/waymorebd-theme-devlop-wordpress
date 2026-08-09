<?php
/**
 * Checkout shipping form — Way More BD
 *
 * Rendered as the "Billing Address" card: heading on the left, a round toggle
 * on the right. Ticking it smoothly reveals the extra address fields.
 *
 * ── LOAD-BEARING ─────────────────────────────────────────────────────────
 *   #ship-to-different-address-checkbox  +  name="ship_to_different_address"
 *   div.shipping_address                 -> WooCommerce's own checkout.js does
 *   the slideDown/slideUp on this exact selector, which is where the smooth
 *   animation comes from. Renaming either breaks the reveal AND the address.
 *
 * NOTE: the "Additional information" / order-notes block that core renders
 * here has been moved to the right column (see form-checkout.php) so it can
 * appear as "Special notes" beside Place Order, matching the reference. The
 * field keys are unchanged, so submission still works.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/checkout/form-shipping.php
 *
 * @package isdb-custom
 * @version 3.6.0
 * @var WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>

		<h3 id="ship-to-different-address" class="m-0">
			<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox wmb-addr-toggle m-0 flex cursor-pointer items-center justify-between gap-3">
				<span class="wmb-sec-head !mb-0"><?php esc_html_e( 'Billing Address', 'isdb-custom' ); ?></span>

				<span class="flex items-center gap-2">
					<span class="hidden text-[12px] text-slate-500 sm:inline"><?php esc_html_e( 'Different from shipping?', 'isdb-custom' ); ?></span>
					<input id="ship-to-different-address-checkbox"
						class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox wmb-addr-check"
						<?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?>
						type="checkbox" name="ship_to_different_address" value="1" />
				</span>
			</label>
		</h3>

		<?php // WooCommerce's checkout.js slides this container — keep the class. ?>
		<div class="shipping_address mt-4 border-t border-slate-100 pt-4">
			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<div class="woocommerce-shipping-fields__field-wrapper">
				<?php
				$fields = $checkout->get_checkout_fields( 'shipping' );

				foreach ( $fields as $key => $field ) {
					woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
				}
				?>
			</div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>
		</div>

	<?php endif; ?>
</div>
