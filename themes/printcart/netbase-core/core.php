<?php

define('NBT_VER', '2.0.0');

class NBT_Core
{
    /**
     * Class prefix for autoload
     *
     * @var string
     */
    protected $prefix = 'NBT_';

    public function __construct()
    {
        require_once get_template_directory() . '/netbase-core/vendor/tgmpa/class-tgm-plugin-activation.php';

        if(! class_exists('Merlin')) {
            require_once get_parent_theme_file_path( '/inc/merlin/vendor/autoload.php' );
            require_once get_parent_theme_file_path( '/inc/merlin/class-merlin.php' );
            require_once get_parent_theme_file_path( '/netbase-core/import/merlin-config.php' );
        }
        

        spl_autoload_register(array($this, 'autoload'));

        new NBT_Customize();

        NBT_Helper::include_template_tags();

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            new NBT_Extensions_Woocommerce();
        }

        add_action('after_switch_theme', array($this, 'setup_after_switch_theme'));
        add_action('after_setup_theme', array($this, 'theme_setup'));
        add_action('widgets_init', array($this, 'default_sidebars'));
        add_action('wp_enqueue_scripts', array($this, 'core_scripts_enqueue'), 9998);
        add_action('wp_enqueue_scripts', array($this, 'print_embed_style'), 9999);
        add_action('wp_enqueue_scripts', array($this, 'google_fonts_url'));
        add_action('wp_head', array('NBT_Helper', 'pingback_header'));
        add_action('edit_category', array($this, 'category_transient_flusher'));
        add_action('save_post', array($this, 'category_transient_flusher'));

        add_filter('body_class', array('NBT_Helper', 'body_classes'));
        add_filter('show_recent_comments_widget_style', '__return_false');
        add_filter('upload_mimes', array($this, 'upload_mimes'));
        add_filter( 'comment_form_default_fields', array($this, 'comment_form_fields') );
        add_filter( 'woocommerce_prevent_automatic_wizard_redirect', array($this, 'woo_prevent_automatic_wizard_redirect'));

        $content_width = 1170;
    }

    public function autoload($class_name)
    {
        if (0 !== strpos($class_name, $this->prefix)) {
            return false;
        }

        // Generate file path from class name.
        $base = get_template_directory() . '/netbase-core/';
        $path = strtolower(str_replace('_', '/', substr($class_name, strlen($this->prefix))));

        // Check if class file exists.
        $standard = $path . '.php';
        $alternative = $path . '/' . current(array_slice(explode('/', str_replace('\\', '/', $path)), -1)) . '.php';

        while (true) {
            if (@is_file($base . $standard)) {
                $exists = $standard;

                break;
            }

            if (@is_file($base . $alternative)) {
                $exists = $alternative;

                break;
            }

            if (false === strrpos($standard, '/') || 0 === strrpos($standard, '/')) {
                break;
            }

            $standard = preg_replace('#/([^/]+)$#', '-\\1', $standard);
            $alternative = implode('/', array_slice(explode('/', str_replace('\\', '/', $standard)), 0, -1)) . '/' . substr(current(array_slice(explode('/', str_replace('\\', '/', $standard)), -1)), 0, -4) . '/' . current(array_slice(explode('/', str_replace('\\', '/', $standard)), -1));
        }

        // Include class declaration file if exists.
        if (isset($exists)) {
            return include_once $base . $exists;
        }

        return false;
    }

    public function theme_setup()
    {
        new NBT_Admin();
        
        load_theme_textdomain('printcart', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // A theme must have at least one navbar, right?
        register_nav_menus(array(
            'primary' => esc_html__('Primary', 'printcart'),
            'header-sub' => esc_html__('Header sub menu', 'printcart'),
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        add_image_size('printcart-masonry', 450, 450, true);
        add_image_size('printcart-rectangle', 450, 360, true);
        add_image_size( 'printcart-online-desginer', 400, 380, array( 'center', 'center' ) );
        add_image_size( 'printcart-blog-home', 370, 300, array( 'center', 'center' ) );
        add_image_size( 'printcart-blog-thumb', 870, 350, array( 'center', 'center' ) );
        
        add_theme_support( 'woocommerce' );
    }

    public function setup_after_switch_theme() {
        //update revslider-templates-check option to prevent download rev templates
        update_option( 'revslider-templates-check', strtotime(date("Y-m-d H:i:s")), 'yes' );
    }

    // since woo 3.6, need this function to activate plugin below Woo in Merlin Import
    public function woo_prevent_automatic_wizard_redirect() {
        return true;
    }



    /**
     * Theme default sidebar.
     *
     * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
     */
    public function default_sidebars()
    {
        register_sidebar(array(
            'name' => esc_html__('Default Sidebar', 'printcart'),
            'id' => 'default-sidebar',
            'description' => esc_html__('Add widgets here.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<div class="side-wrap"><h3 class="widget-title">',
            'after_title' => '</h3></div>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Top Right Sidebar', 'printcart'),
            'id' => 'top-right-sidebar',
            'description' => esc_html__('Add widgets for top right header.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Top Left Sidebar', 'printcart'),
            'id' => 'top-left-sidebar',
            'description' => esc_html__('Add widgets for top left header.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Shop Sidebar', 'printcart'),
            'id' => 'shop-sidebar',
            'description' => esc_html__('Add widgets for category page.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Product Sidebar', 'printcart'),
            'id' => 'product-sidebar',
            'description' => esc_html__('Add widgets for product details page', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Top left', 'printcart'),
            'id' => 'top_left',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Top right', 'printcart'),
            'id' => 'top_right',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Footer Middle #1', 'printcart'),
            'id' => 'footer-top-1',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Footer Middle #2', 'printcart'),
            'id' => 'footer-top-2',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Footer Middle #3', 'printcart'),
            'id' => 'footer-top-3',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));

        register_sidebar(array(
            'name' => esc_html__('Footer Middle #4', 'printcart'),
            'id' => 'footer-top-4',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'name' => esc_html__('Newletter', 'printcart'),
            'id' => 'footer_newletter',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));
        register_sidebar(array(
            'name' => esc_html__('Footer bottom', 'printcart'),
            'id' => 'footer_bottom',
            'description' => esc_html__('For best display, please assign only one widget in this section.', 'printcart'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));
    }

    // Todo change to minified version and load conditional. Example: isotope is now always load
    public function core_scripts_enqueue()
    {
        //TODO Remember this
        wp_dequeue_script('wc-cart');

        wp_enqueue_style( 'owl.carousel', get_template_directory_uri() . '/assets/css/owl.carousel.min.css', false, '1.1', 'all' );
        wp_enqueue_script('owl.carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', array(), '3.0.3', true);

        wp_enqueue_style('flickity_style', get_template_directory_uri() . '/assets/vendor/flickity/flickity.min.css', array(), '3.0.3');
        wp_enqueue_script('flickity_script', get_template_directory_uri() . '/assets/vendor/flickity/flickity.pkgd.min.js', array('jquery'), '3.0.3', true);

        wp_enqueue_style('asRange', get_template_directory_uri() . '/assets/css/asRange.min.css', array(), NBT_VER);

        wp_enqueue_style('printcart_front_style', get_template_directory_uri() . '/assets/netbase/css/frontend/main.css', array(), NBT_VER);

        wp_enqueue_style('printcart_woo_style', get_template_directory_uri() . '/assets/netbase/css/frontend/woocommerce.css', array(), NBT_VER);

        if(is_rtl()){
            wp_enqueue_style('printcart_front_style_rtl', get_template_directory_uri() . '/rtl.css', array(), NBT_VER);
        }

        wp_enqueue_style('printcart_fontello_style', get_template_directory_uri() . '/assets/vendor/fontello/fontello.css', array(), NBT_VER);

        wp_enqueue_script('isotope', get_template_directory_uri() . '/assets/vendor/isotope/isotope.pkdg.min.js', array('jquery'), '3.0.3', true);

        if (function_exists('is_product') && is_product() || function_exists('is_cart') && is_cart()) {
            wp_enqueue_style('magnific-popup', get_template_directory_uri() . '/assets/vendor/magnific-popup/magnific-popup.css', array(), '2.0.5');
            wp_enqueue_script('magnific-popup', get_template_directory_uri() . '/assets/vendor/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), '2.0.5', true);
        }

        wp_enqueue_style('swiper', get_template_directory_uri() . '/assets/vendor/swiper/swiper.min.css', array(), '4.2.0');
        wp_enqueue_script('swiper', get_template_directory_uri() . '/assets/vendor/swiper/swiper.min.js', array('jquery'), '4.2.0', true);

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        if (function_exists('is_product') && is_product() && 'accordion-tabs' == printcart_get_options('nbcore_info_style')) {
            wp_enqueue_script('jquery-ui-accordion');
        }

        if (printcart_get_options('nbcore_header_fixed')) {
            wp_enqueue_script('waypointtheme', get_template_directory_uri() . '/assets/vendor/waypoints/jquery.waypoints.min.js', array('jquery'), '4.0.1', true);
        }

        if (printcart_get_options('nbcore_blog_sticky_sidebar') || printcart_get_options('shop_sticky_sidebar') || printcart_get_options('product_sticky_sidebar')) {
            wp_enqueue_script('sticky-kit', get_template_directory_uri() . '/assets/vendor/sticky-kit/jquery.sticky-kit.min.js', array('jquery'), '1.1.2', true);
        }

        wp_enqueue_script( 'asRange', get_template_directory_uri() . '/assets/js/jquery-asRange.min.js', '', '', true );

        wp_enqueue_script('printcart_matchHeight', get_template_directory_uri() . '/assets/js/jquery.matchHeight-min.js', array('jquery'), NBT_VER, true);
        
		if( function_exists('is_product') && is_product() ) {
			wp_enqueue_style( 'jquery.ez-plus', get_template_directory_uri() . '/assets/vendor/elevatezoom/jquery.ez-plus.css', false, '1.1', 'all' );
			wp_enqueue_script('jquery.ez-plus', get_template_directory_uri() . '/assets/vendor/elevatezoom/jquery.ez-plus.js', array('jquery'), NBT_VER, true);
		}
        wp_enqueue_script('printcart_front_script', get_template_directory_uri() . '/assets/netbase/js/main.js', array('jquery'), time(), true);

        $localize_array = array(
            'ajaxurl'           => admin_url( 'admin-ajax.php', 'relative' ),
            'rest_api_url'      => site_url() . '/wp-json/wp/v2/',
            'upsells_columns' => printcart_get_options('nbcore_pd_upsells_columns'),
            'related_columns' => printcart_get_options('nbcore_pd_related_columns'),
            'cross_sells_columns' => printcart_get_options('nbcore_cross_sells_per_row'),
            'thumb_pos' => printcart_get_options('nbcore_pd_thumb_pos'),
			'enable_image_zoom' => get_theme_mod('nbcore_pd_image_zoom'),
        );
            
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $version = version_compare( preg_replace( '/-beta-([0-9]+)/', '', WC()->version ), '2.3.0', '<' );   
            $localize_array['is2_2'] = $version;
        }

        wp_localize_script('printcart_front_script', 'nb', $localize_array);

        wp_dequeue_script('yith-wcqv-frontend');
        wp_dequeue_style('yith-quick-view');
    }

    //TODO optimize this(grouping and bring to css if can)
    //TODO early esc_
    public function get_embed_style()
    {
        $bg_color = printcart_get_options('nbcore_background_color');
        $inner_bg = printcart_get_options('nbcore_inner_background');

        $top_padding = printcart_get_options('nbcore_top_section_padding');
        $top_bg = printcart_get_options('nbcore_header_top_bg');
        $top_color = printcart_get_options('nbcore_header_top_color');
        $menu_top_right = printcart_get_options('nbcore_header_top_right_color');
        $menu_top_left = printcart_get_options('nbcore_header_top_left_color');
        $menu_top_right_title = printcart_get_options('nbcore_header_top_left_title_color');
        $top_hover_color = printcart_get_options('nbcore_header_top_hovercolor');
        $middle_padding = printcart_get_options('nbcore_middle_section_padding');
        $middle_bg = printcart_get_options('nbcore_header_middle_bg');
        $middle_color = printcart_get_options('nbcore_header_middle_color');
        $bot_padding = printcart_get_options('nbcore_bot_section_padding');
        $bot_bg = printcart_get_options('nbcore_header_bot_bg');
        $bot_color = printcart_get_options('nbcore_header_bot_color');

        $menu_border_color = printcart_get_options('nbcore_header_menu_border_color');
        $menu_color = printcart_get_options('nbcore_header_mainmn_color');
        $menu_color2 = printcart_get_options('nbcore_header_mainmnhover_color');

        $logo_area_width = printcart_get_options('nbcore_logo_width');
        $blog_width = printcart_get_options('nbcore_blog_width');
        $primary_color = printcart_get_options('nbcore_primary_color');
        $divider_color = printcart_get_options('nbcore_divider_color');

        $sidebar_bg = printcart_get_options('nbcore_sidebar_color');

        $heading_font_array = explode(",", printcart_get_options('heading_font_family'));

    
        $heading_family = end($heading_font_array);
        $heading_font_style = explode(",", printcart_get_options('heading_font_style'));
        $heading_weight = end($heading_font_style);
        $heading_color = printcart_get_options('nbcore_heading_color');
        $header_top_border = printcart_get_options('nbcore_header_top_border');

        $heading_base_size = printcart_get_options('body_font_size');

        $body_family_array = explode(",", printcart_get_options('body_font_family'));
        $body_family = end($body_family_array);
        $body_style_array = explode(",", printcart_get_options('body_font_style'));
        $body_weight = end($body_style_array);
        $body_color = printcart_get_options('nbcore_body_color');
        $body_size = printcart_get_options('body_font_size');

        $link_color = printcart_get_options('nbcore_link_color');
        $link_hover_color = printcart_get_options('nbcore_link_hover_color');

        $blog_sidebar = printcart_get_options('nbcore_blog_sidebar');
        $page_title_padding = printcart_get_options('nbcore_page_title_padding');
        $page_title_color = printcart_get_options('nbcore_page_title_color');

        $wc_content_width = printcart_get_options('nbcore_shop_content_width');
        $shop_sidebar = printcart_get_options('nbcore_shop_sidebar');
        $loop_columns = printcart_get_options('nbcore_loop_columns');
        $pd_details_sidebar = printcart_get_options('nbcore_pd_details_sidebar');
        $pd_details_width = printcart_get_options('nbcore_pd_details_width');
        $pd_images_width = printcart_get_options('nbcore_pd_images_width');

        $pb_bg = printcart_get_options('nbcore_pb_background');
        $pb_bg_hover = printcart_get_options('nbcore_pb_background_hover');
        $pb_text = printcart_get_options('nbcore_pb_text');
        $pb_text_hover = printcart_get_options('nbcore_pb_text_hover');
        $pb_border = printcart_get_options('nbcore_pb_border');
        $pb_border_hover = printcart_get_options('nbcore_pb_border_hover');
        $sb_bg = printcart_get_options('nbcore_sb_background');
        $sb_bg_hover = printcart_get_options('nbcore_sb_background_hover');
        $sb_text = printcart_get_options('nbcore_sb_text');
        $sb_text_hover = printcart_get_options('nbcore_sb_text_hover');
        $sb_border = printcart_get_options('nbcore_sb_border');
        $sb_border_hover = printcart_get_options('nbcore_sb_border_hover');
        $button_padding = printcart_get_options('nbcore_button_padding');
        $button_border_radius = printcart_get_options('nbcore_button_border_radius');
        $button_border_width = printcart_get_options('nbcore_button_border_width');

        $footer_top_heading = printcart_get_options('nbcore_footer_top_heading');
        $footer_top_color = printcart_get_options('nbcore_footer_top_color');
        $footer_top_bg = printcart_get_options('nbcore_footer_top_bg');
        $footer_bot_heading = printcart_get_options('nbcore_footer_bot_heading');
        $footer_bot_color = printcart_get_options('nbcore_footer_bot_color');
        $footer_bot_bg = printcart_get_options('nbcore_footer_bot_bg');
        $footer_abs_bg = printcart_get_options('nbcore_footer_abs_bg');
        $footer_abs_color = printcart_get_options('nbcore_footer_abs_color');
        $footer_bot_social = printcart_get_options('nbcore_footer_social_media');
        $footer_bot_social_bor = printcart_get_options('nbcore_footer_social_media_border');
        $footer_top_color_bg = printcart_get_options('nbcore_footer_social_media_bg_hover');
        $footer_social_media_hover = printcart_get_options('nbcore_footer_social_media_hover');
        $footer_social_media_bor_hover = printcart_get_options('nbcore_footer_social_media_bor_hover');
        $footer_color_bottom = printcart_get_options('nbcore_footer_border_color');
        $footer_text_hover= printcart_get_options('nbcore_footer_text_color_hover');
        $footer_head_fontsize = printcart_get_options('nbcore_footer_head_fontsize');
        $footer_text_fontsize = printcart_get_options('nbcore_footer_text_fontsize');
        $footer_top_padding_top = printcart_get_options('nbcore_footer_top_padding_top');
        $footer_top_padding_bottom = printcart_get_options('nbcore_footer_top_padding_bottom');
        $footer_border_parent = printcart_get_options('nbcore_footer_border_color_parent');
        $footer_middle_padding_top = printcart_get_options('nbcore_footer_middle_paddingtop');
        $footer_middle_padding_bottom = printcart_get_options('nbcore_footer_middle_paddingbottom');
        $footer_bot_color_border = printcart_get_options('nbcore_footer_bot_color_border');
        $footer_top_color_border = printcart_get_options('nbcore_footer_top_color_border');

        $blog_title_color = printcart_get_options('nbcore_title_color');
        $blog_detail_color = printcart_get_options('nbcore_detail_color');
        $blog_description_color = printcart_get_options('nbcore_description_color');
        $blog_title_color_hover = printcart_get_options('nbcore_title_color_hover');
        $blog_category_color = printcart_get_options('nbcore_category_color');
        
        $blog_title_size = printcart_get_options('nbcore_blog_single_title_size');
        $page_title_size = printcart_get_options('nbcore_page_title_size');


        $sidebar_style = printcart_get_options('sidebar_style');

        $nbcore_blog_sidebar_style = printcart_get_options('nbcore_blog_sidebar_style');

        $page_bg = wp_get_attachment_image_src(get_post_meta(get_the_ID(), 'page_bg_image', true), 'full');
        $page_bg_color = get_post_meta(get_the_ID(), 'page_bg_color', true);

        $mheader_secondary_color = printcart_get_options('nbcore_header_secondary_color');

        $product_image_border_color = printcart_get_options('nbcore_product_image_border_color');
        $nbcore_container_width_screen = printcart_get_options('nbcore_container_width_screen');

        $nbcore_page_404_bg = printcart_get_options('nbcore_page_404_bg');
        $nbcore_page_404_text = printcart_get_options('nbcore_page_404_text');

        $border_color_opacity = str_replace('rgb', 'rgba', $primary_color);
        $border_color_opacity = str_replace(')', '', $border_color_opacity);

        $css = "";

        if(isset($nbcore_container_width_screen) && $nbcore_container_width_screen > '1170'){
            $css .= "
            @media (min-width: 1367px){
                .container {
                    max-width: " . $nbcore_container_width_screen . "px;
                    width: " . $nbcore_container_width_screen . "px;
                }
            }
            ";
        }
        if(isset($nbcore_container_width_screen) && $nbcore_container_width_screen > '1470'){
            $css .= "
            @media (min-width: 1367px){
                .vc_column_container>.vc_column-inner,.list_products.woocommerce .products .product,
                .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl {
                    padding-right: 25px;
                    padding-left: 25px;
                }
                .vc_row,.list_products.woocommerce .products,.product-online-desginer ul#rig,
                .row {
    
                    margin-right: -25px;
                    margin-left: -25px;
                }
            }
            ";
        }

        if($body_family_array[0] === 'custom') {
            $body_custom_font_url = array_slice($body_family_array, 1, -1);
            $css .= "
            @font-face {
                font-family: '" . end($body_family_array) . "';            
            ";

            foreach($body_custom_font_url as $url) {
                $css .= "
                src: url('" . $url . "');
                ";
            }

            $css .= "
            }
            ";
        }
        if($heading_font_array[0] === 'custom') {
            $heading_custom_font_url = array_slice($heading_font_array, 1, -1);
            $css .= "
            @font-face {
                font-family: '" . end($heading_font_array) . "';            
            ";

            foreach($heading_custom_font_url as $url) {
                $css .= "
                src: url('" . $url . "');
                ";
            }

            $css .= "
            }
            ";
        }
        $css .= "
            #site-wrapper {
                background: " . esc_attr($bg_color) . ";
            }
            .single-blog .entry-author,
            .products .list-type-wrap,
            .shop-main.accordion-tabs .accordion-title-wrap,
            .woocommerce .woocommerce-message,
            .woocommerce .woocommerce-info,
            .woocommerce .woocommerce-error,
            .woocommerce-page .woocommerce-message,
            .woocommerce-page .woocommerce-info,
            .woocommerce-page .woocommerce-error,
            .cart-layout-2 .cart-totals-wrap,
            .blog.style-2 .post .entry-content,
            .nb-comment-form textarea,
            .comments-area,
            .blog .post .entry-cat a,
            
            {
                background-color: " . esc_attr($inner_bg) . ";
            }
            .products.list-type .product .list-type-wrap .product-image:before {
                border-right-color: " . esc_attr($inner_bg) . ";
            }
            .main-logo {
                width: " . esc_attr($logo_area_width) . "px;
            }
            a,
            .widget ul li a:hover,
            .footer-top-section a:hover,
            .footer-top-section .widget ul li a:hover,
            .footer-bot-section a:hover,
            .footer-bot-section .widget ul li a:hover,
            .owl-theme .owl-nav [class*='owl-']:hover,
            .error404 main .pnf-heading,
            .error404 main h1{
                color: " . esc_attr($link_color) . ";
            }
            .footer-abs-section p a:hover{
                color: " . esc_attr($link_hover_color) . ";
            }
            a:hover, a:focus, a:active, .entry-title>a:hover{
                color: " . esc_attr($link_hover_color) . ";
            }

            body {
                font-family: " . esc_attr($body_family) . "; 
                font-weight: " . esc_attr($body_weight) . ";
                font-size: " . esc_attr($body_size) . "px;
        ";
        if (in_array("italic", $body_style_array)) {
            $css .= "
                font-style: italic;
            ";
        }
        if (in_array("underline", $body_style_array)) {
            $css .= "
                text-decoration: underline;
            ";
        }
        if (in_array("uppercase", $body_style_array)) {
            $css .= "
                text-transform: uppercase;
            ";
        }
        $css .= "
            }
            .main-navigation .menu-main-menu-wrap #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link {
                font-size: " . esc_attr($body_size) . "px;
            }
            .button, .nb-primary-button, .post-password-form input[type='submit'],
            .single-product .yith-func-wrap .yith-button-wrap .compare.button:hover, .single-product .yith-func-wrap .yith-button-wrap .yith-wcwl-add-to-wishlist:hover, .wpcf7-submit,
            .mug-banner-button .banner-more,
            a.button.product-go-to-shop-now, .vc-home-blog6 .blog-content .hb-readmore,
            .single-social-simple .single-social-simple-left .yith-wcwl-add-to-wishlist .yith-wcwl-add-button:hover a,
            .single-social-simple .single-social-simple-left .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse:hover,
            .single-social-simple .single-social-simple-left .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse:hover,
            .single-social-simple .single-social-simple-left .compare-button .compare:hover,
            .single-social-simple .single-social-simple-left .share-envelope:hover,
            .nb-quantity.style-1 .qty-buttons span:hover,
            .single-social-simple .single-social-simple-left .tooltip,
            .single-social li.active a,
            .single-social li:hover a,
            .single-social li a:focus,
            .single-social li.active a:focus,
            .single-social li.all-share a {
                color: " . esc_attr($pb_text) . " !important;
                background-color: " . esc_attr($pb_bg) . ";
                border-color: " . esc_attr($pb_border) . ";
            }
            .single-social-simple .single-social-simple-left .tooltip:before {
                border-top-color: " . esc_attr($pb_bg) . ";   
            }
            .single-social li a {
                border-color: " . esc_attr($pb_border) . ";
            }
            .single-social-simple .single-social-simple-right ul li a:hover {
                color: " . esc_attr($pb_bg) . ";
            }
            .nb-quantity.style-1 .qty-buttons:hover:after, td.product-quantity .nb-quantity.style-1 .qty-buttons span:hover{
                background-color: " . esc_attr($pb_bg) . ";
            }
            .vc-home-blog4 .blog-content .hb-readmore span,
            #own9 div.button9 a.vc_btn3,
            footer.site-footer .footer-bot-section .form .submit_b input[type=\"submit\"],
            .vc-home-blog3 .blog-content .hb-readmore,
            #product6 .vc-tab-product-content .cat_img a .buttons:after,
            .error404 main .home-link{
                background-color: " . esc_attr($pb_bg) . ";
            }
            .vc-blog .blog-content .hb-readmore:hover span,
            .vc-home-blog3 .blog-content .hb-readmore:hover,
            .mug-banner-button .banner-more:hover,
            #own9 div.button9 a.vc_btn3:hover,
            footer.site-footer .footer-bot-section .form .submit_b input[type=\"submit\"]:hover,
            #product6 .vc-tab-product-content .cat_img a .buttons:hover:after{
                background-color: " . esc_attr($pb_bg_hover) . ";
            }
            #own9 div.button9 a.vc_btn3,
            .vc-blog .blog-content .hb-readmore,
            #product6 .vc-tab-product-content .cat_img a .buttons{
                color: " . esc_attr($pb_text) . ";
            }
            .vc-blog .blog-content .hb-readmore:hover,
            #own9 div.button9 a.vc_btn3:hover,
            #product6 .vc-tab-product-content .cat_img a .buttons:hover{
                color: " . esc_attr($pb_text_hover) . ";
            }
            header #primary-menu li a,
            header #primary-menu .arrow:before,
            #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link {
                color: " . esc_attr( $menu_color ) . ";
            }
            header #primary-menu li:hover >a,
            header #primary-menu .arrow:hover:before,
            #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item.mega-toggle-on > a.mega-menu-link, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:hover, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item > a.mega-menu-link:focus,
            #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item.mega-current-menu-item > a.mega-menu-link, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item.mega-current-menu-ancestor > a.mega-menu-link, #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-item.mega-current-page-ancestor > a.mega-menu-link,
            .main-navigation .menu-main-menu-wrap #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-flyout ul.mega-sub-menu > li.mega-menu-item > a.mega-menu-link:hover, .main-navigation .menu-main-menu-wrap #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-flyout ul.mega-sub-menu > li.mega-menu-item > a.mega-menu-link:focus
            {
                color: " . esc_attr( $menu_color2 ) . " !important;
            }
            .main-navigation .menu-main-menu-wrap #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-megamenu > ul.mega-sub-menu,
            .main-navigation .menu-main-menu-wrap #mega-menu-wrap-primary #mega-menu-primary > li.mega-menu-flyout ul.mega-sub-menu
            {
                border-top: 3px solid " . esc_attr( $menu_color2 ) . ";
            }
            .header-7.site-header .header7-top-left li{
                color: ". esc_attr( $menu_top_left ) .";
            }
            header.site-header .menu-but span{
                background:". esc_attr( $top_hover_color ) .";
            }
            header.site-header .menu-but{
                border-color:". esc_attr( $top_hover_color ) .";
            }
            header ul#primary-menu li .sub-menu{
                border-top:3px solid ". esc_attr( $menu_color2 ) .";
            }
            header.border-bottom ul#primary-menu > li:hover > a{
                border-bottom-color:". esc_attr( $menu_color2 ) .";
            }
            .vc-blog  article:hover .blog-content .caption a{
                color: ". esc_attr( $link_hover_color ) ." !important;
            }
            .header-7.site-header .header7-top-left li:hover,
            .header-7.site-header .customer-action:hover .display-name,
            .header-7.site-header .show-cart:hover .df-text,
            .header-7.site-header .customer-action .nb-account-dropdown nav ul li:hover a,
             .header-7.site-header .customer-action .customer-links a:hover{
                color: ". esc_attr( $top_hover_color ) .";
            }
            .header-7.site-header .customer-action .display-name,
            .header-7.site-header .customer-action .customer-links a,
            .header-7.site-header .customer-action .customer-links,
            .header-7.site-header .show-cart .df-text{
                color: ". esc_attr( $menu_top_right ) .";
            }
            .header-7.site-header .customer-action .top-title {
                color:".esc_attr($menu_top_right_title).";
            }
            .header-9.site-header .top-section-wrap{
                border-color:".esc_attr($header_top_border).";
            }
            .vc-pcontact-info ul li i {
                color: " . esc_attr($pb_bg) . " !important;
            }
            .single-product .yith-func-wrap .yith-button-wrap .compare.button:hover i, .single-product .yith-func-wrap .yith-button-wrap .yith-wcwl-add-to-wishlist:hover i{
                color: " . esc_attr($pb_text) . " !important;
            }            
            .button:hover, .nb-primary-button:hover, .button:focus, .nb-primary-button:focus, .wpcf7-submit:hover,a.button.product-go-to-shop-now:hover, .vc-home-blog6 .blog-content .hb-readmore:hover {
                color: " . esc_attr($pb_text_hover) . ";
                background-color: " . esc_attr($pb_bg_hover) . ";
                border-color: " . esc_attr($pb_border_hover) . ";
            }
            .single-product-wrap .product-image .thumb-gallery .swiper-slide.swiper-slide-active {
                border-color: " . esc_attr($pb_border_hover) . ";
            }

            .pagination-style-2 .page-numbers:not(.dots){
                color: " . esc_attr($pb_text) . " !important;
                background-color: " . esc_attr($pb_bg) . ";
                border-color: " . esc_attr($pb_border) . ";
            }
            .pagination-style-2 .page-numbers:not(.dots):hover{
                color: " . esc_attr($pb_text_hover) . ";
                background-color: " . esc_attr($pb_bg_hover) . ";
                border-color: " . esc_attr($pb_border_hover) . ";
            }

            .nb-secondary-button {
                color: " . esc_attr($sb_text) . ";
                background-color: " . esc_attr($sb_bg) . ";
                border-color: " . esc_attr($sb_border) . ";
            }
            .nb-secondary-button:hover, .nb-secondary-button:focus {
                color: " . esc_attr($sb_text_hover) . ";
                background-color: " . esc_attr($sb_bg_hover) . ";
                border-color: " . esc_attr($sb_border_hover) . ";
            }
            .list-type .add_to_cart_button, .nb-primary-button, .nb-secondary-button, .single_add_to_cart_button, .post-password-form input[type='submit']{
                padding-left: " . esc_attr($button_padding) . "px;
                padding-right: " . esc_attr($button_padding) . "px;
                border-width: " . esc_attr($button_border_width) . "px;
            ";
                if ($button_border_radius) {
                    $css .= "
                        border-radius: " . esc_attr($button_border_radius) . "px;
                    ";
                } else {
                    $css .= "
                        border-radius: 0px;
                    ";
                }
            $css .= "
            }
            .button,
            input[type=submit] {";
                if ($button_border_radius) {
                    $css .= "
                        border-radius: " . esc_attr($button_border_radius) . "px;
                    ";
                } else {
                    $css .= "
                        border-radius: 0px;
                    ";
                }
            $css .= "
            }
            .wpcf7-submit {
                font-size: " . esc_attr(intval($heading_base_size * 1)) . "px !important;
            }
            body,
            .widget ul li a,
            .woocommerce-breadcrumb a,
            .nb-social-icons > a,
            .wc-tabs > li:not(.active) a,
            .shop-main.accordion-tabs .accordion-title-wrap:not(.ui-state-active) a,
            .nb-account-dropdown a,
            .header-account-wrap .not-logged-in,
            .mid-inline .nb-account-dropdown a, 
            .mid-inline .mini-cart-section span, 
            .mid-inline .mini-cart-section a, 
            .mid-inline .mini-cart-section strong,
            .comments-link a,
            .cate-content9 .nbfw_banner-container .txt-des{
                color: " . esc_attr($body_color) . ";
            }
            h1, .vc-pcontact-para h3, .contact-desc h3 {
                font-size: " . esc_attr(intval($heading_base_size * 1.602)) . "px;
            }
            h2, 
            .single-product-wrap .price > span.amount, 
            .single-product-wrap .price ins span,
            .single_variation_box .price > span.amount,
            .single-product-wrap .price .nbtwccs_price_code > span.amount,
			.woocommerce-variation-price .nbtwccs_price_code .woocommerce-Price-amount {
                font-size: " . esc_attr(intval($heading_base_size * 1.424)) . "px;
            }
            h3 {
                font-size: " . esc_attr(intval($heading_base_size * 1.266)) . "px;
            }
            h4, .products .price .amount, .single-product .nbdesign-button,
            .blog .post .entry-title a,
            .single_add_to_cart_button{
                font-size: " . esc_attr(intval($heading_base_size * 1.125)) . "px;
            }
            h5, .widget .widget-title, .comment-reply-title,
            .single-post .comment-form #submit, .rs-search ul li a h4, .vc-pcontact-para p,.timeline-major-wrapper,
            .shop_table.cart tbody .product-name,
            .vc-getto-blog .getblog-item .bg-getblog-item a.learn-more {
                font-size: " . esc_attr(intval($heading_base_size * 1)) . "px;
            }
            h6, 
            p,
            .home-extra-detail,
            .single-product-wrap .product_meta,
            .item-variations label,
            .vc-tab-product-wrapper ul li,
            .widget_categories li a,
            .widget.widget_tag_cloud .tagcloud a,
            .single-blog .entry-tags label,
            tags-links a,
            .single-blog .single-blog-nav .prev a, .single-blog .single-blog-nav .next a,
            .single-blog .entry-author .author-description,
            .blog .post .entry-summary,
            .blog .post .read-more-link a,
            .product_categories li,
            .middle-section-wrap a, .middle-section-wrap span, .middle-section-wrap i, .middle-section-wrap div,
            .single-social-simple .single-social-simple-right span,
            .widget_products .product-title a {
                font-size: " . esc_attr(intval($heading_base_size * 0.889)) . "px;
            }
            h1, h2, h3, h4, h5, h6,
            h1 > a, h2 > a, h3 > a, h4 > a, h5 > a, h6 > a,
            .entry-title > a,
            .woocommerce-Reviews .comment-reply-title {
                font-family: " . esc_attr($heading_family) . "; 
                font-weight: " . esc_attr($heading_weight) . ";
                color: " . esc_attr($heading_color) . ";
        ";
        if (in_array("italic", $heading_font_style)) {
            $css .= "
                font-style: italic;
            ";
        }
        if (in_array("underline", $heading_font_style)) {
            $css .= "
                text-decoration: underline;
            ";
        }
        if (in_array("uppercase", $heading_font_style)) {
            $css .= "
                text-transform: uppercase;
            ";
        }
        //TODO after make inline below woocommerce.css remove these !important
        //TODO postMessage font-size .header-top-bar a
        $css .= "
            }
            .service-style9 .image-icon, .service-style9 .aio-icon-img ,
            #faq_print .faq_main .uvc-sub-heading:after,
            .enable .side-wrap .widget-title:before{
                background-color: " .esc_attr($primary_color). ";
            }  
            .cate-content9 .nbfw_banner-container .txt-primary a{
                color: " .esc_attr($heading_color). ";
            }
            .site-header .top-section-wrap {
                padding: " . esc_attr($top_padding) . "px 0;
                background-color: " . esc_attr($top_bg) . ";
            }
            .middle-section-wrap .middle-right-content .customer-action:before,
            .middle-section-wrap .middle-right-content .minicart-header .show-cart:after, .rs-search ul li:hover h4 {
                color: " . esc_attr($top_bg) . ";
            
            }
            .top-section-wrap .nb-header-sub-menu a {
                color: " . esc_attr($top_color) . ";
            }
            .top-section-wrap .header-top-right ul li a:hover i{
            color: " . esc_attr($top_hover_color) . ";
        }
            .top-section-wrap .nb-header-sub-menu .sub-menu, .header-sub-language ul, {
                background-color: " . esc_attr($top_bg) . ";
            }
            .site-header .middle-section-wrap {
                padding: " . esc_attr($middle_padding) . "px 0;
                background-color: " . esc_attr($middle_bg) . ";
            }
            .site-header .bot-section-wrap {
                padding: " . esc_attr($bot_padding) . "px 0;                
            }
            
            .site-header .bot-section-wrap {
                background-color: " . esc_attr($bot_bg) . ";           
            }
            .bot-section-wrap a, .bot-section-wrap span, .bot-section-wrap i, .bot-section-wrap div{
                color: " . esc_attr($bot_color) . ";
            }
            .middle-section-wrap span, .middle-section-wrap i, .middle-section-wrap div,.header-6 .middle-right-content .customer-links a,.customer-action .customer-links a{
                color: " . esc_attr($middle_color) . ";
            }
            .top-section-wrap a, .top-section-wrap span, .top-section-wrap i, .top-section-wrap div{
                color: " . esc_attr($top_color) . ";
            }
            .nb-navbar .menu-item-has-children > a span:after,
            .icon-header-section .nb-cart-section,
            .nb-navbar .menu-item a,
            .nb-navbar .sub-menu > .menu-item:not(:last-child),
            .nb-header-sub-menu .sub-menu > .menu-item:not(:last-child),
            .widget .widget-title,
            .enable .side-wrap,
            .blog .classic .post .entry-footer,
            .single-post .single-blog .entry-footer,
            .nb-social-icons > a,
            .single-blog .entry-author-wrap,
            .shop-main:not(.wide) .single-product-wrap .product_meta,
            .shop-main.accordion-tabs .accordion-item .accordion-title-wrap,
            .shop_table thead th,
            .shop_table th,
            .shop_table td,
            .mini-cart-wrap .total,
            .icon-header-wrap .nb-account-dropdown ul li:not(:last-of-type) a,
            .widget tbody th, .widget tbody td,
            .widget ul > li:not(:last-of-type),
            .blog .post .entry-image .entry-cat,
            .comment-list .comment,
            .nb-comment-form textarea,
            .paging-navigation.pagination-style-1 .page-numbers.current,
            .woocommerce-pagination.pagination-style-1 .page-numbers.current{
                border-color: " . esc_attr($divider_color) . ";
            }
            @media (max-width: 767px) {
                .shop_table.cart {
                    border: 1px solid " . esc_attr($divider_color) . ";
                }
                .shop_table.cart td {
                    border-bottom: 1px solid " . esc_attr($divider_color) . ";
                }
            }
            .product .product-image .onsale,
            .wc-tabs > li.active,
            .product .onsale.sale-style-2 .percent,
            .wc-tabs-wrapper .woocommerce-Reviews #review_form_wrapper .comment-respond,
            .shop-main.accordion-tabs .accordion-item .accordion-title-wrap.ui-accordion-header-active,
            .widget .tagcloud a,
            .footer-top-section .widget .tagcloud a,
            .footer-bot-section .widget .tagcloud a,
            .cart-notice-wrap .cart-notice,
            .vc-tab-product-wrapper ul.style-rounded li.active a,
            .vc-tab-product-wrapper ul.style-rounded li:hover a,
            .vc_testimonial_wrap.testimonial_single_big_thumb .vc-avatar-testimonial .vc-avatar-img img,
            .vc-tab-product-wrapper ul.style-border_bottom li.active a,
            .products .product .nb-loop-variable .nbtcs-swatches .swatch:hover:before,
            .header-7.site-header .customer-action .nb-account-dropdown,
            .products .product .product-action.center .bt-4:hover,
            .products .product .product-action.horizontal_fix_wl .bt-4:hover,
            .nbt-upload-zone .nbt-oupload-target,
            .nb-social-icons a:hover,
            .single-product .nbt-variations .list-variations .swatches-radio li:hover .check,
            .single-product .nbt-variations .list-variations .swatches-radio input[type=radio]:checked ~ .check
            {
                border-color: " . esc_attr($primary_color) . ";
            }
            .vc-getto-info ul li .vc-getto-icon
            {
                border-color: " . esc_attr($border_color_opacity) . ", 0.5);
                background-clip: padding-box;
                -webkit-background-clip: padding-box;
            }
            .products .product .product-action .button .tooltip:before{
                border-top-color:". esc_attr($primary_color) .";
            }
            header.site-header .minicart-header .mini-cart-wrap{
                border-top: 3px solid " . esc_attr($primary_color) . ";
            }
            #faq_print .uvc-main-heading h5,
            .paging-navigation.pagination-style-2 .current,
            .product .onsale.sale-style-1,
            .woocommerce-pagination.pagination-style-2 span.current,
            .shop-main.right-dots .flickity-page-dots .dot,
            .wc-tabs-wrapper .form-submit input,
            .nb-input-group .search-button button,
            .widget .tagcloud a:hover,
            .nb-back-to-top-wrap a:hover,
            .single-product-wrap .yith-wcwl-add-to-wishlist,
            .vc-leadership-info,
            .nb-fw.timeline_simple .timeline-major,
            .hotdeal-content-wrapper .item-product-image .sale-perc,
            .hotdeal-content-wrapper.style2 .counter-wrapper > div,
            .nbfw-carousel .owl-dots .owl-dot.active span,
            .step_number,.step_details:after,
            .product-online-desginer ul li .product-online-desginer-detail > div a h6,
            .vc-tab-product-wrapper ul.style-rounded li.active a,
            .vc-tab-product-wrapper ul.style-rounded li:hover a,
            .info-box-our-services .aio-icon-box:hover,
            .swiper-pagination-bullet-active,
            .products .product .product-action .bt-4:hover,
            .wpt-loading:after,
            .vc-tab-product-wrapper .vc-tab-product-header .product-tab-header.show_heading_line h2:after,
            .products .product .product-action .button .tooltip,
            .product .product-image .wishlist-fixed-btn .yith-wcwl-add-to-wishlist:hover .tooltip,
            .products .product .product-action.center .bt-4:hover,
            .products .product .product-action.horizontal_fix_wl .bt-4:hover,
            .header-8 .middle-section-wrap .middle-right-content .minicart-header .counter-number,
            .vc-getto-info ul li .vc-getto-icon,
            .nbtcs-select a:hover,
            .single-post .nb-page-title-wrap,
            .single-product .nbt-variations .list-variations .swatches-radio input[type=radio]:checked ~ .check::before
            {
                background-color: " . esc_attr($primary_color) . ";
            }
            #service9 .services:hover,
            #video6 .sc-video .sc-video-thumb svg{
                background-color: " . esc_attr($primary_color) . " !important;
            }
            .cate-content9 .nbfw_banner-container .txt-primary a:hover,
            .header-7.site-header .header7-middle .header7-search > i,
            .header-7.site-header .customer-action:before,
            .header-7.site-header .header-cart-wrap .show-cart:after,
            .header-7.site-header .show-cart .price-wrapper .price span,
            .header-custom-list.middle-section-wrap .middle-right-content .customer-action:before,
            .header-custom-list.middle-section-wrap .middle-right-content .minicart-header .show-cart:after,
            .cate-content9 .nbfw_banner-container .txt-primary a:hover,
            .header-6 .middle-right-content .nb-account-dropdown ul li:hover a,
            .header-4 .top-section-wrap .header-top-left-wrapper .textwidget p:before,
            .header-5 .middle-section-wrap .price span,
            .nb-page-title-wrap .nb-page-title .woocommerce-breadcrumb,
            #home5-svicon .wrap-icon:hover .aio-icon-default .aio-icon i,
            .nb-social-icons a:hover i ,
            .related-post-wrap .owl-nav > div:not(.disabled)
             {
                color: " . esc_attr($primary_color) . ";
            }
            #home5-boxbn1 .banner-more:hover,
            #home5-product-onlinedesign h5:hover,
            #home5-video .vc_btn3-container a:hover,
            #home5-blog .vc-home-blog5 .wrap > .art .blog-content .hb-readmore:hover{
                border-top-color:" . esc_attr($primary_color) . " !important;
                border-bottom-color:" . esc_attr($primary_color) . " !important;
            }
            #home5-svicon .wrap-icon:hover .aio-icon-header .aio-icon-title,
            #home5-boxbn1 .banner-more:hover,
            #home5-product-onlinedesign h5:hover a,
            #home5-boxbn2 .banner-more:hover,
            #home5-video .vc_btn3-container a:hover,
            #home5-blog .vc-home-blog5 .wrap > .art .blog-content .hb-readmore:hover
            {
               color: " . esc_attr($primary_color) . " !important;
            }
            .vc-home-blog7 .swiper-button div svg{
                fill: " . esc_attr($primary_color) . ";
            }
            input[type=\"search\"],
            .hotdeal-content-wrapper.style1 .number-wrapper,.step_number .number,.step_box {
                border-color: " . esc_attr($primary_color) . ";
            }
            .step_box:before{
                border-right-color: " . esc_attr($primary_color) . ";
            }
            .product .star-rating:before,
            .product .star-rating span,
            .single-product-wrap .price ins,
            .single-product-wrap .price > span.amount,
            .single_variation_box .price > span.amount,
            .single-product .single-product-wrap .price .nbtwccs_price_code > span.amount,
            .wc-tabs > li.active a,
            .wc-tabs > li.active a:hover,
            .wc-tabs > li.active a:focus,
            .wc-tabs .ui-accordion-header-active a,
            .wc-tabs .ui-accordion-header-active a:focus,
            .wc-tabs .ui-accordion-header-active a:hover,
            .shop-main.accordion-tabs .ui-accordion-header-active:after,
            .shop_table .cart_item td .amount,
            .cart_totals .order-total .amount,
            .shop_table.woocommerce-checkout-review-order-table .order-total .amount,
            .woocommerce-order .woocommerce-thankyou-order-received,
            .woocommerce-order .woocommerce-table--order-details .amount,
            .paging-navigation.pagination-style-1 .current,
            .woocommerce-pagination.pagination-style-1 .page-numbers.current,
            .products .product .price .amount,
            .widget_products .widget-product-meta ins span,
            .widget_products .widget-product-meta > span.amount,
            .shop-main .woocommerce-Reviews #review_form_wrapper .stars a,
            .shop-main .woocommerce-Reviews #review_form_wrapper .stars a:hover,
            .hotdeal-list-wrap .item-product-meta ins span,
            .hotdeal-content-wrapper.style1 .number-wrapper,.step_number .number,
            .nbd-sidebar-con-inner ul li a:hover,
            .vc-tab-product-wrapper ul.style-separated li.active a,
            .vc-tab-product-wrapper ul.style-separated li:hover a,
            .vc-tab-product-wrapper ul.style-classic li.active a,
            .vc-tab-product-wrapper ul.style-classic li:hover a,

            .header-4 .middle-section-wrap .middle-right-content .header-cart-wrap .counter-number,
            .horizontal-step .vc_column-inner .step-title,
            .vc_testimonial_wrap .swiper-button-next:hover:before,
            .vc_testimonial_wrap .swiper-button-prev:hover:before,
            .vc-printshop-ourservices .our-services-box .our-services-child:hover a,
            
            .vc_testimonial_wrap.testimonial_multi_thumb .vc-content-testimonial .vc-testimonial-content p.client-name,
            .vc_testimonial_wrap.testimonial_single_thumb .vc-avatar-testimonial .client-name,
            .vc_testimonial_wrap.testimonial_align_left .vc-avatar-testimonial .client-name, .vc_testimonial_wrap.testimonial_cover_flow .vc-avatar-testimonial .client-name,
            .product .product-image .wishlist-fixed-btn .yith-wcwl-wishlistexistsbrowse .icon-heart, .product .product-image .wishlist-fixed-btn .yith-wcwl-wishlistaddedbrowse .icon-heart,
            .product .product-image .wishlist-fixed-btn .yith-wcwl-add-to-wishlist i.icon-heart:hover,
            .header-9.site-header .wrap9 .header-top-right li:hover span,
            .header-9.site-header .wrap9 .header-top-right li:hover i,
            .shop_table .cart_item td.product-remove .pt-icon-trash:hover,
            .woocommerce-variation-price .nbtwccs_price_code .woocommerce-Price-amount,
            #faq_print .uvc-main-heading h4,
            #faq_print .uvc-heading-spacer .aio-icon i
            {
                color: " . esc_attr($primary_color) . ";                
            }
            .products .product .product-action.horizontal_fix_wl .tooltip:before,
            .header-custom-list .customer-action .nb-account-dropdown {
                border-top-color: " . esc_attr($primary_color) . ";
            }
            .nb-page-title-wrap {
                padding-top: " . esc_attr($page_title_padding) . "px;
                padding-bottom: " . esc_attr($page_title_padding) . "px;            
            }
            .nb-page-title-wrap a, .nb-page-title-wrap h1, .nb-page-title-wrap nav {
                color: " . esc_attr($page_title_color) . ";
            }            
            .nb-page-title-wrap h1 {
                font-size: " . esc_attr($page_title_size) . "px;
            }
            .woocommerce-page.wc-no-sidebar #primary {
                width: 100%;
            }
            .shop-main .products.grid-type .product:nth-child(" . esc_attr($loop_columns) . "n + 1) {
                clear: both;
            }
            
        ";

        if($product_image_border_color != 'rgba(255, 255, 255, 0)') {
            $css .= ".product .product-image{
                border: solid 1px;
                border-color: " . esc_attr($product_image_border_color) . ";
            }";
        }
        $css .= "
            .footer-top-section,
            .ib-shape .icon{                
                background-color: " . esc_attr($footer_top_bg) . ";
            }
            .footer-top-section h1,
            .footer-top-section h2,
            .footer-top-section h3,
            .footer-top-section h4,
            .footer-top-section h5,
            .footer-top-section h6,
            .footer-top-section .widget-title a,
            .ib-shape .icon i{
                color: " . esc_attr($footer_top_color) . ";
            }
            .footer-top-section,
            .footer-top-section a,
            .footer-top-section .widget ul li a,
            .footer-top-section .form input::-webkit-input-placeholder {
                color: " . esc_attr($footer_top_color) . ";
            }
            .footer-top-section .widget .tagcloud a{
                border-color: " . esc_attr($footer_top_color) . ";
            }
            footer.site-footer .footer-bot-section{
                background-color: " . esc_attr($footer_bot_bg) . ";
            }
            .site-footer .footer-bot-section .widget .widget-title,
            footer.site-footer .footer-bot-section .form .submit_b input[type=\"submit\"]{
                color:" . esc_attr($footer_bot_heading) . ";
            }
            .footer-bot-section,
            .footer-bot-section a,
            .footer-bot-section .widget ul li a,
            .footer-bot-section .widget-title,
            footer.site-footer .footer-bot-section .textwidget div,
            .footer-section-top h3,
            footer.site-footer .footer-bot-section .form input[type=\"email\"]::-webkit-input-placeholder{
                color: " . esc_attr($footer_bot_color) . ";
            }
            footer.site-footer .footer-top-section{
                padding-top:".esc_attr($footer_top_padding_top)."px;
                padding-bottom:".esc_attr($footer_top_padding_bottom)."px;
            }
            footer.site-footer .footer-bot-section{
                border-top-color:".esc_attr($footer_top_color_border).";
                border-bottom-color:".esc_attr($footer_bot_color_border).";
            }
            footer.site-footer .footer-bot-section{
                padding-top:".esc_attr($footer_middle_padding_top)."px;
                padding-bottom:".esc_attr($footer_middle_padding_bottom)."px;
            }
            .footer-bot-section .widget .tagcloud a{
                border-color: " . esc_attr($footer_bot_color) . ";
            }
            .site-footer .footer-bot-section .widget .widget-title{
                border-bottom-color:".esc_attr($footer_border_parent).";
            }
            .footer-bot-section .widget-title:after,
            .site-footer .footer-bot-section .widget .widget-title.border_right span{
                background-color: " .esc_attr($footer_color_bottom). "; 
            }
            .site-footer .footer-bot-section .widget ul li a:hover,
            footer.site-footer .footer-bot-section .wrap-content li:hover div{
                color:" . esc_attr( $footer_text_hover ). ";
            }
            .footer-abs-section{
                color: " . esc_attr($footer_abs_color) . ";
                background-color: " . esc_attr($footer_abs_bg) . ";
            }
            .footer-abs-section p ,
            .footer-abs-section p a{
                color: " . esc_attr($footer_abs_color) . ";
            }
            footer.site-footer ul.nbfw-social-link-widget li a{
                color: " .esc_attr( $footer_bot_social ). ";
            }
            footer.site-footer ul.nbfw-social-link-widget li:hover a{
                color: " .esc_attr( $footer_social_media_hover ). ";
            }
            footer.site-footer ul.nbfw-social-link-widget li{
                border-color:" .esc_attr( $footer_bot_social_bor ). ";
            }
            footer.site-footer ul.nbfw-social-link-widget li:hover{
                border-color: " .esc_attr( $footer_social_media_bor_hover ). " !important;    
            }
            footer.site-footer ul.nbfw-social-link-widget li:hover{
                background-color:".esc_attr( $footer_top_color_bg).";
            }
            footer.site-footer .footer-bot-section .widget ul li a,
            footer.site-footer .footer-bot-section .textwidget p,
            footer.site-footer .footer-bot-section .textwidget div{
                font-size: ".esc_attr( $footer_text_fontsize )."px;
            }
            footer.site-footer .footer-bot-section h4.widget-title{
                font-size: ".esc_attr( $footer_head_fontsize )."px !important;
            }
            .single-blog .nb-page-title .entry-title,
            .single-blog .entry-title,
            .page-cover-block h1 {
                font-size: " . esc_attr($blog_title_size) . "px;
            }
            ";
        if ($page_bg_color) {
            $css .= "
                .page #site-wrapper {
                    background-color: " . esc_attr($page_bg_color) . ";
                }
                ";
        }
        if ($page_bg[0]) {
            $css .= "
                .page #site-wrapper {
                    background: url(" . esc_url($page_bg[0]) . ") repeat center center / cover; 
                }
            ";
        }
        $css .= "
            @media (min-width: 576px) {
                .shop-main:not(.wide) .single-product-wrap .product-image {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 " . esc_attr($pd_images_width) . "%;
                    flex: 0 0 " . esc_attr($pd_images_width) . "%;                   
                    max-width: " . esc_attr($pd_images_width) . "%;
                }
                .shop-main:not(.wide) .single-product-wrap .entry-summary {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 calc(100% - " . esc_attr($pd_images_width) . "%);
                    flex: 0 0 calc(100% - " . esc_attr($pd_images_width) . "%);                   
                    max-width: calc(100% - " . esc_attr($pd_images_width) . "%);
                }
            }
            @media (min-width: 992px) {
        ";

        if ('no-sidebar' !== $blog_sidebar) {
            $css .= "            
                .site-content .blog #primary,
                .site-content .single-blog #primary {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 " . esc_attr($blog_width) . "%;
                    flex: 0 0 " . esc_attr($blog_width) . "%;
                    max-width: " . esc_attr($blog_width) . "%;
                } 
                .site-content .blog #secondary,
                .site-content .single-blog #secondary {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 calc(100% - " . esc_attr($blog_width) . "%);
                    flex: 0 0 calc(100% - " . esc_attr($blog_width) . "%);
                    max-width: calc(100% - " . esc_attr($blog_width) . "%);
                }                                  
            ";
        }
        if ('left-sidebar' == $blog_sidebar) {
            $css .= "
                .single-blog #primary, .blog #primary {
                    order: 2;
                }
                .single-blog #secondary, .blog #secondary {
                    padding-right: 15px;
                }
            ";
        } elseif ('right-sidebar' == $blog_sidebar) {
            $css .= "
                .single-blog #secondary, .blog #secondary {
                    padding-left: 15px;
                    padding-right: 15px;
                }
            ";
        }
        if ('left-sidebar' == $shop_sidebar) {
            $css .= "
                .archive.woocommerce .shop-main {
                    order: 2;
                }
                .archive.woocommerce #secondary {
                    padding-right: 15px;
                    padding-left: 15px;
                }
            ";
        } elseif('right-sidebar' == $shop_sidebar) {
            $css .= "
                .archive.woocommerce #secondary {
                    padding-left: 30px;
                    padding-right: 15px;
                }
            ";
        }

        $css .= '.show-cart .price-wrapper *, .mini-cart-section a{
            color: '.$mheader_secondary_color.';
        }
        .minicart-header .mini-cart-wrap, .customer-action .nb-account-dropdown{
            border-top: 3px solid '.$mheader_secondary_color.';
        }';

        if ('left-sidebar' == $pd_details_sidebar) {
            $css .= "
                .single-product .shop-main {
                    order: 2;
                }
                .single-product #secondary {
                    padding-right: 30px;
                }
            ";
        } elseif('right-sidebar' == $pd_details_sidebar) {
            $css .= "
                .single-product #secondary {
                    padding-left: 30px;
                }
            ";
        }


        if($sidebar_style == 'sidebar-style-2') {
            $css .= '.sidebar-wrapper {
                background-color: '.$inner_bg.';
                padding: 25px 15px 30px;
            }';
        }else{
            $css .= '.sidebar-wrapper {
                padding: 0;
                background-color: transparent;
            }';
        }

        if($nbcore_blog_sidebar_style == 'sidebar-style-2' ){
            $css .= '.single .sidebar-wrapper {
                background-color: '.$inner_bg.';
                padding: 25px 15px 30px;
            }';
        }else{
            $css .= '.single .sidebar-wrapper {
                padding: 0;
                background-color: transparent;
            }';
        }

        if ('no-sidebar' !== $pd_details_sidebar) {
            $css .= "
                .single-product.wc-pd-has-sidebar .shop-main {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 " . esc_attr($pd_details_width) . "%;
                    flex: 0 0 " . esc_attr($pd_details_width) . "%;
                    max-width: " . esc_attr($pd_details_width) . "%;
                }
                .single-product #secondary {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 calc(100% - " . esc_attr($pd_details_width) . "%);
                    flex: 0 0 calc(100% - " . esc_attr($pd_details_width) . "%);
                    max-width: calc(100% - " . esc_attr($pd_details_width) . "%);
                }
            ";
        }
        // TODO check this for tag ... ?
        if ('no-sidebar' !== $shop_sidebar) {
            $css .= "
                .archive.woocommerce.wc-left-sidebar .shop-main,
                .archive.woocommerce.wc-right-sidebar .shop-main {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 " . esc_attr($wc_content_width) . "%;
                    flex: 0 0 " . esc_attr($wc_content_width) . "%;
                    max-width: " . esc_attr($wc_content_width) . "%;
                }
                .archive.woocommerce.wc-left-sidebar #secondary,
                .archive.woocommerce.wc-right-sidebar #secondary {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 calc(100% - " . esc_attr($wc_content_width) . "%);
                    flex: 0 0 calc(100% - " . esc_attr($wc_content_width) . "%);
                    max-width: calc(100% - " . esc_attr($wc_content_width) . "%);
                }
            ";
        } else {
            $css .= "
                .site-content .shop-main {
                    -webkit-box-flex: 0;
                    -ms-flex: 0 0 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
                }
            ";
        }
        if( $nbcore_page_404_bg ) {
            $css .= "
                .error404 main .home-link .icon-home{
                    background-color: " . esc_attr($nbcore_page_404_bg) . ";
                }
                .error404 main .home-link a{
                    color: " . esc_attr($nbcore_page_404_text) . ";
                }
            ";
        }

        $css .= "
            }
        ";

        return apply_filters('printcart_css_inline', $css);
    }

    public function print_embed_style()
    {
        $style = $this->get_embed_style();

        $style = preg_replace('#/\*.*?\*/#s', '', $style);
        $style = preg_replace('/\s*([{}|:;,])\s+/', '$1', $style);
        $style = preg_replace('/\s\s+(.*)/', '$1', $style);
        
        wp_add_inline_style('printcart_woo_style', $style);
    }

    public function filter_fonts($font)
    {
        $font_args = explode(",", printcart_get_options($font));
        if($font_args[0] === 'google') {
            $this->handle_google_font($font_args[1]);
        } elseif($font_args[0] === 'custom') {
            $this->handle_custom_font($font_args[1]);
        } elseif($font_args[0] === 'standard') {
            $this->handle_standard_font($font_args[1]);
        }
    }

    public function handle_google_font($font_name)
    {
        $font_subset = 'latin,latin-ext';
        $font_families = array();
        $google_fonts = NBT_Helper::google_fonts();
        $font_parse = array();


        $font_weight = $google_fonts[$font_name];
        $font_families[$font_name] = isset($font_families[$font_name]) ? array_unique(array_merge($font_families[$font_name], $font_weight)) : $font_weight;

        foreach ($font_families as $font => $font_weight) {
            $font_parse[] = $font . ':' . implode(',', $font_weight);
        }

        if (printcart_get_options('subset_cyrillic')) {
            $font_subset .= ',cyrillic,cyrillic-ext';
        }
        if (printcart_get_options('subset_greek')) {
            $font_subset .= ',greek,greek-ext';
        }
        if (printcart_get_options('subset_vietnamese')) {
            $font_subset .= ',vietnamese';
        }

        $query_args = array(
            'family' => urldecode(implode('|', $font_parse)),
            'subset' => urldecode($font_subset),
        );

        $font_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');

        $enqueue = esc_url_raw($font_url);

        wp_enqueue_style('nb-google-fonts', $enqueue);
    }

    public function google_fonts_url()
    {
        $gg_font_arr = array();
        $gg_font_parse = array();
        $google_fonts = NBT_Helper::google_fonts();
        $gg_subset = 'latin,latin-ext';

        $body_font = explode(',', printcart_get_options('body_font_family'));
        $heading_font = explode(',', printcart_get_options('heading_font_family'));

        if($body_font[0] === 'google') {
            $body_name = $body_font[1];
            $body_weight = $google_fonts[$body_name];
            $gg_font_arr[$body_name] = isset($gg_font_arr[$body_name]) ? array_unique(array_merge($gg_font_arr[$body_name], $body_weight)) : $body_weight;
        }

        if($heading_font[0] === 'google') {
            $heading_name = $heading_font[1];
            $heading_weight = $google_fonts[$heading_name];
            $gg_font_arr[$heading_name] = isset($gg_font_arr[$heading_name]) ? array_unique(array_merge($gg_font_arr[$heading_name], $heading_weight)) : $heading_weight;
        }

        if(!empty($gg_font_arr)) {
            foreach ($gg_font_arr as $gg_font_name => $gg_font_weight) {
                $gg_font_parse[] = $gg_font_name . ':' . implode(',', $gg_font_weight);
            }

            if (printcart_get_options('subset_cyrillic')) {
                $gg_subset .= ',cyrillic,cyrillic-ext';
            }
            if (printcart_get_options('subset_greek')) {
                $gg_subset .= ',greek,greek-ext';
            }
            if (printcart_get_options('subset_vietnamese')) {
                $gg_subset .= ',vietnamese';
            }

            $query_args = array(
                'family' => urldecode(implode('|', $gg_font_parse)),
                'subset' => urldecode($gg_subset),
            );

            $font_url = add_query_arg($query_args, 'https://fonts.googleapis.com/css');

            $enqueue = esc_url_raw($font_url);

            wp_enqueue_style('nb-google-fonts', $enqueue);
        }
    }

    public function upload_mimes($t)
    {
        // Add supported font extensions and MIME types.
        $t['eot'] = 'application/vnd.ms-fontobject';
        $t['otf'] = 'application/x-font-opentype';
        $t['ttf'] = 'application/x-font-ttf';
        $t['woff'] = 'application/font-woff';
        $t['woff2'] = 'application/font-woff2';

        return $t;
    }

    public function comment_form_fields($fields)
    {
        unset($fields['author']);
        unset($fields['email']);
        unset($fields['url']);

        $req = $html5 = true;
        $aria_req = 'required';

        $fields['author'] = '<p class="comment-form-author"><input id="author" name="author" type="text" value="" size="30" placeholder="' . esc_html__( 'Name', 'printcart' ) . ( $req ? ' *' : '' ) . '" ' . $aria_req . ' /></p>';            
        $fields['email']  = '<p class="comment-form-email"><input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="" size="30" placeholder="' . esc_html__( 'Email', 'printcart' ) . ( $req ? ' *' : '' ) . '" ' . $aria_req . ' /></p>';
        $fields['url']  = '<p class="comment-form-url"><input id="url" name="url" ' . ( $html5 ? 'type="email"' : 'type="url"' ) . ' value="" size="30" placeholder="' . esc_html__( 'Website', 'printcart' ) . ( $req ? ' *' : '' ) . '" ' . $aria_req . ' /></p>';

        return $fields;
    }

    /**
     * Flush out the transients used in printcart_categorized_blog.
     */
    public function category_transient_flusher()
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        delete_transient('nbcore_categories');
    }
}

new NBT_Core();