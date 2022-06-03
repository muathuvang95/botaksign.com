<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('NBD_FRONTEND_PRINTING_OPTIONS')){
    class NBD_FRONTEND_PRINTING_OPTIONS {
        protected static $instance;
        public $is_edit_mode = FALSE;
        /** Holds the cart key when editing a product in the cart **/
        public $cart_edit_key = NULL;
        /** Edit option in cart helper **/
        public $new_add_to_cart_key = FALSE;
        public function __construct() {
            if ( isset( $_REQUEST['nbo_cart_item_key'] ) && $_REQUEST['nbo_cart_item_key'] != '' ){
                $this->is_edit_mode = true;
                $this->cart_edit_key = $_REQUEST['nbo_cart_item_key'];
            }
        }
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
    }
        public function init(){
            add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'show_option_fields' ) );
            add_filter( 'nbd_js_object', array($this, 'nbd_js_object') );
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
            add_action('wp_footer', array($this, 'nbd_load_ajax') , 999);
            
            /* Edit cart item */
            // handle customer input as order item meta
            add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
            // Alters add to cart text when editing a product
            add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'add_to_cart_text' ), 9999, 1 );
            // Remove product from cart when editing a product
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'remove_previous_product_from_cart' ), 99999, 6 );
            // Alters the cart item key when editing a product
            add_action( 'woocommerce_add_to_cart', array( $this, 'add_to_cart' ), 10, 6 );
            // Change quantity value when editing a cart item
            add_filter( 'woocommerce_quantity_input_args', array( $this, 'quantity_input_args' ), 9999, 2 );
            // Redirect to cart when updating information for a cart item
            add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'add_to_cart_redirect' ), 9999, 1 );
            // Calculate totals on remove from cart/update
            //add_action( 'woocommerce_before_calculate_totals', array( $this, 'on_calculate_totals' ), 1, 1 );
            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 're_calculate_price' ), 1, 1 );
            // Add meta to order
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 50, 3 );
            // Change option independent quantity prices to cart fee
            add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cart_fee' ), 1, 1 );
            // Gets saved option when using the order again function
            add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 50, 3 );
                
            // Alter the product thumbnail in cart
            add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 50, 2 );
            // Remove item quantity in checkout
            add_filter( 'woocommerce_checkout_cart_item_quantity', array($this, 'remove_cart_item_quantity'), 10, 3);
            // Adds edit link on product title in cart and item quantity
            add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 50, 3 );
            
            // Add item data to the cart
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 4 );
            
            // persist the cart item data, and set the item price (when needed) first, before any other plugins
            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 1, 2 );

            // on add to cart set the price when needed, and do it first, before any other plugins
            add_filter( 'woocommerce_add_cart_item', array($this, 'set_product_prices'), 1, 1 );
            // Validate upon adding to cart
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 1, 6 );

            // block add to cart when price = 0 and not upload , design
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'block_add_to_cart_none_price' ), 99999, 6 );
            
            /** Force Select Options **/
            if( nbdesigner_get_option('nbdesigner_force_select_options', 'no') == 'yes' ){
                add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
                //add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );
            }
            add_filter( 'woocommerce_cart_redirect_after_error', array( $this, 'cart_redirect_after_error' ), 50, 2 );
            
            /* Disables persistent cart **/
            if( nbdesigner_get_option('nbdesigner_turn_off_persistent_cart', 'no') == 'yes' ){
                add_filter( 'get_user_metadata', array( $this, 'turn_off_persistent_cart' ), 10, 3 );
                add_filter( 'update_user_metadata', array( $this, 'turn_off_persistent_cart' ), 10, 3 );
                add_filter( 'add_user_metadata', array( $this, 'turn_off_persistent_cart' ), 10, 3 );
                add_filter( 'woocommerce_persistent_cart_enabled', '__return_false' );
            }
            
            /** Empty cart button **/
            if( nbdesigner_get_option('nbdesigner_enable_clear_cart_button', 'no') == 'yes' ){
                add_action( 'woocommerce_cart_actions', array( $this, 'add_empty_cart_button' ) );
                // check for empty-cart get param to clear the cart
                add_action( 'init', array( $this, 'clear_cart' ) );
            }
            
            /* Bulk order */
            if ( isset( $_POST['nbb-fields'] ) && ( ! defined('DOING_AJAX') || ! DOING_AJAX ) ) {
                add_action( 'wp_loaded', array( $this, 'bulk_order' ), 20 );
            }
            add_action( 'nbdq_bulk_order', array( $this, 'bulk_order' ), 20 );
            
            /* Quick view */
            add_action( 'woocommerce_api_nbo_quick_view', array( $this, 'quick_view' ) );
            add_action( 'woocommerce_before_variations_form', array( $this, 'action_woocommerce_before_variations_form'), 10, 0 ); 
            
            if( nbdesigner_get_option('nbdesigner_change_base_price_html', 'no') == 'yes' ){
                add_filter( 'woocommerce_get_price_html', array( $this, 'change_product_price_display'), 10, 2 );
            }
            /* Compatible Autoptimize */
            add_filter( 'option_autoptimize_js_exclude', array( $this, 'autoptimize_js_exclude') );
            
            /* AJAX */
            $this->ajax();
            if( nbdesigner_get_option('nbdesigner_enable_ajax_cart', 'no') == 'yes' ){
                add_action('wp_footer', array($this, 'print_popup_ajax_cart'));
                add_filter( 'woocommerce_loop_add_to_cart_link', array(&$this, 'add_to_cart_shop_link'), 20, 2 );
                add_filter( 'nbd_depend_js', array($this, 'nbd_depend_js') );
            }
            
            /* Printing information tab */
            add_filter( 'woocommerce_product_tabs', array( $this, 'printing_tab' ) );
            
            /* Show option in archives */
            if( nbdesigner_get_option('nbdesigner_show_options_in_archive_pages', 'no') == 'yes' ){
                add_action( 'woocommerce_after_shop_loop_item', array(&$this, 'show_options_in_archive_pages'), 10 );
            }

            /* Rich snippet price */
            if( nbdesigner_get_option('nbdesigner_enbale_rich_snippet_price', 'no') == 'yes' ){
                add_filter( 'woocommerce_structured_data_product_offer', array($this, 'update_rich_snippet_price'), 10, 2 );
            }

            if( nbdesigner_get_option('nbdesigner_enable_map_print_options', 'no') == 'yes' ){
                add_action( 'woocommerce_delete_product_transients', array($this, 'on_delete_product_transients'), 10, 1 );
                add_filter('woocommerce_dropdown_variation_attribute_options_args', array($this, 'nbo_dropdown_variation_attribute_options_args'), 20, 1);
            }
        }
        public function ajax(){
            $ajax_events = array(
                'nbo_ajax_cart'                 => true,
                'nbo_get_product_variations'    => true
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
            add_action('wp_ajax_get_content_product', array($this, 'nbo_get_content_product'));
            add_action('wp_ajax_nopriv_get_content_product', array($this, 'nbo_get_content_product'));
        }
        public function block_add_to_cart_none_price($passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ){
            if(isset($_GET['order_again']) && $_GET['order_again'] > 0) {
                return true;
            }
            $passed             = true;
            $post_data          = $_POST;
            $option_id          = $this->get_product_option($product_id);
            $options            = $this->get_option($option_id);
            $product            = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
            $original_price     = (float)$product->get_price('edit');
            $nbd_field          = isset($post_data['nbd-field']) ? $post_data['nbd-field'] : array();
            // $has_design         = false;
            // $has_upload         = false;
            $nbd_item_cart_key  = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id;
            $nbd_session        = WC()->session->get('nbd_item_key_'.$nbd_item_cart_key);
            $nbu_session        = WC()->session->get('nbu_item_key_'.$nbd_item_cart_key);
            $option_price   = $this->option_processing( $options, $original_price, $nbd_field, $qty );
            $total_price = $option_price['total_price'];
            $enable_po = get_post_meta($product_id, '_nbo_enable', true);
            $enabled_design = get_post_meta($product_id, '_nbdesigner_enable', true);
            $enabled_upload =  get_post_meta($product_id, '_nbdesigner_enable_upload', true);
            $enabled_upload_design =  get_post_meta($product_id, '_nbdesigner_enable_upload_without_design', true);
            if($enable_po) {
                if(  !isset($post_data['nbd-field']) && !isset($post_data['nbo-add-to-cart']) && $total_price == 0 && $original_price == 0){
                    $passed = false;
                }
            } else {
                if($original_price == 0) {
                    $passed = false;
                }
            }

            if( $enabled_design ) {
                $passed = false;
            }

            $file_upload = array();
            if($nbu_session) {
                $file_upload = botak_get_list_file_s3( 'reupload-design/'. $nbu_session );
            }
            // if( count($file_upload) > 0 && isset($_POST['nbd-upload-files']) && $_POST['nbd-upload-files']  ){
            if( count($file_upload) > 0 && isset($_POST['nbd-upload-files']) && $_POST['nbd-upload-files']  ){
                $passed = true;
            }

            if( $nbd_session ){
                $passed = true;
            }
            return $passed;
        }
        public function add_to_cart_shop_link( $handler, $product ){
            $product_id = $product->get_id();
            $type       = $product->get_type();
            $need_qv    = false;
            if( $type != 'simple' && $type != 'variable' ){
                return $handler;
            }
            if( $type == 'variable' ){
                $need_qv    = true;
            }else{
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $need_qv    = true;
                }
            }
            ob_start();
            nbdesigner_get_template( 'loop/ajax-add-to-cart-button.php', array( 'need_qv' => $need_qv, 'product' => $product, 'product_id' => $product_id ) );
            $button = ob_get_clean();
            return $button;
        }
        public function printing_tab( $tabs ){
            global $post;
            $nbpt_content = $this->get_printing_tab_content();
            if ( strlen( $nbpt_content ) > 0 ) {
                $nbpt_title         = get_post_meta($post->ID, '_nbpt_title', true);
                $tabs['size_chart'] = array(
                    'title'    => $nbpt_title,
                    'priority' => 50,
                    'callback' => array( $this, 'printing_tab_content' )
                );
            }
            return $tabs;
        }
        public function printing_tab_content(){
            echo $this->get_printing_tab_content();
        }
        public function get_printing_tab_content(){
            global $post;
            return htmlspecialchars_decode(get_post_meta( $post->ID, '_nbpt_content', true ));
        }
        public function print_popup_ajax_cart(){
            ob_start();
            nbdesigner_get_template( 'ajax-cart-alert.php', array() );
            nbdesigner_get_template( 'quick-view-popup.php', array() );
            $content = ob_get_clean();
            echo $content;
        }
        public function show_options_in_archive_pages(){
            global $product;
            $product_id = $product->get_id();
            $transient  = 'nbo_archive_options_' . $product_id;
//            if( false === ( $archive_options = get_transient( $transient ) ) ){
                $option_id = $this->get_product_option( $product_id );
                $archive_options = array();
                if( $option_id ){
                    $_options = $this->get_option( $option_id );
                    if( $_options ){
                        $options = unserialize( $_options['fields'] );
                        if( isset( $options['fields'] ) ){
                            $options['fields'] = $this->recursive_stripslashes( $options['fields'] );
                            foreach ( $options['fields'] as $key => $field ){
                                if( isset($field['general']['attributes']) && $field['general']['enabled'] === 'y' ){
                                    if( isset( $field['appearance']['show_in_archives'] ) && $field['appearance']['show_in_archives'] == 'y'){
                                        $swatch = array();
                                        foreach ( $field['general']['attributes']['options'] as $op_index => $option ){
                                            $swatch[$op_index]          = array(
                                                'name'          =>  $option['name'],
                                                'preview_type'  =>  $option['preview_type']
                                            );
                                            $option['product_image']    = isset($option['product_image']) ? $option['product_image'] : 0;
                                            $attachment_id              = absint( $option['product_image'] );
                                            if( $attachment_id != 0 ){
                                                $swatch[$op_index]['srcset']    = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_catalog' ) : FALSE;
                                                $swatch[$op_index]['src']       = wp_get_attachment_url($attachment_id);
                                            }
                                            //CS botak display option in archive page
                                            $swatch[$op_index]['preview']   = nbd_get_image_thumbnail( $option['image'] );
                                            $swatch[$op_index]['color']     = $option['color'] ? $option['color'] :'#fff';
                                            if ( $field['appearance']['display_type'] == 's') {
                                                $swatch[$op_index]['display_type'] = 's';
                                                if( $option['preview_type'] == 'i' ){
                                                    $swatch[$op_index]['preview'] = nbd_get_image_thumbnail( $option['image'] );
                                                }else{
                                                    $swatch[$op_index]['color'] = $option['color'];
                                                    if( isset( $option['color2'] ) ) $swatch[$op_index]['color2'] = $option['color2'];
                                                }
                                            } else if ( $field['appearance']['different_display_type']  == 's' ) {
                                                $swatch[$op_index]['display_type'] = 'l';
                                                if( $option['preview_type'] == 'i' ){
                                                    $swatch[$op_index]['preview'] = nbd_get_image_thumbnail( $option['image'] );
                                                }else{
                                                    $swatch[$op_index]['color'] = $option['color'];
                                                    if( isset( $option['color2'] ) ) $swatch[$op_index]['color2'] = $option['color2'];
                                                }
                                            } else {
                                                $swatch[$op_index]['display_type'] = 'l';
                                                $swatch[$op_index]['des'] = $option['des'];
                                            }
                                            //CS botak different display type
                                            $swatch[$op_index]['different_display_type'] = $field['appearance']['different_display_type'];
//                                            $swatch[$op_index]['different_show_in_archives'] = $field['appearance']['different_show_in_archives'];
                                        }
                                        if (isset($field['nbd_type']) && $field['nbd_type'] === 'color') {
                                            $archive_options[0] = $swatch;
                                        } else {
                                            $archive_options[$key + 1] = $swatch;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if( $archive_options != false ) set_transient( $transient, $archive_options );
//            }
            ksort($archive_options);
            if( is_array( $archive_options ) && count( $archive_options ) > 0 ){
                ob_start();            
                nbdesigner_get_template( 'loop/swatches.php', array( 'archive_options' => $archive_options ) );
                $html = ob_get_clean();
                echo $html;
            }
        }
        public function action_woocommerce_before_variations_form(){
            if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View'){
                $nbd_qv_type = nbdesigner_get_option('nbdesigner_display_product_option');
                if( !( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'catalog' ) ){
                    if( $nbd_qv_type == '2' ) echo '<div class="nbo-wc-options">'. __('Options', 'web-to-print-online-designer') .'</div>';
                }
            }
        }
        public function autoptimize_js_exclude( $js ){
            if( false === strpos($js, 'angular') ) $js .= ', angular';
            return $js;
        }
        public function change_product_price_display( $price, $product ){
            $option_id = $this->get_product_option($product->get_id());
            $class = $product->get_type() == 'simple' ? 'nbo-base-price-html' : 'nbo-base-price-html-var';

            //CS botak role quantity break in archive page
            $discount_price = 0;
            if ($option_id) {
                $_options = $this->get_option($option_id);
                if($_options){
                    $user = wp_get_current_user();
                    $options = unserialize($_options['fields']);

                    if (isset($options['role_breaks'])) {
                        foreach ($options['role_breaks'] as $role_index => $role_breaks) {
                            if ( in_array( $role_breaks['role'], (array) $user->roles ) ) {
                                $options['quantity_breaks'] = $role_breaks['quantity_breaks'];
                                break;
                            } 
                        }
                    }
                    
                    if ($options['quantity_breaks'][0]['val'] == '1') {
                        $dis = $options['quantity_breaks'][0]['dis'] !== '' ? (float) $options['quantity_breaks'][0]['dis'] : 0;
                        if ($options['quantity_discount_type'] == 'p') {
                            $discount_price = $product->get_price() * $dis / 100;
                        } else if ($options['quantity_discount_type'] == 'f') {
                            $discount_price = $dis;
                        }
                    };
                }
            }
            // cs Botak hiddem price when the product out of stock
            $availability = $product->get_availability();
            if($availability['availability'] == 'Out of stock' ) {
                // return '<span class="'. $class .' '.$availability['class'].'">'. __('', 'web-to-print-online-designer') .'</span> ';
                return '<span class="'. $class .' '.$availability['class'].'">'. __('From ', 'web-to-print-online-designer') .'</span> '.  wc_price(0);
            }
            if ($discount_price !== 0) {
                $price = $product->get_price() - $discount_price;
                $price = wc_price($price);
            }
            //End CS botak role quantity break in archive page
            
            if( $option_id || $product->get_tax_status() === 'taxable' && $product->get_price()){ //CS botak price in archive page have 'From' prefix
                $price = '<span class="'. $class .'">'. __('From', 'web-to-print-online-designer') .'</span> ' . $price;
            }
            return $price;
        }
        public function add_empty_cart_button(){
            echo '<input type="submit" class="nbo-clear-cart-button button" name="nbo_empty_cart" value="' . __('Empty cart', 'web-to-print-online-designer') . '" />';
        }
        public function clear_cart(){
            if ( isset( $_POST['nbo_empty_cart'] ) ) {
                if ( !isset( WC()->cart ) || WC()->cart == '' ) {
                    WC()->cart = new WC_Cart();
                }
                WC()->cart->empty_cart( TRUE );
                do_action('nbo_clear_cart');
            }
        }
        public function turn_off_persistent_cart( $value, $id, $key ){
            $blog_id = get_current_blog_id();
            if ($key == '_woocommerce_persistent_cart' || $key == '_woocommerce_persistent_cart_' . $blog_id) {
                return FALSE;
            }
            return $value;
        }
        public function cart_redirect_after_error( $url = "", $product_id="" ){
            $option_id = $this->get_product_option($product_id);
            if($option_id){
                $url = get_permalink( $product_id );
            }
            return $url;
        }
        public function catalog_add_to_cart_text( $text = "" ){
            return $text;
        }
        public function add_to_cart_url( $url = "" ){
            global $product;
            if(!is_product() && is_object( $product ) && property_exists( $product, 'id' )){
                $product_id = $product->get_id();
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $url = get_permalink( $product_id );
                }
            }
            return $url;
        }
        public function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ){
            // Remove session design if the customer unselect all sides/pages
            $this->remove_session_design( $product_id, $variation_id );
            if( is_ajax() && !isset($_REQUEST['nbo-add-to-cart']) ){
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $_options = $this->get_option($option_id);
                    if($_options){
                        $options = unserialize($_options['fields']);
                        $valid_fields = $this->get_default_option($options);
                        $required_option = false;
                        foreach($valid_fields as $field){
                            if( $field['enable'] && $field['required'] ){
                                $required_option = true;
                                wc_add_notice( sprintf( __( '"%s" is a required field.', 'web-to-print-online-designer' ), $field['title'] ), 'error' );
                            }
                        }
                        if( $required_option ){
                            return FALSE;
                        }
                    }
                }
            }else{
                // Try to validate uploads before they happen
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $_options = $this->get_option($option_id);
                    if($_options){
                        $options = unserialize($_options['fields']);
                        $valid_fields = $this->get_default_option($options);
                        $required_upload = false;
                        foreach($valid_fields as $field_id => $field){
                            if( $field['is_upload'] ){
                                if( !empty($_FILES) && isset($_FILES["nbd-field"]) && isset($_FILES["nbd-field"]["name"][$field_id]) && $_FILES["nbd-field"]["error"][$field_id] == 0 ) {
                                    $origin_field = $this->get_field_by_id( $options, $field_id );
                                    $min_size = $origin_field['general']['upload_option']['min_size'];
                                    $max_size = $origin_field['general']['upload_option']['max_size'];
                                    $allow_type = $origin_field['general']['upload_option']['allow_type'];
                                    $file_info = pathinfo($_FILES["nbd-field"]["name"][$field_id]);
                                    $name = $file_info['filename'];
                                    $ext = strtolower( $file_info['extension'] );
                                    $size = $_FILES["nbd-field"]["size"][$field_id];
                                    if( $allow_type != '' ){
                                        $allow_type_arr = explode(',', strtolower( trim($allow_type ) ));
                                        $check_type = false;
                                        foreach($allow_type_arr as $type){
                                            if($ext == $type) $check_type = true;
                                        }
                                        if( !$check_type ){
                                            wc_add_notice( __( "Sorry, this file type is not permitted for security reasons.", 'web-to-print-online-designer' ) . ' (' . $ext . ')', 'error' );
                                            $passed = false;
                                        }
                                    }
                                    if( $min_size != '' ){
                                        $_min_size = intval($min_size) * 1024 * 1024;
                                        if( $_min_size > $size ){
                                            wc_add_notice( __( "Sorry, file is too small ( min size: ", 'web-to-print-online-designer' ) . $min_size . __( " MB )", 'web-to-print-online-designer' ), 'error' );
                                            $passed = false;
                                        }
                                    }
                                    if( $max_size != '' ){
                                        $_max_size = intval($max_size) * 1024 * 1024;
                                        if( $_max_size < $size ){
                                            wc_add_notice( __( "Sorry, file is too big ( max size: ", 'web-to-print-online-designer' ) . $max_size . __( " MB )", 'web-to-print-online-designer' ), 'error' );
                                            $passed = false;
                                        }
                                    }
                                }else{
                                    if( $field['enable'] && $field['required'] ){
                                        wc_add_notice( __( "Upload file is required.", 'web-to-print-online-designer' ), 'error' );
                                        $passed = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $passed;
        }
        public function remove_session_design(  $product_id, $variation_id ){
            if( isset( $_REQUEST['nbo-ignore-design'] ) && $_REQUEST['nbo-ignore-design'] == '1' ){
                $variation_id = $variation_id != '' ? $variation_id : 0;
                $product_id = get_wpml_original_id($product_id);
                $recent_variation_id = $variation_id;
                $variation_id = get_wpml_original_id($variation_id);
                $nbd_item_cart_key = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id; 
                $recent_nbd_item_cart_key = ($recent_variation_id > 0) ? $product_id . '_' . $recent_variation_id : $product_id;
                WC()->session->__unset('nbd_item_key_'.$nbd_item_cart_key);
                WC()->session->__unset('nbd_item_key_'.$recent_nbd_item_cart_key);
                WC()->session->__unset('nbu_item_key_'.$nbd_item_cart_key);
                WC()->session->__unset('nbu_item_key_'.$recent_nbd_item_cart_key);
            }
        }
        public function get_default_option($options){
            $fields = array();
            if( !isset($options['fields']) ) return $fields;
            foreach ($options['fields'] as $field){
                if($field['general']['enabled'] == 'y'){
                    $fields[$field['id']] = array(
                        'title' =>  $field['general']['title'],
                        'enable'    =>  true,
                        'required'    =>  $field['general']['required'] == 'y' ? true : false,
                        'is_upload'  =>  ( $field['general']['data_type'] == 'i' && $field['general']['input_type'] == 'u') ? true : false
                    );
                    if($field['general']['data_type'] == 'i'){
                        $fields[$field['id']]['value'] = $field['general']['input_type'] != 't' ? ( $field['general']['input_option']['min'] != '' ? $field['general']['input_option']['min'] : 0 ) : '';
                    }else{
                        $fields[$field['id']]['value'] = 0;
                        if( $field['general']['attributes']['options'] ) {
                            foreach ($field['general']['attributes']['options'] as $key => $op){
                                if( isset($op['selected']) && $op['selected'] == 'on' ) $fields[$field['id']]['value'] = $key;
                            }
                        }
                    }
                }
            }
            $valid_fields = $this->validate_field_option($options, $fields);
            return $valid_fields;
        }
        public function validate_field_option( $origin_fields, $fields ){
            foreach( $fields as $field_id => $f ){
                $field = $this->get_field_by_id($origin_fields, $field_id);
                $check = array();
                if( $field['conditional']['enable'] == 'n' || !isset($field['conditional']['depend']) || count($field['conditional']['depend']) == 0 ){
                    continue;
                }
                $show = $field['conditional']['show'];
                $logic = $field['conditional']['logic'];
                $total_check = $logic == 'a' ? true : false;
                foreach($field['conditional']['depend'] as $key => $con){
                    $check[$key] = true;
                    if( $con['id'] != '' ){
                        switch( $con['operator'] ){
                            case 'i':
                                $check[$key] = $fields[$con['id']]['value'] == $con['val'] ? true : false;
                                break;
                            case 'n':
                                $check[$key] = $fields[$con['id']]['value'] != $con['val'] ? true : false;
                                break;
                            case 'e':
                                $check[$key] = $fields[$con['id']]['value'] == '' ? true : false;
                                break;
                            case 'ne':
                                $check[$key] = $fields[$con['id']]['value'] != '' ? true : false;
                                break;
                        }
                    }
                }
                foreach ($check as $c){
                    $total_check = $logic == 'a' ? ($total_check && $c) : ($total_check || $c);
                }
                $fields[$field_id]['enable'] = $show == 'y' ? $total_check : !$total_check;
            }
            return $fields;
        }
        public function order_again_cart_item_data( $arr,  $item,  $order ){
            remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 1, 6 );
            $order_items = $order->get_items();
            foreach( $order_items as $order_item_id => $order_item ){   
                if( $item->get_id() == $order_item_id ){
                    if( $option_price = wc_get_order_item_meta($order_item_id, '_nbo_option_price') ){
                        $arr['nbo_meta']['option_price'] = $option_price;
                    }
                    if( $field = wc_get_order_item_meta($order_item_id, '_nbo_field') ){
                        $arr['nbo_meta']['field'] = $field;
                    }
                    if( $options = wc_get_order_item_meta($order_item_id, '_nbo_options') ){
                        $arr['nbo_meta']['options'] = $options;
                    }
                    if( $original_price = wc_get_order_item_meta($order_item_id, '_nbo_original_price') ){
                        $arr['nbo_meta']['original_price'] = $original_price;
                        $arr['nbo_meta']['price'] = $this->format_price($original_price + $option_price['total_price'] - $option_price['discount_price']);
                    }
                    $arr['nbo_meta']['order_again'] = $order->get_id(); // custom botak phase 4
                }
            }
            return $arr;
        }
        public function add_cart_fee( $cart_object ){
            if ( is_array( $cart_object->cart_contents ) ) {
                foreach ( $cart_object->cart_contents as $key => $value ) {
                    if( isset($value['nbo_meta']) && isset( $value['nbo_meta']['option_price']['cart_item_fee'] ) && $value['nbo_meta']['option_price']['cart_item_fee']['value'] != 0 ){
                        $fees       = array();
                        if ( is_object( $cart_object ) && is_callable( array( $cart_object, "get_fees" ) ) ) {
                            $fees = $cart_object->get_fees();
                        }else{
                            $fees = $cart_object->fees;
                        }
                        foreach( $value['nbo_meta']['option_price']['cart_item_fee']['fields'] as $field ){
                            //$cart_item_fee  = $value['nbo_meta']['option_price']['cart_item_fee'];
                            //$fee_name       = __('Extra fee ', 'web-to-print-online-designer') . strtoupper( substr( $key,0,7 ) ) . ': ' . $cart_item_fee['name'];
                            $fee_name       = $field['name'] . ' - ' . strtoupper( substr( $key,0,7 ) );
                            $product        = $value["data"];
                            $tax_class      = $product->get_tax_class();
                            $tax_status     = $product->get_tax_status();
                            if ( get_option( 'woocommerce_calc_taxes' ) == "yes" && $tax_status == "taxable" ) {
                                $tax = TRUE;
                            } else {
                                $tax = FALSE;
                            }
                            //$fee_price = $this->cacl_fee_price( $cart_item_fee['value'], $product );
                            $fee_price  = $this->cacl_fee_price( $field['price'], $product );
                            $can_add    = TRUE;
                            if ( is_array( $fees ) ) {
                                foreach ( $fees as $fee ) {
                                    if ( $fee->id == sanitize_title( $fee_name ) ) {
                                        $fee->amount = (float) $fee_price;
                                        $can_add     = FALSE;
                                        break;
                                    }
                                }
                            }
                            if( $can_add ){
                                $cart_object->add_fee( $fee_name, $fee_price, $tax, $tax_status );
                            }
                        }
                    }
                }
            }
        }
        public function cacl_fee_price( $price = "", $product = "" ){
            global $woocommerce;
            $taxable    = $product->is_taxable();
            $tax_class  = $product->get_tax_class();
            if ( $taxable ) {
                if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' ) {
                    $tax_rates  = WC_Tax::get_base_tax_rates( $tax_class );
                    $taxes      = WC_Tax::calc_tax( $price, $tax_rates, TRUE );
                    $price      = WC_Tax::round( $price - array_sum( $taxes ) );
                }
                return $price;
            }
            return $price;
        }
        public function remove_cart_item_quantity( $quantity_html, $cart_item, $cart_item_key ){
            if( isset($cart_item['nbo_meta']) ) $quantity_html = '';
            return $quantity_html;
        }
        public function order_line_item( $item, $cart_item_key, $values ){
            if ( isset( $values['nbo_meta'] ) ) {
                //CS botak add sku in gallery option
                if( nbd_is_base64_string( $values['nbo_meta']['options']['fields'] )) {
                    $values['nbo_meta']['options']['fields'] = base64_decode( $values['nbo_meta']['options']['fields'] );    // custom botak fix lose the sku when update base64_decode
                }
                $option_fields = maybe_unserialize($values['nbo_meta']['options']['fields']);
                $field = $values['nbo_meta']['field'];
                $quantity = $item->get_quantity();

                // The WC_Product object
                $product = $item->get_product();
                // Get the  SKU
                $sku = '';

                //CS botak check condition to change gallery
                $check = NBD_FRONTEND_PRINTING_OPTIONS::check_and_get_change_gallery($option_fields['gallery_options'], $field, $quantity);
                if ($check['change'] === true && $check['option']['sku']) {
                    $sku = $check['option']['sku'];
                }
                foreach ($values['nbo_meta']['field'] as $f_id => $fvalue) {
                    $select = !is_array($fvalue) ? $fvalue : $fvalue['value'];
                    foreach ($option_fields['fields'] as $data) {
                        if ($f_id === $data['id']) {
                            $option = $data['general']['attributes']['options'][$select];
                            if (isset($option['sku']) && $option['sku'] != '') {
                                $sku .= $option['sku'];
                            }
                        }
                    }
                }
                
                //End CS botak check condition to change gallery
                if ($sku == '') {
                    $sku = $product->get_sku();
                }
                
                // When sku exist
                if (!empty($sku) && $sku != '') {
                    $item->add_meta_data('SKU', $sku);
                }
                //End CS botak add sku in gallery option
                
                $decimals = nbdesigner_get_option('nbdesigner_number_of_decimals', 2);
                $hide_option_price = nbdesigner_get_option('nbdesigner_hide_option_price_in_order', 'no');
                foreach ($values['nbo_meta']['option_price']['fields'] as $field) {
                    if( !isset( $field['published'] ) || $field['published'] == 'y' ){
                        //CS botak option production time
                        if (floatval($field['price']) > 0) {
                            $price = floatval($field['price']) >= 0 ? '+' . wc_price($field['price'], array( 'decimals' => $decimals )) : wc_price($field['price'], array( 'decimals' => $decimals ));
                            if( isset($field['is_upload']) ){
                                if (strpos($field['val'], 'http') !== false) {
                                    $file_url = $field['val'];
                                }else{
                                    $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$field['val'] );
                                }
                                $field['value_name'] = '<a href="' . $file_url . '">' . $field['value_name'] . '</a>';
                            }
                            $post_fix = '';
                            if( isset($field['ind_qty']) ){
                                $post_fix = '<small>'. __('( cart fee )', 'web-to-print-online-designer') .'</small>';
                            }
                            if( isset( $field['fixed_amount'] ) ){
                                $post_fix = '<small>'. __('( for all items )', 'web-to-print-online-designer') .'</small>';
                            }
                            $display_price = $hide_option_price == 'no' ? $price.$post_fix : '';
                            $item->add_meta_data( $field['name'], $field['value_name']. '&nbsp;&nbsp;' . $display_price );
                        } else {
                            $item->add_meta_data( $field['name'], $field['value_name'] );
                        }
                    }
                }
                if( floatval( $values['nbo_meta']['option_price']['discount_price'] ) > 0 ){
                    $item->add_meta_data( __('Quantity Discount', 'web-to-print-online-designer'), '-' . wc_price( $values['nbo_meta']['option_price']['discount_price'], array( 'decimals' => $decimals ) ) );      
                }
                $item->add_meta_data('_nbo_option_price', $values['nbo_meta']['option_price']);
                $item->add_meta_data('_nbo_field', $values['nbo_meta']['field']);
                $item->add_meta_data('_order_again', $values['nbo_meta']['order_again']); // custom botak phase 4
                $item->add_meta_data('_nbo_options', wp_slash( $values['nbo_meta']['options'] ));
                $item->add_meta_data('_nbo_original_price', $values['nbo_meta']['original_price']);
                $item->add_meta_data('_cart_item_key', $cart_item_key);
                if (array_key_exists('parent_cart_item', $values['nbo_meta'])) {
                    $item->add_meta_data('_parent_cart_item_key', $values['nbo_meta']['parent_cart_item']);
                }
                if (array_key_exists('parent_cart_item_name', $values['nbo_meta'])) {
                    $item->set_name($item->get_name().' - '.$values['nbo_meta']['parent_cart_item_name']);
                }
                if (array_key_exists('service_item_keys', $values['nbo_meta'])) {
                    $item->add_meta_data('_service_item_keys', $values['nbo_meta']['service_item_keys']);
                }
            }
        }
        public function cart_item_thumbnail( $image = "", $cart_item = array() ){
            if( isset($cart_item['nbo_meta']) && $cart_item['nbo_meta']['option_price']['cart_image'] != '' ){
                $size = 'shop_thumbnail';
                $dimensions = wc_get_image_size( $size ); 
                $image = '<img src="'.$cart_item['nbo_meta']['option_price']['cart_image']
                        . '" width="' . esc_attr( $dimensions['width'] )
                        . '" height="' . esc_attr( $dimensions['height'] )
                        . '" class="nbo-thumbnail woocommerce-placeholder wp-post-image" />';
            }
            $image = apply_filters('nbo_cart_item_thumbnail', $image, $cart_item);
            return $image;
        }
        public function re_calculate_price( $cart ){
            foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
                if( isset($cart_item['nbo_meta']) ){
                    //$product = $cart_item['data'];
                    $variation_id = $cart_item['variation_id'];
                    $product_id = $cart_item['product_id'];
                    $product = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
                    $options = $cart_item['nbo_meta']['options'];
                    $fields = $cart_item['nbo_meta']['field'];
                    //$original_price = $this->format_price( $product->get_price('edit') );
                    $original_price = (float)$product->get_price('edit');
                    $quantity = $cart_item['quantity'];
                    $option_price = $this->option_processing( $options, $original_price, $fields, $quantity, $cart_item_key, $product_id ); //CS botak pricing option
                    if( isset($cart_item['nbo_meta']['nbdpb']) ){
                        $path = NBDESIGNER_CUSTOMER_DIR . '/' . $cart_item['nbo_meta']['nbdpb'] . '/preview';
                        $images = Nbdesigner_IO::get_list_images($path, 1);
                        if( count($images) ){
                            ksort( $images );
                            $option_price['cart_image'] = Nbdesigner_IO::wp_convert_path_to_url(end($images));
                        }
                    }
                    $adjusted_price = $original_price + $option_price['total_price'] - $option_price['discount_price'];
                    $adjusted_price = $adjusted_price > 0 ? $adjusted_price : 0;
                    
                    //CS V3 production time: calc price with production time option
                    $role_use = wp_get_current_user()->roles['0'];
                    $have_role_use = false;
                    $have_check_default = false;
                    $_role_options = array();
                    if( !empty($fields) ) {
                        foreach($fields as $key => $val){
                            if( nbd_is_base64_string( $options['fields'] ) ){
                                $options['fields'] = base64_decode( $options['fields'] );
                            }
                            $option_fields = unserialize($options['fields']);  
                            $option_fields = $this->recursive_stripslashes( $option_fields );
                            $origin_field = $this->get_field_by_id( $option_fields, $key );
                            //CS botak pricing option
                            if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'production_time') {
                                foreach ($origin_field['general']['role_options'] as $index_role_option => $role_options) {
                                    if($role_options['role_name'] == $role_use ) {
                                        $_role_options_1 = $role_options;
                                        $have_role_use = true;
                                    }
                                    if($role_options['check_default'] == 'on' || $role_options['check_default'] == '1') {
                                        $have_check_default = true;
                                        $_role_options_2 = $role_options;
                                    }   
                                }
                                if($have_role_use) {
                                    $_role_options = $_role_options_1;
                                }
                                if(!$have_role_use && $have_check_default ) {
                                    $_role_options = $_role_options_2;
                                }
                                if($_role_options) {
                                    $value_option_pt = $val['value'];
                                    $product_time_option = $_role_options['options'][$value_option_pt];
                                    $price_production_time = $adjusted_price * (int) $product_time_option['markup_percent'] / 100;
                                    if($price_production_time < (int)$product_time_option['min_markup_percent']/$quantity) {
                                        $price_production_time = (int)$product_time_option['min_markup_percent']/$quantity;
                                    }
                                    $adjusted_price += $price_production_time;
                                }
                            }
                        };
                    }
                    //End CS botak calc price with production time option
                    
                    //$adjusted_price = $this->format_price($adjusted_price);
                    WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['option_price'] = $option_price;
                    $adjusted_price = apply_filters('nbo_adjusted_price', $adjusted_price, $cart_item);
                    WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['price'] = $adjusted_price;
                    $needed_change = apply_filters('nbo_need_change_cart_item_price', true, WC()->cart->cart_contents[ $cart_item_key ]);
                    if( $needed_change ) WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $adjusted_price ); 
                }
            }
        }
        public function remove_previous_product_from_cart( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ){
            if ( $this->cart_edit_key ) {
                $cart_item_key = $this->cart_edit_key;
                if ( isset( $this->new_add_to_cart_key ) ) {
                    if ( $this->new_add_to_cart_key == $cart_item_key && isset( $_POST['quantity'] ) ) {
                        WC()->cart->set_quantity( $this->new_add_to_cart_key, $_POST['quantity'], TRUE );
                    } else {
                        $nbd_session = WC()->session->get($cart_item_key. '_nbd');
                        if( $nbd_session ){
                            WC()->session->set('nbd_session_removed', $nbd_session);
                            WC()->session->__unset($cart_item_key. '_nbd');

                            if( isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] ) ){
                                $design_id = WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'];
                                WC()->session->set('nbd_session_design_id_removed', $design_id);
                            }
                        }
                        WC()->cart->remove_cart_item( $cart_item_key );
                        unset( WC()->cart->removed_cart_contents[ $cart_item_key ] );
                    }
                }
            }
            return $passed;
        }
        public function add_to_cart_redirect( $url = "" ){
            if ( ( empty( $_REQUEST['add-to-cart'] ) || !is_numeric( $_REQUEST['add-to-cart'] ) ) && ( empty( $_REQUEST['nbo-add-to-cart'] ) || !is_numeric( $_REQUEST['nbo-add-to-cart'] ) ) ) {
                return $url;
            }
            if ( $this->cart_edit_key || isset( $_REQUEST['submit_form_mode2'] ) ) {
                $url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
            }
            if( isset( $_REQUEST['submit_form_from_w2p'] ) ){
                $url = add_query_arg(
                    array(
                        'action'    => 'w2p_printshop_redirect'
                    ),
                    site_url()
                );
            }
            return $url;
        }
        public function quantity_input_args( $args = "", $product = "" ){
            if ( $this->cart_edit_key ) {
                $cart_item_key = $this->cart_edit_key;
                $cart_item = WC()->cart->get_cart_item( $cart_item_key );
                if ( isset( $cart_item["quantity"] ) ) {
                    $args["input_value"] = $cart_item["quantity"];
                }
            }
            return $args;
        }
        public function add_to_cart_text($var){
            if( $this->is_edit_mode ){
                return esc_attr__( 'Update cart', 'woocommerce' );
            }
            return $var;
        }
        public function update_rich_snippet_price( $markup_offer, $product ){
            $post_id        = $product->get_id();
            $snippet_price  = get_post_meta($post_id, '_nbo_snippet_price', true);
            $snippet_price  = $snippet_price ? $snippet_price : '';
            if( $snippet_price != '' ){
                $new_price = wc_format_decimal( $snippet_price, wc_get_price_decimals() );
                if( isset( $markup_offer['price'] ) ){
                    $markup_offer['price'] = $new_price;
                }
                if( isset( $markup_offer['priceSpecification'] ) && isset( $markup_offer['priceSpecification']['price'] ) ){
                    $markup_offer['priceSpecification']['price'] = $new_price;
                }
                if( isset( $markup_offer['lowPrice'] ) ){
                    $markup_offer['lowPrice'] = $markup_offer['highPrice'] = $new_price;
                }
            }
            return $markup_offer;
        }
        public function add_to_cart( $cart_item_key = "", $product_id = "", $quantity = "", $variation_id = "", $variation = "", $cart_item_data = "" ){
            if ( $this->cart_edit_key ) {
                $this->new_add_to_cart_key = $cart_item_key;
                $nbd_session = WC()->session->get('nbd_session_removed');
                if( $nbd_session ){
                    WC()->session->set($cart_item_key. '_nbd', $nbd_session);
                    if( !isset(WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']) ) WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] = array();
                    WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'] = $nbd_session;
                    WC()->session->__unset('nbd_session_removed');
                    
                    $design_id = WC()->session->get('nbd_session_design_id_removed');
                    if( $design_id ){
                        WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] = $design_id;
                        WC()->session->__unset('nbd_session_design_id_removed');
                    }
                }
            }else{
                if (is_array($cart_item_data) && isset($cart_item_data['nbo_meta'])) {
                    $cart_contents = WC()->cart->cart_contents;
                    if (
                        is_array($cart_contents) &&
                        isset($cart_contents[$cart_item_key]) &&
                        !empty($cart_contents[$cart_item_key]) &&
                        !isset($cart_contents[$cart_item_key]['nbo_cart_item_key'])) {
                        WC()->cart->cart_contents[$cart_item_key]['nbo_cart_item_key'] = $cart_item_key;
                    }
                }
            }

            //CS botak
            $nbd_item_cart_key = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id;
            $nbd_image_paid = WC()->session->get('nbd_images_paid_'.$nbd_item_cart_key);
            if ($nbd_image_paid != null) {
                WC()->session->set($cart_item_key. '_images_paid', $nbd_image_paid);
                WC()->session->__unset('nbd_images_paid_'.$nbd_item_cart_key);
            }
            //Save parent item for service
            $product_services_key = 'services_of_product_' . $product_id;
            $services = WC()->session->get($product_services_key);
            if (is_array($services)) {
                foreach( $services as $item_id ){
                    WC()->cart->cart_contents[$item_id]['nbo_meta']['parent_cart_item'] = $cart_item_key;
                    WC()->cart->cart_contents[$item_id]['nbo_meta']['parent_cart_item_name'] = $cart_contents[$cart_item_key]['data']->get_name();
                }
                WC()->session->__unset($product_services_key);   
            }
        }
        
        public function cart_item_name($title = "", $cart_item = array(), $cart_item_key = ""){
            if ( !(is_cart() || is_checkout()) ){
                return $title;
            }
            if ( !isset( $cart_item['nbo_meta'] ) ) {
                return $title;
            }
            if( is_checkout() ){
                $title .= ' &times; <strong>' . $cart_item['quantity'] .'</strong>';
            }
            $product = $cart_item['data'];
            $link = add_query_arg(
                array(
                    'nbo_cart_item_key'  => $cart_item_key,
                )
                , $product->get_permalink( $cart_item ) );
            $link = wp_nonce_url( $link, 'nbo-edit' );
            $show_edit_link = apply_filters('nbo_show_edit_option_link_in_cart', true, $cart_item);
            //CS botak hidden edit option item name in cart
            //if( $show_edit_link ) $title .= '<br /><a class="nbo-edit-option-cart" href="' . $link . '" class="nbo-cart-edit-options">' . __( 'Edit options', 'web-to-print-online-designer' ) . '</a><br />';
            return apply_filters( 'nbo_cart_item_name', $title, $cart_item, $cart_item_key);
        }
        public function get_item_data( $item_data, $cart_item ){
            if ( isset( $cart_item['nbo_meta'] ) ) {
                $hide_zero_price = nbdesigner_get_option('nbdesigner_hide_zero_price');
                $num_decimals = absint( wc_get_price_decimals() );
                if( nbdesigner_get_option('nbdesigner_hide_options_in_cart') != 'yes' ){
                    $hide_option_price = nbdesigner_get_option('nbdesigner_hide_option_price_in_cart', 'no');
                    $decimals = nbdesigner_get_option('nbdesigner_number_of_decimals', 2);
                    foreach ($cart_item['nbo_meta']['option_price']['fields'] as $field) {
                        if( !isset( $field['published'] ) || $field['published'] == 'y' ){
                            $price = floatval($field['price']) >= 0 ? '+' . wc_price( $field['price'], array( 'decimals' =>  $decimals ) ) : wc_price($field['price'], array( 'decimals' => $decimals ));
                            if( $hide_zero_price == 'yes' && round($field['price'], $num_decimals) == 0 ) $price = '';
                            if( isset($field['is_upload']) ){
                                if (strpos($field['val'], 'http') !== false) {
                                    $file_url = $field['val'];
                                }else{
                                    $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$field['val'] );
                                }
                                $field['value_name'] = '<a href="' . $file_url . '">' . $field['value_name'] . '</a>';
                            }
                            $post_fix = '';
                            if( isset($field['ind_qty']) ){
                                $post_fix = '<small>'. __('( cart fee )', 'web-to-print-online-designer') .'</small>';
                            }
                            if( isset( $field['fixed_amount'] ) ){
                                $post_fix = '<small>'. __('( for all items )', 'web-to-print-online-designer') .'</small>';
                            }
                            $item_data[] = array(
                                'name'      => $field['name'],
                                'display'   => $hide_option_price == 'yes' ? $field['value_name'] : $field['value_name']. '&nbsp;&nbsp;' . $price .$post_fix,
                                'hidden'    => false
                            );
                        }
                    }
                    if( floatval( $cart_item['nbo_meta']['option_price']['discount_price'] ) > 0 ){
                        $item_data[] = array(
                            'name'      => __('Quantity Discount', 'web-to-print-online-designer'),
                            'display'   => '-' . wc_price( $cart_item['nbo_meta']['option_price']['discount_price'], array( 'decimals' => $decimals ) ),
                            'hidden'    => false
                        );
                    }
                }
            }
            return $item_data;
        }
        public function get_cart_item_from_session( $cart_item, $values ){
            if ( isset( $values['nbo_meta'] ) ) {
                $cart_item['nbo_meta'] = $values['nbo_meta'];
                // set the product price (if needed)
                $cart_item = $this->set_product_prices( $cart_item );
            }
            return $cart_item;
        }
        public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity = 1 ){
            $post_data = $_POST;
            $option_id = $this->get_product_option($product_id);
            if( !$option_id ){
                return $cart_item_data;
            }
            if( isset($post_data['nbd-field']) || isset($post_data['nbo-add-to-cart']) ){
                $options        = $this->get_option($option_id);
                $option_fields  = unserialize($options['fields']);
                $nbd_field      = isset($post_data['nbd-field']) ? $post_data['nbd-field'] : array();
                if( isset($cart_item_data['nbd-field']) ){
                    /* Bulk variation */
                    $nbd_field = $cart_item_data['nbd-field'];
                    $nbd_field = $this->validate_before_processing($option_fields, $nbd_field);
                    unset($cart_item_data['nbd-field']);
                }else{
                    if( !empty($_FILES) && isset($_FILES["nbd-field"]) ) {
                        foreach( $_FILES["nbd-field"]['name'] as $field_id => $file ){
                            if( !isset($nbd_field[$field_id]) ){
                                $nbd_upload_field = $this->upload_file( $_FILES["nbd-field"], $field_id );
                                if( !empty($nbd_upload_field) ){
                                    $nbd_field[$field_id] = $nbd_upload_field[$field_id];
                                }
                            }
                        }
                    }
                }
                $product        = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
                $original_price = (float)$product->get_price('edit');
                $option_price   = $this->option_processing( $options, $original_price, $nbd_field, $quantity );
                if( isset($post_data['nbdpb-folder']) ){
                    $cart_item_data['nbo_meta']['nbdpb'] = $post_data['nbdpb-folder'];
                    $path   = NBDESIGNER_CUSTOMER_DIR . '/' . $post_data['nbdpb-folder'] . '/preview';
                    $images = Nbdesigner_IO::get_list_images($path, 1);
                    if( count($images) ){
                        ksort( $images );
                        $option_price['cart_image'] = Nbdesigner_IO::wp_convert_path_to_url(end($images));
                    }
                }

                //CS V3 Production time
                $_adjusted_price = $original_price + $option_price['total_price'] - $option_price['discount_price'];
                $role_use = wp_get_current_user()->roles['0'];
                $have_role_use = false;
                $have_check_default = false;
                $_role_options = array();
                foreach($nbd_field as $key => $val){
                    if( nbd_is_base64_string( $options['fields'] ) ){
                        $options['fields'] = base64_decode( $options['fields'] );
                    }
                    $option_fields = unserialize($options['fields']);  
                    $option_fields = $this->recursive_stripslashes( $option_fields );
                    $origin_field = $this->get_field_by_id( $option_fields, $key );
                    //CS botak pricing option
                    if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'production_time') {
                        foreach ($origin_field['general']['role_options'] as $index_role_option => $role_options) {
                           if($role_options['role_name'] == $role_use ) {
                                $_role_options_1 = $role_options;
                                $have_role_use = true;
                            }
                            if($role_options['check_default'] == 'on' || $role_options['check_default'] == '1') {
                                $have_check_default = true;
                                $_role_options_2 = $role_options;
                            }   
                        }
                        if($have_role_use) {
                            $_role_options = $_role_options_1;
                        }
                        if(!$have_role_use && $have_check_default ) {
                            $_role_options = $_role_options_2;
                        }
                        if($_role_options) {
                            $value_option_pt = $val['value'];
                            $product_time_option = $_role_options['options'][$value_option_pt];
                            $price_production_time = $adjusted_price * (int) $product_time_option['markup_percent'] / 100;
                            if($price_production_time < (int)$product_time_option['min_markup_percent']/$quantity) {
                                $price_production_time = (int)$product_time_option['min_markup_percent']/$quantity;
                            }
                            $_adjusted_price += $price_production_time;
                        }
                    }
                };
                // End
                $options['fields']                              = base64_encode( $options['fields'] );
                $cart_item_data['nbo_meta']['option_price']     = $option_price;
                $cart_item_data['nbo_meta']['field']            = $nbd_field;
                $cart_item_data['nbo_meta']['options']          = $options;
                $cart_item_data['nbo_meta']['original_price']   = $original_price;
                $cart_item_data['nbo_meta']['price']            = $_adjusted_price;
            }
            
            $product_services_key = 'services_of_product_' . $product_id;
            $services = WC()->session->get($product_services_key);
            if (is_array($services)) {
                $cart_item_data['nbo_meta']['service_item_keys'] = $services;
            }

            return $cart_item_data;
        }
        public function upload_file( $files, $field_id ){
            $nbd_upload_fields = array();
            global $woocommerce;
            $user_folder = md5( $woocommerce->session->get_customer_id() );
            $file = $files['name'][$field_id];
            if( $files['error'][$field_id] == 0 ){
                $ext = pathinfo( $file, PATHINFO_EXTENSION );
                $new_name = strtotime("now").substr(md5(rand(1111,9999)),0,8).'.'.$ext;
                $new_path = NBDESIGNER_UPLOAD_DIR . '/' .$user_folder . '/' .$new_name;
                $mkpath = wp_mkdir_p( NBDESIGNER_UPLOAD_DIR . '/' .$user_folder);
                if( $mkpath ){
                    if (move_uploaded_file($files['tmp_name'][$field_id], $new_path)) {
                        $nbd_upload_fields[$field_id] = $user_folder . '/' .$new_name;
                    }else{
                        //todo
                    }
                }
            }
            return $nbd_upload_fields;
        }
        public function format_price( $price ){
            //$decimal_separator = stripslashes( wc_get_price_decimal_separator() );
            //$thousand_separator = stripslashes( wc_get_price_thousand_separator() );
            $num_decimals = wc_get_price_decimals();
            //$price = str_replace($decimal_separator, '.', $price);
            //$price = str_replace($thousand_separator, '', $price);
            $price = round($price, $num_decimals);
            return $price;
        }
        public function get_field_by_id( $option_fields, $field_id ){
            if($option_fields['fields']) {
                foreach($option_fields['fields'] as $key => $field){
                    if( $field['id'] == $field_id ) return $field;
                }
            }
        }
        public function validate_before_processing($option_fields, $nbd_field){
            $new_fields = $nbd_field;
            foreach($nbd_field as $field_id => $field){
                $origin_field = $this->get_field_by_id( $option_fields, $field_id );
                if( $origin_field['conditional']['enable'] == 'n' || !isset($origin_field['conditional']['depend']) || count($origin_field['conditional']['depend']) == 0  ) continue;
                $show = $origin_field['conditional']['show'];
                $logic = $origin_field['conditional']['logic'];
                $total_check = $logic == 'a' ? true : false;
                $check = array();
                foreach($origin_field['conditional']['depend'] as $key => $con){
                    $check[$key] = true;
                    if( $con['id'] != '' ){
                        if( !isset($new_fields[$con['id']]) ){
                            $check[$key] = true;
                        }else{
                            switch( $con['operator'] ){
                                case 'i':
                                    $check[$key] = $nbd_field[$con['id']] == $con['val'] ? true : false;
                                    break;
                                case 'n':
                                    $check[$key] = $nbd_field[$con['id']] != $con['val'] ? true : false;
                                    break;
                                case 'e':
                                    $check[$key] = $nbd_field[$con['id']] == '' ? true : false;
                                    break;
                                case 'ne':
                                    $check[$key] = $nbd_field[$con['id']] != '' ? true : false;
                                    break;
                            }
                        }
                    }
                }
                foreach ($check as $c){
                    $total_check = $logic == 'a' ? ($total_check && $c) : ($total_check || $c);
                }
                $enable = $show == 'y' ? $total_check : !$total_check;
                if( !$enable ) unset($new_fields[$field_id]);
            }
            return $new_fields;
        }
        public static function recursive_stripslashes( $fields ){
            $valid_fields = array();
            if($fields) {
                foreach($fields as $key => $field){
                    if(is_array($field) ){
                        $valid_fields[$key] = self::recursive_stripslashes($field);
                    }else if(!is_null($field)){
                        $valid_fields[$key] = stripslashes($field);
                    }
                }
            }
            return $valid_fields;
        }
        public function option_processing( $options, $original_price, $fields, $quantity, $cart_item_key = null, $product_id = 0 ){ //CS botak pricing option
            if( nbd_is_base64_string( $options['fields'] ) ){
                $options['fields'] = base64_decode( $options['fields'] );
            }
            $option_fields = unserialize($options['fields']);  
            $option_fields = $this->recursive_stripslashes( $option_fields );
            
            //CS botak role quantity break
            $user = wp_get_current_user();
            if (!array_key_exists('quantity_breaks', $option_fields)) {
                $option_fields['quantity_breaks'] = array(
                    array(
                        'val'       => 1,
                        'dis'       => '',
                        'default'   => 0
                    )
                );
            }
            if (is_object($user)) {
                $user = wp_get_current_user();
                if (is_object($user)) {
                    if (isset($option_fields['role_breaks']) && is_array($option_fields['role_breaks'])) {
                        foreach ($option_fields['role_breaks'] as $role_index => $role_breaks) {
                            if ( in_array( $role_breaks['role'], (array) $user->roles ) ) {
                                $option_fields['quantity_breaks'] = $role_breaks['quantity_breaks'];
                                break;
                            } 
                        }
                    }
                }
            }

            //CS botak Role quantity break V3 start
            $role_use = wp_get_current_user()->roles['0'];
            $max_production_time = 0;
            $pre_name = '';
            $have_role_use = false;
            $have_check_default = false;
            $_role_options = array();
            foreach($fields as $key => $val){
                $origin_field = $this->get_field_by_id( $option_fields, $key );
                //CS botak pricing option
                if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'production_time') {
                    $value_option_pt = $val['value'];
                    foreach ($origin_field['general']['role_options'] as $index_role_option => $role_options) {
                       if($role_options['role_name'] == $role_use ) {
                            $_role_options_1 = $role_options;
                            $have_role_use = true;
                        }
                        if($role_options['check_default'] == 'on' || $role_options['check_default'] == '1') {
                            $have_check_default = true;
                            $_role_options_2 = $role_options;
                        }   
                    }
                    if($have_role_use) {
                        $_role_options = $_role_options_1;
                    }
                    if(!$have_role_use && $have_check_default ) {
                        $_role_options = $_role_options_2;
                    }
                    if($_role_options) {
                        $pre_name = $_role_options['options'][$value_option_pt]['name'].' - ';
                        $time_quantity_breaks = $_role_options['options'][$value_option_pt]['time_quantity_breaks'];
                        for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                            if ($i === count($time_quantity_breaks) - 1) {
                                if ($quantity >= $time_quantity_breaks[$i]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                    $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                                }
                                break;
                            }
                            if ($quantity >= $time_quantity_breaks[$i]['qty'] && $quantity < $time_quantity_breaks[$i + 1]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                            }
                        }
                    }
                }
            };
            //CS botak Role quantity break V3 end

            //Add role break for range
            $option_fields = $this->calculate_measure_price($option_fields, $product_id);
            
            $quantity_break = $this->get_quantity_break( $option_fields, $quantity );
            $xfactor = 1;
            $total_price = 0;
            $discount_price = 0;
            $_fields = array();
            $cart_image = '';
            $cart_item_fee = 0;
            $line_price     = array(
                'fixed'     =>   0,
                'percent'   => 0,
                'xfactor'   => 1
            );
            $fixed_amount = 0;
            
            //CS botak get size of product
            $_designer_setting = get_post_meta($product_id, '_designer_setting', true);
            //$enable = get_post_meta($product_id, '_nbdesigner_enable', true);
            $product_size = [
                'product_width'     => 0,
                'product_height'    => 0,
            ];
            if ($_designer_setting /*&& $enable*/) {
                $_designer_setting = unserialize($_designer_setting);
                if ($_designer_setting) {
                    $first_side = $_designer_setting[0];
                    $product_size['product_width'] = $first_side['product_width'];
                    $product_size['product_height'] = $first_side['product_height'];
                }
            };
            
            $size_product_width = $product_size['product_width']; //Get width of product by size option
            $size_product_height = $product_size['product_height']; //Get height of product by size option
            $dimension_product_width = 0; //Get width of product by custom dimension option
            $dimension_product_height = 0; //Get height of product by custom dimension option
            $calculation_measure_base_on = 's'; //check calculation option is depended size or dimention option
            foreach($fields as $key => $val){
                $origin_field = $this->get_field_by_id( $option_fields, $key );
                //CS botak pricing option
                if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'size') {
                    if ($origin_field['general']['attributes']['same_size'] === 'n') {
                        $size_product_width = $origin_field['general']['attributes']['options'][$val['value']]['product_width'];
                        $size_product_height = $origin_field['general']['attributes']['options'][$val['value']]['product_height'];

                        //Calc price by size option
                        foreach ($fields as $tmp_key => $tmp_val) {
                            foreach ($option_fields['fields'] as &$tmp_origin_field) {
                                if ($tmp_origin_field['id'] === $tmp_key && isset($tmp_origin_field['nbd_type']) && $tmp_origin_field['nbd_type'] === 'pricing_rates') {
                                    foreach ($tmp_origin_field['general']['attributes']['options'] as &$tmp_option) {
                                        switch ($tmp_option['calc_method']) {
                                            case 'area':
                                                $tmp_option['price'][0] = strval((float) $size_product_width * (float) $size_product_height * (float) $tmp_option['rate']);
                                                break;
                                            case 'perimeter':
                                                $tmp_option['price'][0] = strval(2 * ((float) $size_product_width + (float) $size_product_height) * (float) $tmp_option['rate']);
                                                break;
                                            case 'quantity':
                                                $tmp_option['price'][0] = strval((int) $tmp_option['quantity']* (float) $tmp_option['rate']);
                                                break;
                                        };
                                    };
                                }
                            }
                        }
                        //End calc price by size option
                    }
                }
                //End CS botak pricing option
                
                //Get product size by dimension printing option
                if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'dimension') { //check if field dimension is enabled so calculation option is depended dimention option
                    $calculation_measure_base_on = 'd';
                    $p_size = explode("x",$val);
                    $dimension_product_width = $p_size[0];
                    $dimension_product_height = $p_size[1];
                }
            }
            
            //CS botak calculation option
            if($fields) {
                foreach($fields as $key => $val){
                    if($option_fields['fields']) {
                        foreach($option_fields['fields'] as &$o_field){
                            if( $o_field['id'] == $key ) {
                                //CS botak pricing option
                                if (isset($o_field['nbd_type']) && $o_field['nbd_type'] === 'calculation_option') {
                                    if ($calculation_measure_base_on === 's') {
                                        $o_field = $this->calculate_price_calculation_option($o_field, $size_product_width, $size_product_height);
                                    } else if ($calculation_measure_base_on === 'd') {
                                        $o_field = $this->calculate_price_calculation_option($o_field, $dimension_product_width, $dimension_product_height);
                                    };
                                }
                                break;
                            }
                        }
                    }
                };
            }
            //End CS botak calculation option
            
            foreach($fields as $key => $val){
                $origin_field = $this->get_field_by_id( $option_fields, $key );
                $published    = isset( $origin_field['general']['published'] ) ? $origin_field['general']['published'] : 'y';
                if( isset($origin_field['nbe_type']) && $origin_field['nbe_type'] == 'delivery' ){
                    $turnaround_matrix = $this->build_turnaround_matrix( $option_fields, $origin_field );
                    $position = $quantity_break['index'];
                    $__val = is_array($val) ? ( isset($val['value']) ? $val['value'] : $val[0] ) : $val;
                    if( $turnaround_matrix[ $position ][ $__val ] == 0 ){
                        for ($i = 0; $i < count( $origin_field['general']['attributes']['options'] ); $i++) {
                            if( $turnaround_matrix[ $position ][ $i ] == 1 ){
                                $val = '' . $i;
                                if( !is_null( $cart_item_key ) ){
                                    if( isset( WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta'] ) ){
                                        $nbd_field = WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'];
                                        $nbd_field[ $key ] = $val;
                                        WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'] = $nbd_field;
                                    }
                                }
                                break;
                            }
                        }
                    }
                }

                // CS botak Fix price option old of production time
                if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'production_time') {
                    if( isset($origin_field['general']['attributes']['options'][$val['value']]['price']) ) {
                        $origin_field['general']['attributes']['options'][$val['value']]['price'] = 0;
                        $_fields[$key]['is_pp'] = 0;
                    }
                }

                $_fields[$key] = array(
                    'name'  =>  $origin_field['general']['title'],
                    'val'   =>  $val,
                    'value_name'   =>  $val,
                    'published'   =>  $published
                );
                if( $origin_field['general']['data_type'] == 'i' ){
                    if(isset($origin_field['general']['depend_quantity']) && $origin_field['general']['depend_quantity'] == 'n'){
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $origin_field['general']['price'], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                        }else{
                            $factor = $origin_field['general']['price'];
                        }
                    }else{
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $origin_field['general']['price_breaks'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                        }else{
                            $factor = $origin_field['general']['price_breaks'][$quantity_break['index']];
                        }
                    }
                    if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['measure'] == 'y' /*&& isset($origin_field['general']['measure_range']) && count($origin_field['general']['measure_range']) > 0*/ ){ //CS botak dimention multi component
                        $dimension = explode("x",$val);
                        $factor = $this->calculate_price_base_measurement($origin_field, $dimension[0], $dimension[1]);
                        if( ($origin_field['general']['price_type'] == 'f' || $origin_field['general']['price_type'] == 'c') && $origin_field['general']['measure_base_pages'] == 'y' ){
                            $no_page = 1;
                            foreach($fields as $_key => $_val){
                                $_origin_field = $this->get_field_by_id( $option_fields, $_key );
                                if( isset($_origin_field['nbd_type']) && ( $_origin_field['nbd_type'] == 'page' || $_origin_field['nbd_type'] == 'page1' ) ){
                                    if( $_origin_field['general']['data_type'] == 'i' ){
                                        $no_page = $_val;
                                    }else{
                                        //$no_page = count($_val);
                                    }
                                }
                            }
                            $factor *= floor( ($no_page + 1) / 2 );
                        }
                    }
                    if( $origin_field['general']['input_type'] == 'u' ){
                        $file_name = explode('/', $val);
                        $_fields[$key]['value_name']    = $file_name[ count($file_name) - 1 ];
                        $_fields[$key]['is_upload']     = 1;
                    }
                    if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'page1'
                            && isset($origin_field['general']['price_depend_no']) && $origin_field['general']['price_depend_no'] == 'y'
                            && isset($origin_field['general']['price_no_range']) && count( $origin_field['general']['price_no_range'] ) > 0 ){
                        $default = 0;
                        if( isset( $origin_field['general']['input_option']['default'] ) && $origin_field['general']['input_option']['default'] != '' ){
                            $default = absint( $origin_field['general']['input_option']['default'] );
                        }
                        $current_val    = absint( $val );
                        $current_val   -= $default;
                        $current_val    = $current_val > 0 ? $current_val : 0;
                        $price_no_range = $origin_field['general']['price_no_range'];
                        foreach( $price_no_range as $range ){
                            $qty = absint( $range[0] );
                            if( $current_val >= $qty ){
                                $factor = floatval( $range[1] );
                            }
                        }
                    }
                }else{
                    $select_val = is_array($val) ? ( isset($val['value']) ? $val['value'] : $val[0] ) : $val;
                    $option = $origin_field['general']['attributes']['options'][$select_val];
                    $has_subattr = false;
                    if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                        $has_subattr = true;
                    }
                    if( !$has_subattr && isset( $val['sub_value'] ) ){
                        unset( $val['sub_value'] );
                    }
                    $_fields[$key]['value_name'] = $option['name']; 
                    if(is_array($val)){
                        if( $has_subattr ){
                            $_fields[$key]['value_name'] .= ' - ' . $option['sub_attributes'][$val['sub_value']]['name'];
                        }else{
                            $_fields[$key]['value_name'] = '';
                            foreach($val as $k => $v){
                                $_fields[$key]['value_name'] .= ($k == 0 ? '' : ', ') . $origin_field['general']['attributes']['options'][$v]['name'];
                            }
                        }
                    }
                    if(isset($origin_field['general']['depend_quantity']) && $origin_field['general']['depend_quantity'] == 'n'){
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $option['price'][0], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                        }else{
                            $factor = floatval( $option['price'][0] );
                        }
                    }else{
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $option['price'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                        }else{
                            $factor = floatval( $option['price'][$quantity_break['index']] );
                        }
                    }
                    if( $has_subattr ){
                        $soption = $option['sub_attributes'][$val['sub_value']];
                        if($origin_field['general']['depend_quantity'] == 'n'){
                            if( $origin_field['general']['price_type'] == 'mf' ){
                                $factor += $this->eval_price( $soption['price'][0], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                            }else{
                                $factor += floatval( $soption['price'][0] );
                            }
                        }else{
                            if( $origin_field['general']['price_type'] == 'mf' ){
                                $factor += $this->eval_price( $soption['price'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                            }else{
                                $factor += floatval( $soption['price'][$quantity_break['index']] );
                            }
                        }
                    }
                    if(isset($origin_field['appearance']['change_image_product']) && $origin_field['appearance']['change_image_product'] == 'y' && isset($option['product_image']) && $option['product_image'] > 0){
                        $image = wp_get_attachment_image_src( $option['product_image'], 'thumbnail' );
                        if($image){
                            $cart_image = $image[0];
                        }else{
                            $cart_image = wp_get_attachment_url($option['product_image']);
                        }
                    }
                }
                $_fields[$key]['is_pp'] = 0;
                if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['price_type'] == 'c' ){
                    $origin_field['general']['price_type'] == 'f';
                }
                if( isset($origin_field['nbd_type']) && ( $origin_field['nbd_type'] == 'page' || $origin_field['nbd_type'] == 'page2' ) && $origin_field['general']['data_type'] == 'm' ){
                    $factor = array();
                    if(is_array($val)) {
                        foreach($val as $k => $v){
                            $option = $origin_field['general']['attributes']['options'][$v];
                            if($origin_field['general']['depend_quantity'] == 'n'){
                                if( $origin_field['general']['price_type'] == 'mf' ){
                                    $factor[$k] = $this->eval_price( $option['price'][0], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                                }else{
                                    $factor[$k] = $option['price'][0];
                                }
                            }else{
                                if( $origin_field['general']['price_type'] == 'mf' ){
                                    $factor[$k] = $this->eval_price( $option['price'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields );
                                }else{
                                    $factor[$k] = $option['price'][$quantity_break['index']];
                                }
                            }
                        }
                    }
                    // cs botak fix 0$ while add to card, the product have printing options is Page2
                    if(is_string($val)) {
                        $option = $origin_field['general']['attributes']['options'][$val];
                        if($origin_field['general']['depend_quantity'] == 'n'){
                            $factor[0] = $option['price'][0];
                        }else{
                            $factor[0] = $option['price'][$quantity_break['index']];
                        }
                    }
                    $_fields[$key]['price'] = 0;
                    $xfac = 0; $_xfac = 0;
                    foreach($factor as $fac){
                        $fac = floatval($fac);
                        $_fac = $fac;
                        if( $this->is_independent_qty( $origin_field ) ){
                            $fac =  0;
                            $_fields[$key]['ind_qty'] = 1;
                        }
                        if( $this->is_fixed_amount( $origin_field ) ){
                            $fac /= $quantity;
                            $_fields[$key]['fixed_amount'] = 1;
                        }
                        switch ($origin_field['general']['price_type']){
                            case 'f':
                            case 'mf':
                                $_fields[$key]['price'] += $_fac;
                                $total_price += $fac;
                                if( $this->is_independent_qty( $origin_field ) ){
                                    $line_price['fixed'] += $_fac;
                                }
                                break;
                            case 'p':
                                $_fields[$key]['price'] += $original_price * $_fac / 100;
                                $total_price += $original_price * $fac / 100;
                                if( $this->is_independent_qty( $origin_field ) ){
                                    $line_price['percent'] += $_fac;
                                }
                                break;
                            case 'p+':
                                $_fields[$key]['price'] += $fac / 100;
                                $_fields[$key]['_price'] += $_fac / 100;
                                $_fields[$key]['is_pp'] = 1;
                                $xfac += $fac / 100;
                                $_xfac += $_fac / 100;
                                break;
                        }
                    }
                    if( $origin_field['general']['price_type'] == 'p+' ){
                        $xfactor *= (1 + $xfac / 100);
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['xfactor'] *= (1 + $_xfac / 100);
                        }
                    }
                }else{
                    $factor = floatval($factor);
                    $_factor = $factor;
                    if( $this->is_independent_qty( $origin_field ) ){
                        $factor = 0;
                        $_fields[$key]['ind_qty'] = 1;
                    }
                    if( $this->is_fixed_amount( $origin_field ) ){
                        $factor /= $quantity;
                        $_fields[$key]['fixed_amount'] = 1;
                    }
                    switch ($origin_field['general']['price_type']){
                        case 'f':
                        case 'mf':
                            $_fields[$key]['price'] = $_factor;
                            $total_price += $factor;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_factor;
                            }
                            break;
                        case 'p':
                            $_fields[$key]['price'] = $original_price * $_factor / 100;
                            $total_price += $original_price * $factor / 100;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['percent'] += $_factor;
                            }
                            break;
                        case 'p+':
                            $_fields[$key]['price'] = $factor / 100;
                            $_fields[$key]['_price'] = $_factor / 100;
                            $_fields[$key]['is_pp'] = 1;
                            $xfactor *= (1 + $factor / 100);
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['xfactor'] *= (1 + $_factor / 100);
                            }
                            break;
                        case 'c':
                            $current_val = absint( $val );
                            if( ( isset($origin_field['nbd_type']) && ( ( $origin_field['nbd_type'] == 'page' && $origin_field['general']['data_type'] == 'i' ) || ( $origin_field['nbd_type'] == 'page1' ) ) ) || ( isset( $origin_field['nbe_type'] ) && $origin_field['nbe_type'] == 'number_file' ) ){
                                $default = 0;
                                if( isset( $origin_field['general']['input_option']['default'] ) && $origin_field['general']['input_option']['default'] != '' ){
                                    $default = absint( $origin_field['general']['input_option']['default'] );
                                }
                                $current_val -= $default;
                            }
                            $current_val = $current_val > 0 ? $current_val : 0;
                            $_fields[$key]['price'] = $_factor * $current_val;
                            $total_price += $factor * $current_val;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_factor * $current_val;
                            }
                            break;
                        case 'cp':
                            $_fields[$key]['price'] = $_factor * absint( strlen( $val ) );
                            $total_price += $factor * absint( strlen( $val ) );
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_factor * absint( strlen( $val ) );
                            }
                            break;
                    }
                }
            }
            $total_price += ( ($original_price + $total_price ) * ($xfactor - 1 ) );
            foreach($_fields as $key => $val){
                if( $_fields[$key]['is_pp'] == 1 ) {
                    $_fields[$key]['price'] = $_fields[$key]['price'] * ($original_price + $total_price ) / ( $_fields[$key]['price'] + 1 );
                }
            }
            if( $quantity_break['index'] == 0 && $quantity_break['oparator'] == 'lt' ){
                $qty_factor = '';
            }else{
                $qty_factor = $option_fields['quantity_breaks'][$quantity_break['index']]['dis'];
            }
            $qty_factor = floatval($qty_factor);
            $discount_price = $option_fields['quantity_discount_type'] == 'f' ? $qty_factor : ($original_price + $total_price ) * $qty_factor / 100;
            $total_cart_price = ( $original_price + $total_price - $discount_price ) * $quantity;
            $cart_item_fee = array(
                'value'   => 0,
                'name'    => '',
                'id'      => '',
                'fields'  => array()
            );
            if( $line_price['fixed'] != 0 || $line_price['xfactor'] != 1 || $line_price['percent'] != 0 ){
                $_total_cart_price = $total_cart_price;
                if( $line_price['fixed'] != 0 ){
                    $total_cart_price += $line_price['fixed'];
                }
                if( $line_price['percent'] != 0 ){
                    $total_cart_price += ($original_price * $line_price['percent'] / 100);
                }
                if( $line_price['xfactor'] != 1 ){
                    $total_cart_price += ( $total_cart_price * ( $line_price['xfactor'] - 1 ) );
                    foreach($_fields as $key => $val){
                        if( $val['is_pp'] == 1 && isset($val['ind_qty']) && $val['ind_qty'] == 1  ) {
                            $_fields[$key]['price'] = $val['_price'] * $total_cart_price / ( $val['_price'] + 1 );
                        }
                    }
                }
                foreach($_fields as $key => $val){
                    if( isset($val['ind_qty']) && $val['ind_qty'] == 1 ){
                        $cart_item_fee['name'] .= $val['name'] . ', ';
                        $cart_item_fee['fields'][] = array(
                            'name'  => $val['name'] . ': ' . $val['value_name'],
                            'price' => $val['price']
                        );
                    }
                }
                if( $cart_item_fee['name'] != '' ){
                    $cart_item_fee['name'] = substr($cart_item_fee['name'], 0, strlen($cart_item_fee['name']) - 2);
                }
                $cart_item_fee['value'] = $total_cart_price - $_total_cart_price;
            }
            
            //CS botak check condition to change gallery
            $check = $this->check_and_get_change_gallery($option_fields['gallery_options'], $fields, $quantity);
            if ($check['change'] === true) {
                if (count($check['option']['product_images'])) {
                    $image = wp_get_attachment_image_src( $check['option']['product_images'][0]['product_image'], 'thumbnail' );
                    if ($image) {
                        $cart_image = $image[0];
                    } else {
                        $cart_image = wp_get_attachment_url($check['option']['product_images'][0]['product_image']);
                    }
                }
            }
            //CS botak production time 
            foreach($fields as $key => $val){
                $origin_field = $this->get_field_by_id( $option_fields, $key );
                if (isset($origin_field['nbd_type']) && $origin_field['nbd_type'] === 'production_time') {
                    $_fields[$key] = array(
                        'name'  =>  $origin_field['general']['title'],
                        'val'   =>  $val,
                        'value_name'   =>  $pre_name.$max_production_time . ' Hours',
                        'published'   =>  $published
                    );
                }
            };
            return array(
                'total_price'       => $total_price,
                'cart_item_fee'     => $cart_item_fee,
                'discount_price'    => $discount_price,
                'fields'            => $_fields,
                'cart_image'        => $cart_image
            );
        }
        
        // custom val eval price
        public function eval_price( $formula, $origin_field, $qty, $fields, $original_price, $option_fields, $_fields ){
            require_once NBDESIGNER_PLUGIN_DIR . 'lib/eval-math/EvalMath.php';

            $formula = str_replace( "{quantity}", $qty, $formula );
            $formula = str_replace( "{price}", $original_price, $formula );

            $value      = 0;
            $value_len  = 0;
            if( $origin_field['general']['data_type'] == 'i' ){
                if( $origin_field['general']['input_type'] == 'n' || $origin_field['general']['input_type'] == 'r' ){
                    $value = $fields[$origin_field['id']];
                }
                if( $origin_field['general']['input_type'] == 't' || $origin_field['general']['input_type'] == 'a' ){
                    $value_len =  strlen( $fields[$origin_field['id']] );
                }
            }

            $formula = str_replace( "{this.value}", $value, $formula );
            $formula = str_replace( "{this.value_length}", $value_len, $formula );

            preg_match_all( '/\{(\s)*?field\.([^}]*)}/', $formula, $matches );
            if ( is_array( $matches ) && isset( $matches[2] ) && is_array( $matches[2] ) ) {
                foreach ( $matches[2] as $matchkey => $match ) {
                    $val = 0;
                    $pos = strrpos( $match, "." );
                    if ( $pos !== FALSE ) {
                        $field_id   = substr( $match, 0, $pos );
                        $type       = substr( $match, $pos + 1 );

                        $value          = 0;
                        $value_len      = 0;
                        $_origin_field  = $this->get_field_by_id( $option_fields, $field_id );

                        if( $_origin_field['general']['data_type'] == 'i' ){
                            if( $_origin_field['general']['input_type'] == 'n' || $_origin_field['general']['input_type'] == 'r' ){
                                $value = $fields[$field_id];
                            }
                            if( $_origin_field['general']['input_type'] == 't' || $_origin_field['general']['input_type'] == 'a' ){
                                $value_len =  strlen( $fields[$field_id] );
                            }
                        }

                        switch ( $type ) {
                            case 'price':
                                $val = ( isset( $_fields[$field_id] ) && isset( $_fields[$field_id]['price'] ) ) ? $_fields[$field_id]['price'] : 0;
                                break;
                            case 'value':
                                $val = $value;
                                break;
                            case 'value_length':
                                $val = $value_len;
                                break;
                        }
                    }

                    $formula = str_replace( $matches[0][ $matchkey ], $val, $formula );
                }
            }

            $formula = preg_replace( '/\s+/', '', $formula );
            $formula = rtrim( ltrim( $formula, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

            $eval_math = new EvalMath();
            $price = $formula ? $eval_math->evaluate( $formula ) : 0;
            return $price;
        }

        //CS botak gallery option (check condition change gallery
        public static function check_and_get_change_gallery($gallery_options, $fields, $quantity) {
            if($gallery_options) {
                foreach ($gallery_options as $option) {
                    $change = false;
                    $checks = [];

                    if( isset($option['gallery_enable_con']) && $option['gallery_enable_con'] === 'on' && count($option['gallery_depend']) > 0 ){
                        $logic = $option['gallery_con_logic'];
                        $total_check = $logic == 'a' ? true : false;

                        foreach ($option['gallery_depend'] as $key => $con) {
                            if( $con['id'] != '' ){
                                if ($con['id'] != 'qty' && ((!isset($con['id']) || !isset($fields[$con['id']])))) {
                                    $checks[$key] = false;
                                } else {
                                    if ( $con['id'] == 'qty' ) {
                                        $con['val'] = (int) $con['val'];
                                    }
                                    if(isset($fields[$con['id']]['value'])) {
                                        switch($con['operator']){
                                            case 'i':
                                                $checks[$key] = $fields[$con['id']]['value'] == $con['val'] ? true : false;
                                                break;
                                            case 'n':
                                                $checks[$key] = $fields[$con['id']]['value'] != $con['val'] ? true : false;
                                                break;  
                                            case 'e':
                                                $checks[$key] = $fields[$con['id']]['value'] == '' ? true : false;
                                                break;
                                            case 'ne':
                                                $checks[$key] = $fields[$con['id']]['value'] != '' ? true : false;
                                                break;
                                            case 'eq':
                                                $checks[$key] = $quantity == $con['val'] ? true : false;
                                                break;
                                            case 'gt':
                                                $checks[$key] = $quantity > $con['val'] ? true : false;
                                                break;
                                            case 'lt':
                                                $checks[$key] = $quantity < $con['val'] ? true : false;
                                                break;
                                        }
                                    }
                                }
                            } else {
                                $checks[$key] = true;
                            }
                        };

                        foreach ($checks as $check) {
                            $total_check = $logic == 'a' ? ($total_check && $check) : ($total_check || $check);
                        };
                        $change = $total_check;
                    }
                    
                    if ($change === true) {
                        return [
                            'change'    => true,
                            'option'    => $option
                        ];
                    }
                }
            }
            
            return [
                'change'    => false,
                'option'    => []
            ];
        }
        
        
        //CS botak measure price by role
        public static function calculate_measure_price($option_fields, $product_id = 0) {
            $user = wp_get_current_user();

            $_designer_setting = get_post_meta($product_id, '_designer_setting', true);
            $product_size = [
                'product_width'     => 0,
                'product_height'    => 0,
            ];
            if($option_fields['fields']) {
                foreach ($option_fields['fields'] as &$field) {
                    if (isset($field["nbd_type"])) {
                        switch ($field["nbd_type"]) {
                            case "size": 
                                if (isset($field["general"]["measure_price"]) && $field["general"]["measure_price"] === "y") {
                                    if (count($user->roles)) {
                                        $check = false;
                                        foreach ($field["general"]["role_measure_range"] as $role) {
                                            if ( $role['role'] === 'all') {
                                                $field = NBD_FRONTEND_PRINTING_OPTIONS::calc_option_field_price($field, $role, $product_id);
                                                $check = true;
                                                break;
                                            }
                                        }
                                        if (!$check) {
                                            foreach ($field["general"]["role_measure_range"] as $role) {
                                                if ( in_array( $role['role'], (array) $user->roles )) {
                                                    $field = NBD_FRONTEND_PRINTING_OPTIONS::calc_option_field_price($field, $role, $product_id);
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($field["general"]["role_measure_range"] as $role) {
                                            if ( $role['default'] == 1 || $role['default'] == 'on') {
                                                $field = NBD_FRONTEND_PRINTING_OPTIONS::calc_option_field_price($field, $role, $product_id);
                                                break;
                                            }
                                        }
                                    }
                                }
                                break;
                            case "dimension":
                                if (isset($field["general"]["measure"]) && $field["general"]["measure"] === "y") {
                                    if (count($user->roles)) {
                                        $check = false;
                                        foreach ($field["general"]["role_measure_range"] as $role) {
                                            if ( $role['role'] === 'all' ) {
                                                $field["general"]["default"] = $role['default'];
                                                $field["general"]["multi_measure"] = $role['multi_measure'];
                                                $check = true;
                                                break;
                                            }
                                        }
                                        if (!$check) {
                                            foreach ($field["general"]["role_measure_range"] as $role) {
                                                if ( in_array( $role['role'], (array) $user->roles ) ) {
                                                    $field["general"]["default"] = $role['default'];
                                                    $field["general"]["multi_measure"] = $role['multi_measure'];
                                                    $check = true;
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        foreach ($field["general"]["role_measure_range"] as $role) {
                                            if ( $role['default'] == 1 || $role['default'] == 'on') {
                                                $field["general"]["default"] = $role['default'];
                                                $field["general"]["multi_measure"] = $role['multi_measure'];
                                                break;
                                            }
                                        }
                                    }
                                }
                                break;
                            case "pricing_rates":
                                if ($_designer_setting) {
                                    $_designer_setting = unserialize($_designer_setting);
                                    if ($_designer_setting) {
                                        $first_side = $_designer_setting[0];
                                        $product_size['product_width'] = $first_side['product_width'];
                                        $product_size['product_height'] = $first_side['product_height'];
                                    }
                                }
                                foreach ($field["general"]["attributes"]["options"] as &$option) {
                                    foreach ($option["role_pricing_rates"] as $role) {
                                        //Convert measure_unit to sqmm
                                        switch ($role['measure_unit']) {
                                            case "sqmm":
                                                $measure_unit_reate = 1;
                                                break;
                                            case "sqcm":
                                                $measure_unit_reate = 10;
                                                break;
                                            case "sqin":
                                                $measure_unit_reate = 25.4;
                                                break;
                                            case "sqft":
                                                $measure_unit_reate = 304.8;
                                                break;
                                            default:
                                                $measure_unit_reate = 1;
                                                break;
                                        }

                                        //Convert nbdesigner_dimensions_unit to mm
                                        switch (nbdesigner_get_option('nbdesigner_dimensions_unit')) {
                                            case "mm":
                                                $nbdesigner_dimensions_unit_reate = 1;
                                                break;
                                            case "cm":
                                                $nbdesigner_dimensions_unit_reate = 10;
                                                break;
                                            case "in":
                                                $nbdesigner_dimensions_unit_reate = 25.4;
                                                break;
                                            case "ft":
                                                $nbdesigner_dimensions_unit_reate = 304.8;
                                                break;
                                            default:
                                                $nbdesigner_dimensions_unit_reate = 1;
                                                break;
                                        }

                                        //Async measure_unit with nbdesigner_dimensions_unit
                                        $unit_rate = $nbdesigner_dimensions_unit_reate / $measure_unit_reate;

                                        if ( in_array( $role['role'], (array) $user->roles ) ) {
                                            switch ($role['role_calc_method']) {
                                                case 'area':
                                                    $option['price'][0] = strval((float) $product_size['product_width'] * $unit_rate * (float) $product_size['product_height'] * $unit_rate * (float) $role['role_rate']);
                                                    break;
                                                case 'perimeter':
                                                    $option['price'][0] = strval(2 * ((float) $product_size['product_width'] * $unit_rate + (float) $product_size['product_height']) * $unit_rate * (float) $role['role_rate']);
                                                    break;
                                                case 'quantity':
                                                    $option['price'][0] = strval((int) $role['role_quantity'] * (float) $role['role_rate']);
                                                    break;
                                            }
                                            $option['rate'] = $role['role_rate'];
                                            $option['quantity'] = $role['role_quantity'];
                                            $option['calc_method'] = $role['role_calc_method'];
                                            break;
                                        } else {
                                            $option['price'][0] = '0';
                                            $option['rate'] = '0';
                                            $option['quantity'] = '0';
                                            $option['calc_method'] = '0';
                                        }
                                    }
                                }
                                break;
                            case "calculation_option":
                                foreach ($field["general"]["attributes"]["options"] as &$option) {
                                    $check = false;
                                    $option['measure_unit'] = 'sqmm';
                                    $option['measure_range'] = [['0', '0', '0']];
                                    foreach ($option['role_calculation_option'] as $role) {
                                        if ( in_array( $role['role'], (array) $user->roles ) ) {
                                            $option['measure_unit'] = $role['measure_unit'];
                                            $option['measure_range'] = $role['measure_range'];
                                            $check = true;
                                            break;
                                        }
                                    };
                                    if (!$check) {
                                        foreach ($option['role_calculation_option'] as $role) {
                                            if ( isset($role['default']) && ($role['default'] == 1 || $role['default'] == 'on')) {
                                                $option['measure_unit'] = $role['measure_unit'];
                                                $option['measure_range'] = $role['measure_range'];
                                                break;
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                    }
                }
            }
            return $option_fields;
        }
        public static function calc_option_field_price( $field, $role, $product_id ){
            //Convert nbdesigner_dimensions_unit to mm
            switch (nbdesigner_get_option('nbdesigner_dimensions_unit')) {
                case "mm":
                    $nbdesigner_dimensions_unit_reate = 1;
                    break;
                case "cm":
                    $nbdesigner_dimensions_unit_reate = 10;
                    break;
                case "in":
                    $nbdesigner_dimensions_unit_reate = 25.4;
                    break;
                case "ft":
                    $nbdesigner_dimensions_unit_reate = 304.8;
                    break;
                default:
                    $nbdesigner_dimensions_unit_reate = 1;
                    break;
            }
            
            $product_size = [
                'product_width'     => 0,
                'product_height'    => 0,
            ];
            $_designer_setting = unserialize(get_post_meta($product_id, '_designer_setting', true));
            if ($_designer_setting) {
                $first_side = $_designer_setting[0];
                $product_size['product_width'] = (float) $first_side['product_width'];
                $product_size['product_height'] = (float) $first_side['product_height'];
            }
            
            foreach ($field["general"]["attributes"]["options"] as &$option) {
                if ($field["general"]["attributes"]["same_size"] === 'n') {
                    $product_size['product_width'] = (float) $option['product_width'];
                    $product_size['product_height'] = (float) $option['product_height'];
                };
                $addition_price = 0;
                
                foreach ($role['multi_measure'] as $measure) {
                    //Convert measure_unit to sqmm
                    switch ($measure['measure_unit']) {
                        case "sqmm":
                            $measure_unit_reate = 1;
                            break;
                        case "sqcm":
                            $measure_unit_reate = 10;
                            break;
                        case "sqin":
                            $measure_unit_reate = 25.4;
                            break;
                        case "sqft":
                            $measure_unit_reate = 304.8;
                            break;
                        default:
                            $measure_unit_reate = 1;
                            break;
                    }

                    //Async measure_unit with nbdesigner_dimensions_unit
                    $unit_rate = $nbdesigner_dimensions_unit_reate / $measure_unit_reate;

                    switch ($measure['caculation_method']) {
                        case 'area':
                            $calc_value = $product_size['product_width'] * $unit_rate * $product_size['product_height']* $unit_rate; // (width * height)
                            break;
                        case 'perimeter':
                            $calc_value = 2 * ($product_size['product_width'] * $unit_rate + $product_size['product_height'] * $unit_rate); // 2 * (width + height)
                            break;
                        case 'top-bottom':
                            $calc_value = 2 * $product_size['product_width'] * $unit_rate; // 2 * width * rate
                            break;
                        case 'left-right':
                            $calc_value = 2 * $product_size['product_height'] * $unit_rate; // 2 * height * rate
                            break;
                        case 'custom-formula':
                            $width = $product_size['product_width'] * $unit_rate;
                            $height = $product_size['product_height'] * $unit_rate;
                            $custom_formular = $measure['custom_formular'];
                            $custom_formular = str_replace("{width}", $width, $custom_formular);
                            $custom_formular = str_replace("{height}", $height, $custom_formular);
                            // $custom_formular = str_replace("{rate}", $rate, $custom_formular);

                            // sanitize imput
                            $custom_formular = preg_replace("/[^a-z0-9+\-.*\/()%]/","",$custom_formular);
                            // convert alphabet to $variabel 
                            $custom_formular = preg_replace("/([a-z])+/i", "\$$0", $custom_formular); 
                            // convert percentages to decimal
                            $custom_formular = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$custom_formular);
                            $custom_formular = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$custom_formular);
                            $custom_formular = preg_replace("/([0-9]{1})(%)/",".0\$1",$custom_formular);
                            $custom_formular = preg_replace("/([0-9]+)(%)/",".\$1",$custom_formular);

                            if ( $custom_formular != "" ){
                                $calc_value = eval('return ' . $custom_formular . ';');
                            }
                            if ($calc_value == null) {
                                $calc_value = 0;
                                throw new Exception("Error. Please check calculation syntax!");
                            }

                            break;
                        default :
                            $calc_value = 0;
                    }

                    $rate = 0;
                    if ( $measure["measure_type"] === "u" ) {
                        $measure_range = $measure["measure_range"][0];
                        $rate = $measure_range[2] ? (float) $measure_range[2] : 1;
                    } else {
                        foreach ($measure["measure_range"] as $key => $measure_range) {
                            if ($measure_range[0] != '' && $measure_range[1] != '' && $measure_range[2] != '') {
                                if (((float) $measure_range[0] <= $calc_value && ($calc_value <= (float) $measure_range[1] || (float) $measure_range[1] == 0))
                                    || ((float) $measure_range[0] <= $calc_value && $key == ( count($measure["measure_range"]) - 1 ) && $calc_value > (float) $measure_range[1])) {
                                    $rate = $measure_range[2] ? (float) $measure_range[2] : 1;
                                }
                            }
                        }
                    }

                    $price = $calc_value * (float) $rate;
                    if ($price < (float) $measure['minimum_price']) {
                        $price = $measure['minimum_price'];
                    }
                    $addition_price += $price;
                }
                
                $option['price'] = [$addition_price];
            }
            return $field;
        }
        //End CS botak measure price by role
        public function build_turnaround_matrix( $options, $field ){
            $turnaround_matrix  = array();
            $quantity_breaks    = array();
            foreach( $options['quantity_breaks'] as $break ){
                $quantity_breaks[] = absint($break['val']);
            }
            foreach( $quantity_breaks as $key => $break ){
                $turnaround_matrix[ $key ] = array();
                $qty = absint( $break );
                foreach( $field['general']['attributes']['options'] as $k => $option ){
                    $active  = 0;
                    $max_qty = absint( $option['max_qty'] );
                    if( $option['max_qty'] == '' || $max_qty >= $qty ) $active = 1;
                    $turnaround_matrix[ $key ][ $k ] = $active;
                }
            }
            return $turnaround_matrix;
        }
        public function is_independent_qty( $field ){
            if( isset( $field['general']['depend_qty'] ) && $field['general']['depend_qty'] == 'n' ){
                return true;
            }else{
                return false;
            }
        }
        public function is_fixed_amount( $field ){
            if( isset( $field['general']['depend_qty'] ) && $field['general']['depend_qty'] == 'n2' ){
                return true;
            }else{
                return false;
            }
        }
        public function calculate_price_base_measurement( $origin_field, $width, $height){            
            //Convert nbdesigner_dimensions_unit to mm
            switch (nbdesigner_get_option('nbdesigner_dimensions_unit')) {
                case "mm":
                    $nbdesigner_dimensions_unit_reate = 1;
                    break;
                case "cm":
                    $nbdesigner_dimensions_unit_reate = 10;
                    break;
                case "in":
                    $nbdesigner_dimensions_unit_reate = 25.4;
                    break;
                case "ft":
                    $nbdesigner_dimensions_unit_reate = 304.8;
                    break;
                default:
                    $nbdesigner_dimensions_unit_reate = 1;
                    break;
            }
            
            //foreach($measure_range as $key => $range){
            //    $start_range    = floatval($range[0]);
            //    $end_range      = floatval($range[1]);
            //    $price_range    = floatval($range[2]);
            //    if( $start_range <= $area && ( $area <= $end_range || $end_range == 0 ) ){
            //        $price_per_unit = $price_range;
            //    }
            //    if( $start_range <= $area && $key == ( count($measure_range) - 1 ) && $area > $end_range  ){
            //        $price_per_unit = $price_range;
            //    }
            //}
            // if( isset( $origin_field['general']['measure_type'] ) && $origin_field['general']['measure_type'] == 'r' ) return $price_per_unit;
            
            //CS botak dimention break
            $addition_price = 0;
            foreach ($origin_field['general']['multi_measure'] as $measure) {
                $price_per_unit = 0;

                //Convert measure_unit to sqmm
                switch ($measure['measure_unit']) {
                    case "sqmm":
                        $measure_unit_reate = 1;
                        break;
                    case "sqcm":
                        $measure_unit_reate = 10;
                        break;
                    case "sqin":
                        $measure_unit_reate = 25.4;
                        break;
                    case "sqft":
                        $measure_unit_reate = 304.8;
                        break;
                    default:
                        $measure_unit_reate = 1;
                        break;
                }

                //Async measure_unit with nbdesigner_dimensions_unit
                $unit_rate = $nbdesigner_dimensions_unit_reate / $measure_unit_reate;

                switch ($measure['caculation_method']) {
                    case 'area':
                        $calc_value = (float) $width * $unit_rate * (float) $height * $unit_rate; // (width * height)
                        break;
                    case 'perimeter':
                        $calc_value = 2 * ((float) $width * $unit_rate + (float) $height * $unit_rate); // 2 * (width + height)
                        break;
                    case 'top-bottom':
                        $calc_value = 2 * (float) $width * $unit_rate; // 2 * width * rate
                        break;
                    case 'left-right':
                        $calc_value = 2 * (float) $height * $unit_rate; // 2 * height * rate
                        break;
                    case 'custom-formula':
                        $custom_formular = $measure['custom_formular'];
                        $custom_formular = str_replace("{width}", $width, $custom_formular);
                        $custom_formular = str_replace("{height}", $height, $custom_formular);
                        // $custom_formular = str_replace("{rate}", $rate, $custom_formular);

                        // sanitize imput
                        $custom_formular = preg_replace("/[^a-z0-9+\-.*\/()%]/","",$custom_formular);
                        // convert alphabet to $variabel 
                        $custom_formular = preg_replace("/([a-z])+/i", "\$$0", $custom_formular); 
                        // convert percentages to decimal
                        $custom_formular = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$custom_formular);
                        $custom_formular = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$custom_formular);
                        $custom_formular = preg_replace("/([0-9]{1})(%)/",".0\$1",$custom_formular);
                        $custom_formular = preg_replace("/([0-9]+)(%)/",".\$1",$custom_formular);

                        if ( $custom_formular != "" ){
                            $calc_value = eval('return ' . $custom_formular . ';');
                        }
                        if ($calc_value == null) {
                            $calc_value = 0;
                            throw new Exception("Error. Please check calculation syntax!");
                        }

                        break;
                    default :
                        $calc_value = 0;
                }

                if ( $measure["measure_type"] === "u" ) {
                    $measure_range = $measure["measure_range"][0];
                    $price_per_unit = $measure_range[2] ? (float) $measure_range[2] : 1;
                } else if ( $measure["measure_type"] === "r" ) {
                    foreach ($measure["measure_range"] as $key => $measure_range) {
                        if ($measure_range[0] != '' && $measure_range[1] != '' && $measure_range[2] != '') {
                            if (((float) $measure_range[0] <= $calc_value && ($calc_value <= (float) $measure_range[1] || (float) $measure_range[1] == 0))
                                || ((float) $measure_range[0] <= $calc_value && $key == ( count($measure["measure_range"]) - 1 ) && $calc_value > (float) $measure_range[1])) {
                                $price_per_unit = $measure_range[2] ? (float) $measure_range[2] : 1;
                            }
                        }
                    }
                }

                $price = $calc_value * (float) $price_per_unit;

                if ($price < (float) $measure['minimum_price']) {
                    $price = $measure['minimum_price'];
                }
                $addition_price += $price;
            }
            
            return $addition_price;
            //End CS botak dimention break
        }
        /*
         * Calculate option price for calculation option with product width, height
         * @param object $origin_field
         * @param float $width
         * @param float $height
         * @returns object $origin_field
         */
        public function calculate_price_calculation_option( $origin_field, $width, $height){    
            //Convert nbdesigner_dimensions_unit to mm
            switch (nbdesigner_get_option('nbdesigner_dimensions_unit')) {
                case "mm":
                    $nbdesigner_dimensions_unit_reate = 1;
                    break;
                case "cm":
                    $nbdesigner_dimensions_unit_reate = 10;
                    break;
                case "in":
                    $nbdesigner_dimensions_unit_reate = 25.4;
                    break;
                case "ft":
                    $nbdesigner_dimensions_unit_reate = 304.8;
                    break;
                default:
                    $nbdesigner_dimensions_unit_reate = 1;
                    break;
            }
            
            //CS botak dimention break
            $addition_price = 0;
            foreach ($origin_field['general']['attributes']['options'] as &$option) {
                //Convert measure_unit to sqmm
                switch ($option['measure_unit']) {
                    case "sqmm":
                        $measure_unit_reate = 1;
                        break;
                    case "sqcm":
                        $measure_unit_reate = 10;
                        break;
                    case "sqin":
                        $measure_unit_reate = 25.4;
                        break;
                    case "sqft":
                        $measure_unit_reate = 304.8;
                        break;
                    default:
                        $measure_unit_reate = 1;
                        break;
                }

                //Async measure_unit with nbdesigner_dimensions_unit
                $unit_rate = $nbdesigner_dimensions_unit_reate / $measure_unit_reate;

                switch ($origin_field['general']['caculation_method']) {
                    case 'area':
                        $calc_value = (float) $width * $unit_rate * (float) $height * $unit_rate; // (width * height)
                        break;
                    case 'perimeter':
                        $calc_value = 2 * ((float) $width * $unit_rate + (float) $height * $unit_rate); // 2 * (width + height)
                        break;
                    case 'top-bottom':
                        $calc_value = 2 * (float) $width * $unit_rate; // 2 * width * rate
                        break;
                    case 'left-right':
                        $calc_value = 2 * (float) $height * $unit_rate; // 2 * height * rate
                        break;
                    case 'custom-formula':
                        $custom_formular = $origin_field['general']['custom_formular'];
                        $custom_formular = str_replace("{width}", $width * $unit_rate, $custom_formular);
                        $custom_formular = str_replace("{height}", $height * $unit_rate, $custom_formular);
                        // $custom_formular = str_replace("{rate}", $rate, $custom_formular);

                        // sanitize imput
                        $custom_formular = preg_replace("/[^a-z0-9+\-.*\/()%]/","",$custom_formular);
                        // convert alphabet to $variabel 
                        $custom_formular = preg_replace("/([a-z])+/i", "\$$0", $custom_formular); 
                        // convert percentages to decimal
                        $custom_formular = preg_replace("/([+-])([0-9]{1})(%)/","*(1\$1.0\$2)",$custom_formular);
                        $custom_formular = preg_replace("/([+-])([0-9]+)(%)/","*(1\$1.\$2)",$custom_formular);
                        $custom_formular = preg_replace("/([0-9]{1})(%)/",".0\$1",$custom_formular);
                        $custom_formular = preg_replace("/([0-9]+)(%)/",".\$1",$custom_formular);

                        if ( $custom_formular != "" ){
                            $calc_value = eval('return ' . $custom_formular . ';');
                        }
                        if ($calc_value == null) {
                            $calc_value = 0;
                            throw new Exception("Error. Please check calculation syntax!");
                        }

                        break;
                    default :
                        $calc_value = 0;
                }

                if ( $origin_field['general']["measure_type"] === "u" ) {
                    $measure_range = $option["measure_range"][0];
                    $price_per_unit = $measure_range[2] ? (float) $measure_range[2] : 0;
                } else if ( $origin_field['general']["measure_type"] === "r" ) {
                    foreach ($option["measure_range"] as $key => $measure_range) {
                        if ($measure_range[0] != '' && $measure_range[1] != '' && $measure_range[2] != '') {
                            if (((float) $measure_range[0] <= $calc_value && ($calc_value <= (float) $measure_range[1] || (float) $measure_range[1] == 0))
                                || ((float) $measure_range[0] <= $calc_value && $key == ( count($option["measure_range"]) - 1 ) && $calc_value > (float) $measure_range[1])) {
                                $price_per_unit = $measure_range[2] ? (float) $measure_range[2] : 0;
                            }
                        }
                        if ( !$measure_range[0] && !$measure_range[1] && $measure_range[2] != '') {
                            $price_per_unit = $measure_range[2] ? (float) $measure_range[2] : 0;
                        }
                    }
                }

                $price = $calc_value * (float) $price_per_unit;

                if ($price < (float) $origin_field['general']['minimum_price']) {
                    $price = $origin_field['general']['minimum_price'];
                }
                if($origin_field['general']["measure"] == 'y') {
                    $option['price'][0] = strval($price);
                }
            }
            
            return $origin_field;
            //End CS botak dimention break
        }
        public function get_quantity_break( $options, $quantity ){
            $quantity_break     = array('index' =>  0, 'oparator' => 'gt');
            $quantity_breaks    = array();
            foreach( $options['quantity_breaks'] as $break ){
                $quantity_breaks[] = absint($break['val']);
            }
            foreach($quantity_breaks as $key => $break){
                if( $key == 0 && $quantity < $break){
                    $quantity_break = array('index' =>  0, 'oparator' => 'lt');
                }
                if( $quantity >= $break && $key < ( count( $quantity_breaks ) - 1 ) ){
                    $quantity_break = array('index' =>  $key, 'oparator' => 'bw');
                }
                if( $key == ( count( $quantity_breaks ) - 1 ) && $quantity >= $break){
                    $quantity_break = array('index' =>  $key, 'oparator' => 'gt');
                }
            }
            return $quantity_break;
        }
        public function set_product_prices( $cart_item ){
            if ( isset( $cart_item['nbo_meta'] )){
                $new_price = (float) $cart_item['nbo_meta']['price'];
                $needed_change = apply_filters('nbo_need_change_cart_item_price', true, $cart_item);
                if( $needed_change ) $cart_item['data']->set_price( $new_price );
            }
            return $cart_item;
        }
        public function nbd_js_object( $args ){
            $args['wc_currency_format_num_decimals'] = wc_get_price_decimals();
            $args['currency_format_num_decimals'] = nbdesigner_get_option( 'nbdesigner_number_of_decimals', 4 );
            $args['currency_format_symbol'] = get_woocommerce_currency_symbol();
            $args['currency_format_decimal_sep'] = stripslashes( wc_get_price_decimal_separator() );
            $args['currency_format_thousand_sep'] = stripslashes( wc_get_price_thousand_separator() );
            $args['currency_format'] = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format()) );
            $args['nbdesigner_hide_add_cart_until_form_filled'] = nbdesigner_get_option('nbdesigner_hide_add_cart_until_form_filled');
            $args['total'] = __('Total', 'web-to-print-online-designer');
            $args['check_invalid_fields'] = __('Please check invalid fields and quantity input!', 'web-to-print-online-designer');
            $args['ajax_cart'] = nbdesigner_get_option('nbdesigner_enable_ajax_cart', 'no');
            $args['nbo_qv_url'] = add_query_arg(
                urlencode_deep( array(
                    'wc-api'    => 'NBO_Quick_View',
                    'mode'      => 'catalog'
                ) ),
                home_url( '/' )
            );
            return $args;
        }
        public function nbd_depend_js( $depends ){
            $depends[] = 'wc-add-to-cart-variation';
            return $depends;
        }
        public function wp_enqueue_scripts(){
            wp_register_script('angularjs', NBDESIGNER_PLUGIN_URL . 'assets/libs/angular-1.6.9.min.js', array('jquery', 'accounting'), '1.6.9');
            if(nbdesigner_get_option('nbdesigner_enable_angular_js') == 'yes'){
                wp_enqueue_script(array('angularjs'));
            }
            if( nbdesigner_get_option('nbdesigner_enable_ajax_cart', 'no') == 'yes' ){
                wp_enqueue_script( 'wc-add-to-cart-variation' );
            }
        }
        public function show_option_fields(){
            global $product;
            $product_id = $product->get_id();
            $items = nbd_get_items_product_grouped($product_id);
            if($items) {
                $grouped_id = $items[0]['id'];
            }
            if(isset($grouped_id)) {
                $product_id = $grouped_id;
            } else {
                $product_id = $product->get_id();
            }
            $option_id = $this->get_product_option( $product_id );
            if( $option_id ){
                $_options = $this->get_option( $option_id );
                if( $_options ){
                    $options = unserialize($_options['fields']);
                    if( !isset($options['fields']) ){
                        $options['fields'] = array();
                    }
                    $options['fields'] = $this->recursive_stripslashes( $options['fields'] );
                    foreach ($options['fields'] as $key => $field){
                        if( !isset($field['general']['attributes']) ){
                            $field['general']['attributes'] = array();
                            $field['general']['attributes']['options'] = array();
                            $options['fields'][$key]['general']['attributes'] = array();
                            $options['fields'][$key]['general']['attributes']['options'] = array();
                        }
                        /* //CS botak change product gallery old 
                        if($field['appearance']['change_image_product'] == 'y'){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                $option['product_image'] = isset($option['product_image']) ? $option['product_image'] : 0;
                                $attachment_id = absint( $option['product_image'] );
                                $attachment_id = $attachment_id != 0 ? $attachment_id : absint( $product->get_image_id() ); //CS botak image gallery options
                                if( $attachment_id != 0 ){
                                    $image_link         = wp_get_attachment_url( $attachment_id );
                                    $attachment_object  = get_post( $attachment_id );
                                    $full_src           = wp_get_attachment_image_src( $attachment_id, 'large' );
                                    $image_title        = get_the_title( $attachment_id );
                                    $image_alt          = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', TRUE ) ) );
                                    $image_srcset       = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : FALSE;
                                    $image_sizes        = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : FALSE;
                                    $image_caption      = $attachment_object->post_excerpt;
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index] = array_replace_recursive($options['fields'][$key]['general']['attributes']['options'][$op_index], array(
                                        'imagep'        => 'y',
                                        'image_link'    => $image_link,
                                        'image_title'   => $image_title,
                                        'image_alt'     => $image_alt,
                                        'image_srcset'  => $image_srcset,
                                        'image_sizes'   => $image_sizes,
                                        'image_caption' => $image_caption,
                                        'full_src'      => $full_src[0],
                                        'full_src_w'    => $full_src[1],
                                        'full_src_h'    => $full_src[2]
                                    ));
                                }else{
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['imagep'] = 'n';
                                }
                                if (isset($options['fields'][$key]['general']['attributes']['options'][$op_index]['product_images']) && is_array($options['fields'][$key]['general']['attributes']['options'][$op_index]['product_images'])) {
                                    foreach ($options['fields'][$key]['general']['attributes']['options'][$op_index]['product_images'] as &$image) {
                                        $image_id = isset($image['product_image']) ? $image['product_image'] : 0;
                                        $att_id = absint( $image_id );
                                        if( $att_id != 0 ){
                                            $options['fields'][$key]['general']['attributes']['options'][$op_index]['imagep'] = 'y';
                                            $image_link         = wp_get_attachment_url( $att_id );
                                            $attachment_object  = get_post( $att_id );
                                            $full_src           = wp_get_attachment_image_src( $att_id, 'large' );
                                            $image_title        = get_the_title( $att_id );
                                            $image_alt          = trim( strip_tags( get_post_meta( $att_id, '_wp_attachment_image_alt', TRUE ) ) );
                                            $image_srcset       = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $att_id, 'shop_single' ) : FALSE;
                                            $image_sizes        = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $att_id, 'shop_single' ) : FALSE;
                                            $image_caption      = $attachment_object->post_excerpt;
                                            $image = array(
                                                'imagep'        => 'y',
                                                'image_link'    => $image_link,
                                                'image_title'   => $image_title,
                                                'image_alt'     => $image_alt,
                                                'image_srcset'  => $image_srcset,
                                                'image_sizes'   => $image_sizes,
                                                'image_caption' => $image_caption,
                                                'full_src'      => $full_src[0],
                                                'full_src_w'    => $full_src[1],
                                                'full_src_h'    => $full_src[2]
                                            );
                                        }
                                    }
                                };
                            }
                        } */
                        if( isset($field['nbpb_type']) && $field['nbpb_type'] == 'nbpb_com' ){
                            if( isset($field['general']['pb_config']) ){
                                foreach( $field['general']['pb_config'] as $a_index => $attr ){
                                    foreach( $attr as $s_index => $sattr ){
                                        foreach( $sattr['views'] as $v_index => $view ){
                                            $pb_image_obj = wp_get_attachment_url( absint($view['image']) );
                                            $options['fields'][$key]['general']['pb_config'][$a_index][$s_index]['views'][$v_index]['image_url'] =  $pb_image_obj ? $pb_image_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                                        }
                                    }
                                }
                            }else{
                                $field['general']['pb_config'] = array();
                            }
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                                    foreach( $option['sub_attributes'] as $sa_index => $sattr ){
                                        $options['fields'][$key]['general']['attributes']['options'][$op_index]['sub_attributes'][$sa_index]['image_url'] = nbd_get_image_thumbnail( $sattr['image'] );
                                    }
                                }else{
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = nbd_get_image_thumbnail( $option['image'] );
                                }
                            };
                            $options['fields'][$key]['general']['component_icon_url'] = nbd_get_image_thumbnail( $field['general']['component_icon'] );
                        }
                        if( isset($field['general']['attributes']['bg_type']) && $field['general']['attributes']['bg_type'] == 'i' ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                foreach( $option['bg_image'] as $bg_index => $bg ){
                                    $bg_obj = wp_get_attachment_url( absint($bg) );
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['bg_image_url'][$bg_index] = $bg_obj ? $bg_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                                }
                            };
                        }
                        if( isset($field['nbd_type']) && $field['nbd_type'] == 'overlay' ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                foreach( $option['overlay_image'] as $ov_index => $ov ){
                                    $ov_obj = wp_get_attachment_url( absint($ov) );
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image_url'][$ov_index] = $ov_obj ? $ov_obj : '';
                                }
                            };
                        }
                        if( isset($field['nbe_type']) && $field['nbe_type'] == 'frame' ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                $fr_obj = wp_get_attachment_url( absint($option['image']) );
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = $fr_obj ? $fr_obj : '';
                            };
                        }
                        //CS botak overlay option
                        if( isset($field['nbd_type']) ) {
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                if (isset($options['fields'][$key]['general']['attributes']['options'][$op_index]['show_overlay']) && $options['fields'][$key]['general']['attributes']['options'][$op_index]['show_overlay'] == 'on') {
                                    if ($options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image'] != 0 || $options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image'] != '' || $options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image'] != '0') {
                                        $options['fields'][$key]['general']['attributes']['options'][$op_index]['img_overlay'] = wp_get_attachment_url($options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image']);
                                    }
                                }
                            };
                        }
                    }
                    if( isset($options['views']) ){
                        foreach ($options['views'] as $vkey => $view){
                            $view['base'] = isset($view['base']) ? $view['base'] : 0;
                            $options['views'][$vkey]['base'] = $view['base'];
                            $view_bg_obj = wp_get_attachment_url( absint($view['base']) );
                            $options['views'][$vkey]['base_url'] = $view_bg_obj ? $view_bg_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                        }
                    }
                    $product        = wc_get_product($product_id);
                    $type           = $product->get_type();
                    $variations     = array();
                    $form_values    = array();
                    $cart_item_key  = '';
                    $quantity       = 1;
                    $nbdpb_enable   = get_post_meta($product_id, '_nbdpb_enable', true);
                    //CS botak quantity break by role
                    if ($options['quantity_enable'] == 'y') {
                        $user = wp_get_current_user();
                        if (is_object($user)) {
                            foreach ($options['role_breaks'] as $role_index => $role_breaks) {
                                if ( in_array( $role_breaks['role'], (array) $user->roles ) ) {
                                    foreach( $role_breaks['quantity_breaks'] as $break) {
                                        if( isset( $break['default'] ) && $break['default'] == 'on' ){
                                            $quantity = $break['val'];
                                        }
                                    }
                                } 
                            } 
                        }
                    }
                    
                    //CS botak product gallery option 
                    foreach ($options['gallery_options'] as $gallery_index => &$gallery_option) {
                        if (isset($gallery_option['product_images']) && is_array($gallery_option['product_images'])) {
                            foreach ($gallery_option['product_images'] as &$image) {
                                $image_id = isset($image['product_image']) ? $image['product_image'] : 0;
                                $att_id = absint( $image_id );
                                if( $att_id != 0 ){
                                    $image_link         = wp_get_attachment_url( $att_id );
                                    $attachment_object  = get_post( $att_id );
                                    $full_src           = wp_get_attachment_image_src( $att_id, 'large' );
                                    $image_title        = get_the_title( $att_id );
                                    $image_alt          = trim( strip_tags( get_post_meta( $att_id, '_wp_attachment_image_alt', TRUE ) ) );
                                    $image_srcset       = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $att_id, 'shop_single' ) : FALSE;
                                    $image_sizes        = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $att_id, 'shop_single' ) : FALSE;
                                    $image_caption      = $attachment_object->post_excerpt;
                                    $image = array(
                                        'imagep'        => 'y',
                                        'image_link'    => $image_link,
                                        'image_title'   => $image_title,
                                        'image_alt'     => $image_alt,
                                        'image_srcset'  => $image_srcset,
                                        'image_sizes'   => $image_sizes,
                                        'image_caption' => $image_caption,
                                        'full_src'      => $full_src[0],
                                        'full_src_w'    => $full_src[1],
                                        'full_src_h'    => $full_src[2]
                                    );
                                }
                            }
                        };
                    };
                    //End CS botak product gallery option
                    
                    if( isset($_POST['nbd-field']) ){
                        $form_values = $_POST['nbd-field'];
                        if( isset($_POST["nbo-quantity"]) ){
                            $quantity = $_POST["nbo-quantity"];
                        }
                    }else if( isset($_GET['nbo_cart_item_key']) && $_GET['nbo_cart_item_key'] != '' ){
                        $cart_item_key = $_GET['nbo_cart_item_key'];
                        $cart_item = WC()->cart->get_cart_item( $cart_item_key );
                        if( isset($cart_item['nbo_meta']) ){
                            $form_values = $cart_item['nbo_meta']['field'];
                        }
                        if ( isset( $cart_item["quantity"] ) ) {
                            $quantity = $cart_item["quantity"];
                        }
                    }

                    if( isset( $_GET['nbo_values'] ) ){
                        $params     = array();
                        $value_str  = base64_decode( wc_clean( $_GET['nbo_values'] ) );
                        parse_str( $value_str, $params );
                        if( isset( $params['nbd-field'] ) ){
                            $form_values = $params['nbd-field'];
                        }
                        if ( isset( $params["qty"] ) ) {
                            $quantity = $params["qty"];
                        }
                    }

                    if( $type == 'variable' ){
                        $all = get_posts( array(
                            'post_parent' => $product_id,
                            'post_type'   => 'product_variation',
                            'orderby'     => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
                            'post_status' => 'publish',
                            'numberposts' => -1,
                        ));
                        foreach ( $all as $child ) {
                            $vid = $child->ID;
                            $variation = wc_get_product( $vid );
                            $variations[$vid] = $variation->get_price( 'edit' );
                        }
                    }

                    $options = apply_filters( 'nbo_product_options', $options, $product_id );

                    ob_start();
                    nbdesigner_get_template('single-product/option-builder.php', array(
                        'product_id'            => $product_id,
                        'options'               => $options,
                        'type'                  => $type,
                        'quantity'              => $quantity,
                        'nbdpb_enable'          => $nbdpb_enable,
                        'price'                 => $product->get_price( 'edit' ),
                        'is_sold_individually'  => $product->is_sold_individually(),
                        'variations'            => json_encode( (array) $variations ),
                        'form_values'           => $form_values,
                        'cart_item_key'         => $cart_item_key,
                        'change_base'           => nbdesigner_get_option('nbdesigner_change_base_price_html'),
                        'tooltip_position'      => nbdesigner_get_option('nbdesigner_tooltip_position'),
                        'hide_zero_price'       => nbdesigner_get_option('nbdesigner_hide_zero_price')
                    ));
                    $options_form = ob_get_clean();
                    echo $options_form;
                }
            }
        }
        public static function get_product_option($product_id){
            $enable = get_post_meta($product_id, '_nbo_enable', true);
            if( !$enable ) return false;
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
                            $_selected_options  = self::get_option( $option_id );
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
        public static function get_option( $id ){
            global $wpdb;
            $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
            $sql .= " WHERE id = " . esc_sql($id);
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            return count($result[0]) ? $result[0] : false;
        }
        public function bulk_order( $ajax = false, $return = false, $prevent_redirect = false ){
            $bulk_fields = $_REQUEST['nbb-fields'];
            if( !is_array($bulk_fields) ) return false;
            $nbd_field      = isset( $_REQUEST['nbd-field'] ) ? $_REQUEST['nbd-field'] : array();
            $qtys           = $_REQUEST['nbb-qty-fields'];
            $first_field    = reset($bulk_fields);
            // Gather bulk form fields.
            $nbb_fields = array();
            for( $i=0; $i < count($first_field); $i++ ){
                $arr = array();
                foreach($nbd_field as $field_id => $field_value){
                    if( !isset($bulk_fields[$field_id]) ){
                        $arr[$field_id] = $field_value;
                    }
                }
                foreach($bulk_fields as $field_id => $bulk_field){
                    $arr[$field_id] = $bulk_field[$i];
                }
                $nbb_fields[] = $arr;
            }
            $product_id         = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : $_REQUEST['nbo-add-to-cart']; 
            $added_count        = 0;
            $failed_count       = 0;
            $success_message    = '';
            $error_message      = '';
            $adding_to_cart     = wc_get_product( $product_id );
            if ( ! $adding_to_cart ) {
                return false;
            }   
            $option_id = $this->get_product_option($product_id);
            if( !$option_id ) return false;
            $variation_id = isset($_REQUEST['variation_id']) ? $_REQUEST['variation_id'] : 0;
            $product_type = $adding_to_cart->get_type();
            $uploaded = false;
            $upload_fields = array();
            /* Gather online design data */
            $nbd_item_cart_key = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id;
            $nbd_session = WC()->session->get('nbd_item_key_'.$nbd_item_cart_key);
            $nbu_session = WC()->session->get('nbu_item_key_'.$nbd_item_cart_key);
            $added_cart_item_keys = array();
            if( $product_type == 'variable' ){
                if( $variation_id > 0 ){
                    $missing_attributes = array();
                    $variations = array();
                    try {
                        // Gather posted attributes.
                        $posted_attributes = array();
                        foreach ($adding_to_cart->get_attributes() as $attribute) {
                            if (!$attribute['is_variation']) {
                                continue;
                            }
                            $attribute_key = 'attribute_' . sanitize_title($attribute['name']);
                            if (isset($_REQUEST[$attribute_key])) {
                                if ($attribute['is_taxonomy']) {
                                    // Don't use wc_clean as it destroys sanitized characters.
                                    $value = sanitize_title(wp_unslash($_REQUEST[$attribute_key]));
                                } else {
                                    $value = html_entity_decode(wc_clean(wp_unslash($_REQUEST[$attribute_key])), ENT_QUOTES, get_bloginfo('charset'));
                                }
                                $posted_attributes[$attribute_key] = $value;
                            }
                        }

                        // Check the data we have is valid.
                        $variation_data = wc_get_product_variation_attributes( $variation_id );
                        foreach ( $adding_to_cart->get_attributes() as $attribute ) {
                            if ( ! $attribute['is_variation'] ) {
                                continue;
                            }
                            // Get valid value from variation data.
                            $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                            $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';
                            /**
                             * If the attribute value was posted, check if it's valid.
                             *
                             * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
                             */
                            if ( isset( $posted_attributes[ $attribute_key ] ) ) {
                                $value = $posted_attributes[ $attribute_key ];
                                // Allow if valid or show error.
                                if ( $valid_value === $value ) {
                                    $variations[ $attribute_key ] = $value;
                                } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
                                    // If valid values are empty, this is an 'any' variation so get all possible values.
                                    $variations[ $attribute_key ] = $value;
                                } else {
                                    throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
                                }
                            } elseif ( '' === $valid_value ) {
                                $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                            }
                        }
                        if ( ! empty( $missing_attributes ) ) {
                            throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
                        }
                    } catch ( Exception $e ) {
                        wc_add_notice( $e->getMessage(), 'error' );
                        return false;
                    }
                    foreach($nbb_fields as $index => $nbb_field){
                        /* Add online design data */
                        if( $nbd_session && ! WC()->session->get('nbd_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbd_item_key_'.$nbd_item_cart_key, $nbd_session);
                        if( $nbu_session && ! WC()->session->get('nbu_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbu_item_key_'.$nbd_item_cart_key, $nbu_session);
                        $quantity = $qtys[$index];
                        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
                        if( $quantity > 0){
                            if ( $passed_validation ) {
                                if( !$uploaded ){
                                    if( !empty($_FILES) && isset($_FILES["nbd-field"]) ) {
                                        foreach( $_FILES["nbd-field"]['name'] as $field_id => $file ){
                                            if( !isset($nbd_field[$field_id]) ){
                                                $nbd_upload_field = $this->upload_file( $_FILES["nbd-field"], $field_id );
                                                if( !empty($nbd_upload_field) ){
                                                    $upload_fields[$field_id] = $nbd_upload_field[$field_id];
                                                }
                                            }
                                        }
                                    }
                                    $uploaded = true;
                                }
                                $nbb_field = array_merge($nbb_field, $upload_fields);
                                $cart_item_data['nbd-field'] = $nbb_field;
                                $added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );
                                if ( $added ) {
                                    $added_count ++;
                                    $added_cart_item_keys[] = $added;
                                } else {
                                    $failed_count ++;
                                }
                            }else{
                                $failed_count++;
                            }
                        }else{
                            //$failed_count++;
                            continue;
                        }
                    }
                } else {
                    return false;
                }
            }else{
                foreach($nbb_fields as $index => $nbb_field){
                    /* Add online design data */
                    if( $nbd_session && ! WC()->session->get('nbd_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbd_item_key_'.$nbd_item_cart_key, $nbd_session);
                    if( $nbu_session && ! WC()->session->get('nbu_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbu_item_key_'.$nbd_item_cart_key, $nbu_session);
                    $quantity = $qtys[$index];
                    $passed_validation  = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
                    if( $quantity > 0){
                        if ( $passed_validation ) {
                            if( !$uploaded ){
                                if( !empty($_FILES) && isset($_FILES["nbd-field"]) ) {
                                    foreach( $_FILES["nbd-field"]['name'] as $field_id => $file ){
                                        if( !isset($nbd_field[$field_id]) ){
                                            $nbd_upload_field = $this->upload_file( $_FILES["nbd-field"], $field_id );
                                            if( !empty($nbd_upload_field) ){
                                                $upload_fields[$field_id] = $nbd_upload_field[$field_id];
                                            }
                                        }
                                    }
                                }
                                $uploaded = true;
                            }
                            $nbb_field = array_merge($nbb_field, $upload_fields);
                            $cart_item_data['nbd-field'] = $nbb_field;
                            $added = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );
                            if ( $added ) {
                                $added_count ++;
                                $added_cart_item_keys[] = $added;
                            } else {
                                $failed_count ++;
                            }
                        }else{
                            $failed_count++;
                        }
                    }else{
                        //$failed_count++;
                        continue;
                    }
                }
            }
            WC()->session->__unset('nbd_item_key_'.$nbd_item_cart_key);
            WC()->session->__unset('nbu_item_key_'.$nbd_item_cart_key);
            if ( $added_count ) {
                nbd_bulk_variations_add_to_cart_message( $added_count );
                $cart_item_keys = implode( '|', $added_cart_item_keys );
                WC()->session->set( 'nbd_cart_item_keys', $cart_item_keys );
            }
            if ( $failed_count ) {
                wc_add_notice( sprintf( __( 'Unable to add %s to the cart.  Please check your quantities and make sure the item is available and in stock', 'web-to-print-online-designer' ), $failed_count ), 'error' );
            }  
            if ( ! $added_count && ! $failed_count ) {
                wc_add_notice( __( 'No product quantities entered.', 'web-to-print-online-designer' ), 'error' );
            }
            if ( $failed_count === 0 && wc_notice_count( 'error' ) === 0 ) {
                if( !$prevent_redirect ){
                    if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false ) ) {
                        wp_safe_redirect( $url );
                        exit;
                    } elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
                        wp_safe_redirect( wc_get_cart_url() );
                        exit;
                    }
                }
            } 
            if( $return ){
                return $added_count;
            }
        }
        public function nbo_ajax_cart( $ajax = true ){
            $posted         = $_POST;
            $results        = array();
            $added_count    = 0;
            $added          = "";
            if ( isset( $posted['nbb-fields'] ) ){
                $added_count = $this->bulk_order(true, true, false);
            }else{
                $variation_id       = (isset($posted['variation_id']) && $posted['variation_id'] !== 'undefined') ? $posted['variation_id'] : 0;
                $product_id         = isset($posted['product_id']) ? $posted['product_id'] : ( isset($posted['nbd-add-to-cart']) ? $posted['nbd-add-to-cart'] : ( isset($posted['nbo-add-to-cart']) ? $posted['nbo-add-to-cart'] : 0 ) );
                $quantity           = isset($posted['quantity']) ? $posted['quantity'] : 1;
                $adding_to_cart     = wc_get_product( $product_id );
                $product_type       = $adding_to_cart->get_type();
                if( $product_type == 'variable' ){
                    if( $variation_id > 0 ){
                        $missing_attributes = array();
                        $variations = array();
                        try {
                            $posted_attributes = array();
                            foreach ($adding_to_cart->get_attributes() as $attribute) {
                                if (!$attribute['is_variation']) {
                                    continue;
                                }
                                $attribute_key = 'attribute_' . sanitize_title($attribute['name']);
                                if (isset($_REQUEST[$attribute_key])) {
                                    if ($attribute['is_taxonomy']) {
                                        $value = sanitize_title(wp_unslash($_REQUEST[$attribute_key]));
                                    } else {
                                        $value = html_entity_decode(wc_clean(wp_unslash($_REQUEST[$attribute_key])), ENT_QUOTES, get_bloginfo('charset'));
                                    }
                                    $posted_attributes[$attribute_key] = $value;
                                }
                            }
                            $variation_data = wc_get_product_variation_attributes( $variation_id );
                            foreach ( $adding_to_cart->get_attributes() as $attribute ) {
                                if ( ! $attribute['is_variation'] ) {
                                    continue;
                                }
                                $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                                $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';                       
                                if ( isset( $posted_attributes[ $attribute_key ] ) ) {
                                    $value = $posted_attributes[ $attribute_key ];
                                    if ( $valid_value === $value ) {
                                        $variations[ $attribute_key ] = $value;
                                    } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
                                        $variations[ $attribute_key ] = $value;
                                    } else {
                                        throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
                                    }
                                } elseif ( '' === $valid_value ) {
                                    $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                                }
                            }
                            if ( ! empty( $missing_attributes ) ) {
                                throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
                            }
                        } catch ( Exception $e ) {
                            wc_add_notice( $e->getMessage(), 'error' );
                        }
                        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
                        if ($quantity > 0) {
                            if ($passed_validation) {
                                $added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations, array());
                                if ( $added ) $added_count ++;
                            }
                        }
                    }
                }else{
                    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
                    if ($passed_validation) {
                        $added = WC()->cart->add_to_cart( $product_id, $quantity, 0, array() );
                        if ( $added ) $added_count ++;
                    }
                }
            }
            if( $added_count > 0 ){
                $results = array(
                    'result'   => 'success',
                    'messages' => sprintf( __( '%s products successfully added to your cart.', 'web-to-print-online-designer' ), $added_count ),
                );
            }else{
                $results = array(
                    'result'   => 'failure',
                    'messages' => __('No product has been added.', 'web-to-print-online-designer'),
                );
            }
            if( $ajax !== false ){
                ob_start();
                woocommerce_mini_cart();
                $mini_cart = ob_get_clean();
                $results['data'] = array(
                    'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
                    )),
                    'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session()),
                );
                if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false ) ) {
                    $results['redirect'] = $url;
                } elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
                    $results['redirect'] = $url;
                }
                if ($added != "") {
                    //Check if service be added with product 
                    if (array_key_exists('releated_pid', $_GET)) {
                        $product_services_key = 'services_of_product_' . $_GET['releated_pid'];
                        $services = WC()->session->get($product_services_key);
                        if (!is_array($services)) {
                            $services = [];
                        }
                        $services[] = $added;
                        WC()->session->set($product_services_key, $services);
                    }
                    $results['data']['added_item'] = $added;
                }
                wp_send_json( $results );
                exit();
            }else{
                return $added_count;
            }
        }
        public function quick_view(){
            global $woocommerce, $post;
            $product_id = absint( $_GET['product'] );
            $mode       = isset( $_GET['mode'] ) ? $_GET['mode'] : 'editor';
            if ( $product_id ) {
                $post = get_post($product_id);
                $type = nbdesigner_get_option('nbdesigner_display_product_option');
                setup_postdata($post);
                if($type == '1' || $mode == 'catalog' ){
                    nbdesigner_get_template('quick-view.php', array());
                }else{
                    nbdesigner_get_template('quick-view-tab.php', array());
                }
                exit;
            }
            exit;
        }
        public function nbo_get_product_variations(){
            if ( !isset( $_POST['product_id'] ) || empty( $_POST['product_id'] ) ) {
                wp_send_json_error();
                die();
            }
    
            $product    = wc_get_product( $_POST['product_id'] );
            $variations = $this->get_available_variations( $product );
    
            wp_send_json_success( $variations );
            die();
        }
        private function get_available_variations( $product ) {
            global $wpdb;
            
            $transient_name = 'nbo_available_variations_' . $product->get_id();
            $transient_data = get_transient($transient_name);
            if ( !empty( $transient_data ) ){
                return $transient_data;
            }
            
            $available_variations = array();
    
            //Get the children all in one call.
            //This will prime the WP_Post cache so calls to get_child are much faster. 
    
            $args = array(
                'post_parent'       => $product->get_id(),
                'post_type'         => 'product_variation',
                'orderby'           => 'menu_order',
                'order'             => 'ASC',
                'post_status'       => 'publish',
                'numberposts'       => -1,
                'no_found_rows'     => true
            );
            $children = get_posts( $args );
    
            foreach ( $children as $child ) {
                $variation = wc_get_product( $child );
    
                // Hide out of stock variations if 'Hide out of stock items from the catalog' is checked
                $id = $variation->get_id();
                if ( empty( $id ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && !$variation->is_in_stock() ) ) {
                    continue;
                }
    
                // Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price)
                if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $product->get_id(), $variation ) && !$variation->variation_is_visible() ) {
                    continue;
                }
    
                $available_variations[] = array(
                    'variation_id'          => $variation->get_id(),
                    'variation_is_active'   => $variation->variation_is_active(),
                    'attributes'            => $variation->get_variation_attributes(),
                );
            }
            set_transient( $transient_name, $available_variations, DAY_IN_SECONDS * 30 );
            return $available_variations;
        }
        public function on_delete_product_transients( $product_id ){
            delete_transient( 'nbo_available_variations_' . $product_id );
        }
        public function nbo_dropdown_variation_attribute_options_args( $args ){
            if( $args['attribute'] && $args['product'] instanceof WC_Product ) {
                $product            = $args['product'];
                $attribute          = $args['attribute'];
                $nbo_enable_mapping = $product->get_meta('_enable_nbo_mapping', true);
                $nbo_maps           = $product->get_meta('_nbo_maps', true);

                if( !isset( $args['class'] ) ) $args['class'] = '';

                if( $nbo_enable_mapping && !empty( $nbo_maps ) ){
                    $st_name        = sanitize_title( $attribute );
                    $hashed_name    = md5( $st_name );

                    if( isset( $nbo_maps[ $hashed_name ] ) && $nbo_maps[ $hashed_name ] != '' ){
                        $args['class'] .= ' nbo-mapping-select nbo_field_id-' . $nbo_maps[ $hashed_name ];
                    }else{
                        $args['class'] .= ' nbo-default-select';
                    }
                }else{
                    $args['class'] .= ' nbo-default-select';
                }
            }
            return $args;
        }
        public function nbo_get_content_product() {
            $product_id = (isset($_POST['product_id']))?esc_attr($_POST['product_id']) : '';
            $cur_id = (isset($_POST['cur_id']))?esc_attr($_POST['cur_id']) : '';
            $_pf = new WC_Product_Factory();  
            $product = $_pf->get_product($cur_id);
            $link = $product->get_permalink();
            $args = array(
                'post_type'           => 'product',
                'post_status'         => 'publish',
                'p'                   => $product_id,
            );
            $results_html = array();
            $single_product = new WP_Query($args); 
            if($single_product->have_posts()) {
                ob_start();
                while ( $single_product->have_posts() ) {
                    $single_product->the_post();
                    WPBMap::addAllMappedShortcodes();
                    wc_get_template_part( 'content', 'single-product' ) ;
                }
                $results_html['template_single_product'] = ob_get_clean();
            }
            ob_start();
            echo $this->nbd_get_option_product_grouped($cur_id);
            $results_html['selected'] = ob_get_clean();
            $results_html['title'] = get_the_title($cur_id);
            $results_html['product_id'] = $product_id;
            $results_html['link'] = $link;
            $script_nbdesigner = '<script type="text/javascript" src="/assets/js/nbdesigner.js"></script>';
            ob_start();
            ?>
            <script type="text/javascript" src="<?php echo esc_attr(NBDESIGNER_JS_URL.'/nbdesigner.js') ?>"></script>
            <?php
            $results_html['script_nbdesigner'] = ob_get_clean();
             ob_start();
            ?>
            <script type="text/javascript" src="<?php echo esc_attr(get_template_directory_uri().'/assets/netbase/js/main.js') ?>"></script>
            <?php
            $results_html['swiper'] = ob_get_clean();
            wp_send_json_success( $results_html );
            die();
        }
        public function nbd_get_option_product_grouped($cur_id) {
            $_pf = new WC_Product_Factory();  
            $product = $_pf->get_product($cur_id);
            $items = nbd_get_items_product_grouped($cur_id);
            if($items) {
                ?>
                <div class="nbd_merged_product">
                    <input type="hidden" value="<?php echo esc_attr($product_id); ?>">
                    <table class="nbd-tb-options">
                        <td>
                            <label style="font-weight: 700; padding: 10px 0!important;">Product</label>
                        </td>
                        <td>
                            <select class="merged_product_ajax nbo-ad-result" name="nbd_merged_product" onchange="ajax_change_product()" style="display: block; width: 100%;">
                                <?php
                                    foreach ( $items as $item ) {
                                        $title = get_the_title( $item['id'] );
                                        ?>
                                        <option value="<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_attr( $title ); ?></option>                           
                                        <?php
                                    }
                                ?>
                            </select>
                        </td>
                    </table>
                </div>
                <?php
            }
        }
        public function nbd_get_thumbnail_product($product) {
            $post_thumbnail_id = $product->get_image_id();
            ?>
            <figure class="woocommerce-product-gallery__wrapper">
                <?php
                if ( $product->get_image_id() ) {
                    $html = wc_get_gallery_image_html( $post_thumbnail_id, true );
                } else {
                    $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
                    $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
                    $html .= '</div>';
                }

                echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

                do_action( 'woocommerce_product_thumbnails' );
                ?>
            </figure>
            <?php
        }
        public function nbd_load_ajax() {
            ?>
            <style type="text/css">
                .nbd-loader-ajax {
                    display: none;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    width: 100%;
                    height: 100%;
                    z-index: 9999;
                }
                .nbd-loader-ajax i {
                    font-size: 40px;
                    animation: spin 1s linear 0s infinite;
                    -webkit-animation: spin 1s linear 0s infinite;
                    -moz-animation: spin 1s linear 0s infinite;
                    -o-animation: spin 1s linear 0s infinite;

                }
                @-webkit-keyframes spin{
                    from{
                            -webkit-transform:rotate(0deg);
                            -moz-transform:rotate(0deg);
                            -o-transform:rotate(0deg);
                        }
                    to{
                            -webkit-transform:rotate(360deg);
                            -moz-transform:rotate(360deg);
                            -o-transform:rotate(360deg);
                    }
                }
                /* Standard syntax */ 
                @keyframes spin {
                    from{
                            -webkit-transform:rotate(0deg);
                            -moz-transform:rotate(0deg);
                            -o-transform:rotate(0deg);
                        }
                    to{
                            -webkit-transform:rotate(360deg);
                            -moz-transform:rotate(360deg);
                            -o-transform:rotate(360deg);
                    }
                }
            </style>
            <div class="nbd-loader-ajax"><i class="fa fa-spinner" aria-hidden="true"></i></div>
            <?php
        }
    }
}
$nbd_fontend_printing_options = NBD_FRONTEND_PRINTING_OPTIONS::instance();
$nbd_fontend_printing_options->init();