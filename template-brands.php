<?php
/**
 * Template Name: Brands
 *
 * Standalone "All Brands" directory — Way More BD.
 * Lists every brand (product_brand / pwb-brand / yith_product_brand taxonomy)
 * as a logo card that links to that brand's product archive. The page is
 * auto-created at /brands/ on theme activation (see isdb_ensure_brands_page);
 * the homepage "Our Brands → See all" link points here via isdb_brands_url().
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
		'orderby'    => 'name',
		'order'      => 'ASC',
	) );
	if ( ! is_wp_error( $terms ) ) {
		$brands = $terms;
	}
}
$shop_url = wc_get_page_permalink( 'shop' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- Breadcrumb strip (matches the other page templates) -->
	<section class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 py-2.5 sm:px-6 lg:px-8">
			<nav class="text-[13px] text-slate-500" aria-label="<?php esc_attr_e( 'Breadcrumb', 'isdb-custom' ); ?>">
				<div class="flex flex-wrap items-center">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand-primary"><?php esc_html_e( 'Home', 'isdb-custom' ); ?></a>
					<span class="mx-2 text-slate-300">/</span>
					<span class="text-brand-title"><?php esc_html_e( 'Brands', 'isdb-custom' ); ?></span>
				</div>
			</nav>
		</div>
	</section>

	<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

		<!-- Heading -->
		<div class="mb-6 text-center sm:mb-9">
			<span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.14em] text-brand-primary">
				<span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span> <?php esc_html_e( 'Shop by Brand', 'isdb-custom' ); ?>
			</span>
			<h1 class="mt-3 text-2xl font-extrabold tracking-tight text-brand-title sm:text-3xl"><?php esc_html_e( 'Our Brands', 'isdb-custom' ); ?></h1>
			<?php if ( ! empty( $brands ) ) : ?>
				<p class="mx-auto mt-2 max-w-xl text-sm text-slate-500">
					<?php
					printf(
						/* translators: %s: number of brands. */
						esc_html( _n( 'Browse products from our %s trusted brand.', 'Browse products from our %s trusted brands.', count( $brands ), 'isdb-custom' ) ),
						esc_html( number_format_i18n( count( $brands ) ) )
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $brands ) ) : ?>

			<ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5">
				<?php foreach ( $brands as $brand ) :
					$thumb_id = get_term_meta( $brand->term_id, 'thumbnail_id', true );
					$link     = get_term_link( $brand );
					if ( is_wp_error( $link ) ) {
						continue;
					}
					?>
					<li>
						<a href="<?php echo esc_url( $link ); ?>"
							class="single-brand group flex h-full flex-col items-center justify-between gap-3 rounded-card border border-[#eee] bg-white p-4 text-center transition hover:border-brand-primary hover:shadow-[3px_3px_8px_rgba(0,0,0,0.12)]">

							<span class="flex h-[86px] w-full items-center justify-center">
								<?php if ( $thumb_id ) : ?>
									<?php
									echo wp_get_attachment_image(
										$thumb_id,
										'medium',
										false,
										array(
											'class'   => 'max-h-[80px] w-auto max-w-[140px] object-contain transition duration-300 group-hover:scale-[1.04]',
											'alt'     => $brand->name,
											'loading' => 'lazy',
										)
									);
									?>
								<?php else : ?>
									<span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-primary/10 text-xl font-extrabold uppercase text-brand-primary">
										<?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $brand->name, 0, 1 ) : substr( $brand->name, 0, 1 ) ); ?>
									</span>
								<?php endif; ?>
							</span>

							<span class="w-full">
								<span class="block truncate text-sm font-semibold text-brand-title transition group-hover:text-brand-primary"><?php echo esc_html( $brand->name ); ?></span>
								<span class="mt-0.5 block text-xs text-slate-400">
									<?php
									printf(
										/* translators: %s: product count for this brand. */
										esc_html( _n( '%s product', '%s products', $brand->count, 'isdb-custom' ) ),
										esc_html( number_format_i18n( $brand->count ) )
									);
									?>
								</span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php else : ?>

			<!-- Empty state — no brand taxonomy or no brands with products yet -->
			<div class="mx-auto mt-4 max-w-xl rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center sm:p-10">
				<span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-primary/10 text-brand-primary">
					<svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
				</span>
				<h2 class="mt-4 text-base font-bold text-brand-title"><?php esc_html_e( 'No brands to show yet', 'isdb-custom' ); ?></h2>
				<p class="mx-auto mt-1 max-w-md text-sm text-slate-500"><?php esc_html_e( 'Brands will appear here once they are assigned to products. In the meantime, explore the full shop.', 'isdb-custom' ); ?></p>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="mt-5 inline-flex items-center gap-1.5 rounded-lg bg-brand-primary px-5 py-2.5 text-sm font-bold text-white transition hover:bg-brand-hover">
					<?php esc_html_e( 'Browse the shop', 'isdb-custom' ); ?>
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
				</a>
			</div>

		<?php endif; ?>

	</div>
</main>

<?php
get_footer();
