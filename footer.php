<?php
/**
 * Site footer — Way More BD ("Footer Style 3": white background)
 *
 * 4 columns: About/Logo · Information · Shop By · Consumer Policy
 * + newsletter, socials, and a bottom payment-gateway strip.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$footer_cats = function_exists( 'isdb_top_categories' ) ? isdb_top_categories( 5 ) : array();
?>

<!-- ============================ TRUST STRIP ============================ -->
<section class="border-t border-slate-100 bg-white">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
		<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
			<?php
			// Heroicons v2 outline (MIT).
			$promises = array(
				array( 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z', 'Customer Support', 'Real humans, quick replies' ),
				array( 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', 'Secure Checkout', '100% SSL encrypted' ),
				array( 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3', 'Easy 7-Day Returns', 'Hassle-free replacements' ),
				array( 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z', 'Cash on Delivery', 'Pay only when it arrives' ),
			);
			foreach ( $promises as $p ) : ?>
				<div class="feature-item flex items-center gap-3 rounded-card border border-slate-100 p-3.5">
					<span class="flex h-10 w-10 flex-none items-center justify-center rounded-card bg-brand-soft text-brand-primary">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="<?php echo esc_attr( $p[0] ); ?>"/></svg>
					</span>
					<div class="min-w-0">
						<p class="truncate text-[13px] font-bold text-brand-title"><?php echo esc_html( $p[1] ); ?></p>
						<p class="truncate text-[11px] text-slate-500"><?php echo esc_html( $p[2] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============================ MAIN FOOTER (white) ============================ -->
<footer class="footer style-3 border-t border-slate-100 bg-white">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
		<div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-5">

			<!-- Col 1 — About / Logo -->
			<div>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wmb-logo flex items-center gap-2.5">
					<?php
					$logo_id = get_theme_mod( 'custom_logo' );
					if ( $logo_id ) {
						echo wp_get_attachment_image( $logo_id, 'full', false, array(
							'class' => 'block h-auto w-auto',
							'style' => 'max-width:170px;max-height:48px;height:auto;',
							'alt'   => get_bloginfo( 'name' ),
						) );
					} else {
						?>
						<span class="flex h-10 w-10 items-center justify-center rounded bg-brand-primary text-white">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
						</span>
						<span class="text-lg font-extrabold text-brand-title"><?php bloginfo( 'name' ); ?></span>
						<?php
					}
					?>
				</a>
				<p class="mt-4 text-sm leading-relaxed text-brand-body">
					<?php echo esc_html( get_bloginfo( 'description' ) ?: 'Your Trusted Kitchen Companion.' ); ?>
					Authentic kitchenware, honest pricing, and a promise to make every order right.
				</p>

				<!-- Contact -->
				<ul class="mt-5 space-y-2.5 text-sm text-brand-body">
					<li class="flex items-start gap-2.5">
						<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
						New Market, Dhaka, Bangladesh
					</li>
					<li class="flex items-start gap-2.5">
						<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
						<a href="tel:+8801868662477" class="hover:text-brand-primary">+880 1868 662477</a>
					</li>
					<li class="flex items-start gap-2.5">
						<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
						<a href="mailto:info.waymore.bd@gmail.com" class="break-all hover:text-brand-primary">info.waymore.bd@gmail.com</a>
					</li>
					<li class="flex items-start gap-2.5">
						<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M14 9h3V6h-3c-1.7 0-3 1.3-3 3v2H9v3h2v7h3v-7h2.5l.5-3h-3V9.5c0-.3.2-.5.5-.5H14z"/></svg>
						<a href="<?php echo esc_url( ISDB_FACEBOOK_URL ); ?>" target="_blank" rel="noopener noreferrer" class="hover:text-brand-primary">facebook.com/waymore.bd</a>
					</li>
				</ul>
			</div>

			<!-- Col 2 — Information -->
			<div>
				<h3 class="text-base font-bold text-brand-title">Information</h3>
				<ul class="mt-4 space-y-2.5 text-sm">
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About us</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact us</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/company-information/' ) ); ?>">Company Information</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/our-stories/' ) ); ?>">Our Stories</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>">Terms &amp; Conditions</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Careers</a></li>
				</ul>
			</div>

			<!-- Col 3 — Shop By -->
			<div>
				<h3 class="text-base font-bold text-brand-title">Shop By</h3>
				<ul class="mt-4 space-y-2.5 text-sm">
					<?php if ( ! empty( $footer_cats ) ) : ?>
						<?php foreach ( $footer_cats as $fc ) : ?>
							<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( get_term_link( $fc ) ); ?>"><?php echo esc_html( $fc->name ); ?></a></li>
						<?php endforeach; ?>
					<?php endif; ?>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( $shop_url ); ?>">All Products</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $shop_url ) ); ?>">On Sale</a></li>
				</ul>
			</div>

			<!-- Col 4 — Support -->
			<div>
				<h3 class="text-base font-bold text-brand-title">Support</h3>
				<ul class="mt-4 space-y-2.5 text-sm">
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/support/' ) ); ?>">Support Center</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/how-to-order/' ) ); ?>">How to Order</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( $account_url ); ?>">Order Tracking</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/payment/' ) ); ?>">Payment</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/shipping-policy/' ) ); ?>">Shipping</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">FAQ</a></li>
				</ul>
			</div>

			<!-- Col 5 — Consumer Policy + newsletter -->
			<div>
				<h3 class="text-base font-bold text-brand-title">Consumer Policy</h3>
				<ul class="mt-4 space-y-2.5 text-sm">
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/return-refund-policy/' ) ); ?>">Happy Return</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/return-refund-policy/' ) ); ?>">Refund Policy</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/exchange/' ) ); ?>">Exchange</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/cancellation/' ) ); ?>">Cancellation</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( home_url( '/pre-order/' ) ); ?>">Pre-Order</a></li>
					<li><a class="text-brand-body transition hover:text-brand-primary" href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $shop_url ) ); ?>">Extra Discount</a></li>
				</ul>

				<h3 class="mt-7 text-sm font-bold uppercase tracking-wide text-brand-title">Newsletter</h3>
				<form class="footer-newsletter mt-3 flex overflow-hidden rounded-card border border-brand-line" onsubmit="return false;">
					<label for="wmb-news" class="sr-only">Email</label>
					<input id="wmb-news" type="email" placeholder="Your email" class="w-full border-0 bg-brand-bg px-3 py-2.5 text-sm focus:outline-none focus:ring-0" />
					<button class="flex-none bg-brand-primary px-4 text-sm font-bold text-white transition hover:bg-brand-hover">Join</button>
				</form>

				<!-- Socials -->
				<ul class="footer-social mt-4 flex gap-2">
					<?php
					// Simple Icons brand glyphs (single filled paths, render crisp at any size).
					$socials = array(
						array( defined( 'ISDB_FACEBOOK_URL' ) ? ISDB_FACEBOOK_URL : 'https://www.facebook.com/waymore.bd', 'Facebook', 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z' ),
						array( 'https://www.instagram.com/', 'Instagram', 'M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0Zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227a3.81 3.81 0 0 1-.9 1.382 3.744 3.744 0 0 1-1.38.896c-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421a3.716 3.716 0 0 1-1.379-.9 3.644 3.644 0 0 1-.9-1.38c-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03Zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162ZM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4Zm7.846-10.405a1.441 1.441 0 0 1-2.88 0 1.44 1.44 0 0 1 2.88 0Z' ),
						array( 'https://www.youtube.com/', 'YouTube', 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814ZM9.545 15.568V8.432L15.818 12l-6.273 3.568Z' ),
					);
					foreach ( $socials as $s ) : ?>
						<li>
							<a href="<?php echo esc_url( $s[0] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $s[1] ); ?>"
								class="flex h-9 w-9 items-center justify-center rounded-card bg-brand-bg text-brand-title transition hover:bg-brand-primary hover:text-white">
								<svg class="h-[17px] w-[17px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="<?php echo esc_attr( $s[2] ); ?>"/></svg>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

	<!-- ============================ APP BADGES ============================ -->
	<div class="border-t border-slate-100 bg-white">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
			<p class="text-sm font-semibold text-brand-title">Download App on Mobile :</p>
			<div class="mt-3 flex flex-wrap gap-3">
				<a href="#" class="flex items-center gap-2 rounded bg-black px-4 py-2 text-white transition hover:opacity-90">
					<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3.6 2.3l10.7 9.7-10.7 9.7c-.4-.3-.6-.8-.6-1.4V3.7c0-.6.2-1.1.6-1.4zm12.1 11l2.6 2.4-3.1 1.8-2.6-2.4 3.1-1.8zm0-2.6l-3.1-1.8 2.6-2.4 3.1 1.8-2.6 2.4zM5.4 1.6l9.2 5.3-2.4 2.2L5.4 1.6z"/></svg>
					<span class="leading-tight">
						<span class="block text-[9px] uppercase tracking-wide opacity-80">Get it on</span>
						<span class="block text-sm font-semibold">Google Play</span>
					</span>
				</a>
				<a href="#" class="flex items-center gap-2 rounded bg-black px-4 py-2 text-white transition hover:opacity-90">
					<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M16.4 12.8c0-2.2 1.8-3.3 1.9-3.3-1-1.5-2.6-1.7-3.2-1.7-1.4-.1-2.7.8-3.4.8-.7 0-1.8-.8-2.9-.8-1.5 0-2.9.9-3.7 2.2-1.6 2.7-.4 6.8 1.1 9 .7 1.1 1.6 2.3 2.8 2.3 1.1 0 1.5-.7 2.9-.7 1.3 0 1.7.7 2.9.7 1.2 0 2-1.1 2.7-2.2.9-1.2 1.2-2.4 1.2-2.5 0 0-2.3-.9-2.3-3.8zM14.2 5.6c.6-.7 1-1.7.9-2.7-.9 0-2 .6-2.6 1.3-.6.6-1.1 1.7-.9 2.6 1 .1 2-.5 2.6-1.2z"/></svg>
					<span class="leading-tight">
						<span class="block text-[9px] uppercase tracking-wide opacity-80">Download on the</span>
						<span class="block text-sm font-semibold">App Store</span>
					</span>
				</a>
			</div>
		</div>
	</div>

	<!-- ============================ PAYMENT GRID ============================ -->
	<div class="border-t border-slate-100 bg-white">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
			<?php
			$pay_img = function_exists( 'isdb_payment_image_url' ) ? isdb_payment_image_url() : '';
			$ssl_img = function_exists( 'isdb_ssl_badge_url' ) ? isdb_ssl_badge_url() : '';
			?>
			<?php if ( $pay_img ) : ?>
				<?php
				/*
				 * A supplied payment strip usually already contains its own
				 * "Pay With" label, the gateway logos AND the verified badge.
				 * So render the image alone — adding our label/SSL badge here
				 * duplicated both. Set ISDB_SSL_BADGE_IMAGE only if your strip
				 * genuinely lacks the badge.
				 */
				?>
				<div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
					<img src="<?php echo esc_url( $pay_img ); ?>" alt="Accepted payment methods"
						class="h-auto w-full max-w-[900px]" loading="lazy" decoding="async" />
					<?php if ( $ssl_img ) : ?>
						<img src="<?php echo esc_url( $ssl_img ); ?>" alt="Secure payment verified"
							class="h-auto max-h-[46px] w-auto flex-none" loading="lazy" decoding="async" />
					<?php endif; ?>
				</div>

			<?php else : ?>
				<?php // No image supplied — fall back to text plates + label + SSL badge. ?>
				<div class="flex flex-col items-start gap-4 lg:flex-row lg:items-center">
					<span class="flex-none text-sm font-bold text-brand-title">Pay With</span>

					<div class="min-w-0 flex-1 lg:border-x lg:border-slate-200 lg:px-4">
						<div class="flex flex-wrap gap-2">
							<?php
							$gateways = array(
								'VISA', 'Mastercard', 'AMEX', 'BRAC Bank', 'DBBL Nexus', 'City Touch',
								'Bank Asia', 'AB Direct', 'FastCash', 'MTB', 'Nagad', 'Rocket',
								'M Cash', 'MyCash', 'T-Cash', 'bKash', 'Upay', 'iPay', 'OK Wallet', 'Dmoney',
							);
							foreach ( $gateways as $g ) : ?>
								<span class="flex h-9 min-w-[62px] items-center justify-center rounded border border-slate-200 bg-white px-2 text-[9px] font-bold uppercase tracking-tight text-brand-title">
									<?php echo esc_html( $g ); ?>
								</span>
							<?php endforeach; ?>
						</div>
					</div>

					<span class="inline-flex flex-none items-center gap-1.5 text-[11px] font-medium text-slate-500">
						<svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
						256-bit SSL secured
					</span>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- ============================ COPYRIGHT ============================ -->
	<div class="border-t border-slate-100 bg-white">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<p class="text-center text-xs text-slate-500">Copyright &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
		</div>
	</div>
</footer>

<!-- Floating cart pill (refreshed via cart fragments) -->
<?php if ( function_exists( 'isdb_floating_cart' ) ) { isdb_floating_cart(); } ?>

<!-- Scroll to top -->
<button id="isdb-top" type="button" aria-label="Scroll to top"
	class="fixed bottom-6 right-6 z-40 hidden h-11 w-11 items-center justify-center rounded-full bg-brand-primary text-white shadow-lg transition hover:bg-brand-hover">
	<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/></svg>
</button>
<script>
(function () {
	var btn = document.getElementById('isdb-top');
	if (!btn) { return; }
	function toggle() {
		if (window.scrollY > 400) { btn.classList.remove('hidden'); btn.classList.add('flex'); }
		else { btn.classList.add('hidden'); btn.classList.remove('flex'); }
	}
	window.addEventListener('scroll', toggle, { passive: true });
	btn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
	toggle();
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
