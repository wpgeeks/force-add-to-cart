=== Force Add To Cart for WooCommerce ===
Contributors: wearewpgeeks, MattGeri
Tags: woocommerce, add to cart, force add to cart, linked products
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WooCommerce plugin that allows you to force add a product to the shopping cart.

== Description ==

Force Add To Cart for WooCommerce allows you to select a product which will automatically be added to the shopping cart when another product is added to the cart. Simply visit the "Linked Products" section on the edit product page and choose the product to be force added to the cart when that product is added.

Additionally, you have the ability to select whether a visitor is able to remove the product that was forcefully added or to restrict removing the product from the cart.

The plugin works with both the classic cart experience and the new React based cart block.

Built by [WP Geeks](https://wpgeeks.com).

== Installation ==

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'Force Add To Cart'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

== Frequently Asked Questions ==

= If a user removes the parent product from their cart, will the force added product get also removed? =

No, it will remain in the cart and the user will have to remove it. If you selected the option to restrict removing of the force added product from the cart, once the parent product is removed, the force added product will become removable.

= Does the plugin work with variable products? =

You can add a forced product to a parent variable product, but not to individial variation of that product.

== Changelog ==
= 1.0 =
* Add - Select a linked product to force add to the cart when the parent product is added to the cart
* Add - Enable or disable the ability to remove the force added product from the cart
