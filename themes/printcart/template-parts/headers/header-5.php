<?php
if( printcart_get_options('nbcore_show_header_topbar') ):
?>
<div class="header-custom-list top-section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 header-top-left">
                <ul>
                    <li class="top-header-tel"><i class="fa fa-phone" aria-hidden="true"></i> <?php echo printcart_get_options('nbcore_header_top_hotline');?></li>
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
                                <ul>

                                </ul>
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
            <div class="col-lg-6 col-md-12 col-sm-12 header-top-right">
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
<?php endif; ?>

<div class="header-custom-list middle-section-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-12 col-sm-12 logo-header">
                <div class="logo-wrapper">
                    <?php printcart_get_site_logo(); ?>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 header-searchbox-content">
                <?php echo do_shortcode('[nbt_ajax_search]');?>

            </div>
            <div class="col-lg-4 col-md-12 middle-right-content">
                <?php printcart_header_woo_section();?>

            </div>
        </div>
    </div>
</div>

<div class="header-custom-list bot-section-wrap">
    <div class="container">
        <?php printcart_main_nav();?>
    </div>
</div>