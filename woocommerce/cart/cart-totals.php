<?php
/**
 * Cart totals — Way More BD (cart/cart-totals.php)
 * Order-summary anchoring + free-shipping goal bar + proceed-to-checkout.
 * Keeps all wc_cart_totals_* helpers and the proceed-to-checkout hook.
 * Overrides: wp-content/plugins/woocommerce/templates/cart/cart-totals.php
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

$free_ship = function_exists( 'isdb_free_shipping_progress' ) ? isdb_free_shipping_progress() : null;
?>
<div class="cart_totals rounded-2xl bg-white p-6 ring-1 ring-slate-100 <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="text-lg font-bold text-slate-900">Order Summary</h2>

	<?php // GOAL-GRADIENT: only if a real free-shipping threshold is configured. ?>
	<?php if ( $free_ship ) : ?>
		<div class="mt-4 rounded-xl bg-stone-50 p-3">
			<?php if ( $free_ship['reached'] ) : ?>
				<p class="text-sm font-semibold text-emerald-700">✓ You've unlocked FREE shipping!</p>
			<?php else : ?>
				<p class="text-sm text-slate-600">You're <span class="font-bold text-slate-900"><?php echo wp_kses_post( $free_ship['remaining_html'] ); ?></span> from free shipping</p>
			<?php endif; ?>
			<div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-stone-200">
				<div class="h-full rounded-full bg-emerald-500 transition-all" style="width: <?php echo esc_attr( $free_ship['pct'] ); ?>%"></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="mt-4 space-y-3 text-sm">
		<div class="flex items-center justify-between">
			<span class="text-slate-500">Subtotal</span>
			<span class="font-semibold text-slate-900"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="flex items-center justify-between text-emerald-700 cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
			<div class="[&_.woocommerce-shipping-methods]:space-y-1 [&_label]:text-slate-600 [&_.shipping-calculator-button]:text-amber-600 [&_.shipping-calculator-button]:underline">
				<?php wc_cart_totals_shipping_html(); ?>
			</div>
			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>
			<div class="shipping flex items-center justify-between">
				<span class="text-slate-500"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
				<span><?php woocommerce_shipping_calculator(); ?></span>
			</div>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="flex items-center justify-between fee">
				<span class="text-slate-500"><?php echo esc_html( $fee->name ); ?></span>
				<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';
			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
					?>
					<div class="flex items-center justify-between tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span class="text-slate-500"><?php echo esc_html( $tax->label ) . wp_kses_post( $estimated_text ); ?></span>
						<span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
					<?php
				}
			} else {
				?>
				<div class="flex items-center justify-between tax-total">
					<span class="text-slate-500"><?php echo esc_html( WC()->countries->tax_or_vat() ) . wp_kses_post( $estimated_text ); ?></span>
					<span><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<div class="flex items-center justify-between border-t border-slate-100 pt-3 order-total">
			<span class="text-base font-bold text-slate-900"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
			<span class="text-xl font-extrabold text-slate-900"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
	</div>

	<!-- Proceed to checkout (amber CTA via arbitrary-variant styling of WC's button) -->
	<div class="wc-proceed-to-checkout mt-6 [&_.checkout-button]:block [&_.checkout-button]:w-full [&_.checkout-button]:rounded-xl [&_.checkout-button]:bg-amber-500 [&_.checkout-button]:px-6 [&_.checkout-button]:py-4 [&_.checkout-button]:text-center [&_.checkout-button]:text-base [&_.checkout-button]:font-bold [&_.checkout-button]:text-slate-900 [&_.checkout-button]:shadow-lg [&_.checkout-button]:shadow-amber-500/30 [&_.checkout-button:hover]:bg-amber-400">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>
</div>
