<?php
if( printcart_get_options('nbcore_show_header_topbar') ):
?>
<div class="top-section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="header-top-left-wrapper">
                    <ul class="header-top-left">
                        
                        <?php if( printcart_get_options('nbcore_header_top_login_link' ) ):?>
                            <?php if(is_user_logged_in()){?>
                                <li class="top-header-your-account">
                                    <a href="<?php echo esc_url( get_permalink(wc_get_page_id('myaccount')) . 'edit-account' ) ?>">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        <span><?php esc_html_e('Your Account','printcart') ?></span>
                                        
                                    </a>
                                </li>
                                <li class="top-header-logout">
                                    <a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>">
                                        <span><?php esc_html_e('Logout', 'printcart');?></span>
                                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <?php
                            }else{
                                $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
                                $myaccount_page_url = '';
                                if ( $myaccount_page_id ) {
                                    $myaccount_page_url = get_permalink( $myaccount_page_id );
                                }?>
                                <li class="top-header-join-free">
                                    <a href="<?php echo esc_url($myaccount_page_url);?>">
                                        <i class="fa fa-pencil" aria-hidden="true"></i> 
                                        <span><?php esc_html_e('Join free', 'printcart');?></span>
                                    </a>
                                </li>
                                <li class="top-header-login">
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
                            echo '</div></li>';
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
            <div class="col-lg-7 col-md-12 col-sm-12">
                <div class="header-top-right-wrapper">
                    <?php 
                    if ( is_active_sidebar( 'top-right-sidebar' ) ) {
                        dynamic_sidebar('top-right-sidebar');                                
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="middle-section-wrap">
    <div class="container">
        <div class="row middle-section">
            <div class="col-lg-2 col-md-2 col-sm-3 logo-header">
                <div class="logo-wrapper">
                    <?php printcart_get_site_logo(); ?>
                </div>
            </div>

            <div class="header-right-wrap-top col-sm-7 col-md-9">
                <?php printcart_main_nav();?>
            </div>

            <div class="header-right-cart-search col-xs-5 col-sm-2 col-md-1">
                <div class="middle-right-content">
                    <?php printcart_header_woo_section(); ?>
                    <div class="search_text">
                        <?php printcart_search_section(false);?>
                        <span class="text-search"><a href="#">Search</a></span>
                    </div>
                    <div class='close_popup'></div>
                </div>
            </div>

        </div>
    </div>
</div>