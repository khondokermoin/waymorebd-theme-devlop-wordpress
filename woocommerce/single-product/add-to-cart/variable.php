<?php
/**
 * Variable product add-to-cart — Way More BD (button / pill swatches)
 *
 * Overrides: woocommerce/templates/single-product/add-to-cart/variable.php
 *
 * Keeps the FULL WooCommerce contract so price/stock/add-to-cart stay 100%
 * functional — the native <select> per attribute is preserved (the variation JS
 * reads/writes it); we just render clickable swatch buttons that drive it and
 * hide the select via CSS.
 *
 * Load-bearing (do not remove):
 *   form.variations_form + data-product_variations   (wc-add-to-cart-variation JS)
 *   .wmb-var-control select[name="attribute_*"]       (one per attribute)
 *   .single_variation_wrap + do_action hooks          (price + add-to-cart button)
 *
 * @package isdb-custom
 * @var array      $available_variations
 * @var array      $attributes
 * @var WC_Product $product
 */

defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="variations_form cart wmb-variations-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
	<?php else : ?>
		<div class="wmb-variations">
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
				<div class="wmb-var-row" data-attribute="attribute_<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
					<span class="wmb-var-label"><?php echo esc_html( wc_attribute_label( $attribute_name ) ); ?></span>
					<div class="wmb-var-control">
						<?php
						wc_dropdown_variation_attribute_options( array(
							'options'   => $options,
							'attribute' => $attribute_name,
							'product'   => $product,
						) );
						?>
					</div>
				</div>
			<?php endforeach; ?>

			<?php if ( count( $attribute_keys ) > 0 ) : ?>
				<a class="reset_variations wmb-reset" href="#"><?php esc_html_e( 'Clear selection', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>

		<?php do_action( 'woocommerce_after_variations_table' ); ?>

		<div class="single_variation_wrap">
			<?php
			/**
			 * woocommerce_before_single_variation hook.
			 * woocommerce_single_variation  — prints price + availability container.
			 * woocommerce_single_variation_add_to_cart_button — qty + add-to-cart.
			 */
			do_action( 'woocommerce_before_single_variation' );
			do_action( 'woocommerce_single_variation' );
			do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<script>
(function () {
	if (typeof jQuery === 'undefined') { return; }
	jQuery(function ($) {
		$('.wmb-variations-form').each(function () {
			var $form = $(this);

			// Build a swatch button group in front of each native <select>.
			$form.find('.wmb-var-control select').each(function () {
				var $sel = $(this);
				if ($sel.data('wmbSwatched')) { return; }
				$sel.data('wmbSwatched', true);

				var $swatches = $('<div class="wmb-swatches"></div>');
				$sel.find('option').each(function () {
					var val = $(this).val();
					if (!val) { return; }
					$('<button type="button" class="wmb-swatch"></button>')
						.attr('data-value', val)
						.text($(this).text())
						.appendTo($swatches);
				});
				$sel.before($swatches);

				$swatches.on('click', '.wmb-swatch', function () {
					var $b = $(this);
					if ($b.prop('disabled')) { return; }
					$sel.val($b.attr('data-value')).trigger('change');
				});
			});

			// Reflect the current value + WC's enabled/disabled options onto buttons.
			function sync() {
				$form.find('.wmb-swatches').each(function () {
					var $sw  = $(this);
					var $sel = $sw.siblings('select');
					var cur  = $sel.val();
					$sw.find('.wmb-swatch').each(function () {
						var $b = $(this), v = $b.attr('data-value');
						var $opt = $sel.find('option').filter(function () { return $(this).val() === v; });
						$b.toggleClass('is-active', cur === v);
						$b.prop('disabled', $opt.length ? $opt.is(':disabled') : false);
					});
				});
			}

			$form.on('change', '.wmb-var-control select', sync);
			$form.on('woocommerce_update_variation_values reset_data check_variations woocommerce_variation_has_changed', sync);
			// WC updates option availability slightly after these events fire.
			$form.on('woocommerce_update_variation_values', function () { setTimeout(sync, 0); });
			sync();
		});
	});
})();
</script>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
