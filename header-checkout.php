<?php
/**
 * Isolated checkout header — Way More BD.
 * Logo + "Secure Checkout" + a single safe exit back to cart. No nav/search/cart.
 * Loaded via get_header('checkout') from page.php on the checkout step.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-stone-50 text-slate-900 antialiased' ); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<header class="border-b border-slate-100 bg-white">
	<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
		<div class="flex h-16 items-center justify-between">

			<!-- Safe exit (not a distraction — a reassurance) -->
			<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-900">
				<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
				<span class="hidden sm:inline">Return to cart</span>
			</a>

			<!-- Logo (centered anchor of the funnel) -->
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
					<span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-500 font-black text-slate-900">W</span>
					<span class="text-lg font-extrabold tracking-tight text-slate-900"><?php bloginfo( 'name' ); ?></span>
				<?php endif; ?>
			</a>

			<!-- Security signal -->
			<span class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700">
				<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1l7 3v5c0 4-3 7.5-7 9-4-1.5-7-5-7-9V4l7-3z" clip-rule="evenodd"/></svg>
				<span class="hidden sm:inline">Secure Checkout</span>
			</span>
		</div>
	</div>
</header>
