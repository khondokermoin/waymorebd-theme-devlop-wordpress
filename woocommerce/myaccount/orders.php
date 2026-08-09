<?php
/**
 * My Account → Orders — Way More BD
 * Card list instead of the default table: order no. + date + status pill +
 * item thumbnails + total + actions. Pagination preserved.
 *
 * ── LOAD-BEARING ─────────────────────────────────────────────────────────
 *   do_action('woocommerce_before_account_orders', $has_orders)
 *   do_action('woocommerce_before_account_orders_pagination')
 *   do_action('woocommerce_after_account_orders', $has_orders)
 *   wc_get_account_orders_actions( $order ) — supplies Pay / View / Cancel
 *     links WITH their nonces; never hand-build those URLs.
 *   $customer_orders comes from WC_Shortcode_My_Account (HPOS-safe).
 *
 * Overrides: wp-content/plugins/woocommerce/templates/myaccount/orders.php
 *
 * @package isdb-custom
 * @version 9.5.0
 * @var WP_Query $customer_orders
 * @var bool     $has_orders
 * @var int      $current_page
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<h2 class="!mb-1 text-lg font-bold text-brand-title"><?php esc_html_e( 'Orders', 'woocommerce' ); ?></h2>
<p class="m-0 text-[13px] text-slate-500">Track, review and reorder your purchases.</p>

<?php if ( $has_orders ) : ?>

	<div class="woocommerce-orders-table mt-5 space-y-3">
		<?php
		foreach ( $customer_orders->orders as $customer_order ) {
			$order        = wc_get_order( $customer_order );
			if ( ! $order ) {
				continue;
			}
			$item_count   = $order->get_item_count() - $order->get_item_count_refunded();
			$status       = $order->get_status();
			$actions      = wc_get_account_orders_actions( $order );

			$tone = 'bg-slate-100 text-slate-600';
			if ( 'completed' === $status ) {
				$tone = 'bg-emerald-50 text-emerald-700';
			} elseif ( in_array( $status, array( 'processing', 'on-hold', 'pending' ), true ) ) {
				$tone = 'bg-amber-50 text-amber-700';
			} elseif ( in_array( $status, array( 'cancelled', 'failed', 'refunded' ), true ) ) {
				$tone = 'bg-rose-50 text-rose-700';
			}
			?>
			<div class="woocommerce-orders-table__row order rounded-card border border-[#e0e0e0] bg-white p-4 transition hover:border-brand-primary hover:shadow-[3px_3px_8px_rgba(0,0,0,0.10)]">

				<!-- Head -->
				<div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-3">
					<div class="min-w-0">
						<div class="flex flex-wrap items-center gap-2">
							<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="text-[15px] font-extrabold text-brand-title transition hover:text-brand-primary">
								#<?php echo esc_html( $order->get_order_number() ); ?>
							</a>
							<span class="rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize <?php echo esc_attr( $tone ); ?>">
								<?php echo esc_html( wc_get_order_status_name( $status ) ); ?>
							</span>
						</div>
						<p class="m-0 mt-1 text-[12px] text-slate-500">
							<?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?>
							&middot;
							<?php
							/* translators: %d: item count */
							printf( esc_html( _n( '%d item', '%d items', $item_count, 'woocommerce' ) ), (int) $item_count );
							?>
						</p>
					</div>

					<div class="text-right">
						<p class="m-0 text-[11px] uppercase tracking-wide text-slate-400">Total</p>
						<p class="m-0 text-lg font-extrabold text-brand-primary"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></p>
					</div>
				</div>

				<!-- Item thumbnails -->
				<?php
				$thumbs = array();
				foreach ( $order->get_items() as $item ) {
					$p = $item->get_product();
					if ( $p ) {
						$thumbs[] = $p;
					}
					if ( count( $thumbs ) >= 5 ) {
						break;
					}
				}
				if ( $thumbs ) : ?>
					<div class="flex flex-wrap items-center gap-2 py-3">
						<?php foreach ( $thumbs as $tp ) : ?>
							<a href="<?php echo esc_url( $tp->get_permalink() ); ?>" title="<?php echo esc_attr( $tp->get_name() ); ?>"
								class="flex h-12 w-12 flex-none items-center justify-center overflow-hidden rounded border border-[#e0e0e0] bg-white transition hover:border-brand-primary [&_img]:h-full [&_img]:w-full [&_img]:object-contain">
								<?php echo wp_kses_post( $tp->get_image( 'woocommerce_gallery_thumbnail' ) ); ?>
							</a>
						<?php endforeach; ?>
						<?php if ( $item_count > count( $thumbs ) ) : ?>
							<span class="flex h-12 w-12 flex-none items-center justify-center rounded border border-dashed border-[#ddd] text-[12px] font-semibold text-slate-500">
								+<?php echo esc_html( $item_count - count( $thumbs ) ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Actions (nonced by WooCommerce) -->
				<?php if ( ! empty( $actions ) ) : ?>
					<div class="flex flex-wrap gap-2 pt-1">
						<?php
						foreach ( $actions as $key => $action ) {
							$is_primary = in_array( $key, array( 'pay', 'view' ), true );
							$is_danger  = ( 'cancel' === $key );
							$cls = $is_primary
								? 'bg-brand-primary text-white hover:bg-brand-hover'
								: ( $is_danger
									? 'border border-rose-200 text-rose-600 hover:bg-rose-50'
									: 'border border-[#ddd] text-brand-title hover:bg-brand-bg' );
							printf(
								'<a href="%s" class="inline-flex items-center rounded-card px-4 py-2 text-[12px] font-semibold transition %s">%s</a>',
								esc_url( $action['url'] ),
								esc_attr( $cls ),
								esc_html( $action['name'] )
							);
						}
						?>

						<?php // Reorder — uses WooCommerce's own order_again handler + nonce. ?>
						<?php if ( in_array( $status, array( 'completed', 'processing', 'on-hold' ), true ) ) : ?>
							<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'order_again', $order->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' ) ); ?>"
								class="inline-flex items-center gap-1.5 rounded-card border border-brand-primary px-4 py-2 text-[12px] font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white">
								<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992V4.356m-.001 9.7A8.25 8.25 0 116.638 5.68"/></svg>
								Reorder
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php
		}
		?>
	</div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination mt-6 flex items-center justify-between gap-3">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button button inline-flex items-center gap-1.5 rounded-card border border-[#ddd] px-4 py-2.5 text-[13px] font-semibold text-brand-title transition hover:border-brand-primary hover:text-brand-primary" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
					<?php esc_html_e( 'Previous', 'woocommerce' ); ?>
				</a>
			<?php else : ?>
				<span></span>
			<?php endif; ?>

			<span class="text-[12px] text-slate-500">
				Page <?php echo esc_html( $current_page ); ?> of <?php echo esc_html( $customer_orders->max_num_pages ); ?>
			</span>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button button inline-flex items-center gap-1.5 rounded-card border border-[#ddd] px-4 py-2.5 text-[13px] font-semibold text-brand-title transition hover:border-brand-primary hover:text-brand-primary" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">
					<?php esc_html_e( 'Next', 'woocommerce' ); ?>
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
				</a>
			<?php else : ?>
				<span></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info mt-5 rounded-card border border-dashed border-[#ddd] bg-brand-bg py-12 text-center">
		<span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-white text-slate-400">
			<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
		</span>
		<p class="m-0 mt-4 text-base font-bold text-brand-title"><?php esc_html_e( 'No order has been made yet.', 'woocommerce' ); ?></p>
		<p class="m-0 mt-1 text-[13px] text-slate-500">When you place an order it'll show up here.</p>
		<a class="woocommerce-Button button mt-5 inline-block rounded-card bg-brand-primary px-6 py-3 text-[13px] font-bold text-white transition hover:bg-brand-hover" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>">
			<?php esc_html_e( 'Browse products', 'woocommerce' ); ?>
		</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
