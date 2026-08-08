<?php
/**
 * Site header — Way More BD ("Header Style 7" layout)
 *
 *  ┌ header-middle ──────────────────────────────────────────────┐
 *  │ [logo]      [ ——— expanded search + orange button ——— ]     │
 *  │                         [Track Order] [Sign In] [Cart ▾]    │
 *  └─────────────────────────────────────────────────────────────┘
 *  ┌ header-bottom (bg: #041f1e) ────────────────────────────────┐
 *  │ [All Categories ▾]  Home  Shop  Categories …      [hotline] │
 *  └─────────────────────────────────────────────────────────────┘
 *
 * Icons: Heroicons v2 outline (MIT), inlined. Mini-cart drawer stays AJAX-driven.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

$cart_count  = isdb_cart_count();
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$is_logged   = is_user_logged_in();
?>
<!doctype html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-brand-bg text-brand-body antialiased' ); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<header
	x-data="{ mobile:false, search:false, cartOpen:false }"
	x-effect="document.body.style.overflow = (cartOpen || mobile) ? 'hidden' : ''"
	@keydown.escape.window="cartOpen=false; mobile=false"
	@isdb-open-cart.window="cartOpen = true"
	class="wmb-header sticky top-0 z-50 bg-brand-bg shadow-sm">

	<!-- ============================ HEADER MIDDLE ============================ -->
	<div class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
			<div class="flex h-[68px] items-center gap-4 lg:h-20 lg:gap-8">

				<!-- Mobile menu -->
				<button @click="mobile = !mobile" class="-ml-1 p-2 text-brand-title lg:hidden" aria-label="Menu">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
				</button>

				<!-- Logo (max-w 170 / max-h 48, per their spec) -->
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="wmb-logo flex flex-none items-center gap-2.5">
					<?php
					$logo_id = get_theme_mod( 'custom_logo' );
					if ( $logo_id ) {
						echo wp_get_attachment_image(
							$logo_id,
							'full',
							false,
							array(
								'class' => 'block h-auto w-auto',
								'style' => 'max-width:170px;max-height:48px;height:auto;',
								'alt'   => get_bloginfo( 'name' ),
							)
						);
					} else {
						?>
						<span class="flex h-10 w-10 flex-none items-center justify-center rounded bg-brand-primary text-white">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
						</span>
						<span class="hidden flex-col leading-none sm:flex">
							<span class="text-lg font-extrabold tracking-tight text-brand-title"><?php bloginfo( 'name' ); ?></span>
							<span class="mt-0.5 text-[11px] font-medium text-slate-500"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
						</span>
						<?php
					}
					?>
				</a>

				<!-- Expanded search (desktop) — live type-ahead -->
				<div class="isdb-search relative hidden flex-1 lg:block">
					<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form-main" autocomplete="off">
						<label for="wmb-search" class="sr-only">Search products</label>
						<div class="flex overflow-hidden rounded-full border border-brand-line bg-white focus-within:border-brand-primary">
							<input id="wmb-search" type="search" name="s" value="<?php echo get_search_query(); ?>"
								placeholder="Search in&hellip;"
								class="isdb-search-input w-full border-0 bg-transparent px-5 py-3 text-sm text-brand-title placeholder:text-slate-400 focus:outline-none focus:ring-0" />
							<input type="hidden" name="post_type" value="product" />
							<button type="submit" class="flex flex-none items-center px-5 text-brand-title transition hover:text-brand-primary" aria-label="Search">
								<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
							</button>
						</div>
					</form>
					<!-- Results dropdown -->
					<ul class="isdb-search-box live_search_box hidden absolute left-0 right-0 top-[calc(100%+6px)] z-[70] m-0 max-h-[400px] list-none overflow-y-auto rounded-lg border border-[#e0e0e0] bg-white p-0 shadow-lg"></ul>
				</div>

				<!-- Right actions -->
				<div class="ml-auto flex flex-none items-center gap-1 sm:gap-2 lg:gap-5">

					<!-- Mobile search toggle -->
					<button @click="search = !search" class="p-2 text-brand-title lg:hidden" aria-label="Search">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
					</button>

					<!-- Track Order -->
					<a href="<?php echo esc_url( isdb_track_order_url() ); ?>" class="hidden items-center gap-2 text-brand-title transition hover:text-brand-primary lg:flex">
						<svg class="h-6 w-6 flex-none" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h.375c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9H3.375c-.621 0-1.125.504-1.125 1.125v3.026"/></svg>
						<span class="flex flex-col leading-tight">
							<span class="text-[11px] text-slate-500">Track</span>
							<span class="text-sm font-semibold">Order</span>
						</span>
					</a>

					<!-- Sign in / Account -->
					<a href="<?php echo esc_url( $account_url ); ?>" class="hidden flex-col items-center gap-0.5 text-brand-title transition hover:text-brand-primary sm:flex">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
						<span class="hidden text-[11px] font-medium leading-tight lg:block"><?php echo $is_logged ? 'Account' : 'Sign In'; ?></span>
					</a>

					<!-- Wishlist -->
					<a href="<?php echo esc_url( add_query_arg( 'wishlist', '1', $account_url ) ); ?>" class="hidden flex-col items-center gap-0.5 text-brand-title transition hover:text-brand-primary sm:flex" aria-label="Wishlist">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
						<span class="hidden text-[11px] font-medium leading-tight lg:block">Wishlist</span>
					</a>

					<!-- Cart -->
					<button @click="cartOpen = true" class="cart-dropdown flex flex-col items-center gap-0.5 text-brand-title transition hover:text-brand-primary" aria-label="Open cart">
						<span class="relative">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
							<span class="wmb-cart-count absolute -right-2.5 -top-2 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-brand-primary px-1 text-[10px] font-bold text-white <?php echo $cart_count > 0 ? '' : 'hidden'; ?>">
								<?php echo esc_html( $cart_count ); ?>
							</span>
						</span>
						<span class="hidden text-[11px] font-medium leading-tight lg:block">Cart</span>
					</button>

					<?php // "More" removed on desktop — the full nav bar below already lists every menu item. ?>
				</div>
			</div>

			<!-- Mobile search bar (live type-ahead) -->
			<div x-show="search" x-cloak class="isdb-search relative pb-3 lg:hidden">
				<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" autocomplete="off">
					<div class="flex overflow-hidden rounded-full border border-brand-line bg-white focus-within:border-brand-primary">
						<label for="wmb-search-m" class="sr-only">Search products</label>
						<input id="wmb-search-m" type="search" name="s" placeholder="Search in&hellip;" class="isdb-search-input w-full border-0 bg-transparent px-4 py-2.5 text-sm focus:outline-none focus:ring-0" />
						<input type="hidden" name="post_type" value="product" />
						<button type="submit" class="flex-none px-4 text-brand-title" aria-label="Search">
							<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
						</button>
					</div>
				</form>
				<ul class="isdb-search-box live_search_box hidden absolute left-0 right-0 top-full z-[70] m-0 max-h-[60vh] list-none overflow-y-auto rounded-lg border border-[#e0e0e0] bg-white p-0 shadow-lg"></ul>
			</div>
		</div>
	</div>

	<!-- ============================ HEADER BOTTOM (dark) ============================ -->
	<div class="header-bottom hidden bg-brand-dark lg:block">
		<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
			<div class="flex h-12 items-center justify-between">
				<?php isdb_primary_nav( 'desktop' ); ?>

				<a href="tel:+8801868662477" class="flex items-center gap-2 text-sm font-semibold text-white/90 transition hover:text-brand-primary">
					<svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
					+880 1868 662477
				</a>
			</div>
		</div>
	</div>

	<!-- ============================ LEFT OFF-CANVAS MENU (mobile / tablet) ============================ -->
	<div x-show="mobile" x-cloak class="fixed inset-0 z-[80] lg:hidden" role="dialog" aria-modal="true" aria-label="Menu">
		<div x-show="mobile" x-transition.opacity @click="mobile=false" class="absolute inset-0 bg-slate-900/50"></div>

		<div x-show="mobile"
			x-transition:enter="transition ease-out duration-300"
			x-transition:enter-start="-translate-x-full"
			x-transition:enter-end="translate-x-0"
			x-transition:leave="transition ease-in duration-200"
			x-transition:leave-start="translate-x-0"
			x-transition:leave-end="-translate-x-full"
			class="absolute left-0 top-0 flex h-full w-[85%] max-w-xs flex-col bg-white shadow-2xl">

			<!-- User header (orange) -->
			<div class="flex items-center gap-3 bg-brand-primary px-5 py-4 text-white">
				<span class="flex h-11 w-11 flex-none items-center justify-center rounded-full bg-white/20">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
				</span>
				<div class="min-w-0 flex-1">
					<?php if ( $is_logged ) : $current_user = wp_get_current_user(); ?>
						<p class="truncate text-sm font-bold uppercase leading-tight"><?php echo esc_html( $current_user->display_name ); ?></p>
						<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="text-xs text-white/80 transition hover:text-white">Logout</a>
					<?php else : ?>
						<p class="text-sm font-bold leading-tight">Welcome</p>
						<a href="<?php echo esc_url( $account_url ); ?>" class="text-xs text-white/80 transition hover:text-white">Sign In / Register</a>
					<?php endif; ?>
				</div>
				<button @click="mobile=false" class="-mr-1 flex-none rounded-full p-1.5 text-white/80 transition hover:bg-white/15 hover:text-white" aria-label="Close menu">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
				</button>
			</div>

			<div class="flex-1 overflow-y-auto">

				<!-- Quick action tiles: Account / Wishlist / Cart -->
				<div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100 text-center">
					<a href="<?php echo esc_url( $account_url ); ?>" class="flex flex-col items-center gap-1 px-2 py-3.5 text-brand-title transition hover:bg-brand-soft">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
						<span class="text-[11px] font-semibold"><?php echo $is_logged ? 'Account' : 'Sign In'; ?></span>
					</a>
					<a href="<?php echo esc_url( add_query_arg( 'wishlist', '1', $account_url ) ); ?>" class="flex flex-col items-center gap-1 px-2 py-3.5 text-brand-title transition hover:bg-brand-soft">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
						<span class="text-[11px] font-semibold">Wishlist</span>
					</a>
					<button @click="mobile=false; cartOpen=true" class="flex flex-col items-center gap-1 px-2 py-3.5 text-brand-title transition hover:bg-brand-soft" aria-label="Open cart">
						<span class="relative">
							<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
							<span class="wmb-cart-count absolute -right-2.5 -top-2 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-brand-primary px-1 text-[10px] font-bold text-white <?php echo $cart_count > 0 ? '' : 'hidden'; ?>"><?php echo esc_html( $cart_count ); ?></span>
						</span>
						<span class="text-[11px] font-semibold">Cart</span>
					</button>
				</div>

				<!-- Track Order (prominent) -->
				<a href="<?php echo esc_url( isdb_track_order_url() ); ?>" class="flex items-center gap-3 border-b border-slate-100 px-5 py-3.5 text-sm font-semibold text-brand-title transition hover:bg-brand-soft hover:text-brand-primary">
					<svg class="h-5 w-5 flex-none text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
					Track Order
				</a>

				<!-- Categories -->
				<p class="px-5 pb-1 pt-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">Categories</p>
				<?php isdb_primary_nav( 'mobile' ); ?>

				<!-- Quick links -->
				<p class="px-5 pb-1 pt-4 text-[11px] font-bold uppercase tracking-wider text-slate-400">Quick Links</p>
				<ul class="flex flex-col gap-0.5 p-2 pb-6 [&_a]:flex [&_a]:items-center [&_a]:gap-2.5 [&_a]:rounded-lg [&_a]:px-4 [&_a]:py-2.5 [&_a]:text-[14px] [&_a]:font-medium [&_a]:text-brand-body [&_a:hover]:bg-brand-soft [&_a:hover]:text-brand-primary">
					<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About Us</a></li>
					<li><a href="<?php echo esc_url( add_query_arg( 'wishlist', '1', $account_url ) ); ?>">Wishlist</a></li>
					<li><a href="<?php echo esc_url( home_url( '/faqs/' ) ); ?>">FAQs</a></li>
				</ul>
			</div>
		</div>
	</div>

	<!-- ============================ MINI-CART DRAWER ============================ -->
	<div x-show="cartOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" aria-label="Shopping cart">
		<div x-show="cartOpen" x-transition.opacity @click="cartOpen=false" class="absolute inset-0 bg-slate-900/50"></div>

		<div x-show="cartOpen"
			x-transition:enter="transition ease-out duration-300"
			x-transition:enter-start="translate-x-full"
			x-transition:enter-end="translate-x-0"
			x-transition:leave="transition ease-in duration-200"
			x-transition:leave-start="translate-x-0"
			x-transition:leave-end="translate-x-full"
			class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-white shadow-2xl">

			<div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
				<h2 class="flex items-center gap-2 text-base font-bold text-brand-title">
					<svg class="h-5 w-5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
					Shopping Cart
				</h2>
				<button @click="cartOpen=false" class="rounded p-1.5 text-slate-400 transition hover:bg-stone-100 hover:text-brand-title" aria-label="Close cart">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
				</button>
			</div>

			<div class="flex-1 overflow-y-auto px-5 py-4">
				<div class="widget_shopping_cart_content">
					<?php if ( function_exists( 'woocommerce_mini_cart' ) ) { woocommerce_mini_cart(); } ?>
				</div>
			</div>
		</div>
	</div>
</header>
