<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Settings_Output') ) {    
    class Nbdesigner_Settings_Output{
        public static function get_options() {
            return apply_filters('nbdesigner_output_settings', array(
                'output-settings' => array(
                    array(
                        'title'         => esc_html__( 'Watermark', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_enable_pdf_watermark',
                        'description' 	=> esc_html__('Enable watermark if allow customer download PDFs', 'web-to-print-online-designer'),
                        'default'	=> 'yes',
                        'type' 		=> 'radio',
                        'options'       => array(
                            'yes' => esc_html__('Always', 'web-to-print-online-designer'),
                            'before' => esc_html__('Before complete order', 'web-to-print-online-designer'),
                            'no' => esc_html__('No', 'web-to-print-online-designer')
                        )                      
                    ),
                    array(
                        'title'         => esc_html__( 'Watermark type', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_pdf_watermark_type',
                        'default'	=> '2',
                        'type' 		=> 'radio',
                        'options'       => array(
                            '1' => esc_html__('Image', 'web-to-print-online-designer'),
                            '2' => esc_html__('Text', 'web-to-print-online-designer')
                        )                     
                    ),
                    array(
                        'title'         => esc_html__( 'Watermark image', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_pdf_watermark_image',
                        'description' 	=> esc_html__('Choose a watermark image', 'web-to-print-online-designer'),
                        'default'	=> '',
                        'type' 		=> 'nbd-media'                      
                    ),
                    array(
                        'title'         => esc_html__( 'Watermark text', 'web-to-print-online-designer'),
                        'description' 	=> esc_html__( 'Branded watermark text', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_pdf_watermark_text',
                        'class'         => 'regular-text',
                        'default'	=> get_bloginfo('name'),
                        'type' 		=> 'text'
                    ), 
                    array(
                        'title'         => esc_html__( 'Enable PDF password for customer', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_enable_pdf_password',
                        'description' 	=> esc_html__('Enable PDF protected password for customer when they download PDF file from editor.', 'web-to-print-online-designer'),
                        'default'	=> 'no',
                        'type' 		=> 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )                      
                    ),
                    array(
                        'title'         => esc_html__( 'PDF password', 'web-to-print-online-designer'),
                        'description' 	=> esc_html__( 'PDF password to edit file', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_pdf_password',
                        'class'         => 'regular-text',
                        'default'	=> '',
                        'type' 		=> 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Show bleed', 'web-to-print-online-designer'),
                        'description' 	=> esc_html__( 'If the product include bleed line, show it below/above the content design.', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_bleed_stack',
                        'default'	=> '1',
                        'type' 		=> 'radio',
                        'options'       => array(
                            '1' => esc_html__('Below the content design.', 'web-to-print-online-designer'),
                            '2' => esc_html__('Above the content design.', 'web-to-print-online-designer')
                        )                     
                    ),
                    array(
                        'title'         => esc_html__( 'Truetype fonts', 'web-to-print-online-designer'),
                        'description' 	=> esc_html__( 'Each font in a separate line', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_truetype_fonts',
                        'class'         => 'regular-text',
                        'placeholder'   => 'Abel&#x0a;Abril Fatface&#x0a;Aguafina Script',
                        'css'           =>  'height: 10em;',
                        'default'	=> '',
                        'type' 		=> 'textarea'
                    )
                ),
                'jpeg-settings' => array(
                    array(
                        'title'         => esc_html__( 'Default ICC profile', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_default_icc_profile',
                        'description' 	=> __('Set default ICC profile for jpg image. <br/><b>This feature require your server support Imagemagick with lcms2.</b>', 'web-to-print-online-designer'),
                        'type' 		=> 'select',
                        'default'	=> 1,
                        'options'       =>  nbd_get_icc_cmyk_list()  
                    )
                )
            ));
        }
    }
}