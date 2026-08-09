=== Way More BD — ISDB Custom ===

Contributors: Khondoker Moin Hossain
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: e-commerce, woocommerce, custom-menu, featured-images, translation-ready, rtl-language-support, full-width-template

A 100% custom-coded, high-performance WooCommerce theme for Way More BD — "Your Trusted Kitchen Companion." Raw PHP + Tailwind CSS + Alpine.js. No page builders.

== Description ==

Way More BD (ISDB Custom) is a hand-built, conversion-focused WooCommerce theme designed for modern e-commerce stores. It ships a bespoke shop, product, cart, checkout, my-account and order-tracking experience — all styled with a compiled Tailwind CSS build and lightly enhanced with Alpine.js. No third-party page builders, no bloat.

Key features:

* 100% custom WooCommerce templates (shop, single product, cart, checkout, my account, thank-you / order tracker).
* HPOS (High-Performance Order Storage) compatible.
* Self-hosted assets — no external CDN requests (GDPR-friendly, offline-safe).
* Native Customizer panel (Appearance → Customize → Theme Options) for contact details, social links, mobile-app links and the newsletter toggle.
* Dynamic footer menus (four registered menu locations) with curated fallbacks.
* Live AJAX product search and cart quantity steppers, protected with nonces.
* Public, ownership-verified order tracker with per-IP rate limiting.
* Fully translation-ready — every user-facing string is wrapped with the `isdb-custom` text domain, and a `.pot` file is bundled in `/languages`.

== Installation ==

1. In your WordPress admin, go to Appearance → Themes → Add New → Upload Theme.
2. Upload the theme ZIP and click Install Now, then Activate.
3. Ensure the WooCommerce plugin is installed and active.
4. Go to Appearance → Customize → Theme Options to set your phone, email, address, social and app-store links.
5. (Optional) Assign menus under Appearance → Menus to the four footer menu locations.

== Frequently Asked Questions ==

= Does this theme require WooCommerce? =
Yes. Way More BD is a WooCommerce theme and expects WooCommerce (8.0+) to be active.

= Where do I change the phone number, email and social links? =
Appearance → Customize → Theme Options. Nothing is hardcoded — leave a social or app link empty to hide that icon.

= Is the theme translation-ready? =
Yes. All strings use the `isdb-custom` text domain, and `languages/isdb-custom.pot` is bundled. Drop your `.po`/`.mo` files into `/languages`.

== Changelog ==

= 1.0.0 =
* Initial release.
* Custom WooCommerce templates, Customizer Theme Options, dynamic footer menus.
* Self-hosted Open Sans + Alpine.js (no CDN).
* Full internationalization with bundled .pot.
* AJAX search / cart-state hardening with nonces; ownership-verified order tracking.

== Copyright & Credits ==

Way More BD — ISDB Custom, Copyright 2026 Khondoker Moin Hossain.
Way More BD is distributed under the terms of the GNU GPL v2 or later.

This theme bundles the following third-party open-source resources:

* Alpine.js — Copyright (c) Caleb Porzio and contributors.
  Licensed under the MIT License. https://alpinejs.dev/
* Open Sans (webfont) — Copyright the Open Sans Project Authors.
  Licensed under the Apache License, Version 2.0.
  https://fonts.google.com/specimen/Open+Sans
* Heroicons (inlined SVG icons) — Copyright (c) Tailwind Labs.
  Licensed under the MIT License. https://heroicons.com/
* Simple Icons (brand glyphs) — CC0 1.0 Universal. https://simpleicons.org/

All other code and assets are original work by the theme author and are
licensed under the GNU GPL v2 or later.
