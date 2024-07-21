<?php
/**
 * Adds force add to cart functionality.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\ForceAddToCart
 */

namespace WPGeeks\Plugin\ForceAddToCart;

use Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema;

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
		add_action( 'woocommerce_add_to_cart', array( $this, 'force_add_to_cart' ), 10, 6 );
		add_action( 'woocommerce_blocks_loaded', array( $this, 'extend_cart_endpoint' ) );
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'update_remove_link' ), 10, 2 );
		add_action( 'woocommerce_cart_item_removed', array( $this, 'prevent_remove_item' ), 5, 2 );
		add_filter( 'woocommerce_get_item_data', array( $this, 'add_cart_labels' ), 10, 2 );
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
			$force_add_to_cart_id = (int) wp_unslash( $_POST['force_add_to_cart_id'] ); // @phpcs:ignore
			$disable_remove       = false;

			if ( ! empty( $_POST['force_add_to_cart_disable_remove'] ) ) { // @phpcs:ignore
				$disable_remove = true;
			}

			update_post_meta(
				$product_id,
				self::PRODUCTS_OPTION_NAME,
				array(
					array(
						'product_id'     => $force_add_to_cart_id,
						'disable_remove' => $disable_remove,
					),
				)
			);
		} else {
			delete_post_meta( $product_id, self::PRODUCTS_OPTION_NAME );
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
		$disable_remove       = false;

		if ( ! empty( $values[0] ) ) {
			$force_add_to_cart_id = $values[0]['product_id'];
			$disable_remove       = $values[0]['disable_remove'];
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
				<?php echo wp_kses_post( wc_help_tip( __( 'Additional products which will get added to the cart when a user adds this product to their cart.', 'force-add-to-cart' ) ) ); ?>
			</p>

			<?php
			woocommerce_wp_checkbox(
				array(
					'id'            => 'force_add_to_cart_disable_remove',
					'name'          => 'force_add_to_cart_disable_remove',
					'value'         => $disable_remove ? 'yes' : false,
					'wrapper_class' => '',
					'class'         => '',
					'label'         => __( 'Disable Removing', 'force-add-to-cart' ),
					'desc_tip'      => false,
					'description'   => __( 'Don\'t allow the added product to be removed from the cart', 'force-add-to-cart' ),
				)
			);
			?>
		</div>
		<?php
	}

	/**
	 * Force a product in to the cart.
	 *
	 * @since 1.0
	 *
	 * @param string  $cart_id          ID of the item in the cart.
	 * @param integer $product_id       ID of the product added to the cart.
	 * @param integer $quantity Quantity of the item added to the cart.
	 * @param integer $variation_id     Variation ID of the product added to the cart.
	 * @param array   $variation        Array of variation data.
	 * @param array   $cart_item_data   Array of other cart item data.
	 *
	 * @return void
	 */
	public function force_add_to_cart( $cart_id, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		// To avoid a redirect loop, check if we're adding this item to the cart or if the user is adding it.
		if ( ! empty( $cart_item_data['force_add_to_cart']['enabled'] ) ) {
			return;
		}

		$product = wc_get_product( $product_id );

		// Check if this is a variable product. Use parent product if it is.
		if ( ! empty( $variation_id ) ) {
			$product = wc_get_product( $product->get_parent_id() );
		}

		$values = get_post_meta( $product->get_id(), self::PRODUCTS_OPTION_NAME, true );

		if ( ! empty( $values[0] ) ) {
			$product_id_to_add      = $values[0]['product_id'];
			$product_disable_remove = $values[0]['disable_remove'];

			$cart_items = WC()->cart->get_cart();

			// Check if the item is already in the cart.
			foreach ( $cart_items as $cart_item ) {
				if ( $product_id_to_add === $cart_item['product_id'] ) {
					return;
				}
			}

			// Add product to cart.
			WC()->cart->add_to_cart(
				$product_id_to_add,
				1,
				0,
				array(),
				array(
					'force_add_to_cart' => array(
						'enabled'           => true,
						'disable_remove'    => $product_disable_remove,
						'linked_product_id' => $product->get_id(),
					),
				)
			);
		}
	}

	/**
	 * Extend the cart API endpoint and add products that are not removable to the
	 * extension data so that we can remove them with JS in the new cart.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function extend_cart_endpoint() {
		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => CartSchema::IDENTIFIER,
				'namespace'       => 'wpgeeks_force_add_to_cart',
				'data_callback'   => array( $this, 'extend_data_callback' ),
				'schema_callback' => array( $this, 'extend_schema_callback' ),
				'schema_type'     => ARRAY_A,
			)
		);
	}

	/**
	 * Extension data callback.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function extend_data_callback() {
		$not_removable = array();

		// Get the items in the cart.
		$cart_items = WC()->cart->get_cart();

		// Check if the item is already in the cart.
		foreach ( $cart_items as $cart_item ) {
			if ( ! empty( $cart_item['force_add_to_cart']['disable_remove'] ) ) {
				$not_removable[] = array(
					'key'               => $cart_item['key'],
					'linked_product_id' => $cart_item['force_add_to_cart']['linked_product_id'],
				);
			}
		}

		return array( 'is_not_removable' => $not_removable );
	}

	/**
	 * Extend data schema callback.
	 *
	 * @since 1.0
	 *
	 * @return array
	 */
	public function extend_schema_callback() {
		return array(
			'is_not_removable' => array(
				'description' => __( 'Items that are not removable from the cart', 'force-add-to-cart' ),
				'type'        => 'array',
				'readonly'    => true,
			),
		);
	}

	/**
	 * Legacy cart update the remove link.
	 *
	 * @since 1.0
	 *
	 * @param string $link The current link text.
	 * @param int    $cart_item_id The cart item id.
	 *
	 * @return string
	 */
	public function update_remove_link( $link, $cart_item_id ) {
		if ( WC()->cart->find_product_in_cart( $cart_item_id ) ) {
			$cart_item = WC()->cart->cart_contents[ $cart_item_id ];

			if ( ! empty( $cart_item['force_add_to_cart']['enabled'] ) && ! empty( $cart_item['force_add_to_cart']['disable_remove'] ) ) {
				// Check if linked product is in the cart, if it's not, allow the user to remove the product.
				foreach ( WC()->cart->cart_contents as $cart_contents_item ) {
					if ( $cart_contents_item['product_id'] === $cart_item['force_add_to_cart']['linked_product_id'] ) {
						return '';
					}
				}
			}
		}

		return $link;
	}

	/**
	 * Prevent an item from being removed from the cart.
	 *
	 * @since 1.0
	 *
	 * @param string $key The key of the product being removed.
	 * @param object $cart The cart object.
	 *
	 * @return void
	 */
	public function prevent_remove_item( $key, $cart ) {
		foreach ( $cart->removed_cart_contents as $cart_key => $item ) {
			if ( $key === $cart_key && ! empty( $item['force_add_to_cart']['disable_remove'] ) ) {
				// Check if linked item is still in cart.
				foreach ( $cart->cart_contents as $cart_contents_item ) {
					// Don't allow the removal if the linked item is still in the cart.
					if ( $cart_contents_item['product_id'] === $item['force_add_to_cart']['linked_product_id'] ) {
						$cart->restore_cart_item( $key );

						$product = wc_get_product( $item['force_add_to_cart']['linked_product_id'] );

						wc_add_notice(
							sprintf(
								'%1$s %2$s.',
								__( 'Unable to remove this item from your cart. Required by', 'force-add-to-cart' ),
								$product->get_name()
							),
							'error'
						);

						add_filter( 'woocommerce_add_success', '__return_false' );

						return;
					}
				}
			}
		}
	}

	/**
	 * Add label to cart item to show which product added it to the cart.
	 *
	 * @since 1.0
	 *
	 * @param array $item_data The item data.
	 * @param array $cart_item_data The cart item meta data.
	 *
	 * @return array
	 */
	public function add_cart_labels( $item_data, $cart_item_data ) {
		if ( ! empty( $cart_item_data['force_add_to_cart']['linked_product_id'] ) ) {
			$product     = wc_get_product( $cart_item_data['force_add_to_cart']['linked_product_id'] );
			$item_data[] = array(
				'key'   => __( 'Added By', 'force-add-to-cart' ),
				'value' => $product->get_name(),
			);
		}
		return $item_data;
	}
}
