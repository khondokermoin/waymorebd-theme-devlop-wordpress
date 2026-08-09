<?php
/**
 * Shop & Product archives — Way More BD (archive-product.php)
 * Filter rail (paradox-of-choice control) + sort bar + product grid.
 * Overrides: wp-content/plugins/woocommerce/templates/archive-product.php
 *
 * Filtering logic lives in functions.php (isdb_apply_shop_filters).
 * Sorting (?orderby=) is handled by WooCommerce core.
 *
 * @package isdb-custom
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

global $wp_query, $wp;

$term        = ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ? get_queried_object() : null;
$page_title  = woocommerce_page_title( false );
$description  = $term && ! empty( $term->description ) ? $term->description : get_the_archive_description();
$form_action = esc_url( strtok( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', '?' ) );
$clear_url   = $form_action;

// Active filter state (for pre-checking the UI).
$sel_cats  = isset( $_GET['filter_cat'] ) ? array_map( 'sanitize_title', (array) $_GET['filter_cat'] ) : array();
$sel_min   = isset( $_GET['min_price'] ) ? esc_attr( wp_unslash( $_GET['min_price'] ) ) : '';
$sel_max   = isset( $_GET['max_price'] ) ? esc_attr( wp_unslash( $_GET['max_price'] ) ) : '';
$sel_rate  = isset( $_GET['rating'] ) ? (int) $_GET['rating'] : 0;
$sel_stock = ! empty( $_GET['instock'] );
$sel_sale  = ! empty( $_GET['on_sale'] );
$has_filters = $sel_cats || '' !== $sel_min || '' !== $sel_max || $sel_rate || $sel_stock || $sel_sale;

// Contextual heading for the theme's canonical shop views (New Arrivals / Deals).
if ( function_exists( 'is_shop' ) && is_shop() ) {
	if ( $sel_sale ) {
		$page_title = __( 'Deals & Offers', 'isdb-custom' );
	} elseif ( isset( $_GET['orderby'] ) && 'date' === $_GET['orderby'] ) {
		$page_title = __( 'New Arrivals', 'isdb-custom' );
	}
}

// Category list for the filter rail.
$filter_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'orderby' => 'name' ) );
$filter_cats = is_wp_error( $filter_cats ) ? array() : $filter_cats;

// Price-slider bounds (store-wide) from WooCommerce's product lookup table.
global $wpdb;
$price_bounds = $wpdb->get_row( "SELECT MIN(min_price) AS lo, MAX(max_price) AS hi FROM {$wpdb->prefix}wc_product_meta_lookup" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.NotPrepared
$price_floor = ( $price_bounds && null !== $price_bounds->lo ) ? (int) floor( (float) $price_bounds->lo ) : 0;
$price_ceil  = ( $price_bounds && null !== $price_bounds->hi ) ? (int) ceil( (float) $price_bounds->hi ) : 1000;
if ( $price_ceil <= $price_floor ) {
	$price_ceil = $price_floor + 100;
}
$price_lo = ( '' !== $sel_min ) ? max( $price_floor, (int) $sel_min ) : $price_floor;
$price_hi = ( '' !== $sel_max ) ? min( $price_ceil, (int) $sel_max ) : $price_ceil;

// Sort options (standard WooCommerce catalog ordering).
$orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
	'menu_order' => __( 'Default sorting', 'isdb-custom' ),
	'popularity' => __( 'Most popular', 'isdb-custom' ),
	'rating'     => __( 'Top rated', 'isdb-custom' ),
	'date'       => __( 'Newest', 'isdb-custom' ),
	'price'      => __( 'Price: low to high', 'isdb-custom' ),
	'price-desc' => __( 'Price: high to low', 'isdb-custom' ),
) );
$current_orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : get_option( 'woocommerce_default_catalog_orderby', 'menu_order' );

$total   = (int) $wp_query->found_posts;
$orderby_hidden = esc_attr( $current_orderby );
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- Shop banner (title left · breadcrumb right, on every archive) -->
	<section class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3.5">
			<div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
				<h1 class="text-xl font-bold tracking-tight text-brand-title sm:text-2xl"><?php echo esc_html( $page_title ); ?></h1>
				<nav class="text-[13px] text-slate-500" aria-label="Breadcrumb">
					<?php woocommerce_breadcrumb( array(
						'delimiter'   => '<span class="mx-2 text-slate-300">/</span>',
						'wrap_before' => '<div class="flex flex-wrap items-center">',
						'wrap_after'  => '</div>',
					) ); ?>
				</nav>
			</div>
			<?php // Real category descriptions show below; the generic Shop placeholder is hidden. ?>
			<?php if ( $description && ( ! function_exists( 'is_shop' ) || ! is_shop() ) ) : ?>
				<div class="mt-1.5 max-w-2xl text-[13px] text-slate-500"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
			<?php endif; ?>
		</div>
	</section>

	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6" x-data="{ filters:false }">
		<div class="lg:grid lg:grid-cols-[280px,1fr] lg:gap-10">

			<!-- ============================================================ -->
			<!-- FILTER RAIL · user narrows choice at their own pace          -->
			<!-- ============================================================ -->
			<aside :class="filters ? 'fixed inset-0 z-[60] overflow-y-auto bg-white p-5 lg:static lg:z-auto lg:bg-transparent lg:p-0' : 'hidden lg:block'">
				<div class="mb-4 flex items-center justify-between lg:hidden">
					<span class="text-lg font-bold">Filters</span>
					<button @click="filters=false" class="rounded-lg p-1.5 text-slate-400 hover:bg-stone-100" aria-label="Close filters">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
					</button>
				</div>

				<form method="get" action="<?php echo $form_action; ?>" class="space-y-7">
					<input type="hidden" name="orderby" value="<?php echo $orderby_hidden; ?>" />

					<!-- Categories -->
					<?php if ( ! empty( $filter_cats ) ) : ?>
						<fieldset class="rounded-card border border-[#e0e0e0] bg-white p-4">
							<legend class="mb-1 border-b-2 border-brand-primary pb-1 text-[13px] font-bold uppercase tracking-wide text-brand-title">Filter by Category</legend>
							<div class="mt-3 space-y-2">
								<?php foreach ( $filter_cats as $fc ) : ?>
									<label class="flex items-center gap-2.5 text-sm text-slate-600">
										<input type="checkbox" name="filter_cat[]" value="<?php echo esc_attr( $fc->slug ); ?>" <?php checked( in_array( $fc->slug, $sel_cats, true ) ); ?>
											class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500" />
										<span class="flex-1"><?php echo esc_html( $fc->name ); ?></span>
										<span class="text-xs text-slate-400"><?php echo esc_html( $fc->count ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</fieldset>
					<?php endif; ?>

					<!-- Price (dual-handle range slider) -->
					<fieldset class="rounded-card border border-[#e0e0e0] bg-white p-4">
						<legend class="mb-1 border-b-2 border-brand-primary pb-1 text-[13px] font-bold uppercase tracking-wide text-brand-title">Price Range</legend>
						<div class="wmb-range mt-4"
							x-data="{
								floor: <?php echo (int) $price_floor; ?>, ceil: <?php echo (int) $price_ceil; ?>,
								lo: <?php echo (int) $price_lo; ?>, hi: <?php echo (int) $price_hi; ?>,
								pct(v){ return this.ceil > this.floor ? ((v - this.floor) / (this.ceil - this.floor)) * 100 : 0; },
								fill(){ var a = this.pct(this.lo), b = this.pct(this.hi); return 'left:' + a + '%; width:' + (b - a) + '%'; },
								fixLo(){ if (this.lo > this.hi) { this.lo = this.hi; } },
								fixHi(){ if (this.hi < this.lo) { this.hi = this.lo; } }
							}">
							<div class="mb-3 flex items-center justify-between text-sm font-bold text-brand-title">
								<span>&#2547;<span x-text="lo"></span></span>
								<span>&#2547;<span x-text="hi"></span></span>
							</div>
							<div class="wmb-range-body relative h-5">
								<div class="absolute left-0 right-0 top-1/2 h-1.5 -translate-y-1/2 rounded-full bg-slate-200"></div>
								<div class="absolute top-1/2 h-1.5 -translate-y-1/2 rounded-full bg-brand-primary" :style="fill()"></div>
								<input type="range" :min="floor" :max="ceil" step="1" x-model.number="lo" @input="fixLo()" aria-label="Minimum price" />
								<input type="range" :min="floor" :max="ceil" step="1" x-model.number="hi" @input="fixHi()" aria-label="Maximum price" />
							</div>
							<input type="hidden" name="min_price" :value="lo" />
							<input type="hidden" name="max_price" :value="hi" />
						</div>
					</fieldset>

					<!-- Rating -->
					<fieldset class="rounded-card border border-[#e0e0e0] bg-white p-4">
						<legend class="mb-1 border-b-2 border-brand-primary pb-1 text-[13px] font-bold uppercase tracking-wide text-brand-title">Customer Rating</legend>
						<div class="mt-3 space-y-2">
							<?php foreach ( array( 4, 3, 2 ) as $r ) : ?>
								<label class="flex items-center gap-2.5 text-sm text-slate-600">
									<input type="radio" name="rating" value="<?php echo esc_attr( $r ); ?>" <?php checked( $sel_rate, $r ); ?> class="h-4 w-4 border-slate-300 text-amber-500 focus:ring-amber-500" />
									<span class="flex text-amber-400">
										<?php for ( $i = 0; $i < 5; $i++ ) : ?>
											<svg class="h-4 w-4 <?php echo $i < $r ? '' : 'text-slate-200'; ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.28 3.94a1 1 0 00.95.69h4.15c.97 0 1.37 1.24.59 1.81l-3.36 2.44a1 1 0 00-.36 1.12l1.28 3.94c.3.92-.75 1.69-1.54 1.12l-3.36-2.44a1 1 0 00-1.18 0l-3.36 2.44c-.78.57-1.83-.2-1.54-1.12l1.28-3.94a1 1 0 00-.36-1.12L2.33 9.37c-.78-.57-.38-1.81.59-1.81h4.15a1 1 0 00.95-.69l1.28-3.94z"/></svg>
										<?php endfor; ?>
									</span>
									<span class="text-xs text-slate-400">&amp; up</span>
								</label>
							<?php endforeach; ?>
						</div>
					</fieldset>

					<!-- Availability toggles -->
					<fieldset class="rounded-card border border-[#e0e0e0] bg-white p-4">
						<legend class="mb-1 border-b-2 border-brand-primary pb-1 text-[13px] font-bold uppercase tracking-wide text-brand-title">Product Flag</legend>
						<div class="mt-3 space-y-2">
							<label class="flex items-center gap-2.5 text-sm text-slate-600">
								<input type="checkbox" name="instock" value="1" <?php checked( $sel_stock ); ?> class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500" />
								In stock only
							</label>
							<label class="flex items-center gap-2.5 text-sm text-slate-600">
								<input type="checkbox" name="on_sale" value="1" <?php checked( $sel_sale ); ?> class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500" />
								On sale
							</label>
						</div>
					</fieldset>

					<div class="flex flex-col gap-2 border-t border-slate-100 pt-5">
						<button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-800">Apply Filters</button>
						<?php if ( $has_filters ) : ?>
							<a href="<?php echo esc_url( $clear_url ); ?>" class="rounded-xl px-6 py-2.5 text-center text-sm font-semibold text-slate-500 transition hover:bg-stone-100">Clear all</a>
						<?php endif; ?>
					</div>
				</form>
			</aside>

			<!-- ============================================================ -->
			<!-- PRODUCT GRID                                                  -->
			<!-- ============================================================ -->
			<div>
				<!-- Sort + mobile filter trigger (product count intentionally hidden) -->
				<div class="mb-6 flex items-center gap-3">
					<button @click="filters=true" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 lg:hidden">
						<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 6h18M6 12h12M10 18h4"/></svg>
						Filters
					</button>

					<div class="ml-auto flex items-center gap-2">
						<label class="sr-only" for="isdb-orderby">Sort by</label>
						<select id="isdb-orderby"
							onchange="var u=new URL(location);u.searchParams.set('orderby',this.value);u.searchParams.delete('paged');location=u"
							class="rounded-xl border-slate-200 bg-white py-2 pl-3 pr-8 text-sm font-semibold text-slate-700 focus:border-amber-500 focus:ring-amber-500">
							<?php foreach ( $orderby_options as $val => $label ) : ?>
								<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $current_orderby, $val ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<?php if ( woocommerce_product_loop() ) : ?>
					<ul class="products m-0 grid list-none grid-cols-2 gap-3 p-0 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
						<?php
						while ( have_posts() ) :
							the_post();
							wc_get_template_part( 'content', 'product' );
						endwhile;
						?>
					</ul>

					<!-- Pagination -->
					<?php
					$big  = 999999999;
					$html = paginate_links( array(
						'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'    => '?paged=%#%',
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'total'     => $wp_query->max_num_pages,
						'prev_text' => '&lsaquo; Prev',
						'next_text' => 'Next &rsaquo;',
						'type'      => 'plain',
						'end_size'  => 1,
						'mid_size'  => 1,
					) );
					if ( $html ) : ?>
						<nav class="mt-12 flex justify-center [&_.page-numbers]:mx-0.5 [&_.page-numbers]:inline-flex [&_.page-numbers]:h-10 [&_.page-numbers]:min-w-[2.5rem] [&_.page-numbers]:items-center [&_.page-numbers]:justify-center [&_.page-numbers]:rounded-lg [&_.page-numbers]:border [&_.page-numbers]:border-slate-200 [&_.page-numbers]:bg-white [&_.page-numbers]:px-3 [&_.page-numbers]:text-sm [&_.page-numbers]:font-semibold [&_.page-numbers]:text-slate-600 [&_.current]:!border-amber-500 [&_.current]:!bg-amber-500 [&_.current]:!text-slate-900" aria-label="Pagination">
							<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</nav>
					<?php endif; ?>

				<?php else : ?>
					<!-- Empty state -->
					<div class="rounded-3xl bg-white py-20 text-center ring-1 ring-slate-100">
						<span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-stone-100 text-slate-400">
							<svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="M21 21l-4-4"/></svg>
						</span>
						<h2 class="mt-4 text-lg font-bold text-slate-900"><?php echo $sel_sale ? esc_html__( 'No active deals right now', 'isdb-custom' ) : esc_html__( 'No products match your filters', 'isdb-custom' ); ?></h2>
						<p class="mt-1 text-sm text-slate-500"><?php echo $sel_sale ? esc_html__( 'Check back soon — new offers drop regularly. To feature a product here, set a Sale price on it in WooCommerce.', 'isdb-custom' ) : esc_html__( 'Try widening your price range or clearing a filter.', 'isdb-custom' ); ?></p>
						<?php if ( $has_filters ) : ?>
							<a href="<?php echo esc_url( $clear_url ); ?>" class="mt-6 inline-block rounded-xl bg-amber-500 px-6 py-3 text-sm font-bold text-slate-900 transition hover:bg-amber-400">Clear all filters</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php
get_footer( 'shop' );
