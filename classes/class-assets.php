<?php
/**
 * Enqueue Assets.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\ForceAddToCart
 */

namespace WPGeeks\Plugin\ForceAddToCart;

/**
 * Class Assets
 *
 * @since 1.0
 */
class Assets {

	/**
	 * Handle for enqueuing JS assets.
	 *
	 * @since 1.0
	 */
	const JS_HANDLE = 'force-add-to-cart';

	/**
	 * Handle for enqueuing CSS assets.
	 *
	 * @since 1.0
	 */
	const CSS_HANDLE = 'force-add-to-cart';

	/**
	 * Hooks.
	 *
	 * @since 1.0
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 9999 );
	}

	/**
	 * Enqueue public scripts.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( is_cart() ) {
			wp_enqueue_script( self::JS_HANDLE, FORCE_ADD_TO_CART_URL . 'src/cart.js', array( 'jquery' ), FORCE_ADD_TO_CART_VERSION, false );
		}
	}

	/**
	 * Enqueue the required JS scripts.
	 *
	 * @since 1.0
	 */
	public function enqueue_admin_scripts() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'product' === $screen->post_type ) {
			wp_enqueue_script( self::JS_HANDLE, FORCE_ADD_TO_CART_URL . 'src/admin.js', array( 'jquery' ), FORCE_ADD_TO_CART_VERSION, false );
		}
	}

	/**
	 * Enqueue the required CSS scripts.
	 *
	 * @since 1.0
	 */
	public function enqueue_admin_styles() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'product' === $screen->post_type ) {
			wp_enqueue_style( self::CSS_HANDLE, FORCE_ADD_TO_CART_URL . 'src/admin.css', array(), FORCE_ADD_TO_CART_VERSION );
		}
	}
}
