<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-modal', CUSTOM_BOTAKSIGN_URL . 'assets/js/jquery.modal.min.js', array('jquery'), '3.3.4', true );
wp_enqueue_style( 'css-modal', CUSTOM_BOTAKSIGN_URL . 'assets/css/jquery.modal.min.css' );

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited

if ( ! $order ) {
	return;
}

$list_item             = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

//sort service and parent product
$order_items = [];
$items_parent = [];
$items_service = [];
$push_status = false;
$time_option_items = [];
$order_created_at = $order->get_date_created();
$can_reorder = true;
foreach ($list_item as $item_id => $item) {
    $product = $item->get_product();
    $product_id = $product->get_id();
    if($product_id) {
        $option_id = NBD_FRONTEND_PRINTING_OPTIONS::get_product_option($product_id);
        $_options = NBD_FRONTEND_PRINTING_OPTIONS::get_option( $option_id );
        if(isset($_options['modified']) && $_options['modified'] ) {
            if( strtotime($_options['modified']) > strtotime($order_created_at) ) {
                $can_reorder = false;
            }
        }
    }

    if ($item->get_meta('_parent_cart_item_key')) {
        $items_service[] = $item;
    } else {
        $items_parent[] = $item;
    }

    //CS botak RUSH top
    if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
        $options = $item->get_meta('_nbo_options');
        $origin_fields = unserialize($options['fields']);
        $origin_fields = $origin_fields['fields'];
        $item_fields = $item->get_meta('_nbo_field');

        foreach ($item_fields as $key => $value) {
            foreach ($origin_fields as $field) {
                if ($field['id'] === $key && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                    $current_priority = $field['general']['attributes']['options'][$value['value']]["priority"];
                    if($field['general']['attributes']['options']) {
                        foreach ($field['general']['attributes']['options'] as $option) {
                            if ($option['priority'] > $current_priority) {
                                $push_status = true;
                            }
                        }
                    }
                }
            }
        }
    };
}
$order_status = get_post_meta($order_id, '_cxecrt_status_od' . cxecrt_get_key_by_role_user(), true);
if ($order_status && $order_status !== '1') {
    $push_status = false;
}

//Accociate service with product parent
foreach ($items_parent as $item) {
    $order_items[] = $item;
    if ($item->get_meta('_cart_item_key')) {
        foreach ($items_service as $key => $s_item) {
            if ($s_item->get_meta('_parent_cart_item_key') === $item->get_meta('_cart_item_key')) {
                $order_items[] = $s_item;
                unset($items_service[$key]); //remove service
            }
        }
    }
}
//Check service is single
foreach ($items_service as $item) {
    $order_items[] = $item;
}

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}

$est_time = show_est_completion($order);

?>
<section class="woocommerce-order-details">
    <div class="rush-container">
        <div class="title">
            Need your order to be complete faster? 
            <?php if ($push_status): ?>
                <a class="btn button btn-top-up" href="#rushPopup" rel="modal:open">Top Up for RUSH</a>
            <?php else: ?>
                <a class="btn button btn-top-up-disabled">Top Up for RUSH</a>
            <?php endif; ?>
            <span class="rush-tool-tip"
                title="Top up for RUSH timings for the products in your current order">
            </span>
        </div>
        <!-- Modal -->
        <div class="rush-popup modal" id="rushPopup" role="dialog">
            <form id="rush-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST" enctype="multipart/form-data">
                <div class="popup-loadding">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin:auto;background:#fff;display:block;" width="100" height="100" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"> <g transform="rotate(0 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.9166666666666666s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(30 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.8333333333333334s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(60 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.75s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(90 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.6666666666666666s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(120 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5833333333333334s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(150 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.5s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(180 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.4166666666666667s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(210 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.3333333333333333s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(240 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.25s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(270 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.16666666666666666s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(300 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="-0.08333333333333333s" repeatCount="indefinite"></animate> </rect> </g><g transform="rotate(330 50 50)"> <rect x="47" y="24" rx="3" ry="6" width="6" height="12" fill="#00a651"> <animate attributeName="opacity" values="1;0" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animate> </rect> </g> </svg>
                </div>
                <div id="form-select-rush" style="display: block;">
                    <input type="hidden" name="action" value="nb_push_rush_paypal_request" />
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
                    <h2>PUSH Top-Up</h2>
                    <table>
                        <tr>
                            <th>Product(s)</th>
                            <th>Upgrade To :</th>
                            <th style="width: 150px;">Price ($)</th>
                        </tr>
                        <?php foreach ($list_item as $item_id => $item): ?>
                            <?php
                                $order_types = [];
                                $qty = $item->get_quantity();
                                $production_time_field_id = '';
                                if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
                                    $options = $item->get_meta('_nbo_options');
                                    $origin_fields = unserialize($options['fields']);
                                    $origin_fields = $origin_fields['fields'];
                                    $item_fields = $item->get_meta('_nbo_field');

                                    foreach ($item_fields as $key => $value) {
                                        foreach ($origin_fields as $field) {
                                            if ($field['id'] === $key && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                                                $production_time_field_id = $field['id'];
                                                $order_type = [];
                                                $current_priority = $field['general']['attributes']['options'][$value['value']]["priority"];
                                                if($field['general']['attributes']['options']) {
                                                    foreach ($field['general']['attributes']['options'] as $option) {
                                                        if ($option['priority'] >= $current_priority) {
                                                            $order_types[] = [
                                                                'value' => $option['priority'],
                                                                'name'  => $option['name']
                                                            ];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $product = $item->get_product();
                            ?>
                            <?php if (count($order_types) > 1): ?>
                                <input type="hidden" name="items_data[<?php echo $item_id; ?>][name]" value="<?php echo $product->get_title(); ?>"/>
                                <input type="hidden" name="items_data[<?php echo $item_id; ?>][quantity]" value="<?php echo $qty; ?>"/>
                                <input type="hidden" name="items_data[<?php echo $item_id; ?>][field_id]" value="<?php echo $production_time_field_id; ?>"/>
                                <input type="hidden" name="items_data[<?php echo $item_id; ?>][addition_price]" value="0"/>
                                <input type="hidden" name="items_data[<?php echo $item_id; ?>][addition_tax]" value="0"/>
                                <tr>
                                    <td><?php echo $product->get_title(); ?> x <?php echo $qty; ?></td>
                                    <td>
                                        <select name="items_data[<?php echo $item_id; ?>][value]" class="select-push-type" data-item="<?php echo $item_id; ?>" data-field="<?php echo $production_time_field_id; ?>">
                                            <?php foreach ($order_types as $key => $type): ?>
                                                <option value="<?php echo $type['value']; ?>"><?php echo $key == 0 ? '- Select option -' : $type['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="addition-price-<?php echo $item_id; ?>"><?php echo wc_price(0); ?></td>
                                </tr>
                            <?php elseif (count($order_types) == 1): ?>
                                <tr>
                                    <td><?php echo $product->get_title(); ?> x <?php echo $qty; ?></td>
                                    <td><?php echo $order_types[0]['name'] ?></td>
                                    <td>-</td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <tr>
                            <td class="border-none"></td>
                            <td class="push-top-title border-none">Subtotal</td>
                            <td class="push-top-price push-top-subtotal-price"><?php echo wc_price(0); ?></td>
                        </tr>
                        <tr>
                            <td class="border-none"></td>
                            <td class="push-top-title border-none">GST</td>
                            <td class="push-top-price push-top-gst-price"><?php echo wc_price(0); ?></td>
                        </tr>
                        <tr>
                            <td class="border-none"></td>
                            <td class="push-top-title border-none">Total</td>
                            <td class="push-top-price push-top-total-price"><?php echo wc_price(0); ?></td>
                        </tr>
                    </table>
                    <div class="new-order-time-info">
                        <div class="title"><b>The new completion time for your order would be:</b></div>
                        <div class="new-order-completed-time"><?= $est_time['shipping_datetime_completed']; ?></div>
                        <div class="notice">
                            <div>* This timing does not take into account:</div>
                            <div>- Delays due to unforseen circumstances</div>
                            <div>- Potential delays due to artwork issues</div>
                        </div>
                        <div class="notice-1-hour" style="display: none;">* The difference in completion time upgrading is less than 1 hour</div>
                    </div>
                    <div class="popup-action">
                        <a class="btn button close" href="#" rel="modal:close">Cancel</a>
                        <a class="btn button next" href="#">Next</a>
                    </div>
                </div>
                <div id="payment-form" class="woocommerce-checkout-payment" style="display: none;">
                    <div><b>Select payment method</b></div>
                    <ul class="wc_payment_methods payment_methods methods">
                        <?php
                            if (!empty(WC()->payment_gateways()->get_available_payment_gateways())) {
                                foreach (WC()->payment_gateways()->get_available_payment_gateways() as $gateway) {
                                    if ($gateway->id === 'paypal') {
                                        wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                                    }
                                }
                            } else {
                                echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce') ) . '</li>'; // @codingStandardsIgnoreLine
                            }
                        ?>
                    </ul>
                    <div class="popup-action">
                        <a class="btn button back" href="#">Back</a>
                        <button type="submit" class="button alt proceed" id="place_order" rel="modal:close">Yes, Proceed</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

    <?php 
    //cs botak fix time completed
    $order_data = $order->get_data();

    function botak_convert_format_time($time) {
        $_time= explode(':' , $time );
        $hourse = (int)$_time[0];
        if( $hourse < 12 ) {
            return $time.'am';
        } elseif( $hourse == 12 ) {
            return $time.'pm';
        } else {
            $hourse = $hourse - 12;
            return $hourse.':'.$_time[1].'pm';
        }
    }
    $plotting_options = unserialize(get_option('plotting_options'));
    $order_completed = get_post_meta($order->get_id() , '_order_time_completed' , true);
    $order_completed_str = get_post_meta($order->get_id() , '_order_time_completed_str' , true);
    $date = date('d-m-Y' , $order_completed_str);
    $method = $order->get_shipping_method();
    $period_time_delivery = '';
    $check_day = false;
    if($method=='Delivery') {
        foreach ($plotting_options as $key => $plotting_option) {
            if ($plotting_option['shipping_method']['title'] == 'Delivery') {
                $period_calc = $plotting_option['period_calc'];
                $period_calc = explode('-' , $period_calc );
                $period_dp   = $plotting_option['period_dp'];
                if( count($period_calc) == 2 ) {
                    if(v3_time_to_minutes($period_calc[1]) > 0 && v3_time_to_minutes($period_calc[0]) > 0) {
                        if( v3_time_to_minutes($period_calc[1]) > v3_time_to_minutes($period_calc[0]) ) {
                            $time_from = $date . ' ' . $period_calc[0];
                            $time_to = $date . ' ' . $period_calc[1];
                        } else {
                            $time_from = $date . ' ' . $period_calc[1];
                            $time_to = $date . ' ' . $period_calc[0];
                        }
                        $time_from_str = strtotime($time_from);
                        $time_to_str = strtotime($time_to);
                        if( $order_completed_str >= $time_from_str && $order_completed_str <= $time_to_str) {
                            $period_time_delivery = $period_dp;
                            if($period_time_delivery) {
                                $period_calc = explode('-' , $period_time_delivery );
                                $period_time_delivery = botak_convert_format_time( $period_calc[0]) .' - '.botak_convert_format_time( $period_calc[1]);
                            }
                            if($plotting_option['date'] == 'next_day') {
                                $check_day = true;
                            }
                        }
                    }
                } 
            }
        }
    }
    if($check_day) {
        $order_completed_str += 24*60*60;
    }

    //end
    ?>
	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>
        
        <div class="order-time-info">
            <div class="title"><b>The estimated completion time is:</b></div>
            <div class="time"><?php if($period_time_delivery != '') { echo $period_time_delivery.' '.date("d F Y" , $order_completed_str); } else { echo $est_time['total_time']; } ?></div>
            <div class="notice">
                <p>* This timing does not take into account:</p>
                <p>- Delays due to unforseen circumstances</p>
                <p>- Potential delays due to artwork issues</p>
            </div>
        </div>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php 
    if($can_reorder) {
        do_action( 'woocommerce_order_details_after_order_table', $order ); 
    }
    ?>
</section>

<script>
    jQuery(document).ready(function($) {
        $('.select-push-type').change(function(){
            $('.rush-popup .popup-loadding').addClass('show');
            var items = [];
            $('.select-push-type').map((index, e) => {
                items.push({
                    id: $(e).attr('data-item'),
                    field_id: $(e).attr('data-field'),
                    value: $(e).val()
                });
            })
            var data = {
                action: "update_time_order",
                items_data: items,
                order: <?php echo $order->get_id(); ?>
            }
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                data: data,
                success: function (res) {
                    var subtotal_price = 0;
                    var GST = 0;
                    res.addition_prices.map((value, index) => {
                        $('input[name="items_data[' + value.item + '][addition_price]"').val(value.addition_price);
                        $('input[name="items_data[' + value.item + '][addition_tax]"').val(value.addition_tax);
                        $('.addition-price-' + value.item).html(convert_to_wc_price(value.addition_price));
                        subtotal_price += value.addition_price;
                        GST += value.addition_tax;
                    });
                    $('.push-top-subtotal-price').html(convert_to_wc_price(subtotal_price));
                    $('.push-top-gst-price').html(convert_to_wc_price(GST));
                    $('.push-top-total-price').html(convert_to_wc_price(subtotal_price + GST));
                    $('.new-order-completed-time').html(res.order_new_date_completed);
                    if (res.show_notice_time) {
                        $('.notice-1-hour').show();
                    } else {
                        $('.notice-1-hour').hide();
                    }
                    $('.rush-popup .popup-loadding').removeClass('show');
                },
                function(){
                    alert('Error! Try again!');
                    $('.rush-popup .popup-loadding').removeClass('show');
                } 
            });
        })
        
        $("#rush-form").submit(function(e){
            e.preventDefault();
            $('.rush-popup .popup-loadding').addClass('show');
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                data: $("#rush-form").serialize(),
                success: function (res) {
                    if (res.status === 'success') {
                        window.location.href = res.approval_link;
                    } else {
                        alert(res.message);
                        $('.rush-popup .popup-loadding').removeClass('show');
                    }
                },
                function(){
                    alert('Error! Try again!');
                    $('.rush-popup .popup-loadding').removeClass('show');
                } 
            });
        })
        
        function convert_to_wc_price(price) {
            return accounting.formatMoney(price, {
                symbol: "<?php echo get_woocommerce_currency_symbol(); ?>"
            });
        };
        
        $("#rushPopup .button.next").click(function() {
            $("#payment-form").show();
            $("#form-select-rush").hide();
        });
        
        $(".rush-container .btn-top-up, #rushPopup .button.back").click(function() {
            $("#payment-form").hide();
            $("#form-select-rush").show();
        });
    })
</script>
<?php
if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}