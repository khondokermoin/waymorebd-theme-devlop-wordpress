<?php
/**
 * Homepage — Way More BD (front-page.php)
 *
 * Sections: hero · features · category carousel · Top Selling (large cards) ·
 * Our Brands · Exclusive Combo Deals · product rails · testimonials · guarantee.
 *
 * Carousels use native scroll-snap tracks with JS arrows (isdb_carousel_start /
 * isdb_carousel_end + the shared carousel JS) — no Swiper dependency.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$categories   = function_exists( 'isdb_top_categories' ) ? isdb_top_categories( 12 ) : array();
$best_sellers = function_exists( 'isdb_best_sellers' ) ? isdb_best_sellers( 12 ) : array();
$brands       = function_exists( 'isdb_brand_terms' ) ? isdb_brand_terms( 12 ) : array();
$combos       = function_exists( 'isdb_combo_products' ) ? isdb_combo_products( 8 ) : array();
$on_sale_ids  = function_exists( 'wc_get_product_ids_on_sale' ) ? wc_get_product_ids_on_sale() : array();
$reviews      = function_exists( 'isdb_recent_reviews' ) ? isdb_recent_reviews( 6 ) : array();
$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

/* isdb_section_head() now lives in functions.php (shared with single-product). */

/** Grid of product cards from WC_Product[] (reuses content-product.php). */
function isdb_render_product_grid( $products, $limit = 8 ) {
	$shown = 0;
	global $product, $post;
	$orig_product = $product;
	$orig_post    = $post;
	echo '<ul class="products m-0 grid list-none grid-cols-2 gap-3 p-0 sm:gap-4 md:grid-cols-3 lg:grid-cols-5">';
	foreach ( $products as $p ) {
		if ( ! $p instanceof WC_Product || ! $p->is_visible() ) {
			continue;
		}
		if ( $shown >= $limit ) {
			break;
		}
		$shown++;
		$post    = get_post( $p->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$product = $p;                        // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $post );
		wc_get_template_part( 'content', 'product' );
	}
	echo '</ul>';
	$product = $orig_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	$post    = $orig_post;    // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	wp_reset_postdata();
}
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- ============================ HERO (auto-rotating carousel) ============================ -->
	<?php
	$hero_slides = array(
		array( 'img' => get_theme_file_uri( 'assets/img/hero-1.svg' ), 'url' => $shop_url,              'alt' => __( 'Pure Mustard Oil & Ghee', 'isdb-custom' ) ),
		array( 'img' => get_theme_file_uri( 'assets/img/hero-2.svg' ), 'url' => isdb_deals_url(),        'alt' => __( 'Raw Natural Honey', 'isdb-custom' ) ),
		array( 'img' => get_theme_file_uri( 'assets/img/hero-3.svg' ), 'url' => isdb_new_arrivals_url(), 'alt' => __( 'Premium Nuts & Dates', 'isdb-custom' ) ),
	);
	?>
	<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
		<div class="grid grid-cols-1 gap-3 lg:grid-cols-[1.9fr,1fr]">

			<!-- Auto-rotating slider (Alpine: loops, autoplay, pause-on-hover, dots + arrows) -->
			<div class="relative overflow-hidden rounded-lg"
				x-data="{ i:0, n:<?php echo (int) count( $hero_slides ); ?>, t:null,
					play(){ this.stop(); this.t = setInterval(() => this.go(this.i + 1), 6000); },
					stop(){ if (this.t) { clearInterval(this.t); this.t = null; } },
					go(k){ this.i = (k + this.n) % this.n; } }"
				x-init="play()" @mouseenter="stop()" @mouseleave="play()">

				<div class="flex transition-transform duration-500 ease-out" :style="'transform: translateX(-' + (i * 100) + '%)'">
					<?php foreach ( $hero_slides as $i => $s ) : ?>
						<a href="<?php echo esc_url( $s['url'] ); ?>" class="w-full flex-none">
							<img src="<?php echo esc_url( $s['img'] ); ?>" alt="<?php echo esc_attr( $s['alt'] ); ?>"
								width="1000" height="400" decoding="async"
								<?php echo 0 === $i ? 'fetchpriority="high" loading="eager"' : 'loading="lazy"'; ?>
								class="h-[180px] w-full object-cover sm:h-[260px] lg:h-[330px]" />
						</a>
					<?php endforeach; ?>
				</div>

				<!-- Arrows -->
				<button type="button" @click="stop(); go(i - 1)" aria-label="<?php esc_attr_e( 'Previous slide', 'isdb-custom' ); ?>"
					class="absolute left-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/85 text-brand-title shadow-md transition hover:bg-white">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
				</button>
				<button type="button" @click="stop(); go(i + 1)" aria-label="<?php esc_attr_e( 'Next slide', 'isdb-custom' ); ?>"
					class="absolute right-3 top-1/2 z-10 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-white/85 text-brand-title shadow-md transition hover:bg-white">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
				</button>

				<!-- Dots -->
				<div class="absolute bottom-3 left-1/2 z-10 flex -translate-x-1/2 gap-2">
					<template x-for="d in n" :key="d">
						<button type="button" @click="stop(); go(d - 1)" :aria-label="'<?php echo esc_js( __( 'Go to slide', 'isdb-custom' ) ); ?> ' + d"
							class="h-2 rounded-full bg-white transition-all"
							:class="i === (d - 1) ? 'w-6 opacity-100' : 'w-2 opacity-60 hover:opacity-90'"></button>
					</template>
				</div>
			</div>

			<!-- Right fixed banner -->
			<a href="<?php echo esc_url( isdb_deals_url() ); ?>" class="hidden overflow-hidden rounded-lg lg:block">
				<img src="<?php echo esc_url( get_theme_file_uri( 'assets/img/hero-side.svg' ) ); ?>" alt="<?php esc_attr_e( 'Cash on Delivery nationwide', 'isdb-custom' ); ?>" width="500" height="410" loading="lazy" decoding="async" class="h-[330px] w-full object-cover" />
			</a>
		</div>
	</section>

	<!-- ============================ FEATURE STRIP ============================ -->
	<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
		<div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
			<?php
			$pillars = array(
				array( 'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z', __( 'Authentic Products', 'isdb-custom' ), __( 'Sourced & verified', 'isdb-custom' ) ),
				array( 'M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h.375c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9H3.375c-.621 0-1.125.504-1.125 1.125v3.026', __( 'Fast Delivery', 'isdb-custom' ), __( 'Nationwide, quick', 'isdb-custom' ) ),
				array( 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z', __( 'Cash on Delivery', 'isdb-custom' ), __( 'Pay when it arrives', 'isdb-custom' ) ),
				array( 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z', __( 'Support Promise', 'isdb-custom' ), __( 'Real humans, fast', 'isdb-custom' ) ),
			);
			foreach ( $pillars as $p ) : ?>
				<div class="feature-item flex items-center gap-3 rounded-card border border-[#e0e0e0] bg-white p-3.5">
					<span class="flex h-10 w-10 flex-none items-center justify-center rounded-card bg-brand-soft text-brand-primary">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $p[0] ); ?>"/></svg>
					</span>
					<div class="min-w-0">
						<p class="truncate text-[13px] font-bold text-brand-title"><?php echo wp_kses_post( $p[1] ); ?></p>
						<p class="truncate text-[11px] text-slate-500"><?php echo wp_kses_post( $p[2] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ============================ FEATURED CATEGORIES (carousel) ============================ -->
	<?php if ( ! empty( $categories ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<h2 class="mb-4 text-center text-lg font-bold text-brand-title sm:text-xl"><?php esc_html_e( 'Featured Categories', 'isdb-custom' ); ?></h2>
			<?php isdb_carousel_start(); ?>
				<?php foreach ( $categories as $term ) :
					$thumb_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
					$img_url  = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : ''; ?>
					<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"
						class="group w-[104px] flex-none snap-start rounded-card border border-[#e0e0e0] bg-white p-3 text-center transition hover:shadow-[3px_3px_8px_rgba(0,0,0,0.16)] sm:w-[120px]">
						<span class="mx-auto flex h-[62px] w-[62px] items-center justify-center overflow-hidden rounded-full bg-brand-bg sm:h-[72px] sm:w-[72px]">
							<?php if ( $img_url ) : ?>
								<?php echo wp_get_attachment_image( $thumb_id, 'medium', false, array( 'class' => 'h-full w-full object-cover transition duration-300 group-hover:scale-105', 'alt' => $term->name ) ); ?>
							<?php else : ?>
								<span class="text-xl font-black text-brand-primary"><?php echo esc_html( mb_substr( $term->name, 0, 1 ) ); ?></span>
							<?php endif; ?>
						</span>
						<span class="mt-2 line-clamp-2 block text-[11px] font-semibold leading-tight text-brand-title group-hover:text-brand-primary sm:text-xs"><?php echo esc_html( $term->name ); ?></span>
					</a>
				<?php endforeach; ?>
			<?php isdb_carousel_end(); ?>
		</section>
	<?php endif; ?>

	<!-- ============================ TOP SELLING (large horizontal cards) ============================ -->
	<?php
	$top = array();
	foreach ( $best_sellers as $bp ) {
		if ( $bp instanceof WC_Product && $bp->is_visible() ) {
			$top[] = $bp;
		}
		if ( count( $top ) >= 4 ) {
			break;
		}
	}
	if ( $top ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<h2 class="mb-4 text-center text-lg font-bold text-brand-title sm:text-xl"><?php esc_html_e( 'Top Selling Products', 'isdb-custom' ); ?></h2>

			<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4">
				<?php foreach ( $top as $tp ) :
					$t_reg = (float) $tp->get_regular_price();
					$t_cur = (float) $tp->get_price();
					if ( $tp instanceof WC_Product_Variable ) {
						$t_reg = (float) $tp->get_variation_regular_price( 'min', true );
						$t_cur = (float) $tp->get_variation_price( 'min', true );
					}
					$t_off = ( $tp->is_on_sale() && $t_reg > 0 && $t_cur > 0 && $t_reg > $t_cur )
						? round( ( ( $t_reg - $t_cur ) / $t_reg ) * 100 ) : 0;
					$t_save = ( $t_reg > $t_cur ) ? ( $t_reg - $t_cur ) : 0;
					?>
					<div class="relative flex gap-3 rounded-card border border-[#e0e0e0] bg-white p-3 transition hover:shadow-[3px_3px_8px_rgba(0,0,0,0.16)] sm:gap-4 sm:p-4">

						<?php if ( $tp->get_total_sales() > 15 ) : ?>
							<span class="absolute right-0 top-0 z-10 rounded-bl-lg bg-brand-primary px-2 py-1 text-[10px] font-semibold leading-none text-white"><?php esc_html_e( 'Best Selling', 'isdb-custom' ); ?></span>
						<?php endif; ?>

						<a href="<?php echo esc_url( $tp->get_permalink() ); ?>" class="flex h-[112px] w-[100px] flex-none items-center justify-center overflow-hidden sm:h-[140px] sm:w-[130px]">
							<?php echo $tp->get_image( 'woocommerce_thumbnail', array( 'class' => 'max-h-full w-auto max-w-full object-contain' ) ); ?>
						</a>

						<div class="flex min-w-0 flex-1 flex-col justify-center">
							<h3 class="line-clamp-2 text-sm font-semibold leading-snug text-brand-title sm:text-base">
								<a href="<?php echo esc_url( $tp->get_permalink() ); ?>" class="transition hover:text-brand-primary"><?php echo esc_html( $tp->get_name() ); ?></a>
							</h3>

							<div class="mt-1.5 flex flex-wrap items-baseline gap-x-2 text-[15px] font-bold text-brand-primary
								[&_del]:text-[13px] [&_del]:font-normal [&_del]:text-slate-400 [&_ins]:text-brand-primary [&_ins]:no-underline">
								<?php echo $tp->get_price_html(); ?>
							</div>

							<?php if ( $t_save > 0 ) : ?>
								<span class="mt-1.5 inline-flex w-fit items-center rounded bg-[#22c55e] px-1.5 py-0.5 text-[10px] font-semibold leading-none text-white">
									<?php esc_html_e( 'Save', 'isdb-custom' ); ?> <?php echo wp_kses_post( wc_price( $t_save ) ); ?><?php echo $t_off ? ' (' . esc_html( $t_off ) . '%)' : ''; ?>
								</span>
							<?php endif; ?>

							<!-- Add to Cart + Buy now, side by side -->
							<div class="mt-3 flex flex-wrap gap-2">
								<div class="isdb-cart-ctl min-w-[120px] flex-1" data-product="<?php echo esc_attr( $tp->get_id() ); ?>">
									<div class="isdb-add-wrap
										[&_.button]:flex [&_.button]:w-full [&_.button]:items-center [&_.button]:justify-center [&_.button]:gap-1.5
										[&_.button]:rounded-card [&_.button]:border [&_.button]:border-brand-primary [&_.button]:bg-white
										[&_.button]:px-3 [&_.button]:py-2 [&_.button]:text-[12px] [&_.button]:font-semibold
										[&_.button]:text-brand-primary [&_.button]:no-underline [&_.button]:transition
										[&_.button:hover]:bg-brand-primary [&_.button:hover]:text-white
										[&_.added_to_cart]:hidden">
										<a href="?add-to-cart=<?php echo esc_attr( $tp->get_id() ); ?>" data-quantity="1"
											data-product_id="<?php echo esc_attr( $tp->get_id() ); ?>"
											class="button add_to_cart_button <?php echo $tp->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : ''; ?>">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
											<?php esc_html_e( 'Add to Cart', 'isdb-custom' ); ?>
										</a>
									</div>
								</div>

								<a href="<?php echo esc_url( isdb_buy_now_url( $tp->get_id() ) ); ?>"
									class="inline-flex min-w-[100px] flex-1 items-center justify-center rounded-card bg-brand-primary px-3 py-2 text-[12px] font-semibold text-white transition hover:bg-brand-hover">
									<?php esc_html_e( 'Buy now', 'isdb-custom' ); ?>
								</a>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============================ OUR BRANDS ============================ -->
	<?php if ( ! empty( $brands ) ) :
		$brand_tax = isdb_brand_taxonomy(); ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<?php isdb_section_head( __( 'Our Brands', 'isdb-custom' ), get_post_type_archive_link( 'product' ) ?: $shop_url, __( 'See all', 'isdb-custom' ) ); ?>
			<?php isdb_carousel_start(); ?>
				<?php foreach ( $brands as $brand ) :
					$b_thumb = get_term_meta( $brand->term_id, 'thumbnail_id', true );
					$b_img   = $b_thumb ? wp_get_attachment_image_url( $b_thumb, 'medium' ) : ''; ?>
					<a href="<?php echo esc_url( get_term_link( $brand ) ); ?>"
						class="single-brand flex h-[74px] w-[150px] flex-none snap-start items-center justify-center rounded-card border border-[#eee] bg-white px-3 transition hover:border-brand-primary sm:w-[180px]">
						<?php if ( $b_img ) : ?>
							<?php echo wp_get_attachment_image( $b_thumb, 'medium', false, array( 'class' => 'max-h-[52px] w-auto max-w-[124px] object-contain', 'alt' => $brand->name ) ); ?>
						<?php else : ?>
							<span class="text-center text-sm font-extrabold uppercase tracking-wide text-brand-title"><?php echo esc_html( $brand->name ); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			<?php isdb_carousel_end(); ?>
		</section>
	<?php endif; ?>

	<!-- ============================ EXCLUSIVE COMBO DEALS ============================ -->
	<?php if ( ! empty( $combos ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<!-- gradient: primary @12% -> primary @4%, matching the reference -->
			<div class="combo-section-inner rounded-2xl p-4 sm:p-8"
				style="background:linear-gradient(135deg, rgba(244,135,33,.12), rgba(244,135,33,.04));">

				<div class="combo-section-head mb-5 flex flex-wrap items-center justify-between gap-3">
					<h2 class="m-0 flex items-center gap-3 text-lg font-bold text-brand-title sm:text-xl">
						<span class="flex h-[38px] w-[38px] items-center justify-center rounded-card bg-brand-primary text-white">
							<svg class="h-4.5 w-4.5" style="height:18px;width:18px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
						</span>
						<?php esc_html_e( 'Exclusive Combo Deals', 'isdb-custom' ); ?>
					</h2>
					<a href="<?php echo esc_url( $shop_url ); ?>"
						class="flex items-center gap-1 rounded-card bg-brand-primary px-3 py-2 text-[13px] font-semibold text-white transition hover:bg-brand-dark">
						<?php esc_html_e( 'View All Combos', 'isdb-custom' ); ?>
						<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
					</a>
				</div>

				<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
					<?php
					$c_shown = 0;
					foreach ( $combos as $cp ) :
						if ( ! $cp instanceof WC_Product || ! $cp->is_visible() ) {
							continue;
						}
						if ( $c_shown >= 4 ) {
							break;
						}
						$c_shown++;

						$c_reg = (float) $cp->get_regular_price();
						$c_cur = (float) $cp->get_price();
						if ( $cp instanceof WC_Product_Variable ) {
							$c_reg = (float) $cp->get_variation_regular_price( 'min', true );
							$c_cur = (float) $cp->get_variation_price( 'min', true );
						}
						$c_off = ( $c_reg > 0 && $c_cur > 0 && $c_reg > $c_cur )
							? round( ( ( $c_reg - $c_cur ) / $c_reg ) * 100 ) : 0;
						?>
						<div class="combo-card overflow-hidden rounded-card border border-[#ddd] bg-white transition hover:shadow-[3px_3px_8px_rgba(0,0,0,0.16)]">

							<!-- Image + badges -->
							<a href="<?php echo esc_url( $cp->get_permalink() ); ?>" class="combo-top relative block h-[170px] sm:h-[210px]">
								<?php echo $cp->get_image( 'woocommerce_thumbnail', array( 'class' => 'combo-image h-full w-full object-contain' ) ); ?>

								<!-- Combo Offer: top-right, orange -->
								<span class="combo-label absolute right-0 top-0 rounded-bl-xl bg-brand-primary px-1.5 py-1 text-[10px] font-semibold leading-none text-white">
									<?php esc_html_e( 'Combo Offer', 'isdb-custom' ); ?>
								</span>

								<!-- Save X%: bottom-right of the image, green -->
								<?php if ( $c_off > 0 ) : ?>
									<span class="combo-saving absolute bottom-0 right-0 rounded-tl-xl bg-[#22c55e] px-1.5 py-1 text-[10px] font-semibold leading-none text-white">
										<?php printf( esc_html__( 'Save %s%%', 'isdb-custom' ), esc_html( $c_off ) ); ?>
									</span>
								<?php endif; ?>
							</a>

							<!-- Content -->
							<div class="combo-content p-2 px-3">
								<h3 class="combo-name m-0 line-clamp-2 text-[13px] font-semibold leading-tight text-brand-title sm:text-[15px]">
									<a href="<?php echo esc_url( $cp->get_permalink() ); ?>" class="transition hover:text-brand-primary"><?php echo esc_html( $cp->get_name() ); ?></a>
								</h3>

								<div class="combo-pricing mt-2 flex flex-wrap items-center gap-2">
									<span class="price text-base font-semibold text-brand-primary"><?php echo wp_kses_post( wc_price( $c_cur ) ); ?></span>
									<?php if ( $c_reg > $c_cur ) : ?>
										<span class="combo-regular-price price text-[13px] leading-tight text-brand-title/40 line-through"><?php echo wp_kses_post( wc_price( $c_reg ) ); ?></span>
									<?php endif; ?>
								</div>

								<a href="<?php echo esc_url( $cp->get_permalink() ); ?>"
									class="combo-btn mt-2 mb-2 block rounded-card bg-brand-primary py-2 text-center text-xs font-semibold text-white transition hover:bg-brand-dark">
									<?php esc_html_e( 'View Details', 'isdb-custom' ); ?>
								</a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============================ PRODUCT RAILS ============================ -->
	<?php if ( ! empty( $best_sellers ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<?php isdb_section_head( __( 'Just For You', 'isdb-custom' ), $shop_url, __( 'View all products', 'isdb-custom' ) ); ?>
			<?php isdb_render_product_carousel( $best_sellers, 12, 5000 ); ?>
		</section>
	<?php endif; ?>

	<?php
	$latest = function_exists( 'wc_get_products' ) ? wc_get_products( array(
		'status' => 'publish', 'limit' => 10, 'orderby' => 'date', 'order' => 'DESC',
	) ) : array();
	if ( ! empty( $latest ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<?php isdb_section_head( __( 'New Arrivals', 'isdb-custom' ), isdb_new_arrivals_url(), __( 'View all items', 'isdb-custom' ) ); ?>
			<?php isdb_render_product_carousel( $latest, 12, 5500 ); ?>
		</section>
	<?php endif; ?>

	<!-- ============================ DEAL BAND ============================ -->
	<?php if ( ! empty( $on_sale_ids ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<div class="overflow-hidden rounded-2xl" style="background-image:linear-gradient(135deg,#f48721,#f8a95c);">
				<div class="flex flex-col items-center justify-between gap-5 p-6 text-center sm:flex-row sm:p-8 sm:text-left">
					<div>
						<p class="m-0 text-[11px] font-bold uppercase tracking-wider text-white/80"><?php esc_html_e( 'Limited-time savings', 'isdb-custom' ); ?></p>
						<h2 class="mt-1 text-xl font-extrabold !text-white sm:text-2xl"><?php esc_html_e( 'On Sale Now — while stocks last', 'isdb-custom' ); ?></h2>
						<p class="mt-1.5 text-sm text-white/85"><?php printf( esc_html( _n( '%s product currently discounted.', '%s products currently discounted.', count( $on_sale_ids ), 'isdb-custom' ) ), esc_html( count( $on_sale_ids ) ) ); ?></p>
					</div>
					<a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $shop_url ) ); ?>" class="flex-none rounded-card bg-brand-dark px-7 py-3.5 text-sm font-bold text-white transition hover:bg-black"><?php esc_html_e( 'Shop the Sale', 'isdb-custom' ); ?></a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- ============================ TESTIMONIALS (carousel) ============================ -->
	<?php if ( ! empty( $reviews ) ) : ?>
		<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<?php isdb_section_head( __( 'What Our Customers Say', 'isdb-custom' ) ); ?>
			<?php isdb_carousel_start(); ?>
				<?php foreach ( $reviews as $review ) :
					$r = (int) get_comment_meta( $review->comment_ID, 'rating', true );
					$r = $r ? $r : 5; ?>
					<figure class="testimonial-card flex w-[280px] flex-none snap-start flex-col rounded-card border border-[#e0e0e0] bg-white p-5 sm:w-[340px]">
						<div class="flex">
							<?php for ( $i = 0; $i < 5; $i++ ) : ?>
								<svg class="h-4 w-4 <?php echo $i < $r ? 'text-brand-primary' : 'text-slate-200'; ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.28 3.94a1 1 0 00.95.69h4.15c.97 0 1.37 1.24.59 1.81l-3.36 2.44a1 1 0 00-.36 1.12l1.28 3.94c.3.92-.75 1.69-1.54 1.12l-3.36-2.44a1 1 0 00-1.18 0l-3.36 2.44c-.78.57-1.83-.2-1.54-1.12l1.28-3.94a1 1 0 00-.36-1.12L2.33 9.37c-.78-.57-.38-1.81.59-1.81h4.15a1 1 0 00.95-.69l1.28-3.94z"/></svg>
							<?php endfor; ?>
						</div>
						<blockquote class="mt-3 flex-1 text-[13px] leading-relaxed text-brand-body">
							&ldquo;<?php echo esc_html( wp_trim_words( $review->comment_content, 28 ) ); ?>&rdquo;
						</blockquote>
						<figcaption class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-3">
							<span class="flex h-9 w-9 flex-none items-center justify-center rounded-full bg-brand-primary text-sm font-bold text-white"><?php echo esc_html( mb_substr( $review->comment_author, 0, 1 ) ); ?></span>
							<div class="min-w-0">
								<p class="m-0 truncate text-[13px] font-semibold text-brand-title"><?php echo esc_html( $review->comment_author ); ?></p>
								<p class="m-0 text-[11px] text-emerald-600">✓ <?php esc_html_e( 'Verified purchase', 'isdb-custom' ); ?></p>
							</div>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			<?php isdb_carousel_end(); ?>
		</section>
	<?php endif; ?>

	<!-- ============================ GUARANTEE ============================ -->
	<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 pb-10">
		<div class="flex flex-col items-center gap-5 rounded-2xl bg-brand-dark p-7 text-center sm:flex-row sm:text-left">
			<span class="flex h-14 w-14 flex-none items-center justify-center rounded-card bg-brand-primary/15 text-brand-primary ring-1 ring-brand-primary/30">
				<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
			</span>
			<div class="flex-1">
				<h2 class="text-lg font-bold !text-white sm:text-xl"><?php esc_html_e( 'The Way More BD Promise', 'isdb-custom' ); ?></h2>
				<p class="mt-1.5 max-w-2xl text-sm text-white/75"><?php esc_html_e( 'Authentic products, honest pricing, and a support team that answers. If something isn\'t right, we make it right — no hassle.', 'isdb-custom' ); ?></p>
			</div>
			<a href="<?php echo esc_url( $shop_url ); ?>" class="flex-none rounded-card bg-brand-primary px-7 py-3.5 text-sm font-bold text-white transition hover:bg-brand-hover"><?php esc_html_e( 'Start Shopping', 'isdb-custom' ); ?></a>
		</div>
	</section>

</main>

<?php
get_footer();
