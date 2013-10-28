<?php
/**
 * Variable Product Add to Cart
 */
 
global $woocommerce, $product, $post;
?>
<script type="text/javascript">
    var product_variations = <?php echo json_encode($available_variations) ?>;
</script>

<!-- <?php var_dump($available_variations); ?> -->

<?php do_action('woocommerce_before_add_to_cart_form'); ?>

	<?php $loop = 0; foreach ($attributes as $name => $options) : $loop++; ?>

		<h3><?php echo $woocommerce->attribute_label($name) ?></h3>  <!-- Format title -->

		<table cellspacing="0" class="group_table">
			<tbody>

			<?php if(is_array($options)) :
				$selected_value = (isset($selected_attributes[sanitize_title($name)])) ? $selected_attributes[sanitize_title($name)] : '';
				// Get terms if this is a taxonomy - ordered
				if (taxonomy_exists(sanitize_title($name))) :
					$args = array('menu_order' => 'ASC');
					$terms = get_terms( sanitize_title($name), $args );

					foreach ($terms as $term) :
						if (!in_array($term->slug, $options)) continue;

/*?>
						<tr>
							<form action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="variations_form cart" method="post" enctype='multipart/form-data'>
								<input type="hidden" name="variation_id" value="<?php echo $" />

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
<?php
*/
						// echo '<option value="'.$term->slug.'" '.selected($selected_value, $term->slug).'>'.$term->name.'</option>';
					endforeach;
				else :
					foreach ($options as $option) :
						// echo '<option value="'.$option.'" '.selected($selected_value, $option).'>'.$option.'</option>';
					endforeach;
				endif; // taxonomy_exists()

			endif; // is_array($options)
			?>




				<tr>
					<td><label for="<?php echo sanitize_title($name); ?>"><?php echo $woocommerce->attribute_label($name); ?></label></td>
					<td><select id="<?php echo esc_attr( sanitize_title($name) ); ?>" name="attribute_<?php echo sanitize_title($name); ?>">
						<option value=""><?php echo __('Choose an option', 'woocommerce') ?>&hellip;</option>
						<?php if(is_array($options)) : ?>
							<?php
								$selected_value = (isset($selected_attributes[sanitize_title($name)])) ? $selected_attributes[sanitize_title($name)] : '';
								// Get terms if this is a taxonomy - ordered
								if (taxonomy_exists(sanitize_title($name))) :
									$args = array('menu_order' => 'ASC');
									$terms = get_terms( sanitize_title($name), $args );
	
									foreach ($terms as $term) :
										if (!in_array($term->slug, $options)) continue;
										echo '<option value="'.$term->slug.'" '.selected($selected_value, $term->slug).'>'.$term->name.'</option>';
									endforeach;
								else :
									foreach ($options as $option) :
										echo '<option value="'.$option.'" '.selected($selected_value, $option).'>'.$option.'</option>';
									endforeach;
								endif;
							?>
						<?php endif;?>
					</select> <?php
						if ( sizeof($attributes) == $loop ) {
							echo '<a class="reset_variations" href="#reset">'.__('Reset selection', 'woocommerce').'</a>';
						}
					?></td>
				</tr>
	        <?php endforeach;?>
		</tbody>
	</table>

	<?php do_action('woocommerce_before_add_to_cart_button'); ?>

	<div class="single_variation_wrap" style="display:none;">
		<div class="single_variation"></div>
		<div class="variations_button">
			<input type="hidden" name="variation_id" value="" />
			<?php woocommerce_quantity_input(); ?>
			<button type="submit" class="button alt"><?php echo apply_filters('single_add_to_cart_text', __('Add to cart', 'woocommerce'), $product->product_type); ?></button>
		</div>
	</div>
	<div><input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" /></div>

	<?php do_action('woocommerce_after_add_to_cart_button'); ?>

</form>

<?php do_action('woocommerce_after_add_to_cart_form'); ?>
