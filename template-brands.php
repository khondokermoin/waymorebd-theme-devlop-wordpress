<?php
/**
 * Template Name: Brands
 *
 * "All Brands" directory — Way More BD.
 * Layout: full-width hero banner · intro title + copy · "Popular Brands" logo
 * grid. Each logo links to that brand's product archive.
 *
 * The hero uses assets/img/brands-hero.svg by default. To swap in a real photo,
 * drop brands-hero.jpg / .png / .webp in assets/img/ (it wins automatically),
 * or define ISDB_BRANDS_HERO with a Media Library URL.
 *
 * Brands come from the first registered brand taxonomy (product_brand /
 * pwb-brand / yith_product_brand). Auto-created at /brands/ — see
 * isdb_ensure_brands_page(). Homepage "Our Brands → See all" points here.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$brand_tax = function_exists( 'isdb_brand_taxonomy' ) ? isdb_brand_taxonomy() : '';
$brands    = array();
if ( $brand_tax ) {
	$terms = get_terms( array(
		'taxonomy'   => $brand_tax,
		'hide_empty' => true,   // only brands that actually have products
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );
	if ( ! is_wp_error( $terms ) ) {
		$brands = $terms;
	}
}
$shop_url = wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

// Resolve the hero banner (raster override -> constant -> bundled SVG).
$hero_url = '';
if ( defined( 'ISDB_BRANDS_HERO' ) && ISDB_BRANDS_HERO ) {
	$hero_url = ISDB_BRANDS_HERO;
} else {
	foreach ( array( 'brands-hero.jpg', 'brands-hero.webp', 'brands-hero.png', 'brands-hero.svg' ) as $file ) {
		if ( file_exists( get_theme_file_path( 'assets/img/' . $file ) ) ) {
			$hero_url = get_theme_file_uri( 'assets/img/' . $file );
			break;
		}
	}
}

// Intro copy (editable — swap freely).
$brand_title = __( 'Brands You Can Trust', 'isdb-custom' );
$brand_lead  = __( 'At Way More BD, every brand we carry earns its place. We hand-pick partners who share our belief that everyday essentials should be honest, safe, and genuinely good — from careful sourcing to thoughtful packaging. Whether it is your kitchen, your home, or your family\'s table, each brand here is one we would happily choose for our own. That is our promise: quality you can feel, and trust you can taste.', 'isdb-custom' );
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- ═══════════ HERO BANNER (full width) ═══════════ -->
	<?php if ( $hero_url ) : ?>
		<section class="relative w-full overflow-hidden">
			<img src="<?php echo esc_url( $hero_url ); ?>" alt="<?php esc_attr_e( 'Way More BD — trusted brands', 'isdb-custom' ); ?>"
				class="h-[190px] w-full object-cover sm:h-[300px] lg:h-[420px]" loading="eager" fetchpriority="high" />
		</section>
	<?php endif; ?>

	<!-- ═══════════ INTRO: TITLE + COPY ═══════════ -->
	<section class="mx-auto max-w-4xl px-4 py-10 text-center sm:px-6 sm:py-14 lg:px-8">
		<h1 class="text-2xl font-bold tracking-tight text-brand-title sm:text-3xl"><?php echo esc_html( $brand_title ); ?></h1>
		<span class="mx-auto mt-3 block h-[3px] w-14 rounded-full bg-brand-primary"></span>
		<p class="mx-auto mt-6 max-w-3xl text-[15px] leading-8 text-slate-600"><?php echo esc_html( $brand_lead ); ?></p>
	</section>

	<!-- ═══════════ POPULAR BRANDS ═══════════ -->
	<section class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">

		<div class="mb-8 text-center">
			<h2 class="text-xl font-bold tracking-tight text-brand-title sm:text-2xl"><?php esc_html_e( 'Popular Brands', 'isdb-custom' ); ?></h2>
			<span class="mx-auto mt-3 block h-[3px] w-14 rounded-full bg-brand-primary"></span>
		</div>

		<?php if ( ! empty( $brands ) ) : ?>

			<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
				<?php foreach ( $brands as $brand ) :
					$thumb_id = get_term_meta( $brand->term_id, 'thumbnail_id', true );
					$link     = get_term_link( $brand );
					if ( is_wp_error( $link ) ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_attr( $brand->name ); ?>"
						class="single-brand group flex h-[104px] items-center justify-center rounded-lg border border-[#eee] bg-white p-4 transition hover:border-brand-primary hover:shadow-[3px_3px_10px_rgba(0,0,0,0.08)]">
						<?php if ( $thumb_id ) : ?>
							<?php
							echo wp_get_attachment_image(
								$thumb_id,
								'medium',
								false,
								array(
									'class'   => 'max-h-[64px] w-auto max-w-[150px] object-contain transition duration-300 group-hover:scale-[1.05]',
									'alt'     => $brand->name,
									'loading' => 'lazy',
								)
							);
							?>
						<?php else : ?>
							<span class="text-center text-base font-extrabold uppercase tracking-wide text-brand-title transition group-hover:text-brand-primary"><?php echo esc_html( $brand->name ); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>

		<?php else : ?>

			<!-- Empty state — no brand taxonomy or no brands with products yet -->
			<div class="mx-auto max-w-xl rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center sm:p-10">
				<span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-primary/10 text-brand-primary">
					<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
				</span>
				<h3 class="mt-4 text-base font-bold text-brand-title"><?php esc_html_e( 'No brands to show yet', 'isdb-custom' ); ?></h3>
				<p class="mx-auto mt-1 max-w-md text-sm text-slate-500"><?php esc_html_e( 'Brands will appear here once they are assigned to products. In the meantime, explore the full shop.', 'isdb-custom' ); ?></p>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="mt-5 inline-flex items-center gap-1.5 rounded-lg bg-brand-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-hover">
					<?php esc_html_e( 'Browse the shop', 'isdb-custom' ); ?>
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
				</a>
			</div>

		<?php endif; ?>

	</section>
</main>

<?php
get_footer();
