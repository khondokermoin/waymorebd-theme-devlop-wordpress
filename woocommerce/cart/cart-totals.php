<?php
/**
 * Cart totals — Way More BD (cart/cart-totals.php)
 *
 * Order-summary with anchoring (total savings), goal-gradient free-shipping bar,
 * a single loud checkout CTA, and anxiety-reducing trust signals (SSL, COD,
 * returns). All wc_cart_totals_* helpers + the proceed-to-checkout hook kept.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/cart/cart-totals.php
 *
 * @package isdb-custom
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

$free_ship = function_exists( 'isdb_free_shipping_progress' ) ? isdb_free_shipping_progress() : null;

// Loss-aversion: total savings across the cart (regular − sale, × qty).
$cart_savings = 0.0;
foreach ( WC()->cart->get_cart() as $ci ) {
	$p = isset( $ci['data'] ) ? $ci['data'] : null;
	if ( $p instanceof WC_Product ) {
		$reg = (float) $p->get_regular_price();
		$cur = (float) $p->get_price();
		if ( $reg > $cur && $cur > 0 ) {
			$cart_savings += ( $reg - $cur ) * (int) $ci['quantity'];
		}
	}
}
?>
<div class="cart_totals rounded-2xl bg-white p-6 ring-1 ring-slate-100 <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="text-lg font-bold text-brand-title"><?php esc_html_e( 'Order Summary', 'isdb-custom' ); ?></h2>

	<?php // GOAL-GRADIENT: only if a real free-shipping threshold is configured. ?>
	<?php if ( $free_ship ) : ?>
		<div class="mt-4 rounded-xl bg-brand-bg p-3">
			<?php if ( $free_ship['reached'] ) : ?>
				<p class="text-sm font-semibold text-emerald-700">✓ <?php esc_html_e( 'You\'ve unlocked FREE shipping!', 'isdb-custom' ); ?></p>
			<?php else : ?>
				<p class="text-sm text-brand-body"><?php
					printf(
						/* translators: %s: remaining amount until free shipping */
						esc_html__( 'You\'re %s away from FREE shipping', 'isdb-custom' ),
						'<span class="font-bold text-brand-title">' . wp_kses_post( $free_ship['remaining_html'] ) . '</span>'
					);
				?></p>
			<?php endif; ?>
			<div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200">
				<div class="h-full rounded-full bg-brand-primary transition-all" style="width: <?php echo esc_attr( $free_ship['pct'] ); ?>%"></div>
			</div>
		</div>
	<?php endif; ?>

	<div class="mt-4 space-y-3 text-sm">
		<div class="flex items-center justify-between">
			<span class="text-slate-500"><?php esc_html_e( 'Subtotal', 'isdb-custom' ); ?></span>
			<span class="font-semibold text-brand-title"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="flex items-center justify-between text-emerald-700 cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
			<div class="[&_.woocommerce-shipping-methods]:space-y-1 [&_label]:text-slate-600 [&_.shipping-calculator-button]:text-brand-primary [&_.shipping-calculator-button]:underline">
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
			<span class="text-base font-bold text-brand-title"><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
			<span class="text-xl font-extrabold text-brand-primary"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
	</div>

	<?php // Loss-aversion reinforcement — the win they'd lose by abandoning. ?>
	<?php if ( $cart_savings > 0 ) : ?>
		<div class="mt-3 flex items-center justify-between rounded-xl bg-emerald-50 px-3.5 py-2.5">
			<span class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-700">
				<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0l-3.5-3.5a1 1 0 011.4-1.4l2.8 2.8 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
				<?php esc_html_e( 'You\'re saving', 'isdb-custom' ); ?>
			</span>
			<span class="text-sm font-extrabold text-emerald-700"><?php echo wp_kses_post( wc_price( $cart_savings ) ); ?></span>
		</div>
	<?php endif; ?>

	<!-- Proceed to checkout (single loud orange CTA) -->
	<div class="wc-proceed-to-checkout mt-5 [&_.checkout-button]:flex [&_.checkout-button]:w-full [&_.checkout-button]:items-center [&_.checkout-button]:justify-center [&_.checkout-button]:gap-2 [&_.checkout-button]:rounded-xl [&_.checkout-button]:bg-brand-primary [&_.checkout-button]:px-6 [&_.checkout-button]:py-4 [&_.checkout-button]:text-center [&_.checkout-button]:text-base [&_.checkout-button]:font-extrabold [&_.checkout-button]:uppercase [&_.checkout-button]:tracking-wide [&_.checkout-button]:text-white [&_.checkout-button]:shadow-lg [&_.checkout-button]:shadow-brand-primary/30 [&_.checkout-button:hover]:bg-brand-hover">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<!-- Anxiety reducers: COD + secure + returns -->
	<div class="mt-4 space-y-2.5">
		<p class="flex items-center gap-2 text-[13px] font-medium text-brand-body">
			<svg class="h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
			<span><?php
				printf(
					/* translators: %s: bold "No payment now" phrase */
					esc_html__( '%s — pay Cash on Delivery.', 'isdb-custom' ),
					'<strong class="font-bold text-brand-title">' . esc_html__( 'No payment now', 'isdb-custom' ) . '</strong>'
				);
			?></span>
		</p>
		<p class="flex items-center gap-2 text-[13px] font-medium text-brand-body">
			<svg class="h-4 w-4 flex-none text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
			<span><?php esc_html_e( '256-bit SSL secure checkout.', 'isdb-custom' ); ?></span>
		</p>
		<p class="flex items-center gap-2 text-[13px] font-medium text-brand-body">
			<svg class="h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
			<span><?php esc_html_e( 'Easy 7-day returns & replacements.', 'isdb-custom' ); ?></span>
		</p>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>
</div>
