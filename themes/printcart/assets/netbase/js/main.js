
/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	var container, button, menu, links, i, len;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Get all the link elements within the menu.
	links    = menu.getElementsByTagName( 'a' );

	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( -1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	/**
	 * Toggles `focus` class to allow submenu access on tablets.
	 */
	( function( container ) {
		var touchStartFn, i,
			parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

		if ( 'ontouchstart' in window ) {
			touchStartFn = function( e ) {
				var menuItem = this.parentNode, i;

				if ( ! menuItem.classList.contains( 'focus' ) ) {
					e.preventDefault();
					for ( i = 0; i < menuItem.parentNode.children.length; ++i ) {
						if ( menuItem === menuItem.parentNode.children[i] ) {
							continue;
						}
						menuItem.parentNode.children[i].classList.remove( 'focus' );
					}
					menuItem.classList.add( 'focus' );
				} else {
					menuItem.classList.remove( 'focus' );
				}
			};

			for ( i = 0; i < parentLink.length; ++i ) {
				parentLink[i].addEventListener( 'touchstart', touchStartFn, false );
			}
		}
	}( container ) );
} )();

/**
 * File skip-link-focus-fix.js.
 *
 * Helps with accessibility for keyboard only users.
 *
 * Learn more: https://git.io/vWdr2
 */
(function() {
	var isIe = /(trident|msie)/i.test( navigator.userAgent );

	if ( isIe && document.getElementById && window.addEventListener ) {
		window.addEventListener( 'hashchange', function() {
			var id = location.hash.substring( 1 ),
				element;

			if ( ! ( /^[A-z0-9_-]+$/.test( id ) ) ) {
				return;
			}

			element = document.getElementById( id );

			if ( element ) {
				if ( ! ( /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) ) {
					element.tabIndex = -1;
				}

				element.focus();
			}
		}, false );
	}
})();

/* global wc_cart_params */
jQuery( function( $ ) {

	// wc_cart_params is required to continue, ensure the object exists
	if ( typeof wc_cart_params === 'undefined' ) {
		return false;
	}

	// Utility functions for the file.

	/**
	 * Gets a url for a given AJAX endpoint.
	 *
	 * @param {String} endpoint The AJAX Endpoint
	 * @return {String} The URL to use for the request
	 */
	var get_url = function( endpoint ) {
		return wc_cart_params.wc_ajax_url.toString().replace(
			'%%endpoint%%',
			endpoint
		);
	};

	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

	/**
	 * Update the .woocommerce div with a string of html.
	 *
	 * @param {String} html_str The HTML string with which to replace the div.
	 * @param {bool} preserve_notices Should notices be kept? False by default.
	 */
	var update_wc_div = function( html_str, preserve_notices ) {
		var $html       = $.parseHTML( html_str );
		var $new_form   = $( '.woocommerce-cart-form', $html );
		var $new_totals = $( '.cart_totals', $html );
		var $notices    = $( '.woocommerce-error, .woocommerce-message, .woocommerce-info', $html );

		// No form, cannot do this.
		if ( $( '.woocommerce-cart-form' ).length === 0 ) {
			window.location.href = window.location.href;
			return;
		}

		// Remove errors
		if ( ! preserve_notices ) {
			$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
		}

		if ( $new_form.length === 0 ) {
			// If the checkout is also displayed on this page, trigger reload instead.
			if ( $( '.woocommerce-checkout' ).length ) {
				window.location.href = window.location.href;
				return;
			}

			// No items to display now! Replace all cart content.
			var $cart_html = $( '.cart-empty', $html ).closest( '.woocommerce' );
			$( '.woocommerce-cart-form__contents' ).closest( '.woocommerce' ).replaceWith( $cart_html );

			// Display errors
			if ( $notices.length > 0 ) {
				show_notice( $notices, $( '.cart-empty' ).closest( '.woocommerce' ) );
			}
		} else {
			// If the checkout is also displayed on this page, trigger update event.
			if ( $( '.woocommerce-checkout' ).length ) {
				$( document.body ).trigger( 'update_checkout' );
			}

			$( '.woocommerce-cart-form' ).replaceWith( $new_form );
			$( '.woocommerce-cart-form' ).find( 'input[name="update_cart"]' ).prop( 'disabled', true );

			if ( $notices.length > 0 ) {
				show_notice( $notices );
			}

			update_cart_totals_div( $new_totals );
		}

		$( document.body ).trigger( 'updated_wc_div' );
	};

	/**
	 * Update the .cart_totals div with a string of html.
	 *
	 * @param {String} html_str The HTML string with which to replace the div.
	 */
	var update_cart_totals_div = function( html_str ) {
		$( '.cart_totals' ).replaceWith( html_str );
		$( document.body ).trigger( 'updated_cart_totals' );
	};

	/**
	 * Clear previous notices and shows new one above form.
	 *
	 * @param {Object} The Notice HTML Element in string or object form.
	 */
	var show_notice = function( html_element, $target ) {
		if ( ! $target ) {
			$target = $( '.woocommerce-cart-form' );
		}
		$target.before( html_element );
	};


	/**
	 * Object to handle AJAX calls for cart shipping changes.
	 */
	var cart_shipping = {

		/**
		 * Initialize event handlers and UI state.
		 */
		init: function( cart ) {
			this.cart                       = cart;
			this.toggle_shipping            = this.toggle_shipping.bind( this );
			this.shipping_method_selected   = this.shipping_method_selected.bind( this );
			this.shipping_calculator_submit = this.shipping_calculator_submit.bind( this );

			// $( document ).on(
			// 	'click',
			// 	'.shipping-calculator-button',
			// 	this.toggle_shipping
			// );
			$( document ).on(
				'change',
				'select.shipping_method, input[name^=shipping_method]',
				this.shipping_method_selected
			);
			$( document ).on(
				'submit',
				'form.woocommerce-shipping-calculator',
				this.shipping_calculator_submit
			);

			$( '.shipping-calculator-form' ).hide();
		},

		/**
		 * Toggle Shipping Calculator panel
		 */
		toggle_shipping: function() {
			$( '.shipping-calculator-form' ).slideToggle( 'slow' );
			return false;
		},

		/**
		 * Handles when a shipping method is selected.
		 *
		 * @param {Object} evt The JQuery event.
		 */
		shipping_method_selected: function( evt ) {
			var target = evt.currentTarget;

			var shipping_methods = {};

			$( 'select.shipping_method, input[name^=shipping_method][type=radio]:checked, input[name^=shipping_method][type=hidden]' ).each( function() {
				shipping_methods[ $( target ).data( 'index' ) ] = $( target ).val();
			} );

			block( $( 'div.cart_totals' ) );

			var data = {
				security: wc_cart_params.update_shipping_method_nonce,
				shipping_method: shipping_methods
			};

			$.ajax( {
				type:     'post',
				url:      get_url( 'update_shipping_method' ),
				data:     data,
				dataType: 'html',
				success:  function( response ) {
					update_cart_totals_div( response );
				},
				complete: function() {
					unblock( $( 'div.cart_totals' ) );
					$( document.body ).trigger( 'updated_shipping_method' );
				}
			} );
		},

		/**
		 * Handles a shipping calculator form submit.
		 *
		 * @param {Object} evt The JQuery event.
		 */
		shipping_calculator_submit: function( evt ) {
			evt.preventDefault();

			var $form = $( evt.currentTarget );

			block( $( 'div.cart_totals' ) );
			block( $form );

			// Provide the submit button value because wc-form-handler expects it.
			$( '<input />' ).attr( 'type', 'hidden' )
							.attr( 'name', 'calc_shipping' )
							.attr( 'value', 'x' )
							.appendTo( $form );

			// Make call to actual form post URL.
			$.ajax( {
				type:     $form.attr( 'method' ),
				url:      $form.attr( 'action' ),
				data:     $form.serialize(),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		}
	};

	/**
	 * Object to handle cart UI.
	 */
	var cart = {
		/**
		 * Initialize cart UI events.
		 */
		init: function() {
			this.update_cart_totals    = this.update_cart_totals.bind( this );
			this.input_keypress        = this.input_keypress.bind( this );
			this.cart_submit           = this.cart_submit.bind( this );
			this.submit_click          = this.submit_click.bind( this );
			this.apply_coupon          = this.apply_coupon.bind( this );
			this.remove_coupon_clicked = this.remove_coupon_clicked.bind( this );
			this.quantity_update       = this.quantity_update.bind( this );
			this.item_remove_clicked   = this.item_remove_clicked.bind( this );
			this.update_cart           = this.update_cart.bind( this );

			$( document ).on(
				'wc_update_cart',
				this.update_cart );
			$( document ).on(
				'click',
				'.woocommerce-cart-form input[type=submit]',
				this.submit_click );
			$( document ).on(
				'keypress',
				'.woocommerce-cart-form input[type=number]',
				this.input_keypress );
			$( document ).on(
				'submit',
				'.woocommerce-cart-form',
				this.cart_submit );
			$( document ).on(
				'click',
				'a.woocommerce-remove-coupon',
				this.remove_coupon_clicked );
			$( document ).on(
				'click',
				'.woocommerce-cart-form .product-remove > a',
				this.item_remove_clicked );
			$( document ).on(
				'change input',
				'.woocommerce-cart-form .cart_item :input',
				this.input_changed );

			$( '.woocommerce-cart-form input[name="update_cart"]' ).prop( 'disabled', true );
		},

		/**
		 * After an input is changed, enable the update cart button.
		 */
		input_changed: function() {
			$( '.woocommerce-cart-form input[name="update_cart"]' ).prop( 'disabled', false );
		},

		/**
		 * Update entire cart via ajax.
		 */
		update_cart: function( preserve_notices ) {
			var $form = $( '.woocommerce-cart-form' );

			block( $form );
			block( $( 'div.cart_totals' ) );

			// Make call to actual form post URL.
			$.ajax( {
				type:     $form.attr( 'method' ),
				url:      $form.attr( 'action' ),
				data:     $form.serialize(),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response, preserve_notices );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Update the cart after something has changed.
		 */
		update_cart_totals: function() {
			block( $( 'div.cart_totals' ) );

			$.ajax( {
				url:      get_url( 'get_cart_totals' ),
				dataType: 'html',
				success:  function( response ) {
					update_cart_totals_div( response );
				},
				complete: function() {
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Handle the <ENTER> key for quantity fields.
		 *
		 * @param {Object} evt The JQuery event
		 *
		 * For IE, if you hit enter on a quantity field, it makes the
		 * document.activeElement the first submit button it finds.
		 * For us, that is the Apply Coupon button. This is required
		 * to catch the event before that happens.
		 */
		input_keypress: function( evt ) {

			// Catch the enter key and don't let it submit the form.
			if ( 13 === evt.keyCode ) {
				evt.preventDefault();
				this.cart_submit( evt );
			}
		},

		/**
		 * Handle cart form submit and route to correct logic.
		 *
		 * @param {Object} evt The JQuery event
		 */
		cart_submit: function( evt ) {
			var $submit = $( document.activeElement );
			var $clicked = $( 'input[type=submit][clicked=true]' );
			var $form = $( evt.currentTarget );

			// For submit events, currentTarget is form.
			// For keypress events, currentTarget is input.
			if ( ! $form.is( 'form' ) ) {
				$form = $( evt.currentTarget ).parents( 'form' );
			}

			if ( 0 === $form.find( '.woocommerce-cart-form__contents' ).length ) {
				return;
			}

			if ( is_blocked( $form ) ) {
				return false;
			}

			if ( $clicked.is( 'input[name="update_cart"]' ) || $submit.is( 'input.qty' ) ) {
				evt.preventDefault();
				this.quantity_update( $form );

			} else if ( $clicked.is( 'input[name="apply_coupon"]' ) || $submit.is( '#coupon_code' ) ) {
				evt.preventDefault();
				this.apply_coupon( $form );
			}
		},

		/**
		 * Special handling to identify which submit button was clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		submit_click: function( evt ) {
			$( 'input[type=submit]', $( evt.target ).parents( 'form' ) ).removeAttr( 'clicked' );
			$( evt.target ).attr( 'clicked', 'true' );
		},

		/**
		 * Apply Coupon code
		 *
		 * @param {JQuery Object} $form The cart form.
		 */
		apply_coupon: function( $form ) {
			block( $form );

			var cart = this;
			var $text_field = $( '#coupon_code' );
			var coupon_code = $text_field.val();

			var data = {
				security: wc_cart_params.apply_coupon_nonce,
				coupon_code: coupon_code
			};

			$.ajax( {
				type:     'POST',
				url:      get_url( 'apply_coupon' ),
				data:     data,
				dataType: 'html',
				success: function( response ) {
					$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
					show_notice( response );
					$( document.body ).trigger( 'applied_coupon', [ coupon_code ] );
				},
				complete: function() {
					unblock( $form );
					$text_field.val( '' );
					cart.update_cart( true );
				}
			} );
		},

		/**
		 * Handle when a remove coupon link is clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		remove_coupon_clicked: function( evt ) {
			evt.preventDefault();

			var cart     = this;
			var $wrapper = $( evt.currentTarget ).closest( '.cart_totals' );
			var coupon   = $( evt.currentTarget ).attr( 'data-coupon' );

			block( $wrapper );

			var data = {
				security: wc_cart_params.remove_coupon_nonce,
				coupon: coupon
			};

			$.ajax( {
				type:    'POST',
				url:      get_url( 'remove_coupon' ),
				data:     data,
				dataType: 'html',
				success: function( response ) {
					$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
					show_notice( response );
					$( document.body ).trigger( 'removed_coupon', [ coupon ] );
					unblock( $wrapper );
				},
				complete: function() {
					cart.update_cart( true );
				}
			} );
		},

		/**
		 * Handle a cart Quantity Update
		 *
		 * @param {JQuery Object} $form The cart form.
		 */
		quantity_update: function( $form ) {
			block( $form );
			block( $( 'div.cart_totals' ) );

			// Provide the submit button value because wc-form-handler expects it.
			$( '<input />' ).attr( 'type', 'hidden' )
							.attr( 'name', 'update_cart' )
							.attr( 'value', 'Update Cart' )
							.appendTo( $form );

			// Make call to actual form post URL.
			$.ajax( {
				type:     $form.attr( 'method' ),
				url:      $form.attr( 'action' ),
				data:     $form.serialize(),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Handle when a remove item link is clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		item_remove_clicked: function( evt ) {
			evt.preventDefault();

			var $a = $( evt.currentTarget );
			var $form = $a.parents( 'form' );

			block( $form );
			block( $( 'div.cart_totals' ) );

			$.ajax( {
				type:     'GET',
				url:      $a.attr( 'href' ),
				dataType: 'html',
				success: update_wc_div,
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		}
	};

	cart_shipping.init( cart );
	cart.init();
} );

(function ($) {
	var screenHeight = $(document).height();
	var screenWidth = $(window).width();
	var $rtl = false;
	var menu_resp = 992;
	if (jQuery("html").attr("dir") == 'rtl'){
		$rtl = true;
	}
	
	$(document).ready(function() {
		if ($('.product .onsale.sale-style-2').length>0) {
			$('.product .onsale.sale-style-2').parent('.woocommerce-LoopProduct-link').siblings('.wishlist-fixed-btn').css({
				'top': '-8px',
				'right': '5px',
				'z-index': 10
			});
		}
		if ($('body.single-product a#triggerDesign').length > 0) {
			$('body.single-product a#triggerDesign').removeClass('bt-4');
		}
		setInterval(function() {
			var screenWidth = $(window).width();
			if(screenWidth<=768) {
				if($('.mega-menu-grid ul.mega-sub-menu li.mega-menu-row').length>0) {
					$('.mega-menu-grid ul.mega-sub-menu li.mega-menu-row').addClass('mega-mm-online-design mm-online-design');
				}
			}

			if(screenWidth<=640) {
				$('.minicart-header').click(function(event) {
					location.href = nbds_frontend.cart_url;
				});
			}

			if ($('table.compare-list td ul').length>0) {
				$('table.compare-list td ul').css('list-style','none');
			}

			if ($('.woocommerce-form__label-for-checkbox .woocommerce-terms-and-conditions-checkbox-text .woocommerce-terms-and-conditions-link').length>0) {
				$('.woocommerce-form__label-for-checkbox .woocommerce-terms-and-conditions-checkbox-text .woocommerce-terms-and-conditions-link').removeClass('woocommerce-terms-and-conditions-link');
			}
		}, 1000);

		$("body").mouseup(function() {
			if ($('#searchbox_autocomplete .nbt-no-result').length>0) {
				$('#searchbox_autocomplete .nbt-no-result').hide();
			}
		});
	});

	/* Replace default display price of woocommerce ver 3.4 */
	if( $('body').hasClass('has-price-matrix') ) {
		$('.nbt-variations > .table-responsive').remove();
	}
	
	$('.all-share').click(function() {
		var $t = $(this);
		var $this = $t.closest('ul');

		if( $t.hasClass('active') ){
			$t.removeClass('active');
			$this.find('li').not($t).hide();
		}else{
			$t.addClass('active');
			$this.find('li').not($t).css('display', 'inline-block');
		}
		return false;
	});

	if( $('.wc-range-slider').length ){
		$( ".wc-range-slider" ).each(function( index ) {
			var currency = $(this).attr('data-currency');
			$(this).asRange({
				limit: true,
				range: true,
				min: $(this).attr('data-min'),
				max: $(this).attr('data-max'),
				set: [10, 50],
				format: function(value) {
					return '$' + value;
				},
				onChange: function(value) {
					$('[name="wcrs-price-input-from"]').val(value[0]);
					$('[name="wcrs-price-input-to"]').val(value[1]);
					

					$('.wcrs-price-show-from').text(currency + '' + value[0]);
					$('.wcrs-price-show-to').text(currency + '' + value[1]);
				},
				tip: {
					active: 'onMove'
				}
			});

		});

		$('.wc-action-filter .button').on("click", function(){
			var $min = $('[name="wcrs-price-input-from"]').val();
			var $max = $('[name="wcrs-price-input-to"]').val();

			filter_price($min, $max);
		});
	}

	function filter_price($min, $max) {
		var url = $('.wc-range-slider').attr('data-url');
		window.location.href = url + '?price_from=' + $min + '&price_to=' + $max;
	}

	/* Cal row full widths */
	if( $('.nbt_row-full-width').length ){
		var containereWidth = (screenWidth - $('.container').outerWidth()) / 2;
		// console.log(containereWidth);
		$('.nbt_row-full-width').each(function( index ) {
			$(this).css({
				'position' : 'relative',
				'width' : (screenWidth - 1),
				'left' : '-' + (containereWidth + 15) + 'px',
				'box-sizing' : 'border-box'
			});
		});
	}
	var bot_section_wrap = $('.site-header.header-1.fixed .bot-section-wrap,'
	+ '.site-header.header-3.fixed .bot-section-wrap,'
	+ '.site-header.header-5.fixed .bot-section-wrap,'
	+ '.site-header.header-6.fixed .bot-section-wrap');
	if(bot_section_wrap.length > 0) {
		var sticky = new Waypoint.Sticky({
			element: $('.site-header.header-1.fixed .bot-section-wrap,'
			+ '.site-header.header-3.fixed .bot-section-wrap,'
			+ '.site-header.header-5.fixed .bot-section-wrap,'
			+ '.site-header.header-6.fixed .bot-section-wrap')[0],
		})
	}

	var middle_section_wrap = $('.site-header.header-2.fixed .middle-section-wrap,'
	+ '.site-header.header-4.fixed .middle-section-wrap,'
	+ '.site-header.header-7.fixed .middle-section-wrap,'
	+ '.site-header.header-8.fixed .middle-section-wrap,'
	+ '.site-header.header-9.fixed .middle-section-wrap,'
	+ '.site-header.header-10.fixed .middle-section-wrap');
	if(middle_section_wrap.length > 0) {
		var sticky = new Waypoint.Sticky({
			element: $('.site-header.header-2.fixed .middle-section-wrap,'
			+ '.site-header.header-4.fixed .middle-section-wrap,'
			+ '.site-header.header-7.fixed .middle-section-wrap,'
			+ '.site-header.header-8.fixed .middle-section-wrap,'
			+ '.site-header.header-9.fixed .middle-section-wrap,'
			+ '.site-header.header-10.fixed .middle-section-wrap')[0],
		})
	}

	$('.widget_nav_menu .menu-item-has-children > a').on('click', function(e) {
		e.preventDefault();
		$(this).next('.sub-menu').first().slideToggle('fast');
	});

	$(window).load(function(){
		auto_cal_ajaxcart();
		nav_blog_slider();
	});

	$( window ).resize(function() {
		auto_cal_ajaxcart();
		nav_blog_slider();
	});
	function nav_blog_slider(){
		var img = ($('.bp-slider-wrap .item a').height() / 2) - 29;
		$('.owl-theme .owl-nav').css("top", img);
	}
	function auto_cal_ajaxcart() {
		if($(".mini-cart-section").length){
			var $width = $(window).width() - 300;
			var position = $('.minicart-header').offset();

			if($width < position.left){
				$(".mini-cart-section").addClass('nbt-ajaxcart-right');
				$(".mini-cart-section").css({
					'left':'inherit',
					'right':'0'
				});
			}else{
				$(".mini-cart-section").addClass('nbt-ajaxcart-left');
				$(".mini-cart-section").css({
					'left':'0',
					'right':'inherit'
				});
			}
		}
	}
	
	function menuPosition() {
		if ($('#main-menu ul.sub-menu').length) {
			$('#main-menu ul.sub-menu').each(function () {
				$(this).removeAttr("style");
				var $containerWidth = $("body").outerWidth();
				var $menuwidth = $(this).outerWidth();
				var $parentleft = $(this).parent().offset().left;
				var $parentright = $(this).parent().offset().left + $(this).parent().outerWidth();
				if ($(this).parents('.sub-menu').length) {
					var $menuleft = $parentleft - $(this).outerWidth();
					var $menuright = $parentright + $(this).outerWidth();
					if ($rtl){
						if ($menuleft < 0) {
							if ($menuright > $containerWidth) {
								if ($parentleft > ($containerWidth - $parentright)) {
									$(this).css({
										'width': $parentleft + 'px',
										'left': 'auto',
										'right': '100%'
									});
								} else {
									$(this).css({
										'width': ($containerWidth - $parentright) + 'px',
										'left': '100%',
										'right': 'auto'
									});
								}
							} else {
								$(this).css({
									'left': '100%',
									'right': 'auto'
								});
							}
						} else {
							$(this).css({
								'left': '-100%'
							});
						}
					} else {
						if ($menuright > $containerWidth) {
							if ($menuleft < 0) {
								if ($parentleft > ($containerWidth - $parentright)) {
									$(this).css({
										'width': $parentleft + 'px',
										'left': 'auto',
										'right': '100%'
									});
								} else {
									$(this).css({
										'width': ($containerWidth - $parentright) + 'px',
										'left': '100%',
										'right': 'auto'
									});
								}
							} else {
								$(this).offset({
									'left': $menuleft
								});
							}
						} else {
							$(this).css({
								'left': '100%'
							});
						}
					}
				} else {
					var $menuleft = $parentright - $(this).outerWidth();
					var $menuright = $parentleft + $(this).outerWidth();
					if ($rtl){
						if ($menuleft < 0) {
							if ($menuright > $containerWidth) {
								$(this).offset({
									'left': ($containerWidth - $menuwidth) / 2
								});
							} else {
								$(this).offset({
									'left': $parentleft
								});
							}
						} else {
							$(this).offset({
								'left': $menuleft
							});
						}
					} else {
						if ($menuright > $containerWidth) {
							if ($menuleft < 0) {
								$(this).offset({
									'left': ($containerWidth - $menuwidth) / 2
								});
							} else {
								$(this).offset({
									'left': $menuleft
								});
							}
						} else {
							$(this).offset({
								'left': $parentleft
							});
						}
					}
				}
			});
		}
	}
	function menuShow() {
		if ($rtl){
			$('.main-navigation .menu-main-menu-wrap').animate({'right': '0'}, 250);
		} else {
			$('.main-navigation .menu-main-menu-wrap').animate({'left': '0'}, 250);
		}
		$('.main-navigation .menu-main-menu-wrap').css('display', 'block');
	}
	function menuHide() {
		if ($rtl){
			$('.main-navigation .menu-main-menu-wrap').animate({'right': '-100%'}, 250);
		} else {
			$('.main-navigation .menu-main-menu-wrap').animate({'left': '-100%'}, 250);
		}
	}
	function menuResponsive(){
		var screenHeight = jQuery(document).height();
		var screenWidth = jQuery(window).width();
		if ($('.navigation_right .menu-sub-menu-container').length){
			if (screenWidth < menu_resp) {
				$('.navigation_right #menu-sub-menu').appendTo('.navigation_left .menu-main-menu-container');
				$('.main-navigation').appendTo('.navigation_right');
			} else {
				$('.main-navigation').appendTo('.navigation_left');
				$('.navigation_left #menu-sub-menu').appendTo('.navigation_right .menu-sub-menu-container');
			}
		} else {
			if($('.main-menu-section .nb-header-sub-menu').length){
				$('.main-menu-section .nb-header-sub-menu > li').appendTo('.nb-navbar');
				$('.main-menu-section .sub-navigation').remove();
			}
		}
		if (screenWidth < menu_resp) {
			$('.site-header').addClass('header-mobile');
			$('.main-navigation').addClass('main-mobile-navigation');
		} else {
			$('.site-header').removeClass('header-mobile');
			$('.main-navigation').removeClass('main-mobile-navigation');
			$('.main-navigation .menu-main-menu-wrap').removeAttr('style');
			$('.main-navigation .menu-item-has-children').removeClass('open');
			menuPosition();
		}
	}
	menuResponsive();
	$('.main-navigation .mobile-toggle-button').on('click', function () {
		menuShow();
	});
	$('.main-navigation .icon-cancel-circle').on('click', function () {
		menuHide();
	});
	$('.main-navigation .menu-item-has-children').on('click', function () {
		$(this).toggleClass('open');
	});
	$('.main-navigation .menu-item-has-children > *').on('click', function (e) {
		e.stopPropagation();
	});
	jQuery('#mega-menu-primary .mega-menu-item-has-children').on('click', function () {
		jQuery(this).toggleClass('open');
	});
	jQuery('#mega-menu-primary .mega-menu-item-has-children > *').on('click', function (e) {
		e.stopPropagation();
	});

	jQuery(window).on('resize', function () {
		menuResponsive();
	});

	var $blog_modern = $('.blog .modern').imagesLoaded( function() {
        // init Isotope after all images have loaded
        $blog_modern.isotope({
            itemSelector: '.post',
        });
    });


	$('div.blog:not(.layout-1-columns).collapse .layout').isotope({
        itemSelector: '.post',
    });

	var d = 0;
	var $numbertype = null;

	var quantityButton = function() {
		$(".quantity-plus, .quantity-minus").mousedown(function () {
			$el = $(this).closest('.nb-quantity').find('.qty');
			$numbertype = parseInt($el.val());
			d = $(this).is(".quantity-minus") ? -1 : 1;
			$numbertype = $numbertype + d;
			if($numbertype > 0) {
				$el.val($numbertype);
				$('[name="quantity"]').triggerHandler('change');
			}

		});
	};
	quantityButton();

	if (jQuery().magnificPopup) {
		$('.featured-gallery').magnificPopup({
			delegate: 'img',
			type: 'image',
			gallery: {
				enabled: true
			},
			callbacks: {
				elementParse: function (item) {
					item.src = item.el.attr('src');
				}
			}
		});
	}

	var $upsells = $('.upsells .products');
	var $upsellsCells = $upsells.find('.product');

	if ($upsellsCells.length <= nb.upsells_columns) {
		$upsells.addClass('hiding-nav-ui');
	}

	var $related = $('.related .products');
	var $relatedCells = $related.find('.product');

	if ($relatedCells.length <= nb.related_columns) {
		$related.addClass('hiding-nav-ui');
	}

	var $crossSells = $('.cross-sells .products');
	var $crossSellsCells = $crossSells.find('.product');

	if ($crossSellsCells.length <= nb.cross_sells_columns) {
		$crossSells.addClass('hiding-nav-ui');
	}

	if (jQuery().accordion) {
		$('.shop-main.accordion-tabs .wc-tabs').accordion({
			header: ".accordion-title-wrap",
			heightStyle: "content",
		});
	}

	$('.header-cart-wrap').on({
		mouseenter: function () {
			$(this).find('.mini-cart-section').stop().fadeIn('fast');
		},
		mouseleave: function () {
			$(this).find('.mini-cart-section').stop().fadeOut('fast');
		}
	});

	$('.customer-action').on({
		mouseenter: function () {
			$(this).find('.nb-account-dropdown').stop().fadeIn('fast');
		},
		mouseleave: function () {
			$(this).find('.nb-account-dropdown').stop().fadeOut('fast');
		}
	});

	$(document.body).on('added_to_cart', function () {
		$(".cart-notice-wrap").addClass("active").delay(5000).queue(function(next){
			$(this).removeClass("active");
			next();
		});
	});

	$('.cart-notice-wrap span').on('click', function() {
		$(this).closest('.cart-notice-wrap').removeClass('active');
	});

	var $sticky = $('.sticky-wrapper.sticky-sidebar');

	if($sticky.length > 0) {
		$($sticky).stick_in_parent({
			offset_top: 45
		});

		$(window).on('resize', function() {
			$($sticky).trigger('sticky_kit:detach');
		});
	}

	if ($('#back-to-top-button').length) {
        var scrollTrigger = 500; // px
        var backToTop = function () {
        	var scrollTop = $(window).scrollTop();
        	if (scrollTop > scrollTrigger) {
        		$('#back-to-top-button').addClass('show');
        	} else {
        		$('#back-to-top-button').removeClass('show');
        	}
        };
        backToTop();
        $(window).on('scroll', function () {
        	backToTop();
        });
        $('#back-to-top-button').on('click', function (e) {
        	e.preventDefault();
        	$('html,body').animate({
        		scrollTop: 0
        	}, 700);
        });
    }
    if ($('.related .swiper-container').length){
    	var slidesm = 2;
    	var slidemd = 3;
    	if(nb.related_columns==2){
    		slidesm = 1;
    		slidemd = 2;
    	}
    	var related = new Swiper('.related .swiper-container', {
    		slidesPerView: nb.related_columns,
    		pagination: {
    			el: '.swiper-pagination',
    			clickable: true,
    		},
    		breakpoints: {
    			991: {
    				slidesPerView: slidemd
    			},
    			767: {
    				slidesPerView: slidesm
    			},
    			575: {
    				slidesPerView: 1
    			}
    		}
    	});
    }
    if ($('.upsells .swiper-container').length){
    	var slidesm = 2;
    	var slidemd = 3;
    	if(nb.upsells_columns==2){
    		slidesm = 1;
    		slidemd = 2;
    	}
    	var upsells = new Swiper('.upsells .swiper-container', {
    		slidesPerView: nb.upsells_columns,
    		pagination: {
    			el: '.swiper-pagination',
    			clickable: true,
    		},
    		breakpoints: {
    			991: {
    				slidesPerView: slidemd
    			},
    			767: {
    				slidesPerView: slidesm
    			},
    			575: {
    				slidesPerView: 1
    			}
    		}
    	});
    }
    if ($('.cross-sells .swiper-container').length){
    	var slidemd = 3;
    	var slidelg = 4;
    	if(nb.cross_sells_columns==3){
    		slidemd = 2;
    		slidelg = 3
    	}
    	var crossSells = new Swiper('.cross-sells .swiper-container', {
    		slidesPerView: nb.cross_sells_columns,
    		pagination: {
    			el: '.swiper-pagination',
    			clickable: true,
    		},
    		breakpoints: {
    			1199: {
    				slidesPerView: slidelg,
    			},
    			991: {
    				slidesPerView: slidemd,
    			},
    			767: {
    				slidesPerView: 2,
    			},
    			575: {
    				slidesPerView: 1,
    			}
    		}
    	});
    }
    var swiperInit = function() {
    	if ($('.featured-gallery').length && $('.thumb-gallery').length){
    		var featuredObj = {
    			navigation: {
    				nextEl: '.swiper-button-next',
    				prevEl: '.swiper-button-prev',
    			},
    			spaceBetween: 10,
    			slidesPerView: 1
    		};

    		var galleryTop = new Swiper('.featured-gallery', featuredObj);

    		var thumbObj = {
    			spaceBetween: 10,
    			centeredSlides: true,
				slidesPerView: 3,
				preloadImages:true,
				updateOnImagesReady:true,
    			touchRatio: 0.2,
    			slideToClickedSlide: true,
    			virtualTranslate: false,
    			on:{
    				transitionStart: function(){
    					translate = this.getTranslate();
    					// console.log('translate',translate);
    					slidesPerView = this.params.slidesPerView == 'auto ' ?this.slidesPerViewDynamic() : this.params.slidesPerView;
    					// console.log(this,this.slidesPerView,this.slides.length);
    					if(this.slides.length<=slidesPerView){
    						return;
    					}

    					var y = 0;
    					var z = 0;
    					var x = 0;

    					if(this.activeIndex > slidesPerView/2)
    					{
    						// console.log(this.activeIndex);
    						translate = this.activeIndex == this.slides.length -1 ? -this.snapGrid[this.snapGrid.length - 2] : this.translate;

    						if (this.isHorizontal()) {
    							x = this.params.rtl ? -translate : translate;
    						} else {
    							y = translate;
    						}

    						if (this.roundLengths) {
    							x = Math.floor(x);
    							y = Math.floor(y);
    						}
    					}
    					
    					if (this.support.transforms3d) { this.$wrapperEl.transform(("translate3d(" + x + "px, " + y + "px, " + z + "px)")); }
    					else { this.$wrapperEl.transform(("translate(" + x + "px, " + y + "px)")); }

    				}
    			},
    		}

    		if(nb.thumb_pos === 'left-thumb' || nb.thumb_pos === 'inside-thumb') {
    			thumbObj.direction = 'vertical';
    		}

    		if($('#yith-quick-view-content .left-thumb').length>0 || $('#yith-quick-view-content .inside-thumb').length>0) {
    			thumbObj.direction = 'vertical';
    		}

    		if($('#yith-quick-view-content .bottom-thumb').length>0) {
    			thumbObj.direction = 'horizontal';
    		}

			// if(nb.thumb_pos === 'inside-thumb' || nb.thumb_pos === 'bottom-thumb') {
			// 	thumbObj.centeredSlides = true
			// }

			var galleryThumbs = new Swiper('.thumb-gallery', thumbObj);

			$('.single-product-wrap .featured-gallery .woocommerce-product-gallery__image').each(function(index){
                $(this).attr('data-index',index);
            })

            $( '.variations_form' ).on( 'show_variation', function ( event, variation){
                var currentindex = 0;
                $('.single-product-wrap .featured-gallery .woocommerce-product-gallery__image').each(function(){
                    var image = $(this).children('img').attr('src');
                    if(image === variation.image.src){
                        currentindex = $(this).data('index');
                    }
                })
                galleryTop.slideTo( currentindex, 300, false );
            })

			try {
				galleryTop.on( 'slideChange', function () {

					var currentIndex = galleryTop.activeIndex;
					galleryThumbs.slideTo( currentIndex, 300, false );

				});

				galleryThumbs.on('slideChange', function () {

					var currentIndex = galleryThumbs.activeIndex;		
					galleryTop.slideTo( currentIndex, 300, false );

					if( nb.enable_image_zoom == '1' ) {
						$('.ZoomContainer').remove();
						$('.featured-gallery .swiper-slide-active > img').ezPlus();
					}
				});

			} catch(err) {
				// console.log(err.message);
				swiperInit2();
			}

			setTimeout(function() {
				if(nb.thumb_pos === 'left-thumb' || $('#yith-quick-view-content .left-thumb').length>0) {
					$('.left-thumb .thumb-gallery .swiper-wrapper .woocommerce-product-gallery__image img').css({
						'height': $('.left-thumb .thumb-gallery .swiper-wrapper .woocommerce-product-gallery__image').height()-10,
						'width': '100%'
					});
				}
				if(nb.thumb_pos === 'bottom-thumb' || $('#yith-quick-view-content .bottom-thumb').length>0) {
					$('.shop-main.bottom-thumb .woocommerce-product-gallery__wrapper .thumb-gallery .swiper-wrapper').css({
						'transform': 'translate3d(0px, 0px, 0px)'
					});
				}
			}, 1000);
		}
	};

	var swiperInit2 = function() {
		if ($('#yith-quick-view-modal .featured-gallery').length && $('#yith-quick-view-modal .thumb-gallery').length){
			var featuredObj = {
				navigation: {
					nextEl: '#yith-quick-view-modal .swiper-button-next',
					prevEl: '#yith-quick-view-modal .swiper-button-prev',
				},
				spaceBetween: 10,
				slidesPerView: 1
			};

			var galleryTop = new Swiper('#yith-quick-view-modal .featured-gallery', featuredObj);

			var thumbObj = {
				spaceBetween: 10,
				centeredSlides: true,
				slidesPerView: 3,
				preloadImages:true,
				updateOnImagesReady:true,
				touchRatio: 0.2,
				slideToClickedSlide: true,
				virtualTranslate: false,
				on:{
					transitionStart: function(){
						translate = this.getTranslate();
    					// console.log('translate',translate);
    					slidesPerView = this.params.slidesPerView == 'auto ' ?this.slidesPerViewDynamic() : this.params.slidesPerView;
    					// console.log(this,this.slidesPerView,this.slides.length);
    					if(this.slides.length<=slidesPerView){
    						return;
    					}

    					var y = 0;
    					var z = 0;
    					var x = 0;

    					if(this.activeIndex > slidesPerView/2)
    					{
    						// console.log(this.activeIndex);
    						translate = this.activeIndex == this.slides.length -1 ? -this.snapGrid[this.snapGrid.length - 2] : this.translate;

    						if (this.isHorizontal()) {
    							x = this.params.rtl ? -translate : translate;
    						} else {
    							y = translate;
    						}

    						if (this.roundLengths) {
    							x = Math.floor(x);
    							y = Math.floor(y);
    						}
    					}
    					
    					if (this.support.transforms3d) { this.$wrapperEl.transform(("translate3d(" + x + "px, " + y + "px, " + z + "px)")); }
    					else { this.$wrapperEl.transform(("translate(" + x + "px, " + y + "px)")); }

    				}
    			},
    		}

    		var test = false;
    		if($('#yith-quick-view-content .left-thumb').length>0 || $('#yith-quick-view-content .inside-thumb').length>0) {
    			thumbObj.direction = 'vertical';
    		} else {
    			test = true;
    		}

    		$('#yith-quick-view-modal .thumb-gallery').removeClass('swiper-container-vertical').removeClass('swiper-container-horizontal');

    		var galleryThumbs = new Swiper('#yith-quick-view-modal .thumb-gallery', thumbObj);

    		galleryTop.on( 'slideChange', function () {

    			var currentIndex = galleryTop.activeIndex;
    			galleryThumbs.slideTo( currentIndex, 300, false );

    		});

    		galleryThumbs.on('slideChange', function () {

    			var currentIndex = galleryThumbs.activeIndex;		
    			galleryTop.slideTo( currentIndex, 300, false );
			});

    		if (test) {
    			setTimeout(function() {
    				$('#yith-quick-view-modal .thumb-gallery .woocommerce-product-gallery__image').css({
    					'height': 'auto'
    				});
    			}, 1000);
    		}

    		if($('#yith-quick-view-content .left-thumb').length>0) {
    			setTimeout(function() {
    				$('.shop-main.left-thumb .woocommerce-product-gallery__wrapper .thumb-gallery .swiper-wrapper').css({
    					'transform': 'translate3d(0px, 0px, 0px)'
    				});
    				$('.shop-main.left-thumb .woocommerce-product-gallery__wrapper .thumb-gallery .swiper-slide').css({
    					'width': 'auto'
    				});
    				$('#yith-quick-view-modal .left-thumb .thumb-gallery .swiper-wrapper .woocommerce-product-gallery__image img').css({
    					'height': $('#yith-quick-view-modal .left-thumb .thumb-gallery .swiper-wrapper .woocommerce-product-gallery__image').height()-10,
    					'width': '100%'
    				});
    			}, 1500);
    		}
    		if($('#yith-quick-view-content .bottom-thumb').length>0) {
    			setTimeout(function() {
    				$('.shop-main.bottom-thumb .woocommerce-product-gallery__wrapper .thumb-gallery .swiper-wrapper').css({
    					'transform': 'translate3d(0px, 0px, 0px)'
    				});
    			}, 1000);
    		}
    	}
    };

    swiperInit();

    var isMobile = false;
    var $variation_form = $('.variations_form');
    var $product_variations = $variation_form.data( 'product_variations' );
    $('body').on('click touchstart','li.swatch-item',function(){
    	var current = $(this);
    	var value = current.attr('option-value');
    	var selector_name = current.closest('ul').attr('data-id');
    	if($("select#"+selector_name).find('option[value="'+value+'"]').length > 0)
    	{
    		$(this).closest('ul').children('li').each(function(){
    			$(this).removeClass('selected');
    			$(this).removeClass('disable');
    		});
    		if(!$(this).hasClass('selected'))
    		{
    			current.addClass('selected');
    			$("select#"+selector_name).val(value).change();
    			$("select#"+selector_name).trigger('change');
    			$variation_form.trigger( 'wc_variation_form' );
    			$variation_form
    			.trigger( 'woocommerce_variation_select_change' )
    			.trigger( 'check_variations', [ '', false ] );
    		}
    	}else{
    		current.addClass('disable');
    	}
    });

    $variation_form.on('wc_variation_form', function() {
    	$( this ).on( 'click', '.reset_variations', function( event ) {
    		$(this).parents('.variations').eq(0).find('ul.swatch li').removeClass('selected');
    	});
    });
    var $single_variation_wrap = $variation_form.find( '.single_variation_wrap' );
    $single_variation_wrap.on('show_variation', function(event,variation) {
    	var $product = $variation_form.closest('.product');
    	if(variation.image_link)
    	{
    		var variation_image = variation.image_link;
    		$product.find('.main-image a').attr('href',variation_image);
    		$product.find('.main-image a img').attr('src',variation.image_src);
    		$product.find('.main-image a img').attr('srcset',variation.image_srcset);
    		$product.find('.main-image a img').attr('alt',variation.image_alt);
    		$product.find('.main-image a img').attr('title',variation.image_title);
    		$product.find('.main-image a img').attr('sizes',variation.image_sizes);
    		$product.find('.main-image img').attr('data-large',variation_image);
    	}
    });

    if( typeof nb === 'undefined' ) {
    	return;
    }

    var qv_modal    = $(document).find( '#yith-quick-view-modal' ),
    qv_overlay  = qv_modal.find( '.yith-quick-view-overlay'),
    qv_content  = qv_modal.find( '#yith-quick-view-content' ),
    qv_close    = qv_modal.find( '#yith-quick-view-close' ),
    qv_wrapper  = qv_modal.find( '.yith-wcqv-wrapper'),
    qv_wrapper_w = qv_wrapper.width(),
    qv_wrapper_h = qv_wrapper.height(),
    center_modal = function() {

    	var window_w = $(window).width(),
    	window_h = $(window).height(),
    	width    = ( ( window_w - 60 ) > qv_wrapper_w ) ? qv_wrapper_w : ( window_w - 60 ),
    	height   = ( ( window_h - 120 ) > qv_wrapper_h ) ? qv_wrapper_h : ( window_h - 120 );

    	qv_wrapper.css({
    		'left' : (( window_w/2 ) - ( width/2 )),
    		'top' : (( window_h/2 ) - ( height/2 )),
    		'width'     : width + 'px',
    		'height'    : height + 'px'
    	});
    };


    /*==================
     *MAIN BUTTON OPEN
     ==================*/

     $.fn.yith_quick_view = function() {

     	$(document).off( 'click', '.yith-wcqv-button' ).on( 'click', '.yith-wcqv-button', function(e){
     		e.preventDefault();

     		var t           = $(this),
     		product_id  = t.data( 'product_id' );

     		t.block({
     			message: null,
     			overlayCSS  : {
     				background: '#fff url(' + nb.loader + ') no-repeat center',
     				opacity   : 0.5,
     				cursor    : 'none'
     			}
     		});

     		t.addClass('loading');

     		setTimeout(function() {
     			t.removeClass('loading');
     		}, 3000);

     		if( ! qv_modal.hasClass( 'loading' ) ) {
     			qv_modal.addClass('loading')
     		}

            // stop loader
            $(document).trigger( 'qv_loading' );
            ajax_call( t, product_id, true );
        });
     };

    /*================
     * MAIN AJAX CALL
     ================*/

     var ajax_call = function( t, product_id, is_blocked ) {

     	$.ajax({
     		url: nb.ajaxurl,
     		data: {
     			action: 'yith_load_product_quick_view',
     			product_id: product_id
     		},
     		dataType: 'html',
     		type: 'POST',
     		success: function (data) {

     			qv_content.html(data);

                // quantity fields for WC 2.2
                if (nb.is2_2) {
                	qv_content.find('div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)').addClass('buttons_added').append('<input type="button" value="+" class="plus" />').prepend('<input type="button" value="-" class="minus" />');
                }

                // Variation Form
                var form_variation = qv_content.find('.variations_form');

                form_variation.wc_variation_form();
                form_variation.trigger('check_variations');

                if (typeof $.fn.yith_wccl !== 'undefined') {
                	form_variation.yith_wccl();
                }

                // Init prettyPhoto
                if (typeof $.fn.prettyPhoto !== 'undefined') {
                	qv_content.find("a[data-rel^='prettyPhoto'], a.zoom").prettyPhoto({
                		hook: 'data-rel',
                		social_tools: false,
                		theme: 'pp_woocommerce',
                		horizontal_padding: 20,
                		opacity: 0.8,
                		deeplinking: false
                	});
                }

                if (!qv_modal.hasClass('open')) {
                	qv_modal.removeClass('loading').addClass('open');
                	if (is_blocked)
                		t.unblock();
                }

                // stop loader
                $(document).trigger('qv_loader_stop');
                swiperInit();
                quantityButton();
            }
        });
     };

    /*===================
     * CLOSE QUICK VIEW
     ===================*/

     var close_modal_qv = function() {

        // Close box by click overlay
        qv_overlay.on( 'click', function(e){
        	close_qv();
        });
        // Close box with esc key
        $(document).keyup(function(e){
        	if( e.keyCode === 27 )
        		close_qv();
        });
        // Close box by click close button
        qv_close.on( 'click', function(e) {
        	e.preventDefault();
        	close_qv();
        });

        var close_qv = function() {
        	qv_modal.removeClass('open').removeClass('loading');

        	setTimeout(function () {
        		qv_content.html('');
        	}, 1000);
        }
    };

    close_modal_qv();


    center_modal();
    $( window ).on( 'resize', center_modal );

    // START
    $.fn.yith_quick_view();

    $( document ).on( 'yith_infs_adding_elem yith-wcan-ajax-filtered', function(){
        // RESTART
        $.fn.yith_quick_view();
    });

    $('.add_to_wishlist').on('click', function() {
    	$(this).find('.icon-heart').hide();
    });

    $('.product-action .compare').each(function(){
    	jQuery(this).addClass('button bt-4');
    });
    

    $(".site-header .search-form").on('click',function(){
    	$(".header-search-wrap").addClass("popup_content");
    	$(".search-form").addClass("visible");
    	$(".close_popup").css("display","block");
    	$(".site-header .search-field").focus();
    	$(".header-cart-wrap").addClass("fix_position_cart");

    	var link=$(".site-header .search-field");
    	var left = link.offset().left;
    	var top = link.offset().top;
    	var right = left + link.width();
    	$('.popup_content .search-form .nb-input-group .search-button').css({
    		'left': (right-10)+'px',
    		'top': (top-(screenWidth<1000?5:0))+'px'
    	});
    });
    $(".site-header .text-search").on('click',function(){
    	$(".header-search-wrap").addClass("popup_content");
    	$(".search-form").addClass("visible");
    	$(".close_popup").css("display","block");
    	$(".site-header .search-field").focus();

    });
    $(".close_popup").on('click',function(){
    	$(".header-search-wrap").removeClass("popup_content");
    	$(".search-form").removeClass("visible");
    	$(".close_popup").css("display","none");
    	$(".header-cart-wrap").removeClass("fix_position_cart");
    });

    $( "#netbase-responsive-toggle" ).on("click",function(e){
    	e.preventDefault();
    	$(".header-right-wrap-top").animate({ width: 'toggle', height: '8335px'});
    	$(".mega-menu-toggle").addClass('mega-menu-open');
    });

    $(".mega-menu-toggle").on("click",function(){
    	$(".header-right-wrap-top").animate({ width: 'toggle', height: '8335px'});
    	setTimeout(function(){ $(".header-right-wrap-top").removeAttr('style'); }, 1000);
	});
	
	$('.woocommerce-cart tbody tr.cart_item td[data-title],.woocommerce-checkout tbody tr.cart_item td[data-title]').each(function(){
        var attr = $(this).attr('data-title');
        $(this).prepend("<span class='title'>"+ attr +"</span>");
    })

    jQuery('.header-7 .header7-middle .header7-search > i').click(function(){
    	jQuery('.header-7 div.search_text').show();
    })
    jQuery('.header-9 .header9-search > i').click(function(){
    	jQuery('.header-9 div.search_text').show();
    })
    $(".header-9.site-header div.search_text > i").click(function(){
    	$(".header-9.site-header div.search_text").hide();
    })
    $(".header-7.site-header div.search_text > i").click(function(){
    	$(".header-7.site-header div.search_text").hide();
    })
    var width=$('#content > .container ').width();
    var innerWidth=$(window).innerWidth();
    var sub=(innerWidth-width)/2;
    $('#how-work7 >.vc_column-inner,#own7-img >.vc_column-inner,#testimonial7 > .vc_column-inner').css({paddingLeft:sub})
    $('#own7 > .vc_column-inner,#testimonial7-c > .vc_column-inner').css({paddingRight:sub})
    $(window).resize(function(){
    	var width=$('#content > .container').width();
    	var innerWidth=$(window).innerWidth();
    	var sub=(innerWidth-width)/2;
    	$('#how-work7 >.vc_column-inner,#own7-img >.vc_column-inner,#testimonial7 > .vc_column-inner').css({paddingLeft:sub})
    	$('#own7 > .vc_column-inner,#testimonial7-c > .vc_column-inner').css({paddingRight:sub})
    })
    $("#cate7-wrap .block .aio-icon-component").on("hover",function(){
    	$("#cate7-wrap .block .aio-icon-component").removeClass('active');
    	$(this).addClass('active');
    })
    $("#cate7-wrap .block .aio-icon-component").on("mouseleave",function(){
    	$("#cate7-wrap .block .aio-icon-component").removeClass('active');
    	$("#cate7-wrap .block .aio-icon-component:not(:last-child)").addClass('active');
	})
	var swiper = new Swiper('#entry-swiper .swiper-container', {
        slidesPerView: 'auto',
        centeredSlides: true,
        spaceBetween: 30,
        autoHeight: true,
        loop: true,
    });

	if( jQuery().ezPlus ) {
		if( nb.enable_image_zoom == '1' ) {
			$("#netbase-primary-image").ezPlus();
		}
	}
})(jQuery);