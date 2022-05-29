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

class Payment_Paypal {
    public $paypalConfig;
    public $apiContext;
    public $enableSandbox;
    
    function __construct() {
        $this->action_hook();
        $this->init();
    }
    
    function init() {
        // For test payments we want to enable the sandbox mode. If you want to put live
        // payments through then this setting needs changing to `false`.
        $this->enableSandbox = true;

        // PayPal settings. Change these to your account details and the relevant URLs
        // for your site.
        $paypal_client_id = get_option('nbdesigner_paypal_cliend_id', true);
        $paypal_client_secret = get_option('nbdesigner_paypal_secret_key', true);
        $this->paypalConfig = [
            'client_id'     => $paypal_client_id,
            'client_secret' => $paypal_client_secret,
        ];
        $this->apiContext = $this->getApiContext($this->paypalConfig['client_id'], $this->paypalConfig['client_secret'], $this->enableSandbox);
    }
    
    function action_hook() {
        add_action('wp_ajax_nb_push_rush_paypal_request', array($this, 'paypalRequest'));
        add_action('wp_ajax_nopriv_nb_push_rush_paypal_request', array($this, 'paypalRequest'));
        add_action('wp_ajax_nbd_payment_paypal', array($this, 'payment_paypal_ajax'));
        add_action('wp_ajax_nopriv_nbd_payment_paypal', array($this, 'payment_paypal_ajax'));
        add_shortcode( 'nb_push_rush_success', array( $this, 'payment_success_page' ) );
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
        if ($_POST['items_data'] != null && is_array($_POST['items_data']) && count($_POST['items_data']) > 0) {
            WC()->session->set('nbd_update_rush_order_' . $_POST['order_id'], serialize($_POST['items_data']));
            $payer = new Payer();
            $payer->setPaymentMethod("paypal");

            $array_item = [];
            $total = 0;
            foreach ($_POST['items_data'] as $item_data) {
                if ($item_data['addition_price'] > 0) {
                    $item = new Item();
                    $item->setName($item_data['name'])
                        ->setCurrency('SGD')
                        ->setQuantity((int) $item_data['quantity'])
                        ->setPrice((float) $item_data['addition_price'] + (float) $item_data['addition_tax']);
                    $array_item[] = $item;
                    $total += ((float) $item_data['addition_price'] + (float) $item_data['addition_tax']) * (int) $item_data['quantity'];
                }
            }

            if ($total == 0) {
               wp_send_json(array(
                    'status'        => 'error',
                    'message'       => 'Please select a valid option!',
                ), 200);
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

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(home_url() . "/push-rush/?success=true&order_id=" . $_POST['order_id'])
                        ->setCancelUrl(get_permalink(get_option('woocommerce_myaccount_page_id')) . 'view-order/' . $_POST['order_id'] . '?success=false');
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
                'message'       => 'Invalid data',
            )), 200);
        }
        
        die();
    }

    /**
     * Handle paypal response
     */
    function paypalResponse() {
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
            if (WC()->session->get('nbd_update_rush_order_' . $_GET['order_id']) != null) {
                $payment->execute($execution, $this->apiContext);
                
                $item_datas = unserialize(WC()->session->get('nbd_update_rush_order_' . $_GET['order_id']));

                $order = wc_get_order($_GET['order_id']);
                $list_item = $order->get_items('line_item');
                foreach ($item_datas as $item_id => $data) {
                    foreach ($list_item as $key => &$item) {
                        if ($key === $item_id) {
                            if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
                                $options = $item->get_meta('_nbo_options');
                                $origin_fields = unserialize($options['fields']);
                                $origin_fields = $origin_fields['fields'];
                                $item_fields = $item->get_meta('_nbo_field');
                                foreach ($item_fields as $fkey => &$fvalue) {
                                    foreach ($origin_fields as $field) {
                                        if ($field['id'] === $fkey && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                                            $option_name = $field['general']['attributes']['options'][$data['value']]['name'];
                                            wc_update_order_item_meta($item_id, 'Production Time', $option_name);
                                        }
                                    }
                                    if ($fkey === $data['field_id']) {
                                        $fvalue['value'] = $data['value'];
                                        $item_quantity = $item->get_quantity();
                                        $new_item_price = $item->get_subtotal() + $data['addition_price'];
                                        $new_subtotal_tax = $item->get_subtotal_tax() + $data['addition_tax'];

                                        //Recalc price and tax
                                        $item->set_subtotal($new_item_price);
                                        $item->set_total($new_item_price * $item_quantity);
                                        $item->set_subtotal_tax($new_subtotal_tax);
                                        $item->set_total_tax($new_subtotal_tax * $item_quantity);
                                        $item->set_taxes(
                                            array(
                                                'total' => [$new_subtotal_tax * $item_quantity],
                                                'subtotal' => [$new_subtotal_tax],
                                            )
                                        );
                                        $item->save();
                                    }
                                }
                                wc_update_order_item_meta($item_id, '_nbo_field', $item_fields);
                            };
                        }
                    }
                }

                $order->calculate_totals();
                $order->save();

                //Get new info of order
                $norder = wc_get_order($_GET['order_id']);
                $nlist_item = $norder->get_items('line_item');
                $order_type_standard = 0;
                $order_type_rush = 1;
                $order_type_super_rush = 2;

                //CS botak update type for order
                $order_type = $order_type_standard;
                foreach ($nlist_item as $nitem_id => $nitem) {
                    if ($nitem->get_meta('_nbo_options') && $nitem->get_meta('_nbo_field')) {
                        $noptions = $nitem->get_meta('_nbo_options');
                        $norigin_fields = unserialize($noptions['fields']);
                        $norigin_fields = $norigin_fields['fields'];
                        $nitem_field = $nitem->get_meta('_nbo_field');

                        foreach ($nitem_field as $nkey => $nvalue) {
                            foreach ($norigin_fields as $nfield) {
                                if ($nfield['id'] === $nkey && isset($nfield['nbd_type']) && $nfield['nbd_type'] === 'production_time') {
                                    switch ($nfield['general']['attributes']['options'][$nvalue['value']]["name"]) {
                                        case 'RUSH':
                                            if ($order_type != $order_type_super_rush) {
                                                $order_type = $order_type_rush;
                                            }
                                            break;
                                        case 'Super RUSH':
                                            $order_type = $order_type_super_rush;
                                            break;
                                    };
                                }
                            }
                        }
                    }
                }
                update_post_meta($_GET['order_id'], 'order_type', $order_type);

                //Send email for customer
                send_botaksign_email($_GET['order_id'], 'ORDER CONFIRMED', 'A1.php');
                
                WC()->session->__unset('nbd_update_rush_order_' . $_GET['order_id']);
                ?>
                    <h3 style='text-align: center;'>Payment success! You will be redirected to the order page after 5 seconds!</h3>
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            window.setTimeout(function() {
                                window.location.href='<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . 'view-order/' . $_GET['order_id']; ?>';
                            }, 5000);
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
    
    function payment_success_page() {
        //Handle payment paypal
        $this->paypalResponse();
    }
}

new Payment_Paypal();
