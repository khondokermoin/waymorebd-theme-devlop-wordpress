<?php
/**
 * My Account navigation — Way More BD
 * Sidebar with Heroicons and a solid brand-orange active state.
 * Collapses into a toggle panel on mobile (Alpine).
 *
 * Uses wc_get_account_menu_items() so plugin-added endpoints still appear.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/myaccount/navigation.php
 *
 * @package isdb-custom
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

// Heroicons v2 (outline, MIT) keyed by endpoint.
$icons = array(
	'dashboard'       => 'M2.25 12l8.955-8.955a1.125 1.125 0 011.59 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75',
	'orders'          => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
	'downloads'       => 'M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3',
	'edit-address'    => 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z',
	'edit-account'    => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
	'payment-methods' => 'M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z',
	'customer-logout' => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75',
);

$menu_items = wc_get_account_menu_items();

// Label for the mobile toggle: whichever item is currently active.
$current_label = __( 'Account Menu', 'isdb-custom' );
foreach ( $menu_items as $endpoint => $label ) {
	if ( wc_is_current_account_menu_item( $endpoint ) ) {
		$current_label = $label;
		break;
	}
}
?>
<nav class="woocommerce-MyAccount-navigation" x-data="{ open:false }">

	<!-- Mobile toggle -->
	<button type="button" @click="open = !open"
		class="flex w-full items-center justify-between rounded-card border border-[#e0e0e0] bg-white px-4 py-3 text-sm font-semibold text-brand-title lg:hidden">
		<span class="flex items-center gap-2">
			<svg class="h-5 w-5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
			<?php echo esc_html( $current_label ); ?>
		</span>
		<svg class="h-4 w-4 text-slate-400 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
	</button>

	<?php // Hidden on mobile until toggled; always visible from lg up. `!block` wins over `hidden`. ?>
	<ul class="mt-2 hidden space-y-1.5 overflow-hidden rounded-card border border-[#e0e0e0] bg-white p-2.5 lg:mt-0 lg:block"
		:class="{ '!block': open }">
		<?php foreach ( $menu_items as $endpoint => $label ) :
			$is_active = wc_is_current_account_menu_item( $endpoint );
			$path      = isset( $icons[ $endpoint ] ) ? $icons[ $endpoint ] : 'M8.25 4.5l7.5 7.5-7.5 7.5';
			$is_logout = ( 'customer-logout' === $endpoint );
			?>
			<li class="woocommerce-MyAccount-navigation-link woocommerce-MyAccount-navigation-link--<?php echo esc_attr( $endpoint ); ?> <?php echo $is_active ? 'is-active' : ''; ?> <?php echo $is_logout ? 'mt-2 border-t border-slate-100 pt-2.5' : ''; ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
					class="group flex items-center gap-3 rounded-card px-3.5 py-2.5 text-[13px] font-semibold transition duration-150
						<?php echo $is_active
							? 'bg-brand-primary text-white shadow-sm'
							: ( $is_logout ? 'text-rose-600 hover:bg-rose-50' : 'text-brand-title hover:bg-brand-soft hover:text-brand-primary' ); ?>">
					<svg class="h-5 w-5 flex-none transition <?php echo $is_active ? 'text-white' : ( $is_logout ? 'text-rose-500' : 'text-slate-400 group-hover:text-brand-primary' ); ?>"
						fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $path ); ?>"/>
					</svg>
					<span class="flex-1"><?php echo esc_html( $label ); ?></span>
					<?php if ( ! $is_logout ) : ?>
						<svg class="h-4 w-4 flex-none opacity-0 transition group-hover:opacity-100 <?php echo $is_active ? 'opacity-100' : ''; ?>" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
