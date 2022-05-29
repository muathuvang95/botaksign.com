<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('NBD_ADMIN_PRINTING_OPTIONS')){
    class NBD_ADMIN_PRINTING_OPTIONS {
        protected static $instance;
        public function __construct() {
            //todo something
        }
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                    self::$instance = new self();
            }
            return self::$instance;
        }
        public function init(){
            if (is_admin()) {
                $this->ajax();
            }
            add_action('nbd_menu', array($this, 'tab_menu'));
            add_action('nbd_create_tables', array($this, 'create_options_table'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 30, 1);
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'), 30);
            add_action('save_post', array($this, 'save_product_option'));

            // Alter the product thumbnail in order
            add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'admin_order_item_thumbnail' ), 50, 3 ); 
            //Hide some price option data in order
            add_filter( 'woocommerce_hidden_order_itemmeta', array($this, 'hidden_custom_order_item_metada'));

            if( nbdesigner_get_option( 'nbdesigner_enable_map_print_options', 'no' ) == 'yes' ){
                add_action( 'woocommerce_product_write_panel_tabs', array(&$this, 'product_write_panel_tab'), 99 );
                add_action( 'woocommerce_product_data_panels', array(&$this, 'product_data_panel'), 99 );
                add_action( 'woocommerce_process_product_meta', array(&$this, 'process_product_meta'), 1, 2 );
            }
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_download_option_image'     => true,
                'nbd_get_media_full_size_url'   => true,
                'nbd_get_data_material_by_sku'   => true, // custom botak Phase 4 material
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    // NBDesigner AJAX can be used for frontend ajax requests
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        // custom botak Phase 4 material
        public function nbd_get_data_material_by_sku(){
            global $wpdb;
            $result = array(
                'flag'      => 1,
                'options_price'    => array()
            );
            $list_sku = json_decode( stripslashes( $_POST['sku'] ), true );
            if( is_array($list_sku) ) {
                foreach( $list_sku as $key => $sku ) { 
                    $query = "SELECT post_id FROM {$wpdb->prefix}postmeta AS postmt WHERE ( postmt.meta_key LIKE 'material_code' AND postmt.meta_value LIKE '".$sku."' ) ";
                    $options = $wpdb->get_results($query, 'ARRAY_A');
                    $post_id = $options[0]['post_id'] ? (int) $options[0]['post_id'] : 0;
                    $post_fields = get_field( 'material_price' , $post_id);
                    if(is_array($post_fields)) {
                        foreach($post_fields as $k => $post_field) {
                            $role_id = $post_field['role'];
                            $slug = get_post_meta( $role_id , 'wppb_role_slug' , true);
                            $name = html_entity_decode(get_the_title( $role_id ) , ENT_QUOTES | ENT_XML1, 'UTF-8');
                            $result['options_price'][$key][$k] = array(
                                'name' => $name,
                                'value' => $slug,
                                'price' => $post_field['price'],
                            ); 
                        }
                    }
                } 
            } else {
                $result['flag'] = 0;
            }
            wp_send_json_success($result);
            wp_die();
        }
        public function nbd_get_media_full_size_url(){
            if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
                die('Security error');
            }
            $result = array(
                'flag'      => 1,
                'images'    => array()
            );
            $images = json_decode( stripslashes( $_POST['images'] ), true );
            foreach( $images as $key => $image ){
                $result['images'][$key] = wp_get_attachment_url( $image );
            }
            echo json_encode($result);
            wp_die();
        }
        public function nbd_download_option_image(){
            if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
                die('Security error');
            }
            $result = array(
                'flag'      => 1,
                'image'     => array()
            );
            $url = $_POST['image'];
            require_once(NBDESIGNER_PLUGIN_DIR.'includes/class.download.image.php');
            if( strpos( $url, get_site_url() ) > -1 ){
                $result['image'] = array(
                    'current_site'  => 1
                );
            }else{
                $download_remote_image = new Nbdesigner_Download_Image( $url, array() );
                $attachment_id = $download_remote_image->download();
                if( $attachment_id ){
                    $result['image'] = array(
                        'current_site'  => 0,
                        'id'            => $attachment_id
                    );
                }else{
                    $result['flag'] = 0;
                }
            }
            echo json_encode($result);
            wp_die();
        }
        public function hidden_custom_order_item_metada($order_items){
            $order_items[] = '_nbo_option_price';
            $order_items[] = '_nbo_field';
            $order_items[] = '_nbo_options';
            $order_items[] = '_nbo_original_price';
            return $order_items;
        }
        public function admin_order_item_thumbnail( $image = "", $item_id = "", $item = "" ){
            $order = nbd_get_order_object();
            $item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', FALSE ) : $order->get_item_meta( $item_id ); 
            if( isset($item_meta['_nbo_option_price']) ){
                $option_price = maybe_unserialize( $item_meta['_nbo_option_price'][0] );
                $size = 'shop_thumbnail';
                $dimensions = wc_get_image_size( $size );  
                if( isset($option_price['cart_image']) && $option_price['cart_image'] != '' ){
                    $image = '<img src="'.$option_price['cart_image']
                            . '" width="' . esc_attr( $dimensions['width'] )
                            . '" height="' . esc_attr( $dimensions['height'] )
                            . '" class="nbo-thumbnail woocommerce-placeholder wp-post-image" />';
                }
            }
            return $image;
        }
        public function save_product_option($post_id){
            if (!isset($_POST['nbo_box_nonce']) || !wp_verify_nonce($_POST['nbo_box_nonce'], 'nbo_box')
                || !(current_user_can('administrator') || current_user_can('shop_manager') || current_user_can('designer'))) {
                return $post_id;
            }
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $post_id;
            }
            if ('page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return $post_id;
                }
            }

            $enable = $_POST['_nbo_enable']; 
            update_post_meta($post_id, '_nbo_enable', $enable);
            if( isset($_POST['_nbdpb_enable']) ){
                $enable = $_POST['_nbdpb_enable']; 
                update_post_meta($post_id, '_nbdpb_enable', $enable);
            }

            $nbpt_title     = $_POST['_nbpt_title'];
            $nbpt_content   = $_POST['_nbpt_content'];
            update_post_meta($post_id, '_nbpt_title', $nbpt_title);
            update_post_meta($post_id, '_nbpt_content', htmlspecialchars( $nbpt_content ) );

            if( isset( $_POST['_nbo_snippet_price'] ) ){
                $snippet_price = wc_clean( $_POST['_nbo_snippet_price'] );
                update_post_meta($post_id, '_nbo_snippet_price', $snippet_price);
            }

            do_action('nbo_save_options', $post_id);
        }
        public function add_meta_boxes(){
            add_meta_box('nbo_print_option', __('NBD options', 'web-to-print-online-designer'), array($this, 'meta_box'), 'product', 'normal', 'high');
        }
        public function meta_box(){
            $post_id            = get_the_ID();
            $enable             = get_post_meta($post_id, '_nbo_enable', true);
            $nbdpb_enable       = get_post_meta($post_id, '_nbdpb_enable', true);
            $option_id          = $this->get_product_option( $post_id );
            $option_id          = $option_id ? $option_id : 0;
            $link_edit_option   = add_query_arg(array(
                    'product_id'    => $post_id, 
                    'action'        => 'edit',
                    'paged'         => 1,
                    'id'            => $option_id
                ),
                admin_url('admin.php?page=nbd_printing_options'));
            $nbpt_title     = get_post_meta($post_id, '_nbpt_title', true);
            $nbpt_content   = get_post_meta($post_id, '_nbpt_content', true);
            include_once(NBDESIGNER_PLUGIN_DIR .'views/options/meta-box.php');
        }
        public function get_product_option( $product_id ){
            $option_id = get_transient( 'nbo_product_'.$product_id );
            if( false === $option_id ){
                global $wpdb;
                $sql = "SELECT id, priority, apply_for, product_ids, product_cats, date_from, date_to FROM {$wpdb->prefix}nbdesigner_options WHERE published = 1";
                $options = $wpdb->get_results($sql, 'ARRAY_A');
                if($options){
                    $_options = array();
                    foreach( $options as $option ){
                        $execute_option = true;
                        $from_date = false;
                        if( isset($option['date_from']) ){
                            $from_date = empty( $option['date_from'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_from'] ), false ) );
                        }
                        $to_date = false;
                        if( isset($option['date_to']) ){
                            $to_date = empty( $option['date_to'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_to'] ), false ) );
                        }
                        $now  = current_time( 'timestamp' );
                        if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
                            $execute_option = false;
                        } elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
                            $execute_option = false;
                        } elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
                            $execute_option = false;
                        }
//                        if( $execute_option ){
//                            if( $option['apply_for'] == 'p' ){
//                                $products = unserialize($option['product_ids']);
//                                $execute_option = in_array($product_id, $products) ? true : false;
//                            }else {
//                                $categories = $option['product_cats'] ? unserialize($option['product_cats']) : array();
//                                $product = wc_get_product($product_id);
//                                $product_categories = $product->get_category_ids();
//                                $intersect = array_intersect($product_categories, $categories);
//                                $execute_option = ( count($intersect) > 0 ) ? true : false;
//                            }
//                        }
                        if( $execute_option ){
                            //CS botak
                            switch ($option['apply_for']) {
                                case 'p':
                                    $products = unserialize($option['product_ids']);
                                    $execute_option = in_array($product_id, $products) ? true : false;
                                    break;
                                case 's':
                                    $product = wc_get_product($product_id);
                                    $execute_option = "service" === $product->get_type() ? true : false;
                                    break;
                                default :
                                    $categories = $option['product_cats'] ? unserialize($option['product_cats']) : array();
                                    $product = wc_get_product($product_id);
                                    $product_categories = $product->get_category_ids();
                                    $intersect = array_intersect($product_categories, $categories);
                                    $execute_option = ( count($intersect) > 0 ) ? true : false;
                                    break;
                            }
                        }
                        if( $execute_option ){
                            $_options[] = $option;
                        }
                    }
                    $_options = array_reverse( $_options );
                    $option_priority = 0;
                    foreach( $_options as $_option ){
                        if( $_option['priority'] > $option_priority ){
                            $option_priority = $_option['priority'];
                            $option_id = $_option['id'];
                        }
                    }
                    if( $option_id ){
                        set_transient( 'nbo_product_'.$product_id , $option_id );
                        
                        $is_artwork_action = get_transient( 'nbo_action_'.$product_id );
                        if( false === $is_artwork_action ){
                            $_selected_options  = $this->get_option( $option_id );
                            $selected_options   = unserialize( $_selected_options['fields'] );
                            if ( isset( $selected_options['fields'] ) ) {
                                foreach ($selected_options['fields'] as $key => $field) {
                                    if ( $field['general']['enabled'] == 'y' && isset( $field['nbe_type'] ) && $field['nbe_type'] == 'actions' ) {
                                        $is_artwork_action = true;
                                    }
                                }
                            }
                            if( $is_artwork_action ){
                                set_transient( 'nbo_action_'.$product_id , '1' );
                            }
                        }
                    }
                }
            } 
            return $option_id;
        }
        public function tab_menu(){
            if(current_user_can('manage_nbd_tool')){  
                $options_hook = add_submenu_page(
                    'nbdesigner', 'Printing Options', 'Printing Options', 'manage_nbd_tool', 'nbd_printing_options', array($this, 'printing_options')
                );
                add_action( "load-$options_hook", array( $this, 'screen_option' ));
            }
        }
        public function create_options_table(){
            global $wpdb;
            $collate = '';
            if ( $wpdb->has_cap( 'collation' ) ) {
                $collate = $wpdb->get_charset_collate();
            } 
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            if (NBDESIGNER_VERSION != get_option("nbdesigner_version_plugin")) {        
                $tables =  "
CREATE TABLE {$wpdb->prefix}nbdesigner_options ( 
 id bigint(20) unsigned NOT NULL auto_increment,
 title text NOT NULL,
 priority  TINYINT(1) NOT NULL default 0, 
 published  TINYINT(1) NOT NULL default 1, 
 product_ids text NULL, 
 product_cats text NULL,  
 date_from TINYTEXT NULL,  
 date_to TINYTEXT NULL,  
 apply_for TINYTEXT NULL,  
 enabled_roles text NULL,  
 disabled_roles text NULL,  
 created datetime NOT NULL default '0000-00-00 00:00:00',
 modified datetime NOT NULL default '0000-00-00 00:00:00', 
 created_by BIGINT(20) NULL, 
 modified_by BIGINT(20) NULL,  
 fields longtext,
 builder text NULL,
 PRIMARY KEY  (id)
) $collate; 
                ";
                @dbDelta($tables);
            }
        }
        public function admin_enqueue_scripts( $hook ){
            if( $hook == 'nbdesigner_page_nbd_printing_options' ){
                wp_register_style('nbd_options', NBDESIGNER_CSS_URL . 'admin-options.css', array('wp-color-picker'), NBDESIGNER_VERSION);
                wp_register_script('nbd_options', NBDESIGNER_JS_URL . 'admin-options.js', array('jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-datepicker', 'jquery-ui-autocomplete', 'wp-color-picker', 'angularjs', 'wc-enhanced-select'), NBDESIGNER_VERSION);
                wp_localize_script('nbd_options', 'nbd_options', array(
                    'nbd_options_lang'          => nbd_option_i18n(),
                    'calendar_image'            =>  NBDESIGNER_PLUGIN_URL.'assets/images/calendar.png',
                    'search_products_nonce'     =>  wp_create_nonce( "search-products" ),
                ));   
                wp_enqueue_style(array('wp-jquery-ui-dialog', 'wp-color-picker', 'nbd_options'));
                wp_enqueue_script(array('wpdialogs', 'nbd_options'));
                $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
                wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
            }
        }
        public function printing_options(){ 
            if( isset( $_GET['action'] ) && $_GET['action'] != 'copy' ){
                $paged      = get_query_var( 'paged', 1 );
                $message    = array( 'content'  => '' );
                if( $_GET['action'] == 'unpublish' ){
                    $this->unpublish_option( $_REQUEST['id'] );
                    wp_redirect(esc_url_raw(add_query_arg(array('paged' => $paged), admin_url('admin.php?page=nbd_printing_options'))));
                }else{
                    $id = (isset( $_REQUEST['id'] ) && absint($_REQUEST['id']) > 0 ) ? absint($_REQUEST['id']) : 0;
                    if( isset( $_POST['save'] ) || isset( $_POST['options'] ) ){
                        $result = $this->save_option();
                        if($result['status']){
                            $message = array(
                                'flag'      => 'success',
                                'content'   => __('Option updated.', 'web-to-print-online-designer')
                            );
                            if( $id == 0 ){
                                $id = $result['id'];
                                wp_redirect(esc_url_raw(add_query_arg(array(
                                    'paged'     => 1,
                                    'action'    => 'edit',
                                    'id'        => $id
                                ), admin_url('admin.php?page=nbd_printing_options'))));
                            }
                        }else{
                            $message = array(
                                'flag'      => 'error',
                                'content'   => ''
                            );
                        }
                    }
                    $_options = ($id > 0) ? $this->get_option($id) : false;
                    if( $_options ){
                        $raw_options = unserialize( $_options['fields'] );
                        if( !isset( $raw_options["fields"] ) ){
                            $raw_options["fields"] = array();
                        }
                        $options                    = $this->build_options( $raw_options );
                        $options['id']              = $_options['id'];
                        $options['title']           = $_options['title'];
                        $options['priority']        = $_options['priority'];
                        $options['published']       = $_options['published'];
                        $options['date_from']       = isset($_options['date_from']) ? $_options['date_from'] : '';
                        $options['date_to']         = isset($_options['date_to']) ? $_options['date_to'] : '';
                        $options['apply_for']       = isset($_options['apply_for']) ? $_options['apply_for'] : 'p';
                        $options['product_cats']    = isset($_options['product_cats']) ? ( !is_null(unserialize($_options['product_cats'])) ? unserialize($_options['product_cats']) : array()) : array();
                        $options['product_ids']     = isset($_options['product_ids']) ? ( !is_null(unserialize($_options['product_ids'])) ? unserialize($_options['product_ids']) : array()) : array();
                    }else{
                        $options = $this->build_options();
                        $options['id']              = 0;
                        $options['title']           = '';
                        $options['date_from']       = '';
                        $options['date_to']         = '';
                        $options['priority']        = 1;
                        $options['published']       = 1;
                        $options['apply_for']       = 'p';
                        $options['product_cats']    = array();
                        $options['product_ids']     = array();
                    }
                    foreach ( $options["fields"] as $f_index => $field ){
                        if($field["conditional"]['enable'] == 'n'){
                            $options["fields"][$f_index]["conditional"]['depend']   = $this->build_config_conditional_depend();
                            $options["fields"][$f_index]["conditional"]['logic']    = $this->build_config_conditional_logic();
                            $options["fields"][$f_index]["conditional"]['show']     = $this->build_config_conditional_show();
                        }
                    }
                    $default_field = $this->default_config_field();
                    $product_id = (isset($_GET['product_id']) && absint($_GET['product_id']) > 0) ? absint($_GET['product_id']) : 0;
                    if( $product_id ){
                        if( !$_options ){
                            $options['product_ids'] = array( 0 => $product_id);
                        }
                    }
                    include_once(NBDESIGNER_PLUGIN_DIR . 'views/options/edit-option.php');
                }
            }else{
                require_once NBDESIGNER_PLUGIN_DIR . 'includes/options/fields-list-table.php';
                $nbd_options = new NBD_Options_List_Table();
                include_once( NBDESIGNER_PLUGIN_DIR . 'views/options/options-list-table.php' );
            }
        }
        public function screen_option(){
            if( !isset( $_GET['action'] ) || $_GET['action'] == 'copy' ){
                $option = 'per_page';
                $args   = array(
                    'label'   => __('Printing Options', 'web-to-print-online-designer'),
                    'default' => 10,
                    'option'  => 'options_per_page'
                );
                add_screen_option( $option, $args );
            }
        }
        public function save_option(){
            $id             = absint($_REQUEST['id']);
            $modified_date  = new DateTime();
            $arr            = array(
                'title'         => $_POST['title'],
                'published'     => 1,
                'priority'      => $_POST['priority'],
                'date_from'     => $_POST['date_from'],
                'date_to'       => $_POST['date_to'],
                'apply_for'     => $_POST['apply_for'],
                'product_cats'  => isset($_POST['product_cats']) ? serialize($_POST['product_cats']) : serialize(array()),
                'product_ids'   => isset($_POST['product_ids']) ? serialize($_POST['product_ids']) : serialize(array()),
                'modified'      => $modified_date->format('Y-m-d H:i:s')
            );
            $post_options = $_POST['options'];
            if( isset( $_POST['options']['jsonFields'] ) ){
                $post_options['fields'] = json_decode( stripslashes( $_POST['options']['jsonFields'] ), true );
                unset( $post_options['jsonFields'] );
            }
            
            //CS botak disable depend_quantity
            foreach ($post_options['fields'] as $f_index => $field) { 
                $post_options['fields'][$f_index]["general"]["depend_quantity"] = "n";
            }
            
            $arr['fields'] = serialize( $this->validate_option( $post_options ) );
            
            global $wpdb;
            $date = new DateTime();
            if( $id > 0 ){
                $arr['modified']    = $date->format('Y-m-d H:i:s');
                $arr['modified_by'] = wp_get_current_user()->ID;
                $result             = $wpdb->update("{$wpdb->prefix}nbdesigner_options", $arr, array( 'id' => $id) );
            }else{
                $arr['created']     = $date->format('Y-m-d H:i:s');
                $arr['created_by']  = wp_get_current_user()->ID;
                $result             = $wpdb->insert("{$wpdb->prefix}nbdesigner_options", $arr);
                $id                 = $result ?  $wpdb->insert_id : 0;
            }
            $this->clear_transients();
            do_action('nbo_save_print_option', $arr);
            return array(
                'status'    => $result,
                'id'        => $id
            );
        }
        private function clear_transients(){
            global $wpdb;
            $sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_nbo_product_%' OR option_name LIKE '_transient_timeout_nbo_product_%'";
            $wpdb->query( $sql );
            /* Clear swatches in archive shop pages */
            $sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_nbo_archive_options_%' OR option_name LIKE '_transient_timeout_nbo_archive_options_%'";   
            $wpdb->query( $sql );
            /* Clear product artwork action */
            $sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_nbo_action_%' OR option_name LIKE '_transient_timeout_nbo_action_%'";
            $wpdb->query( $sql );
            do_action('nbo_clear_transients');
        }
        private function validate_option( $options ){
            if( $options['display_type'] == 2 ){
                if( !isset($options['pm_hoz']) ){
                    $options['pm_hoz'] = array();
                }
                if( !isset($options['pm_ver']) ){
                    $options['pm_ver'] = array();
                }
            }else if( $options['display_type'] == 3 ){
                if( !isset($options['bulk_fields']) ){
                    $options['bulk_fields'] = array();
                }
            }else if( $options['display_type'] == 4 ) {
                if( !isset($options['groups']) ){
                    $options['groups'] = array();
                }
            }else if( $options['display_type'] == 6 ){
                if( !isset($options['popup_fields']) ){
                    $options['popup_fields']        = array();
                    $options['popup_trigger_field'] = '';
                    $options['popup_trigger_value'] = '';
                }
            } else if( !isset( $options['display_type'] ) ) {
                $options['display_type'] = 1;
            }
            if( isset($options["fields"]) ){
                foreach ( $options["fields"] as $f_index => $field ){
                    $array_price_type = array( 'f', 'p', 'p+', 'c', 'cp', 'cf' );
                    if( !in_array( $field["general"]['price_type'], $array_price_type ) ){
                        //$options["fields"][$f_index]["general"]['price_type'] = $field["general"]['data_type'] == 'i' ? 'c' : 'f';
                        $options["fields"][$f_index]["general"]['price_type'] = 'f';
                        if( isset( $options["fields"][$f_index]['nbd_type'] ) && ( $options["fields"][$f_index]['nbd_type'] == 'page' || $options["fields"][$f_index]['nbd_type'] == 'page1' ) ){
                            $options["fields"][$f_index]["general"]['price_type'] = 'c';
                        }
                    }
                    if( isset( $field["conditional"]['depend'] ) ){
                        foreach( $field["conditional"]['depend'] as $d_index => $depend ){
                            if( ( $depend['id'] == '? string: ?' || $depend['id'] == '' ) || ( ($depend['operator'] == 'i' || $depend['operator'] == 'n') && ( !isset( $depend['val'] ) || $depend['val'] == '? string: ?' || $depend['val'] == '? string:? object:null ? ?' || $depend['val'] == '' ) ) ){
                                unset($options["fields"][$f_index]["conditional"]['depend'][$d_index]);
                            }
                        }
                    }
                    if( $field["general"]['data_type'] == 'm' ){
                        if( count( $field["general"]['attributes']['options'] ) ){
                            foreach( $field["general"]['attributes']['options'] as $oIndex => $option ){
                                if( isset( $option['enable_con'] ) && $option['enable_con'] == 'on' ){
                                    foreach( $option['depend'] as $d_index => $depend ){
                                        if( ( $depend['id'] == '? string: ?' || $depend['id'] == '' ) || ( ($depend['operator'] == 'i' || $depend['operator'] == 'n') && ( !isset( $depend['val'] ) || $depend['val'] == '? string: ?' || $depend['val'] == '? string:? object:null ? ?' || $depend['val'] == '' ) ) ){
                                            unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['depend'][$d_index] );
                                        }else if( ( $depend['operator'] == 'i' || $depend['operator'] == 'n' ) && ( !isset( $depend['subval'] ) || $depend['subval'] == '? string: ?' || $depend['subval'] == '? string:? object:null ? ?' || $depend['subval'] == '' ) ){
                                            unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['depend'][$d_index]['subval'] );
                                        }
                                        if( $depend['id'] == 'qty' && isset( $depend['subval'] ) ){
                                            unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['depend'][$d_index]['subval'] );
                                        }
                                    }
                                }else{
                                    if( isset( $option['depend'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['depend'] );
                                    if( isset( $option['con_show'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['con_show'] );
                                    if( isset( $option['con_logic'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['con_logic'] );
                                }

                                if( isset( $option['enable_subattr'] ) &&  $option['enable_subattr'] == 'on' 
                                    && isset( $option['sub_attributes'] ) &&  count( $option['sub_attributes'] ) > 0 ){
                                    foreach( $option['sub_attributes'] as $saIndex => $subAttr ){
                                        if( isset( $subAttr['enable_con'] ) && $subAttr['enable_con'] == 'on' ){
                                            foreach( $subAttr['depend'] as $d_index => $depend ){
                                                if( ( $depend['id'] == '? string: ?' || $depend['id'] == '' ) || ( ($depend['operator'] == 'i' || $depend['operator'] == 'n') && ( !isset( $depend['val'] ) || $depend['val'] == '? string: ?' || $depend['val'] == '? string:? object:null ? ?' || $depend['val'] == '' ) ) ){
                                                    unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['sub_attributes'][$saIndex]['depend'][$d_index] );
                                                }else if( ( $depend['operator'] == 'i' || $depend['operator'] == 'n' ) && ( !isset( $depend['subval'] ) || $depend['subval'] == '? string: ?' || $depend['subval'] == '? string:? object:null ? ?' || $depend['subval'] == '' ) ){
                                                    unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['sub_attributes'][$saIndex]['depend'][$d_index]['subval'] );
                                                }
                                                if( $depend['id'] == 'qty' && isset( $depend['subval'] ) ){
                                                    unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['sub_attributes'][$saIndex]['depend'][$d_index]['subval'] );
                                                }
                                            }
                                        }else{
                                            if( isset( $subAttr['depend'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['sub_attributes'][$saIndex]['depend'] );
                                            if( isset( $subAttr['con_show'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['sub_attributes'][$saIndex]['con_show'] );
                                            if( isset( $subAttr['con_logic'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['sub_attributes'][$saIndex]['con_logic'] );
                                        }
                                    }
                                }
                                
                                //CS botak condition change gallery
//                                if( isset( $option['gallery_enable_con'] ) && $option['gallery_enable_con'] == 'on' ){
//                                    foreach( $option['gallery_depend'] as $d_index => $depend ){
//                                        if( ( $depend['id'] == '? string: ?' || $depend['id'] == '' ) || ( ($depend['operator'] == 'i' || $depend['operator'] == 'n') && ( !isset( $depend['val'] ) || $depend['val'] == '? string: ?' || $depend['val'] == '? string:? object:null ? ?' || $depend['val'] == '' ) ) ){
//                                            unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['gallery_depend'][$d_index] );
//                                        }else if( ( $depend['operator'] == 'i' || $depend['operator'] == 'n' ) && ( !isset( $depend['subval'] ) || $depend['subval'] == '? string: ?' || $depend['subval'] == '? string:? object:null ? ?' || $depend['subval'] == '' ) ){
//                                            unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['gallery_depend'][$d_index]['subval'] );
//                                        }
//                                        if( $depend['id'] == 'qty' && isset( $depend['subval'] ) ){
//                                            unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['gallery_depend'][$d_index]['subval'] );
//                                        }
//                                    }
//                                }else{
//                                    if( isset( $option['gallery_depend'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['gallery_depend'] );
//                                    if( isset( $option['gallery_con_logic'] ) ) unset( $options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['gallery_con_logic'] );
//                                }
                            }
                        }
                    }
                    if( isset( $field["general"]['published'] ) && $field["general"]['published'] == 'n' ){
                        $options["fields"][$f_index]["general"]['required'] = 'n';
                    }
                    if( isset( $field["general"]['depend_qty'] ) && $field["general"]['depend_qty'] == 'n2' ){
                        $options["fields"][$f_index]["general"]['price_type'] = 'f';
                    }
                }
            }
            return $options;
        }
        public function get_option($id){
            global $wpdb;
            $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
            $sql .= " WHERE id = " . esc_sql($id);
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            return count($result[0]) ? $result[0] : false;
        }
        public function delete_option( $id ){
            global $wpdb;
            $sql = "DELETE FROM {$wpdb->prefix}nbdesigner_options";
            $sql .= " WHERE id = " . esc_sql($id);
            $result = $wpdb->query( $sql );
            if( $result ) $this->clear_transients();
        }
        public function unpublish_option( $id ){
            global $wpdb;
            $result = $wpdb->update($wpdb->prefix . 'nbdesigner_options', array(
                'published' => 0
            ), array( 'id' => esc_sql($id))); 
            if( $result ) $this->clear_transients();
        }
        public function build_options( $options = null ){
            //CS botak role quantity break
            global $wp_roles;
            $all_roles = $wp_roles->roles;
            
            if( is_null($options) ){
                $options = array(
                    'quantity_type'             => 'r',
                    'quantity_discount_type'    => 'p',
                    'quantity_min'              => 1,
                    'quantity_max'              => 100,
                    'quantity_step'             => 1,
                    'quantity_enable'           => 'n',
                    'quantity_breaks'           => array(
                        array(
                            'val'       => 1,
                            'dis'       => '',
                            'default'   => 0
                        )
                    ),
                    //CS botak quantity rol break
                    'role_breaks'           => array(
                        array(
                            'role'              => 'subscriber',
                            'role_name'         => 'Subscriber',
                            'quantity_breaks'   => array(
                                array(
                                    'val'       => 1,
                                    'dis'       => '',
                                    'default'   => 0
                                )
                            )
                        )
                    ),
                    //CS botak gallery option
                    'gallery_options'            => array(
                        array (
                            'product_images'     => [],
                            'gallery_enable_con' => 0,
                            'gallery_con_logic'  => 'a',
                            'gallery_depend'     => array(
                                0   => array(
                                    'id'        => '',
                                    'operator'  => 'i',
                                    'val'       => '',
                                    'subval'    => ''
                                )
                            )
                        ),
                    ),
                    'display_type'              => 1,
                    'version'                   => NBDESIGNER_NUMBER_VERSION,
                    'pm_hoz'                    => array(),
                    'pm_ver'                    => array(),
                    'bulk_fields'               => array(),
                    'groups'                    => array(),
                    'popup_fields'              => array(),
                    'popup_trigger_field'       => '',
                    'popup_trigger_value'       => '',
                    'fields'                    => array(
                        0   =>  $this->default_field()
                    )
                );
            }
            //CS botak role quantity break
            if (!array_key_exists('quantity_breaks', $options)) {
                $options['quantity_breaks'] = array(
                    array(
                        'val'       => 1,
                        'dis'       => '',
                        'default'   => 0
                    )
                );
            }
            if( isset($options['role_breaks']) ){
                foreach ($options['role_breaks'] as &$role) {
                    $role['role_name'] = '';
                    if (isset($all_roles[$role['role']])) {
                        $role['role_name'] = $all_roles[$role['role']]['name'];
                    }
                    if (!is_array($role['quantity_breaks'])) {
                        $role['quantity_breaks'] = array(
                            array(
                                'val'       => 1,
                                'dis'       => '',
                                'default'   => 0
                            )
                        );
                    }
                }
            } else {
                $options['role_breaks'] = array(
                    array(
                        'role'              => 'subscriber',
                        'role_name'         => 'Subscriber',
                        'quantity_breaks'   => array(
                            array(
                                'val'       => 1,
                                'dis'       => '',
                                'default'   => 0
                            )
                        )
                    )
                );
            }
            if ( !isset($options['gallery_options'])) {
                $options['gallery_options'] =  array(
                    array (
                        'product_images'     => [],
                        'gallery_enable_con' => 0,
                        'gallery_con_logic'  => 'a',
                        'gallery_depend'     => array(
                            0   => array(
                                'id'        => '',
                                'operator'  => 'i',
                                'val'       => '',
                                'subval'    => ''
                            )
                        )
                    ),
                );
            } else {
                foreach ($options['gallery_options'] as &$gallery_option) {
                    if (!isset($gallery_option['product_images']) || !is_array($gallery_option['product_images'])) {
                        $gallery_option['product_images'] = [];
                    } else {
                        foreach ($gallery_option['product_images'] as &$img) {
                            $img['product_image_url'] = nbd_get_image_thumbnail( $img['product_image'] );
                        }
                    }
                    if (!isset($gallery_option['gallery_depend']) || !is_array($gallery_option['gallery_depend'])) {
                        $gallery_option['gallery_depend'] = [
                            [
                                'id'        => '',
                                'operator'  => 'i',
                                'val'       => '',
                                'subval'    => ''
                            ]
                        ];
                    }
                }
            }
            if( !isset($options['version']) ){
                $options['quantity_enable'] = 'n';
            }
            if( !isset($options['groups']) ){
                $options['groups'] = array();
            }
            foreach ( $options['groups'] as $gkey => $group ){
                $options['groups'][$gkey]['image_url'] = nbd_get_image_thumbnail( $group['image'] );
            }
            $options['fields'] = $this->recursive_stripslashes($options['fields']);
            foreach( $options['fields'] as $f_key => $field ){
                $field = array_replace_recursive($this->default_field(), $field);
                foreach ($field as $tab =>  $data){
                    if( $tab != 'id' && $tab != 'nbd_type' && $tab != 'nbpb_type' && $tab != 'nbe_type' ){
                        foreach ($data as $key => $f){
                            if( !in_array($key, array( 'price_no_range', 'price_depend_no', 'component_icon', 'page_display', 'exclude_page', 'auto_select_page', 'measure', 'measure_type', 'measure_range', 'measure_base_pages', 'min_width', 'max_width', 'step_width', 'default_width', 'min_height', 'max_height', 'step_height', 'default_height')) ){
                                $funcname = "build_config_".$tab.'_'.$key;
                                if( is_callable( array( $this, $funcname ) ) ){
                                    $options['fields'][$f_key][$tab][$key] = $this->$funcname($f);
                                }
                            }
                            if( $key == 'component_icon' ){
                                $options['fields'][$f_key][$tab]['component_icon_url'] = nbd_get_image_thumbnail( $f );
                            }
                        }

                        //CS botak add role range price
                        if (isset($options['fields'][$f_key]['nbd_type']) && ($options['fields'][$f_key]['nbd_type'] === 'size' || $options['fields'][$f_key]['nbd_type'] === 'dimension') && $tab === 'general') {                                        
                            $role_measure_range = [];
                            if (isset($options['fields'][$f_key][$tab]['role_measure_range'])) {
                                //Render role name
                                $general = $options['fields'][$f_key][$tab];
                                foreach ($general['role_measure_range'] as $role_measure) {
                                    $role_measure['role_name'] = '';
                                    if ($role_measure['role'] == 'all') {
                                        $role_measure['role_name'] = 'All';
                                    } else if (isset($all_roles[$role['role']])) {
                                        $role_measure['role_name'] = $all_roles[$role_measure['role']]['name'];
                                    }
                                    
                                    //Add multi measure (multi component for caculation price)
                                    if (!isset($role_measure['multi_measure']) || !is_array($role_measure['multi_measure'])) {
                                        $role_measure['multi_measure'] = array(
                                            array(
                                                'caculation_method' => 'area', //CS botak meansure price (5 methods: area, perimeter, top-bottom, left-right, custom-formula)
                                                'minimum_price'     => 0, //CS botak meansure price
                                                'custom_formular'   => '', //CS botak meansure price
                                                'measure_unit'      => 'sq' . nbdesigner_get_option('nbdesigner_dimensions_unit'), //CS botak meansure price
                                                'measure_type'      => 'u', //CS botak meansure price
                                                'measure_range'     => array([])
                                            )
                                        );
                                    }
                                    foreach ($role_measure['multi_measure'] as &$measure) {
                                        if (!isset($measure['measure_range']) || !is_array($measure['measure_range'])) {
                                            $measure['measure_range'] = [];
                                        }
                                    }
                                    if (!isset($role_measure['default'])) {
                                        $role_measure['default'] = 'off';
                                    }
                                    $role_measure_range[] = $role_measure;
                                }
                            } else {
                                $role_measure_range = array(
                                    array(
                                        'default'           => 'on',
                                        'role'              => 'subscriber',
                                        'role_name'         => 'Subscriber',
                                        'multi_measure'     => array(
                                            array(
                                                'caculation_method' => 'area', //CS botak meansure price (5 methods: area, perimeter, top-bottom, left-right, custom-formula)
                                                'minimum_price'     => 0, //CS botak meansure price
                                                'custom_formular'   => '', //CS botak meansure price
                                                'measure_unit'      => 'sq' . nbdesigner_get_option('nbdesigner_dimensions_unit'), //CS botak meansure price
                                                'measure_type'      => 'u', //CS botak meansure price
                                                'measure_range'     => array([])
                                            )
                                        )
                                    )
                                );
                            }

                            $array_measure_price = array(
                                'measure_price'      => isset($general['measure_price']) ? $general['measure_price'] : 'y', //CS botak meansure price
                                'role_measure_range' => $role_measure_range, //CS botak meansure price
                            );
                            $options['fields'][$f_key][$tab] = array_merge($options['fields'][$f_key][$tab], $array_measure_price);
                        };
                        
                        //CS botak option pricing rates
                        if (isset($options['fields'][$f_key]['nbd_type']) && $options['fields'][$f_key]['nbd_type'] === 'pricing_rates' && $tab === 'general') {
                            foreach ($options['fields'][$f_key][$tab]['attributes']['options'] as $key => &$option) {
                                if (isset($option['role_pricing_rates'])) {
                                    //Render role name
                                    foreach ($option['role_pricing_rates'] as &$role_pricing_rate) {
                                        $role_pricing_rate['role_name'] = '';
                                        if (isset($all_roles[$role['role']])) {
                                            $role_pricing_rate['role_name'] = $all_roles[$role_pricing_rate['role']]['name'];
                                        }
                                    }
                                } else {
                                    $option['role_pricing_rates'] = array(
                                        array(
                                            'role'              => 'subscriber',
                                            'role_name'         => 'Subscriber',
                                            'role_rate'         => 0,
                                            'role_quantity'     => 0,
                                            'role_calc_method'  => 'area'
                                        )
                                    );
                                }
                            }
                        }
                        //CS botak calculation option
                        if (isset($options['fields'][$f_key]['nbd_type']) && $options['fields'][$f_key]['nbd_type'] === 'calculation_option' && $tab === 'general') {
                            //$options['fields'][$f_key][$tab]['measure_base_on'] = isset($options['fields'][$f_key][$tab]['measure_base_on']) ? $options['fields'][$f_key][$tab]['measure_base_on'] : 's';
                            foreach ($options['fields'][$f_key][$tab]['attributes']['options'] as $key => &$option) {
                                if (isset($option['role_calculation_option'])) {
                                    //Render role name
                                    foreach ($option['role_calculation_option'] as &$role_calculation_option) {
                                        $role_calculation_option['role_name'] = '';
                                        if (isset($all_roles[$role['role']])) {
                                            $role_calculation_option['role_name'] = $all_roles[$role_calculation_option['role']]['name'];
                                        }
                                    }
                                } else {
                                    $option['role_calculation_option'] = array(
                                        array(
                                            'default'           => 'off',
                                            'role'              => 'subscriber',
                                            'role_name'         => 'Subscriber',
                                            'measure_unit'      => 'sqmm', //CS botak meansure price
                                            'measure_range'     => [[]]
                                        )
                                    );
                                }
                            }
                        }
                        
                        //CS botak product time option
                        if (isset($options['fields'][$f_key]['nbd_type']) && $options['fields'][$f_key]['nbd_type'] === 'production_time' && $tab === 'general') {
                            foreach ($options['fields'][$f_key][$tab]['attributes']['options'] as $key => &$option) {
                                if (!isset($option['time_quantity_breaks'])) {
                                    //Add time_quantity_breaks if null
                                    $option['time_quantity_breaks'] = [];
                                }
                            }
                        }
                    }
                    if( $tab == 'nbd_type' ){
                        $options['fields'][$f_key]['nbd_template'] = 'nbd.'.$data;
                        if( isset($options['fields'][$f_key]['general']['measure'])){
                            if( !isset($options['fields'][$f_key]['general']['measure_range']) ) $options['fields'][$f_key]['general']['measure_range'] = array();
                            if( !isset($options['fields'][$f_key]['general']['measure_base_pages']) ) $options['fields'][$f_key]['general']['measure_base_pages'] = 'y';
                            if( !isset($options['fields'][$f_key]['general']['measure_type']) ) $options['fields'][$f_key]['general']['measure_type'] = 'u';
                        }
                    }
                    if( $tab == 'nbpb_type' || $tab == 'nbe_type' ){
                        $options['fields'][$f_key]['nbd_template'] = 'nbd.'.$data;
                    }
                }
            }
            if( isset($options['views']) ){
                foreach ($options['views'] as $vkey => $view){
                    $view['base'] = isset($view['base']) ? $view['base'] : 0;
                    $options['views'][$vkey]['base'] = $view['base'];
                    $options['views'][$vkey]['base_url'] = nbd_get_image_thumbnail( $view['base'] );
                }
            }
            return $options;
        }
        public function recursive_stripslashes( $fields ){
            $valid_fields = array();
            foreach($fields as $key => $field){
                if(is_array($field) ){
                    $valid_fields[$key] = $this->recursive_stripslashes($field);
                }else if(!is_null($field)){
                    $valid_fields[$key] = stripslashes($field);
                }
            }
            return $valid_fields;
        }
        public function default_field(){
            return array(
                'id'    =>  'f' . round(microtime(true) * 1000),
                'general' => array(
                    'title' =>  null,
                    'description'       => null,
                    'agreement_clause'  => null, // CS-V3 add new field
                    'data_type'         => null,
                    'input_type'        => null,
                    'input_option'      => null,
                    'text_option'       => null,
                    'placeholder'       => null,
                    'upload_option'     => null,
                    'enabled'           => null,
                    'required'          => null,
                    'published'         => null,
                    'price_type'        => null,
                    'depend_quantity'   => null,
                    'depend_qty'        => null,
                    'price'             => null,
                    'price_breaks'      => null,
                    'attributes'        => null
                ),
                'conditional' => array(
                    'enable'    => 'n',
                    'show'      => 'y',
                    'logic'     => 'a',
                    'depend'    => null
                ),
                'appearance' => array(
                    'display_type'          => null,
                    'change_image_product'  => null,
                    'show_in_archives'      => null,
                    'different_display_type'          => null, //CS botak different display type 
//                    'different_show_in_archives'      => null, //CS botak different display type 
                    'css_class'             => null
                )
            ); 
        }
        public function default_config_field(){
            $field = $this->default_field();
            foreach ($field as $tab =>  $data){
                if( $tab != 'id' ){
                    foreach ($data as $key => $f){
                        $funcname = "build_config_".$tab.'_'.$key;
                        if( is_callable( array( $this, $funcname ) ) ){
                            $field[$tab][$key] = $this->$funcname($f);
                        }
                    }
                }
            }
            return $field;
        }
        public function build_config_general_title( $value = null ){
            if (is_null($value)) $value = __( 'Option name', 'web-to-print-online-designer');
            return array(
                'title'         => __( 'Option name', 'web-to-print-online-designer'),
                'description'   =>  '',
                'value'         => $value,
                'type'          => 'text'
            );
        }
        public function build_config_general_description( $value = null ){
            if (is_null($value)) $value = __( 'Option description', 'web-to-print-online-designer');
            return array(
                'title'         => __( 'Description', 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'textarea'
            );
        }
        // CS-V3 add new field
        public function build_config_general_agreement_clause( $value = null ){
            if (is_null($value)) $value = __( "I've read and agree with the above", 'web-to-print-online-designer');
            return array(
                'title'         => __( "I've read and agree with the above", 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'textarea'
            );
        }
        public function build_config_general_data_type( $value = null ){
            if (is_null($value)) $value = 'm';
            return array(
                'title'         => __( 'Data type', 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'       => 'i',
                        'text'      => __( 'Custom input', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'm',
                        'text'      => __( 'Multiple options', 'web-to-print-online-designer')
                    )
                )
            );
        } 
        public function build_config_general_input_type( $value = null ){
            if (is_null($value)) $value = 't';
            return array(
                'title'         => __( 'Input type', 'web-to-print-online-designer'),
                'description'   =>  '',
                'value'         => $value,
                'type'          => 'dropdown',
                'depend'        => array(
                    array(
                        'field'     =>  'data_type',
                        'operator'  =>  '=',
                        'value'     =>  'i'
                    )
                ),
                'options'       => array(
                    array(
                        'key'       => 't',
                        'text'      => __( 'Text', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'n',
                        'text'      => __( 'Number', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'r',
                        'text'      => __( 'Number range', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'u',
                        'text'      => __( 'Upload', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'a',
                        'text'      => __( 'Textarea', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_general_input_option( $value = null ){
            if (is_null($value)){$value = array(
                'min'       => 1,
                'max'       => 100,
                'step'      => 1,
                'default'   => 1
            );}
            if( !isset( $value['default'] ) ) $value['default'] = $value['min'];
            return array(
                'title'         => __( 'Input option', 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'table',
                'depend'        => array(
                    array(
                        'field'     => 'data_type',
                        'operator'  => '=',
                        'value'     => 'i'
                    ), 
                    array(
                        'field'     => 'input_type',
                        'operator'  => '#',
                        'value'     => 't'
                    ),
                    array(
                        'field'     => 'input_type',
                        'operator'  => '#',
                        'value'     => 'u'
                    ),
                    array(
                        'field'     => 'input_type',
                        'operator'  => '#',
                        'value'     => 'a'
                    )
                )
            );
        }
        public function build_config_general_text_option( $value = null ){
            if (is_null($value)){$value = array(
                'min'   =>  0,
                'max'   =>  999
            );}
            return array(
                'title'         => __( 'Text input option', 'web-to-print-online-designer'),
                'description'   =>  '',
                'value'         => $value,
                'type'          => 'table',
                'depend'        =>  array(
                    array(
                        'field'     =>  'data_type',
                        'operator'  =>  '=',
                        'value'     =>  'i'
                    ),
                    array(
                        'field'     =>  'input_type',
                        'operator'  =>  '=',
                        'value'     =>  't,a'
                    )
                )
            );
        }
        public function build_config_general_placeholder( $value = null ){
            if ( is_null( $value ) ) $value = '';
            return array(
                'title'         => __( 'Placeholder', 'web-to-print-online-designer'),
                'description'   =>  '',
                'value'         => $value,
                'type'          => 'text',
                'depend'        =>  array(
                    array(
                        'field'     =>  'data_type',
                        'operator'  =>  '=',
                        'value'     =>  'i'
                    ),
                    array(
                        'field'     =>  'input_type',
                        'operator'  =>  '=',
                        'value'     =>  't,a'
                    )
                )
            );
        }
        public function build_config_general_upload_option( $value = null ){
            if (is_null($value)){$value = array(
                'min_size'      =>  0,
                'max_size'      =>  nbd_get_max_upload_default(),
                'allow_type'    =>  'png,jpg,jpeg'
            );}
            return array(
                'title'         => __( 'Upload file option', 'web-to-print-online-designer'),
                'description'   =>  '',
                'value'         => $value,
                'type'          => 'table',
                'depend'        =>  array(
                    array(
                        'field'     =>  'data_type',
                        'operator'  =>  '=',
                        'value'     =>  'i'
                    ), 
                    array(
                        'field'     =>  'input_type',
                        'operator'  =>  '=',
                        'value'     =>  'u'
                    )
                )
            );
        }
        public function build_config_general_enabled( $value = null ){
            if (is_null($value)) $value = 'y';
            return array(
                'title'         => __( 'Enabled', 'web-to-print-online-designer'),
                'description'   => __( 'Choose whether the option is enabled or not.', 'web-to-print-online-designer'),
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'       => 'y',
                        'text'      => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'n',
                        'text'      => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_general_published( $value = null ){
            if (is_null($value)) $value = 'y';
            return array(
                'title'         => __( 'Published', 'web-to-print-online-designer'),
                'description'   => __( 'Show in summary options or not.', 'web-to-print-online-designer'),
                'value'         => $value,
                'type'          => 'dropdown',
                'options' =>    array(
                    array(
                        'key'       => 'y',
                        'text'      => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'n',
                        'text'      => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_general_required( $value = null ){
            if (is_null($value)) $value = 'n';
            return array(
                'title'         => __( 'Required', 'web-to-print-online-designer'),
                'description'   => __( 'Choose whether the option is required or not.' ),
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'       => 'y',
                        'text'      => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'n',
                        'text'      => __( 'No', 'web-to-print-online-designer')
                    )
                ),
                'depend'        => array(
                    array(
                        'field'     => 'published',
                        'operator'  => '#',
                        'value'     => 'n'
                    )
                )
            );
        } 
        public function build_config_general_price_type( $value = null ){
            if (is_null($value)) $value = 'f';
            return array(
                'title'         => __( 'Price type', 'web-to-print-online-designer'),
                'description'   => __( 'Here you can choose how the price is calculated. Depending on the field there various types you can choose.' ),
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'       => 'f',
                        'text'      => __( 'Fixed amount', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'p',
                        'text'      => __( 'Percent of the original price', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'p+',
                        'text'      => __( 'Percent of the original price + options', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'       => 'c',
                        'text'      => __( 'Current value * price', 'web-to-print-online-designer'),
                        'depend'    => array(
                            array(
                                'field'     => 'data_type',
                                'operator'  => '=',
                                'value'     => 'i'
                            ),
                            array(
                                'field'     => 'input_type',
                                'operator'  => '#',
                                'value'     => 'u'
                            ),
                            array(
                                'field'     => 'input_type',
                                'operator'  => '#',
                                'value'     => 't'
                            ),
                            array(
                                'field'     => 'input_type',
                                'operator'  => '#',
                                'value'     => 'a'
                            )
                        )
                    ),
                    array(
                        'key'       => 'cp',
                        'text'      => __( 'Price per char', 'web-to-print-online-designer'),
                        'depend'    => array(
                            array(
                                'field'     => 'data_type',
                                'operator'  => '=',
                                'value'     => 'i'
                            ),
                            array(
                                'field'     => 'input_type',
                                'operator'  => '=',
                                'value'     => 't'
                            )
                        )
                    )
                )
            );
        }
        public function build_config_general_depend_quantity( $value = null ){
            if (is_null($value)) $value = 'n';
            return array(
                'title'         => __( 'Depend quantity breaks', 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'   => 'y',
                        'text'  => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'n',
                        'text'  => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_general_depend_qty( $value = null ){
            if (is_null($value)) $value = 'y';
            return array(
                'title'         => __( 'Depend quantity', 'web-to-print-online-designer'),
                'description'   => 'If choose No, the additional price will be apply for cart item independently with the quantity.',
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'   => 'y',
                        'text'  => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'n',
                        'text'  => __( 'No, the additional price is cart item fee', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'n2',
                        'text'  => __( 'No, the additional price is fixed amount for all items', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_general_price( $value = null ){
            if (is_null($value)) $value = '';
            return array(
                'title'         => __( 'Additional Price', 'web-to-print-online-designer'),
                'description'   => __( 'Enter the price for this field or leave it blank for no price.' ),
                'value'         => $value,
                'depend'        => array(
                    array(
                        'field'     => 'depend_quantity',
                        'operator'  => '#',
                        'value'     => 'y'
                    ),
                    array(
                        'field'     => 'data_type',
                        'operator'  => '=',
                        'value'     => 'i'
                    )
                ),
                'type'          => 'number'
            );
        }
        public function build_config_general_price_breaks( $value = null ){
            if (is_null($value)) $value = array();
            return array(  
                'title'     => __( 'Price depend quantity breaks', 'web-to-print-online-designer'),
                'depend'    =>  array(
                    array(
                        'field'     =>  'depend_quantity',
                        'operator'  =>  '=',
                        'value'     =>  'y'
                    ),
                    array(
                        'field'     =>  'data_type',
                        'operator'  =>  '=',
                        'value'     =>  'i'
                    )
                ),
                'description'   => '',
                'value'         => $value,
                'type'          => 'single_quantity_depend'
            );
        }
        public function build_config_general_attributes( $attributes = null ){
            if (is_null($attributes)){ $options = array(
                    0 => array(
                        'name'                  => __( 'Attribute name', 'web-to-print-online-designer'),
                        'des'                   => '',
                        'price'                 => array(),
                        'selected'              => 0,
                        'enable_subattr'        => 0,
                        'preview_type'          => 'i',
                        'image'                 => 0,
                        'image_url'             => '',
                        'product_image'         => 0,
                        'product_image_url'     => '',
                        'product_images'        => [],
                        'overlay_image'         => 0, //CS botak overlay option
                        'overlay_image_url'     => '',
                        'color'                 => '#ffffff',
                        'sub_attributes'        => array(),
                        'sattr_display_type'    => 's',
                        'enable_con'            => 0,
                        'con_show'              => 'n',
                        'con_logic'             => 'a',
                        'depend'                => array(
                            0   => array(
                                'id'        => '',
                                'operator'  => 'i',
                                'val'       => '',
                                'subval'    => ''
                            )
                        ),
                        /* CS botak gallery option
                        'gallery_enable_con'            => 0,
                        'gallery_con_logic'             => 'a',
                        'gallery_depend'                => array(
                            0   => array(
                                'id'        => '',
                                'operator'  => 'i',
                                'val'       => '',
                                'subval'    => ''
                            )
                        ) */
                    )
                );
            } else {
                $options = $attributes['options'];
            };
            foreach( $options as $key => $option){
                $options[$key]['enable_subattr']     = isset( $options[$key]['enable_subattr'] ) ? $options[$key]['enable_subattr'] : 0;
                $options[$key]['sub_attributes']     = isset( $options[$key]['sub_attributes'] ) ? $options[$key]['sub_attributes'] : array();
                $options[$key]['sattr_display_type'] = isset( $options[$key]['sattr_display_type'] ) ? $options[$key]['sattr_display_type'] : 's';
                $options[$key]['enable_con']         = isset( $options[$key]['enable_con'] ) ? $options[$key]['enable_con'] : 0;
                $options[$key]['con_show']           = isset( $options[$key]['con_show'] ) ? $options[$key]['con_show'] : 'n';
                $options[$key]['con_logic']          = isset( $options[$key]['con_logic'] ) ? $options[$key]['con_logic'] : 'a';
                $options[$key]['depend']             = ( isset( $option['depend'] ) && count( $option['depend'] ) ) ? $option['depend'] : array( 0 => array( 'id' => '', 'operator' => 'i', 'val' => '', 'subval' => '' ) );
//                $options[$key]['gallery_depend']     = ( isset( $option['gallery_depend'] ) && count( $option['gallery_depend'] ) ) ? $option['gallery_depend'] : array( 0 => array( 'id' => '', 'operator' => 'i', 'val' => '', 'subval' => '' ) );
//                $options[$key]['gallery_con_logic']  = isset( $options[$key]['gallery_con_logic'] ) ? $options[$key]['gallery_con_logic'] : 'a';
//                $options[$key]['gallery_enable_con'] = isset( $options[$key]['gallery_enable_con'] ) ? $options[$key]['gallery_enable_con'] : 0;
                
                if( isset($option['enable_subattr']) ){
                    foreach( $options[$key]['sub_attributes'] as $sak => $sa ){
                        $options[$key]['sub_attributes'][$sak]['enable_con']    = isset( $sa['enable_con'] ) ? $sa['enable_con'] : 0;
                        $options[$key]['sub_attributes'][$sak]['con_show']      = isset( $sa['con_show'] ) ? $sa['con_show'] : 'n';
                        $options[$key]['sub_attributes'][$sak]['con_logic']     = isset( $sa['con_logic'] ) ? $sa['con_logic'] : 'a';
                        $options[$key]['sub_attributes'][$sak]['depend']        = ( isset( $sa['depend'] ) && count( $sa['depend'] ) ) ? $sa['depend'] : array( 0 => array( 'id' => '', 'operator' => 'i', 'val' => '', 'subval' => '' ) );
                    }
                }
                
                $options[$key]['image_url']          = nbd_get_image_thumbnail( $option['image'] );
                if( isset($options[$key]['product_image']) ){
                    $options[$key]['product_image_url'] = nbd_get_image_thumbnail( $option['product_image'] );
                }
                //CS botak change gallery image
                if( isset($options[$key]['product_images']) ){
                    foreach ($options[$key]['product_images'] as &$img) {
                        $img['product_image_url'] = nbd_get_image_thumbnail( $img['product_image'] );
                    }
                } else {
                    $options[$key]['product_images'] = [];
                }
                //CS botak overlay in option
                if( isset($options[$key]['overlay_image']) ){
                    $options[$key]['overlay_image_url'] = nbd_get_image_thumbnail( $option['overlay_image'] );
                }
                if( isset( $attributes['bg_type'] ) ){
                    if( $attributes['bg_type'] == 'i' ){
                        foreach( $option['bg_image'] as $k => $bg ){
                            $options[$key]['bg_image_url'][$k] = nbd_get_image_thumbnail( $bg );
                        }
                    }else{
                        $options[$key]['bg_image']      = array();
                        $options[$key]['bg_image_url']  = array();
                    }
                }
                if( isset( $option['enable_subattr'] ) ){
                    foreach( $options[$key]['sub_attributes'] as $sak => $sa ){
                        $options[$key]['sub_attributes'][$sak]['image_url'] = nbd_get_image_thumbnail( $sa['image'] );
                    }
                }
                if( isset( $option['overlay_image'] )  && is_array($option['overlay_image']) ){
                    foreach( $option['overlay_image'] as $k => $ov ){
                        $options[$key]['overlay_image_url'][$k] = nbd_get_image_thumbnail( $ov );
                    }
                }
                if( isset( $option['frame_image'] ) ){
                    $options[$key]['frame_image_url'] = nbd_get_image_thumbnail( $option['frame_image'] );
                }
            }
            $same_size          = isset($attributes['same_size']) ? $attributes['same_size'] : 'y';
            $bg_type            = isset($attributes['bg_type']) ? $attributes['bg_type'] : 'i';
            $show_as_pt         = isset($attributes['show_as_pt']) ? $attributes['show_as_pt'] : 'n';
            $number_of_sides    = isset($attributes['number_of_sides']) ? $attributes['number_of_sides'] : 2;
            return array(
                'title'             => __( 'Attributes', 'web-to-print-online-designer'),
                'description'       => __( 'Attributes let you define extra product data, such as size or color.'),
                'type'              => 'attributes',
                'same_size'         => $same_size,
                'bg_type'           => $bg_type,
                'show_as_pt'        => $show_as_pt,
                'number_of_sides'   => $number_of_sides,
                'depend'            =>  array(
                    array(
                        'field'     => 'data_type',
                        'operator'  => '=',
                        'value'     => 'm'
                    )
                ), 
                'options'         => $options
            );
        }
        public function build_config_general_pb_config($configs){
            foreach( $configs as $key => $o_config){
                foreach( $o_config as $skey => $so_config){
                    foreach( $so_config['views'] as $vkey => $view){
                        $configs[$key][$skey]['views'][$vkey]['display']    = (isset($view['display']) && $view['display'] == 'on') ? true : false;
                        $configs[$key][$skey]['views'][$vkey]['image_url']  = nbd_get_image_thumbnail( $view['image'] );
                    }
                }
            }
            return $configs;
        }
        public function build_config_general_nbpb_text_configs($configs){
            if( !isset($configs['views']) ) $configs['views'] = array();
            foreach( $configs['views'] as $key => $view){
                $configs['views'][$key]['display'] = (isset($view['display']) && $view['display'] == 'on') ? true : false;
            }
            return $configs;
        }
        public function build_config_general_nbpb_image_configs($configs){
            if( !isset($configs['views']) ) $configs['views'] = array();
            foreach( $configs['views'] as $key => $view){
                $configs['views'][$key]['display'] = (isset($view['display']) && $view['display'] == 'on') ? true : false;
            }
            return $configs;
        }
        public function build_config_appearance_display_type( $value = null ){
            if (is_null($value)) $value = 'ad';
            return array(  
                'title'         => __( 'Display type', 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'   => 'd',
                        'text'  => __( 'Dropdown', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'r',
                        'text'  => __( 'Radio button', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 's',
                        'text'  => __( 'Swatch', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'l',
                        'text'  => __( 'Label', 'web-to-print-online-designer')
                    ),    
                     array(
                        'key'   => 'ad',
                        'text'  => __( 'Advanced Dropdown', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'xl',
                        'text'  => __( 'Large label', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_appearance_change_image_product( $value = null ){
            if (is_null($value)) $value = 'n';
            return array(  
                'title'         => __( 'Changes product image', 'web-to-print-online-designer'),
                'description'   => __('Choose whether to change the product image.', 'web-to-print-online-designer'),
                'type'          => 'dropdown',
                'value'         => $value,
                'options'       => array(
                    array(
                        'key'   => 'y',
                        'text'  => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'n',
                        'text'  => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_appearance_css_class( $value = null ){
            if (is_null($value)) $value = '';
            return array(
                'title'         => __( 'CSS Class', 'web-to-print-online-designer'),
                'description'   => '',
                'type'          => 'text',
                'value'         => $value
            );
        }
        public function build_config_appearance_show_in_archives( $value = null ){
            if (is_null($value)) $value = 'n';
            return array(  
                'title'         => __( 'Show in archive pages', 'web-to-print-online-designer'),
                'description'   => __('Show option in archive pages as swatch.', 'web-to-print-online-designer'),
                'type'          => 'dropdown',
                'value'         => $value,
                'options'       => array(
                    array(
                        'key'   => 'y',
                        'text'  => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'n',
                        'text'  => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }  
        public function build_config_appearance_different_show_in_archives( $value = null ){
            if (is_null($value)) $value = 'n';
            return array(  
                'title'         => __( 'Different display type in archive pages', 'web-to-print-online-designer'),
                'description'   => __('', 'web-to-print-online-designer'),
                'type'          => 'dropdown',
                'value'         => $value,
                'options'       => array(
                    array(
                        'key'   => 'y',
                        'text'  => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'   => 'n',
                        'text'  => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }  
        public function build_config_appearance_different_display_type( $value = null ){
            if (is_null($value)) $value = 'l';
            return array(  
                'title'         => __( 'Display type (Archive Pages', 'web-to-print-online-designer'),
                'description'   => '',
                'value'         => $value,
                'type'          => 'dropdown',
                'options'       => array(
                    array(
                        'key'   => 'l',
                        'text'  => __( 'Label', 'web-to-print-online-designer')
                    ),   
                    array(
                        'key'   => 's',
                        'text'  => __( 'Swatch', 'web-to-print-online-designer')
                    ), 
                     array(
                        'key'   => 'b',
                        'text'  => __( 'Both', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_appearance_quantity_selector( $value = null ){
            if (is_null($value)) $value = 'n';
            return array(  
                'title'         => __( 'Quantity selector', 'web-to-print-online-designer'),
                'description'   => __('This will show a quantity selector for this option.', 'web-to-print-online-designer'),
                'type'          => 'dropdown',
                'value'         => $value,
                'options'       => array(
                    array(
                        'key'    => 'y',
                        'text'   => __( 'Yes', 'web-to-print-online-designer')
                    ),
                    array(
                        'key'    => 'n',
                        'text'   => __( 'No', 'web-to-print-online-designer')
                    )
                )
            );
        }
        public function build_config_conditional_enable( $value = null ){
            if (is_null($value)) $value = 'n';
            return $value;
        }
        public function build_config_conditional_show( $value = null ){
            if (is_null($value)) $value = 'n';
            return $value;
        } 
        public function build_config_conditional_logic( $value = null ){
            if (is_null($value)) $value = 'a';
            return $value;
        } 
        public function build_config_conditional_depend( $value = null ){
            if (is_null($value) || count($value) == 0) $value = array(
                0   =>  array(
                    'id'        => '',
                    'operator'  => 'i',
                    'val'       => ''
                )
            );
            return $value;
        }
        public function product_write_panel_tab(){
            ?>
            <li class="nbo_mapping show_if_variable">
                <a href="#nbo_mapping">
                    <?php _e('Map print options', 'web-to-print-online-designer'); ?>
                </a>
            </li>
            <?php
        }
        public function product_data_panel(){
            include_once(NBDESIGNER_PLUGIN_DIR .'views/options/options-mapping.php');
        }
        public function process_product_meta( $post_id, $post ){
            $product = wc_get_product( $post_id );

            $enable_mapping = isset( $_POST['_enable_nbo_mapping'] ) ? $_POST['_enable_nbo_mapping'] : 0;

            if( $enable_mapping == '1' ){
                $maps = isset( $_POST['_nbo_maps'] ) ? $_POST['_nbo_maps'] : array();
                $product->update_meta_data( '_nbo_maps', $maps );
            }

            $product->update_meta_data( '_enable_nbo_mapping', $enable_mapping );
            $product->save_meta_data();
        }
    }
}
function nbd_option_i18n(){
    return array(
        'page'                  => __('Sides/Pages', 'web-to-print-online-designer'),
        'page1'                 => __('Number of Pages', 'web-to-print-online-designer'),
        'page2'                 => __('Side list', 'web-to-print-online-designer'),
        'page3'                 => __('Front/Back Sides', 'web-to-print-online-designer'),
        'color'                 => __('Color', 'web-to-print-online-designer'),
        'size'                  => __('Size', 'web-to-print-online-designer'),
        'dpi'                   => __('DPI', 'web-to-print-online-designer'),
        'area'                  => __('Area design shape', 'web-to-print-online-designer'),
        'orientation'           => __('Orientation', 'web-to-print-online-designer'),
        'dimension'             => __('Custom dimension', 'web-to-print-online-designer'),
        'padding'               => __('Padding', 'web-to-print-online-designer'),
        'rounded_corner'        => __('Rounded corner', 'web-to-print-online-designer'),
        'nbpb_com'              => __('Component', 'web-to-print-online-designer'),
        'nbpb_text'             => __('Text', 'web-to-print-online-designer'),
        'nbpb_image'            => __('Image', 'web-to-print-online-designer'),
        'dpi_description'       => __('DPI is used to describe the resolution number of dots per inch in a digital print and the printing resolution of a hard copy print dot gain, which is the increase in the size of the halftone dots during printing.', 'web-to-print-online-designer'),
        'vertical'              => __('Vertical', 'web-to-print-online-designer'),
        'horizontal'            => __('Horizontal', 'web-to-print-online-designer'),
        'can_not_add_att'       => __('Can not add more attribute for this option.', 'web-to-print-online-designer'),
        'can_not_remove_att'    => __('Can not remove this attribute.', 'web-to-print-online-designer'),
        'rectangle'             => __('Rectangle', 'web-to-print-online-designer'),
        'ellipse'               => __('Ellipse', 'web-to-print-online-designer'),
        'attribute_name'        => __('Attribute name', 'web-to-print-online-designer'),
        'sub_attribute_name'    => __('Sub attribute name', 'web-to-print-online-designer'),
        'can_not_copy'          => __('Can not copy this option.', 'web-to-print-online-designer'),
        'option_exist'          => __('This option exist.', 'web-to-print-online-designer'),
        'front'                 => __('Front & Back', 'web-to-print-online-designer'),
        'back'                  => __('Back side', 'web-to-print-online-designer'),
        'one_side'              => __('1 side', 'web-to-print-online-designer'),
        'both'                  => __('Both sides', 'web-to-print-online-designer'),
        'want_to_delete'        => __('Are you sure you want to delete this field?', 'web-to-print-online-designer'),
        'want_to_delete_all'    => __('Are you sure you want to delete all fields?', 'web-to-print-online-designer'),
        'choose_image'          => __('Choose Image', 'web-to-print-online-designer'),
        'view_name'             => __('View name', 'web-to-print-online-designer'),
        'max_input_var'         => __('PHP max input vars:', 'web-to-print-online-designer'),
        'max_input_notice'      => __('Please increase "PHP max input vars"!', 'web-to-print-online-designer'),
        'group_title'           => __('Group name', 'web-to-print-online-designer'),
        'group_des'             => __('Group description', 'web-to-print-online-designer'),
        'group_note'            => __('Group note', 'web-to-print-online-designer'),
        'delivery'              => __('Delivery', 'web-to-print-online-designer'),
        'delivery_3_days'       => __('3 days', 'web-to-print-online-designer'),
        'actions'               => __('Artwork actions', 'web-to-print-online-designer'),
        'no_thank'              => __('No thank you', 'web-to-print-online-designer'),
        'upload_design'         => __('Upload your design', 'web-to-print-online-designer'),
        'create_your_own'       => __('Create your design online', 'web-to-print-online-designer'),
        'hire_designer'         => __('Want the experts design for you', 'web-to-print-online-designer'),
        'overlay'               => __('Overlay', 'web-to-print-online-designer'),
        'fold'                  => __('Folding Styles', 'web-to-print-online-designer'),
        'pricing_rates'         => __('Pricing Rates', 'web-to-print-online-designer'), //CS botak pricing rate option
        'production_time'       => __('Production Time', 'web-to-print-online-designer'), //CS botak production time option
        'calculation_option'    => __('Calculation Option', 'web-to-print-online-designer'), //CS botak calculation option
        'terms_conditions'      => __('Terms & Conditions' , 'web-to-print-online-designer'), // CS-V3 add new Terms Condistions
        'frame'                 => __('Frame options', 'web-to-print-online-designer'),
        'number_file'           => __('Number of upload files', 'web-to-print-online-designer'),
        'shape_name'            => __('Shape Name', 'web-to-print-online-designer'),
        'shape'                 => __('Area Design Shape', 'web-to-print-online-designer')

    );
}
$nbd_printing_options = NBD_ADMIN_PRINTING_OPTIONS::instance();
$nbd_printing_options->init();