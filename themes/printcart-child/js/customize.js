'use strict';
jQuery(document).ready(function () {
    jQuery( "<style>table.compare-list tr.description td ul { list-style: none !important; }</style>" ).appendTo( "head" );

    var blogSite = jQuery('.blog');
    var firstArticle = blogSite.find('article').first();
    firstArticle.addClass('first-article');
        
    //CSS botak submenu height
    var sub_item_menu_wrap_post_type = jQuery('#mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item-type-post_type > ul.mega-sub-menu > li.mega-menu-item > ul.mega-sub-menu');
    var sub_item_menu_wrap_customize = jQuery('#mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item-type-taxonomy > ul.mega-sub-menu > li.mega-menu-item > ul.mega-sub-menu');
    function customize_col_megamenu(sub_item_menu_wrap) {
        jQuery.each(sub_item_menu_wrap, (index, e) => {
            var flag1 = false;
            var flag2 = false;
            var flag3 = false;
            var title = '';
            var first_col = jQuery('<div class="first-col"></div>');
            var second_col = jQuery('<div class="second-col"></div>');
            var third_col = jQuery('<div class="third-col"></div>');
            var sub_item_menu = jQuery(e).find('> .mega-menu-item');
            if(sub_item_menu.length == 2) {
                if(jQuery(sub_item_menu[0]).hasClass('mega-menu-item-has-children') || jQuery(sub_item_menu[1]).hasClass('mega-menu-item-has-children')) {
                    flag2 = true;
                    first_col.append(sub_item_menu[0]);
                    second_col.append(sub_item_menu[1]);
                }
            } else {
                jQuery.each(sub_item_menu, (i, sub_item) => {
                    if(jQuery(sub_item).hasClass('mega-menu-item-has-children')) {
                        flag1 = true;
                        switch (i % 3) {
                            case 0:
                                first_col.append(sub_item);
                                break;
                            case 1:
                                second_col.append(sub_item);
                                break;
                            case 2:
                                third_col.append(sub_item);
                                break;
                        }
                    } 
                });
            }
            title = jQuery(e).parent().find('> a.mega-menu-link')[0].outerHTML;
            if(flag2) {
                jQuery(e).addClass('menu-2-col');
                jQuery(e).empty();
                jQuery(e).append(first_col);
                jQuery(e).append(second_col);
            }
            if(flag1) {
                jQuery(e).addClass('menu-3-col');
                jQuery(e).empty();
                jQuery(e).append(first_col);
                jQuery(e).append(second_col);
                jQuery(e).append(third_col);
            }
            if(!flag1 && !flag2) {
                jQuery(e).addClass('menu-1-col');
                jQuery(e).prepend(title);
            }
    //        var max_height = 0;
    //        jQuery.each(sub_item_menu, (i, se) => {
    //            if (max_height < jQuery(se).find('ul.mega-sub-menu').height()) {
    //                max_height = jQuery(se).find('ul.mega-sub-menu').height();
    //            };
    //        })
    //        jQuery.each(sub_item_menu, (i, se) => {
    //            if (i < 3) {
    //                jQuery(se).find('ul.mega-sub-menu').css('height', max_height);
    //            }
    //        })
        });
    }
    customize_col_megamenu(sub_item_menu_wrap_post_type);
    customize_col_megamenu(sub_item_menu_wrap_customize);
    jQuery( ".single-product .product-image .featured-gallery .woocommerce-product-gallery__image" ).height(jQuery( ".single-product .product-image .featured-gallery .woocommerce-product-gallery__image" ).width());
    jQuery( ".single-product .product-image .thumb-gallery .woocommerce-product-gallery__image" ).height(jQuery( ".single-product .product-image .thumb-gallery .woocommerce-product-gallery__image" ).width());
    jQuery(window).resize(function() {
        jQuery( ".single-product .product-image .featured-gallery .woocommerce-product-gallery__image" ).height(jQuery( ".single-product .product-image .featured-gallery .woocommerce-product-gallery__image" ).width());
        jQuery( ".single-product .product-image .thumb-gallery .woocommerce-product-gallery__image" ).height(jQuery( ".single-product .product-image .thumb-gallery .woocommerce-product-gallery__image" ).width());
    });
    jQuery(".archive .shop-main .products li.product.first img").addClass('attachment-printcart-masonry size-printcart-masonry wp-post-image');
//    //Plus and minus quantity button
//    jQuery('.nb-quantity .quantity-plus-cart').one().on('click', function() {
//        var input = jQuery('.nb-quantity input.qty');
//        var val = parseInt(input.val());
//        var step = input.attr('step');
//        step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
//        input.val( val + step ).change();
//    });
//
//    jQuery('.nb-quantity .quantity-minus-cart').one().on('click', function() {
//        var input = jQuery('.nb-quantity input.qty');
//        var val = parseInt(input.val());
//        var step = input.attr('step');
//        step = 'undefined' !== typeof(step) ? parseInt(step) : 1;
//        if (val > 1) {
//            input.val( val - step ).change();
//        }
//    });
});