<?php
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class NB_Payment_Gateway {
    public $paypalConfig;
    public $apiContext;
    public $enableSandbox;
    
    function __construct() {
        $this->action_hook();
        $this->init();
    }
    
    function init() {
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/payment/payment-setting.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/payment.php' );
        
        // For test payments we want to enable the sandbox mode. If you want to put live
        // payments through then this setting needs changing to `false`.
        $this->enableSandbox = get_option('nbdesigner_paypal_enable_sanbox');

        // PayPal settings. Change these to your account details and the relevant URLs
        // for your site.
        $paypal_client_id = get_option('nbdesigner_paypal_cliend_id');
        $paypal_client_secret = get_option('nbdesigner_paypal_secret_key');
        $this->paypalConfig = [
            'client_id'     => $paypal_client_id,
            'client_secret' => $paypal_client_secret,
        ];
        $this->apiContext = $this->getApiContext($this->paypalConfig['client_id'], $this->paypalConfig['client_secret'], $this->enableSandbox);
    }
    
    function action_hook() {
        add_action('wp_ajax_nbd_payment_stripe', array($this, 'nbd_payment_stripe'));
        add_action('wp_ajax_nopriv_nbd_payment_stripe', array($this, 'nbd_payment_stripe'));
        add_action('wp_ajax_nbd_paypal_request', array($this, 'paypalRequest'));
        add_action('wp_ajax_nopriv_nbd_paypal_request', array($this, 'paypalRequest'));
        add_action('wp_ajax_nbd_payment_paypal', array($this, 'payment_paypal_ajax'));
        add_action('wp_ajax_nopriv_nbd_payment_paypal', array($this, 'payment_paypal_ajax'));
        add_action( 'woocommerce_admin_order_items_after_line_items', array($this, 'order_items'), 10, 1);
        add_filter('nbdesigner_settings_tabs', array('NBD_Payment_Setting', 'add_setting_payment'), 20, 1);
        add_filter('nbdesigner_settings_blocks', array('NBD_Payment_Setting', 'add_settings_blocks'), 20, 1);
        add_filter('nbdesigner_settings_options', array('NBD_Payment_Setting', 'add_settings_options'), 20, 1);
        add_shortcode( 'nb_payment_success', array( $this, 'payment_success_page' ) );
        add_shortcode( 'nb_payment_cancel', array( $this, 'payment_cancel_page' ) );
    }
    
    function nbd_payment_stripe() {
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => 'Login is required!'
            ), 200);
        }
        
        $strip_secret_key = get_option('nbdesigner_stripe_secret_key', true);
        \Stripe\Stripe::setApiKey($strip_secret_key);
        $token = $_POST['stripeToken'];
        $amount = (int) $_POST['amount']; //Currency is cent => $1 = 100cent 
        $img_ids = $_POST['img_ids'];
        
        $imgs_paid = get_user_meta($user_id, 'nbd_user_image_gallery_paid', true);
        if (!$imgs_paid || !is_array($imgs_paid)) {
            $imgs_paid = [];
        }
        
        try {
            $charge = \Stripe\Charge::create(
                array(
                    'amount' => $amount,
                    'currency' => 'SGD',
                    'source' => $token
                )
            );
            try {
                update_user_meta($user_id, 'nbd_user_image_gallery_paid', array_merge($imgs_paid, $img_ids));
                
                // Create and completed order
                $order = wc_create_order();
                // Set payment gateway
                $order->set_payment_method( 'stripe' );
                $order->set_customer_id( $user_id );
                $order->set_payment_method_title( 'Payment image via Stripe' );
                // Calculate totals
                $order->set_total($amount / 100); //Convert cent to SGD
                $order->set_status('completed');
                $order->save();
                update_post_meta($order->get_id(), 'nbd_order_gallery', true);
                update_post_meta($order->get_id(), 'nbd_order_gallery_items', $img_ids);
            } catch (Exception $e) {
                wp_send_json(array(
                    'status'    => 'success',
                    'message'   => 'Error when update user'
                ), 200);
            }
            $array_origin_url = [];
            foreach ($img_ids as $img_id) {
                $array_origin_url[] = [
                    'img_id'    => (int) $img_id,
                    'img_url'   => get_post_meta((int) $img_id, 'nbd_image_original', true) ? get_post_meta($img_id, 'nbd_image_original', true) : ''
                ];
            }
            wp_send_json(array(
                'status'    => 'success',
                'message'   => 'Payment success!',
                'data'      => $array_origin_url
            ), 200);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => $e->getError()->message
            ), 200);
        }
        die();
    }
    
    /**
     * Set up a connection to the API
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param bool   $enableSandbox Sandbox mode toggle, true for test payments
     * @return \PayPal\Rest\ApiContext
    */
    function getApiContext($clientId, $clientSecret, $enableSandbox = true) {
        $apiContext = new ApiContext(
            new OAuthTokenCredential($clientId, $clientSecret)
        );

        $apiContext->setConfig([
            'mode' => $enableSandbox ? 'sandbox' : 'live'
        ]);

        return $apiContext;
    }
    
    /**
     * Handle paypal request
     */
    function paypalRequest() {
        if ($_POST['amount'] != null && $_POST['img_ids'] != null) {
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");

            $array_item = [];
            $total = 0;
            foreach ($_POST['img_ids'] as $img_id) {
                $image = get_post($img_id);
                $image_title = $image->post_title;
                $image_price = get_post_meta($img_id, 'nbd_image_price', true) ? get_post_meta($img_id, 'nbd_image_price', true) : 0;
                $item = new Item();
                $item->setName($image_title)
                    ->setCurrency('SGD')
                    ->setQuantity(1)
                    ->setPrice((float) $image_price);
                $array_item[] = $item;
                $total += (float) $image_price;
            }

            $itemList = new ItemList();
            $itemList->setItems($array_item);

            $amount = new Amount();
            $amount->setCurrency("SGD")
                ->setTotal((float) $total);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setInvoiceNumber(uniqid());

            $baseUrl = home_url();
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl("$baseUrl/payment-success/?success=true")
                ->setCancelUrl("$baseUrl/payment-cancel/?success=false");

            $payment = new Payment();
            $payment->setIntent("sale")
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions(array($transaction));
            
            try {
                $payment->create($this->apiContext);
                wp_send_json(array(
                    'status'        => 'success',
                    'message'       => 'success',
                    'approval_link' => $payment->getApprovalLink()
                ), 200);
            } catch (Exception $e) {
                wp_send_json(wp_send_json(array(
                    'status'        => 'error',
                    'message'       => $e->getMessage(),
                )), 200);
            }
        } else {
            wp_send_json(wp_send_json(array(
                'status'        => 'error',
                'message'       => 'Save design failed!!!',
            )), 200);
        }
        
        die();
    }
    
    /**
     * Handle paypal response
     */
    function payment_paypal_ajax() {
        $user_id = get_current_user_id();
        if (!$user_id) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => 'Login is required!'
            ), 200);
        }
        if (empty($_POST['paymentId']) || empty($_POST['payerId'])) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => 'The request is missing the paymentId and PayerID!'
            ), 200);
        }
        if (empty($_POST['amount']) || empty($_POST['img_ids'])) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => 'The request is missing list images or amount!'
            ), 200);
        }

        $paymentId = $_POST['paymentId'];
        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($_POST['payerId']);
        
        try {
            // Take the payment
            $payment->execute($execution, $this->apiContext);

            // Create and completed order
            $order = wc_create_order();
            // Set payment gateway
            $order->set_payment_method( 'paypal' );
            $order->set_payment_method_title( 'Payment image via Paypal' );
            // Calculate totals
            $order->set_total($_POST['amount']);
            $order->set_status('completed');
            $order->save();
            update_post_meta($order->get_id(), 'nbd_order_gallery', true);
            update_post_meta($order->get_id(), 'nbd_order_gallery_items', $_POST['img_ids']);

            $imgs_paid = get_user_meta($user_id, 'nbd_user_image_gallery_paid', true);
            if (!$imgs_paid || !is_array($imgs_paid)) {
                $imgs_paid = [];
            }
            update_user_meta($user_id, 'nbd_user_image_gallery_paid', array_merge($imgs_paid, $_POST['img_ids']));

            foreach ($_POST['img_ids'] as $img_id) {
                $array_origin_url[] = [
                    'img_id'    => (int) $img_id,
                    'img_url'   => get_post_meta((int) $img_id, 'nbd_image_original', true) ? get_post_meta($img_id, 'nbd_image_original', true) : ''
                ];
            }
            
            wp_send_json(array(
                'status'    => 'success',
                'message'   => 'Payment success',
                'data'      => $array_origin_url
            ), 200);
        } catch (Exception $e) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => $e->getMessage()
            ), 200);
        }
        
        die();
    }
    
    /**
     * Handle paypal response
     */
    function paypalResponseOld() { //Handle paypal old version of cs botak
        if (empty($_GET['paymentId']) || empty($_GET['PayerID'])) {
            echo 'The response is missing the paymentId and PayerID';
            exit;
        }

        $paymentId = $_GET['paymentId'];
        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($_GET['PayerID']);

        try {
            // Take the payment
            if (WC()->session->get('nbd_order_gallery_data') != null) {
                $payment->execute($execution, $this->apiContext);
                
                $data = unserialize(WC()->session->get('nbd_order_gallery_data'));
                
                // Create and completed order
                $order = wc_create_order();
                // Set payment gateway
                $order->set_payment_method( 'paypal' );
                $order->set_payment_method_title( 'Payment image via Paypal' );
                // Calculate totals
                $order->set_total($data['amount']);
                $order->set_status('completed');
                $order->save();
                update_post_meta($order->get_id(), 'nbd_order_gallery', true);
                update_post_meta($order->get_id(), 'nbd_order_gallery_items', $data['img_ids']);
                
                $imgs_paid = get_user_meta($data['user_id'], 'nbd_user_image_gallery_paid', true);
                if (!$imgs_paid || !is_array($imgs_paid)) {
                    $imgs_paid = [];
                }
                update_user_meta($data['user_id'], 'nbd_user_image_gallery_paid', array_merge($imgs_paid, $data['img_ids']));
                $url_download = base64_decode($data['rdrl']);
                
//                WC()->session->__unset('nbd_order_gallery_data');
                ?>
                    <h3 style='text-align: center;'>Payment success! File will download automatic in few second!</h3>
                    <p style='text-align: center;'>If file don't download automatically, download manually <a href="<?php echo $url_download;?>">here.</p>
                    <script>
                        document.addEventListener("DOMContentLoaded", function(event) {
                            window.location = "<?php echo $url_download; ?>";
                        })
                    </script>
                <?php
            } else {
                echo "Payment failed! Order don't exist!";
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Handle paypal response
     */
    function paypalResponse() {
        ?>
            <h3 style='text-align: center;'>Payment success! File will download automatic in few second!</h3>
        <?php
    }
    
    function payment_success_page() {
        //Handle payment paypal
        $this->paypalResponse();
    }
    
    function payment_cancel_page() {
        echo sprintf("<h3 style='text-align: center;'>Payment canceled! Go to <a href='%s'>Home page</a><h3>", home_url());
    }
    
    function order_items($order_id) {
        if (get_post_meta($order_id, 'nbd_order_gallery', true)) {
            $array_image = get_post_meta($order_id, 'nbd_order_gallery_items', true);
            foreach ($array_image as $img_id) {
                $image = get_post($img_id);
                $image_url = get_post_meta($img_id, 'nbd_image_original', true);
                $image_price = get_post_meta($img_id, 'nbd_image_price', true);
            ?>
                <tr class="item">
                    <td class="thumb">
                        <div class="wc-order-item-thumbnail">
                            <img width="150" height="150" src="<?php echo $image_url;?>" class="attachment-thumbnail size-thumbnail" alt="" title="">
                        </div>
                    </td>
                    <td class="name">
                        <a href="#" class="wc-order-item-name">
                            <?php echo $image->post_title; ?>
                        </a>
                    </td>
                    <td class="item_cost" width="1%"></td>
                    <td class="quantity" width="1%"></td>
                    <td class="line_cost" width="1%">
                        <div class="view">
                            <?php echo wc_price($image_price); ?>
                        </div>
                    </td>
                    <td class="wc-order-edit-line-item" width="1%">
                        <div class="wc-order-edit-line-item-actions">
                        </div>
                    </td>
                </tr>
            <?php }
        }
    }
}

new NB_Payment_Gateway();
