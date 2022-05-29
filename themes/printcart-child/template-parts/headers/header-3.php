<?php
if( printcart_get_options('nbcore_show_header_topbar') ):
    ?>
    <div class="header-custom-list top-section-wrap">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 header-top-left">
                    <?php
                    if ( is_active_sidebar( 'top-left-sidebar' ) ) {
                        dynamic_sidebar('top-left-sidebar');
                    }
                    ?>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 header-top-right">
                    <ul>
                        <?php
                        if( function_exists('icl_get_languages') && printcart_get_options('nbcore_header_top_language') ) {
                            echo '<li class="top-header-language">';
                            echo sprintf( esc_html__( 'Language: ', 'printcart' ) );
                            echo '<div class="header-sub-language"><span class="has-arrow">' . ICL_LANGUAGE_NAME . '</span>';
                            $wpml_language = icl_get_languages('skip_missing=N&orderby=id&order=ASC&link_empty_to=str');
                            echo '<ul>';
                            foreach ($wpml_language as $wpml_key => $wpml_value) {
                                echo '<li><a href="' . esc_url($wpml_value['url']) . '">'. esc_attr($wpml_value['native_name']) .'</a></li>';
                            }
                            echo '</ul></div></li>';
                        }?>

                        <?php if(printcart_get_options('nbcore_header_top_currency')){?>
                            <li class="top-header-currency">
                                <?php echo sprintf( esc_html__( 'Currency: ', 'printcart' ) );?>
                                <div class="header-sub-language">
                                    <?php echo do_shortcode( '[nbt_currency_switcher]', false ); ?>
                                </div>
                            </li>
                        <?php }?>

                        <?php if( printcart_get_options('nbcore_header_top_login_link' ) ):?>
                            <?php if(is_user_logged_in()){?>
                                <li>
                                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('myaccount')) . 'edit-account') ?>">

                                        <span><?php esc_html_e('Your Account','printcart') ?></span>

                                    </a>
                                </li>
                                <li class="logout">
                                    <a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>">
                                        <span><?php esc_html_e('Logout', 'printcart');?></span>
                                    </a>
                                </li>
                                <?php
                            }else{
                                $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
                                $myaccount_page_url = '';
                                if ( $myaccount_page_id ) {
                                    $myaccount_page_url = get_permalink( $myaccount_page_id );
                                }?>
                                <li>
                                    <a href="<?php echo esc_url($myaccount_page_url);?>">
                                        <span><?php esc_html_e('Register', 'printcart');?></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo esc_url($myaccount_page_url);?>">
                                        <span><?php esc_html_e('Login', 'printcart');?></span>
                                    </a>
                                </li>
                            <?php }?>
                        <?php endif;?>
                    </ul>
                    <?php
                    if ( is_active_sidebar( 'top-right-sidebar' ) ) {
                        dynamic_sidebar('top-right-sidebar');
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="header-custom-list middle-section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-12 col-12 logo-header">
                <div class="logo-wrapper">
                    <?php printcart_get_site_logo(); ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-9 col-9 header-searchbox-content">
                <?php echo do_shortcode('[yith_woocommerce_ajax_search]');?>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-3 middle-right-content">
                <div class="header-cart-wrap minicart-header">
                    <div class="botaksign-cart-wrapper">
                        <div class="show-cart">
                            <span class="text"><span class="df-text"><?php esc_html_e('Your Cart', 'printcart');?></span></span>
                            <span class="price-wrapper"><span class="counter-number"><?php echo WC()->cart->get_cart_contents_count(); ?></span><span class="price"><?php echo WC()->cart->get_cart_total();?></span></span>
                        </div>
                    </div>
                    <div class="mini-cart-section">
                        <div class="mini-cart-container">
                            <div class="mini-cart-wrap <?php echo esc_attr( 'cart-'.WC()->cart->get_cart_contents_count() );?>">
                                <?php wc_get_template( 'cart/mini-cart.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="header-custom-list bot-section-wrap">
    <div class="container">
        <?php printcart_main_nav();?>
    </div>
</div>