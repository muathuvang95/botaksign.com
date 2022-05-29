<?php

class NBD_Aws_Setting {
    static function add_setting_aws($setting) {
        $setting['aws'] = '<span class="dashicons dashicons-post-status"></span> '. esc_html__('AWS setting', 'web-to-print-online-designer');
        return $setting;
    }
    
    static function add_settings_blocks($block) {
        $block['aws'] = array(
            'aws-settings'      => esc_html__('AWS Settings', 'web-to-print-online-designer'),
        );
        
        return $block;
    }
    
    static function add_settings_options($options) {
        $payment_options = Nbdesigner_Settings_Aws::get_options();
        $options['aws-settings'] = $payment_options['aws-settings'];
        
        return $options;
    }
}
