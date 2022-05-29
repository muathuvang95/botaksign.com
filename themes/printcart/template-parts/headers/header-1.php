<?php
if( printcart_get_options('nbcore_show_header_topbar') ):
?>
<div class="top-section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-6">
                <ul class="header-top-left">
                    <li class="top-header-tel"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo printcart_get_options('nbcore_header_top_hotline');?></li>
                    
                    <?php if( printcart_get_options('nbcore_header_top_login_link' ) ):?>
                        <?php if(is_user_logged_in()){?>
                            <li>
                                <?php
                                if(function_exists('wc_get_page_id')):
                                    ?>
                                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('myaccount')) . 'edit-account') ?>">

                                        <span><?php esc_html_e('Your Account','printcart') ?></span>

                                    </a>
                                <?php endif; ?>
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
                                    <span><?php esc_html_e('Login', 'printcart');?></span>
                                </a>
                            </li>
                        <?php }?>
                    <?php endif;?>
                    
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
                </ul>
                <?php 
                if ( is_active_sidebar( 'top-left-sidebar' ) ) {
                    dynamic_sidebar('top-left-sidebar');                                
                }
                ?>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6">
                <div class="header-top-right">
                    <?php printcart_social_section();?>
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
        <div class="row">
            <div class="col-lg-2 col-md-2 col-sm-3 logo-header">
                <div class="logo-wrapper">
                    <?php printcart_get_site_logo(); ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="header-searchbox-content">
                    <?php 
                    if ( is_plugin_active( 'nb-elements/nb-elements.php' ) ) {
                        echo do_shortcode('[nbt_ajax_search]');
                    }
                    ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="middle-right-content">
                    <?php printcart_header_woo_section();?>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="bot-section-wrap">
    <div class="container">
        <?php printcart_main_nav();?>
    </div>
</div>