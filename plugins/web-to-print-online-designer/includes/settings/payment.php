<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!class_exists('Nbdesigner_Settings_Payment')){
    class Nbdesigner_Settings_Payment {
        public static function get_options() {
            return apply_filters('nbdesigner_payment_settings', array(
                'paypal-settings' => array(
                    array(
                        'title'         => esc_html__('Enable PayPal sandbox', 'web-to-print-online-designer'),
                        'description'   => esc_html__('PayPal sandbox can be used to test payments.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_paypal_enable_sanbox',
                        'default'       => 'no',
                        'type'          => 'checkbox'
                    ),
                    array(
                        'title'         => esc_html__( 'Client ID', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your client ID of your paypal app.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_paypal_cliend_id',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => ''
                    ),
                    array(
                        'title'         => esc_html__( 'Secret Key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your secret key of your paypal app.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_paypal_secret_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'password',
                        'placeholder'   => ''
                    ),
                ),
                'stripe-settings' => array(   
                    array(
                        'title'         => esc_html__( 'Publishable key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your publishable key of your stripe API.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_stripe_publishable_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => ''
                    ),
                    array(
                        'title'         => esc_html__( 'Secret Key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your secret key of your stripe API.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_stripe_secret_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'password',
                        'placeholder'   => ''
                    ),
                )
            ));
        }
    }
}