<?php
/**
 * Fallback template — the last resort in the WP template hierarchy.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="site-main" class="bg-stone-50">
	<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
		<?php if ( have_posts() ) : ?>

			<?php if ( is_home() && ! is_front_page() ) : ?>
				<h1 class="mb-8 text-3xl font-bold tracking-tight text-slate-900"><?php single_post_title(); ?></h1>
			<?php endif; ?>

			<div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'group rounded-2xl bg-white p-5 ring-1 ring-slate-100 transition hover:shadow-lg' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="block overflow-hidden rounded-xl bg-stone-100">
								<?php the_post_thumbnail( 'large', array( 'class' => 'aspect-video w-full object-cover transition duration-500 group-hover:scale-105' ) ); ?>
							</a>
						<?php endif; ?>
						<h2 class="mt-4 text-lg font-semibold text-slate-900">
							<a href="<?php the_permalink(); ?>" class="transition hover:text-amber-600"><?php the_title(); ?></a>
						</h2>
						<div class="mt-2 text-sm leading-relaxed text-slate-600"><?php the_excerpt(); ?></div>
						<a href="<?php the_permalink(); ?>" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-amber-600">Read more <span aria-hidden="true">→</span></a>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="mt-12"><?php the_posts_pagination( array( 'mid_size' => 1, 'class' => 'flex gap-2' ) ); ?></div>

		<?php else : ?>
			<div class="py-24 text-center">
				<h1 class="text-2xl font-bold text-slate-900">Nothing here yet</h1>
				<p class="mt-2 text-slate-500">Try a search, or head back to the shop.</p>
				<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="mt-6 inline-block rounded-xl bg-amber-500 px-6 py-3 font-bold text-slate-900 transition hover:bg-amber-400">Browse Products</a>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
