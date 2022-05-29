<?php
if( printcart_get_options('nbcore_show_header_topbar') ):
    $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
    $myaccount_page_url = '';
    if ( $myaccount_page_id ) {
        $myaccount_page_url = get_permalink( $myaccount_page_id );
    }
    ?>
    <div class="top-section-wrap">
        <div class="container">
            <div class="row wrap9">
                <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4 logo-header">
                    <div class="logo-wrapper">
                        <?php printcart_get_site_logo(); ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8 col-sm-8 col-xs-8 login">
                    <div class="header-top-right-wrapper">
                        <ul class="header-top-right">
                            <li class="top-header-pencil"><i class="fa fa-pencil" aria-hidden="true"></i> <a href="<?php echo esc_url($myaccount_page_url);?>">
                                <span><?php esc_html_e('Join free', 'printcart');?></span>
                            </a></li>
                            
                            <?php if( printcart_get_options('nbcore_header_top_login_link' ) ):?>
                                <?php if(is_user_logged_in()){?>
                                    <li class="logout">
                                        <a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>">
                                            <i class="fa fa-sign-out" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Logout', 'printcart');?></span>
                                        </a>
                                    </li>
                                    <?php
                                }else{
                                    ?>
                                    <li>
                                        <a href="<?php echo esc_url($myaccount_page_url);?>">
                                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                                            <span><?php esc_html_e('Login', 'printcart');?></span>
                                        </a>
                                    </li>
                                <?php }?>
                            <?php endif;?>
                            
                            <?php
                            if( function_exists('icl_get_languages') && printcart_get_options('nbcore_header_top_language') ) {
                                echo '<li class="top-header-language">';
                                echo '<div class="header-sub-language"><span class="has-arrow">' . ICL_LANGUAGE_NAME . '</span>';
                                $wpml_language = icl_get_languages('skip_missing=N&orderby=id&order=ASC&link_empty_to=str');
                                echo '<ul>';
                                foreach ($wpml_language as $wpml_key => $wpml_value) {
                                    echo '<li><a href="' . esc_url($wpml_value['url']) . '">'. esc_attr($wpml_value['native_name']) .'</a></li>';
                                }
                                echo '</ul></div></li>';
                            }?> 
                            <?php if(printcart_get_options('nbcore_header_top_currency')) { ?>
                                <li class="top-header-currency">
                                    <div class="header-sub-language">
                                        <?php echo do_shortcode( '[nbt_currency_switcher]', false ); ?>
                                    </div>
                                </li>
                            <?php }?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="middle-section-wrap">
    <div class="container">
        <div class="row middle-section">
            <div class="header-right-wrap-top col-md-10 col-sm-9">
                <?php printcart_main_nav();?>
            </div>

            <div class="header-right-cart-search col-md-2 col-sm-3">
                <div class="middle-right-content">
                    <div class="header-cart-wrap minicart-header">
                        <div class="cart-wrapper">
                            <span class="counter-number"><?php echo WC()->cart->get_cart_contents_count(); ?></span>

                        </div>
                        <div class="mini-cart-section">
                            <div class="mini-cart-container">
                                <div class="mini-cart-wrap <?php echo esc_attr( 'cart-'.WC()->cart->get_cart_contents_count() );?>">
                                    <?php wc_get_template( 'cart/mini-cart.php'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header9-search">
                        <i class="fa fa-search"></i>
                    </div>
                    <div class='close_popup'></div>
                </div>
            </div>
            <div class="search_text col-lg-12">
                <i class="fa fa-times" aria-hidden="true"></i>
                <?php printcart_search_section(false);?>
                <span class="text-search"><a href="#">Search</a></span>
            </div>
        </div>
    </div>
</div>