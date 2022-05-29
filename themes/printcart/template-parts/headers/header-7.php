<div class="header-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-12 left7">
                <div class="header7-top-left-wrapper">
                    <ul class="header7-top-left">
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
                        }
                        
                        if(printcart_get_options('nbcore_header_top_currency')){?>
                        <li class="top-header-currency">
                            <?php echo sprintf( esc_html__( 'Currency: ', 'printcart' ) ); ?> <div class="header-sub-language">
                                <?php echo do_shortcode( '[nbt_currency_switcher]', false ); ?>
                            </div>
                        </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-4 logo-header">
                <div class="logo-wrapper">
                    <?php printcart_get_site_logo(); ?>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-8 right7">
                <div class="header7-top-right-wrapper">
                    <div class="header7-top-right">
                        <div class='close_popup'></div>
                        <?php printcart_header_woo_section(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="middle-section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 header7-middle">
                <?php printcart_main_nav();?>
                <div class="header7-search">
                    <i class="pt-icon-search"></i>
                </div>
            </div>
            <div class="search_text col-lg-12">
                <i class="fa fa-times" aria-hidden="true"></i>
                <?php printcart_search_section();?>
            </div>
        </div>
    </div>
</div>