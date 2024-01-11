<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('Botak_Custom_Feature')) {

    class Botak_Custom_Feature
    {
        protected static $instance;
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
            //todo
        }

        public function init() {
            $this->hooks();
        }

        public function hooks() {
            add_action('nbo_options_meta_box_tabs', array( $this, 'options_custom_feature_tab'), 10, 1);
            add_action( 'nbo_options_before_meta_box_panels', array( $this, 'options_custom_feature_panel' ), 10, 1 );
            add_action( 'nbo_save_options', array( $this, 'nbo_save_options' ), 10, 1 );
            add_action( 'woocommerce_checkout_order_processed', array($this, 'checkout_order_processed') );
        }

        public function options_custom_feature_tab() {
            ?>
            <li><a href="#nbcf-options"><span class="dashicons dashicons-admin-tools"></span> <?php _e('Custom Feature', 'web-to-print-online-designer'); ?></a></li>
            <?php
        }

        public function options_custom_feature_panel($post_id) {
            $email_alert = get_post_meta($post_id, '_nbcf_email_alert', true);
            ?>
            <div class="nbo_options_panel" id="nbcf-options" style="display: none;">
                <p class="nbo-form-field">
                    <label for="_nbo_enable"><?php _e('Email Alert', 'web-to-print-online-designer'); ?></label>
                    <span class="nbo-option-val">
                        <input type="text" value="<?php echo $email_alert; ?>" name="_nbcf_email_alert" />
                    </span>
                </p>
            </div>
            <?php
        }

        public function nbo_save_options($post_id) {
            if( isset($_POST['_nbcf_email_alert']) ){
                $email_alert = $_POST['_nbcf_email_alert']; 
                update_post_meta($post_id, '_nbcf_email_alert', $email_alert);
            }
        }

        public function checkout_order_processed($order_id) {
            if(!$order_id) return;

            $order = wc_get_order($order_id);

            if(!$order) return;

            $items = $order->get_items();

            $email_alert_list = array();

            foreach( $items as $order_item_id => $item ){
                $product_id = $item->get_product_id();
                $email_alert = get_post_meta($product_id, '_nbcf_email_alert', true);

                $_emails = explode(',', $email_alert);

                if(!empty($_emails)) {

                    foreach ($_emails as $value) {
                        if($value && !in_array($value, $email_alert_list)) {
                            $email_alert_list[] = $value;
                        }
                    }
                }
            }

            if(!empty($email_alert_list)) {
                $subject = 'New Order #' . $order_id;

                $message = 'Order #' . $order_id . ' has been created';

                wp_mail($email_alert_list, $subject, $message);
            }
        }
    }
}
$botak_custom_feature = Botak_Custom_Feature::instance();
$botak_custom_feature->init();