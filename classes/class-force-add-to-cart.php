<?php
/**
 * Adds force add to cart functionality.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\ForceAddToCart
 */

namespace WPGeeks\Plugin\ForceAddToCart;

/**
 * Class Force_Add_To_Cart
 *
 * Sets up the UI and basic functionality.
 *
 * @since 1.0
 */
class Force_Add_To_Cart {

	/**
	 * Option name.
	 *
	 * @since 0.1
	 */
	const PRODUCTS_OPTION_NAME = 'wpgeeks_force_add_to_cart_products';

	/**
	 * Hooks.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_settings' ) );
		add_action( 'woocommerce_product_options_related', array( $this, 'render_settings' ) );
	}

	/**
	 * Save our settings to the product meta.
	 *
	 * @since 1.0
	 *
	 * @param int $product_id The ID of the product being saved.
	 *
	 * @return void
	 */
	public function save_settings( $product_id ) {
		if ( ! empty( $_POST['force_add_to_cart_id'] ) ) { // @phpcs:ignore
			$force_add_to_cart_id = (int) wp_unslash( $_POST['force_add_to_cart_id'] );
			$is_product_removable = true;

			if ( empty( $_POST['force_add_to_cart_removable'] ) ) { // @phpcs:ignore
				$is_product_removable = false;
			}

			update_post_meta( $product_id, self::PRODUCTS_OPTION_NAME, array( array( $force_add_to_cart_id, $is_product_removable ) )); // @phpcs:ignore
		}
	}

	/**
	 * Render settings in the product options meta box.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function render_settings() {
		global $post;
		$values = get_post_meta( $post->ID, self::PRODUCTS_OPTION_NAME, true );

		$force_add_to_cart_id = '';
		$is_product_removable = 1;

		if ( ! empty( $values[0] ) ) {
			$force_add_to_cart_id = $values[0][0];
			$is_product_removable = $values[0][1];
		}
		?>
		<div class="options_group">
			<p class="form-field">
				<label for="force_add_to_cart_id"><?php esc_html_e( 'Force Add To Cart', 'force-add-to-cart' ); ?></label>
				<select class="wc-product-search" style="width: 50%;" id="force_add_to_cart_id" name="force_add_to_cart_id" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'force-add-to-cart' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">
					<?php
					if ( ! empty( $force_add_to_cart_id ) ) {
						$product = wc_get_product( $force_add_to_cart_id );
						if ( is_object( $product ) ) {
							echo '<option value="' . esc_attr( $force_add_to_cart_id ) . '"' . selected( true, true, false ) . '>' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
						}
					}
					?>
				</select>
				<span class="button clear-force-add-to-cart-id">Clear</span>
				<?php echo wc_help_tip( __( 'Additional products which will get added to the cart when a user adds this product to their cart.', 'force-add-to-cart' ) ); // WPCS: XSS ok. ?>
			</p>

			<?php
			woocommerce_wp_checkbox(
				array(
					'id'            => 'force_add_to_cart_removable',
					'name'          => 'force_add_to_cart_removable',
					'value'         => $is_product_removable ? 'yes' : false,
					'wrapper_class' => '',
					'class'         => '',
					'label'         => __( 'Removeable', 'hide-prices' ),
					'desc_tip'      => false,
					'description'   => __( 'Allow the added product to be removed from the cart', 'hide-prices' ),
				)
			);
			?>
		</div>
		<?
	}
}
