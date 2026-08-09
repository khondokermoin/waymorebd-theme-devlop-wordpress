<?php
/**
 * Generic page template — also renders WooCommerce Cart & Checkout pages
 * (they inject content via shortcode/block into the_content()).
 *
 * CHECKOUT ISOLATION: on the checkout step (but NOT order-received) we load a
 * distraction-free header/footer — no nav, search, or cart drawer.
 *
 * CONTENT PAGES (About Us, Contact, policies…) render inside Tailwind
 * Typography (`prose`) so plain WordPress block/editor content — headings,
 * paragraphs, lists, quotes, images, tables — is readable with no extra markup.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

$is_account = function_exists( 'is_account_page' ) && is_account_page();
$is_cart_pg = function_exists( 'is_cart' ) && is_cart();
$is_chkout  = function_exists( 'is_checkout' ) && is_checkout();

// WooCommerce-owned pages render their own headings — no big title banner here.
$is_wc_page = ( $is_cart_pg || $is_chkout || $is_account );

// Checkout keeps the SITE header + footer (matches the reference / client
// preference over a stripped "isolated" funnel). Set to a truthy expression
// again if you ever want to A/B the distraction-free checkout.
$isolate = false;

// Full-width grid for all WooCommerce pages, narrower for content pages.
$width = $is_wc_page ? 'max-w-7xl' : 'max-w-4xl';

/*
 * Pages that ship their own hero (e.g. About Us) can set custom field
 * `hide_page_banner` = 1. We then drop the big title but STILL show a thin
 * breadcrumb strip (so there's no dead gap under the nav), and the page
 * content leads flush right after it.  get_queried_object_id() is used because
 * this runs before the loop.
 */
$hide_banner = ( ! $is_wc_page ) ? (bool) get_post_meta( get_queried_object_id(), 'hide_page_banner', true ) : false;

get_header( $isolate ? 'checkout' : '' );
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<?php
	/*
	 * Breadcrumb strip on EVERY page — cart, checkout, account and content
	 * pages alike. The big page title shows only on content pages that don't
	 * opt out via `hide_page_banner`.
	 */
	$show_title = ( ! $is_wc_page ) && ! $hide_banner;
	?>
	<section class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 <?php echo $show_title ? 'py-3.5' : 'py-2.5'; ?>">
			<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) : ?>
				<nav class="text-[13px] text-slate-500 <?php echo $show_title ? 'mb-1' : ''; ?>" aria-label="<?php esc_attr_e( 'Breadcrumb', 'isdb-custom' ); ?>">
					<?php woocommerce_breadcrumb( array(
						'delimiter'   => '<span class="mx-2 text-slate-300">/</span>',
						'wrap_before' => '<div class="flex flex-wrap items-center">',
						'wrap_after'  => '</div>',
					) ); ?>
				</nav>
			<?php endif; ?>
			<?php if ( $show_title ) : ?>
				<h1 class="text-xl font-extrabold tracking-tight text-brand-title sm:text-2xl"><?php the_title(); ?></h1>
			<?php endif; ?>
		</div>
	</section>

	<?php
	// Hidden-banner pages lead with their own (often full-width) hero →
	// drop the container's side/top padding so it sits flush under the strip.
	if ( $is_wc_page ) {
		$content_classes = $width . ' px-4 sm:px-6 lg:px-8 py-5 lg:py-6';
	} elseif ( $hide_banner ) {
		$content_classes = 'max-w-none pb-6';
	} else {
		$content_classes = $width . ' px-4 sm:px-6 lg:px-8 py-6';
	}
	?>
	<div class="mx-auto <?php echo esc_attr( $content_classes ); ?>">
		<?php
		while ( have_posts() ) :
			the_post();

			if ( $is_wc_page ) {
				// Cart / checkout: no prose wrapper — WC markup is styled directly.
				the_content();
			} else {
				// Standard content page: full Tailwind Typography treatment.
				?>
				<article class="prose prose-slate max-w-none
					prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-slate-900
					prose-h2:mt-12 prose-h2:mb-4 prose-h2:text-2xl
					prose-h3:mt-8 prose-h3:mb-3 prose-h3:text-xl
					prose-p:leading-relaxed prose-p:text-slate-600
					prose-a:font-medium prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline
					prose-strong:text-slate-900
					prose-li:text-slate-600 prose-li:marker:text-amber-500
					prose-blockquote:border-l-4 prose-blockquote:border-amber-500 prose-blockquote:bg-white
					prose-blockquote:not-italic prose-blockquote:py-2 prose-blockquote:px-5 prose-blockquote:rounded-r-xl
					prose-img:rounded-2xl prose-img:shadow-sm
					prose-hr:border-slate-200
					prose-table:text-sm prose-th:text-slate-900
					prose-figcaption:text-slate-400">
					<?php
					the_content();

					wp_link_pages( array(
						'before' => '<div class="mt-8 flex gap-2 text-sm font-semibold text-slate-600">',
						'after'  => '</div>',
					) );
					?>
				</article>

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<div class="mt-12"><?php comments_template(); ?></div>
				<?php endif; ?>
				<?php
			}
		endwhile;
		?>
	</div>
</main>

<?php
get_footer( $isolate ? 'checkout' : '' );
