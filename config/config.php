<?php
/**
 * Configs used throughout the plugin.
 *
 * @since 1.0
 *
 * @package WPGeeks\Plugin\ForceAddToCart
 */

namespace WPGeeks\Plugin\ForceAddToCart;

define( 'FORCE_ADD_TO_CART_VERSION', '1.0' );
define( 'FORCE_ADD_TO_CART_DIR', trailingslashit( dirname( __DIR__ ) ) );
define( 'FORCE_ADD_TO_CART_URL', trailingslashit( plugins_url( '', FORCE_ADD_TO_CART_DIR . 'force-add-to-cart.php' ) ) );
define( 'FORCE_ADD_TO_CART_BASENAME', plugin_basename( FORCE_ADD_TO_CART_DIR . 'force-add-to-cart.php' ) );
