<?php
/**
 * Template Name: Order Tracking
 *
 * Standalone public order tracker — Way More BD.
 * A visitor enters their order number + the phone/email used on the order and
 * sees the same tracker shown on the thank-you page (timeline, products,
 * summary, shipping). Lookup + ownership check: isdb_find_trackable_order().
 *
 * Set up: create a Page titled "Track Order" (slug: track-order) and choose the
 * "Order Tracking" template. isdb_track_order_url() points the header link here.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$req_number  = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';
$req_contact = isset( $_GET['contact'] ) ? sanitize_text_field( wp_unslash( $_GET['contact'] ) ) : '';

if ( '' === $req_number ) {
	$result = null;
} elseif ( ! isdb_track_lookup_allowed() ) {
	$result = array( 'order' => null, 'error' => 'Too many attempts. Please try again in a few minutes.' );
} else {
	$result = isdb_find_trackable_order( $req_number, $req_contact );
}
$order  = ( $result && $result['order'] instanceof WC_Order ) ? $result['order'] : null;
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- Breadcrumb strip (matches page.php, bg = site background) -->
	<section class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 py-2.5 sm:px-6 lg:px-8">
			<nav class="text-[13px] text-slate-500" aria-label="Breadcrumb">
				<div class="flex flex-wrap items-center">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand-primary">Home</a>
					<span class="mx-2 text-slate-300">/</span>
					<span class="text-brand-title">Track Order</span>
				</div>
			</nav>
		</div>
	</section>

	<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">

		<!-- ═══════════ HERO + SEARCH ═══════════ -->
		<div class="grid items-center gap-6 rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 lg:grid-cols-[1fr,minmax(300px,380px)]">
			<div>
				<span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.14em] text-brand-primary">
					<span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span> Live Order Tracking
				</span>
				<h1 class="mt-3 text-2xl font-extrabold tracking-tight text-brand-title sm:text-3xl">Track Your Order</h1>
				<p class="mt-2 text-sm text-slate-500">Real-time updates on your shipment progress. Enter the order number and the phone number used at checkout.</p>
				<?php if ( $order ) : ?>
					<p class="mt-4 text-sm text-slate-500">Order Number</p>
					<p class="text-lg font-extrabold text-brand-primary">#<?php echo esc_html( $order->get_order_number() ); ?></p>
				<?php endif; ?>
			</div>

			<form method="get" class="space-y-3 rounded-xl bg-brand-bg p-4">
				<div>
					<label for="track-order-no" class="mb-1 block text-xs font-semibold text-brand-title">Order Number</label>
					<input id="track-order-no" type="text" name="order" value="<?php echo esc_attr( $req_number ); ?>" required
						placeholder="e.g. 12345"
						class="w-full rounded-lg border border-brand-line bg-white px-3.5 py-2.5 text-sm text-brand-title placeholder:text-slate-400 focus:border-brand-primary focus:outline-none focus:ring-0" />
				</div>
				<div>
					<label for="track-contact" class="mb-1 block text-xs font-semibold text-brand-title">Phone or Email <span class="font-normal text-slate-400">(used on the order)</span></label>
					<input id="track-contact" type="text" name="contact" value="<?php echo esc_attr( $req_contact ); ?>"
						placeholder="01XXXXXXXXX"
						class="w-full rounded-lg border border-brand-line bg-white px-3.5 py-2.5 text-sm text-brand-title placeholder:text-slate-400 focus:border-brand-primary focus:outline-none focus:ring-0" />
				</div>
				<button type="submit" class="w-full rounded-lg bg-brand-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-hover">
					Track Order
				</button>
			</form>
		</div>

		<?php if ( $result && ! $order ) : ?>
			<!-- Lookup error -->
			<div class="mx-auto mt-6 flex max-w-3xl items-start gap-3 rounded-2xl border border-rose-100 bg-rose-50 p-5">
				<svg class="h-5 w-5 flex-none text-rose-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
				<p class="text-sm font-medium text-rose-700"><?php echo esc_html( $result['error'] ); ?></p>
			</div>
		<?php elseif ( $order ) : ?>
			<!-- ═══════════ TRACKER BODY (shared) ═══════════ -->
			<div class="mt-6">
				<?php isdb_render_order_tracker( $order ); ?>
			</div>
		<?php else : ?>
			<!-- Empty state / instructions -->
			<div class="mx-auto mt-6 max-w-3xl rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
				<span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-primary/10 text-brand-primary">
					<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
				</span>
				<h2 class="mt-4 text-base font-bold text-brand-title">Enter your order details above</h2>
				<p class="mx-auto mt-1 max-w-md text-sm text-slate-500">You'll find the order number in your confirmation on the thank-you page or in your email. <?php if ( is_user_logged_in() ) : ?>Or view all your orders in <a class="font-semibold text-brand-primary hover:underline" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">My Account</a>.<?php endif; ?></p>
			</div>
		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
