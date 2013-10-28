<?php


// Woocommerce functions
// ----------------------------------------------

add_action( 'init', 'nw_woocommerce_init' );
function nw_woocommerce_init() {

	/* remove woocommerce actions */
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
	remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
	// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10, 2);
	remove_action( 'woocommerce_product_tabs', 'woocommerce_product_description_tab', 10 );
	remove_action( 'woocommerce_product_tabs', 'woocommerce_product_attributes_tab', 20 );
	remove_action( 'woocommerce_product_tabs', 'woocommerce_product_reviews_tab', 30 );
	remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_description_panel', 10 );
	remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_attributes_panel', 20 );
	remove_action( 'woocommerce_product_tab_panels', 'woocommerce_product_reviews_panel', 30 );
	// remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30, 2 );
	// add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_add_to_cart', 30, 2 );
}


add_action( 'woocommerce_single_product_summary', 'nw_product_description' );
function nw_product_description() {
	the_content();
}


function nw_get_attribute( $post_id, $att ) {

	// meta attribute
	$attributes = (array) maybe_unserialize(get_post_meta( $post_id, 'product_attributes', true ));
	$atts = $attributes[$att]['value'];

	if ( empty( $atts ) ) {
		// taxonomy attribute
		$atts = wp_get_object_terms( $post_id, 'pa_'.$att );
		return $atts[0]->name;
	}
	return $atts;
}


// add div around cart actions
/*function woocommerce_template_single_add_to_cart( $post, $_product ) { ?>
	<div class="add-to-cart <?= $_product->product_type ?>">
	<?php
	do_action( 'woocommerce_' . $_product->product_type . '_add_to_cart' ); ?>
	</div>
	<?php
}*/

// remove image link
function woocommerce_show_product_images() {
	global $_product, $post, $woocommerce;

	echo '<div class="images">';

	$thumb_id = 0;
	if (has_post_thumbnail()) :
		$thumb_id = get_post_thumbnail_id();
		$thumbnail_size = apply_filters('single_product_large_thumbnail_size', 'shop_single');
		// echo '<a href="'.wp_get_attachment_url($thumb_id).'" class="zoom" rel="thumbnails" title="'.get_the_title().'">';
		the_post_thumbnail($thumbnail_size); 
		// echo '</a>';
	else : 
		// echo '<img src="'.$woocommerce->plugin_url().'/assets/images/placeholder.png" alt="Placeholder" />'; 
	endif;

	do_action('woocommerce_product_thumbnails');

	echo '</div>';

}

// edit inputs
/*
function woocommerce_grouped_add_to_cart() {
	global $_product;

	?>
	<form action="<?php echo esc_url( $_product->add_to_cart_url() ); ?>" class="cart" method="post">
		<table cellspacing="0" class="group_table">
			<tbody>
				<?php foreach ($_product->get_children() as $child) : $child_product = &new woocommerce_product( $child->ID ); $cavailability = $child_product->get_availability();
				// echo '<!-- ';
				$catno = nw_get_attribute( $child->ID, 'catalog_no' );
				// echo ' -->';
				 ?>
					<tr>
						<td><input name="quantity[<?php echo $child->ID; ?>]" id="product-<?php echo $child_product->id; ?>-qty" size="4" title="Qty" class="input-text qty text" maxlength="12"<?php 
							if ( $child_product->virtual ) : 
							?> value="1" type="checkbox"<?php
							else : ?> value="1" type="text"<?php
							endif; ?> /></td>
						<td><label for="product-<?php echo $child_product->id; ?>-qty"><?php 
							if ($child_product->is_visible()) echo '<a href="'.get_permalink($child->ID).'">';
							echo $child_product->get_title();
							if ($child_product->is_visible()) echo '</a>';
						?></label></td>
						<td class="price"><?php echo $child_product->get_price_html(); ?><small class="stock <?php echo $cavailability['class'] ?>"><?php echo $cavailability['availability']; ?></small></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<button type="submit" class="button alt"><?php _e('Add to cart', 'woothemes'); ?></button>
		<?php do_action('woocommerce_add_to_cart_form'); ?>
	</form>
	<?php
}
*/

