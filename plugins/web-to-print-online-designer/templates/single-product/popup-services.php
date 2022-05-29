<?php
    //CS botak
    wp_enqueue_style('popup-service', NBDESIGNER_PLUGIN_URL . '/assets/css/popup-service.css')
?>
<div class="popup-service" id="popup-services" style="display: none;">
    <div class="popup-container">
        <span class="close-sevice-popup">x</span>
        <div class="popup-body">
            <?php
                $post = get_page_by_path( 'artwork-services', OBJECT, 'page' ) ;
            ?>
            <?php if($post): ?>
                <?php echo do_shortcode($post->post_content); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="popup-service" id="popup-service" style="display: none;">
    <div class="popup-container">
        <div class="loading">
            <div class="loadding-container">
                <svg width="38" height="38" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#999" style="margin: auto;">
                    <g fill="none" fill-rule="evenodd">
                        <g transform="translate(1 1)" stroke-width="2">
                            <circle stroke-opacity=".5" cx="18" cy="18" r="18"></circle>
                            <path d="M36 18c0-9.94-8.06-18-18-18" transform="rotate(279.969 18 18)">
                                <animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"></animateTransform>
                            </path>
                        </g>
                    </g>
                </svg>
            </div>
        </div>
        <span class="close-sevice-popup">x</span>
        <div class="popup-body"></div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($) {
        var services = $('.artwork-service');
        $('#popup-services .popup-container .popup-body').empty();
        services.map(i => {
            $('#popup-services .popup-container .popup-body').append(services[i].outerHTML);
        })
        $('#popup-services .content-block a').click(function(e) {
            var service_url = $(this).attr('href');
            e.preventDefault();
            $('#popup-service .popup-container .popup-body').empty();
            $("#popup-service").show();
            $('#popup-service .loading').show();
            $('#popup-services').hide(); 
            $.get(service_url, function( data ) {
                var body = $(data).find(".single-product-wrap");
                body.find(".thumb-gallery.swiper-container").before('<a href="' +service_url + '" target="blank" class="button btn-detail-service">View details</a>');
                body.find(".thumb-gallery.swiper-container").remove();
                $(body).find(".swiper-wrapper .woocommerce-product-gallery__image.swiper-slide").not(':first').remove();
                $(body).find(".product-image .swiper-button-black").remove();
                $('#popup-service .popup-container .popup-body').html(body);
                $('#popup-service .loading').hide();
                $('.single-product .single-product-wrap .wrap-price-pro').css('margin-top', '30px');
                $('.single_add_to_cart_button').show();
            });
        })
        $('#open_m-services-wrap').click(e => {
            $('#popup-services').show();
            $("#container-online-designer").removeClass('is-visible');
        })
        $('.close-sevice-popup').click(e=> {
            $('#popup-services, #popup-service').hide(); 
            $("html").removeClass("nbd-prevent-scroll");
            $("body").removeClass("nbd-prevent-scroll");
        })
        $("body").on( "submit", "#popup-service form.cart", function( event ) {
            $('#popup-service .loading').show();
            event.preventDefault();
            var add_cart_url = '<?php echo home_url('/wp-admin/admin-ajax.php?action=nbo_ajax_cart&releated_pid=' . $pid); ?>';
            var data = new FormData($("#popup-service form.cart")[0]);
            var service_title = $("#popup-service .product_title").html();
            
            $.ajax({
                type: "POST",
                url: add_cart_url,
                data: data,
                processData: false,
                contentType: false,
                success: function(data) {
                    $('#popup-service .loading').hide();
                    $('#popup-services, #popup-service').hide(); 
                    $("html").removeClass("nbd-prevent-scroll");
                    $("body").removeClass("nbd-prevent-scroll");
                    $('.woocommerce-notices-wrapper').html('<div class="woocommerce-message" role="alert"><a href="<?php echo wc_get_cart_url()?>" tabindex="1" class="button wc-forward">View cart</a> “'+ service_title +'” has been added to your cart.</div>');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    $('#popup-service .loading').hide();
                }
            });
        })
    })
</script>