<?php

/**
 * REST API Custom Orders controller
 *
 * Handles requests to the /orders endpoint.
 *
 */
defined( 'ABSPATH' ) || exit;

class WC_REST_Custom_Controller {
	/**
	 * You can extend this class with
	 * WP_REST_Controller / WC_REST_Controller / WC_REST_Products_V2_Controller / WC_REST_CRUD_Controller etc.
	 * Found in packages/woocommerce-rest-api/src/Controllers/
	 */
	protected $namespace = 'wc/v3';

	protected $rest_base = 'custom';

	protected $post_type = 'shop_order';

	public function __construct() {
       	add_action( 'rest_api_init' , array( $this , 'register_routes') );
    }

	public function register_routes() {
		// Order
		register_rest_route( $this->namespace, '/' . $this->rest_base,
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_orders' ),
			)
		);

		// Get total order
		register_rest_route( $this->namespace, '/' . $this->rest_base.'/total',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_total_order' ),
			)
		);

		// Get,update order by id
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)',  array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_order_by_id' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_order_by_id' ),
				'permission_callback' => array( $this, 'update_specialist_permissions_check' ),
			)
		));
		// get order detail
		register_rest_route( $this->namespace, '/' . $this->rest_base.'/order-detail/(?P<id>[\d]+)',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_order_detail_by_id' ),
			)
		);
		// get order items  by order id
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/items/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_order_items' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_order_item' ),			)
		));

		// get all specialist
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/specialist', 
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_all_specialist' ),
			)
		);

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/current-user', 
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_current_user' ),
			)
		);

		//get,update specialist by order id
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/specialist/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_specialist_by_order_id' ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_specialist' ),
				// 'permission_callback' => array( $this, 'update_specialist_permissions_check' ),
			)
		));

		// get artwork by order id
		register_rest_route( $this->namespace, '/' . $this->rest_base.'/artwork/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_artwork_by_order_id' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_artwork' ),
			),
		));
		register_rest_route( $this->namespace, '/' . $this->rest_base.'/del-artwork/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'delete_artwork' ),
			)
		));

		// get order by specialist
		register_rest_route( $this->namespace, '/' . $this->rest_base.'/orders-spec',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_order_by_specialist' ),
			)
		);

		//  SEARCH
		register_rest_route( $this->namespace, '/' . $this->rest_base.'/search',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'search' ),
			)
		);

		// get,update order notes
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/notes/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_order_notes_by_order_id' ),
			)
		));

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/shipping', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_shipping_method' ),
			)
		));
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/settings', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_setting_options' ),
			)
		));
		// Tools support design
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/tools', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_tools' ),
			)
		));

		// API Delivery Plotter
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/plotter', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_delivery_plotter' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_delivery_plotter' ),
			)
		));
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/plotter-search', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'search_order_out_plotter' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_order_out_plotter' ),
			)
		));
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/plotter/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_plotter_order_detail' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_plotter_order_detail' ),
			)
		));
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/period', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_period_time' ),
			),
		));

		// get List files download order items
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/list-link/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_list_link_order_items' ),
			)
		));
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/list-link-test/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_list_link_order_items_test' ),
			)
		));
	}
	
	public function get_current_user($request) {
		$current_user = wp_get_current_user();
		$response = rest_ensure_response( $current_user );
		return $response;
	}

	public function get_orders( $request ) {
		global $wpdb;
		$data = array();
		$prepared_args = array();
		$total_order = (int) $this->get_total_order();
		$posts_per_page = $request['per_page'] ? (int) $request['per_page'] : 20;
		$page = $request['page'] ? (int) $request['page'] - 1 : 0;
		$query = "SELECT id FROM {$wpdb->prefix}posts AS p WHERE p.post_type='shop_order' ORDER BY ID DESC";
		$order_ids = $wpdb->get_results($query);
		foreach ($order_ids as $key => $order_id) {
			if($key >= $page*$posts_per_page && $key < (($page+1)*$posts_per_page) ) {
				$data[] = $this->get_order( $order_id->id, $request );
			}
		}
		// Wrap the data in a response object.
		$datas = array();
		$datas['orders'] = $data;
		$datas['total'] = $total_order;
		$response = rest_ensure_response( $datas );
		return $response;
	}

	public function get_order_by_id( $request ) {
		$id   = (int) $request['id'];
		$post = get_post( $id );
		if($post) {
			$data = $this->get_order( $id, $request );
		} else {
			$data = array();
		}
		$response = rest_ensure_response( $data );

		$response->link_header( 'alternate', get_permalink( $id ), array( 'type' => 'text/html' ) );

		return $response;
	}

	public function get_total_order() {
		global $wpdb;
		$query = "SELECT * FROM {$wpdb->prefix}posts AS p WHERE p.post_type='shop_order'";
		$total = $wpdb->get_var("SELECT COUNT(1) FROM (${query}) AS combined_table");
		return (int) $total;
	}

	public function get_order_items($request) {
		$user_id = $request['user_id'];
		$user_can = v3_get_role_status_by_user($user_id);
		$order_id = (int) $request['id'];
		$order = wc_get_order($order_id);
		$item_meta = array();
		$order_items = $order->get_items('line_item');
		$order_no = 0;
		foreach ( $order_items as $item_id => $item ) {
			$product = wc_get_product($item->get_product_id());
			if( wc_get_product($item->get_product_id()) && !$product->is_type( 'service' ) ){
				$opt_status = wc_get_order_item_meta($item_id, '_item_status', true);
				if (!$opt_status || $opt_status == '0') {
	                $opt_status = 'order_received';
	                wc_update_order_item_meta($item_id, '_item_status', $opt_status);
	            }
	            $user_can[$opt_status] = unserialize(get_option('custom_status_order') )[$opt_status];
	            $nbu_item_key = wc_get_order_item_meta($item_id, '_nbu');
	            $nbd_item_key = wc_get_order_item_meta($item_id, '_nbd');
	            $download = wc_get_order_item_meta($item_id , '_nbd_item_edit');
	            $time_completed = wc_get_order_item_meta($item_id , '_item_time_completed');
				if(!isset($time_completed) || $time_completed == '') {
					$time_completed = date( 'd/m/Y H:i a' , strtotime( v3_get_time_completed_item(v3_get_production_time_item($item ,$order , true) ,$order)['production_datetime_completed'] ) );
					wc_update_order_item_meta($item_id , '_item_time_completed' , $time_completed);
				}
				$date_now = date("H:i Y/m/d");
				$check_expiring = '';
				$_time_cpl = wc_get_order_item_meta($item_id , '_item_time_completed');
				$_time_cpl = str_replace('am' , '' , $_time_cpl);
				$_time_cpl = str_replace('pm' , '' , $_time_cpl);
				$_time_cpl = str_replace('/' , '-' , $_time_cpl);
				$expiring = ( strtotime( $_time_cpl ) - strtotime("now") )/3600 - 8;
				if($expiring <= 2 && ( $opt_status != 'collection_point' && $opt_status != 'collected' ) ) {
					$check_expiring = 'expiring';
				}
				$roles = get_userdata($user_id)->roles;
				if(in_array('specialist', $roles) && $opt_status != 'order_received' && $opt_status != 'cancelled' ) {
			        unset($user_can['cancelled']);
			    }
				$items['order_no']			= $order_no;
				$items['item_id']			= $item_id;
				$items['download']			= $download;
				$items['order_id']			= $order_id;
				$items['id']				= $item->get_product_id();
				$items['name']				= $item->get_name();
				$items['qty']				= $item->get_quantity();
				$items['status']			= $opt_status;
				$items['nbu']				= $nbu_item_key ? $nbu_item_key : '';
				$items['nbd']				= $nbd_item_key ? $nbd_item_key : '';
				$items['production_time']	= v3_get_production_time_item($item ,$order , false);
				$items['date_completed']	= $time_completed;
				$items['date_out']			= wc_get_order_item_meta($item_id, '_item_date_out');
				$items['list_status']		= $user_can;
				$items['expiring']			= $check_expiring;
				$items['item_on_hold']      = wc_get_order_item_meta($item_id , '_item_on_hold');
				$items['user_can']    		= 'edit';
				$item_meta[] = $items;
				$order_no ++;
			}	
			
		} 
		$response = rest_ensure_response( $item_meta );
		return $response;
	}

	public function get_order( $order_id, $request ) {
		$order = wc_get_order($order_id);
		$dp    = wc_get_price_decimals();
		$date_created = date( 'd/m/Y H:i a' , strtotime($order->get_date_created()) + 8*3600 );
		$date_completed = get_post_meta( $order_id , '_order_time_completed' , true);
		if(!isset($date_completed) || $date_completed == '') {
			$date_completed = date( 'd/m/Y H:i a' , strtotime(show_est_completion($order)['production_datetime_completed']) );
			update_post_meta( $order->get_id() , '_order_time_completed', $date_completed );
			update_post_meta( $order->get_id() , '_order_time_completed_str', strtotime(show_est_completion($order)['production_datetime_completed']) );
		}
		// update specialist if miss
		$_specialist_id = get_post_meta( $order_id , '_specialist_id' , true);
		$current_user = get_post_meta($order_id, '_customer_user', true);
	    if($current_user && !$_specialist_id) {
	        $_specialist_id = get_user_meta($current_user, 'specialist', true);
	        if($_specialist_id) {
	            update_post_meta($order_id, '_specialist_id', $_specialist_id);
	        }
	    }
	    //
		$order_items = $order->get_items('line_item');
		$check_expiring = '';
		$date_now = date("H:i Y/m/d");
		$has_os = false;
		if(!get_post_meta( $order_id , '_order_status' , true)) {
			if( $order->get_status() == 'processing' ) {
				update_post_meta( $order_id , '_order_status' , 'New');
			} elseif($order->get_status() == 'pending' || $order->get_status() == 'on-hold') {
				update_post_meta( $order_id , '_order_status' , 'Pending');
				$has_os = true;
			} elseif($order->get_status() == 'cancelled') {
				update_post_meta($order->get_id() , '_order_status' , 'Cancelled');
			} elseif ($order->get_status() == 'completed') {
				update_post_meta($order->get_id() , '_order_status' , 'Collected');
			}
		}
		foreach ($order_items as $item_id => $item) {
			$production_time = v3_get_production_time_item($item ,$order , true);
			$time_completed = date( 'd/m/Y H:i a' , strtotime( v3_get_time_completed_item($production_time ,$order)['production_datetime_completed'] ) );
			wc_update_order_item_meta($item_id , '_item_time_completed' , $time_completed);
			// $_time = wc_get_order_item_meta($item_id , '_item_time_completed' );
			$_time = $time_completed;
			$_time = str_replace('am' , '' , $_time);
			$_time = str_replace('pm' , '' , $_time);
			$_time = str_replace('/' , '-' , $_time);
			$expiring = ( strtotime($_time) - strtotime("now") )/3600 - 8;
			$item_status = wc_get_order_item_meta($item_id, '_item_status');
			if($expiring <= 2 && ( $item_status != 'collection_point' && $item_status != 'collected' )  ) {
				$check_expiring = 'expiring';
			}
			if( $item->get_product_id() != 0 && !wc_get_product($item->get_product_id())->is_type( 'service' ) ){
				if ($has_os) {
	                $item_status = 0;
	                wc_update_order_item_meta($item_id, '_item_status', $opt_status);
	                
	            }
			}
		}
		// update status payment by woocoomerce
		$payment_status = get_post_meta($order->get_id() , '_payment_status' , true);
		if($payment_status != 'paid') {
			if( ($order->get_payment_method() == 'paypal' || $order->get_payment_method() == 'omise_paynow' || $order->get_payment_method() == 'omise') && ( $order->get_status() == 'on-hold' || $order->get_status() == 'pending' ) ) {
				$payment_status = 'pendding';
			} 
			if ($order->get_payment_method() == 'cod') {
				$payment_status = 'pendding';
			}
			if( ($order->get_payment_method() == 'paypal' || $order->get_payment_method() == 'omise_paynow' || $order->get_payment_method() == 'omise') && ( $order->get_status() == 'processing' || $order->get_status() == 'completed' ) ) {
				$payment_status = 'paid';
			}
			if( $order->get_status() == 'cancelled' ||  $order->get_status() == 'failed') {
				$payment_status = 'cancelled';
			}
			
			if(get_post_meta( $order_id , '_order_status' , true) == 'Cancelled') {
				$payment_status = 'cancelled';
			}
		}
		// if( get_post_meta($order_id, '_cxecrt_status_od', true) == 11 || get_post_meta($order_id, '_cxecrt_status_od', true) == 9 ) {
		// 	update_post_meta($order->get_id() , '_order_status' , 'Collected');

		// }
		if($payment_status != '') {
			update_post_meta($order_id , '_payment_status' , $payment_status);
		}
		$invoice_number = get_post_meta($order->get_id() , '_wcpdf_invoice_number' , true);
		$link_pdf_invoice = '';
		if($payment_status == 'paid' || $invoice_number ) {
			$link_pdf_invoice = admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&document_type=invoice&order_ids=" . $order_id).'&_wpnonce='.wp_create_nonce('generate_wpo_wcpdf');
		}
		// Get link download order detail
		// global $botakit;
	    $link_down = '';
	    if ($order_id) {
	        // $html = v3_generate_order_detail_pdf($order_id);
	        // $botakit->_content = $html;
	        // $filename = 'order-' . $order_id . '.pdf';
	        // $botakit->generate_pdf_template($filename , false);
	        // $pdf_path = $botakit->_file_to_save . '/' . $filename;
	        // $link_down = convertLinkDesign($pdf_path);
	        $link_down = home_url().'/order-detail/?order_id='.$order_id;
	    }

	    //zip file s3
		$listPrefix_s3 = array();
        $product_s3 = $order->get_items();
        $list_file = array();
        $list_files = array();
        $no = 1;
        foreach($product_s3 as $order_item_id => $product) {
            $nbu_item_key_s3 = wc_get_order_item_meta($order_item_id, '_nbu');  
            $nbd_item_key_s3 = wc_get_order_item_meta($order_item_id, '_nbd'); 
            if($nbu_item_key_s3) {
            	$list_nbu_Prefix_s3[] = $nbu_item_key_s3;
            }
            if($nbd_item_key_s3) {
            	$list_nbd_Prefix_s3[] = $nbd_item_key_s3;
            }     
        }
        if($list_nbu_Prefix_s3) {
        	$listNbuPrefixStr = implode(",",$list_nbu_Prefix_s3);
        }
		if($list_nbd_Prefix_s3) {
			$listNbdPrefixStr = implode(",",$list_nbd_Prefix_s3);
		}
		$url_s3 = get_home_url().'/s3_zip/index.php?order_id='.$order_id;
		if($listNbuPrefixStr) {
			$url_s3.= '&prefix_nbu='.$listNbuPrefixStr;
		}
		if($listNbdPrefixStr) {
			$url_s3.= '&prefix_nbd='.$listNbdPrefixStr;
		}
		$link_s3 = '';
		if( count($list_nbu_Prefix_s3) || count($list_nbd_Prefix_s3)  ) {
			// $link_s3 = str_replace( 'http' , 'https' ,v3_getLinkAWS($order_id) );
			$link_s3 = $url_s3.'&t='.strtotime('now');
		} 
	 //    $link_s3 = '';
		// if( v3_getLinkAWS($order_id) ) {
		// 	$link_s3 = v3_getLinkAWS($order_id);
		// } else {
		// 	if( v3_check_link_download_order($order_id) ) {
		// 		$link_s3 = 'zip_fail';
		// 	}
		// }
		$data = array(
			'id'                   => $order->get_id(),
			'status'               => get_post_meta( $order_id , '_order_status' , true),
			'currency'             => $order->get_currency(),
			'date_created'         => $date_created,
			'date_modified'        => $order->get_date_modified(),
			'date_time_out'        => get_post_meta($order_id , '_order_time_out' , true),
			'discount_total'       => wc_format_decimal( $order->get_total_discount(), $dp ),
			'discount_tax'         => wc_format_decimal( $order->get_discount_tax(), $dp ),
			'shipping_total'       => wc_format_decimal( $order->get_shipping_total(), $dp ),
			'shipping_tax'         => wc_format_decimal( $order->get_shipping_tax(), $dp ),
			'cart_tax'             => wc_format_decimal( $order->get_cart_tax(), $dp ),
			'total'                => wc_format_decimal( $order->get_total(), $dp ),
			'total_tax'            => wc_format_decimal( $order->get_total_tax(), $dp ),
			'billing'              => array(),
			'shipping'             => array(),
			'full_name'			   => $order->get_formatted_billing_full_name(),
			'user_link'			   => add_query_arg( 'user_id', $order->get_user_id(), self_admin_url( 'user-edit.php' ) ),
			'shipping_method'      => $order->get_shipping_method(),
			'payment_status'       => $payment_status,
			'payment_method'       => $order->get_payment_method(),
			'payment_method_title' => $order->get_payment_method_title(),
			'created_via'          => $order->get_created_via(),
			'customer_note'        => $order->get_customer_note(),
			'date_completed'       => $date_completed,
			'date_paid'            => $order->get_date_paid(),
			'specialist'           => array(),
			'link_download'        => $link_s3,
			'order_on_hold'        => get_post_meta( $order_id , 'order_on_hold' , true),
			'order_time_out'       => get_post_meta( $order_id , '_order_time_out', true),
			'expiring'       	   => $check_expiring,
			'pdf_invoice'		   => $link_pdf_invoice,
			'order_detail'		   => $link_down,
		);

		// Add addresses.
		$data['billing']  = $order->get_address( 'billing' );
		$data['shipping'] = $order->get_address( 'shipping' );
		
		// Get Specialist.
	    $id_specialist = get_user_meta( $order->get_user_id() , 'specialist' ,true);
	    $specialist = get_userdata($id_specialist)->display_name;
	    $data['specialist']['name'] = $specialist ? $specialist : 'No Specialist' ;
	    $data['specialist']['id'] = $id_specialist ? $id_specialist : '-1' ;
		$specialist_roles = get_userdata($id_specialist)->roles;
		if(!in_array('specialist', $specialist_roles, true)) {
			$data['specialist']['name'] = 'No Specialist' ;
			$data['specialist']['id'] = '-1';
		}

		return $data;

	}

	public function get_order_detail_by_id($request) {
		$order_id = (int) $request['id'];
		$order = wc_get_order($order_id);
		$order_items = $order->get_items('line_item');
		$data = array();
		$datas = array();
		$payment_status = '';
		$payment_status = get_post_meta($order->get_id() , '_payment_status' , true);
		if(get_post_meta( $order_id , '_order_status' , true) == 'Cancelled') {
			$payment_status = 'cancelled';
			update_post_meta($order->get_id() , '_payment_status' , 'cancelled');
		}
		if(!isset($payment_status) || $payment_status == '' ) {
			if( ($order->get_payment_method() == 'paypal' || $order->get_payment_method() == 'omise_paynow' || $order->get_payment_method() == 'omise') && $order->get_status() == 'processing' ) {
				$payment_status = 'paid';
				update_post_meta($order->get_id() , '_payment_status' , 'paid');
			}
			if ($order->get_payment_method() == 'cod') {
				$payment_status = 'pendding';
				update_post_meta($order->get_id() , '_payment_status' , 'pendding');
			}
		}
		$_invoice_number  = '';
        if($payment_status == 'paid') {
            $_invoice_number = get_post_meta( $order->get_id(), '_wcpdf_invoice_number', true);
        }
		$data = array(
			'full_name'			   => $order->get_formatted_billing_full_name(),
			'billing'			   => $order->get_address('billing'),
			'shipping_method'      => $order->get_shipping_method(),
			'payment_status'       => $payment_status,
			'payment_method'       => $order->get_payment_method(),
			'payment_method_title' => $order->get_payment_method_title(),
			'email_value'		   => 'mailto:'.$order->get_billing_email(),
			'email'				   => $order->get_billing_email(),
			'phone'				   => $order->get_billing_phone(),
			'phone_value'		   => 'tel:'.$order->get_billing_phone(),
			'invoice_date'		   => date_format( date_create(get_post_meta( $order_id, '_wcpdf_invoice_date_formatted', true)) , "F  d  Y" ),
			'order_id'		       => $order_id,
			'order_date'		   => date_format($order->get_date_created() , "F  d  Y"),
			'invoice_no'		   => $_invoice_number,
		);
		$_item = array();
		$_items = array();
		$meta_data = array();
		$meta_datas = array();
		$has_meta = '';
		$index = 0;
		foreach ($order_items as $item_id => $item) {
			$formatted_meta_data = $item->get_formatted_meta_data('_', true);
            foreach ($formatted_meta_data as $k => $v) {
                if($v->key != "_item_status" && $v->key != "_item_meta_service" && $v->key != "_item_meta_issue" && $v->key != "_item_on_hold") {
                	$meta_data['key'] = $v->display_key;
                	$meta_data['value'] = $v->display_value;
                	$has_meta = 'has_meta';
                	$meta_datas[] = $meta_data;
                }	
            }
            $last_name = '';
            if( wc_get_product($item->get_product_id())->is_type( 'service' ) ){
            	$has_meta = '';
				$last_name = '<span style="color:#f00"><b> * ( Issue )</b></span>';
			}
			$_item = array(
				'index' 	=> $index,
				'has_meta'	=> $has_meta,
				'meta_data' => $meta_datas,
				'qty'		=> $item['quantity'],
				'tax'		=> wc_price($item['subtotal_tax']),
				'total'		=> wc_price($item['line_total']),
				'name'		=> $item->get_name().''.$last_name,
			);
			$_items[] = $_item;
			$index++;
		}
		// get link download order detail
		global $botakit;
	    $link_down = '';
	    if ($order_id) {
	        $html = v3_generate_order_detail_pdf($order_id);
	        $botakit->_content = $html;
	        $filename = 'order-' . $order_id . '.pdf';
	        $botakit->generate_pdf_template($filename);
	        $pdf_path = $botakit->_file_to_save . '/' . $filename;
	        $link_down = convertLinkDesign($pdf_path);
	    }
	    //end

		$datas['item'] = $_items;
		$datas['orders'] = $data;
		$datas['download'] = $link_down;
		$response = rest_ensure_response( $datas );
		return $response;
	}

	protected function prepare_links( $order, $request ) {
		$links = array(
			'self' => array(
				'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->rest_base, $order->get_id() ) ),
			),
			'collection' => array(
				'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ),
			),
		);
		if ( 0 !== (int) $order->get_user_id() ) {
			$links['customer'] = array(
				'href' => rest_url( sprintf( '/%s/customers/%d', $this->namespace, $order->get_user_id() ) ),
			);
		}
		if ( 0 !== (int) $order->get_parent_id() ) {
			$links['up'] = array(
				'href' => rest_url( sprintf( '/%s/orders/%d', $this->namespace, $order->get_parent_id() ) ),
			);
		}
		return $links;
	}

	// Lấy tất cả specialist
	public function get_all_specialist($request) {
		$args = array(
		    'role'    => 'specialist',
		    'orderby' => 'id',
		    'order'   => 'ASC'
		);
		$users = get_users( $args );
		$data = array();
		foreach ($users as $key => $value) {
			$user = array();
			$user['id'] = $value->ID;
			$user['name'] = $value->data->display_name;
			$data[] = $user;
		}
		$response = rest_ensure_response( $data );
		return $response;
	}

	// Lấy danh sách tất cả order theo mảng Specialist
	public function get_order_by_specialist($request){
		$specialist_id = array();
		$specialists = json_decode($request['specialist']);
		$get_total = $request['get_total'];
		$posts_per_page = $request['per_page'] ? (int) $request['per_page'] : 20;
		$page = $request['page'] ? (int) $request['page'] - 1 : 0;
		$meta_query = array();
		if(!empty($specialists)) {
			foreach ($specialists as $value) {
				$specialist_id[] = $value->id;
			}
		} else {
			if($page == '-2' || (isset($get_total) && $get_total == 1) ) {
				return $this->get_total_order();
			}
			return $this->get_orders($request);
		}
		if(count($specialist_id) > 0) {
			$meta_query['relation'] =  'OR';
			foreach ($specialist_id as $key => $value) {
				$meta_query[] = array(
					'key'	=> 'specialist',
			    	'value'	=> $value,
			    	'compare'	=> '='
				);
			}
		}
		$args = array(
		    'orderby' => 'id',
		    'order'   => 'ASC',
		    'meta_query'   => $meta_query,
		);
		$users = get_users( $args );
		$order_ids = array();
		foreach ($users as $key => $user) {
			$customer_orders = get_posts( array(
		        'numberposts' => -1,
		        'meta_key'    => '_customer_user',
		        'meta_value'  => $user->ID,
		        'post_type'   => 'shop_order',
		        'post_status' => array_keys( wc_get_order_statuses() ),
		    ) );
		    foreach ($customer_orders as $customer_order) {
		    	$order_ids[] = $customer_order->ID;
		    }
		}
		$total = count($order_ids);
		$data = array();
		if(!empty($order_ids)) {
			foreach ($order_ids as $key => $order_id) {
				if($key >= $page*$posts_per_page && $key < (($page+1)*$posts_per_page) ) {
					$data[] = $this->get_order( $order_id, $request );
				}
			}
		}
		// Wrap the data in a response object.
		$datas = array();
		$datas['orders'] = $data;
		$datas['total'] = $total;
		$response = rest_ensure_response( $datas );
		return $response;
	}
	public function get_artwork_by_order_id($request) {
		global $wpdb;
		$order_id = (int) $request['id'];
		$order = wc_get_order($order_id);
		$order_items = $order->get_items('line_item');
		$items = array();
		$product_items = array();
		$issue_items = array();
		$service_items = array();
		$artwork = array();
		$artworks = array();
		if(isset($order_items)) {
			foreach ( $order_items as $item_id => $item ) {
				$product['id'] = $item->get_product_id();
				$product['name'] = $item->get_name();
				$product['item_id'] = $item_id;
				if( !wc_get_product($item->get_product_id())->is_type( 'service' ) ){
					$product_items[] = $product;
				}	
					
			}
			foreach ( $order_items as $item_id => $item ) {
				$meta_service = wc_get_order_item_meta($item_id, '_item_meta_service');
				$meta_issue = wc_get_order_item_meta($item_id, '_item_meta_issue'); 

				if($meta_issue) {
					$artwork['item_id'] = $item_id;
					$artwork['meta_service'] = $meta_service;
					$artwork['meta_issue'] = $meta_issue;
					if( !wc_get_product($item->get_product_id())->is_type( 'service' ) ){
						$artworks[] = $artwork;
					}	
				} 	
			}
		}
		if(empty($artworks)) {
			$artwork['item_id'] = '';
			$artwork['meta_service'] = 0;
			$artwork['meta_issue'] = 0;
			$artworks[] = $artwork;
		}
		while (have_rows('list_issue', 'option')) { 
			the_row();
        	$issue_items[] = get_sub_field('issue'); 
        }

		$query = "SELECT SQL_CALC_FOUND_ROWS wp_posts.ID FROM wp_posts LEFT JOIN wp_term_relationships ON (wp_posts.ID = wp_term_relationships.object_id) WHERE 1=1 AND ( wp_term_relationships.term_taxonomy_id IN (171) ) AND wp_posts.post_type = 'product' AND (wp_posts.post_status = 'publish' OR wp_posts.post_status = 'acf-disabled' OR wp_posts.post_status = 'wc-nbdq-new' OR wp_posts.post_status = 'wc-nbdq-pending' OR wp_posts.post_status = 'wc-nbdq-expired' OR wp_posts.post_status = 'wc-nbdq-accepted' OR wp_posts.post_status = 'wc-nbdq-rejected' OR wp_posts.post_status = 'future' OR wp_posts.post_status = 'draft' OR wp_posts.post_status = 'pending' OR wp_posts.post_status = 'private') GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC LIMIT 0, 4";
        $service = $wpdb->get_results($query);
        foreach ($service as $key => $value) {
        	$service['price'] = wc_get_product($value)->get_price();
            $service['name'] = wc_get_product($value)->get_name();
            $service['id'] = wc_get_product($value)->get_id();
            $service_items[] = $service;
        }
        $items['product_items'] = $product_items;
        $items['issue_items'] = $issue_items;
        $items['service_items'] = $service_items;
        $items['artworks'] = $artworks;
		$response = rest_ensure_response( $items );
		return $response;
	}

	public function get_specialist_by_order_id($request) {
		$id  = (int) $request['id'];
		$specialist = $this->get_user($id  , $request);
		return rest_ensure_response( $specialist );
	}
	
	public function get_order_notes_by_order_id($request) {
		global $wpdb;
		$order_id = (int) $request['id'];
		$query = "SELECT * FROM wp_comments WHERE 1=1 AND wp_comments.comment_post_ID = '${order_id}' AND wp_comments.comment_type = 'order_log' ORDER BY wp_comments.comment_ID DESC";
		$results = $wpdb->get_results($query);
		$item = array();
		$order_logs = array();
		foreach ($results as $key => $value) {
			$item['date'] = date( 'd/m/Y H:i a' , strtotime($value->comment_date ) ); 
			$item['order_item'] = $value->comment_content; 
			$item['changes'] = $value->comment_agent; 
			if($value->comment_author != '') {
				$item['user'] = $value->comment_author . '(' . $value->comment_author_email. ')'; 
			} else {
				$item['user'] = '';
			}
			
			$order_logs[] = $item;
		}
		return rest_ensure_response( $order_logs );
	}
	public function get_setting_options($requests) {
		$time_refresh_new = $requests['time'];
		$data = array();
		// time refresh
		if(!$time_refresh_new) {
			$time_refresh = get_option('time_refresh_order_dashboard');
			if(!$time_refresh) {
				update_option('time_refresh_order_dashboard' ,  30);
				$time_refresh = 30;
			}
		} else {
			update_option('time_refresh_order_dashboard' ,  $time_refresh_new);
			$time_refresh = $time_refresh_new;
		}
		// per page
		$per_page_new = $requests['per_page'];
		if(!$per_page_new) {
			$per_page = get_option('per_page_order_dashboard');
			if(!$per_page) {
				update_option('per_page_order_dashboard' ,  25);
				$per_page = 25;
			}
		} else {
			update_option('per_page_order_dashboard' ,  $per_page_new);
			$per_page = $per_page_new;
		}
		// $data['time_refresh'] = $time_refresh;
		$data['time_refresh'] = 1000;
		$data['per_page'] = $per_page;
		$response = rest_ensure_response($data);
		return $response;
	}
	public function add_order_notes($request, $change , $log) {
		$user_id = $request['user_id'] ? (int) $request['user_id'] : 2;
		$user =  get_userdata($user_id);
		$user_name = $user->display_name;
		$user_email = $user->user_email;
		$order_id = (int) $request['id'];
		$type = 'order_log';
		if($log == 'update_status_order') {
			$user_name = '';
			$user_email = '';
			$log = '';
		}
		$args = array(
			'comment_post_ID' => $order_id,
			'comment_author' => $user_name,
			'comment_agent' => $change,
			'comment_content' => $log,
			'comment_author_email' => $user_email,
			'comment_type' => $type,
			'comment_approved' => 2,

		);
		wp_insert_comment($args);
	}

	public function update_specialist($request) {
		$order_id = (int) $request['id'];
		$specialist_id = (int) $request['specialist_id'];
		$order = wc_get_order($order_id);
		$user_id = $order->get_user_id();
		if($user_id != $specialist_id) {
			update_post_meta($order_id, '_specialist_id', $specialist_id);
			update_user_meta($user_id , 'specialist' , $specialist_id);
			update_user_meta($user_id , '_specialist' , 'field_5e79d59463a04');
		}
		$data = array();
		$data['specialist_id'] = $specialist_id;
		$data['status'] = 'success';
		$response = rest_ensure_response( $data);
		return $response;
	}
	public function get_tools($request) {
		$type = $request['type'];
		$value = $request['value'];
		$data = array(
			'flag' => 0
		);
		if($type == 'fix-design-pdf' && $value) {
			require_once( NBDESIGNER_PLUGIN_DIR.'includes/class-output.php' );
    		$files = Nbdesigner_Output::_export_pdfs( $value);
    		if($files && is_array($files)) {
    			$check = true;
    			foreach ($files as $key => $file) {
    				if(!$file || !file_exists($file)) {
    					$check = false;
    					break;
    				}
    			}
    			if($check) {
	    			$data['flag'] = 1;
	    		}
    		}
    		$data['design_dettal'] = $files;
		}
		$response = rest_ensure_response( $data);
		return $response;
	}
	public function update_artwork($request) {
		$order_id = (int) $request['id'];
		$data = json_decode($request['artworks']);
		$artwork = array();
		$artworks = array();
		$log = false;
		$order = wc_get_order($order_id);
		$order_items = $order->get_items('line_item');
		$count_item = count($order_items);
		$flag = 0;
		$price_old = get_post_meta($order_id , 'price_old' , true);
		if( !$price_old) {
			update_post_meta($order_id , 'price_old' , $order->get_total() -  $order->get_total_tax() );
		}
		$price_service = 0;
		foreach ($data as $key => $value) {
			$item_id = $value->item_id;
			$item_service = $value->item_service;
			$item_issue = $value->item_issue;
			$item_id_current = wc_get_order_item_meta($item_id , '_item_id_service');
			wc_delete_order_item( $item_id_current );
			wc_update_order_item_meta($item_id, '_item_meta_service' , $item_service  );
			wc_update_order_item_meta($item_id, '_item_meta_issue' , $item_issue);
			$meta_service = wc_get_order_item_meta($item_id, '_item_meta_service');
			$meta_issue = wc_get_order_item_meta($item_id, '_item_meta_issue');
			if( isset($meta_issue) && $meta_issue != '') {
				$artwork[$item_id] = 'artwork_amendment';
				$artworks[] = $artwork;
			} 
			if( $item_service && $item_issue && $item_id) {
				$flag ++;
				//update button on-hold
				
				wc_update_order_item_meta($item_id , '_item_on_hold' , 'on_hold');
				$datas['item_on_hold'][$key] = wc_get_order_item_meta($item_id, '_item_on_hold');
				$datas['item_status'][$key] = 'artwork_amendment';
				//update status
				wc_update_order_item_meta($item_id , '_item_status' , 'artwork_amendment');

				// Update log
				$item_index = 1;
				foreach ($value->product_items as $k => $v) {
					if($v->id == $item_id) {
						$item_index = $k + 1;
					}
				}
				$_get_item = new WC_Order_Item_Product($item_id);
				$this->add_order_notes($request ,  'Artwork Amendment', $item_index. ' - ' .$_get_item->get_name() );
				update_post_meta( $order_id , 'order_on_hold' , 'on_hold');
				$datas['order_on_hold'] = get_post_meta( $order_id , 'order_on_hold' , true);
				
				$product = wc_get_product($item_service );
				$price_service += (float)$product->get_price();
                $args = array(
                    'attribute_billing-period' => 'Yearly',
                    'attribute_subscription-type' => 'Both',
                );
                $quantity = 1;
                $item_id_new = $order->add_product($product, $quantity, $args);
                wc_update_order_item_meta($item_id , '_item_id_service' , $item_id_new);
                wc_update_order_item_meta($item_id_new , '_product_type' , 'service');
			}			
		}
		if($flag == $count_item && $item_service && $item_issue ) {
			update_post_meta( $order_id , '_order_status', 'Ongoing' );
			// update status order WC
			wp_update_post(array(
			    'ID'    =>  $order_id,
			    'post_status'   =>  'wc-processing'
			));
			$this->add_order_notes($request , 'Ongoing' ,  'update_status_order');
			$datas['order_status'] = 'Ongoing';
		}
		$order = wc_get_order($order_id);
		if($flag > 0) {

			// update status order WC
			if($price_service > 0) {
				wp_update_post(array(
				    'ID'    =>  $order_id,
				    'post_status'   =>  'wc-pending'
				));
			}
				
			// sub price old
		    if(!$price_old) {
		    	update_post_meta($order_id, '_status_pay_artwork_form', 0);
		    	$price_old = get_post_meta($order_id , 'price_old' , true);
		    	$item_fee = new WC_Order_Item_Fee();
			    $item_fee->set_name( "Reduction Of Advance Payment" ); // Generic fee name
			    $item_fee->set_amount( '-'.$price_old); // Fee amount
			    $item_fee->set_tax_class( '' ); // default for ''
			    $item_fee->set_tax_status( '' ); // or 'none'
			    $item_fee->set_total( '-'.$price_old); // Fee amount
			    $order->add_item( $item_fee ); 
		    }
		    
		    // update price order and send mail
			send_botaksign_email($order_id , 'ARTWORK AMENDMENT', 'C1.php');
		}
		$order->calculate_totals();
		$order->save();
		$datas['artworks'] = $artworks;
		$response = rest_ensure_response( $datas);
		return $response;
	}

	public function update_order_by_id($request) {
		
	}
	public function get_user( $id, $request ) {
		
	}

	// DELETE

	public function delete_artwork($request) {
		$item_id = (int) $request['id'];
		$order_id = WC_Order_Item_Data_Store::get_order_id_by_order_item_id($item_id);
		$order = wc_get_order($order_id);
		$item_id_current = wc_get_order_item_meta($item_id , '_item_id_service');
		wc_update_order_item_meta($item_id, '_item_meta_service' , 0  );
		wc_update_order_item_meta($item_id, '_item_meta_issue' , 0);
		wc_update_order_item_meta($item_id , '_item_id_service' , '');
		if(isset($item_id_current)) {
			wc_delete_order_item( $item_id_current );
			$order->calculate_totals();
		}
	}

	public function update_order_item($request) {
		$order_id = (int) $request['id'];
		$order = wc_get_order($order_id);
		$user_id = (int) $request['user_id'];
		$user_can = v3_get_role_status_by_user($user_id);
		$item_id = (int) $request['item_id'];
		$status = $request['status'];
		$update_est = $request['update_est'];
		$payment_status = $request['payment_status'];
		$new_date = json_decode($request['new_date']);
		$max_time = 0;
		$opt_status = wc_get_order_item_meta($item_id, '_item_status', true);
		if( isset($new_date) && $new_date != '') { 
			foreach ($new_date as $key => $value) {
				if($value->item_select_date != '' && $value->item_select_time != '') {
					$str_new_time = strtotime($value->item_select_date.' '. $value->item_select_time);
					$_new_date = date( 'd/m/Y H:i a' , $str_new_time );
					if( $str_new_time > $max_time) {
						$max_time = $str_new_time;
						update_post_meta( $order_id , '_order_time_completed' , $_new_date);
						update_post_meta( $order_id , '_order_time_completed_str' , $str_new_time);
						if( ( $str_new_time - strtotime("now") - 8*3600 )/3600 <= 2 && ( $opt_status != 'collection_point' && $opt_status != 'collected' ) ) {
							$items['check_expiring'] = 'expiring';
						}
						$items['order_new_date'] = $_new_date;
					}
					wc_update_order_item_meta($value->item_id , '_item_time_completed' , $_new_date);
					$items['item_new_date'][$key] = $_new_date;
					$_get_item = new WC_Order_Item_Product($item_id);
					send_botaksign_email($order_id , 'ORDER COMPLETION DELAY', 'H1.php');
					$this->add_order_notes($request , 'Reprint(*'.$value->item_select_time.'* *'.$value->item_select_date.'*)' , $value->item_no.' - '.$_get_item->get_name() );
				}
			}
		}
		if(isset($payment_status) && $payment_status != '') {
			$payment_status = 'paid';
			update_post_meta($order_id , '_payment_status' , 'paid');
			send_botaksign_email($order_id , 'ORDER RECEIVED', 'G1.php');
		}
		if( isset($status) && $status != '') {
			$_order_status_df = get_post_meta( $order_id , '_order_status', true );
			wc_update_order_item_meta($item_id , '_item_status' , $status);
			$data = array();
			$order_items = $order->get_items('line_item');
			$order_no = 0;
			$item_index = 1;
			$new = 0;
			$_order_status_wc = '';
			$ongoing = true;
			$completed = 0;
			$collected = 0;
			$cancelled = 0;
			$count_item_service = 0;
			$note_log = false;
			foreach ( $order_items as $key => $value ) {
				if( wc_get_product($value->get_product_id())->is_type( 'service' ) ){
					$count_item_service++;
				}
			}
			$count_item = count($order_items) - $count_item_service;
			foreach ( $order_items as $key => $value ) {
				$_status = wc_get_order_item_meta($key , '_item_status');

				if($_status == 'collection_point') {
					$completed++;
					wc_update_order_item_meta($item_id , '_item_date_out' , date("d/m/Y H:i a" , strtotime("now") + 8*3600 ) );
				}
				if($_status == 'order_received') {
					$new++;
				}
				if($_status == 'collected') {
					$collected++;
					$completed++;
				}
				if($_status == 'cancelled') {
					$cancelled++;
				}
				$order_no++;
				if($item_id == $key) {
					$item_index = $order_no;
					$item = $value;
				}
			}
			if($new == $count_item) {
				update_post_meta( $order_id , '_order_status', 'New' );
				// update status order WC
				wp_update_post(array(
				    'ID'    =>  $order_id,
				    'post_status'   =>  'wc-pending'
				));
				$_order_status = 'New';
				$_order_status_wc = 'pending';
				$ongoing = false;
				if($_order_status_df && $_order_status_df != 'New') {
					$note_log = true;
				}
			}
			if( ($completed == $count_item || ($cancelled > 0 && $completed > 0 && $completed + $cancelled == $count_item) ) && $collected != $completed ) {
				update_post_meta( $order_id , '_order_status', 'Completed' );
				update_post_meta( $order_id , '_order_time_out', date("d/m/Y H:i a" , strtotime("now") + 8*3600 ) );
				$_method = $order->get_shipping_method();
				if($_order_status_df && $_order_status_df != 'Completed') {
					if($_method=='Self-collection') {
					    send_botaksign_email($order_id , 'ORDER COMPLETED', 'B2.php');
					} elseif ($_method=='Delivery') {
						send_botaksign_email($order_id , 'ORDER COMPLETED', 'A2.php');
					} else {
					    send_botaksign_email($order_id , 'ORDER COMPLETED', 'A2.php');
					}
					$note_log = true;
				}
				$_order_status = 'Completed';
				$ongoing = false;
			}
			if($collected == $count_item || ($cancelled > 0 && $collected > 0 && $collected + $cancelled == $count_item) ) {
				update_post_meta( $order_id , '_order_status', 'Collected' );
				// update status order WC
				wp_update_post(array(
				    'ID'    =>  $order_id,
				    'post_status'   =>  'wc-completed'
				));
				$_order_status = 'Collected';
				$_order_status_wc = 'completed';
				$ongoing = false;
				if($_order_status_df && $_order_status_df != 'Collected') {
					$note_log = true;
				}
			}
			if($cancelled == $count_item) {
				update_post_meta( $order_id , '_order_status', 'Cancelled' );
				// update status order WC
				wp_update_post(array(
				    'ID'    =>  $order_id,
				    'post_status'   =>  'wc-cancelled'
				));
				update_post_meta( $order_id , '_order_time_out', date("d/m/Y H:i a" , strtotime("now") + 8*3600 ) );
				update_post_meta($order->get_id() , '_payment_status' , 'cancelled');
				$payment_status = 'cancelled';
				$_order_status = 'Cancelled';
				$_order_status_wc = 'cancelled';
				$ongoing = false;
				if($_order_status_df && $_order_status_df != 'Cancelled') {
					$note_log = true;
					send_botaksign_email($order_id , 'ORDER CANCELLED', 'F1.php', '', null, true);
				}
			}
			if($ongoing) {
				update_post_meta( $order_id , '_order_status', 'Ongoing' );
				// update status order WC
				wp_update_post(array(
				    'ID'    =>  $order_id,
				    'post_status'   =>  'wc-processing'
				));
				$_order_status = 'Ongoing';
				$_order_status_wc = 'processing';
				if($_order_status_df && $_order_status_df != 'Ongoing') {
					$note_log = true;
				}
			}
			if($note_log ) {
				$this->add_order_notes($request , $_order_status ,  'update_status_order');
				if($_order_status_wc != '') {
					$this->add_order_notes($request , 'WC - '.$_order_status_wc ,  'update_status_order');
				}
			}

			if($item) {
				$opt_status = $status;
		        $nbu_item_key = wc_get_order_item_meta($item_id, '_nbu');
		        $nbd_item_key = wc_get_order_item_meta($item_id, '_nbd');
		        $download = '';
		        if( $nbu_item_key || $nbd_item_key ) { 
		        	$files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
		        	foreach($files as $key => $file) { 
		        		$count_img_design++;
		           		if($key > 0) { 
		           			$download = NBDESIGNER_UPLOAD_URL.'/'.basename($file);               	
		           		}
		           	}
		        }
				$production_time = v3_get_production_time_item($item ,$order, true);
				$time_completed_item = wc_get_order_item_meta($item_id , '_item_time_completed');
				if(!isset($time_completed_item) || $time_completed_item == '') {
					$time_completed_item = date( 'd/m/Y H:i a' , strtotime( v3_get_time_completed_item($production_time ,$order)['production_datetime_completed'] ) );
				}
				$roles = get_userdata($user_id)->roles;
				if(in_array('specialist', $roles) && $_status != 'order_received' && $_status != 'cancelled' ) {
			        unset($user_can['cancelled']);
			    }
				$items['item_id'] = $item_id;
				$items['download'] = $download;
				$items['order_id'] = $order_id;
				$items['id'] = $item->get_product_id();
				$items['name'] = $item->get_name();
				$items['qty'] = $item->get_quantity();
				$items['status'] = $opt_status;
				$items['production_time'] = v3_get_production_time_item($item ,$order, false);
				$items['date_completed'] = $time_completed_item;
				$items['order_status'] = get_post_meta( $order_id , '_order_status' , true);
				$items['date_time_out'] = get_post_meta( $order_id , '_order_time_out' , true);
				$items['payment_status'] = $payment_status;
				$items['user_can'] = $user_can[$opt_status] == 1 ? 'edit' : 'view';
			}

			// click On - hold

			if( isset($update_est) && $request['update_est'] == 'update') {
				$date_now = date("H:i Y/m/d" , strtotime("now") + 8*3600 );
				
				//update EST order
				// $order_production_time = v3_get_production_time_order($order);
				// $order_est_completion = v3_recalc_time_completed($date_now , $order_production_time , $order)['production_datetime_completed'];
				// 
				// 

				// update EST item
				if($item) {
					$item_production_time = v3_get_production_time_item($item ,$order, true);
					$item_est_completion = v3_recalc_time_completed($date_now , $item_production_time , $order)['production_datetime_completed'];
					wc_update_order_item_meta($item_id , '_item_time_completed' , date('d/m/Y H:i a' , strtotime($item_est_completion)) );
					$items['date_completed'] = date('d/m/Y H:i a' , strtotime($item_est_completion));
					if( strtotime($item_est_completion) > strtotime(show_est_completion($order)['production_datetime_completed']) ) {
						update_post_meta( $order->get_id() , '_order_time_completed', date('d/m/Y H:i a' , strtotime($item_est_completion)) );
						update_post_meta( $order->get_id() , '_order_time_completed_str', strtotime($item_est_completion) );
						$items['order_date_completed'] = date('d/m/Y H:i a' , strtotime($item_est_completion));
					} else {
						$items['order_date_completed'] = get_post_meta( $order->get_id() , '_order_time_completed', true );
					}
					if( ( strtotime($item_est_completion) - strtotime("now") - 8*3600 )/3600 <= 2 && ( $opt_status != 'collection_point' && $opt_status != 'collected' )  ) {
						$items['check_expiring'] = 'expiring';
					}
				}
				update_post_meta($order_id , 'order_on_hold' , '');
				wc_update_order_item_meta($item_id , '_item_on_hold' , '');
			}

			// update log
			$list_status = unserialize(get_option('custom_status_order'));
			if(!isset($list_status)) {
				$list_status = array(
			        'order_received'		=> 'Order Received',
			        'processing'			=> 'Processing',
			        'artwork_amendment'		=> 'Artwork Amendment',
			        'collection_point'		=> 'Collection Point',
			        'collected'				=> 'Collected',
			        'cancelled'				=> 'Cancelled',
			    );
			}
			$status_name = $list_status[$opt_status];
			if($status_name != '' && $item) {
				$this->add_order_notes($request , $status_name ,  $item_index.' - '.$items['name']);
			}
			//end
		}
		$items['payment_status'] = $payment_status;
		$response = rest_ensure_response( $items);
		return $response;
	}
	public function search($request) {
		global $wpdb;
		$specialist = $request['specialist'] ? json_decode($request['specialist']) : '';
		$user_id = $request['user_id'] ? (int) $request['user_id'] : '';
		$posts_per_page = $request['per_page'] ? (int) $request['per_page'] : 20;
		$page = $request['page'] ? (int) $request['page'] : 1;
		$offset = ($page * $posts_per_page) - $posts_per_page;
		$id = $request['id'] ? $request['id'] : '';
		$status = $request['status'] ? $request['status'] : '';
		$date_in = $request['date_in'] ? $request['date_in'] : '';
		$date_out = $request['date_out'] ? $request['date_out'] : '';
		$date_completed = $request['date_completed'] ? $request['date_completed'] : '';
		$name = $request['name'] ? $request['name'] : '';
		$company = $request['company'] ? $request['company'] : '';
		$payment = $request['payment'] ? $request['payment'] : '';
		$payment_status = $request['payment_status'] ? $request['payment_status'] : '';
		$delivery = $request['delivery'] ? $request['delivery'] : '';
		$completion = $request['completion'] ? $request['completion'] : '';

		$date_in_y = date_parse($date_in)['year'];
		$date_in_m = date_parse($date_in)['month'];
		$date_in_d = date_parse($date_in)['day'];

		$date_out_y = date_parse($date_out)['year'];
		$date_out_m = date_parse($date_out)['month'];
		$date_out_d = date_parse($date_out)['day'];
		$date_out_str = $date_out_d.'/'.$date_out_m.'/'.$date_out_y;

		$query_first = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id )";
		$query_last = " AND wp_posts.post_type = 'shop_order' AND ((wp_posts.post_status = 'wc-pending' OR wp_posts.post_status = 'wc-processing' OR wp_posts.post_status = 'wc-on-hold' OR wp_posts.post_status = 'wc-completed' OR wp_posts.post_status = 'wc-cancelled' OR wp_posts.post_status = 'wc-refunded' OR wp_posts.post_status = 'wc-failed'))";
		// if($specialist) {
		// 	$check_query = false;
		// 	if(is_array($specialist)) {
		// 		$query_user = "SELECT user_id FROM wp_usermeta WHERE (";
		// 		foreach ($specialist as $key => $value) {
		// 			$specialist_id = $value->id;
		// 			$query_user .= "(wp_usermeta.meta_key='specialist' AND wp_usermeta.meta_value =${specialist_id})";
		// 			if(count($specialist) >0 && $key != count($specialist) - 1) {
		// 				$query_user .= " OR ";
		// 			}
		// 			$check_query = true;
		// 		}
		// 		$query_user .= " )";
		// 	}
		// 	if($check_query) {
		// 		$list_user_id = $wpdb->get_results($query_user);
		// 		foreach ($specialist as $key => $value) {
		// 			if( !array_search($value->id, array_column($list_user_id, 'user_id')) ) {
		// 				$list_user_id[] = array(
		// 					'user_id' => $value->id
		// 				);
		// 			} 
		// 		}
		// 	}
		// 	if(is_array($list_user_id) ) {
		// 		$query_last .= " AND ( ";
		// 		foreach ($list_user_id as $key => $value) {
		// 			$user_id = $value->user_id;
		// 			$query_last .= "( wp_postmeta.meta_key = '_customer_user' AND wp_postmeta.meta_value = '${user_id}' )";
		// 			if(count($list_user_id)>0 && $key != count($list_user_id) - 1) {
		// 				$query_last .= " OR ";
		// 			}
		// 		}
		// 		$query_last .= " )";
		// 	}
		// }
		if($specialist) {
			if(is_array($specialist)) {
				$query_first .= " INNER JOIN wp_postmeta AS mts ON ( wp_posts.ID = mts.post_id )";
				$query_last .= " AND ( mts.meta_key = '_specialist_id' AND ( ";
				foreach ($specialist as $key => $value) {
					$specialist_id = $value->id;
					$query_last .= "mts.meta_value = '${specialist_id}'";
					if(count($specialist) >0 && $key < count($specialist) - 1) {
						$query_last .= " OR ";
					}
				}
				$query_last .= " ))";
			}
		}
		if($id != '') {
			$query_last .= " AND wp_posts.ID LIKE '%${id}%'";
		}
		//$delivery = 'Self-collection';
		if($status != '') {
			$query_first .= " INNER JOIN wp_postmeta AS mt1 ON ( wp_posts.ID = mt1.post_id )";
			$query_last .= " AND ( ( mt1.meta_key = '_order_status' AND mt1.meta_value = '${status}' ) )";
		}
		if($date_in != '') {
			$query_last .= " AND ( ( YEAR( wp_posts.post_date ) = ${date_in_y} AND MONTH( wp_posts.post_date ) = ${date_in_m} AND DAYOFMONTH( wp_posts.post_date ) = ${date_in_d} ) )";
		}
		if($date_out != '') {
			$datas = array();
			$datas['orders'] = array();
			$datas['total'] = 0;
			$datas['shipping_method'] = $this->get_shipping_method();
			$response = rest_ensure_response( $datas );
			return $response;

			$query_first .= " INNER JOIN wp_postmeta AS mt2 ON ( wp_posts.ID = mt2.post_id )";
			$query_last .= " AND ( ( mt2.meta_key = '_order_time_out' AND mt2.meta_value LIKE '%".$date_out_str."%') )";
		}
		if($company != '') {
			$company = esc_sql($company);
			$query_first .= " INNER JOIN wp_postmeta AS mt3 ON ( wp_posts.ID = mt3.post_id )";
			$query_last .= " AND ( ( mt3.meta_key = '_billing_company' AND mt3.meta_value LIKE '%${company}%' ) )";
		}
		if($payment != '') {
			$query_first .= " INNER JOIN wp_postmeta AS mt4 ON ( wp_posts.ID = mt4.post_id )";
			$query_last .= " AND ( ( mt4.meta_key = '_payment_method' AND mt4.meta_value = '${payment}' ) )";
		}
		if($completion != '') {
			$time_from = strtotime('-10 now');
			$time_to = strtotime('-8 now');
			// $query_first .= " INNER JOIN wp_postmeta AS mt5 ON ( wp_posts.ID = mt5.post_id )";
			// $query_last .= "AND ( ( mt5.meta_key = '_order_time_completed_str' AND mt5.meta_value BETWEEN '${time_from}' AND '${time_to}' ) )";
			$query_first .= " INNER JOIN wp_postmeta AS mt5 ON ( wp_posts.ID = mt5.post_id )";
			$query_last .= "AND ( ( mt5.meta_key = '_order_time_completed_str' AND mt5.meta_value BETWEEN '0' AND '${time_from}' ) ) AND ( ( wp_postmeta.meta_key = '_order_status' AND wp_postmeta.meta_value != 'Completed' ) ) AND ( ( wp_postmeta.meta_key = '_order_status' AND wp_postmeta.meta_value != 'Collected' ) ) ";
		}
		if($name != '') {
			//$query_last .= " AND ( ( ( ( wp_postmeta.meta_key = '_billing_first_name' AND wp_postmeta.meta_value LIKE '%${name}%' ) OR ( wp_postmeta.meta_key = '_billing_last_name' AND wp_postmeta.meta_value LIKE '%${name}%' ) ) ) )";
			$name = esc_sql($name);
			$query_first .= " INNER JOIN wp_postmeta AS mt6 ON ( wp_posts.ID = mt6.post_id )";
			$query_last .= " INNER JOIN wp_usermeta ON (mt6.meta_key = '_customer_user' AND mt6.meta_value = wp_usermeta.user_id) WHERE ( wp_usermeta.meta_key = 'billing_first_name' AND  wp_usermeta.meta_value LIKE '%${name}%' ) OR (  wp_usermeta.meta_key = 'billing_last_name' AND  wp_usermeta.meta_value LIKE '%${name}%' )";
		}
		if($payment_status != '') {
			$query_first .= " INNER JOIN wp_postmeta AS mt7 ON ( wp_posts.ID = mt7.post_id )";
			$query_last .= " AND ( ( mt7.meta_key = '_payment_status' AND mt7.meta_value = '${payment_status}' ) )";
		}
		if($delivery != '') {
			// $method_type = '';
			// $shipping_method = array();
			// $query_method = "SELECT * FROM wp_woocommerce_shipping_zone_methods WHERE zone_id = 3 AND is_enabled = 1";
			// $query_zone_method = $wpdb->get_results($query_method);
			// if( isset($query_zone_method) ) {
			//     foreach ($query_zone_method as $key => $value) {
			//         $instance_id = $value->instance_id;
			//         $method_id   = $value->method_id;
			//         $shipping_info = get_option('woocommerce_'.$method_id.'_'.$instance_id.'_settings') ;
			//         if( $shipping_info['title'] == $delivery) {
			//         	$method_type = $method_id;
			//             $instance_id = $value->instance_id;
			//         }
			//     }
			// }
			// $_qr = "SELECT comment FROM wp_woocommerce_shipping_table_rate AS st WHERE st.instance_id = ${instance_id}";
			// $_shipping_method = $wpdb->get_results($_qr, 'ARRAY_A');
			// if($_shipping_method && $method_type == 'table_rate') {
			// 	$qrf = " INNER JOIN wp_woocommerce_order_items ON ( wp_posts.ID = wp_woocommerce_order_items.order_id )  WHERE ( ( wp_woocommerce_order_items.order_item_type = 'shipping' AND ";
			// 	$count = 0;
			// 	foreach ($_shipping_method as $key => $value) {
			// 		$delivery_method = $value['comment'];
			// 		$qrm .= "( wp_woocommerce_order_items.order_item_name LIKE '${delivery_method}' )";
			// 		$count ++;
			// 		if($count < count($_shipping_method) ) {
			// 			$qrm .= " OR ";
			// 		}
			// 	}
			// 	$qr = $qrf.$qrm." ) )";
			// } else {
			// 	$qr = " INNER JOIN wp_woocommerce_order_items ON ( wp_posts.ID = wp_woocommerce_order_items.order_id )  WHERE ( ( wp_woocommerce_order_items.order_item_type = 'shipping' AND wp_woocommerce_order_items.order_item_name LIKE '${delivery}' )) ";
			// }
			$query_last .= " INNER JOIN wp_woocommerce_order_items ON ( wp_posts.ID = wp_woocommerce_order_items.order_id )  WHERE ( ( wp_woocommerce_order_items.order_item_name LIKE '${delivery}' ) )";
			// $query_last .= $qr;
		}
		$query = $query_first . ' '.$query_last;
		$query_total = $query." GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
		$query .= " GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC LIMIT ${offset}, ${posts_per_page}";

		$total = count($wpdb->get_results($query_total));	
		$results = $wpdb->get_results($query);
		$total_order = count($results);
		foreach ($results as $key => $value) {
			$data[] = $this->get_order( $value->ID, $request );
		}
		$datas = array();
		$datas['orders'] = $data;
		$datas['total'] = $total;
		$datas['shipping_method'] = $this->get_shipping_method();
		$response = rest_ensure_response( $datas );
		return $response;
	}
	public function update_specialist_permissions_check( $request ) {
		$id = (int) $request['id'];

		if ( ! wc_rest_check_user_permissions( 'edit', $id ) ) {
			return new WP_Error( 'woocommerce_rest_cannot_edit', __( 'Sorry, you are not allowed to edit this resource.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}
	public function update_status_order_and_item($order) {

	}
	public function get_shipping_method() {
		global $wpdb;
		$zone_id = 3;
		$shipping_method = array();
		$query_method = "SELECT * FROM wp_woocommerce_shipping_zone_methods WHERE zone_id = 3 AND is_enabled = 1";
		$query_zone_method = $wpdb->get_results($query_method);
		if( isset($query_zone_method) ) {
			foreach ($query_zone_method as $key => $value) {
				$instance_id = $value->instance_id;
				$method_id 	 = $value->method_id;
				// $_text = 'woocommerce_'.$method_id.'_'.$instance_id.'_settings';
				// return $_text;
				$shipping_info = get_option('woocommerce_'.$method_id.'_'.$instance_id.'_settings') ;
				$shipping_method[] = $shipping_info['title'];
			}
		}
		return $shipping_method;
	}
	// Function Delivery Plotter
	public function get_plotter_order_detail($request) {
		$order_id = $request['id'] ? (int) $request['id'] : '';
		$order = wc_get_order($order_id);
		$data['address'] 	= $order->get_shipping_address_1().' '.$order->get_shipping_address_2();
		$data['name']		= $order->get_shipping_first_name().' '.$order->get_shipping_last_name();
		$data['phone'] 		= $order->get_billing_phone();
		$data['remarks'] 	= get_post_meta($order_id , '_remarks_order' , true);
		$response = rest_ensure_response( $data );
		return $response;
	}
	public function update_plotter_order_detail($request) {
		$order_id = isset($request['id']) ? $request['id'] : '';
		$order = wc_get_order($order_id);
		$action = isset($request['action']) ? $request['action'] : '';
		$value = isset($request['value']) ? $request['value'] : '';
		switch ($action) {
			case 'address':
				update_post_meta($order_id , '_shipping_address_1' , $value);
				update_post_meta($order_id , '_shipping_address_2' , '');
				break;
			case 'name':
				update_post_meta($order_id , '_shipping_first_name' , $value);
				update_post_meta($order_id , '_shipping_last_name' , '');
				break;
			case 'phone':
				update_post_meta($order_id , '_billing_phone' , $value);
				break;
			case 'remarks':
				update_post_meta($order_id , '_remarks_order' , $value);
				break;
			case 'tick':
				update_post_meta($order_id , '_order_tick_vue' , $value);
				break;
			case 'delete':
				update_post_meta($order_id , '_order_in_plot' , 'trash');
				break;
		}
		$response = rest_ensure_response( $order_id );
		return $response;
	}
	public function search_order_out_plotter($request) {
		global $wpdb;
		$key = isset($request['key']) ? $request['key']: '';
		$query_first = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id )";
		$query_last = " AND wp_posts.post_type = 'shop_order' AND ((wp_posts.post_status = 'wc-pending' OR wp_posts.post_status = 'wc-processing' OR wp_posts.post_status = 'wc-on-hold' OR wp_posts.post_status = 'wc-completed' OR wp_posts.post_status = 'wc-cancelled' OR wp_posts.post_status = 'wc-refunded' OR wp_posts.post_status = 'wc-failed'))";
		$query_last .= " AND wp_posts.ID LIKE '%${key}%'";
		$query_first .= " INNER JOIN wp_postmeta AS mt1 ON ( wp_posts.ID = mt1.post_id )";
		$query_last .= " AND ( ( mt1.meta_key = '_order_status' AND mt1.meta_value = 'Completed' ) )";
		// $query_first .= " INNER JOIN wp_postmeta AS mt2 ON ( wp_posts.ID = mt2.post_id )";
		// $query_last .= " AND ( ( mt2.meta_key = '_order_in_plot' AND mt2.meta_value != '1' ) )";
		$order = $wpdb->get_results($query_first.$query_last." GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC");
		$orders = array();
		foreach ($order as $key => $value) {
			if(get_post_meta($value->ID , '_order_in_plot', true) != '1') {
				$orders[] = array(
					'ID'	=>  $value->ID
				);
			}
		}
		$response = rest_ensure_response( $orders );
		return $response;
	}
	public function update_order_out_plotter($request) {
		$order_id = isset($request['order_id']) ? $request['order_id']: '';
		$day = isset($request['day']) ? $request['day']: '';

		$_date_format = $day;
		$_date_format_a = explode('-', $_date_format);
		if(strlen($_date_format_a['1']) == 1 ) {
			$_date_format_a['1'] = '0'.$_date_format_a['1']; 
		}
		if(strlen($_date_format_a['2']) == 1 ) {
			$_date_format_a['2'] = '0'.$_date_format_a['2']; 
		}
		if(strlen($_date_format_a['0']) == 1 ) {
			$_date_format_a['0'] = '0'.$_date_format_a['0']; 
		}
		$_date_format = $_date_format_a['0'].'-'. $_date_format_a['1'].'-'.$_date_format_a['2'];

		$period = isset($request['period']) ? $request['period']: '';
		update_post_meta($order_id , '_order_in_plot' , 1);
		update_post_meta($order_id , '_order_add_plot' , 1);
		update_post_meta($order_id , '_order_in_plot_completed' , $_date_format. ' ' . $period);
		$order = array( 
			'order_id' 		=> $order_id,
			'order_detail'  => $this->get_link_order_detail($order_id)
		);
		$response = rest_ensure_response( $order );
		return $response; 
	}
	public function get_delivery_plotter($request) {
		global $wpdb;
		$date 				= $request['date'] ? $request['date'] : '';
		$status_completed 	= $request['status_completed'] ? $request['status_completed'] : '';
		$period 			= $request['period'] ? $request['period'] : '';
		// $periods 			= explode('-', $date['time']);
		$str_date_a 		= strtotime($date. ' 00:00') - 3600*24;
		$str_date_c 		= strtotime($date. ' 23:59');
		$thu = strtolower(date( 'l' , strtotime($date. ' 10:00')));
    	if($thu == 'saturday') return array();
		$date_current		= explode('-', $date)[2];
		$shipping_method 	= array();
		$data 				= array();
		$period_options 	= unserialize(get_option('period_dp_options'));
		// get and set shipping meothod
		//[{"zone_id":"3","instance_id":"16","method_id":"local_pickup","method_order":"3","is_enabled":"1"},
		// {"zone_id":"3","instance_id":"20","method_id":"table_rate","method_order":"5","is_enabled":"1"}]
		$zone_id = 3;
		$query_method = "SELECT * FROM wp_woocommerce_shipping_zone_methods WHERE zone_id = 3 AND is_enabled = 1";
		$query_zone_method = $wpdb->get_results($query_method);
		if( isset($query_zone_method) ) {
			foreach ($query_zone_method as $key => $value) {
				$shipping_info = get_option('woocommerce_'. $value->method_id.'_'.$value->instance_id.'_settings') ;
				if($value->method_id == 'table_rate') {
					$instance_id 	= $value->instance_id;
					$query_rate 	= "SELECT comment FROM wp_woocommerce_shipping_table_rate WHERE wp_woocommerce_shipping_table_rate.instance_id = '${instance_id}'";
					foreach ($wpdb->get_results($query_rate) as $rate) {
						$shipping_method[$rate->comment] = array(
							'value' => 2,
							'name'	=> $shipping_info['title'],
						);
					}
				} else {
					$shipping_method[$shipping_info['title']] = 1;
				}
			}
			update_option('_get_title_shipping_method' , serialize($shipping_method));

		}
		// plotter option [{"shipping_method":"Self-collection","date":"none","period":"Other"},{"shipping_method":"Delivery1","date":"same_day","period":"09:00-23:11"}]
		$query_first = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id )";
		$query_last = " AND wp_posts.post_type = 'shop_order' AND ((wp_posts.post_status = 'wc-pending' OR wp_posts.post_status = 'wc-processing' OR wp_posts.post_status = 'wc-on-hold' OR wp_posts.post_status = 'wc-completed' OR wp_posts.post_status = 'wc-cancelled' OR wp_posts.post_status = 'wc-refunded' OR wp_posts.post_status = 'wc-failed')) ";
		// $query_first .= " INNER JOIN wp_postmeta AS mt1 ON ( wp_posts.ID = mt1.post_id ) ";
		// $query_first .= " AND ( ( mt1.meta_key = '_order_in_plot' AND mt1.meta_value != '0' ) ) ";
		$plotting_options = unserialize(get_option('plotting_options'));
		$plotting_options_method = array();
		if($plotting_options) {
			foreach ($plotting_options as $plotting_index => $plotting_option) {
				if( $plotting_option['date'] == 'none' ) {
					$none_options[] = array(
						'shipping_method_title'	=> $plotting_option['shipping_method']['title'],
						'shipping_method_key'	=> $plotting_option['shipping_method']['key'],
						'period'			=> $plotting_option['period_calc'],
						'period_dp'			=> $plotting_option['period_dp'],
					);
				}
				if( $plotting_option['date'] == 'same_day' ) {
					$same_day_options[] = array(
						'shipping_method_title'	=> $plotting_option['shipping_method']['title'],
						'shipping_method_key'	=> $plotting_option['shipping_method']['key'],
						'period'			=> $plotting_option['period_calc'],
						'period_dp'			=> $plotting_option['period_dp'],
					);
				}
				if( $plotting_option['date'] == 'next_day' ) {
					$next_day_options[] = array(
						'shipping_method_title'	=> $plotting_option['shipping_method']['title'],
						'shipping_method_key'	=> $plotting_option['shipping_method']['key'],
						'period'			=> $plotting_option['period_calc'],
						'period_dp'			=> $plotting_option['period_dp'],
					);
				}
				$plotting_options_method[$plotting_option['shipping_method']['key']] = array(
					'date'				=>	$plotting_option['date'],
					'period'			=> $plotting_option['period_calc'],
					'period_dp'			=> $plotting_option['period_dp'],
				);
			}
		}
		// $plotting_options_method = {"Self-collection":{"date":"none","period":""},"Express":{"date":"same_day","period":"09:00-23:11"},"Delivery1":{"date":"next_day","period":"Other"}}
		if($date) {
			// GET Order same (orders day seleced)
			$str_date_f = strtotime($date. ' 00:00');
			$str_date_t = strtotime($date. ' 23:59');
			$query_sd_first = $query_first;
			$query_sd_last = $query_last;
			$meta = 'mts';
			$meta1 = 'mts1';
			$meta2 = 'mts2';
			$query_sd_first .= " INNER JOIN wp_postmeta AS ".$meta." ON ( wp_posts.ID = ".$meta.".post_id ) ";
			$query_sd_first .= " INNER JOIN wp_postmeta AS ".$meta2." ON ( wp_posts.ID = ".$meta2.".post_id ) ";
			$query_sd_last .= " AND ( ( ".$meta.".meta_key = '_order_time_completed_str' AND ".$meta.".meta_value BETWEEN '${str_date_f}' AND '${str_date_t}') ) ";
			$query_sd_last .= " AND ( ( ".$meta2.".meta_key = '_order_status' AND (( ".$meta2.".meta_value != 'Cancelled' AND ".$meta2.".meta_value != 'Pending' )) ) ) ";
			$query_sd = $query_sd_first.$query_sd_last." GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
			$order_sd = $wpdb->get_results($query_sd );
			// GET Order next day (orders before day seleced)
			$str_date_f = strtotime($date. ' 00:00') - 24*3600;
			$str_date_t = strtotime($date. ' 23:59') - 24*3600;
			if($thu == 'monday') {
	            $str_date_f = strtotime($date. ' 00:00') - 72*3600;
	        }
			$_meta = 'mtn';
			$_meta1 = 'mtn1';
			$_meta2 = 'mtn2';
			$query_nd_first = $query_first;
			$query_nd_first .= " INNER JOIN wp_postmeta AS ".$_meta." ON ( wp_posts.ID = ".$_meta.".post_id ) ";
			$query_nd_first .= " INNER JOIN wp_postmeta AS ".$_meta2." ON ( wp_posts.ID = ".$_meta2.".post_id ) ";
			$query_nd_last = $query_last;
			$query_nd_last .= " AND ( ( ".$_meta.".meta_key = '_order_time_completed_str' AND ".$_meta.".meta_value BETWEEN '${str_date_f}' AND '${str_date_t}') ) ";
			$query_nd_last .= " AND ( ( ".$_meta2.".meta_key = '_order_status' AND (( ".$_meta2.".meta_value != 'Cancelled' AND ".$_meta2.".meta_value != 'Pending' ))  )) ";
			$query_nd = $query_nd_first.$query_nd_last." GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
			$order_nd = $wpdb->get_results($query_nd );
		}
		
		$final_orders = array();
		if(isset($order_sd )) {
			foreach ($order_sd as $key => $value) {
				$_periods = array();
                $_periods_clone = array();
				$order_id = $value->ID;
				$order_status = get_post_meta($order_id , '_order_status' , true);
				$order = wc_get_order($order_id);
				$shipping_method = v3_get_shipping_method_order($order);
				$shipping_method_clone = '';
				if($shipping_method == 'Delivery') {
					$shipping_method_clone = 'delivery_clone';
				}
				$check_orther = false;
				$in_plotter = false;
				$period_orther = '';
				if($plotting_options_method[$shipping_method]['date'] == 'same_day' || $plotting_options_method[$shipping_method_clone]['date'] == 'same_day') {
					if($plotting_options_method[$shipping_method]['date'] == 'same_day') {
						$_periods = explode('-' , $plotting_options_method[$shipping_method]['period'] );
					}
					if($shipping_method == 'Delivery' && $plotting_options_method[$shipping_method_clone]['date'] == 'same_day' ) {
						$_periods_clone = explode('-' , $plotting_options_method[$shipping_method_clone]['period'] );
					}
					if( count($_periods) == 2 ) {
						$check_orther = false;
						if(v3_time_to_minutes($_periods[1]) > 0 && v3_time_to_minutes($_periods[0]) > 0) {
							if( v3_time_to_minutes($_periods[1]) > v3_time_to_minutes($_periods[0]) ) {
								$time_from = $date . ' ' . $_periods[0];
								$time_to = $date . ' ' . $_periods[1];
							} else {
								$time_from = $date . ' ' . $_periods[1];
								$time_to = $date . ' ' . $_periods[0];
							}
							$time_from_str = strtotime($time_from);
							$time_to_str = strtotime($time_to);
							$order_time_completed_str = get_post_meta($order_id , '_order_time_completed_str' , true);
							$check_add_plot = get_post_meta($order_id , '_order_add_plot' , true);
							$check_in_plot = get_post_meta($order_id , '_order_in_plot' , true);
							if($check_add_plot != 1 && $check_in_plot != 'trash' ) {
								if($order_status == 'Collected') {
									update_post_meta($order_id , '_order_tick_vue' , 'ok');
								}
								if( $order_time_completed_str >= $time_from_str && $order_time_completed_str < $time_to_str) {
									$final_orders[$plotting_options_method[$shipping_method]['period_dp']][]	= array( 
										'order_id' 		=> $order_id,
										'order_detail'  => $this->get_link_order_detail($order_id),
										'order_tick'	=> get_post_meta($order_id , '_order_tick_vue' , true),
										'order_status'	=> $order_status
									);
									$in_plotter = true;
									$check_orther = false;
									update_post_meta($order_id , '_order_not_in_order' , 1);
								}
								else {
									$check_orther = true;
								}
								update_post_meta($order_id , '_order_in_plot' , 1);
							}
						}
					} 
					if(isset($_periods_clone) && count($_periods_clone) == 2 ) {
						if(v3_time_to_minutes($_periods_clone[1]) > 0 && v3_time_to_minutes($_periods_clone[0]) > 0) {
							if( v3_time_to_minutes($_periods_clone[1]) > v3_time_to_minutes($_periods_clone[0]) ) {
								$time_from = $date . ' ' . $_periods_clone[0];
								$time_to = $date . ' ' . $_periods_clone[1];
							} else {
								$time_from = $date . ' ' . $_periods_clone[1];
								$time_to = $date . ' ' . $_periods_clone[0];
							}
							$time_from_str = strtotime($time_from);
							$time_to_str = strtotime($time_to);
							$order_time_completed_str = get_post_meta($order_id , '_order_time_completed_str' , true);
							$check_add_plot = get_post_meta($order_id , '_order_add_plot' , true);
							$check_in_plot = get_post_meta($order_id , '_order_in_plot' , true);
							if($check_add_plot != 1 && $check_in_plot != 'trash' ) { 
								if($order_status == 'Collected') {
									update_post_meta($order_id , '_order_tick_vue' , 'ok');
								}
								if( $order_time_completed_str >= $time_from_str && $order_time_completed_str <= $time_to_str) {
									$final_orders[$plotting_options_method[$shipping_method_clone]['period_dp']][]	= array( 
										'order_id' 		=> $order_id,
										'order_detail'  => $this->get_link_order_detail($order_id),
										'order_tick'	=> get_post_meta($order_id , '_order_tick_vue' , true),
										'order_status'	=> $order_status
									);
									$in_plotter = true;
									$check_orther = false;
									update_post_meta($order_id , '_order_not_in_order' , 1);
								}
								else {
									$check_orther = true;
								}
								update_post_meta($order_id , '_order_in_plot' , 1);
							}
						}
					}
					$not_in_orther = get_post_meta($order_id , '_order_not_in_order' , true);
					if($check_orther && !$in_plotter && $not_in_orther != '1' ) {
						$final_orders['other'][]	= array( 
							'order_id' 		=> $order_id,
							'order_detail'  => $this->get_link_order_detail($order_id),
							'order_tick'	=> get_post_meta($order_id , '_order_tick_vue' , true),
							'order_status'	=> $order_status
						);
					}
				}

			}
		}
		if(isset($order_nd)) {
			foreach ($order_nd as $key => $value) {
				$_periods = array();
                $_periods_clone = array();
				$order_id = $value->ID;
				$order_status = get_post_meta($order_id , '_order_status' , true);
				$order = wc_get_order($order_id);
				$shipping_method = v3_get_shipping_method_order($order);
				$shipping_method_clone = '';
				if($shipping_method == 'Delivery') {
					$shipping_method_clone = 'delivery_clone';
				}
				$check_orther = false;
				$in_plotter = false;
				$period_orther = '';
				if($plotting_options_method[$shipping_method]['date'] == 'next_day' || $plotting_options_method[$shipping_method_clone]['date'] == 'next_day') {
					if($plotting_options_method[$shipping_method]['date'] == 'next_day') {
						$_periods = explode('-' , $plotting_options_method[$shipping_method]['period'] );
					}
					if($shipping_method == 'Delivery' && $plotting_options_method[$shipping_method_clone]['date'] == 'next_day' ) {
						$_periods_clone = explode('-' , $plotting_options_method[$shipping_method_clone]['period'] );
					}

					if( count($_periods) == 2 ) {
						$check_orther = false;
						if(v3_time_to_minutes($_periods[1]) > 0 && v3_time_to_minutes($_periods[0]) > 0) {
							if( v3_time_to_minutes($_periods[1]) > v3_time_to_minutes($_periods[0]) ) {
								$time_from = $date . ' ' . $_periods[0];
								$time_to = $date . ' ' . $_periods[1];
							} else {
								$time_from = $date . ' ' . $_periods[1];
								$time_to = $date . ' ' . $_periods[0];
							}
							$time_from_str = strtotime($time_from) - 24*3600;
							if($thu == 'monday') {
					            $time_from_str = strtotime($time_from) - 72*3600;
					        }
							$time_to_str = strtotime($time_to) - 24*3600;
							$order_time_completed_str = get_post_meta($order_id , '_order_time_completed_str' , true);
							$check_add_plot = get_post_meta($order_id , '_order_add_plot' , true);
							$check_in_plot = get_post_meta($order_id , '_order_in_plot' , true);
							if($check_add_plot != 1 && $check_in_plot != 'trash' ) {
								if($order_status == 'Collected') {
									update_post_meta($order_id , '_order_tick_vue' , 'ok');
								}
								if( $order_time_completed_str >= $time_from_str && $order_time_completed_str <= $time_to_str) {
									$final_orders[$plotting_options_method[$shipping_method]['period_dp']][]	= array( 
										'order_id' 		=> $order_id,
										'order_detail'  => $this->get_link_order_detail($order_id),
										'order_tick'	=> get_post_meta($order_id , '_order_tick_vue' , true),
										'order_status'	=> $order_status
									);
									$in_plotter = true;
									$check_orther = false;
									update_post_meta($order_id , '_order_not_in_order' , 1);
								}
								else {
									$check_orther = true;
								}
								update_post_meta($order_id , '_order_in_plot' , 1);
							}
						}
					} 
					if(isset($_periods_clone) && count($_periods_clone) == 2 ) {
						if(v3_time_to_minutes($_periods_clone[1]) > 0 && v3_time_to_minutes($_periods_clone[0]) > 0) {
							if( v3_time_to_minutes($_periods_clone[1]) > v3_time_to_minutes($_periods_clone[0]) ) {
								$time_from = $date . ' ' . $_periods_clone[0];
								$time_to = $date . ' ' . $_periods_clone[1];
							} else {
								$time_from = $date . ' ' . $_periods_clone[1];
								$time_to = $date . ' ' . $_periods_clone[0];
							}
							$time_from_str = strtotime($time_from) - 24*3600;
							if($thu == 'monday') {
					            $time_from_str = strtotime($time_from) - 72*3600;
					        }
							$time_to_str = strtotime($time_to) - 24*3600;
							$order_time_completed_str = get_post_meta($order_id , '_order_time_completed_str' , true);
							$check_add_plot = get_post_meta($order_id , '_order_add_plot' , true);
							$check_in_plot = get_post_meta($order_id , '_order_in_plot' , true);
							if($check_add_plot != 1 && $check_in_plot != 'trash' ) {
								if($order_status == 'Collected') {
									update_post_meta($order_id , '_order_tick_vue' , 'ok');
								}
								if( $order_time_completed_str >= $time_from_str && $order_time_completed_str < $time_to_str) {
									$final_orders[$plotting_options_method[$shipping_method_clone]['period_dp']][]	= array( 
										'order_id' 		=> $order_id,
										'order_detail'  => $this->get_link_order_detail($order_id),
										'order_tick'	=> get_post_meta($order_id , '_order_tick_vue' , true),
										'order_status'	=> $order_status
									);
									$in_plotter = true;
									$check_orther = false;
									update_post_meta($order_id , '_order_not_in_order' , 1);
								}
								else {
									$check_orther = true;
								}
								update_post_meta($order_id , '_order_in_plot' , 1);
							}
						}
					}
					if($shipping_method == 'Delivery' && $plotting_options_method[$shipping_method_clone]['date'] == 'next_day' ) {
						$check_orther = false;
					}
					$not_in_orther = get_post_meta($order_id , '_order_not_in_order' , true);
					
					if($check_orther && !$in_plotter && $not_in_orther != '1') {
						$final_orders['other'][]	= array( 
							'order_id' 		=> $order_id,
							'order_detail'  => $this->get_link_order_detail($order_id),
							'order_tick'	=> get_post_meta($order_id , '_order_tick_vue' , true),
							'order_status'	=> $order_status
						);
					}
				}
			}
		}
		if($period_options) {
			foreach ($period_options as $key => $value) {
				$_date_format = $date;
				$_date_format_a = explode('-', $_date_format);
				if(strlen($_date_format_a['1']) == 1 ) {
					$_date_format_a['1'] = '0'.$_date_format_a['1']; 
				}
				if(strlen($_date_format_a['2']) == 1 ) {
					$_date_format_a['2'] = '0'.$_date_format_a['2']; 
				}
				$_date_format = $_date_format_a['0'].'-'. $_date_format_a['1'].'-'.$_date_format_a['2'];
				$date_add = $_date_format. ' '.$value;
				$query_date_add = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id )  INNER JOIN wp_postmeta AS mta1 ON ( wp_posts.ID = mta1.post_id )  INNER JOIN wp_postmeta AS mta2 ON ( wp_posts.ID = mta2.post_id ) INNER JOIN wp_postmeta AS mta3 ON ( wp_posts.ID = mta3.post_id ) AND wp_posts.post_type = 'shop_order' AND ((wp_posts.post_status = 'wc-pending' OR wp_posts.post_status = 'wc-processing' OR wp_posts.post_status = 'wc-on-hold' OR wp_posts.post_status = 'wc-completed' OR wp_posts.post_status = 'wc-cancelled' OR wp_posts.post_status = 'wc-refunded' OR wp_posts.post_status = 'wc-failed'))  AND ( ( mta1.meta_key = '_order_in_plot_completed' AND mta1.meta_value = '${date_add}' ) ) AND ( ( mta2.meta_key = '_order_add_plot' AND mta2.meta_value = '1' ) )  AND ( ( mta3.meta_key = '_order_status' AND ( mta3.meta_value = 'Completed' OR mta3.meta_value = 'Collected' OR mta3.meta_value = 'Ongoing' OR mta3.meta_value = 'New' OR mta3.meta_value = 'Pending' ) ) ) GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
				$order_add = $wpdb->get_results($query_date_add );
				foreach ($order_add as $_value) {
					if($_value->ID) {
						$check_in_plot = get_post_meta($_value->ID , '_order_in_plot' , true);
						if($check_in_plot != 'trash' ) {
							$final_orders[$value][] = array( 
								'order_id' 		=> $_value->ID,
								'order_detail'  => $this->get_link_order_detail($_value->ID),
								'order_tick'	=> get_post_meta($_value->ID , '_order_tick_vue' , true),
								'order_status'	=> get_post_meta($_value->ID , '_order_status' , true),
							);
						}
					}
				}
				
			}

			// other
			$_date_format = $date;
			$_date_format_a = explode('-', $_date_format);
			if(strlen($_date_format_a['1']) == 1 ) {
				$_date_format_a['1'] = '0'.$_date_format_a['1']; 
			}
			if(strlen($_date_format_a['2']) == 1 ) {
				$_date_format_a['2'] = '0'.$_date_format_a['2']; 
			}
			$_date_format = $_date_format_a['0'].'-'. $_date_format_a['1'].'-'.$_date_format_a['2'];
			$query_date_add = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id )  INNER JOIN wp_postmeta AS mta1 ON ( wp_posts.ID = mta1.post_id )  INNER JOIN wp_postmeta AS mta2 ON ( wp_posts.ID = mta2.post_id ) INNER JOIN wp_postmeta AS mta3 ON ( wp_posts.ID = mta3.post_id ) AND wp_posts.post_type = 'shop_order' AND ((wp_posts.post_status = 'wc-pending' OR wp_posts.post_status = 'wc-processing' OR wp_posts.post_status = 'wc-on-hold' OR wp_posts.post_status = 'wc-completed' OR wp_posts.post_status = 'wc-cancelled' OR wp_posts.post_status = 'wc-refunded' OR wp_posts.post_status = 'wc-failed'))  AND ( ( mta1.meta_key = '_order_in_plot_completed' AND mta1.meta_value = '${_date_format}' ) ) AND ( ( mta2.meta_key = '_order_add_plot' AND mta2.meta_value = '1' ) )  AND ( ( mta3.meta_key = '_order_status' AND ( mta3.meta_value = 'Completed' OR mta3.meta_value = 'Collected' OR mta3.meta_value = 'Ongoing' OR mta3.meta_value = 'New' OR mta3.meta_value = 'Pending' ) ) ) GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC";
			$order_add = $wpdb->get_results($query_date_add );
			foreach ($order_add as $_value) {
				if($_value->ID) {
					$check_in_plot = get_post_meta($_value->ID , '_order_in_plot' , true);
					if($check_in_plot != 'trash' ) {
						$final_orders['other'][] = array( 
							'order_id' 		=> $_value->ID,
							'order_detail'  => $this->get_link_order_detail($_value->ID),
							'order_tick'	=> get_post_meta($_value->ID , '_order_tick_vue' , true),
							'order_status'	=> get_post_meta($_value->ID , '_order_status' , true),
						);
					}
				}
			}
		}
		$response = rest_ensure_response($final_orders);
		return $response;
	}
	public function get_list_link_order_items($request) {
		$order_id = isset($request['id']) ? $request['id']: '';
		if($order_id) {
			$list_link = v3_get_link_download_order($order_id);
		}
		$response = rest_ensure_response($list_link);
		return $response;
	}
	public function get_list_link_order_items_test($request) {
		$order_id = isset($request['id']) ? $request['id']: '';
		$zip_files = '';
		$zip_files = add_query_arg(array('download-all' => 'true', 'order_id' => $order_id), admin_url('admin.php?page=nbdesigner_detail_order'));
		if($order_id) {
			$list_link = v3_get_link_download_order($order_id);
		}
		$results['list'] = $list_link;
		$results['zip_files'] = $zip_files;
		$response = rest_ensure_response($results);
		return $response;
	}
	public function get_link_order_detail($order_id) {
		global $botakit;
	    $link_down = '';
	    if ($order_id) {
	        $html = v3_generate_order_detail_pdf($order_id);
	        $botakit->_content = $html;
	        $filename = 'order-' . $order_id . '.pdf';
	        $botakit->generate_pdf_template($filename);
	        $pdf_path = $botakit->_file_to_save . '/' . $filename;
	        $link_down = convertLinkDesign($pdf_path);
	    }
	    return $link_down;
	}
	public function get_period_time() {
		$period_options = unserialize(get_option('period_dp_options'));
		$time_period = array();
		$time_periods = array();
		foreach ($period_options as $key => $period) {
			if( strpos( $period , '-')) {
	            $period_from = explode('-', $period)[0];
	            $period_to = explode('-', $period)[1];
	            $period_label = v3_convert_time($period_from).' - '.v3_convert_time($period_to);
	        } else {
	        	$period_label = $period;
	        }
	        $time_period['period_label'] = $period_label;
	        $time_period['period_value'] = $period;
	        $time_periods[] = $time_period;
		}
		$response = rest_ensure_response($time_periods);
		return $response;
	}
	public function update_delivery_plotter($request) {

	}	
}

add_filter( 'woocommerce_rest_api_get_rest_namespaces', 'woo_custom_api' );

function woo_custom_api( $controllers ) {
	$controllers['wc/v3']['custom'] = 'WC_REST_Custom_Controller';

	return $controllers;
}