<?php
/**
 * Site footer — Way More BD ("Footer Style 3": white background)
 *
 * 4 menu columns (Information · Shop By · Support · Consumer Policy) driven by
 * nav-menu locations with curated fallbacks + a contact/logo column, newsletter,
 * socials, app badges and a payment-gateway strip. Contact details, socials,
 * app links and the newsletter toggle are all managed in the Customizer
 * (Appearance → Customize → Theme Options).
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- ============================ TRUST STRIP ============================ -->
<section class="border-t border-slate-100 bg-white">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
		<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
			<?php
			// Heroicons v2 outline (MIT).
			$promises = array(
				array( 'M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z', __( 'Customer Support', 'isdb-custom' ), __( 'Real humans, quick replies', 'isdb-custom' ) ),
				array( 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z', __( 'Secure Checkout', 'isdb-custom' ), __( '100% SSL encrypted', 'isdb-custom' ) ),
				array( 'M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3', __( 'Easy 7-Day Returns', 'isdb-custom' ), __( 'Hassle-free replacements', 'isdb-custom' ) ),
				array( 'M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z', __( 'Cash on Delivery', 'isdb-custom' ), __( 'Pay only when it arrives', 'isdb-custom' ) ),
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

			<!-- Col 1 — About / Logo / Contact -->
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
					<?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'Your Trusted Kitchen Companion.', 'isdb-custom' ) ); ?>
					<?php esc_html_e( 'Authentic kitchenware, honest pricing, and a promise to make every order right.', 'isdb-custom' ); ?>
				</p>

				<!-- Contact (Customizer → Theme Options → Contact Information) -->
				<ul class="mt-5 space-y-2.5 text-sm text-brand-body">
					<?php if ( isdb_address() ) : ?>
						<li class="flex items-start gap-2.5">
							<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
							<?php echo esc_html( isdb_address() ); ?>
						</li>
					<?php endif; ?>
					<?php if ( isdb_phone() ) : ?>
						<li class="flex items-start gap-2.5">
							<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
							<a href="<?php echo esc_url( isdb_phone_tel() ); ?>" class="hover:text-brand-primary"><?php echo esc_html( isdb_phone() ); ?></a>
						</li>
					<?php endif; ?>
					<?php if ( isdb_email() ) : ?>
						<li class="flex items-start gap-2.5">
							<svg class="mt-0.5 h-4 w-4 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
							<a href="mailto:<?php echo esc_attr( isdb_email() ); ?>" class="break-all hover:text-brand-primary"><?php echo esc_html( isdb_email() ); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			</div>

			<!-- Col 2 — Information (menu: Footer — Information) -->
			<div>
				<h3 class="text-base font-bold text-brand-title"><?php esc_html_e( 'Information', 'isdb-custom' ); ?></h3>
				<?php isdb_footer_menu( 'footer_information' ); ?>
			</div>

			<!-- Col 3 — Shop By (menu: Footer — Shop By) -->
			<div>
				<h3 class="text-base font-bold text-brand-title"><?php esc_html_e( 'Shop By', 'isdb-custom' ); ?></h3>
				<?php isdb_footer_menu( 'footer_shop' ); ?>
			</div>

			<!-- Col 4 — Support (menu: Footer — Support) -->
			<div>
				<h3 class="text-base font-bold text-brand-title"><?php esc_html_e( 'Support', 'isdb-custom' ); ?></h3>
				<?php isdb_footer_menu( 'footer_support' ); ?>
			</div>

			<!-- Col 5 — Consumer Policy (menu) + newsletter + socials -->
			<div>
				<h3 class="text-base font-bold text-brand-title"><?php esc_html_e( 'Consumer Policy', 'isdb-custom' ); ?></h3>
				<?php isdb_footer_menu( 'footer_policy' ); ?>

				<?php if ( isdb_opt_bool( 'isdb_newsletter_enable', true ) ) : ?>
					<h3 class="mt-7 text-sm font-bold uppercase tracking-wide text-brand-title"><?php esc_html_e( 'Newsletter', 'isdb-custom' ); ?></h3>
					<?php
					/*
					 * Newsletter signup. By default the form posts to the current page
					 * (harmless no-op) and exposes two integration points so buyers can
					 * wire Mailchimp / a CRM with zero template edits:
					 *   • filter `isdb_newsletter_action`      → set the form's action URL
					 *   • action `isdb_newsletter_form_fields` → inject hidden fields/nonces
					 */
					$isdb_news_action = apply_filters( 'isdb_newsletter_action', '' );
					?>
					<form class="footer-newsletter mt-3 flex overflow-hidden rounded-card border border-brand-line" method="post" action="<?php echo esc_url( $isdb_news_action ); ?>">
						<label for="wmb-news" class="sr-only"><?php esc_html_e( 'Email', 'isdb-custom' ); ?></label>
						<input id="wmb-news" type="email" name="isdb_newsletter_email" placeholder="<?php esc_attr_e( 'Your email', 'isdb-custom' ); ?>" required class="w-full border-0 bg-brand-bg px-3 py-2.5 text-sm focus:outline-none focus:ring-0" />
						<?php do_action( 'isdb_newsletter_form_fields' ); ?>
						<button type="submit" class="flex-none bg-brand-primary px-4 text-sm font-bold text-white transition hover:bg-brand-hover"><?php esc_html_e( 'Join', 'isdb-custom' ); ?></button>
					</form>
				<?php endif; ?>

				<!-- Socials (Customizer → Theme Options → Social Media; empty = hidden) -->
				<?php $isdb_socials = isdb_social_links(); ?>
				<?php if ( ! empty( $isdb_socials ) ) : ?>
					<ul class="footer-social mt-4 flex gap-2">
						<?php foreach ( $isdb_socials as $s ) : ?>
							<li>
								<a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $s['label'] ); ?>"
									class="flex h-9 w-9 items-center justify-center rounded-card bg-brand-bg text-brand-title transition hover:bg-brand-primary hover:text-white">
									<svg class="h-[17px] w-[17px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="<?php echo esc_attr( $s['path'] ); ?>"/></svg>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- ============================ APP BADGES ============================ -->
	<?php
	$isdb_gplay  = isdb_opt( 'isdb_google_play', '' );
	$isdb_astore = isdb_opt( 'isdb_app_store', '' );
	if ( $isdb_gplay || $isdb_astore ) :
		?>
		<div class="border-t border-slate-100 bg-white">
			<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
				<p class="text-sm font-semibold text-brand-title"><?php esc_html_e( 'Download App on Mobile :', 'isdb-custom' ); ?></p>
				<div class="mt-3 flex flex-wrap gap-3">
					<?php if ( $isdb_gplay ) : ?>
						<a href="<?php echo esc_url( $isdb_gplay ); ?>" class="flex items-center gap-2 rounded bg-black px-4 py-2 text-white transition hover:opacity-90">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3.6 2.3l10.7 9.7-10.7 9.7c-.4-.3-.6-.8-.6-1.4V3.7c0-.6.2-1.1.6-1.4zm12.1 11l2.6 2.4-3.1 1.8-2.6-2.4 3.1-1.8zm0-2.6l-3.1-1.8 2.6-2.4 3.1 1.8-2.6 2.4zM5.4 1.6l9.2 5.3-2.4 2.2L5.4 1.6z"/></svg>
							<span class="leading-tight">
								<span class="block text-[9px] uppercase tracking-wide opacity-80"><?php esc_html_e( 'Get it on', 'isdb-custom' ); ?></span>
								<span class="block text-sm font-semibold"><?php esc_html_e( 'Google Play', 'isdb-custom' ); ?></span>
							</span>
						</a>
					<?php endif; ?>
					<?php if ( $isdb_astore ) : ?>
						<a href="<?php echo esc_url( $isdb_astore ); ?>" class="flex items-center gap-2 rounded bg-black px-4 py-2 text-white transition hover:opacity-90">
							<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M16.4 12.8c0-2.2 1.8-3.3 1.9-3.3-1-1.5-2.6-1.7-3.2-1.7-1.4-.1-2.7.8-3.4.8-.7 0-1.8-.8-2.9-.8-1.5 0-2.9.9-3.7 2.2-1.6 2.7-.4 6.8 1.1 9 .7 1.1 1.6 2.3 2.8 2.3 1.1 0 1.5-.7 2.9-.7 1.3 0 1.7.7 2.9.7 1.2 0 2-1.1 2.7-2.2.9-1.2 1.2-2.4 1.2-2.5 0 0-2.3-.9-2.3-3.8zM14.2 5.6c.6-.7 1-1.7.9-2.7-.9 0-2 .6-2.6 1.3-.6.6-1.1 1.7-.9 2.6 1 .1 2-.5 2.6-1.2z"/></svg>
							<span class="leading-tight">
								<span class="block text-[9px] uppercase tracking-wide opacity-80"><?php esc_html_e( 'Download on the', 'isdb-custom' ); ?></span>
								<span class="block text-sm font-semibold"><?php esc_html_e( 'App Store', 'isdb-custom' ); ?></span>
							</span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

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
					<img src="<?php echo esc_url( $pay_img ); ?>" alt="<?php esc_attr_e( 'Accepted payment methods', 'isdb-custom' ); ?>"
						class="h-auto w-full max-w-[900px]" loading="lazy" decoding="async" />
					<?php if ( $ssl_img ) : ?>
						<img src="<?php echo esc_url( $ssl_img ); ?>" alt="<?php esc_attr_e( 'Secure payment verified', 'isdb-custom' ); ?>"
							class="h-auto max-h-[46px] w-auto flex-none" loading="lazy" decoding="async" />
					<?php endif; ?>
				</div>

			<?php else : ?>
				<?php // No image supplied — fall back to text plates + label + SSL badge. ?>
				<div class="flex flex-col items-start gap-4 lg:flex-row lg:items-center">
					<span class="flex-none text-sm font-bold text-brand-title"><?php esc_html_e( 'Pay With', 'isdb-custom' ); ?></span>

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
						<?php esc_html_e( '256-bit SSL secured', 'isdb-custom' ); ?>
					</span>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- ============================ COPYRIGHT ============================ -->
	<div class="border-t border-slate-100 bg-white">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
			<p class="text-center text-xs text-slate-500">Copyright &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
		</div>
	</div>
</footer>

<!-- Floating cart pill (refreshed via cart fragments) -->
<?php if ( function_exists( 'isdb_floating_cart' ) ) { isdb_floating_cart(); } ?>

<!-- Scroll to top -->
<button id="isdb-top" type="button" aria-label="<?php esc_attr_e( 'Scroll to top', 'isdb-custom' ); ?>"
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
