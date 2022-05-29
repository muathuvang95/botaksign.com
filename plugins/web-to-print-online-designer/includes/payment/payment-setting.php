<?php

class NBD_Payment_Setting {
    static function add_setting_payment($setting) {
        $setting['payment'] = '<span class="dashicons dashicons-tickets-alt"></span> '. esc_html__('Payment', 'web-to-print-online-designer');
        return $setting;
    }
    
    static function add_settings_blocks($block) {
        $block['payment'] = array(
            'stripe-settings'      => esc_html__('Stripe Settings', 'web-to-print-online-designer'),
            'paypal-settings'      => esc_html__('Paypal Settings', 'web-to-print-online-designer'),
        );
        
        return $block;
    }
    
    static function add_settings_options($options) {
        $payment_options = Nbdesigner_Settings_Payment::get_options();
        $options['stripe-settings'] = $payment_options['stripe-settings'];
        $options['paypal-settings'] = $payment_options['paypal-settings'];
        
        return $options;
    }
}

