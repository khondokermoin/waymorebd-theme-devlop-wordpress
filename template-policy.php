<?php
/**
 * Template Name: Policy Page
 *
 * Branded layout for policy / legal content (Return & Refund, Terms, Privacy).
 * Hero (slug-based icon + title + lead) → styled prose body → trust CTA band.
 * The lead subtitle comes from the page excerpt; body from the_content().
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

get_header();

$pid  = get_queried_object_id();
$slug = get_post_field( 'post_name', $pid );

// Icon + accent chip by policy type.
if ( false !== strpos( $slug, 'privacy' ) ) {
	$chip = 'Your Data, Protected';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>';
} elseif ( false !== strpos( $slug, 'return' ) || false !== strpos( $slug, 'refund' ) ) {
	$chip = 'No Hassle, No Stress';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.286z"/>';
} elseif ( false !== strpos( $slug, 'term' ) ) {
	$chip = 'Fair & Transparent';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>';
} elseif ( false !== strpos( $slug, 'company' ) ) {
	$chip = 'Who We Are';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>';
} elseif ( false !== strpos( $slug, 'stor' ) ) {
	$chip = 'The WayMore Journey';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>';
} elseif ( false !== strpos( $slug, 'career' ) ) {
	$chip = 'Grow With Us';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 5.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 13.5c-1.573 0-3.16-.163-4.744-.487a3.06 3.06 0 01-.673-.38m4.5-6.13V4.5A2.25 2.25 0 0010.5 2.25h-3A2.25 2.25 0 005.25 4.5v2.006c-1.147.109-2.286.234-3.413.387m7.5 0V6.375c0-.621-.504-1.125-1.125-1.125h-2.25c-.621 0-1.125.504-1.125 1.125v1.5m4.5 0h-4.5"/>';
} else {
	$chip = 'WayMoreBD';
	$icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>';
}

$lead = has_excerpt( $pid ) ? get_the_excerpt( $pid ) : '';
?>

<main id="site-main" class="bg-brand-bg text-brand-body">

	<!-- Breadcrumb strip (bg = site background) -->
	<section class="border-b border-black/5 bg-brand-bg">
		<div class="mx-auto max-w-7xl px-4 py-2.5 sm:px-6 lg:px-8">
			<nav class="text-[13px] text-slate-500" aria-label="<?php esc_attr_e( 'Breadcrumb', 'isdb-custom' ); ?>">
				<div class="flex flex-wrap items-center">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hover:text-brand-primary"><?php esc_html_e( 'Home', 'isdb-custom' ); ?></a>
					<span class="mx-2 text-slate-300">/</span>
					<span class="text-brand-title"><?php the_title(); ?></span>
				</div>
			</nav>
		</div>
	</section>

	<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">

		<?php while ( have_posts() ) : the_post(); ?>

			<!-- Hero -->
			<header class="text-center">
				<span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-primary/10 text-brand-primary">
					<svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg>
				</span>
				<span class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-brand-primary/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-brand-primary">
					<span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span> <?php echo esc_html( $chip ); ?>
				</span>
				<h1 class="mt-4 text-3xl font-extrabold tracking-tight text-brand-title sm:text-4xl"><?php the_title(); ?></h1>
				<?php if ( $lead ) : ?>
					<p class="mx-auto mt-3 max-w-xl text-lg text-slate-500"><?php echo esc_html( $lead ); ?></p>
				<?php endif; ?>
				<p class="mt-4 text-xs text-slate-400"><?php printf( esc_html__( 'Last updated %s', 'isdb-custom' ), esc_html( get_the_modified_date( 'F j, Y' ) ) ); ?></p>
			</header>

			<!-- Body -->
			<article class="prose prose-slate mt-10 max-w-none
				prose-headings:font-bold prose-headings:tracking-tight prose-headings:text-brand-title
				prose-h2:mt-10 prose-h2:mb-3 prose-h2:text-xl prose-h2:flex prose-h2:items-center prose-h2:gap-2
				prose-h2:before:h-5 prose-h2:before:w-1 prose-h2:before:rounded-full prose-h2:before:bg-brand-primary prose-h2:before:content-['']
				prose-p:leading-relaxed prose-p:text-brand-body
				prose-a:font-medium prose-a:text-brand-primary prose-a:no-underline hover:prose-a:underline
				prose-strong:text-brand-title
				prose-li:text-brand-body prose-li:marker:text-brand-primary
				prose-blockquote:border-l-4 prose-blockquote:border-brand-primary prose-blockquote:bg-white
				prose-blockquote:not-italic prose-blockquote:font-medium prose-blockquote:text-brand-title
				prose-blockquote:py-3 prose-blockquote:px-5 prose-blockquote:rounded-r-xl prose-blockquote:shadow-sm">
				<?php the_content(); ?>
			</article>

		<?php endwhile; ?>

		<!-- Trust CTA band -->
		<div class="mt-12 flex flex-col items-center gap-5 rounded-2xl bg-brand-dark p-8 text-center text-white sm:flex-row sm:text-left">
			<svg class="h-10 w-10 flex-none text-brand-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V5l7-3z"/></svg>
			<div class="flex-1">
				<p class="font-bold"><?php esc_html_e( 'Still have questions? We\'re here to help.', 'isdb-custom' ); ?></p>
				<p class="mt-1 text-sm text-white/70"><?php esc_html_e( 'Our friendly support team replies fast — reach out any time and we\'ll take care of you.', 'isdb-custom' ); ?></p>
			</div>
			<a href="<?php echo esc_url( isdb_phone_tel() ); ?>" class="flex-none rounded-xl bg-brand-primary px-6 py-3 text-sm font-bold text-white transition hover:bg-brand-hover"><?php esc_html_e( 'Call Support', 'isdb-custom' ); ?></a>
		</div>

	</div>
</main>

<?php
get_footer();
