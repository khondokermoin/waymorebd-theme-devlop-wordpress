<?php
/**
 * My Account dashboard — Way More BD
 * Welcome + stat tiles + one-click reorder + quick-action cards + recent orders.
 * Keeps the woocommerce_account_dashboard hook.
 *
 * All order reads go through wc_get_orders()/CRUD — HPOS-safe.
 *
 * Overrides: wp-content/plugins/woocommerce/templates/myaccount/dashboard.php
 *
 * @package isdb-custom
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$user    = wp_get_current_user();
$user_id = get_current_user_id();

$recent = function_exists( 'wc_get_orders' ) ? wc_get_orders( array(
	'customer' => $user_id,
	'limit'    => 5,
	'orderby'  => 'date',
	'order'    => 'DESC',
) ) : array();

// Totals for the stat tiles.
$all_orders   = function_exists( 'wc_get_orders' ) ? wc_get_orders( array(
	'customer' => $user_id,
	'limit'    => -1,
	'return'   => 'ids',
) ) : array();
$order_count  = is_array( $all_orders ) ? count( $all_orders ) : 0;

$processing = function_exists( 'wc_get_orders' ) ? wc_get_orders( array(
	'customer' => $user_id,
	'limit'    => -1,
	'return'   => 'ids',
	'status'   => array( 'wc-processing', 'wc-on-hold', 'wc-pending' ),
) ) : array();
$processing_count = is_array( $processing ) ? count( $processing ) : 0;

$lifetime = 0;
if ( function_exists( 'wc_get_customer_total_spent' ) ) {
	$lifetime = (float) wc_get_customer_total_spent( $user_id );
}

// Most recent actionable order -> reorder CTA.
$last_actionable = function_exists( 'wc_get_orders' ) ? wc_get_orders( array(
	'customer' => $user_id,
	'limit'    => 1,
	'orderby'  => 'date',
	'order'    => 'DESC',
	'status'   => array( 'wc-completed', 'wc-processing', 'wc-on-hold' ),
) ) : array();
$last = ! empty( $last_actionable ) ? $last_actionable[0] : null;
?>

<h2 class="!mb-1 text-lg font-bold text-brand-title">Dashboard</h2>
<p class="m-0 text-[13px] text-slate-500">Here's what's happening with your account.</p>

<!-- Stat tiles -->
<div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-3">
	<?php
	$tiles = array(
		array(
			'label' => 'Total Orders',
			'value' => number_format_i18n( $order_count ),
			'icon'  => 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zM3.75 12h.007v.008H3.75V12zm0 5.25h.007v.008H3.75v-.008z',
			'tone'  => 'text-brand-primary bg-brand-soft',
		),
		array(
			'label' => 'In Progress',
			'value' => number_format_i18n( $processing_count ),
			'icon'  => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
			'tone'  => 'text-amber-700 bg-amber-50',
		),
		array(
			'label' => 'Total Spent',
			'value' => wp_strip_all_tags( wc_price( $lifetime ) ),
			'icon'  => 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z',
			'tone'  => 'text-emerald-700 bg-emerald-50',
		),
	);
	foreach ( $tiles as $t ) : ?>
		<div class="rounded-card border border-[#e0e0e0] bg-white p-4 transition hover:shadow-[3px_3px_8px_rgba(0,0,0,0.10)]">
			<span class="flex h-9 w-9 items-center justify-center rounded-card <?php echo esc_attr( $t['tone'] ); ?>">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $t['icon'] ); ?>"/></svg>
			</span>
			<p class="m-0 mt-2.5 text-xl font-extrabold leading-tight text-brand-title"><?php echo esc_html( $t['value'] ); ?></p>
			<p class="m-0 text-[12px] text-slate-500"><?php echo esc_html( $t['label'] ); ?></p>
		</div>
	<?php endforeach; ?>
</div>

<!-- One-click reorder -->
<?php if ( $last ) :
	$reorder_url = wp_nonce_url(
		add_query_arg( 'order_again', $last->get_id(), wc_get_cart_url() ),
		'woocommerce-order_again'
	); ?>
	<div class="mt-4 flex flex-col items-start justify-between gap-3 rounded-card p-4 sm:flex-row sm:items-center"
		style="background:linear-gradient(135deg, rgba(244,135,33,.12), rgba(244,135,33,.04));">
		<div>
			<p class="m-0 text-sm font-bold text-brand-title">Order again in one click</p>
			<p class="m-0 text-[12px] text-slate-500">
				Last order <span class="font-semibold text-brand-title">#<?php echo esc_html( $last->get_order_number() ); ?></span>
				&middot; <?php echo wp_kses_post( $last->get_formatted_order_total() ); ?>
			</p>
		</div>
		<a href="<?php echo esc_url( $reorder_url ); ?>"
			class="inline-flex flex-none items-center gap-1.5 rounded-card bg-brand-primary px-5 py-2.5 text-[13px] font-bold text-white transition hover:bg-brand-hover">
			Reorder
			<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
		</a>
	</div>
<?php endif; ?>

<!-- Recent orders -->
<div class="mt-6">
	<div class="mb-3 flex items-center justify-between border-b border-[#ddd] pb-2.5">
		<h3 class="m-0 text-base font-bold text-brand-title">Recent Orders</h3>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="text-[12px] font-semibold uppercase tracking-wide text-brand-primary transition hover:underline">View all</a>
	</div>

	<?php if ( ! empty( $recent ) ) : ?>
		<div class="space-y-2.5">
			<?php foreach ( $recent as $o ) :
				$status = $o->get_status();
				$tone   = 'bg-slate-100 text-slate-600';
				if ( in_array( $status, array( 'completed' ), true ) ) {
					$tone = 'bg-emerald-50 text-emerald-700';
				} elseif ( in_array( $status, array( 'processing', 'on-hold', 'pending' ), true ) ) {
					$tone = 'bg-amber-50 text-amber-700';
				} elseif ( in_array( $status, array( 'cancelled', 'failed', 'refunded' ), true ) ) {
					$tone = 'bg-rose-50 text-rose-700';
				}
				?>
				<a href="<?php echo esc_url( $o->get_view_order_url() ); ?>"
					class="flex flex-wrap items-center gap-x-4 gap-y-2 rounded-card border border-[#e0e0e0] bg-white p-3.5 transition hover:border-brand-primary hover:shadow-[3px_3px_8px_rgba(0,0,0,0.10)]">
					<span class="flex h-10 w-10 flex-none items-center justify-center rounded-card bg-brand-soft text-brand-primary">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
					</span>
					<span class="min-w-0 flex-1">
						<span class="block text-[13px] font-bold text-brand-title">#<?php echo esc_html( $o->get_order_number() ); ?></span>
						<span class="block text-[12px] text-slate-500"><?php echo esc_html( wc_format_datetime( $o->get_date_created() ) ); ?> &middot; <?php echo esc_html( $o->get_item_count() ); ?> item(s)</span>
					</span>
					<span class="rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize <?php echo esc_attr( $tone ); ?>"><?php echo esc_html( wc_get_order_status_name( $status ) ); ?></span>
					<span class="text-sm font-bold text-brand-primary"><?php echo wp_kses_post( $o->get_formatted_order_total() ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="rounded-card border border-dashed border-[#ddd] bg-brand-bg py-10 text-center">
			<p class="m-0 text-sm font-semibold text-brand-title">No orders yet</p>
			<p class="m-0 mt-1 text-[12px] text-slate-500">Your orders will appear here once you place one.</p>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="mt-4 inline-block rounded-card bg-brand-primary px-5 py-2.5 text-[13px] font-bold text-white transition hover:bg-brand-hover">Start Shopping</a>
		</div>
	<?php endif; ?>
</div>

<!-- Quick actions -->
<div class="mt-6">
	<h3 class="mb-3 border-b border-[#ddd] pb-2.5 text-base font-bold text-brand-title">Quick Actions</h3>
	<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
		<?php
		$actions = array(
			array( 'orders', 'My Orders', 'Track &amp; review your order history', 'M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zM3.75 12h.007v.008H3.75V12zm0 5.25h.007v.008H3.75v-.008z' ),
			array( 'edit-address', 'Addresses', 'Manage billing &amp; delivery details', 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z' ),
			array( 'edit-account', 'Account Details', 'Name, email &amp; password', 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z' ),
			array( 'customer-logout', 'Log out', 'Sign out of your account securely', 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75' ),
		);
		foreach ( $actions as $a ) : ?>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $a[0] ) ); ?>"
				class="group flex items-center gap-3.5 rounded-card border border-[#e0e0e0] bg-white p-4 transition hover:border-brand-primary hover:shadow-[3px_3px_8px_rgba(0,0,0,0.10)]">
				<span class="flex h-11 w-11 flex-none items-center justify-center rounded-card bg-brand-bg text-slate-500 transition group-hover:bg-brand-soft group-hover:text-brand-primary">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $a[3] ); ?>"/></svg>
				</span>
				<span class="min-w-0 flex-1">
					<span class="block text-[13px] font-bold text-brand-title"><?php echo esc_html( $a[1] ); ?></span>
					<span class="block text-[12px] text-slate-500"><?php echo wp_kses_post( $a[2] ); ?></span>
				</span>
				<svg class="h-4 w-4 flex-none text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand-primary" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<?php
/** Plugins hook extra dashboard content here — keep it. */
do_action( 'woocommerce_account_dashboard' );
