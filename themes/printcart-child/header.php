<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
global $post;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <script id="mcjs">!function (c, h, i, m, p) {
                m = c.createElement(h), p = c.getElementsByTagName(h)[0], m.async = 1, m.src = i, p.parentNode.insertBefore(m, p)
            }(document, "script", "https://chimpstatic.com/mcjs-connected/js/users/632054597762a91166d37dc21/2f512d9df3c2516c7c0abd793.js");</script>

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <div id="page" class="site">
            <?php if (is_front_page()) { ?>
                <h1 class="h1-hidden"><?php echo get_bloginfo(); ?></h1>
            <?php } ?>
            <div id="site-wrapper" <?php
            if (printcart_get_options('nbcore_page_fullbox')) {
                echo 'class="container"';
            }
            ?>>
                <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'printcart'); ?></a>
                <header class="nbheader-desk site-header <?php printcart_header_class(); ?>  <?php
                            if (printcart_get_options('nbcore_header_menu_config')) {
                                echo 'border-bottom';
                            }
                            ?>" role="banner">
                            <?php
                            do_action('nb_core_before_header');

                            printcart_get_header();

                            do_action('nb_core_after_header');
                            ?>
                </header>
                <header class="nbheader-mobile nb-mobile site-header <?php printcart_header_class(); ?>  <?php
                        if (printcart_get_options('nbcore_header_menu_config')) {
                            echo 'border-bottom';
                        }
                            ?>" role="banner">
                    <div class="header-custom-list top-section-wrap">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-6 logo-header">
                                    <div class="logo-wrapper">
                                        <?php printcart_get_site_logo(); ?>
                                    </div>
                                </div>
                                <div class="col-sm-6 header-top-right">
                                    <ul>
                                        <?php
                                        if (function_exists('icl_get_languages') && printcart_get_options('nbcore_header_top_language')) {
                                            echo '<li class="top-header-language">';
                                            echo sprintf(esc_html__('Language: ', 'printcart'));
                                            echo '<div class="header-sub-language"><span class="has-arrow">' . ICL_LANGUAGE_NAME . '</span>';
                                            $wpml_language = icl_get_languages('skip_missing=N&orderby=id&order=ASC&link_empty_to=str');
                                            echo '<ul>';
                                            foreach ($wpml_language as $wpml_key => $wpml_value) {
                                                echo '<li><a href="' . esc_url($wpml_value['url']) . '">' . esc_attr($wpml_value['native_name']) . '</a></li>';
                                            }
                                            echo '</ul></div></li>';
                                        }
                                        ?>

                                        <?php if (printcart_get_options('nbcore_header_top_currency')) { ?>
                                            <li class="top-header-currency">
                                                <div class="header-sub-language">
                                                <?php echo do_shortcode('[nbt_currency_switcher]', false); ?>
                                                </div>
                                            </li>
<?php } ?>

<?php if (printcart_get_options('nbcore_header_top_login_link')): ?>
    <?php if (is_user_logged_in()) { ?>
                                                <li>
                                                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('myaccount')) . 'edit-account') ?>">

                                                        <span><?php esc_html_e('Your Account', 'printcart') ?></span>

                                                    </a>
                                                </li>
                                                <li class="logout">
                                                    <a href="<?php echo wp_logout_url(esc_url(home_url('/'))); ?>">
                                                        <span><?php esc_html_e('Logout', 'printcart'); ?></span>
                                                    </a>
                                                </li>
                                                <?php
                                            } else {
                                                $myaccount_page_id = get_option('woocommerce_myaccount_page_id');
                                                $myaccount_page_url = '';
                                                if ($myaccount_page_id) {
                                                    $myaccount_page_url = get_permalink($myaccount_page_id);
                                                }
                                                ?>
                                                <li>
                                                    <a href="<?php echo esc_url($myaccount_page_url); ?>">
                                                        <span><?php esc_html_e('Register', 'printcart'); ?></span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="<?php echo esc_url($myaccount_page_url); ?>">
                                                        <span><?php esc_html_e('Login', 'printcart'); ?></span>
                                                    </a>
                                                </li>
                                        <?php } ?>
                                    <?php endif; ?>
                                    </ul>
<?php
if (is_active_sidebar('top-right-sidebar')) {
    dynamic_sidebar('top-right-sidebar');
}
?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="header-custom-list middle-section-wrap">
                        <div class="container">
                            <div class="row">
                                <div class="col-sm-2 nbmain-menu">
                                    <?php printcart_main_nav(); ?>
                                </div>
                                <div class="col-sm-7 header-searchbox-content">
                                    <?php echo do_shortcode('[yith_woocommerce_ajax_search]'); ?>
                                </div>
                                <div class="col-sm-3 middle-right-content">
<?php printcart_header_woo_section(false); ?>

                                </div>
                                <div class="nbmain-menusub">
<?php printcart_main_nav(); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </header>
                <div id="content" class="site-content">