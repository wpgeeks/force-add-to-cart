(function($) {
	$(document).ready(function() {
		if (undefined === window.wc.blocksCheckout) {
			return;
		}

		const { registerCheckoutFilters } = window.wc.blocksCheckout;

		const updateRemoveLink = ( value, extensions, args ) => {
			if (undefined !== args.cart.extensions.force_add_to_cart.is_not_removable) {
				const cartProducts = args.cart.cartItems.map((item) => {
					return item.id;
				});

				const is_not_removable = args.cart.extensions.force_add_to_cart.is_not_removable.map((item) => {
					// Check if the linked product is in the cart first.
					if (cartProducts.includes(item.linked_product_id)) {
						// Linked product is in the cart, hide the remove link.
						return item.key;
					}
				});
				
				if (is_not_removable.includes(args.cartItem.key)) {
					return false;
				}
			}
			return value;
		};

		registerCheckoutFilters( 'force-add-to-cart-remove-link', {
			showRemoveItemLink: updateRemoveLink,
		} );
	});
})(jQuery);
