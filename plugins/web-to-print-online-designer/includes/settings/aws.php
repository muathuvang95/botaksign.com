<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!class_exists('Nbdesigner_Settings_Aws')){
    class Nbdesigner_Settings_Aws {
        public static function get_options() {
            return apply_filters('nbdesigner_aws_settings', array(
                'aws-settings' => array(
                    array(
                        'title'         => esc_html__( 'Region', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your region.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_aws_region',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => ''
                    ),
                    array(
                        'title'         => esc_html__( 'Bucket', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your upload bucket.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_aws_bucket',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => ''
                    ),
                    array(
                        'title'         => esc_html__( 'Access Key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your AWS access key.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_aws_access_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => ''
                    ),
                    array(
                        'title'         => esc_html__( 'Secret Key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your AWS secret key.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_aws_secret_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'password',
                        'placeholder'   => ''
                    ),
                ),
            ));
        }
    }
}