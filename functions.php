<?php
/**
 * Way More BD — ISDB Custom theme bootstrap.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'ISDB_VERSION' ) ) {
	define( 'ISDB_VERSION', '1.0.4' );
}

/*
 * Free-shipping threshold (store currency). Drives the mini-cart / cart
 * "You're ৳X away from free shipping" goal-gradient bar.
 * 0 = disabled/hidden (we never show an invented number). Set this to your
 * REAL free-shipping minimum once your shipping zone is configured.
 */
if ( ! defined( 'ISDB_FREE_SHIP_THRESHOLD' ) ) {
	// ⚠️ Set this to YOUR real free-shipping minimum, AND configure a matching
	// WooCommerce → Settings → Shipping → Free Shipping (min order = this value),
	// otherwise the bar promises free shipping you don't actually offer. 0 = off.
	define( 'ISDB_FREE_SHIP_THRESHOLD', 1000 );
}

/** Official Way More BD social / contact endpoints. */
if ( ! defined( 'ISDB_FACEBOOK_URL' ) ) {
	define( 'ISDB_FACEBOOK_URL', 'https://www.facebook.com/waymore.bd' );
}

/**
 * Payment-methods strip image.
 *
 * Drop your own combined gateway image at:
 *     wp-content/themes/isdb-custom/assets/img/payments.png   (or .webp/.jpg)
 * …or point ISDB_PAYMENT_IMAGE at a Media Library URL:
 *     define( 'ISDB_PAYMENT_IMAGE', 'https://waymorebd.com/wp-content/uploads/…' );
 *
 * When no image is found the footer falls back to text plates, so the strip
 * never renders as a broken image.
 *
 * @return string Image URL, or '' when none is available.
 */
function isdb_payment_image_url() {
	if ( defined( 'ISDB_PAYMENT_IMAGE' ) && ISDB_PAYMENT_IMAGE ) {
		return ISDB_PAYMENT_IMAGE;
	}
	foreach ( array( 'payment.png', 'payments.png', 'payment.webp', 'payments.webp', 'payment.jpg', 'payments.jpg', 'payment.svg', 'payments.svg' ) as $file ) {
		if ( file_exists( get_theme_file_path( 'assets/img/' . $file ) ) ) {
			return get_theme_file_uri( 'assets/img/' . $file );
		}
	}
	return '';
}

/** Optional "Verified by …" badge image, same lookup rules. */
function isdb_ssl_badge_url() {
	if ( defined( 'ISDB_SSL_BADGE_IMAGE' ) && ISDB_SSL_BADGE_IMAGE ) {
		return ISDB_SSL_BADGE_IMAGE;
	}
	foreach ( array( 'ssl-badge.png', 'ssl-badge.webp', 'ssl-badge.svg' ) as $file ) {
		if ( file_exists( get_theme_file_path( 'assets/img/' . $file ) ) ) {
			return get_theme_file_uri( 'assets/img/' . $file );
		}
	}
	return '';
}

/* ------------------------------------------------------------------ *
 * 1. THEME SUPPORT
 * ------------------------------------------------------------------ */
add_action( 'after_setup_theme', function () {

	// Load translations from /languages (ships with isdb-custom.pot).
	load_theme_textdomain( 'isdb-custom', get_template_directory() . '/languages' );

	// Hand template control to this theme (REQUIRED for WC overrides).
	add_theme_support( 'woocommerce' );

	// Native WooCommerce gallery UX — no plugin needed.
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Standard WP supports.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'primary'            => __( 'Primary Menu', 'isdb-custom' ),
		'footer_information' => __( 'Footer — Information', 'isdb-custom' ),
		'footer_shop'        => __( 'Footer — Shop By', 'isdb-custom' ),
		'footer_support'     => __( 'Footer — Support', 'isdb-custom' ),
		'footer_policy'      => __( 'Footer — Consumer Policy', 'isdb-custom' ),
	) );
} );

/* ------------------------------------------------------------------ *
 * 2. HPOS (Custom Order Tables) COMPATIBILITY
 *    This install has woocommerce_custom_orders_table_enabled = yes.
 * ------------------------------------------------------------------ */
add_action( 'before_woocommerce_init', function () {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
			'custom_order_tables',
			__FILE__,
			true
		);
	}
} );

/* ------------------------------------------------------------------ *
 * 3. ASSETS — Tailwind + Alpine + theme JS
 * ------------------------------------------------------------------ */
add_action( 'wp_enqueue_scripts', function () {

	/*
	 * TAILWIND CSS (production)
	 * -------------------------
	 * Compiled + minified from assets/css/tailwind.css via `npm run build`
	 * (see package.json / tailwind.config.js). Rebuild whenever you add new
	 * utility classes to any .php template. Versioned by file mtime so browsers
	 * pick up rebuilds without a manual cache bust.
	 */
	// Brand typeface — Open Sans (matches the design system).
	// Self-hosted (no Google CDN): GDPR-safe, offline-safe, marketplace-compliant.
	$isdb_font_dep = array();
	$fonts_css     = get_theme_file_path( 'assets/css/fonts.css' );
	if ( file_exists( $fonts_css ) ) {
		wp_enqueue_style( 'isdb-fonts', get_theme_file_uri( 'assets/css/fonts.css' ), array(), (string) filemtime( $fonts_css ) );
		$isdb_font_dep = array( 'isdb-fonts' );
	}

	$app_css = get_theme_file_path( 'assets/css/app.css' );
	if ( file_exists( $app_css ) ) {
		wp_enqueue_style( 'isdb-tailwind', get_theme_file_uri( 'assets/css/app.css' ), $isdb_font_dep, (string) filemtime( $app_css ) );
	}

	// Theme header block (x-cloak, FOUC guard, cart carousel + qty CSS).
	// Version off filemtime so edits to style.css always bust the browser cache
	// (ISDB_VERSION is static and would otherwise serve a stale stylesheet).
	$style_css     = get_stylesheet_directory() . '/style.css';
	$style_version = file_exists( $style_css ) ? (string) filemtime( $style_css ) : ISDB_VERSION;
	wp_enqueue_style( 'isdb-style', get_stylesheet_uri(), array( 'isdb-tailwind' ), $style_version );

	// ALPINE.JS (product tabs, gallery reactivity). Bundled locally (no CDN);
	// `defer` is required by Alpine. Falls back gracefully if the file is absent.
	$alpine_js = get_theme_file_path( 'assets/js/alpine.min.js' );
	if ( file_exists( $alpine_js ) ) {
		wp_enqueue_script( 'alpinejs', get_theme_file_uri( 'assets/js/alpine.min.js' ), array(), '3.14.1', array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}

	// Theme JS (only loaded if the file exists — safe on fresh scaffold).
	$app_js = get_theme_file_path( 'assets/js/app.js' );
	if ( file_exists( $app_js ) ) {
		wp_enqueue_script( 'isdb-main', get_theme_file_uri( 'assets/js/app.js' ), array( 'jquery' ), ISDB_VERSION, true );
	}

	// Ensure WC cart-fragments script is present so the header badge updates via AJAX.
	if ( function_exists( 'is_woocommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
}, 20 );

/* ------------------------------------------------------------------ *
 * 4. STRIP DEFAULT WOOCOMMERCE CSS  (Tailwind owns all styling)
 * ------------------------------------------------------------------ */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/* ------------------------------------------------------------------ *
 * 4b. STAY-ON-PAGE ADD TO CART
 *     Force AJAX add-to-cart and never redirect to the Cart page after an
 *     add, so shoppers can keep adding products from the listing. A toast
 *     (isdb-qty inline JS + .wmb-toast in style.css) confirms each add in
 *     place of WooCommerce's full-page "added to cart" notice. Filtering the
 *     option getters overrides the WooCommerce → Settings → Products values
 *     at runtime (non-destructive — nothing is written to the DB).
 * ------------------------------------------------------------------ */
add_filter( 'option_woocommerce_enable_ajax_add_to_cart', function () { return 'yes'; } );
add_filter( 'option_woocommerce_cart_redirect_after_add', function () { return 'no'; } );

/*
 * No standalone Cart page — the slide-over drawer IS the cart (like the
 * reference store). Anyone who still lands on /cart/ (direct URL, an old link,
 * order-again, etc.) is sent to Checkout; an empty cart goes to the shop. The
 * drawer's "View Cart" link is removed in cart/mini-cart.php.
 */
add_action( 'template_redirect', function () {
	if ( is_admin() || ! function_exists( 'is_cart' ) ) {
		return;
	}
	if ( is_cart() || is_page( 'cart' ) ) {
		$empty = ! ( function_exists( 'WC' ) && WC()->cart ) || WC()->cart->is_empty();
		if ( $empty ) {
			$shop = wc_get_page_permalink( 'shop' );
			$dest = $shop ? $shop : home_url( '/' );
		} else {
			$dest = wc_get_checkout_url();
		}
		wp_safe_redirect( $dest );
		exit;
	}
}, 5 );

/* ------------------------------------------------------------------ *
 * 5. CONTENT WRAPPERS
 *    Remove WC's default sidebar + open/close wrappers; we render our
 *    own <main> inside header.php/footer.php + the templates.
 * ------------------------------------------------------------------ */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

add_action( 'woocommerce_before_main_content', function () {
	echo '<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">';
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div>';
}, 10 );

/* ------------------------------------------------------------------ *
 * 6. CART COUNT FRAGMENT  — live-updates the header badge after AJAX add
 * ------------------------------------------------------------------ */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;

	// (a) The header badge count.
	ob_start();
	?>
	<span class="wmb-cart-count absolute -right-1 -top-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-500 px-1 text-xs font-bold text-slate-900 <?php echo $count > 0 ? '' : 'hidden'; ?>">
		<?php echo esc_html( $count ); ?>
	</span>
	<?php
	$fragments['.wmb-cart-count'] = ob_get_clean();

	// (b) The slide-over mini-cart drawer body — refreshed on every AJAX add/remove.
	ob_start();
	?>
	<div class="widget_shopping_cart_content">
		<?php if ( function_exists( 'woocommerce_mini_cart' ) ) { woocommerce_mini_cart(); } ?>
	</div>
	<?php
	$fragments['div.widget_shopping_cart_content'] = ob_get_clean();

	// (c) Floating cart widget (bottom-right pill: "N Items / total").
	ob_start();
	isdb_floating_cart();
	$fragments['div.isdb-floating-cart'] = ob_get_clean();

	return $fragments;
} );

/**
 * Floating cart pill (bottom-right). Hidden when the cart is empty.
 * Rendered in footer.php and refreshed through the cart fragments filter.
 */
function isdb_floating_cart() {
	// IMPORTANT: this renderer feeds a WooCommerce cart fragment — see the
	// woocommerce_add_to_cart_fragments filter above (key 'div.isdb-floating-cart').
	// Fragments are cached in the browser's sessionStorage and re-applied to the
	// DOM on EVERY page across the whole site. It must therefore emit the SAME
	// markup in every request context; page-conditional output here (e.g. an
	// early return on Cart/Checkout) gets cached and then wipes the pill on all
	// pages until sessionStorage clears. Cart/Checkout hiding is handled at the
	// footer.php call site (skips the direct render) and by CSS in style.css —
	// never in here.
	$count = isdb_cart_count();
	?>
	<div class="isdb-floating-cart fixed right-0 top-1/2 z-40 -translate-y-1/2 <?php echo $count > 0 ? '' : 'hidden'; ?>">
		<button type="button" onclick="window.dispatchEvent(new CustomEvent('isdb-open-cart'))"
			class="flex flex-col items-stretch overflow-hidden rounded-l-2xl bg-brand-primary text-white shadow-lg ring-1 ring-black/5 transition hover:bg-brand-hover">
			<span class="flex flex-col items-center gap-1 px-3 pb-2 pt-2.5">
				<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
				<span class="whitespace-nowrap text-[11px] font-semibold leading-none"><?php echo esc_html( $count ); ?> Items</span>
			</span>
			<span class="isdb-fc-price block whitespace-nowrap border-t border-white/25 bg-black/10 px-3 py-1.5 text-center text-[13px] font-extrabold leading-none"><?php echo wp_kses_post( isdb_cart_subtotal_html() ); ?></span>
		</button>
	</div>
	<?php
}

/* ------------------------------------------------------------------ *
 * 7. SMALL HELPERS
 * ------------------------------------------------------------------ */

/** Number of items currently in the cart (safe on non-WC pages). */
function isdb_cart_count() {
	return function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/** Cart subtotal, formatted (safe on non-WC pages). */
function isdb_cart_subtotal_html() {
	return function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_subtotal() : '';
}

/**
 * Top-level product categories, ordered by popularity.
 * Excludes the default "Uncategorized" term. Returns array of WP_Term.
 *
 * @param int $limit
 * @return WP_Term[]
 */
function isdb_top_categories( $limit = 5 ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}
	$terms = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'number'     => (int) $limit,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'exclude'    => array_filter( array( (int) get_option( 'default_product_cat' ) ) ),
	) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Primary navigation. Uses an assigned 'primary' menu if one exists,
 * otherwise renders real top-level product categories dynamically.
 *
 * @param string $variant 'desktop' | 'mobile'
 */
function isdb_primary_nav( $variant = 'desktop' ) {
	$is_mobile = ( 'mobile' === $variant );

	/*
	 * Mobile: style links via arbitrary variants on the <ul> so wp_nav_menu
	 * items (which don't receive our per-link class) look identical to the
	 * dynamic fallback. Desktop nav sits on the dark bar -> white text.
	 */
	$mobile_link = '[&_a]:flex [&_a]:items-center [&_a]:rounded-lg [&_a]:px-4 [&_a]:py-3 '
		. '[&_a]:text-[15px] [&_a]:font-semibold [&_a]:text-brand-title [&_a]:transition '
		. '[&_a:hover]:bg-brand-soft [&_a:hover]:text-brand-primary '
		. '[&_.current-menu-item>a]:bg-brand-soft [&_.current-menu-item>a]:text-brand-primary '
		. '[&_li]:border-b [&_li]:border-slate-50 [&_li:last-child]:border-0';

	$ul_class  = $is_mobile
		? 'flex flex-col gap-0.5 p-2 ' . $mobile_link
		: 'flex items-center gap-7 h-12 text-sm font-semibold text-white';
	$a_class   = $is_mobile
		? 'flex items-center rounded-lg px-4 py-3 text-[15px] font-semibold text-brand-title transition hover:bg-brand-soft hover:text-brand-primary'
		: 'text-white/90 hover:text-brand-primary transition';

	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => $ul_class,
		) );
		return;
	}

	// Dynamic fallback: real product categories.
	echo '<ul class="' . esc_attr( $ul_class ) . '">';
	echo '<li><a class="' . esc_attr( $a_class ) . '" href="' . esc_url( home_url( '/shop/' ) ) . '">' . esc_html__( 'Shop All', 'isdb-custom' ) . '</a></li>';
	echo '<li><a class="' . esc_attr( $a_class ) . '" href="' . esc_url( isdb_new_arrivals_url() ) . '">' . esc_html__( 'New Arrivals', 'isdb-custom' ) . '</a></li>';
	echo '<li><a class="' . esc_attr( $a_class ) . '" href="' . esc_url( isdb_deals_url() ) . '">' . esc_html__( 'Deals', 'isdb-custom' ) . '</a></li>';
	foreach ( isdb_top_categories( 5 ) as $term ) {
		echo '<li><a class="' . esc_attr( $a_class ) . '" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
	}
	echo '<li><a class="' . esc_attr( $a_class ) . '" href="' . esc_url( home_url( '/about-us/' ) ) . '">' . esc_html__( 'About Us', 'isdb-custom' ) . '</a></li>';
	echo '</ul>';
}

/**
 * Free-shipping progress toward a threshold (goal-gradient nudge).
 * Set ISDB_FREE_SHIP_THRESHOLD to your REAL free-shipping minimum to enable.
 * Returns null when disabled (0) or off-cart — so we never invent a number.
 *
 * @return array{remaining:float, remaining_html:string, pct:float, reached:bool}|null
 */
function isdb_free_shipping_progress() {
	if ( ! defined( 'ISDB_FREE_SHIP_THRESHOLD' ) || ISDB_FREE_SHIP_THRESHOLD <= 0 ) {
		return null;
	}
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return null;
	}
	$threshold = (float) ISDB_FREE_SHIP_THRESHOLD;
	$subtotal  = (float) WC()->cart->get_subtotal();
	$remaining = max( 0, $threshold - $subtotal );

	return array(
		'remaining'      => $remaining,
		'remaining_html' => wc_price( $remaining ),
		'pct'            => min( 100, ( $subtotal / $threshold ) * 100 ),
		'reached'        => $remaining <= 0,
	);
}

/* ------------------------------------------------------------------ *
 * COOKIE CONSENT  (plugin-free, trust-first, cache-safe)
 *   Rendered in the footer on every front-end view. A small vanilla-JS layer
 *   shows it only when the visitor hasn't consented yet (the check runs
 *   client-side, so it's safe with page caching), stores a 30-day cookie on
 *   accept, and animates in/out. A subtle × dismisses for the current session.
 * ------------------------------------------------------------------ */
add_action( 'wp_footer', 'isdb_cookie_consent_banner', 100 );
function isdb_cookie_consent_banner() {
	if ( is_admin() ) {
		return;
	}
	$policy = esc_url( home_url( '/privacy-policy/' ) );
	?>
	<div id="isdb-cookie" role="dialog" aria-live="polite" aria-label="<?php esc_attr_e( 'Privacy and cookie notice', 'isdb-custom' ); ?>">
		<div class="isdb-cc-card">
			<button type="button" class="isdb-cc-close isdb-cc-dismiss" aria-label="<?php esc_attr_e( 'Dismiss', 'isdb-custom' ); ?>">
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
			</button>

			<div class="isdb-cc-head">
				<span class="isdb-cc-ico" aria-hidden="true">
					<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
				</span>
				<h2 class="isdb-cc-title"><?php esc_html_e( 'Your Privacy & Security First', 'isdb-custom' ); ?></h2>
			</div>

			<p class="isdb-cc-body"><?php esc_html_e( 'We use essential cookies to ensure a highly secure, fast, and personalized shopping experience. Your data is protected and never sold.', 'isdb-custom' ); ?></p>

			<span class="isdb-cc-secure">
				<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
				<?php esc_html_e( '256-bit encrypted & never shared', 'isdb-custom' ); ?>
			</span>

			<div class="isdb-cc-actions">
				<button type="button" class="isdb-cc-accept"><?php esc_html_e( 'Accept & Secure Session', 'isdb-custom' ); ?></button>
				<a class="isdb-cc-link" href="<?php echo $policy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already esc_url()'d. ?>"><?php esc_html_e( 'Read Privacy Policy', 'isdb-custom' ); ?></a>
			</div>
		</div>
	</div>

	<style>
	#isdb-cookie{position:fixed;left:1rem;right:1rem;bottom:1rem;z-index:55;display:none;max-width:400px;
		font-family:"Open Sans",ui-sans-serif,system-ui,sans-serif;opacity:0;transform:translateY(18px);
		transition:opacity .38s cubic-bezier(.16,.84,.44,1),transform .38s cubic-bezier(.16,.84,.44,1);}
	@media(min-width:640px){#isdb-cookie{right:auto;width:400px;left:1.25rem;bottom:1.25rem;}}
	#isdb-cookie.isdb-cc-in{opacity:1;transform:translateY(0);}
	#isdb-cookie.isdb-cc-out{opacity:0;transform:translateY(18px);}
	#isdb-cookie *{box-sizing:border-box;}
	#isdb-cookie .isdb-cc-card{position:relative;background:#fff;border:1px solid #ededed;border-radius:16px;
		box-shadow:0 22px 48px -14px rgba(4,31,30,.30);padding:20px 20px 18px;}
	#isdb-cookie .isdb-cc-close{position:absolute;top:10px;right:10px;width:28px;height:28px;border:0;background:transparent;
		color:#9ca3af;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s,color .15s;}
	#isdb-cookie .isdb-cc-close:hover{background:#f3f4f6;color:#374151;}
	#isdb-cookie .isdb-cc-head{display:flex;align-items:center;gap:12px;padding-right:24px;}
	#isdb-cookie .isdb-cc-ico{flex:none;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;
		background:rgba(244,135,33,.12);color:#f48721;}
	#isdb-cookie .isdb-cc-title{margin:0;font-size:15px;font-weight:800;color:#222831;letter-spacing:-.01em;line-height:1.3;}
	#isdb-cookie .isdb-cc-body{margin:12px 0 0;font-size:13px;line-height:1.6;color:#4b5563;}
	#isdb-cookie .isdb-cc-secure{display:inline-flex;align-items:center;gap:6px;margin-top:11px;font-size:11px;font-weight:700;color:#059669;}
	#isdb-cookie .isdb-cc-actions{display:flex;align-items:center;gap:14px;margin-top:16px;flex-wrap:wrap;}
	#isdb-cookie .isdb-cc-accept{flex:1 1 auto;min-width:180px;border:0;cursor:pointer;border-radius:10px;background:#f48721;color:#fff;
		font-size:13.5px;font-weight:800;padding:12px 18px;box-shadow:0 8px 18px -6px rgba(244,135,33,.6);transition:background .15s ease,transform .08s ease;}
	#isdb-cookie .isdb-cc-accept:hover{background:#e0761a;}
	#isdb-cookie .isdb-cc-accept:active{transform:scale(.98);}
	#isdb-cookie .isdb-cc-link{font-size:12.5px;font-weight:600;color:#6b7280;text-decoration:none;white-space:nowrap;}
	#isdb-cookie .isdb-cc-link:hover{color:#f48721;text-decoration:underline;}
	</style>

	<script>
	(function () {
		var KEY = 'isdb_secure_cookie_consent';
		var el  = document.getElementById('isdb-cookie');
		if (!el) { return; }

		function hasCookie(name) {
			return document.cookie.split('; ').some(function (c) { return c.indexOf(name + '=') === 0; });
		}
		function setCookie(name, value, days) {
			var d = new Date();
			d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
			document.cookie = name + '=' + value + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
		}
		function remove() { if (el && el.parentNode) { el.parentNode.removeChild(el); } }

		/*
		 * Unlock marketing / analytics scripts that must run only AFTER consent.
		 * Wire Meta Pixel / Google Analytics to it, e.g.:
		 *   window.addEventListener('isdb:consent', function () {
		 *     fbq('consent', 'grant'); gtag('consent', 'update', { ad_storage: 'granted' });
		 *   });
		 * or define window.isdbConsentGranted = function () { ... };
		 */
		function fireConsentScripts() {
			try { window.dispatchEvent(new CustomEvent('isdb:consent', { detail: { status: 'accepted' } })); } catch (e) {}
			if (typeof window.isdbConsentGranted === 'function') {
				try { window.isdbConsentGranted(); } catch (e) {}
			}
		}

		// Returning visitor who already consented → fire tracking, no banner.
		if (hasCookie(KEY)) { fireConsentScripts(); remove(); return; }

		setTimeout(function () {
			el.style.display = 'block';
			requestAnimationFrame(function () { el.classList.add('isdb-cc-in'); });
		}, 900);

		function hide() {
			el.classList.remove('isdb-cc-in');
			el.classList.add('isdb-cc-out');
			setTimeout(remove, 380);
		}

		// Accept AND close (X) do the EXACT same thing: store a 30-day
		// "accepted" consent, unlock tracking scripts, then hide gracefully.
		function grantConsent() {
			setCookie(KEY, 'accepted', 30);
			fireConsentScripts();
			hide();
		}

		var accept = el.querySelector('.isdb-cc-accept');
		if (accept) { accept.addEventListener('click', grantConsent); }
		var close = el.querySelector('.isdb-cc-dismiss');
		if (close) { close.addEventListener('click', grantConsent); }
	})();
	</script>
	<?php
}

/**
 * Recent GENUINE product reviews (4★+) for homepage social proof.
 * Never fabricates — returns only real approved reviews. Empty array if none.
 *
 * @param int $limit
 * @return WP_Comment[]
 */
function isdb_recent_reviews( $limit = 3 ) {
	if ( ! post_type_exists( 'product' ) ) {
		return array();
	}
	$comments = get_comments( array(
		'number'     => (int) $limit,
		'status'     => 'approve',
		'post_type'  => 'product',
		'parent'     => 0,
		'meta_query' => array(
			array(
				'key'     => 'rating',
				'value'   => 4,
				'compare' => '>=',
				'type'    => 'NUMERIC',
			),
		),
	) );
	return is_array( $comments ) ? $comments : array();
}

/**
 * Best-selling published products (by WooCommerce total_sales).
 * Falls back to latest products when nothing has sold yet.
 *
 * @param int $limit
 * @return WC_Product[]
 */
function isdb_best_sellers( $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$products = wc_get_products( array(
		'status'   => 'publish',
		'limit'    => (int) $limit,
		'orderby'  => 'meta_value_num',
		'meta_key' => 'total_sales',
		'order'    => 'DESC',
	) );
	return is_array( $products ) ? $products : array();
}

/* ------------------------------------------------------------------ *
 * 7b. CHECKOUT QUANTITY STEPPER  (AJAX)
 *     WooCommerce has no native way to change cart quantity from the
 *     checkout page, so we add a minimal endpoint. It only mutates the
 *     cart; the checkout form itself is untouched, and the review box is
 *     refreshed through WooCommerce's own `update_checkout` event.
 * ------------------------------------------------------------------ */
/**
 * Map of product_id => array( key, qty ) for everything currently in the cart.
 * Lets product cards render a quantity stepper instead of "Add to Cart"
 * when the item is already in the cart (matches the reference UI).
 *
 * @return array
 */
function isdb_cart_qty_map() {
	$map = array();
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $map;
	}
	foreach ( WC()->cart->get_cart() as $key => $item ) {
		$pid = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
		// Same product added twice with different data: keep the largest line.
		if ( ! isset( $map[ $pid ] ) || $item['quantity'] > $map[ $pid ]['qty'] ) {
			$map[ $pid ] = array( 'key' => $key, 'qty' => (int) $item['quantity'] );
		}
	}
	return $map;
}

/* ------------------------------------------------------------------ *
 * 6d. SIMPLIFIED CHECKOUT FIELDS (BD-friendly — fewer inputs)
 *     Trims WooCommerce's 10 default fields down to the 6 a Bangladeshi
 *     COD customer actually needs: Full Name · Phone · Email(optional) ·
 *     Address · District · Area/Thana. Fields are only relabelled/removed,
 *     never renamed — orders, emails and admin keep working.
 * ------------------------------------------------------------------ */
add_filter( 'woocommerce_checkout_fields', 'isdb_simplify_checkout_fields', 20 );
function isdb_simplify_checkout_fields( $fields ) {

	foreach ( array( 'billing', 'shipping' ) as $g ) {
		if ( empty( $fields[ $g ] ) ) {
			continue;
		}

		// Drop the clutter.
		unset(
			$fields[ $g ][ "{$g}_last_name" ],
			$fields[ $g ][ "{$g}_company" ],
			$fields[ $g ][ "{$g}_address_2" ],
			$fields[ $g ][ "{$g}_postcode" ]
		);

		// Full name (reuse first_name, full width).
		if ( isset( $fields[ $g ][ "{$g}_first_name" ] ) ) {
			$fields[ $g ][ "{$g}_first_name" ]['label']       = __( 'Full Name', 'isdb-custom' );
			$fields[ $g ][ "{$g}_first_name" ]['placeholder'] = __( 'Full Name', 'isdb-custom' );
			$fields[ $g ][ "{$g}_first_name" ]['class']       = array( 'form-row-wide' );
			$fields[ $g ][ "{$g}_first_name" ]['priority']    = 10;
		}

		// Country stays (WC needs it) but is hidden — store defaults to BD below.
		if ( isset( $fields[ $g ][ "{$g}_country" ] ) ) {
			$fields[ $g ][ "{$g}_country" ]['class']    = array( 'form-row-wide', 'wmb-hidden-field' );
			$fields[ $g ][ "{$g}_country" ]['priority'] = 15;
		}

		// Address (single line).
		if ( isset( $fields[ $g ][ "{$g}_address_1" ] ) ) {
			$fields[ $g ][ "{$g}_address_1" ]['label']       = __( 'Address', 'isdb-custom' );
			$fields[ $g ][ "{$g}_address_1" ]['placeholder'] = __( 'House no. / building / street / area', 'isdb-custom' );
			$fields[ $g ][ "{$g}_address_1" ]['class']       = array( 'form-row-wide' );
			$fields[ $g ][ "{$g}_address_1" ]['priority']    = 40;
		}

		// District (state) + Area/Thana (city), side by side.
		if ( isset( $fields[ $g ][ "{$g}_state" ] ) ) {
			$fields[ $g ][ "{$g}_state" ]['label']       = __( 'District', 'isdb-custom' );
			$fields[ $g ][ "{$g}_state" ]['placeholder'] = __( 'Select District', 'isdb-custom' );
			$fields[ $g ][ "{$g}_state" ]['class']       = array( 'form-row-first' );
			$fields[ $g ][ "{$g}_state" ]['priority']    = 50;
		}
		if ( isset( $fields[ $g ][ "{$g}_city" ] ) ) {
			$fields[ $g ][ "{$g}_city" ]['label']       = __( 'Area / Thana', 'isdb-custom' );
			$fields[ $g ][ "{$g}_city" ]['placeholder'] = __( 'Area / Thana (optional)', 'isdb-custom' );
			$fields[ $g ][ "{$g}_city" ]['required']    = false;
			$fields[ $g ][ "{$g}_city" ]['class']       = array( 'form-row-last' );
			$fields[ $g ][ "{$g}_city" ]['priority']    = 60;
		}

		// Phone — billing always; shipping too when the store collects it (so
		// shipping_phone never renders as a blank, placeholder-less box).
		if ( isset( $fields[ $g ][ "{$g}_phone" ] ) ) {
			$fields[ $g ][ "{$g}_phone" ]['label']       = __( 'Phone Number', 'isdb-custom' );
			$fields[ $g ][ "{$g}_phone" ]['placeholder'] = __( 'Phone (01XXXXXXXXX)', 'isdb-custom' );
			$fields[ $g ][ "{$g}_phone" ]['required']    = ( 'billing' === $g );
			$fields[ $g ][ "{$g}_phone" ]['class']       = array( 'form-row-first' );
			$fields[ $g ][ "{$g}_phone" ]['priority']    = 20;
		}
	}

	// Billing-only: Email (Phone is handled in the loop above).
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['required']    = false;
		$fields['billing']['billing_email']['label']       = __( 'Email', 'isdb-custom' ); // WC appends "(optional)" itself.
		$fields['billing']['billing_email']['placeholder'] = __( 'Email (optional)', 'isdb-custom' );
		$fields['billing']['billing_email']['class']       = array( 'form-row-last' );
		$fields['billing']['billing_email']['priority']    = 30;
	}

	return $fields;
}

/**
 * Address-field labels/visibility at the SOURCE — more reliable for the WC
 * address group (country/address_1/city/state) than woocommerce_checkout_fields
 * alone, which the country locale + AJAX re-render can partially override.
 * The wmb-hidden-field class only hides inside .wmb-checkout, so country still
 * shows on My-Account address forms.
 */
add_filter( 'woocommerce_default_address_fields', function ( $fields ) {
	if ( isset( $fields['country'] ) ) {
		$existing                      = isset( $fields['country']['class'] ) ? (array) $fields['country']['class'] : array();
		$fields['country']['class']    = array_merge( $existing, array( 'wmb-hidden-field' ) );
		$fields['country']['priority'] = 25;
	}
	if ( isset( $fields['address_1'] ) ) {
		$fields['address_1']['label']       = __( 'Address', 'isdb-custom' );
		$fields['address_1']['placeholder'] = __( 'Full address — house, road, area', 'isdb-custom' );
		$fields['address_1']['class']       = array( 'form-row-wide' );
		$fields['address_1']['priority']    = 40;
	}
	if ( isset( $fields['state'] ) ) {
		$fields['state']['label']    = __( 'District', 'isdb-custom' );
		$fields['state']['class']    = array( 'form-row-first' );
		$fields['state']['priority'] = 50;
	}
	if ( isset( $fields['city'] ) ) {
		$fields['city']['label']    = __( 'Area / Thana', 'isdb-custom' );
		$fields['city']['required'] = false;
		$fields['city']['class']    = array( 'form-row-last' );
		$fields['city']['priority'] = 60;
	}
	return $fields;
}, 20 );

/* ------------------------------------------------------------------ *
 * NEVER CACHE THE PER-VISITOR PAGES
 *   Cart / Checkout / My Account / Order-received are unique to each session.
 *   If a page cache (LiteSpeed, WP Rocket, W3TC, Super Cache, host-level) stores
 *   one copy, every visitor gets that snapshot — which is why an "empty cart"
 *   page can appear right after adding a product. These flags/headers tell every
 *   common cache layer to skip the page.
 * ------------------------------------------------------------------ */
add_action( 'template_redirect', function () {
	if ( ! function_exists( 'is_cart' ) ) {
		return;
	}

	$is_private = is_cart()
		|| is_checkout()
		|| ( function_exists( 'is_account_page' ) && is_account_page() )
		|| ( function_exists( 'is_order_received_page' ) && is_order_received_page() );

	if ( ! $is_private ) {
		return;
	}

	// Generic constants honoured by most WP caching plugins.
	if ( ! defined( 'DONOTCACHEPAGE' ) )   { define( 'DONOTCACHEPAGE', true ); }
	if ( ! defined( 'DONOTCACHEOBJECT' ) ) { define( 'DONOTCACHEOBJECT', true ); }
	if ( ! defined( 'DONOTCACHEDB' ) )     { define( 'DONOTCACHEDB', true ); }

	// LiteSpeed Cache explicit control.
	do_action( 'litespeed_control_set_nocache', 'WooCommerce cart/checkout/account page' );

	// Browser/proxy level.
	nocache_headers();
}, 0 );

// Default the checkout to Bangladesh so the hidden country field is valid.
add_filter( 'default_checkout_billing_country', function () { return 'BD'; } );
add_filter( 'default_checkout_shipping_country', function () { return 'BD'; } );

/* ------------------------------------------------------------------ *
 * 6e. THANK-YOU / ORDER TRACKER
 *     thankyou.php renders its own order tracker (timeline + summary +
 *     shipping), so drop WooCommerce's default order-details/customer tables
 *     to avoid duplication. Plugins hooking woocommerce_thankyou still fire.
 * ------------------------------------------------------------------ */
add_action( 'wp', function () {
	if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
		remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
	}
} );

/**
 * Map an order's status to a step index on the tracker timeline.
 * Filterable so a store using custom statuses (packed/shipped) can slot them in.
 *
 * @param WC_Order $order
 * @return int
 */
function isdb_order_timeline_step( $order ) {
	$map = apply_filters( 'isdb_order_timeline_status_map', array(
		'pending'    => 0,
		'on-hold'    => 0,
		'processing' => 1,
		'packed'     => 2,
		'ready'      => 2,
		'shipped'    => 3,
		'in-transit' => 3,
		'out-for-delivery' => 3,
		'completed'  => 4,
	) );
	$status = $order->get_status();
	return isset( $map[ $status ] ) ? (int) $map[ $status ] : 0;
}

/**
 * Public URL of the standalone order-tracking page.
 * Create a Page using the "Order Tracking" template (slug: track-order).
 * Filterable if your slug differs.
 *
 * @return string
 */
function isdb_track_order_url() {
	return apply_filters( 'isdb_track_order_url', home_url( '/track-order/' ) );
}

/**
 * Auto-create the public "Track Order" page (slug: track-order) using the
 * "Order Tracking" template, so the header/menu link works with no manual setup.
 * Runs on theme activation and once in admin (option-guarded).
 */
function isdb_ensure_track_order_page() {
	if ( get_page_by_path( 'track-order' ) ) {
		return;
	}
	$page_id = wp_insert_post( array(
		'post_title'  => 'Track Order',
		'post_name'   => 'track-order',
		'post_status' => 'publish',
		'post_type'   => 'page',
	) );
	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'template-track-order.php' );
	}
}
add_action( 'after_switch_theme', 'isdb_ensure_track_order_page' );
add_action( 'admin_init', function () {
	if ( get_option( 'isdb_track_page_done' ) ) {
		return;
	}
	isdb_ensure_track_order_page();
	update_option( 'isdb_track_page_done', 1 );
} );

/**
 * Title / lead / body for the branded policy pages, keyed by slug.
 * Slugs match the footer links. Body uses only prose-safe tags.
 *
 * @return array<string,array{title:string,excerpt:string,content:string}>
 */
function isdb_policy_pages_content() {
	$return = <<<'HTML'
<p>At WayMoreBD, your satisfaction isn't just a promise—it's our top priority. We understand that sometimes things don't go exactly as planned. If something isn't right, we've got your back with a smooth, worry-free return process.</p>

<h2>3-Day Easy Return Window</h2>
<p>Take your time! You have a full <strong>3 days</strong> from the moment you receive your order to request a return if the product is damaged, defective, or not what you expected.</p>

<h2>Zero-Hassle Return Process</h2>
<p>We hate complicated forms as much as you do. Returning an item is as easy as reaching out to us:</p>
<ul>
<li>Contact our friendly support team via WhatsApp, Facebook Messenger, Live Chat, or a quick phone call.</li>
<li>Simply share your <strong>Order ID</strong>, a brief note about the issue, and a quick photo or video so we can instantly verify and assist you.</li>
</ul>

<h2>Your Choice: Replacement or Fast Refund</h2>
<p>Once your return is approved, you hold the power. Choose what works best for you:</p>
<ul>
<li><strong>Replacement:</strong> We'll swiftly deliver a brand-new, perfect product right to your doorstep.</li>
<li><strong>Refund:</strong> Get your money safely back through your original payment method (bKash/Nagad/Card). For our Cash on Delivery (COD) family, refunds are seamlessly processed via mobile banking.</li>
</ul>

<h2>Simple Conditions for a Happy Return</h2>
<p>To help us process your return instantly, just ensure:</p>
<ul>
<li>The product is completely unused and resting in its original packaging.</li>
<li>All those little extras—accessories, tags, and freebies—are included.</li>
</ul>
<blockquote><strong>Note:</strong> We cannot accept returns for items damaged due to accidental misuse or mishandling.</blockquote>

<h2>Pre-Order Items</h2>
<p>Special pre-order items are uniquely sourced for you! They can be safely returned if they arrive in a damaged or defective condition.</p>

<h2>No Hassle, No Stress</h2>
<p>Our dedicated team is standing by to guide you through every step of the return process. Your happiness means everything to us. Shop with absolute confidence and peace of mind at WayMoreBD.</p>
HTML;

	$terms = <<<'HTML'
<p>Thank you for choosing WayMoreBD! When you shop with us, we want you to feel completely secure. These simple Terms of Service are designed to ensure a transparent, fair, and delightful shopping experience for you.</p>

<h2>Your Account &amp; Security</h2>
<p>When you create an account with WayMoreBD, you are trusting us with your details. In return, we ask that you keep your login credentials safe. You are in control of your account, and any activity under your account is your responsibility.</p>

<h2>Honest Pricing &amp; Product Information</h2>
<p>We strive for <strong>100% accuracy</strong>. Every image, description, and price on WayMoreBD is crafted to give you the clearest picture of what you are buying. In the rare event of a typographical error in pricing or stock, we promise to communicate with you honestly and find a fair solution before processing the order.</p>

<h2>Seamless Order Fulfillment</h2>
<p>When you tap "Place Order," our team immediately gets to work. We reserve the right to accept, decline, or gracefully cancel orders in cases of stock unavailability or security concerns. If an order is canceled after payment, your money will be refunded instantly—no questions asked.</p>

<h2>A Community of Respect</h2>
<p>WayMoreBD is built on mutual trust. By using our platform, you agree to interact respectfully and use our website for its intended purpose—enjoying a premium shopping experience without engaging in fraudulent or harmful activities.</p>
HTML;

	$privacy = <<<'HTML'
<p>At WayMoreBD, we don't just protect your packages; we protect your digital footprint. We know that your personal information is extremely private, and we treat it with the highest level of security and respect.</p>

<h2>What We Collect &amp; Why</h2>
<p>To deliver your favorite items to your doorstep, we only collect the essentials: your name, shipping address, phone number, and email. This information is strictly used to:</p>
<ul>
<li>Process and deliver your orders lightning-fast.</li>
<li>Send you important updates regarding your delivery status.</li>
<li>Make your next checkout experience even smoother.</li>
</ul>

<h2>100% Secure Payments</h2>
<p>Your financial safety is non-negotiable. When you pay online, your credit card or mobile banking details are encrypted and processed through highly secure, bank-level payment gateways. <strong>WayMoreBD never stores your sensitive payment or card information.</strong></p>

<h2>We Never Sell Your Data</h2>
<p>Your trust is our biggest asset. WayMoreBD absolutely guarantees that your personal information will never be sold, rented, or traded to third-party companies. We only share necessary details (like your address and phone number) with our trusted delivery partners so they can bring your package home.</p>

<h2>Your Control</h2>
<p>You are always in the driver's seat. You have the complete right to access, edit, or request the deletion of your personal data from our system at any time. Just drop us a message, and we'll handle it immediately.</p>
<blockquote>Shop freely and securely. At WayMoreBD, your privacy is always in safe hands.</blockquote>
HTML;

	$contact = <<<'HTML'
<p>Have a question, a special request, or simply want to say hello? Our friendly team at WayMoreBD is always ready to help. Reach out through whichever channel is easiest for you—we usually reply within a few hours.</p>
HTML;

	$company = <<<'HTML'
<p>WayMoreBD is a homegrown Bangladeshi online store with one simple mission: to bring premium kitchen and home essentials to your doorstep—honestly priced, carefully sourced, and delivered with care.</p>

<h2>Who We Are</h2>
<p>We started WayMoreBD because we believe everyday families deserve more—more quality, more honesty, and more value for every taka they spend. From our base in Dhaka, we hand-pick products we would happily use in our own homes and make them available to customers across Bangladesh.</p>

<h2>What We Stand For</h2>
<ul>
<li><strong>Honesty first:</strong> real photos, clear prices, and no hidden surprises at checkout.</li>
<li><strong>Quality you can trust:</strong> every product is checked before it reaches you.</li>
<li><strong>Customer care that feels personal:</strong> real humans, quick replies, and a genuine "we've got your back" attitude.</li>
<li><strong>Cash on Delivery convenience:</strong> pay only when your order arrives safely.</li>
</ul>

<h2>Where to Find Us</h2>
<p>WayMoreBD<br>New Market, Dhaka, Bangladesh<br>Phone: +880 1868 662477<br>Email: info.waymore.bd@gmail.com</p>
HTML;

	$stories = <<<'HTML'
<p>Behind every order is a real kitchen, a real family, and a real moment made a little easier. These are the stories that keep us going.</p>

<h2>Why We Started</h2>
<p>WayMoreBD began with a simple frustration: too many online stores promised the world and delivered disappointment. We wanted to build the opposite—a place where "what you see is what you get," where support actually supports, and where shopping feels safe and joyful.</p>

<h2>The WayMore Difference</h2>
<p>We obsess over the small things—the way an order is packed, how fast a question is answered, the little freebies that make you smile. Because we know it's these details that turn a first-time buyer into a lifelong friend of the brand.</p>

<h2>Made for Bangladeshi Homes</h2>
<p>From spices to kitchen tools to everyday essentials, everything we offer is chosen with local homes in mind. Our goal is simple: help you cook, care, and live a little better every single day.</p>

<blockquote>Every product we deliver carries a promise—quality you can feel and service you can trust.</blockquote>
HTML;

	$careers = <<<'HTML'
<p>At WayMoreBD, we're building something special—and we're always looking for passionate people who want to grow with us. If you love solving problems, delighting customers, and doing honest work, you'll feel right at home here.</p>

<h2>Why Join WayMoreBD</h2>
<ul>
<li><strong>Real impact:</strong> your work directly shapes the experience of thousands of customers.</li>
<li><strong>A culture of respect:</strong> we win as a team and support each other every step of the way.</li>
<li><strong>Room to grow:</strong> we invest in people who take initiative and learn fast.</li>
</ul>

<h2>Who We're Looking For</h2>
<p>We welcome talented, dependable people across customer support, operations, marketing, and delivery. More than a perfect CV, we value a great attitude, honesty, and a genuine desire to help others.</p>

<h2>How to Apply</h2>
<p>Interested in joining the family? Send your CV and a short note about yourself to <a href="mailto:info.waymore.bd@gmail.com">info.waymore.bd@gmail.com</a> with the role you're excited about in the subject line. We review every application with care and reach out if there's a good fit.</p>
HTML;

	return array(
		'return-refund-policy' => array(
			'title'    => 'Happy Return & Refund Policy',
			'excerpt'  => 'Your Happiness is Our Guarantee',
			'content'  => $return,
			'template' => 'template-policy.php',
		),
		'terms-and-conditions' => array(
			'title'    => 'Terms of Services',
			'excerpt'  => 'Welcome to the WayMoreBD Family',
			'content'  => $terms,
			'template' => 'template-policy.php',
		),
		'privacy-policy'       => array(
			'title'    => 'Privacy Policy',
			'excerpt'  => 'Your Privacy is Our Promise',
			'content'  => $privacy,
			'template' => 'template-policy.php',
		),
		'contact'              => array(
			'title'    => 'Contact Us',
			'excerpt'  => "We're Always Just a Message Away",
			'content'  => $contact,
			'template' => 'template-contact.php',
		),
		'company-information'  => array(
			'title'    => 'Company Information',
			'excerpt'  => 'The Story Behind WayMoreBD',
			'content'  => $company,
			'template' => 'template-policy.php',
		),
		'our-stories'          => array(
			'title'    => 'Our Stories',
			'excerpt'  => 'Real People. Real Kitchens. Real WayMore.',
			'content'  => $stories,
			'template' => 'template-policy.php',
		),
		'careers'              => array(
			'title'    => 'Careers',
			'excerpt'  => 'Grow With Us',
			'content'  => $careers,
			'template' => 'template-policy.php',
		),
	);
}

/**
 * Auto-create the branded policy pages (Return, Terms, Privacy) using the
 * "Policy Page" template. Slugs match the footer links; existing pages are
 * never overwritten (safe to edit in wp-admin afterwards).
 */
function isdb_ensure_policy_pages() {
	foreach ( isdb_policy_pages_content() as $slug => $data ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$page_id = wp_insert_post( array(
			'post_title'   => $data['title'],
			'post_name'    => $slug,
			'post_excerpt' => $data['excerpt'],
			'post_content' => $data['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		) );
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			$template = ! empty( $data['template'] ) ? $data['template'] : 'template-policy.php';
			update_post_meta( $page_id, '_wp_page_template', $template );
		}
	}
}
add_action( 'after_switch_theme', 'isdb_ensure_policy_pages' );
add_action( 'admin_init', function () {
	// Bumped key (v2) so newly added managed pages are created even if the
	// earlier policy-only batch already ran. get_page_by_path() skips existing.
	if ( get_option( 'isdb_managed_pages_v2' ) ) {
		return;
	}
	isdb_ensure_policy_pages();
	update_option( 'isdb_managed_pages_v2', 1 );
} );

/**
 * Simple per-IP throttle for the public order lookup (defense-in-depth against
 * order-ID + phone brute-forcing). 10 attempts per 10 minutes.
 *
 * @return bool True while under the limit.
 */
function isdb_track_lookup_allowed() {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x';
	$key = 'isdb_track_try_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 10 ) {
		return false;
	}
	set_transient( $key, $n + 1, 10 * MINUTE_IN_SECONDS );
	return true;
}

/**
 * Look up an order for the PUBLIC tracker, verifying ownership.
 *
 * Security: never expose an order by number alone (numbers are enumerable).
 * We accept it only when EITHER the logged-in requester owns the order, OR the
 * phone/email they supplied matches the order's billing record.
 *
 * @param string $number  Order number / ID (any format; digits are extracted).
 * @param string $contact Billing phone or email used to verify a guest.
 * @return array{order:?WC_Order, error:string}
 */
function isdb_find_trackable_order( $number, $contact = '' ) {
	$digits = preg_replace( '/\D+/', '', (string) $number );
	if ( '' === $digits ) {
		return array( 'order' => null, 'error' => __( 'Please enter a valid order number.', 'isdb-custom' ) );
	}

	$order = wc_get_order( (int) $digits );
	if ( ! $order instanceof WC_Order ) {
		return array( 'order' => null, 'error' => __( 'No order found with that number. Please check and try again.', 'isdb-custom' ) );
	}

	// Owner shortcut — logged-in customer viewing their own order.
	if ( is_user_logged_in() && (int) $order->get_customer_id() === get_current_user_id() ) {
		return array( 'order' => $order, 'error' => '' );
	}

	// Guest: require a matching phone or email.
	$contact = trim( (string) $contact );
	if ( '' === $contact ) {
		return array( 'order' => null, 'error' => __( 'Enter the phone number used on the order to view it.', 'isdb-custom' ) );
	}

	if ( is_email( $contact ) ) {
		$match = ( 0 === strcasecmp( trim( $order->get_billing_email() ), $contact ) );
	} else {
		$norm  = static function ( $p ) {
			$p = preg_replace( '/\D+/', '', (string) $p );
			return strlen( $p ) >= 10 ? substr( $p, -10 ) : $p; // ignore +88 / leading 0
		};
		$in    = $norm( $contact );
		$match = ( '' !== $in && $in === $norm( $order->get_billing_phone() ) );
	}

	if ( ! $match ) {
		return array( 'order' => null, 'error' => __( 'The details don\'t match our records. Please check the phone/email used on the order.', 'isdb-custom' ) );
	}

	return array( 'order' => $order, 'error' => '' );
}

/**
 * Render the order-tracker BODY — status timeline, products, order summary and
 * shipping details. Shared by the thank-you page and the standalone tracking
 * page (each supplies its own hero). Styling lives in .wmb-stepper (app.css).
 *
 * @param WC_Order $order
 */
function isdb_render_order_tracker( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$is_paid      = $order->is_paid();
	$total_raw    = (float) $order->get_total();
	$paid_raw     = $is_paid ? $total_raw : 0.0;
	$due_raw      = $is_paid ? 0.0 : $total_raw;
	$discount_raw = (float) $order->get_total_discount();
	$ship_raw     = (float) $order->get_shipping_total();
	$current      = isdb_order_timeline_step( $order );
	$is_cancelled = $order->has_status( array( 'cancelled', 'refunded' ) );
	$total_html   = $order->get_formatted_order_total();

	$steps = array(
		array( 'label' => __( 'Order Placed', 'isdb-custom' ), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12A2.25 2.25 0 0118.628 21H5.372a2.25 2.25 0 01-2.24-2.493l1.264-12A2.25 2.25 0 016.632 6h10.736a2.25 2.25 0 012.24 2.007z"/>' ),
		array( 'label' => __( 'Confirmed', 'isdb-custom' ), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>' ),
		array( 'label' => __( 'Packed', 'isdb-custom' ), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>' ),
		array( 'label' => __( 'On the Way', 'isdb-custom' ), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>' ),
		array( 'label' => __( 'Delivered', 'isdb-custom' ), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>' ),
	);
	$last_index = count( $steps ) - 1;
	$fill_pct   = $last_index > 0 ? min( 100, ( $current / $last_index ) * 100 ) : 0;
	?>
	<!-- ── STATUS TIMELINE ── -->
	<div class="mx-auto max-w-3xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8">
		<div class="mb-6 flex items-center justify-between">
			<h2 class="flex items-center gap-2 text-base font-bold text-brand-title">
				<svg class="h-5 w-5 text-brand-primary" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				<?php esc_html_e( 'Order Timeline', 'isdb-custom' ); ?>
			</h2>
			<?php if ( $is_cancelled ) : ?>
				<span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-600"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
			<?php else : ?>
				<span class="rounded-full bg-brand-primary/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-brand-primary"><?php echo esc_html( $steps[ min( $current, $last_index ) ]['label'] ); ?></span>
			<?php endif; ?>
		</div>

		<div class="wmb-stepper" style="--wmb-fill: <?php echo esc_attr( $is_cancelled ? 0 : $fill_pct ); ?>%;">
			<?php
			foreach ( $steps as $i => $step ) :
				$state = $is_cancelled ? 'pending' : ( $i < $current ? 'done' : ( $i === $current ? 'active' : 'pending' ) );
				?>
				<div class="wmb-step wmb-step--<?php echo esc_attr( $state ); ?>">
					<div class="wmb-step__circle">
						<?php if ( 'done' === $state ) : ?>
							<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
						<?php else : ?>
							<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><?php echo $step['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg>
						<?php endif; ?>
					</div>
					<span class="wmb-step__label"><?php echo esc_html( $step['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<!-- ── PRODUCTS + SUMMARY + SHIPPING ── -->
	<div class="mx-auto mt-6 grid max-w-3xl gap-6 lg:grid-cols-[1fr,320px] lg:items-start">

		<section class="rounded-2xl border border-slate-100 bg-white p-6">
			<h2 class="mb-4 text-base font-bold text-brand-title"><?php esc_html_e( 'Products', 'isdb-custom' ); ?></h2>
			<ul class="divide-y divide-slate-100">
				<?php foreach ( $order->get_items() as $item ) :
					$product   = $item->get_product();
					$thumb     = $product ? $product->get_image( array( 56, 56 ), array( 'class' => 'h-14 w-14 rounded-lg object-cover' ) ) : '';
					$line_html = $order->get_formatted_line_subtotal( $item );
					?>
					<li class="flex items-center gap-3 py-3">
						<div class="flex-none overflow-hidden rounded-lg bg-brand-bg"><?php echo wp_kses_post( $thumb ); ?></div>
						<div class="min-w-0 flex-1">
							<p class="truncate text-sm font-semibold text-brand-title"><?php echo esc_html( $item->get_name() ); ?></p>
							<p class="mt-0.5 text-xs text-slate-500"><?php printf( esc_html__( 'Qty: %s', 'isdb-custom' ), esc_html( $item->get_quantity() ) ); ?></p>
						</div>
						<span class="flex-none text-sm font-bold text-brand-title"><?php echo wp_kses_post( $line_html ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>

		<div class="space-y-6">
			<section class="rounded-2xl border border-slate-100 bg-white p-6">
				<h2 class="mb-4 text-base font-bold text-brand-title"><?php esc_html_e( 'Order Summary', 'isdb-custom' ); ?></h2>
				<dl class="space-y-2.5 text-sm">
					<div class="flex items-center justify-between">
						<dt class="text-slate-500"><?php esc_html_e( 'Subtotal', 'isdb-custom' ); ?></dt>
						<dd class="font-semibold text-brand-title"><?php echo wp_kses_post( wc_price( $order->get_subtotal() ) ); ?></dd>
					</div>
					<?php if ( $discount_raw > 0 ) : ?>
						<div class="flex items-center justify-between">
							<dt class="text-slate-500"><?php esc_html_e( 'Discount', 'isdb-custom' ); ?></dt>
							<dd class="font-semibold text-emerald-600">&minus;<?php echo wp_kses_post( wc_price( $discount_raw ) ); ?></dd>
						</div>
					<?php endif; ?>
					<div class="flex items-center justify-between">
						<dt class="text-slate-500"><?php esc_html_e( 'Delivery Fee', 'isdb-custom' ); ?></dt>
						<dd class="font-semibold text-brand-title"><?php echo $ship_raw > 0 ? wp_kses_post( wc_price( $ship_raw ) ) : '<span class="text-emerald-600">' . esc_html__( 'Free', 'isdb-custom' ) . '</span>'; ?></dd>
					</div>
					<div class="flex items-center justify-between border-t border-slate-100 pt-2.5">
						<dt class="font-bold text-brand-title"><?php esc_html_e( 'Grand Total', 'isdb-custom' ); ?></dt>
						<dd class="text-base font-extrabold text-brand-primary"><?php echo wp_kses_post( $total_html ); ?></dd>
					</div>
					<div class="flex items-center justify-between">
						<dt class="text-slate-500"><?php esc_html_e( 'Total Paid', 'isdb-custom' ); ?></dt>
						<dd class="font-semibold text-brand-title"><?php echo wp_kses_post( wc_price( $paid_raw ) ); ?></dd>
					</div>
					<div class="flex items-center justify-between">
						<dt class="text-slate-500"><?php esc_html_e( 'Amount Due', 'isdb-custom' ); ?></dt>
						<dd class="font-bold <?php echo $due_raw > 0 ? 'text-rose-600' : 'text-emerald-600'; ?>"><?php echo wp_kses_post( wc_price( $due_raw ) ); ?></dd>
					</div>
				</dl>

				<div class="mt-4 flex items-center justify-between rounded-xl bg-brand-bg px-3.5 py-2.5">
					<span class="text-xs font-medium text-slate-500"><?php esc_html_e( 'Payment Status', 'isdb-custom' ); ?></span>
					<span class="inline-flex items-center gap-1.5 text-xs font-bold <?php echo $is_paid ? 'text-emerald-600' : 'text-amber-600'; ?>">
						<span class="h-1.5 w-1.5 rounded-full <?php echo $is_paid ? 'bg-emerald-500' : 'bg-amber-500'; ?>"></span>
						<?php echo $is_paid ? esc_html__( 'Paid', 'isdb-custom' ) : esc_html__( 'Payment Pending', 'isdb-custom' ); ?>
					</span>
				</div>

				<?php if ( $due_raw > 0 && $order->needs_payment() ) : ?>
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="mt-3 block rounded-xl border border-brand-primary px-4 py-2.5 text-center text-sm font-bold text-brand-primary transition hover:bg-brand-primary hover:text-white">
						<?php esc_html_e( 'Pay Remaining Amount', 'isdb-custom' ); ?>
					</a>
				<?php endif; ?>
			</section>

			<section class="rounded-2xl border border-slate-100 bg-white p-6">
				<h2 class="mb-4 text-base font-bold text-brand-title"><?php esc_html_e( 'Shipping Details', 'isdb-custom' ); ?></h2>
				<dl class="space-y-3 text-sm">
					<div>
						<dt class="text-xs font-medium uppercase tracking-wide text-slate-400"><?php esc_html_e( 'Customer', 'isdb-custom' ); ?></dt>
						<dd class="mt-0.5 font-semibold text-brand-title"><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></dd>
					</div>
					<?php if ( $order->get_billing_phone() ) : ?>
						<div>
							<dt class="text-xs font-medium uppercase tracking-wide text-slate-400"><?php esc_html_e( 'Phone', 'isdb-custom' ); ?></dt>
							<dd class="mt-0.5 font-semibold text-brand-title"><?php echo esc_html( $order->get_billing_phone() ); ?></dd>
						</div>
					<?php endif; ?>
					<div>
						<dt class="text-xs font-medium uppercase tracking-wide text-slate-400"><?php esc_html_e( 'Delivery Address', 'isdb-custom' ); ?></dt>
						<dd class="mt-0.5 leading-relaxed text-brand-body"><?php echo wp_kses_post( $order->get_formatted_billing_address() ?: '&mdash;' ); ?></dd>
					</div>
					<div>
						<dt class="text-xs font-medium uppercase tracking-wide text-slate-400"><?php esc_html_e( 'Payment Method', 'isdb-custom' ); ?></dt>
						<dd class="mt-0.5 font-semibold text-brand-title"><?php echo esc_html( $order->get_payment_method_title() ); ?></dd>
					</div>
				</dl>
			</section>
		</div>
	</div>
	<?php
}

/* ------------------------------------------------------------------ *
 * 6a. CHECKOUT LAYOUT SPLIT
 *     Core fires BOTH the order-review table and the payment box from the
 *     single `woocommerce_checkout_order_review` action. We detach them so
 *     the template can place items (left column) and payment (right column)
 *     independently. Safe because WC's update_order_review AJAX targets the
 *     two fragments by CLASS (.woocommerce-checkout-review-order-table and
 *     .woocommerce-checkout-payment) — both still exist in the DOM.
 * ------------------------------------------------------------------ */
add_action( 'wp', function () {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}
	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
	remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

	// Coupon is rendered in the right column, not at the top of the page.
	remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
} );

/**
 * Checkout order-summary totals — rendered in the RIGHT column, and refreshed
 * live via the fragment below whenever address/shipping/qty changes.
 */
function isdb_render_checkout_totals() {
	?>
	<div class="wmb-order-totals space-y-2.5 text-sm">
		<div class="flex items-center justify-between">
			<span class="text-brand-body"><?php esc_html_e( 'Sub total', 'isdb-custom' ); ?></span>
			<span class="font-semibold text-brand-title"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="flex items-center justify-between text-emerald-700">
				<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			<div class="isdb-ship-rows [&_ul]:m-0 [&_ul]:list-none [&_ul]:p-0 [&_li]:flex [&_li]:items-center [&_li]:justify-between [&_li]:py-0.5 [&_label]:m-0 [&_label]:text-brand-body [&_.amount]:font-semibold [&_.amount]:text-brand-title">
				<?php wc_cart_totals_shipping_html(); ?>
			</div>
		<?php else : ?>
			<div class="flex items-center justify-between">
				<span class="text-brand-body"><?php esc_html_e( 'Delivery cost', 'isdb-custom' ); ?></span>
				<span class="font-semibold text-brand-title"><?php echo esc_html( wc_price( 0 ) ); ?></span>
			</div>
		<?php endif; ?>
		<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="flex items-center justify-between">
				<span class="text-brand-body"><?php echo esc_html( $fee->name ); ?></span>
				<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
		<div class="flex items-center justify-between border-t border-[#e0e0e0] pt-3">
			<span class="text-base font-bold text-brand-title"><?php esc_html_e( 'Total', 'isdb-custom' ); ?></span>
			<span class="text-lg font-extrabold text-brand-primary"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>
		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

		<?php
		// Loss-aversion: reinforce the win right at the decision point.
		$checkout_savings = 0.0;
		foreach ( WC()->cart->get_cart() as $ci ) {
			$p = isset( $ci['data'] ) ? $ci['data'] : null;
			if ( $p instanceof WC_Product ) {
				$reg = (float) $p->get_regular_price();
				$cur = (float) $p->get_price();
				if ( $reg > $cur && $cur > 0 ) {
					$checkout_savings += ( $reg - $cur ) * (int) $ci['quantity'];
				}
			}
		}
		if ( $checkout_savings > 0 ) :
			?>
			<div class="mt-1 flex items-center justify-between rounded-lg bg-emerald-50 px-3 py-2">
				<span class="text-[13px] font-bold text-emerald-700">&#127881; <?php esc_html_e( 'You\'re saving', 'isdb-custom' ); ?></span>
				<span class="text-[13px] font-extrabold text-emerald-700"><?php echo wp_kses_post( wc_price( $checkout_savings ) ); ?></span>
			</div>
			<?php
		endif;
		?>
	</div>
	<?php
}

// Keep the right-column totals in sync with WooCommerce's update_order_review AJAX.
add_filter( 'woocommerce_update_order_review_fragments', function ( $fragments ) {
	ob_start();
	isdb_render_checkout_totals();
	$fragments['.wmb-order-totals'] = ob_get_clean();
	return $fragments;
} );

/* ------------------------------------------------------------------ *
 * 6b. AUTH PLUGIN BRIDGES  (Social login + phone/OTP login)
 *     These render ONLY when a provider plugin is actually active — no
 *     dead buttons are ever printed.
 * ------------------------------------------------------------------ */

/**
 * Render social-login buttons (Nextend Social Login, or any plugin exposing
 * a known shortcode).
 *
 * @return bool True when something was printed.
 */
function isdb_render_social_login() {
	$html = isdb_get_social_login_html();
	if ( '' === $html ) {
		return false;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin-generated markup.
	return true;
}

/**
 * Social-login buttons as an HTML string (empty when nothing to show).
 *
 * NOTE: NextendSocialLogin::renderButtonsWithContainer() RETURNS the markup,
 * it does not echo — and it returns '' when the user is already logged in or
 * no provider is enabled. Callers must check the string, not just whether the
 * plugin exists, otherwise you get a divider with nothing under it.
 *
 * @return string
 */
function isdb_get_social_login_html() {
	static $cached = null;
	if ( null !== $cached ) {
		return $cached;
	}

	$html = '';

	// Nextend Social Login — official PHP API.
	if ( class_exists( 'NextendSocialLogin' ) && method_exists( 'NextendSocialLogin', 'renderButtonsWithContainer' ) ) {
		$html = (string) NextendSocialLogin::renderButtonsWithContainer();
	}

	// Fallback: any known shortcode.
	if ( '' === trim( $html ) ) {
		$tags = apply_filters( 'isdb_social_login_shortcodes', array(
			'nextend_social_login',
			'miniorange_social_login',
			'wordpress_social_login',
		) );
		foreach ( (array) $tags as $tag ) {
			if ( shortcode_exists( $tag ) ) {
				$html = (string) do_shortcode( '[' . $tag . ']' );
				if ( '' !== trim( $html ) ) {
					break;
				}
			}
		}
	}

	$cached = trim( $html );
	return $cached;
}

/**
 * Render a phone / OTP login form from whichever OTP plugin is active.
 *
 * Set the exact shortcode in wp-config.php or a child theme if auto-detection
 * misses yours:
 *     define( 'ISDB_OTP_SHORTCODE', '[your-otp-shortcode]' );
 * …or filter `isdb_otp_shortcode`.
 *
 * @return bool True when something was printed.
 */
function isdb_render_otp_login() {
	// 1) Explicit override always wins.
	$explicit = defined( 'ISDB_OTP_SHORTCODE' ) ? ISDB_OTP_SHORTCODE : '';
	$explicit = apply_filters( 'isdb_otp_shortcode', $explicit );
	if ( $explicit ) {
		echo do_shortcode( $explicit );
		return true;
	}

	// 2) Auto-detect common OTP-plugin shortcodes.
	$tags = apply_filters( 'isdb_otp_login_shortcodes', array(
		'xoo-el-inline-form',   // xootix Login/Register
		'xoo-ml-form',          // xootix Mobile Login
		'miniorange_otp_login',
		'mo_phone_login',
		'otp_login_form',
		'loginotp',
		'wpotp_login',
		'digits_login',
	) );
	foreach ( (array) $tags as $tag ) {
		if ( shortcode_exists( $tag ) ) {
			echo do_shortcode( '[' . $tag . ']' );
			return true;
		}
	}
	return false;
}

/** True when some OTP provider is available (used to pick the login layout). */
function isdb_has_otp_login() {
	if ( defined( 'ISDB_OTP_SHORTCODE' ) && ISDB_OTP_SHORTCODE ) {
		return true;
	}
	if ( apply_filters( 'isdb_otp_shortcode', '' ) ) {
		return true;
	}
	foreach ( apply_filters( 'isdb_otp_login_shortcodes', array( 'xoo-el-inline-form', 'xoo-ml-form', 'miniorange_otp_login', 'mo_phone_login', 'otp_login_form', 'loginotp', 'wpotp_login', 'digits_login' ) ) as $tag ) {
		if ( shortcode_exists( $tag ) ) {
			return true;
		}
	}
	return false;
}

/**
 * True only when social buttons would actually RENDER something.
 * Deliberately checks the produced HTML, not merely whether a plugin is
 * installed — a configured-but-silent plugin used to leave an empty divider.
 */
function isdb_has_social_login() {
	return '' !== isdb_get_social_login_html();
}

/* ------------------------------------------------------------------ *
 * 7a. BRAND + COMBO DATA SOURCES
 * ------------------------------------------------------------------ */

/** First registered brand taxonomy on this install (WC 9.6+ ships product_brand). */
function isdb_brand_taxonomy() {
	foreach ( array( 'product_brand', 'pwb-brand', 'yith_product_brand' ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			return $tax;
		}
	}
	return '';
}

/**
 * Brand terms for the "Our Brands" strip. Empty array when the store has no
 * brand taxonomy or no brands yet — the section then simply doesn't render
 * (we never fake brands).
 *
 * @param int $limit
 * @return WP_Term[]
 */
function isdb_brand_terms( $limit = 12 ) {
	$tax = isdb_brand_taxonomy();
	if ( ! $tax ) {
		return array();
	}
	$terms = get_terms( array(
		'taxonomy'   => $tax,
		'hide_empty' => true,
		'number'     => (int) $limit,
		'orderby'    => 'count',
		'order'      => 'DESC',
	) );
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Products for the "Exclusive Combo Deals" section.
 * Looks for a `combo`/`combos`/`combo-deals` product category first, then
 * falls back to grouped products (WooCommerce's native "bundle" type).
 *
 * @param int $limit
 * @return WC_Product[]
 */
function isdb_combo_products( $limit = 8 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	foreach ( array( 'combo', 'combos', 'combo-deals', 'combo-offer' ) as $slug ) {
		if ( term_exists( $slug, 'product_cat' ) ) {
			$found = wc_get_products( array(
				'status'   => 'publish',
				'limit'    => (int) $limit,
				'category' => array( $slug ),
			) );
			if ( ! empty( $found ) ) {
				return $found;
			}
		}
	}

	$grouped = wc_get_products( array(
		'status' => 'publish',
		'limit'  => (int) $limit,
		'type'   => 'grouped',
	) );
	return is_array( $grouped ) ? $grouped : array();
}

/**
 * "Buy now" URL — adds the product to the cart and lands on checkout.
 * Uses WooCommerce's own ?add-to-cart handler, so stock/validation still apply.
 *
 * @param int $product_id
 * @return string
 */
function isdb_buy_now_url( $product_id ) {
	$checkout = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' );
	return add_query_arg( 'add-to-cart', (int) $product_id, $checkout );
}

/**
 * Reusable carousel shell.
 * Prints the opening markup; caller echoes the slides; then call
 * isdb_carousel_end(). Arrows are wired by the shared carousel JS.
 *
 * @param string $extra_track_class
 */
function isdb_carousel_start( $extra_track_class = '', $autoplay = 4500 ) {
	?>
	<div class="isdb-carousel relative"<?php echo $autoplay ? ' data-autoplay="' . (int) $autoplay . '"' : ''; ?>>
		<button type="button" class="isdb-car-prev absolute -left-3 top-1/2 z-20 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-brand-primary text-white shadow-md transition hover:bg-brand-hover disabled:pointer-events-none disabled:opacity-0" aria-label="Previous">
			<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
		</button>

		<div class="isdb-car-track flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth pb-1 sm:gap-4 <?php echo esc_attr( $extra_track_class ); ?>" style="scrollbar-width:none;">
	<?php
}

/** Closes the carousel shell opened by isdb_carousel_start(). */
function isdb_carousel_end() {
	?>
		</div>

		<button type="button" class="isdb-car-next absolute -right-3 top-1/2 z-20 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-brand-primary text-white shadow-md transition hover:bg-brand-hover disabled:pointer-events-none disabled:opacity-0" aria-label="Next">
			<svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
		</button>

		<!-- Pagination dots (built by the shared carousel JS) -->
		<div class="isdb-car-dots mt-4 flex items-center justify-center gap-2"></div>
	</div>
	<?php
}

/**
 * Product carousel — same shell as isdb_carousel_start/end, with each card in a
 * fixed-width snap cell (~2 / 3 / 4 / 5 per view). Autoplay + dots come free
 * from the shared carousel JS.
 *
 * @param WC_Product[] $products
 * @param int          $limit
 * @param int          $autoplay Milliseconds between auto-advances (0 = off).
 */
function isdb_render_product_carousel( $products, $limit = 12, $autoplay = 5000 ) {
	global $product, $post;
	$orig_product = $product;
	$orig_post    = $post;

	$valid = array();
	foreach ( $products as $p ) {
		if ( $p instanceof WC_Product && $p->is_visible() ) {
			$valid[] = $p;
		}
		if ( count( $valid ) >= $limit ) {
			break;
		}
	}
	if ( empty( $valid ) ) {
		return;
	}

	isdb_carousel_start( '', $autoplay );
	foreach ( $valid as $p ) {
		$post    = get_post( $p->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		$product = $p;                        // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $post );
		echo '<div class="isdb-car-cell w-[46%] flex-none snap-start sm:w-[30%] md:w-[23%] lg:w-[18.5%] [&>li]:h-full">';
		wc_get_template_part( 'content', 'product' );
		echo '</div>';
	}
	isdb_carousel_end();

	$product = $orig_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	$post    = $orig_post;    // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	wp_reset_postdata();
}

/**
 * Section header: title left, an "action →" link right (orange underline rule).
 * Shared by the homepage rails and the single-product related section.
 *
 * @param string $title
 * @param string $link
 * @param string $label
 */
function isdb_section_head( $title, $link = '', $label = 'View all items' ) {
	?>
	<div class="section-head mb-4 flex items-end justify-between gap-4 border-b border-[#ddd] pb-2.5">
		<h2 class="text-base font-bold tracking-tight text-brand-title sm:text-lg lg:text-xl"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $link ) : ?>
			<a href="<?php echo esc_url( $link ); ?>"
				class="group inline-flex flex-none items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wide text-brand-primary transition hover:underline sm:text-xs">
				<?php echo esc_html( $label ); ?>
				<svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/* ------------------------------------------------------------------ *
 * 7c. LIVE SEARCH  (type-ahead product results, no page reload)
 * ------------------------------------------------------------------ */

/**
 * Brand label for a product: native WooCommerce `product_brand` taxonomy when
 * present, otherwise the first product category. Empty string if neither.
 *
 * @param WC_Product $product
 * @return string
 */
function isdb_product_brand_label( $product ) {
	foreach ( array( 'product_brand', 'pwb-brand', 'yith_product_brand' ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			$terms = wp_get_post_terms( $product->get_id(), $tax, array( 'fields' => 'names' ) );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				return $terms[0];
			}
		}
	}
	$cats = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
	return ! empty( $cats ) ? $cats[0] : '';
}

add_action( 'wp_ajax_isdb_live_search', 'isdb_live_search' );
add_action( 'wp_ajax_nopriv_isdb_live_search', 'isdb_live_search' );
function isdb_live_search() {
	// Lightweight anti-scraping nonce. Soft-fail: degrade to empty results
	// rather than wp_die(), so a stale cached nonce never throws a 403 at users.
	if ( ! check_ajax_referer( 'isdb-frontend', 'nonce', false ) ) {
		wp_send_json_success( array( 'items' => array(), 'more' => '' ) );
	}

	$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

	if ( mb_strlen( $term ) < 2 || ! function_exists( 'wc_get_products' ) ) {
		wp_send_json_success( array( 'items' => array(), 'more' => '' ) );
	}

	$products = wc_get_products( array(
		'status'   => 'publish',
		'limit'    => 8,
		's'        => $term,
		'orderby'  => 'relevance',
	) );

	$items = array();
	foreach ( (array) $products as $p ) {
		if ( ! $p instanceof WC_Product || ! $p->is_visible() ) {
			continue;
		}
		$img_id = $p->get_image_id();
		$items[] = array(
			'title' => $p->get_name(),
			'url'   => $p->get_permalink(),
			'img'   => $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_gallery_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' ),
			'price' => wp_strip_all_tags( $p->get_price_html() ),
			'reg'   => ( $p->is_on_sale() && $p->get_regular_price() ) ? wp_strip_all_tags( wc_price( $p->get_regular_price() ) ) : '',
			'brand' => isdb_product_brand_label( $p ),
		);
	}

	wp_send_json_success( array(
		'items' => $items,
		'more'  => add_query_arg( array( 's' => rawurlencode( $term ), 'post_type' => 'product' ), home_url( '/' ) ),
	) );
}

/** Cart state for JS (used to swap card buttons into steppers after AJAX add). */
add_action( 'wp_ajax_isdb_cart_state', 'isdb_cart_state' );
add_action( 'wp_ajax_nopriv_isdb_cart_state', 'isdb_cart_state' );
function isdb_cart_state() {
	// Same lightweight nonce as live search; soft-fail to an empty map.
	if ( ! check_ajax_referer( 'isdb-frontend', 'nonce', false ) ) {
		wp_send_json_success( array( 'items' => array() ) );
	}
	wp_send_json_success( array( 'items' => isdb_cart_qty_map() ) );
}

add_action( 'wp_ajax_isdb_checkout_qty', 'isdb_checkout_qty' );
add_action( 'wp_ajax_nopriv_isdb_checkout_qty', 'isdb_checkout_qty' );
function isdb_checkout_qty() {
	check_ajax_referer( 'isdb-checkout-qty', 'nonce' );

	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => 'Cart unavailable.' ), 400 );
	}

	$key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	$qty = isset( $_POST['quantity'] ) ? (int) $_POST['quantity'] : -1;

	$cart_item = WC()->cart->get_cart_item( $key );
	if ( ! $key || $qty < 0 || ! $cart_item ) {
		wp_send_json_error( array( 'message' => 'Invalid cart item.' ), 400 );
	}

	// Respect stock limits / sold-individually.
	$product = $cart_item['data'];
	if ( $product instanceof WC_Product ) {
		if ( $product->is_sold_individually() ) {
			$qty = min( 1, $qty );
		}
		$max = $product->get_max_purchase_quantity();
		if ( $max > 0 ) {
			$qty = min( $qty, $max );
		}
		if ( $qty > 0 && ! $product->has_enough_stock( $qty ) ) {
			wp_send_json_error( array( 'message' => 'Not enough stock.' ), 400 );
		}
	}

	// qty 0 removes the line (WooCommerce handles this in set_quantity).
	WC()->cart->set_quantity( $key, $qty, true );

	wp_send_json_success( array(
		'count'    => WC()->cart->get_cart_contents_count(),
		'is_empty' => WC()->cart->is_empty(),
		'cart_url' => wc_get_cart_url(),
	) );
}

/**
 * Fresh cart-qty nonce, served UNCACHED via WC-AJAX (?wc-ajax=isdb_nonce).
 * Full-page caching freezes the wp_localize nonce, so admin-ajax's
 * check_ajax_referer() 403s; the stepper JS refetches from here and retries.
 * WC-AJAX sends no-cache headers and runs for guests + logged-in users. The
 * response is same-origin readable only, so it stays CSRF-safe.
 */
add_action( 'wc_ajax_isdb_nonce', 'isdb_ajax_nonce' );
function isdb_ajax_nonce() {
	wp_send_json( array( 'nonce' => wp_create_nonce( 'isdb-checkout-qty' ) ) );
}

/**
 * Inline JS for the checkout quantity stepper.
 * Delegated binding: the review box is replaced wholesale on every
 * update_checkout, so handlers must survive DOM replacement.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}
	wp_register_script( 'isdb-qty', '', array( 'jquery' ), ISDB_VERSION, true );
	wp_enqueue_script( 'isdb-qty' );
	wp_localize_script( 'isdb-qty', 'isdbQty', array(
		'ajax'       => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'isdb-checkout-qty' ),
		'snonce'     => wp_create_nonce( 'isdb-frontend' ),
		'isCheckout' => ( function_exists( 'is_checkout' ) && is_checkout() ) ? 1 : 0,
		'cartUrl'    => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
		'nonceUrl'   => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( 'isdb_nonce' ) : '',
	) );
	wp_add_inline_script( 'isdb-qty', <<<'JS'
jQuery(function ($) {
	var busy = false;

	function refreshCards() {
		// Swap "Add to cart" <-> stepper on product cards using live cart state.
		$.post(isdbQty.ajax, { action: 'isdb_cart_state', nonce: isdbQty.snonce }).done(function (res) {
			if (!res || !res.success) { return; }
			var items = res.data.items || {};
			$('.isdb-cart-ctl').each(function () {
				var $ctl = $(this);
				var pid  = String($ctl.data('product'));
				var st   = items[pid];
				if (st) {
					$ctl.find('.isdb-add-wrap').addClass('hidden');
					var $st = $ctl.find('.isdb-step-wrap').removeClass('hidden');
					$st.find('.isdb-step-val').text(st.qty);
					$st.find('.isdb-qty-btn').attr('data-key', st.key);
					$st.find('.isdb-step-minus').attr('data-qty', st.qty - 1);
					$st.find('.isdb-step-plus').attr('data-qty', st.qty + 1);
				} else {
					$ctl.find('.isdb-step-wrap').addClass('hidden');
					$ctl.find('.isdb-add-wrap').removeClass('hidden');
				}
			});
		});
	}

	// Cart-qty nonce, kept fresh via an UNCACHED WC-AJAX endpoint. A full-page
	// cache embeds a stale wp_localize nonce, so admin-ajax's check_ajax_referer
	// returns 403; we refetch a fresh nonce and retry once. Readable same-origin
	// only, so it stays CSRF-safe.
	var isdbNonce = (window.isdbQty && isdbQty.nonce) ? isdbQty.nonce : '';
	function isdbWcAjax(action) {
		return (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url)
			? wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', action)
			: '';
	}
	function isdbRefreshNonce(cb) {
		var u = (window.isdbQty && isdbQty.nonceUrl) ? isdbQty.nonceUrl : isdbWcAjax('isdb_nonce');
		if (!u) { cb(); return; }
		$.get(u).done(function (r) { if (r && r.nonce) { isdbNonce = r.nonce; } }).always(cb);
	}

	$(document.body).on('click', '.isdb-qty-btn', function (e) {
		e.preventDefault();
		e.stopPropagation();
		if (busy) { return; }

		var $btn = $(this);
		var key  = $btn.attr('data-key');
		var qty  = parseInt($btn.attr('data-qty'), 10);
		if (!key || isNaN(qty) || qty < 0) { return; }

		busy = true;
		$btn.closest('.isdb-review-item, .isdb-cart-ctl, .isdb-mini-item').css('opacity', 0.5);

		function finish() {
			busy = false;
			$('.isdb-review-item, .isdb-cart-ctl, .isdb-mini-item').css('opacity', '');
		}
		function done(res) {
			if (isdbQty.isCheckout) {
				if (res && res.success && res.data && res.data.is_empty) {
					window.location.href = res.data.cart_url;
					return;
				}
				$(document.body).trigger('update_checkout');
			}
			$(document.body).trigger('wc_fragment_refresh');
			refreshCards();
		}
		function send(retry) {
			$.post(isdbQty.ajax, {
				action: 'isdb_checkout_qty',
				nonce: isdbNonce,
				cart_item_key: key,
				quantity: qty
			}).done(function (res) {
				done(res);
				finish();
			}).fail(function (xhr) {
				if (xhr && xhr.status === 403 && !retry) {
					isdbRefreshNonce(function () { send(true); });
				} else {
					finish();
				}
			});
		}
		send(false);
	});

	// After WooCommerce's own AJAX add-to-cart, turn that card into a stepper.
	$(document.body).on('added_to_cart wc_fragments_refreshed wc_fragments_loaded', function () {
		refreshCards();
	});

	// Toast confirmation on AJAX add-to-cart (replaces WC's full-page notice).
	function isdbToast(title) {
		var wrap = document.querySelector('.wmb-toast-wrap');
		if (!wrap) {
			wrap = document.createElement('div');
			wrap.className = 'wmb-toast-wrap';
			document.body.appendChild(wrap);
		}
		var cartUrl = (window.isdbQty && isdbQty.cartUrl) ? isdbQty.cartUrl : '';
		var t = document.createElement('div');
		t.className = 'wmb-toast';
		t.setAttribute('role', 'status');
		t.innerHTML =
			'<span class="wmb-toast__icon" aria-hidden="true">✓</span>' +
			'<span class="wmb-toast__body">' +
				'<span class="wmb-toast__title"></span>' +
				'<span class="wmb-toast__sub">Added to your cart</span>' +
			'</span>' +
			(cartUrl ? '<a class="wmb-toast__cta" href="' + cartUrl + '">View cart →</a>' : '');
		t.querySelector('.wmb-toast__title').textContent = title || 'Item added';
		wrap.appendChild(t);
		requestAnimationFrame(function () { t.classList.add('is-in'); });
		setTimeout(function () {
			t.classList.remove('is-in');
			setTimeout(function () { if (t.parentNode) { t.parentNode.removeChild(t); } }, 420);
		}, 3500);
	}

	$(document.body).on('added_to_cart', function (e, fragments, cart_hash, $btn) {
		var name = '';
		try {
			var $b = $($btn);
			name = $.trim($b.closest('li.product').find('h3').first().text());
			if (!name) { name = $.trim($b.closest('.product, form.wmb-cart, form.cart').find('.product_title, h1').first().text()); }
			if (!name) { name = $.trim($('.product_title, h1.entry-title').first().text()); }
		} catch (err) {}
		isdbToast(name || 'Item added to cart');
	});

	// Single-product add-to-cart via AJAX — never navigate to /cart/. Adds the
	// product, refreshes the cart fragments (the floating pill + mini-cart) and
	// shows the toast. Variable/grouped forms are left to WooCommerce (they need
	// variation handling; with redirect off they simply reload the same page).
	$(document.body).on('submit', 'form.wmb-cart, form.cart', function (e) {
		var $form = $(this);
		if ($form.is('.variations_form, .grouped_form') || $form.find('[name="variation_id"]').length) {
			return; // let WooCommerce handle these natively
		}
		var pid = $form.find('[name="add-to-cart"]').val();
		if (!pid || isNaN(parseInt(pid, 10))) { return; } // can't resolve -> normal submit
		if (typeof wc_add_to_cart_params === 'undefined' || !wc_add_to_cart_params.wc_ajax_url) { return; }
		e.preventDefault();
		var qty = parseInt($form.find('[name="quantity"]').val(), 10);
		if (!qty || qty < 1) { qty = 1; }
		var $submit = $form.find('button[type="submit"], [name="add-to-cart"]').first();
		$submit.addClass('loading').prop('disabled', true);
		var url = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart');
		$.post(url, { product_id: pid, quantity: qty }, function (res) {
			if (!res) { return; }
			if (res.error && res.product_url) { window.location = res.product_url; return; }
			if (res.fragments) {
				$.each(res.fragments, function (key, value) { $(key).replaceWith(value); });
			}
			$(document.body).trigger('wc_fragments_loaded');
			$(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash, $submit]);
		}).always(function () {
			$submit.removeClass('loading').prop('disabled', false);
		});
	});

	// Self-heal a STALE cached mini-cart fragment: WooCommerce stores the mini
	// cart HTML in the browser (localStorage), so after a template update the old
	// markup (no pinned footer) can render inside the new drawer shell. If items
	// are present but the new footer isn't, force one fragment refresh.
	setTimeout(function () {
		var $c = $('.widget_shopping_cart_content');
		if ($c.length && $c.find('.woocommerce-mini-cart').length && !$c.find('.wmb-mini-foot').length) {
			$(document.body).trigger('wc_fragment_refresh');
		}
	}, 800);

	/* ---------------- LIVE SEARCH (type-ahead) ---------------- */
	var timer = null, lastQ = '', xhr = null;

	function hideBox($box) { $box.addClass('hidden').empty(); }

	function render($box, data, q) {
		if (!data.items.length) {
			$box.removeClass('hidden').html(
				'<li class="px-4 py-4 text-center text-[13px] text-slate-500">No products found for "' +
				$('<i>').text(q).html() + '"</li>'
			);
			return;
		}
		var html = '';
		data.items.forEach(function (it) {
			html += '<li class="border-b border-slate-100 last:border-b-0">' +
				'<a href="' + it.url + '" class="flex items-center gap-3 px-3 py-2.5 transition hover:bg-brand-bg">' +
					'<img src="' + it.img + '" alt="" width="50" height="50" loading="lazy" decoding="async" class="h-[50px] w-[50px] flex-none rounded border border-slate-200 object-contain" />' +
					'<span class="min-w-0 flex-1">' +
						'<span class="block truncate text-[13px] font-medium text-brand-title">' + $('<i>').text(it.title).html() + '</span>' +
						'<span class="mt-0.5 block text-[13px] font-semibold text-brand-primary">' + it.price +
							(it.reg ? ' <s class="ml-1 text-[11px] font-normal text-slate-400">' + it.reg + '</s>' : '') +
						'</span>' +
						(it.brand ? '<span class="block text-[11px] text-slate-500">' + $('<i>').text(it.brand).html() + '</span>' : '') +
					'</span>' +
				'</a></li>';
		});
		html += '<li class="border-t border-slate-100"><a href="' + data.more +
			'" class="block px-4 py-2.5 text-center text-[12px] font-semibold text-brand-primary hover:underline">View all results &rarr;</a></li>';
		$box.removeClass('hidden').html(html);
	}

	$(document.body).on('input', '.isdb-search-input', function () {
		var $input = $(this);
		var $box   = $input.closest('.isdb-search').find('.isdb-search-box');
		var q      = $.trim($input.val());

		clearTimeout(timer);
		if (q.length < 2) { hideBox($box); lastQ = ''; return; }
		if (q === lastQ) { return; }

		timer = setTimeout(function () {
			lastQ = q;
			if (xhr && xhr.readyState !== 4) { xhr.abort(); }
			$box.removeClass('hidden').html('<li class="px-4 py-4 text-center text-[13px] text-slate-500">Searching&hellip;</li>');
			xhr = $.get(isdbQty.ajax, { action: 'isdb_live_search', q: q, nonce: isdbQty.snonce }).done(function (res) {
				if (res && res.success) { render($box, res.data, q); }
			});
		}, 300); // debounce
	});

	// Re-open results when refocusing a field that still has a query.
	$(document.body).on('focus', '.isdb-search-input', function () {
		var $box = $(this).closest('.isdb-search').find('.isdb-search-box');
		if ($box.children().length) { $box.removeClass('hidden'); }
	});

	// Close on outside click / Escape.
	$(document).on('click', function (e) {
		if (!$(e.target).closest('.isdb-search').length) { $('.isdb-search-box').addClass('hidden'); }
	});
	$(document).on('keydown', function (e) {
		if (e.key === 'Escape') { $('.isdb-search-box').addClass('hidden'); }
	});

	/* --------- CAROUSELS (scroll-snap + arrows + dots + autoplay) ----------
	   Native scroll-snap track (free touch/trackpad swipe). Arrows & dots
	   scrollBy() one page; data-autoplay="ms" auto-advances and loops.       */
	function carPages(track) {
		var w = track.clientWidth;
		var max = track.scrollWidth - w;
		return max > 4 ? Math.round(max / w) + 1 : 1;
	}

	function syncCar($car) {
		var track = $car.find('.isdb-car-track')[0];
		if (!track) { return; }
		var w = track.clientWidth;
		var max = track.scrollWidth - w;
		var scrollable = max > 4;
		var pages = carPages(track);
		var active = Math.min(pages - 1, Math.round(track.scrollLeft / Math.max(1, w)));

		$car.find('.isdb-car-prev, .isdb-car-next')
			.toggleClass('hidden', !scrollable)
			.toggleClass('sm:flex', scrollable);
		$car.find('.isdb-car-prev').prop('disabled', track.scrollLeft <= 2);
		$car.find('.isdb-car-next').prop('disabled', track.scrollLeft >= max - 2);

		var $dots = $car.find('.isdb-car-dots');
		if (!$dots.length) { return; }
		if (!scrollable) { $dots.addClass('hidden').empty(); $dots.data('pages', 0); return; }
		$dots.removeClass('hidden');
		if ($dots.data('pages') !== pages) {
			$dots.data('pages', pages).empty();
			for (var d = 0; d < pages; d++) {
				$dots.append('<button type="button" class="isdb-car-dot h-2 w-2 rounded-full bg-brand-line transition-all" data-page="' + d + '" aria-label="Go to page ' + (d + 1) + '"></button>');
			}
		}
		$dots.children().each(function (idx) {
			$(this).toggleClass('!w-6 !bg-brand-primary', idx === active)
			       .toggleClass('bg-brand-line', idx !== active);
		});
	}

	function initCarousels() {
		$('.isdb-carousel').each(function () {
			var $car = $(this);
			var track = $car.find('.isdb-car-track')[0];
			if (!track) { return; }

			if (!$car.data('isdbInit')) {
				$car.data('isdbInit', true);

				$car.find('.isdb-car-prev').on('click', function () {
					track.scrollBy({ left: -track.clientWidth, behavior: 'smooth' });
				});
				$car.find('.isdb-car-next').on('click', function () {
					track.scrollBy({ left: track.clientWidth, behavior: 'smooth' });
				});
				$car.on('click', '.isdb-car-dot', function () {
					track.scrollTo({ left: $(this).data('page') * track.clientWidth, behavior: 'smooth' });
				});
				track.addEventListener('scroll', function () { syncCar($car); }, { passive: true });

				var ms = parseInt($car.attr('data-autoplay'), 10);
				if (ms && ms > 1200) {
					var tick = function () {
						var w = track.clientWidth;
						var max = track.scrollWidth - w;
						if (max <= 4) { return; }
						track.scrollTo({ left: (track.scrollLeft >= max - 2) ? 0 : track.scrollLeft + w, behavior: 'smooth' });
					};
					var start = function () { $car.data('carTimer', setInterval(tick, ms)); };
					var stop  = function () { clearInterval($car.data('carTimer')); };
					start();
					$car.on('mouseenter touchstart pointerdown', stop).on('mouseleave touchend', start);
				}
			}

			syncCar($car);
		});
	}

	initCarousels();
	$(window).on('resize', function () { $('.isdb-carousel').each(function () { syncCar($(this)); }); });
	// Re-sync after images load (scrollWidth changes as they paint).
	$(window).on('load', initCarousels);
	$(document.body).on('wc_fragments_refreshed', initCarousels);
});
JS
	);
}, 30 );

/**
 * Canonical shop-view URLs the theme guarantees to render products. Point the
 * "New Arrivals" and "Deals/Offers" menu items at these.
 *   New Arrivals -> {shop}?orderby=date   (newest first, WooCommerce native)
 *   Deals        -> {shop}?on_sale=1       (handled by isdb_apply_shop_filters)
 */
function isdb_new_arrivals_url() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	return apply_filters( 'isdb_new_arrivals_url', add_query_arg( 'orderby', 'date', $shop ) );
}
function isdb_deals_url() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
	return apply_filters( 'isdb_deals_url', add_query_arg( 'on_sale', '1', $shop ) );
}

/**
 * A bare "New Arrivals" / "Deals" PAGE has no product loop, so it shows nothing.
 * If a visitor lands on one (menu still points at the clean /new-arrivals/ URL),
 * send them to the shop view that actually lists products.
 */
add_action( 'template_redirect', function () {
	if ( is_admin() || ! is_page() ) {
		return;
	}
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	$map  = array(
		'new-arrivals'     => 'new',
		'new-arrival'      => 'new',
		'deals'            => 'deal',
		'deals-offers'     => 'deal',
		'deals-and-offers' => 'deal',
		'deal-offers'      => 'deal',
		'offers'           => 'deal',
		'offer'            => 'deal',
		'on-sale'          => 'deal',
	);
	if ( isset( $map[ $slug ] ) ) {
		wp_safe_redirect( 'new' === $map[ $slug ] ? isdb_new_arrivals_url() : isdb_deals_url(), 302 );
		exit;
	}
} );

/* ------------------------------------------------------------------ *
 * 8. RAW SHOP FILTER ENGINE  (no plugin)
 *    Reads our own GET params and injects tax_query/meta_query into
 *    the WooCommerce product archive query. Sorting (?orderby=) is
 *    left to WooCommerce core, which already handles it.
 * ------------------------------------------------------------------ */
add_action( 'woocommerce_product_query', 'isdb_apply_shop_filters', 20, 1 );
function isdb_apply_shop_filters( $q ) {
	if ( is_admin() ) {
		return;
	}

	$meta_query = (array) $q->get( 'meta_query' );
	$tax_query  = (array) $q->get( 'tax_query' );

	// --- On sale only (this is what the homepage deal band links to) ---
	if ( ! empty( $_GET['on_sale'] ) && function_exists( 'wc_get_product_ids_on_sale' ) ) {
		$sale_ids = wc_get_product_ids_on_sale();
		$sale_ids = ! empty( $sale_ids ) ? $sale_ids : array( 0 ); // 0 => "no matches" (never "all")
		$existing = (array) $q->get( 'post__in' );
		$q->set( 'post__in', $existing ? array_intersect( $existing, $sale_ids ) : $sale_ids );
	}

	// --- Price range ---
	$min = isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ? floatval( $_GET['min_price'] ) : null;
	$max = isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ? floatval( $_GET['max_price'] ) : null;
	if ( null !== $min || null !== $max ) {
		$price = array( 'key' => '_price', 'type' => 'NUMERIC' );
		if ( null !== $min && null !== $max ) {
			$price['value']   = array( $min, $max );
			$price['compare'] = 'BETWEEN';
		} elseif ( null !== $min ) {
			$price['value']   = $min;
			$price['compare'] = '>=';
		} else {
			$price['value']   = $max;
			$price['compare'] = '<=';
		}
		$meta_query[] = $price;
	}

	// --- Minimum average rating ---
	if ( ! empty( $_GET['rating'] ) ) {
		$meta_query[] = array(
			'key'     => '_wc_average_rating',
			'value'   => floatval( $_GET['rating'] ),
			'compare' => '>=',
			'type'    => 'DECIMAL(3,2)',
		);
	}

	// --- In stock only ---
	if ( ! empty( $_GET['instock'] ) ) {
		$meta_query[] = array(
			'key'   => '_stock_status',
			'value' => 'instock',
		);
	}

	// --- Category filter (checkbox array OR comma-separated slugs) ---
	if ( ! empty( $_GET['filter_cat'] ) ) {
		$raw   = is_array( $_GET['filter_cat'] ) ? $_GET['filter_cat'] : explode( ',', wp_unslash( $_GET['filter_cat'] ) );
		$slugs = array_filter( array_map( 'sanitize_title', (array) $raw ) );
		if ( $slugs ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $slugs,
			);
		}
	}

	if ( ! empty( $meta_query ) ) {
		$q->set( 'meta_query', $meta_query );
	}
	if ( ! empty( $tax_query ) ) {
		$q->set( 'tax_query', $tax_query );
	}
}

/* ------------------------------------------------------------------ *
 * 9. THEME OPTIONS  (Appearance -> Customize -> Theme Options)
 *    Contact details, social links, mobile-app links and the newsletter
 *    toggle are all editable in the Customizer. Every front-end use reads
 *    get_theme_mod() through the helpers below, so buyers configure the
 *    theme in the UI - no code edits, nothing hardcoded.
 * ------------------------------------------------------------------ */

/** String option getter. */
function isdb_opt( $key, $default = '' ) {
	return get_theme_mod( $key, $default );
}

/** Boolean option getter (checkbox). */
function isdb_opt_bool( $key, $default = false ) {
	return (bool) get_theme_mod( $key, $default );
}

/** Store phone number (display form). */
function isdb_phone() {
	return isdb_opt( 'isdb_phone', '+880 1868 662477' );
}

/** Store phone as a tel: href (keeps digits and a leading +). */
function isdb_phone_tel() {
	$raw = preg_replace( '/[^0-9+]/', '', isdb_phone() );
	return $raw ? 'tel:' . $raw : '';
}

/** Store contact email. */
function isdb_email() {
	return isdb_opt( 'isdb_email', 'info.waymore.bd@gmail.com' );
}

/** Store address (single line). */
function isdb_address() {
	return isdb_opt( 'isdb_address', 'New Market, Dhaka, Bangladesh' );
}

/**
 * Social links that are actually set. Facebook defaults to the brand page
 * (ISDB_FACEBOOK_URL); Instagram & YouTube stay empty until configured, so
 * their icons are hidden until the store owner adds a URL.
 *
 * @return array<int,array{key:string,label:string,url:string,path:string}>
 */
function isdb_social_links() {
	$fb_default = defined( 'ISDB_FACEBOOK_URL' ) ? ISDB_FACEBOOK_URL : '';
	$defs = array(
		'facebook'  => array( 'Facebook', isdb_opt( 'isdb_facebook', $fb_default ), 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z' ),
		'instagram' => array( 'Instagram', isdb_opt( 'isdb_instagram', '' ), 'M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0Zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227a3.81 3.81 0 0 1-.9 1.382 3.744 3.744 0 0 1-1.38.896c-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421a3.716 3.716 0 0 1-1.379-.9 3.644 3.644 0 0 1-.9-1.38c-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03Zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162ZM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4Zm7.846-10.405a1.441 1.441 0 0 1-2.88 0 1.44 1.44 0 0 1 2.88 0Z' ),
		'youtube'   => array( 'YouTube', isdb_opt( 'isdb_youtube', '' ), 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814ZM9.545 15.568V8.432L15.818 12l-6.273 3.568Z' ),
	);
	$out = array();
	foreach ( $defs as $key => $d ) {
		if ( ! empty( $d[1] ) ) {
			$out[] = array( 'key' => $key, 'label' => $d[0], 'url' => $d[1], 'path' => $d[2] );
		}
	}
	return $out;
}

/** Checkbox sanitizer for the Customizer. */
function isdb_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true === (bool) $checked );
}

/** Register the "Theme Options" panel and its settings. */
add_action( 'customize_register', 'isdb_customize_register' );
function isdb_customize_register( $wp_customize ) {

	$wp_customize->add_panel( 'isdb_theme_options', array(
		'title'    => __( 'Theme Options', 'isdb-custom' ),
		'priority' => 30,
	) );

	/* ---- Contact Information ---- */
	$wp_customize->add_section( 'isdb_contact', array(
		'title' => __( 'Contact Information', 'isdb-custom' ),
		'panel' => 'isdb_theme_options',
	) );
	$wp_customize->add_setting( 'isdb_phone', array(
		'default'           => '+880 1868 662477',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'isdb_phone', array(
		'label'   => __( 'Phone Number', 'isdb-custom' ),
		'section' => 'isdb_contact',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'isdb_email', array(
		'default'           => 'info.waymore.bd@gmail.com',
		'sanitize_callback' => 'sanitize_email',
	) );
	$wp_customize->add_control( 'isdb_email', array(
		'label'   => __( 'Email Address', 'isdb-custom' ),
		'section' => 'isdb_contact',
		'type'    => 'email',
	) );
	$wp_customize->add_setting( 'isdb_address', array(
		'default'           => 'New Market, Dhaka, Bangladesh',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'isdb_address', array(
		'label'   => __( 'Address', 'isdb-custom' ),
		'section' => 'isdb_contact',
		'type'    => 'text',
	) );

	/* ---- Social Media ---- */
	$wp_customize->add_section( 'isdb_social', array(
		'title' => __( 'Social Media', 'isdb-custom' ),
		'panel' => 'isdb_theme_options',
	) );
	$isdb_social_fields = array(
		'isdb_facebook'  => __( 'Facebook URL', 'isdb-custom' ),
		'isdb_instagram' => __( 'Instagram URL', 'isdb-custom' ),
		'isdb_youtube'   => __( 'YouTube URL', 'isdb-custom' ),
	);
	foreach ( $isdb_social_fields as $isdb_key => $isdb_label ) {
		$isdb_default = ( 'isdb_facebook' === $isdb_key && defined( 'ISDB_FACEBOOK_URL' ) ) ? ISDB_FACEBOOK_URL : '';
		$wp_customize->add_setting( $isdb_key, array(
			'default'           => $isdb_default,
			'sanitize_callback' => 'esc_url_raw',
		) );
		$wp_customize->add_control( $isdb_key, array(
			'label'       => $isdb_label,
			'section'     => 'isdb_social',
			'type'        => 'url',
			'description' => __( 'Leave empty to hide this icon.', 'isdb-custom' ),
		) );
	}

	/* ---- Mobile App Links ---- */
	$wp_customize->add_section( 'isdb_apps', array(
		'title' => __( 'Mobile App Links', 'isdb-custom' ),
		'panel' => 'isdb_theme_options',
	) );
	$wp_customize->add_setting( 'isdb_google_play', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'isdb_google_play', array(
		'label'       => __( 'Google Play URL', 'isdb-custom' ),
		'section'     => 'isdb_apps',
		'type'        => 'url',
		'description' => __( 'Leave empty to hide the badge.', 'isdb-custom' ),
	) );
	$wp_customize->add_setting( 'isdb_app_store', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'isdb_app_store', array(
		'label'       => __( 'App Store URL', 'isdb-custom' ),
		'section'     => 'isdb_apps',
		'type'        => 'url',
		'description' => __( 'Leave empty to hide the badge.', 'isdb-custom' ),
	) );

	/* ---- Newsletter ---- */
	$wp_customize->add_section( 'isdb_newsletter', array(
		'title' => __( 'Newsletter', 'isdb-custom' ),
		'panel' => 'isdb_theme_options',
	) );
	$wp_customize->add_setting( 'isdb_newsletter_enable', array(
		'default'           => true,
		'sanitize_callback' => 'isdb_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'isdb_newsletter_enable', array(
		'label'   => __( 'Show newsletter signup in the footer', 'isdb-custom' ),
		'section' => 'isdb_newsletter',
		'type'    => 'checkbox',
	) );
}

/* ------------------------------------------------------------------ *
 * 9a. DYNAMIC FOOTER MENUS
 *     Each footer column is a nav-menu location. Until the owner assigns a
 *     menu, isdb_footer_menu_fallback() renders the curated default links,
 *     so the footer looks complete out of the box.
 * ------------------------------------------------------------------ */

/** Curated default links per footer column (used when no menu is assigned). */
function isdb_footer_default_links( $location ) {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	switch ( $location ) {
		case 'footer_information':
			return array(
				array( 'label' => __( 'About us', 'isdb-custom' ), 'url' => home_url( '/about-us/' ) ),
				array( 'label' => __( 'Contact us', 'isdb-custom' ), 'url' => home_url( '/contact/' ) ),
				array( 'label' => __( 'Company Information', 'isdb-custom' ), 'url' => home_url( '/company-information/' ) ),
				array( 'label' => __( 'Our Stories', 'isdb-custom' ), 'url' => home_url( '/our-stories/' ) ),
				array( 'label' => __( 'Terms & Conditions', 'isdb-custom' ), 'url' => home_url( '/terms-and-conditions/' ) ),
				array( 'label' => __( 'Privacy Policy', 'isdb-custom' ), 'url' => home_url( '/privacy-policy/' ) ),
				array( 'label' => __( 'Careers', 'isdb-custom' ), 'url' => home_url( '/careers/' ) ),
			);

		case 'footer_shop':
			$links = array();
			if ( function_exists( 'isdb_top_categories' ) ) {
				foreach ( isdb_top_categories( 5 ) as $term ) {
					$links[] = array( 'label' => $term->name, 'url' => get_term_link( $term ) );
				}
			}
			$links[] = array( 'label' => __( 'All Products', 'isdb-custom' ), 'url' => $shop_url );
			$links[] = array( 'label' => __( 'On Sale', 'isdb-custom' ), 'url' => add_query_arg( 'on_sale', '1', $shop_url ) );
			return $links;

		case 'footer_support':
			return array(
				array( 'label' => __( 'Support Center', 'isdb-custom' ), 'url' => home_url( '/support/' ) ),
				array( 'label' => __( 'How to Order', 'isdb-custom' ), 'url' => home_url( '/how-to-order/' ) ),
				array( 'label' => __( 'Order Tracking', 'isdb-custom' ), 'url' => function_exists( 'isdb_track_order_url' ) ? isdb_track_order_url() : home_url( '/track-order/' ) ),
				array( 'label' => __( 'Payment', 'isdb-custom' ), 'url' => home_url( '/payment/' ) ),
				array( 'label' => __( 'Shipping', 'isdb-custom' ), 'url' => home_url( '/shipping-policy/' ) ),
				array( 'label' => __( 'FAQ', 'isdb-custom' ), 'url' => home_url( '/faq/' ) ),
			);

		case 'footer_policy':
			return array(
				array( 'label' => __( 'Happy Return', 'isdb-custom' ), 'url' => home_url( '/return-refund-policy/' ) ),
				array( 'label' => __( 'Refund Policy', 'isdb-custom' ), 'url' => home_url( '/return-refund-policy/' ) ),
				array( 'label' => __( 'Exchange', 'isdb-custom' ), 'url' => home_url( '/exchange/' ) ),
				array( 'label' => __( 'Cancellation', 'isdb-custom' ), 'url' => home_url( '/cancellation/' ) ),
				array( 'label' => __( 'Pre-Order', 'isdb-custom' ), 'url' => home_url( '/pre-order/' ) ),
				array( 'label' => __( 'Extra Discount', 'isdb-custom' ), 'url' => add_query_arg( 'on_sale', '1', $shop_url ) ),
			);
	}
	return array();
}

/**
 * wp_nav_menu fallback: renders the curated default links for a footer column
 * with the same Tailwind classes as an assigned menu.
 *
 * @param array $args wp_nav_menu args (theme_location, menu_class, echo...).
 * @return string
 */
function isdb_footer_menu_fallback( $args ) {
	$location = isset( $args['theme_location'] ) ? $args['theme_location'] : '';
	$links    = isdb_footer_default_links( $location );
	if ( empty( $links ) ) {
		return '';
	}
	$class = isset( $args['menu_class'] ) ? $args['menu_class'] : '';
	$html  = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $link ) {
		$html .= '<li><a href="' . esc_url( $link['url'] ) . '">' . esc_html( $link['label'] ) . '</a></li>';
	}
	$html .= '</ul>';

	if ( empty( $args['echo'] ) ) {
		return $html;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_html() above.
	return '';
}

/**
 * Render one footer column menu (assigned nav menu, or curated fallback).
 *
 * @param string $location Registered footer menu location.
 */
function isdb_footer_menu( $location ) {
	wp_nav_menu( array(
		'theme_location' => $location,
		'container'      => false,
		'menu_class'     => 'mt-4 space-y-2.5 text-sm [&_a]:text-brand-body [&_a]:transition [&_a:hover]:text-brand-primary',
		'depth'          => 1,
		'fallback_cb'    => 'isdb_footer_menu_fallback',
	) );
}
