<?php
/**
 * Way More BD — Custom Single Product Template
 * Path: wp-content/themes/isdb-custom/woocommerce/single-product.php
 *
 * 100% hand-built. Bypasses WooCommerce's do_action() summary hooks.
 * Data comes from the $product CRUD getters (HPOS-safe, future-proof).
 * The ONLY WC form kept intact is the add-to-cart form (functional core).
 *
 * Brand: "Your Trusted Kitchen Companion"
 * Palette:  slate-900 (trust/text) · stone-50 (breathing bg) · amber-500 (ACTION CTA)
 *           emerald-600 (in-stock/guarantee) · rose-600 (scarcity/urgency)
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

global $product;
if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_the_ID() );
}
if ( ! $product ) {
	get_footer( 'shop' );
	return;
}

/* ------------------------------------------------------------------ *
 * NEUROMARKETING DATA LAYER — computed once, reused in the markup
 * ------------------------------------------------------------------ */
$avg_rating   = (float) $product->get_average_rating();      // e.g. 4.7
$review_count = (int)   $product->get_review_count();
$rating_count = (int)   $product->get_rating_count();
$is_on_sale   = $product->is_on_sale();
$in_stock     = $product->is_in_stock();
$manage_stock = $product->managing_stock();
$stock_qty    = $product->get_stock_quantity();

// SCARCITY: only show a real number when stock is genuinely managed & low.
// Fake scarcity destroys trust — this fires only on true low stock (<= 8).
$low_stock    = ( $manage_stock && is_numeric( $stock_qty ) && $stock_qty > 0 && $stock_qty <= 8 );

// URGENCY: "high demand" social proof — shown for popular in-stock items.
$high_demand  = ( $in_stock && $product->get_total_sales() > 15 );

// Discount percentage badge (loss-aversion anchor)
$discount_pct = 0;
if ( $is_on_sale && $product->is_type( 'simple' ) ) {
	$reg = (float) $product->get_regular_price();
	$sal = (float) $product->get_sale_price();
	if ( $reg > 0 && $sal > 0 ) {
		$discount_pct = round( ( ( $reg - $sal ) / $reg ) * 100 );
	}
}

// Gallery image IDs (main + thumbs)
$main_id   = $product->get_image_id();
$gallery   = $product->get_gallery_image_ids();
$all_imgs  = array_filter( array_merge( array( $main_id ), $gallery ) );
?>

<main id="site-main" class="bg-stone-50 text-slate-900 antialiased">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

		<!-- Breadcrumb: cognitive ease / orientation -->
		<nav class="mb-6 text-sm text-slate-500" aria-label="Breadcrumb">
			<?php woocommerce_breadcrumb( array(
				'delimiter'   => '<span class="mx-2 text-slate-300">/</span>',
				'wrap_before' => '<div class="flex flex-wrap items-center">',
				'wrap_after'  => '</div>',
			) ); ?>
		</nav>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">

			<!-- ============================================================ -->
			<!-- LEFT · PRODUCT GALLERY  (generous white space, focal clarity) -->
			<!-- ============================================================ -->
			<div class="lg:sticky lg:top-24 self-start">
				<div class="relative overflow-hidden rounded-3xl bg-white ring-1 ring-slate-100 shadow-sm">

					<!-- Loss-aversion discount badge -->
					<?php if ( $discount_pct > 0 ) : ?>
						<span class="absolute left-4 top-4 z-10 rounded-full bg-rose-600 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white shadow-lg">
							Save <?php echo esc_html( $discount_pct ); ?>%
						</span>
					<?php endif; ?>

					<?php
					/*
					 * MIDJOURNEY / DALL·E PROMPT (fallback hero when a product has no image):
					 * "Premium stainless-steel non-stick frying pan on a warm marble kitchen
					 *  counter, soft natural window light from the left, shallow depth of field,
					 *  minimal props, warm neutral tones (stone, cream, amber), photorealistic
					 *  product photography, 4k, appetizing and clean, --ar 1:1 --style raw"
					 * Unsplash fallback (kitchen/cookware, on-brand):
					 *   https://images.unsplash.com/photo-1584990347449-a2d4c2c9a3f0?auto=format&fit=crop&w=1200&q=80
					 */
					if ( $main_id ) {
						echo wp_get_attachment_image( $main_id, 'large', false, array(
							'id'    => 'wmb-main-image',
							'class' => 'aspect-square w-full object-cover transition duration-500',
						) );
					} else {
						echo '<img id="wmb-main-image" class="aspect-square w-full object-cover"
							src="https://images.unsplash.com/photo-1584990347449-a2d4c2c9a3f0?auto=format&fit=crop&w=1200&q=80"
							alt="' . esc_attr( $product->get_name() ) . '">';
					}
					?>
				</div>

				<!-- Thumbnails -->
				<?php if ( count( $all_imgs ) > 1 ) : ?>
					<div class="mt-4 grid grid-cols-5 gap-3">
						<?php foreach ( $all_imgs as $i => $img_id ) :
							$full = wp_get_attachment_image_url( $img_id, 'large' ); ?>
							<button type="button"
								class="wmb-thumb overflow-hidden rounded-xl bg-white ring-1 ring-slate-100 transition hover:ring-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500 <?php echo $i === 0 ? 'ring-2 ring-amber-500' : ''; ?>"
								data-full="<?php echo esc_url( $full ); ?>">
								<?php echo wp_get_attachment_image( $img_id, 'woocommerce_thumbnail', false, array(
									'class' => 'aspect-square w-full object-cover',
								) ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- ============================================================ -->
			<!-- RIGHT · SUMMARY / DECISION COLUMN                             -->
			<!-- ============================================================ -->
			<div class="flex flex-col">

				<!-- Urgency micro-signal (social proof) -->
				<?php if ( $high_demand ) : ?>
					<div class="mb-3 inline-flex w-fit items-center gap-2 rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
						<span class="relative flex h-2 w-2">
							<span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
							<span class="relative inline-flex h-2 w-2 rounded-full bg-rose-500"></span>
						</span>
						High demand — selling fast this week
					</div>
				<?php endif; ?>

				<h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-slate-900">
					<?php echo esc_html( $product->get_name() ); ?>
				</h1>

				<!-- Rating: trust signal. Only render stars if real reviews exist. -->
				<?php if ( $review_count > 0 ) : ?>
					<div class="mt-3 flex items-center gap-3">
						<div class="flex items-center" aria-label="<?php echo esc_attr( sprintf( 'Rated %s out of 5', $avg_rating ) ); ?>">
							<?php for ( $s = 1; $s <= 5; $s++ ) :
								$filled = $s <= round( $avg_rating ); ?>
								<svg class="h-5 w-5 <?php echo $filled ? 'text-amber-400' : 'text-slate-200'; ?>" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
									<path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.28 3.94a1 1 0 00.95.69h4.15c.97 0 1.37 1.24.59 1.81l-3.36 2.44a1 1 0 00-.36 1.12l1.28 3.94c.3.92-.75 1.69-1.54 1.12l-3.36-2.44a1 1 0 00-1.18 0l-3.36 2.44c-.78.57-1.83-.2-1.54-1.12l1.28-3.94a1 1 0 00-.36-1.12L2.33 9.37c-.78-.57-.38-1.81.59-1.81h4.15a1 1 0 00.95-.69l1.28-3.94z"/>
								</svg>
							<?php endfor; ?>
						</div>
						<span class="text-sm font-medium text-slate-700"><?php echo esc_html( number_format( $avg_rating, 1 ) ); ?></span>
						<a href="#reviews" class="text-sm text-slate-500 underline-offset-2 hover:text-amber-600 hover:underline">
							<?php echo esc_html( sprintf( _n( '%s verified review', '%s verified reviews', $review_count, 'isdb-custom' ), $review_count ) ); ?>
						</a>
					</div>
				<?php endif; ?>

				<!-- Price: sale price anchored against strikethrough regular (loss aversion) -->
				<div class="mt-5 flex items-end gap-3">
					<div class="text-3xl font-extrabold text-slate-900"><?php echo $product->get_price_html(); ?></div>
					<?php if ( $discount_pct > 0 ) : ?>
						<span class="mb-1 rounded-md bg-emerald-50 px-2 py-0.5 text-sm font-semibold text-emerald-700">
							You save <?php echo esc_html( $discount_pct ); ?>%
						</span>
					<?php endif; ?>
				</div>

				<!-- Short description: breathable, benefit-led -->
				<?php $short = apply_filters( 'woocommerce_short_description', $product->get_short_description() );
				if ( $short ) : ?>
					<div class="prose prose-slate mt-5 max-w-none text-slate-600 leading-relaxed">
						<?php echo wp_kses_post( $short ); ?>
					</div>
				<?php endif; ?>

				<!-- SCARCITY: genuine low-stock bar (only on truly low, managed stock) -->
				<?php if ( $low_stock ) :
					$pct = max( 8, min( 100, ( $stock_qty / 8 ) * 100 ) ); ?>
					<div class="mt-6 rounded-2xl border border-rose-100 bg-rose-50/60 p-4">
						<div class="flex items-center justify-between text-sm font-semibold text-rose-700">
							<span>🔥 Only <?php echo esc_html( $stock_qty ); ?> left in stock</span>
							<span class="text-rose-500">Order soon</span>
						</div>
						<div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-rose-100">
							<div class="h-full rounded-full bg-rose-500" style="width: <?php echo esc_attr( $pct ); ?>%"></div>
						</div>
					</div>
				<?php elseif ( $in_stock ) : ?>
					<p class="mt-6 inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-700">
						<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0l-3.5-3.5a1 1 0 011.4-1.4l2.8 2.8 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/></svg>
						In stock &amp; ready to ship
					</p>
				<?php endif; ?>

				<!-- ======================================================== -->
				<!-- ADD TO CART · FUNCTIONAL WC FORM (do NOT strip name/nonce) -->
				<!-- High-contrast amber CTA = the single loudest element here  -->
				<!-- ======================================================== -->
				<div class="mt-7">
					<?php if ( $in_stock && $product->is_type( 'simple' ) ) : ?>
						<form class="wmb-cart flex flex-col sm:flex-row gap-3" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
							<div class="flex items-center rounded-xl ring-1 ring-slate-200 bg-white">
								<button type="button" class="wmb-qminus px-4 py-3 text-lg text-slate-500 hover:text-slate-900" aria-label="Decrease quantity">&minus;</button>
								<input type="number" name="quantity" value="1" min="1"
									max="<?php echo $manage_stock && $stock_qty ? esc_attr( $stock_qty ) : ''; ?>"
									class="w-14 border-0 text-center text-lg font-semibold focus:ring-0" />
								<button type="button" class="wmb-qplus px-4 py-3 text-lg text-slate-500 hover:text-slate-900" aria-label="Increase quantity">+</button>
							</div>
							<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>"
								class="group flex-1 rounded-xl bg-amber-500 px-8 py-4 text-center text-base font-bold text-slate-900 shadow-lg shadow-amber-500/30 transition hover:bg-amber-400 hover:shadow-amber-500/40 focus:outline-none focus:ring-4 focus:ring-amber-300 active:scale-[.99]">
								Add to Cart
								<span class="ml-1 inline-block transition group-hover:translate-x-1">&rarr;</span>
							</button>
						</form>
					<?php elseif ( $in_stock ) : ?>
						<!-- Variable / grouped / external: WC form (variation JS is load-bearing); styled via .wmb-wc-cart + swatch override -->
						<div class="wmb-wc-cart">
							<?php woocommerce_template_single_add_to_cart(); ?>
						</div>
					<?php else : ?>
						<button type="button" disabled class="w-full cursor-not-allowed rounded-xl bg-slate-200 px-8 py-4 font-bold text-slate-500">
							Currently Unavailable
						</button>
					<?php endif; ?>
				</div>

				<!-- ======================================================== -->
				<!-- TRUST STRIP · reduces purchase anxiety at the decision point -->
				<!-- ======================================================== -->
				<div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4 border-t border-slate-100 pt-8">
					<?php
					// Heroicons v2 (outline, MIT) — shield-check, banknotes, arrow-uturn, chat-bubble.
					$trust = array(
						array( 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', 'Secure Checkout', 'SSL encrypted' ),
						array( 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z', 'Cash on Delivery', 'Pay when you get it' ),
						array( 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3', 'Easy Returns', '7-day replacement' ),
						array( 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z', 'Support Promise', 'Real humans, fast' ),
					);
					foreach ( $trust as $t ) : ?>
						<div class="flex flex-col items-center text-center">
							<svg class="h-7 w-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $t[0] ); ?>"/></svg>
							<span class="mt-2 text-sm font-semibold text-slate-800"><?php echo esc_html( $t[1] ); ?></span>
							<span class="text-xs text-slate-500"><?php echo esc_html( $t[2] ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>

				<!-- Guarantee band — echoes their real "Customer Support Promise" -->
				<div class="mt-6 flex items-start gap-4 rounded-2xl bg-slate-900 p-5 text-white">
					<svg class="h-10 w-10 flex-none text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V5l7-3z"/></svg>
					<div>
						<p class="font-bold">Way More BD — Customer Support Promise</p>
						<p class="mt-1 text-sm text-slate-300">Your trusted kitchen companion. Authentic products, honest pricing, and a team that answers. Not satisfied? We make it right.</p>
					</div>
				</div>
			</div>
		</div>

		<!-- ============================================================ -->
		<!-- TABS · custom, no WC hook. Description / Details / Reviews    -->
		<!-- ============================================================ -->
		<div class="mt-16" x-data="{ tab: 'desc' }">
			<!-- Pill tab bar -->
			<div class="mb-4 flex flex-wrap gap-2">
				<button @click="tab='desc'"
					:class="tab==='desc' ? 'bg-brand-primary text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-primary'"
					class="rounded-lg px-5 py-2.5 text-sm font-semibold transition">Description</button>
				<button @click="tab='details'"
					:class="tab==='details' ? 'bg-brand-primary text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-primary'"
					class="rounded-lg px-5 py-2.5 text-sm font-semibold transition">Specifications</button>
				<button @click="tab='reviews'"
					:class="tab==='reviews' ? 'bg-brand-primary text-white shadow-sm' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:text-brand-primary'"
					class="rounded-lg px-5 py-2.5 text-sm font-semibold transition">Customer Reviews (<?php echo esc_html( $review_count ); ?>)</button>
			</div>

			<!-- Content card -->
			<div class="rounded-2xl bg-white p-6 ring-1 ring-slate-100 sm:p-8">

				<div x-show="tab==='desc'">
					<h3 class="mb-5 inline-block border-b-2 border-brand-primary pb-1.5 text-base font-bold text-slate-900">Product Details</h3>
					<div class="prose prose-slate max-w-none prose-headings:text-slate-900 prose-strong:text-slate-900 prose-li:marker:text-brand-primary">
						<?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
					</div>
				</div>

				<div x-show="tab==='details'" x-cloak>
					<h3 class="mb-5 inline-block border-b-2 border-brand-primary pb-1.5 text-base font-bold text-slate-900">Specifications</h3>
					<?php
					$attributes = $product->get_attributes();
					if ( $attributes ) : ?>
						<table class="w-full text-sm">
							<tbody class="divide-y divide-slate-100">
							<?php foreach ( $attributes as $attr ) :
								if ( ! $attr->get_visible() ) {
									continue;
								}
								$name   = wc_attribute_label( $attr->get_name() );
								$values = $product->get_attribute( $attr->get_name() ); ?>
								<tr>
									<th class="w-1/3 py-3 pr-4 text-left font-semibold text-slate-700"><?php echo esc_html( $name ); ?></th>
									<td class="py-3 text-slate-600"><?php echo esc_html( $values ); ?></td>
								</tr>
							<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p class="text-slate-500">No additional specifications listed for this product.</p>
					<?php endif; ?>
				</div>

				<div id="reviews" x-show="tab==='reviews'" x-cloak>
					<h3 class="mb-5 inline-block border-b-2 border-brand-primary pb-1.5 text-base font-bold text-slate-900">Customer Reviews</h3>
					<?php comments_template(); // WC review form/list — kept functional ?>
				</div>
			</div>
		</div>

		<!-- ============================================================ -->
		<!-- RELATED PRODUCTS · real product cards in an auto-moving carousel -->
		<!-- ============================================================ -->
		<?php
		$related = array();
		foreach ( wc_get_related_products( $product->get_id(), 12 ) as $rid ) {
			$rp = wc_get_product( $rid );
			if ( $rp instanceof WC_Product && $rp->is_visible() ) {
				$related[] = $rp;
			}
		}
		if ( $related ) : ?>
			<section class="mt-16">
				<?php isdb_section_head( 'Related Products', get_permalink( wc_get_page_id( 'shop' ) ) ?: home_url( '/shop/' ), 'More Products' ); ?>
				<?php isdb_render_product_carousel( $related, 12, 5000 ); ?>
			</section>
		<?php endif; ?>

	</div>
</main>

<!-- Gallery thumb switch + quantity stepper (no jQuery dependency) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
	var main = document.getElementById('wmb-main-image');
	document.querySelectorAll('.wmb-thumb').forEach(function (btn) {
		btn.addEventListener('click', function () {
			if (main && btn.dataset.full) { main.src = btn.dataset.full; main.srcset = ''; }
			document.querySelectorAll('.wmb-thumb').forEach(function (b) { b.classList.remove('ring-2','ring-amber-500'); });
			btn.classList.add('ring-2','ring-amber-500');
		});
	});
	document.querySelectorAll('.wmb-cart').forEach(function (form) {
		var input = form.querySelector('input[name="quantity"]');
		var minus = form.querySelector('.wmb-qminus');
		var plus  = form.querySelector('.wmb-qplus');
		if (minus) minus.addEventListener('click', function () {
			input.value = Math.max(1, (parseInt(input.value, 10) || 1) - 1);
		});
		if (plus) plus.addEventListener('click', function () {
			var max = parseInt(input.max, 10);
			var next = (parseInt(input.value, 10) || 1) + 1;
			input.value = max ? Math.min(max, next) : next;
		});
	});
});
</script>

<?php
get_footer( 'shop' );
