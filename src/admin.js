(function($) {
	$(document).ready(function() {
		$('.clear-force-add-to-cart-id').on('click', function(e){
			e.preventDefault();
			$('#force_add_to_cart_id').empty().trigger('change');
		});
	});
})(jQuery);
