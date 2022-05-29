<?php

class NBT_Admin
{
    protected $plugins;

    protected $tgmpa;

    protected $package;

    public function __construct()
    {
        $this->tgmpa = isset($GLOBALS['tgmpa']) ? $GLOBALS['tgmpa'] : TGM_Plugin_Activation::get_instance();

        add_action('admin_enqueue_scripts', array($this, 'admin_scripts_enqueue'));
        add_action( 'tgmpa_register', array($this, 'register_required_plugins') );

        $this->package = wp_get_theme(get_template())->get('Tags');
        
    }

    public function admin_scripts_enqueue()
    {
        if(is_customize_preview()){
            wp_enqueue_style('fontello-admin', get_template_directory_uri() . '/assets/vendor/fontello/fontello.css', array(), NBT_VER);
        }
    }


    public function register_required_plugins()
    {

        $required = array(
            array(
                'name'              => 'Woocommerce',
                'slug'              => 'woocommerce',
                'required'          => true,
                'version'           => '3.8.0',
            ),
            array(
                'name'              => 'Netbase Framework',
                'slug'              => 'nb-fw',
                'required'          => true,
                'version'           => '1.4.5',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/nbfw-no-merlin/nb-fw.zip'),
            ),
            array(
                'name'              => 'Slider Revolution',
                'slug'              => 'revslider',
                'required'          => false,
                'version'           => '6.1.5',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/revslider.zip'),
            ),
            array(
                'name'              => 'WPBakery Visual Composer',
                'slug'              => 'js_composer',
                'required'          => true,
                'version'           => '6.0.5',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/newvc/js_composer.zip'),
            ),
            array(
                'name'              => 'Ultimate Addons for Visual Composer',
                'slug'              => 'Ultimate_VC_Addons',
                'required'          => true,
                'version'           => '3.19.0',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/Ultimate_VC_Addons.zip'),
            ),
            array(
                'name'              => 'Netbase Elements',
                'slug'              => 'nb-elements',
                'required'          => true,
                'version'           => '1.2.3',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/nb-elements.zip'),
            ),
            array(
                'name'              => 'Netbase Solutions',
                'slug'              => 'netbase_solutions',
                'required'          => true,
                'version'           => '1.9.2',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase_solutions.zip'),
            ),
            array(
                'name'              => 'WooPanel',
                'slug'              => 'woopanel',
                'required'          => false,
                'version'           => '2.2.0',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/woopanel.zip'),
            ),
            array(
                'name'              => 'Max Mega Menu',
                'slug'              => 'megamenu',
                'required'          => true,
                'version'           => '2.7.2',
            ),
            array(
                'name'              => 'YITH WooCommerce Compare',
                'slug'              => 'yith-woocommerce-compare',
                'required'          => true,
                'version'           => '2.3.7',
            ),
            array(
                'name'              => 'YITH WooCommerce Wishlist',
                'slug'              => 'yith-woocommerce-wishlist',
                'required'          => true,
                'version'           => '2.2.5',
            ),
            array(
                'name'              => 'YITH WooCommerce Quick View',
                'slug'              => 'yith-woocommerce-quick-view',
                'required'          => true,
                'version'           => '1.3.6',
            ),
            array(
                'name'              => 'Contact Form 7',
                'slug'              => 'contact-form-7',
                'required'          => false,
                'version'           => '5.1',
            ),
            array(
                'name'              => 'MailChimp for WordPress',
                'slug'              => 'mailchimp-for-wp',
                'required'          => false,
                'version'           => '4.3.2',
            )
        );
        
        $advance = array(
            array(
                'name'              => 'Order Delivery Date for WooCommerce',
                'slug'              => 'order-delivery-date-for-woocommerce',
                'required'          => false,
                'version'           => '3.6',
            ),
            array(
                'name'              => 'WooCommerce Coupon Generator',
                'slug'              => 'coupon-generator-for-woocommerce',
                'required'          => false,
                'version'           => '1.0.1',
            ),
            array(
                'name'              => 'Yoast SEO',
                'slug'              => 'wordpress-seo',
                'required'          => false,
                'version'           => '9.2.1',
            ),
            array(
                'name'              => 'Popup Maker – Popup Forms, Optins & More',
                'slug'              => 'popup-maker',
                'required'          => true,
                'version'           => '1.7.30',
            ),
            array(
                'name'              => 'Netbase Dashboard',
                'slug'              => 'netbase_dashboard',
                'required'          => false,
                'version'           => '1.2.2',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase_dashboard.zip'),
            ),
            array(
                'name'              => 'ThirstyAffiliates',
                'slug'              => 'thirstyaffiliates',
                'required'          => false,
                'version'           => '3.4',
            )
        );
        
        $premium = array(
            array(
                'name'              => 'Nbdesigner',
                'slug'              => 'web-to-print-online-designer',
                'required'          => true,
                'version'           => '2.6.0',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/web-to-print-online-designer.zip'),
            ),
        );
        
        $enterprise = array(
            array(
                'name'              => 'Dokan',
                'slug'              => 'dokan-lite',
                'required'          => true,
                'version'           => '2.9.26',
            ),
            array(
                'name'              => 'Dokan Pro',
                'slug'              => 'dokan-pro',
                'required'          => true,
                'version'           => '2.9.15',
                'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/dokan-pro.zip'),
            )
        );
        
        $this->plugins = $required;
        
        if( in_array('nb-advanced', $this->package) ) {
            $this->plugins = array_merge($required, $advance);
        }
        
        if( in_array('nb-premium', $this->package) ) {
            $this->plugins = array_merge($required, $advance, $premium);
        }
        
        if( in_array('nb-enterprise', $this->package) ) {
            $this->plugins = array_merge($required, $advance, $premium, $enterprise);
        }


        $config = array(
            'id'           => 'core-wp',                 // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins', // Menu slug.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false,                   // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.
        );

        tgmpa( $this->plugins, $config );
    }
}