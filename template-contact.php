<?php
/**
 * Template Name: Contact Page
 *
 * Branded contact page — hero + optional intro (the_content) + contact-method
 * cards (call / email / Messenger / visit) + support hours + CTA band.
 * Details mirror footer.php; edit there and here together if they change.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Pulled from the Customizer (Theme Options) so the contact page always
// matches the site-wide contact details — no per-template hardcoding.
$phone_display = function_exists( 'isdb_phone' ) ? isdb_phone() : '';
$phone_tel     = preg_replace( '/[^0-9+]/', '', $phone_display ); // digits only; template prefixes tel:
$email         = function_exists( 'isdb_email' ) ? isdb_email() : '';
$fb_url        = function_exists( 'isdb_opt' ) ? isdb_opt( 'isdb_facebook', defined( 'ISDB_FACEBOOK_URL' ) ? ISDB_FACEBOOK_URL : '' ) : '';
$address       = function_exists( 'isdb_address' ) ? isdb_address() : '';

$cards = array(
	array(
		'label' => 'Call Us',
		'value' => $phone_display,
		'href'  => 'tel:' . $phone_tel,
		'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>',
	),
	array(
		'label' => 'Email Us',
		'value' => $email,
		'href'  => 'mailto:' . $email,
		'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>',
	),
	array(
		'label' => 'Message on Facebook',
		'value' => 'facebook.com/waymore.bd',
		'href'  => $fb_url,
		'blank' => true,
		'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>',
	),
	array(
		'label' => 'Visit Us',
		'value' => $address,
		'href'  => 'https://maps.google.com/?q=' . rawurlencode( $address ),
		'blank' => true,
		'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>',
	),
);

$lead = has_excerpt( get_queried_object_id() ) ? get_the_excerpt() : '';
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- Breadcrumb strip (bg = site background) -->
	<section class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 py-2.5 sm:px-6 lg:px-8">
			<nav class="text-[13px] text-slate-500" aria-label="Breadcrumb">
				<div class="flex flex-wrap items-center">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand-primary">Home</a>
					<span class="mx-2 text-slate-300">/</span>
					<span class="text-brand-title"><?php the_title(); ?></span>
				</div>
			</nav>
		</div>
	</section>

	<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

		<!-- Hero -->
		<header class="text-center">
			<span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-primary/10 text-brand-primary">
				<svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
			</span>
			<span class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-brand-primary/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-brand-primary">
				<span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span> We're Here for You
			</span>
			<h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-title sm:text-4xl"><?php the_title(); ?></h1>
			<?php if ( $lead ) : ?>
				<p class="mx-auto mt-3 max-w-xl text-lg text-slate-500"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</header>

		<?php
		while ( have_posts() ) :
			the_post();
			if ( trim( wp_strip_all_tags( get_the_content() ) ) ) :
				?>
				<article class="prose prose-slate mx-auto mt-8 max-w-2xl text-center prose-p:leading-relaxed prose-p:text-brand-body prose-a:text-brand-primary">
					<?php the_content(); ?>
				</article>
				<?php
			endif;
		endwhile;
		?>

		<!-- Contact method cards -->
		<div class="mt-10 grid gap-4 sm:grid-cols-2">
			<?php foreach ( $cards as $card ) : ?>
				<a href="<?php echo esc_url( $card['href'] ); ?>"<?php echo ! empty( $card['blank'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
					class="group flex items-start gap-4 rounded-2xl border border-slate-100 bg-white p-5 transition hover:-translate-y-0.5 hover:border-brand-primary/40 hover:shadow-lg">
					<span class="flex h-12 w-12 flex-none items-center justify-center rounded-xl bg-brand-primary/10 text-brand-primary transition group-hover:bg-brand-primary group-hover:text-white">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><?php echo $card['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg>
					</span>
					<span class="min-w-0">
						<span class="block text-sm font-bold text-brand-title"><?php echo esc_html( $card['label'] ); ?></span>
						<span class="mt-0.5 block break-words text-sm text-brand-body transition group-hover:text-brand-primary"><?php echo esc_html( $card['value'] ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>

		<!-- Support hours -->
		<div class="mt-4 flex flex-col items-center justify-between gap-2 rounded-2xl border border-slate-100 bg-white px-6 py-4 text-center sm:flex-row sm:text-left">
			<span class="flex items-center gap-2 text-sm font-semibold text-brand-title">
				<svg class="h-5 w-5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				Support Hours
			</span>
			<span class="text-sm text-slate-500">Every day · 9:00 AM – 10:00 PM (BST). We usually reply within a few hours.</span>
		</div>

		<!-- CTA band -->
		<div class="mt-12 flex flex-col items-center gap-5 rounded-2xl bg-brand-dark p-8 text-center text-white sm:flex-row sm:text-left">
			<svg class="h-10 w-10 flex-none text-brand-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V5l7-3z"/></svg>
			<div class="flex-1">
				<p class="font-bold">Prefer to shop while you wait?</p>
				<p class="mt-1 text-sm text-white/70">Browse our latest kitchen &amp; home essentials — Cash on Delivery available across Bangladesh.</p>
			</div>
			<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="flex-none rounded-xl bg-brand-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-hover">Shop Now</a>
		</div>

	</div>
</main>

<?php
get_footer();
