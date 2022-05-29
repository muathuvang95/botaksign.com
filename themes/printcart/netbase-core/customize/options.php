<?php

class NBT_Customize_Options
{
    public function footer()
    {
        return array(
            'title' => esc_html__('Footer', 'printcart'),
            'priority' => 17,
            'options' => array(
                'nbcore_footer_logo_upload' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_file_image')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Site Logo footer', 'printcart'),
                        'description' => esc_html__('If you don\'t upload logo image, your site\'s logo will be the Site Title ', 'printcart'),
                        'type' => 'WP_Customize_Upload_Control'
                    ),
                ),
                'nbcore_footer_top_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Footer top section', 'printcart'),
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_show_footer_top' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show footer top', 'printcart'),
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_footer_top_padding_top' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Padding top', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '100',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_footer_top_padding_bottom' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Padding bottom', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '100',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_footer_title' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Footer title', 'printcart'),
                        'type' => 'textarea',
                    ),
                ),
                'nbcore_footer_phone' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Your phone', 'printcart'),
                        'type' => 'text',
                    ),
                ),
                'nbcore_footer_email' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Your email', 'printcart'),
                        'type' => 'text',
                    ),
                ),
                'nbcore_footer_address' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Your Address', 'printcart'),
                        'type' => 'text',
                    ),
                ),
                'nbcore_footer_cap' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Newsletter caption', 'printcart'),
                        'type' => 'text',
                    ),
                ),
                'nbcore_footer_top_layout' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Columns', 'printcart'),
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'layout-1' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-2.png',
                            'layout-2' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-3.png',
                        ),
                    ),
                ),
                'nbcore_footer_bot_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Footer middle section', 'printcart'),
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_show_footer_bot' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show footer middle', 'printcart'),
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_footer_bot_layout' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Columns', 'printcart'),
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'layout-1' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-1.png',
                            'layout-2' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-2.png',
                            'layout-3' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-3.png',
                            'layout-4' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-4.png',
                            'layout-5' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-5.png',
                            'layout-6' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-6.png',
                            'layout-7' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-7.png',
                            'layout-8' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-8.png',
                            'layout-9' => get_template_directory_uri() . '/assets/netbase/images/options/footers/footer-9.png',
                        ),
                    ),
                ),
                'nbcore_footer_head_fontsize' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Font size head', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '8',
                            'max' => '30',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_footer_heading_up' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Heading uppercase', 'printcart'),
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_footer_list_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('List style type switch', 'printcart'),
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_footer_text_fontsize' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Font size text', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '8',
                            'max' => '30',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_footer_middle_paddingtop' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Padding Top', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '100',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_footer_middle_paddingbottom' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Padding Bottom', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '100',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_footer_abs_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Footer bottom section', 'printcart'),
                        'description' => esc_html__('These area take text and HTML code for its content', 'printcart'),
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_footer_abs_left_content' => array(
                    'settings' => array(
                        // 'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Left content', 'printcart'),
                        'type' => 'textarea',
                    ),
                ),
                'nbcore_footer_abs_right_content' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_file_image')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Site Logo footer', 'printcart'),
                        'description' => esc_html__('Pick your image footer bottom ', 'printcart'),
                        'type' => 'WP_Customize_Upload_Control'
                    ),
                ),
                'nbcore_footer_color_focus' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'type' => 'NBT_Customize_Control_Focus',
                        'choices' => array(
                            'footer_colors' => esc_html__('Edit color', 'printcart'),
                        ),
                    ),
                ),
            ),
        );
    }

    public function blog()
    {
        return array(
            'title' => esc_html__('Blog', 'printcart'),
            'priority' => 16,
            'sections' => array(
                'blog_general' => array(
                    'title' => esc_html__('General', 'printcart')
                ),
                'blog_archive' => array(
                    'title' => esc_html__('Blog Archive', 'printcart'),
                ),
                'blog_single' => array(
                    'title' => esc_html__('Blog Single', 'printcart')
                ),
            ),
            'options' => array(
                'nbcore_blog_layout_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Layout', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_blog_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Sidebar position', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'left-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/2cl.png',
                            'no-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/1c.png',
                            'right-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/2cr.png',
                        ),
                    ),
                ),
                'nbcore_blog_width' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Blog width', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => '%',
                            'min' => '60',
                            'max' => '80',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_blog_meta_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Post meta', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_blog_meta_date' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show date', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_meta_read_time' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show time to read', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_meta_author' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show author', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_meta_category' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show categories', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_meta_tag' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Tags', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_other_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Other', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_blog_sticky_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Sticky sidebar', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_style_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Style sidebar', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_display_swipper' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Display blog swipper', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_collapse_post' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Collapse post', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_meta_align' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Meta align', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'left' => get_template_directory_uri() . '/assets/netbase/images/options/meta-left.png',
                            'center' =>get_template_directory_uri() . '/assets/netbase/images/options/meta-center.png',
                            'right' => get_template_directory_uri() . '/assets/netbase/images/options/meta-right.png',
                        ),
                    ),
                ),
                'nbcore_blog_sidebar_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Style', 'printcart'),
                        'section' => 'blog_general',
                        'type' => 'select',
                        'choices' => array(
                            'sidebar-style-1' => esc_html__('Style 1','printcart'),
                            'sidebar-style-2' => esc_html__('Style 2','printcart'),
                        ),
                    ),
                ),
                'nbcore_blog_archive_layout' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Blog Archive Layout', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'classic' => get_template_directory_uri() . '/assets/netbase/images/options/classic.png',
                            'masonry' => get_template_directory_uri() . '/assets/netbase/images/options/modern.jpg',
                            'blogs' => get_template_directory_uri() . '/assets/netbase/images/options/classic.png',
                            'layout' => get_template_directory_uri() . '/assets/netbase/images/options/masonry.png',
                        ),
                    ),
                ),
                'nbcore_blog_masonry_columns' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Masonry Columns', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Select',
                        'choices' => array(
                            '2' => esc_html__('2', 'printcart'),
                            '3' => esc_html__('3', 'printcart'),
                        ),
                        'condition' => array(
                            'element' => 'nbcore_blog_archive_layout',
                            'value'   => 'masonry',
                        ),
                    ),
                ),
                'nbcore_blog_layout_columns' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Layout Columns', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Select',
                        'choices' => array(
                            '1' => esc_html__('1', 'printcart'),
                            '2' => esc_html__('2', 'printcart'),
                            '3' => esc_html__('3', 'printcart'),
                        ),
                        'condition' => array(
                            'element' => 'nbcore_blog_archive_layout',
                            'value'   => 'layout',
                        ),
                    ),
                ),
                'nbcore_blog_archive_summary' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Post summary', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_excerpt_only' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Excerpt Only', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_excerpt_length' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Excerpt Length', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'min' => '20',
                            'max' => '100',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_blog_archive_comments' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Comments number', 'printcart'),
                        'section' => 'blog_archive',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_single_title_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Post title', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_blog_single_title_positions' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Post title style', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'position-1' => get_template_directory_uri() . '/assets/netbase/images/options/post-title-1.png',
                            'position-2' => get_template_directory_uri() . '/assets/netbase/images/options/post-title-2.png',
                        ),
                    ),
                ),
                'nbcore_blog_single_cover' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_file_image')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Blog single cover', 'printcart'),
                        'section' => 'blog_single',
                        'description' => esc_html__('We recommend this image\'s to be 300px minimum', 'printcart'),
                        'type' => 'WP_Customize_Upload_Control'
                    ),
                ),
                'nbcore_blog_single_title_size' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Font size', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '16',
                            'max' => '70',
                            'step' => '1',
                        ),
                    ),
                ),
                'nbcore_blog_single_layout_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Layout', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_blog_single_show_thumb' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('featured thumbnail', 'printcart'),
                        'description' => esc_html__('Show featured thumbnail of this post on top of its content', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Switch',
                    )
                ),
                'nbcore_blog_single_show_social' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show social button', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_single_show_author' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show author info', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_single_show_nav' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show post navigation', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_blog_single_show_comments' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show post comments', 'printcart'),
                        'section' => 'blog_single',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
            ),
        );
    }

    public function color()
    {
        return array(
            'title' => esc_html__('Color', 'printcart'),
            'priority' => 13,
            'sections' => array(
                'general_color' => array(
                    'title' => esc_html__('General', 'printcart')
                ),
                'type_color' => array(
                    'title' => esc_html__('Type', 'printcart')
                ),
                'header_colors' => array(
                    'title' => esc_html__('Header', 'printcart')
                ),
                'footer_colors' => array(
                    'title' => esc_html__('Footer', 'printcart')
                ),
                'button_colors' => array(
                    'title' => esc_html__('Buttons', 'printcart')
                ),
                'other_colors' => array(
                    'title' => esc_html__('Other', 'printcart')
                ),
            ),
            'options' => array(
                'nbcore_main_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Main Colors', 'printcart'),
                        'section' => 'general_color',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_primary_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Primary Color', 'printcart'),
                        'section' => 'general_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_secondary_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Secondary Color', 'printcart'),
                        'section' => 'general_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_background_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Background', 'printcart'),
                        'section' => 'general_color',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_background_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Site Background Color', 'printcart'),
                        'section' => 'general_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_inner_background' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Inner Background Color', 'printcart'),
                        'section' => 'general_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_text_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Text', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_heading_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Heading Color', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_body_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Body Color', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_link_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Link', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_link_color' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Link Color', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_link_hover_color' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Link Hover Color', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_divider_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Divider', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_divider_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Divider Color', 'printcart'),
                        'section' => 'type_color',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header Top', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_header_top_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('background color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_right_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color right', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_left_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color left', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_left_title_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color right title', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_hovercolor' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text hover color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_top_border' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_middle_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header Middle', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_header_middle_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('background color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_middle_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_secondary_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Secondary color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_bottom_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header Bottom', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_header_bot_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('background color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_bot_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_mainmn_colors_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Main Menu', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_header_mainmn_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_header_mainmnhover_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text hover', 'printcart'),
                        'section' => 'header_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                
                'nbcore_footer_top_color_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Footer top', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_footer_top_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_top_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_bot_color_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Footer Middle', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_footer_bot_heading' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Heading color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_border_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border color child', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_border_color_parent' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border color parent', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_text_color_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text hover color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_bot_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_bot_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_top_color_border' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border top color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_bot_color_border' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border bottom color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_abs_color_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Footer Bottom', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_footer_abs_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                
                'nbcore_footer_abs_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_social_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Social setting', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_footer_social_media' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Social media color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_social_media_border' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border social media color', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_social_media_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Social media color hover', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_social_media_bg_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Social media color border hover', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_footer_social_media_bor_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Social media color border hover', 'printcart'),
                        'section' => 'footer_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_pb_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Primary button', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_pb_background' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_pb_background_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background Hover', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_pb_text' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_pb_text_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text hover', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_pb_border' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_pb_border_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border hover', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_sb_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Secondary button', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_sb_background' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_sb_background_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background Hover', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_sb_text' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_sb_text_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text hover', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_sb_border' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_sb_border_hover' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border hover', 'printcart'),
                        'section' => 'button_colors',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),
                'nbcore_page_title_color_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Page title', 'printcart'),
                        'section' => 'other_colors',
                        'type' => 'NBT_Customize_Control_Heading'
                    ),
                ),
                'nbcore_page_title_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text color', 'printcart'),
                        'section' => 'other_colors',
                        'type' => 'NBT_Customize_Control_Color'
                    ),
                ),
                'nbcore_page_404_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('404 Page', 'printcart'),
                        'section' => 'other_colors',
                        'type' => 'NBT_Customize_Control_Heading'
                    ),
                ),
                'nbcore_page_404_bg' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Background of Button', 'printcart'),
                        'section' => 'other_colors',
                        'type' => 'NBT_Customize_Control_Color'
                    ),
                ),
                'nbcore_page_404_text' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Text of Button', 'printcart'),
                        'section' => 'other_colors',
                        'type' => 'NBT_Customize_Control_Color'
                    ),
                ),
            ),
        );
    }

    public function elements()
    {
        return array(
            'title' => esc_html__('Elements', 'printcart'),
            'priority' => 12,
            'sections' => array(
                'title_section_element' => array(
                    'title' => esc_html__('Title Section', 'printcart')
                ),
                'button_element' => array(
                    'title' => esc_html__('Button', 'printcart')
                ),
                'sidebar_element' => array(
                    'title' => esc_html__('Sidebar', 'printcart')
                ),
                'share_buttons_element' => array(
                    'title' => esc_html__('Social Share', 'printcart')
                ),
                'pagination_element' => array(
                    'title' => esc_html__('Pagination', 'printcart')
                ),
                'back_top_element' => array(
                    'title' => esc_html__('Back to Top Button', 'printcart')
                ),
            ),
            'options' => array(
                'sidebar_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Style', 'printcart'),
                        'section' => 'sidebar_element',
                        'type' => 'select',
                        'choices' => array(
                            'sidebar-style-1' => esc_html__('Style 1','printcart'),
                            'sidebar-style-2' => esc_html__('Style 2','printcart'),
                        ),
                    ),
                ),
                'show_title_section' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show title section', 'printcart'),
                        'section' => 'title_section_element',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'home_page_title_section' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Homepage title', 'printcart'),
                        'description' => esc_html__('Turn this off to not display the title section for only homepage', 'printcart'),
                        'section' => 'title_section_element',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_page_title_size' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Font size', 'printcart'),
                        'section' => 'title_section_element',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '16',
                            'max' => '70',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_page_title_padding' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Padding top and bottom', 'printcart'),
                        'section' => 'title_section_element',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '15',
                            'max' => '105',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_page_title_image' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_file_image')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Image Background', 'printcart'),
                        'section' => 'title_section_element',
                        'type' => 'WP_Customize_Cropped_Image_Control',
                        'flex_width'  => true,
                        'flex_height' => true,
                        'width' => 2000,
                        'height' => 1000,
                    ),
                ),
                'nbcore_page_title_color_focus' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'section' => 'title_section_element',
                        'type' => 'NBT_Customize_Control_Focus',
                        'choices' => array(
                            'other_colors' => esc_html__('Edit color', 'printcart')
                        ),
                    ),
                ),
                'nbcore_button_padding' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Padding left & right', 'printcart'),
                        'section' => 'button_element',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '5',
                            'max' => '60',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_button_border_radius' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border Radius', 'printcart'),
                        'section' => 'button_element',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '50',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_button_border_width' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Border Width', 'printcart'),
                        'section' => 'button_element',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '1',
                            'max' => '10',
                            'step' => '1'
                        ),
                    ),
                ),
                'share_buttons_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Style','printcart'),
                        'section' => 'share_buttons_element',
                        'type' => 'select',
                        'choices' => array(
                            'style-1' => esc_html__('Style 1', 'printcart'),
                            'style-2' => esc_html__('Style 2', 'printcart'),
                            'style-3' => esc_html__('Style 3', 'printcart'),
                        ),
                    ),
                ),
                'share_buttons_position' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Buttons position','printcart'),
                        'section' => 'share_buttons_element',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'inside-content' => get_template_directory_uri() . '/assets/netbase/images/options/ss-inside.png',
                            'floating' => get_template_directory_uri() . '/assets/netbase/images/options/ss-floating.png',
                        ),
                    ),
                ),
                'pagination_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Style', 'printcart'),
                        'section' => 'pagination_element',
                        'type' => 'select',
                        'choices' => array(
                            'pagination-style-1' => esc_html__('Style 1','printcart'),
                            'pagination-style-2' => esc_html__('Style 2','printcart'),
                        ),
                    ),
                ),
                'show_back_top' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show button', 'printcart'),
                        'section' => 'back_top_element',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'back_top_shape' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show button', 'printcart'),
                        'section' => 'back_top_element',
                        'type' => 'select',
                        'choices' => array(
                            'circle' => esc_html__('Circle','printcart'),
                            'square' => esc_html__('Square','printcart'),
                        ),
                    ),
                ),
                'back_top_style' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show button', 'printcart'),
                        'section' => 'back_top_element',
                        'type' => 'select',
                        'choices' => array(
                            'light' => esc_html__('Light','printcart'),
                            'dark' => esc_html__('Dark','printcart'),
                        ),
                    ),
                ),
            ),
        );
    }

    public function header()
    {
        return array(
            'title' => esc_html__('Header Options', 'printcart'),
            'description' => esc_html__('header description', 'printcart'),
            'priority' => 11,
            'sections' => array(
                'header_general' => array(
                    'title' => esc_html__('Sections', 'printcart'),
                ),
                'header_presets' => array(
                    'title' => esc_html__('Presets', 'printcart'),
                ),
                'header_menu' => array(
                    'title' => esc_html__('Menu', 'printcart'),
                ),
            ),
            'options' => array(
                'nbcore_general_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('General', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_logo_upload' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_file_image')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Site Logo', 'printcart'),
                        'section' => 'header_general',
                        'description' => esc_html__('If you don\'t upload logo image, your site\'s logo will be the Site Title ', 'printcart'),
                        'type' => 'WP_Customize_Upload_Control'
                    ),
                ),
                'nbcore_logo_width' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Logo Area Width', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '100',
                            'max' => '600',
                            'step' => '10',
                        ),
                    ),
                ),
                'nbcore_header_fixed' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Fixed header', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_header_top_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header topbar', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_show_header_topbar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__( 'Show header topbar', 'printcart' ),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Switch'
                    ),
                ),
                'nbcore_top_section_padding' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Top & bottom padding', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '45',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_header_top_hotline' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Hotline', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_top_language' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Language', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_header_top_currency' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Currency', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_header_top_login_link' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Login Link', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_header_facebook' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Facebook', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_twitter' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Twitter', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_linkedin' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Linked In', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_pinterest' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Pinterest', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_ggplus' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Google Plus', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_instagram' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Instagram', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_blog' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Blog', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'text',
                    ),
                ),
                'nbcore_header_middle_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header Middle', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_middle_section_padding' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Top & bottom padding', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '45',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_header_bottom_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header Bottom', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_bot_section_padding' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Top & bottom padding', 'printcart'),
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '0',
                            'max' => '45',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_header_color_focus' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'section' => 'header_general',
                        'type' => 'NBT_Customize_Control_Focus',
                        'choices' => array(
                            'header_colors' => esc_html__('Edit color', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_header_presets_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Header style', 'printcart'),
                        'section' => 'header_presets',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_header_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Select Style', 'printcart'),
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'section' => 'header_presets',
                        'choices' => array(
                            'header-1' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-2' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-3' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-4' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-5' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-6' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-7' => get_template_directory_uri() . '/assets/netbase/images/options/headers/mid-stack.png',
                            'header-9' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-stack.png',
                            'header-8' => get_template_directory_uri() . '/assets/netbase/images/options/headers/plain.png',
                            'header-10' => get_template_directory_uri() . '/assets/netbase/images/options/headers/left-inline.png',
                        ),
                    ),
                ),
                'nbcore_header_menu_config' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show menu border', 'printcart'),
                        'section' => 'header_menu',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),

            ),



        );
    }

    public function typo()
    {
        return array(
            'title' => esc_html__('Typography', 'printcart'),
            'priority' => 14,
            'options' => array(
                'body_font_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Body Font', 'printcart'),
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'body_font_family' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label'   => esc_html__( 'Font Family', 'printcart' ),
                        'dependency' => 'body_font_style',
                        'type'    => 'NBT_Customize_Control_Typography',
                    ),
                ),
                'body_font_style' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Font Styles', 'printcart'),
                        'type'    => 'NBT_Customize_Control_Font_Style',
                        'choices' => array(
                            'italic' => true,
                            'underline' => true,
                            'uppercase' => true,
                            'weight' => true,
                        )
                    ),
                ),
                'body_font_size' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Base Size', 'printcart'),
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '8',
                            'max' => '30',
                            'step' => '1',
                        ),
                    ),
                ),
                'heading_font_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Heading Font', 'printcart'),
                        'section' => 'typography',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'heading_font_family' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label'   => esc_html__( 'Heading font', 'printcart' ),
                        'dependency' => 'heading_font_style',
                        'type'    => 'NBT_Customize_Control_Typography',
                    ),
                ),
                'heading_font_style' => array(
                    'settings' => array(
                        'sanitize_callback' => 'wp_filter_nohtml_kses',
                    ),
                    'controls' => array(
                        'label' => esc_html__('Font Styles', 'printcart'),
                        'type'    => 'NBT_Customize_Control_Font_Style',
                        'choices' => array(
                            'italic' => true,
                            'underline' => true,
                            'uppercase' => true,
                            'weight' => true,
                        ),
                    ),
                ),
                'subset_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Font subset', 'printcart'),
                        'description' => esc_html__('Turn these settings on if you have to support these scripts', 'printcart'),
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'subset_cyrillic' => array(
                    'settings' => array(
                        'transport' => 'refresh',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox'),
                    ),
                    'controls' => array(
                        'label'   => esc_html__( 'Cyrillic subset', 'printcart' ),
                        'type'    => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'subset_greek' => array(
                    'settings' => array(
                        'transport' => 'refresh',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox'),
                    ),
                    'controls' => array(
                        'label'   => esc_html__( 'Greek subset', 'printcart' ),
                        'type'    => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'subset_vietnamese' => array(
                    'settings' => array(
                        'transport' => 'refresh',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox'),
                    ),
                    'controls' => array(
                        'label'   => esc_html__( 'Vietnamese subset', 'printcart' ),
                        'type'    => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'font_color_focus' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'type'    => 'NBT_Customize_Control_Focus',
                        'choices' => array(
                            'type_color' => esc_html__('Edit font color', 'printcart'),
                        ),
                    ),
                ),
            ),
        );
    }

    public function woocommerce()
    {

        $attributes = array();
        if ( class_exists( 'WooCommerce' ) ) {

            $attributes_tax = wc_get_attribute_taxonomies();
            foreach ($attributes_tax as $attribute) {
                $attributes[$attribute->attribute_name] = $attribute->attribute_label;

            }
        }

        return array(
            'title' => esc_html__('Products', 'printcart'),
            'priority' => 15,
            'sections' => array(
                'product_category' => array(
                    'title' => esc_html__('Product Category', 'printcart'),
                ),
                'product_details' => array(
                    'title' => esc_html__('Product Details', 'printcart'),
                ),
                'other_wc_pages' => array(
                    'title' => esc_html__('Other Pages', 'printcart'),
                ),
            ),
            'options' => array(
                'nbcore_pa_title_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Product category title', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_shop_title' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Shop page title', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'text',
                    ),
                ),
                'nbcore_wc_breadcrumb' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show breadcrumb ?', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pa_layout_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Product category layout', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_shop_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Sidebar Layout', 'printcart'),
                        'section' => 'product_category',
                        'description' => esc_html__('Sidebar Position for product category and shop page', 'printcart'),
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'left-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/2cl.png',
                            'no-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/1c.png',
                            'right-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/2cr.png',
                        ),
                    ),
                ),
                'nbcore_shop_content_width' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Woocommerce content width', 'printcart'),
                        'description' => esc_html__('This options also effect Cart page', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => '%',
                            'min' => '60',
                            'max' => '80',
                            'step' => '1'
                        ),
                    ),
                ),
                'shop_sticky_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Sticky Sidebar', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                //TODO nbcore_product_list depencies
                'nbcore_grid_product_description' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Product Description', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),

                'nbcore_show_separated_border' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show separated border', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),

                'nbcore_product_meta_align' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Choose product meta align', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'select',
                        'choices' => array(
                            'left' => esc_html__('Left', 'printcart'),
                            'center' => esc_html__('Center', 'printcart'),
                            'right' => esc_html__('Right', 'printcart'),
                        ),
                        'default' => 'horizontal'
                    ),
                ),

                'nbcore_product_hover' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Choose product hover', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'select',
                        'choices' => array(
                            'image' => esc_html__('Display box shadow only product image', 'printcart'),
                            'full_block' => esc_html__('Display box shadow whole product block', 'printcart'),
                        ),
                        'default' => 'horizontal'
                    ),
                ),

                'nbcore_loop_columns' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Products per row', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'two-columns' => get_template_directory_uri() . '/assets/netbase/images/options/2-columns.png',
                            'three-columns' => get_template_directory_uri() . '/assets/netbase/images/options/3-columns.png',
                            'four-columns' => get_template_directory_uri() . '/assets/netbase/images/options/4-columns.png',
                        ),
                    ),
                ),
                'nbcore_pa_other_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Other', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_shop_banner' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Shop Banner', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'WP_Customize_Cropped_Image_Control',
                        'flex_width'  => true,
                        'flex_height' => true,
                        'width' => 2000,
                        'height' => 1000,
                    ),
                ),
                'nbcore_shop_action' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show shop action', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch'
                    ),
                ),

                'nbcore_product_image_mask' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show opacity when hover', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch'
                    ),
                ),

                'nbcore_product_rating' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show product rating', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch'
                    ),
                ),

                'nbcore_product_action_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Choose product action style', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'select',
                        'choices' => array(
                            'vertical' => esc_html__('Vertical', 'printcart'),
                            'horizontal' => esc_html__('Horizontal', 'printcart'),
                            'center' => esc_html__('Center', 'printcart'),
                            'vertical_fix_wl' => esc_html__('Vertical with fixed wishlist button', 'printcart'),
                            'horizontal_fix_wl' => esc_html__('Horizontal with fixed wishlist button', 'printcart'),
                        ),
                        'default' => 'horizontal'
                    ),
                ),

                'nbcore_products_per_page' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Products per Page', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'number',
                        'input_attrs' => array(
                            'min'   => 1,
                            'step'  => 1,
                        ),
                    ),
                ),
                'nbcore_wc_sale' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Choose sale tag style', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'select',
                        'choices' => array(
                            ''        => esc_html__('Do not display', 'printcart'),
                            'style-1' => esc_html__('Style 1', 'printcart'),
                            'style-2' => esc_html__('Style 2', 'printcart'),
                        ),
                    ),
                ),

                'nbcore_product_image_border_color' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'wp_filter_nohtml_kses'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Product Image Border Color', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Color',
                    ),
                ),



                'product_category_compare' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Compare button', 'printcart'),
                        'description' => esc_html__('This feature need YITH Woocommerce Compare plugin to be installed and activated', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'product_category_wishlist' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Wishlist button', 'printcart'),
                        'description' => esc_html__('This feature need YITH Woocommerce Wishlist plugin to be installed and activated', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'product_category_quickview' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Quickview button', 'printcart'),
                        'description' => esc_html__('This feature need YITH Woocommerce Quick View plugin to be installed and activated', 'printcart'),
                        'section' => 'product_category',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_layout_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Layout', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_pd_details_title' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Enable Product title', 'printcart'),
                        'description' => esc_html__('Default product title is not display if the Page title is showing. Enable this to displaying both.', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_details_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Product details sidebar', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'left-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/2cl.png',
                            'no-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/1c.png',
                            'right-sidebar' => get_template_directory_uri() . '/assets/netbase/images/options/2cr.png',
                        ),
                    ),
                ),
                'nbcore_pd_details_width' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Product details content width', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => '%',
                            'min' => '60',
                            'max' => '80',
                            'step' => '1'
                        ),
                    ),
                ),
                'product_sticky_sidebar' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Sticky sidebar', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_meta_layout' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Product meta layout', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'left-images' => get_template_directory_uri() . '/assets/netbase/images/options/left-image.png',
                            'right-images' => get_template_directory_uri() . '/assets/netbase/images/options/right-image.png',
                            'wide' => get_template_directory_uri() . '/assets/netbase/images/options/wide.png',
                        ),
                    ),
                ),
                'nbcore_add_cart_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Add to cart input style', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'select',
                        'choices' => array(
                            'style-1' => esc_html__('Style 1', 'printcart'),
                            'style-2' => esc_html__('Style 2', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_pd_show_social' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show social share?', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_gallery_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Product Gallery', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_pd_images_width' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Product images width', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => '%',
                            'min' => '30',
                            'max' => '60',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_pd_featured_autoplay' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Featured Images Autoplay', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_thumb_pos' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Small thumb position', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'bottom-thumb' => get_template_directory_uri() . '/assets/netbase/images/options/bottom-thumb.png',
                            'left-thumb' => get_template_directory_uri() . '/assets/netbase/images/options/left-thumb.png',
                            'inside-thumb' => get_template_directory_uri() . '/assets/netbase/images/options/inside-thumb.png',
                        ),
                    ),
                ),
                'nbcore_pd_image_zoom' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Enable Image Zoom', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_info_tab_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Information tab', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_info_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Tabs style', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'select',
                        'choices' => array(
                            'horizontal-tabs' => esc_html__('Horizontal', 'printcart'),
                            'accordion-tabs' => esc_html__('Accordion', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_reviews_form' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Reviews form style', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'select',
                        'choices' => array(
                            'split' => esc_html__('Split', 'printcart'),
                            'full-width' => esc_html__('Full Width', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_reviews_round_avatar' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Round reviewer avatar', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_other_products_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Related & Cross-sells products', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Heading',
                    ),
                ),
                'nbcore_show_upsells' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show upsells products?', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_upsells_columns' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Upsells Products per row', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'select',
                        'choices' => array(
                            '2' => esc_html__('2 Products', 'printcart'),
                            '3' => esc_html__('3 Products', 'printcart'),
                            '4' => esc_html__('4 Products', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_upsells_limit' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Upsells Products limit', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'number',
                        'input_attrs' => array(
                            'min' => '2',
                            'step' => '1'
                        ),
                    ),
                ),
                'nbcore_show_related' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show related product?', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_pd_related_columns' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Related Products per row', 'printcart'),
                        'section' => 'product_details',
                        'type' => 'select',
                        'choices' => array(
                            '2' => esc_html__('2 Products', 'printcart'),
                            '3' => esc_html__('3 Products', 'printcart'),
                            '4' => esc_html__('4 Products', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_cart_intro' => array(
                    'settings' => array( 'sanitize_callback' => 'absint' ),
                    'controls' => array(
                        'label' => esc_html__('Cart', 'printcart'),
                        'section' => 'other_wc_pages',
                        'type' => 'NBT_Customize_Control_Heading'
                    ),
                ),
                'nbcore_cart_layout' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Cart page layout', 'printcart'),
                        'section' => 'other_wc_pages',
                        'type' => 'NBT_Customize_Control_Radio_Image',
                        'choices' => array(
                            'cart-layout-1' => get_template_directory_uri() . '/assets/netbase/images/options/cart-style-1.png',
                            'cart-layout-2' => get_template_directory_uri() . '/assets/netbase/images/options/cart-style-2.png',
                        ),
                    ),
                ),
                'nbcore_show_to_shop' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show Continue shopping button', 'printcart'),
                        'section' => 'other_wc_pages',
                        'type' => 'NBT_Customize_Control_Switch',
                    ),
                ),
                'nbcore_show_cross_sells' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_checkbox')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Show cross sells', 'printcart'),
                        'section' => 'other_wc_pages',
                        'type' => 'NBT_Customize_Control_Switch'
                    ),
                ),
                'nbcore_cross_sells_per_row' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Products per row', 'printcart'),
                        'section' => 'other_wc_pages',
                        'type' => 'select',
                        'choices' => array(
                            '3' => esc_html__('3 products', 'printcart'),
                            '4' => esc_html__('4 products', 'printcart'),
                            '5' => esc_html__('5 products', 'printcart'),
                        ),
                    ),
                ),
                'nbcore_cross_sells_limit' => array(
                    'settings' => array(
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Cross sells Products limit', 'printcart'),
                        'section' => 'other_wc_pages',
                        'type' => 'number',
                        'input_attrs' => array(
                            'min' => '3',
                            'step' => '1'
                        ),
                    ),
                ),
            ),
        );
    }

    public function nblayout()
    {
        return array(
            'title' => esc_html__('Layout', 'printcart'),
            'priority' => 15,
            'sections' => array(
                'site_layout' => array(
                    'title' => esc_html__('Layout', 'printcart'),
                ),
            ),
            'options' => array(
                'nbcore_container_width_screen' => array(
                    'settings' => array(
                        'transport' => 'postMessage',
                        'sanitize_callback' => 'absint'
                    ),
                    'controls' => array(
                        'label' => esc_html__('Container Width Screen', 'printcart'),
                        'section' => 'site_layout',
                        'type' => 'NBT_Customize_Control_Slider',
                        'choices' => array(
                            'unit' => 'px',
                            'min' => '1170',
                            'max' => '1470',
                            'step' => '1'
                        ),
                        'default' => '1170',
                    ),
                ),
            ),
        );
    }
    public function onlinedesign()
    {
        return array(
            'title' => esc_html__('Online Design', 'printcart'),
            'priority' => 15,
            'sections' => array(
                'online_design' => array(
                    'title' => esc_html__('Online Design', 'printcart'),
                ),
            ),
            'options' => array(
                'nbcore_template_designer_style' => array(
                    'settings' => array(
                        'sanitize_callback' => array('NBT_Customize_Sanitize', 'sanitize_selection')
                    ),
                    'controls' => array(
                        'label' => esc_html__('Choose template designer style', 'printcart'),
                        'section' => 'online_design',
                        'type' => 'select',
                        'choices' => array(
                            'style1' => esc_html__('Style 1', 'printcart'),
                            'style2' => esc_html__('Style 2', 'printcart'),
                            'style3' => esc_html__('Style 3', 'printcart'),
                        ),
                        'default' => 'style1'
                    ),
                ),
            ),
        ); 
    }
}
