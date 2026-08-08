<?php
/**
 * Isolated checkout footer — Way More BD.
 * Minimal: copyright + security/payment signals only. No link columns.
 *
 * @package isdb-custom
 */

defined( 'ABSPATH' ) || exit;
?>

<footer class="mt-16 border-t border-slate-100 bg-white">
	<div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-6">
		<div class="flex flex-col items-center justify-between gap-3 sm:flex-row">
			<p class="text-xs text-slate-400">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.</p>
			<div class="flex items-center gap-3 text-slate-400">
				<span class="inline-flex items-center gap-1.5 text-xs font-medium">
					<svg class="h-4 w-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1l7 3v5c0 4-3 7.5-7 9-4-1.5-7-5-7-9V4l7-3z" clip-rule="evenodd"/></svg>
					256-bit SSL secured
				</span>
				<span class="rounded bg-stone-100 px-2 py-1 text-[10px] font-bold tracking-wide text-slate-500">VISA</span>
				<span class="rounded bg-stone-100 px-2 py-1 text-[10px] font-bold tracking-wide text-slate-500">MASTERCARD</span>
				<span class="rounded bg-stone-100 px-2 py-1 text-[10px] font-bold tracking-wide text-slate-500">bKash</span>
				<span class="rounded bg-stone-100 px-2 py-1 text-[10px] font-bold tracking-wide text-slate-500">COD</span>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
