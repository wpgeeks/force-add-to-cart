<?php
/**
 * Force Add To Cart for WooCommerce
 *
 * @package   WPGeeks\Plugin\ForceAddtoCart
 * @author    WP Geeks <support@wpgeeks.com>
 * @copyright 2024 WP Geeks
 * @license   GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @wordpress-plugin
 * Plugin Name: Force Add To Cart for WooCommerce
 * Plugin URI:  https://wpgeeks.com/force-add-to-cart/
 * Description: Force a product to be added to the shopping cart in WooCommerce.
 * Version:     1.0
 * Author:      WP Geeks
 * Author URI:  https://wpgeeks.com
 * Text Domain: force-add-to-cart
 * Domain Path: /languages
 *
 * Requires Plugins: woocommerce
 *
 * WC requires at least: 5.7.0
 * WC tested up to: 9.2.3
 *
 * Copyright:   © 2024 WP Geeks
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Fire up the engines! Main plugin file which is simply used for getting
 * things started.
 *
 * @since 1.0
 */

namespace WPGeeks\Plugin\ForceAddToCart;

// Autoload classes.
require_once 'helpers/autoloader.php';

// Load config.
require_once 'config/config.php';

$bootstrap = Bootstrap::get_instance();
$bootstrap->load();
