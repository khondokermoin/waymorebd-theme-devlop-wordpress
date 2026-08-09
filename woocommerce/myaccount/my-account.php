<?php
/**
 * My Account wrapper — Way More BD
 * Sidebar navigation (left) + endpoint content (right).
 * Keeps woocommerce_account_navigation / woocommerce_account_content intact.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/myaccount/my-account.php
 *
 * @package isdb-custom
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();
?>
<div class="wmb-account">

	<!-- Account header -->
	<div class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-card border border-[#e0e0e0] bg-white p-5">
		<div class="flex items-center gap-3.5">
			<span class="flex h-12 w-12 flex-none items-center justify-center rounded-full bg-brand-primary text-lg font-black text-white">
				<?php echo esc_html( mb_strtoupper( mb_substr( $user->display_name, 0, 1 ) ) ); ?>
			</span>
			<div>
				<p class="m-0 text-base font-bold text-brand-title">Hello, <?php echo esc_html( $user->display_name ); ?></p>
				<p class="m-0 text-[13px] text-slate-500"><?php echo esc_html( $user->user_email ); ?></p>
			</div>
		</div>
		<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"
			class="inline-flex items-center gap-1.5 rounded-card bg-brand-primary px-4 py-2.5 text-[13px] font-semibold text-white transition hover:bg-brand-hover">
			<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
			Continue Shopping
		</a>
	</div>

	<div class="lg:grid lg:grid-cols-[260px,1fr] lg:items-start lg:gap-5">
		<?php
		/** Renders myaccount/navigation.php */
		do_action( 'woocommerce_account_navigation' );
		?>

		<div class="woocommerce-MyAccount-content mt-5 rounded-card border border-[#e0e0e0] bg-white p-5 sm:p-6 lg:mt-0">
			<?php
			/** Endpoint content (dashboard, orders, addresses, …) */
			do_action( 'woocommerce_account_content' );
			?>
		</div>
	</div>
</div>
