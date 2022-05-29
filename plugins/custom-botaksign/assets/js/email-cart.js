jQuery( function($){

	$( document ).ready( function() {

		/**
		 * From Cart Reports
		 */
		var expiration_row = jQuery('#cxecrt_cart_expiration_time').closest('tr');
		var expiration_checked = jQuery("#cxecrt_cart_expiration_active:checked").length;

		var expiration_opt_in = jQuery("#cxecrt_cart_expiration_active");

		if (expiration_checked == 0) {
			expiration_row.hide();
		}

		jQuery('#cxecrt_cart_expiration_active').click(function() {
			expiration_row.toggle();
		});
		
		/**
		 * Notification on attempted 'trash' of Cart.
		 */
		
		// On admin cart list.
		$( document ).on( 'click', '.post-type-stored-carts .bulkactions #doaction, .post-type-stored-carts .bulkactions #doaction2', function() {
			if ( 'trash' == $( this ).parent().children('[name="action2"]').val() || 'trash' == $( this ).parent().children('[name="action"]').val() ) {
				if ( ! confirm( cxecrt_params.i18n_delete_cart_confirm ) ) {
					return false;
				}
			}
		} );
		
		// On admin cart edit.
		$( '.submitdelete.deletion' ).cxecrtTipTip({
			// 'attribute': 'data-tip',
			'content': cxecrt_params.i18n_delete_cart_confirm,
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		});
		
		/**
		 * Overwrite Cart Panel
		 */
		
		// Init the panel
		$('.cxecrt-overwrite-cart-holder')
			.slideUp(0)
			.css({ position: 'relative', visibility: 'visible' });
		
		// Toggle slide to open panel.
		$( document ).on( 'click', '.cart-edit-button, .cancel-button', function(){
			$('.cxecrt-overwrite-cart-holder').slideToggle(300);
			return false;
		});

		// Add confirm dialog to the overwrite button.
		$( document ).on( 'click', '.overwrite-button', function(){
			
			if ( ! confirm( cxecrt_params.i18n_overwrite_cart_confirm ) ) {
				return false;
			}
		});

		/**
		 * Toggle Redirect to Cart/Checkout
		 */
		 $( document ).on( 'change', '[name="cxecrt-landing-page"]', function(){
			
			// Get elements.
			$radio_input = $(this);
			$related_text_input = $('#cxecrt-landing-page-display');
			
			// Update value.
			$related_text_input.val( $radio_input.data('url') );
		});
		
		
		/**
		 * Get the mini cart's HTML to display them on the Saved Cart Edit page.
		 *
		 * Have to get these carts by Ajax because WC (and plugins like Dynamic Pricing)
		 * don't load resources required to display the cart in wp-admin, as carts are
		 * usually only displayed on the front end. The resources are loaded on the
		 * front-end and during Ajax.
		 */
		if (
				// Check we're on the Save Cart single.
				$( '.post-type-stored-carts .cxecrt-mini-saved-cart').length &&
				$( '.post-type-stored-carts .cxecrt-mini-current-cart').length
			) {
			
			// Block both the mini cart elements while we load them via Ajax.
			$('.cxecrt-mini-saved-cart').block({
				message: '',
				overlayCSS: { backgroundColor: 'transparent' }
			});
			$('.cxecrt-mini-current-cart').block({
				message: '',
				overlayCSS: { backgroundColor: 'transparent' }
			});
			
			// Get the mini carts HTML via Ajax, and inser them into the layout.
			$.ajax({
				type     : 'post',
				dataType : 'html',
				url      : cxecrt_params.ajax_url,
				data     : {
					action  : 'get_cart_html_ajax',
					cart_id : $('#post_ID').val(),
				},
				success: function( response ) {

					$('.cxecrt-mini-saved-cart').append( $( response ) );
					$('.cxecrt-mini-saved-cart').unblock();
				},
				error: function(xhr, status, error) {

					console.log( 'ERROR!' );
				}
			});
			$.ajax({
				type     : 'post',
				dataType : 'html',
				url      : cxecrt_params.ajax_url,
				data     : {
					action: 'get_cart_html_ajax',
				},
				success: function( response ) {

					$('.cxecrt-mini-current-cart').append( $( response ) );
					$('.cxecrt-mini-current-cart').unblock();
				},
				error: function(xhr, status, error) {

					console.log( 'ERROR!' );
				}
			});
		}

	});

});