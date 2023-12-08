<?php
if (!defined('ABSPATH')) exit;

if (!class_exists('NB_Order_Meta')) {

	class NB_Order_Meta {

		public static $validate_input = array('specialist_id', 'order_status', 'post_date', 'order_time_completed', 'order_time_completed_str', 'order_time_out', 'billing_first_name', 'billing_last_name', 'billing_company', 'payment_method', 'payment_status', 'delivery', 'order_create', 'order_update', 'log');

		public static $validate_input2 = array('_specialist_id', '_order_status', 'post_date', '_order_time_completed', '_order_time_completed_str', '_order_time_out', '_billing_first_name', '_billing_last_name', '_billing_company', '_payment_method', '_payment_status', 'delivery', 'order_create', 'order_update', 'log');

		public function __construct()
        {
        }

        public static function update_post_meta($post_id, $meta_key, $meta_value) {
        	$rest = update_post_meta($post_id, $meta_key, $meta_value);
        	self::update_log($post_id, $meta_key. ':' . $meta_value, true);
        	$updated = false;
        	if($meta_key && in_array($meta_key, self::$validate_input2)) {
        		$index = array_search($meta_key, self::$validate_input2);
				$_meta_key = self::$validate_input[$index];
				$data = [];
				$_meta_value = $meta_value;
				if($_meta_key == 'order_time_completed_str' && $meta_value) {
		    		$data['order_time_completed'] = date("Y-m-d H:i:s", $meta_value);
				}

				if($_meta_key == 'order_time_completed') {
					$order_time_completed_str = get_post_meta( $order_id , '_order_time_completed_str' , true);
		    		$_meta_value = date("Y-m-d H:i:s", $order_time_completed_str);
				}

				if($_meta_key == 'order_time_out') {
		    		$order_time_out_str = '';
					if($meta_value) {
					    $_order_time_out = str_replace(' pm', '', $meta_value);
					    $_order_time_out = str_replace(' am', '', $_order_time_out);
					    $order_time_out_date=date_create_from_format("d/m/Y H:i", $_order_time_out);
					    $_order_time_out_date = date_format($order_time_out_date,"Y-m-d H:i:s");
					    $order_time_out_str = strtotime($_order_time_out_date);
					    $data['order_time_out_str'] = $order_time_out_str;
					}
				}

		    	$data[$_meta_key] = $_meta_value;

		    	$updated = self::update_order_meta($post_id, $data);
		    }
		    return $rest;
        }

		public static function create_table_order_meta() {
		    global $wpdb;

		    $collate = '';

		    if ( $wpdb->has_cap( 'collation' ) ) {

		        $collate = $wpdb->get_charset_collate();

		    }

		    $tables =  "
		        CREATE TABLE {$wpdb->prefix}nb_order_meta ( 
		        id bigint(20) unsigned NOT NULL auto_increment,
		        order_id BIGINT(20) UNSIGNED NOT NULL,
		        specialist_id BIGINT(20) UNSIGNED NOT NULL,
		        order_status text NULL,
		        post_date datetime default NULL,
		        order_time_completed datetime default NULL,
		        order_time_completed_str text NULL,
		        order_time_out text NOT NULL,
		        order_time_out_str text NOT NULL,
		        billing_first_name text NULL,
		        billing_last_name text NULL,
		        billing_company text NULL,
		        payment_method text NULL,
		        payment_status text NULL,
		        delivery text NULL,
		        order_create datetime default NULL,
		        order_update datetime default NULL,
		        log text,
		        PRIMARY KEY  (id)
		        ) $collate; 
		    ";
		    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		    @dbDelta($tables);
		}


		public static function migrate_column_post_date($columns) {
		    global $wpdb;

		    $table_name = $wpdb->prefix . 'nb_order_meta';

		    if(is_array($columns) && count($columns) > 0) {
		        foreach($columns as $key => $col_name) {
		            if($col_name) {
		                $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
		                WHERE table_name = '${table_name}' AND column_name = '${col_name}'"  );

		                if(empty($row)){
		                   $wpdb->query("ALTER TABLE ${table_name} ADD ${col_name} datetime");
		                }
		            }
		        }
		    }
		}

		public static function get_order_meta($order_id, $meta_key) {
			global $wpdb;

			if(!$order_id || !$meta_key || !in_array($meta_key, self::$validate_input)) return false;

		    $table_name = $wpdb->prefix . 'nb_order_meta';

		    $data = $wpdb->get_var( $wpdb->prepare("SELECT ${meta_key} FROM $table_name WHERE order_id = %s", $order_id) );

		    return $data;
		}

		public static function is_sync($order_id) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'nb_order_meta';

			$id = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $table_name WHERE order_id = %s", $order_id) );

			if ( !$id ) {
				return false;
			}
			return true;
		}

		public static function add_order_meta($order_id, $data) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'nb_order_meta';

			if ( $wpdb->get_var(
				$wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE order_id = %s", $order_id)
			) ) {
				return false;
			}

			if(!is_array($data)) {
				return false;
			}

			$_data = array();

			foreach($data as $key => $value) {
				if($key && in_array($key, self::$validate_input)) {
			    	$_data[$key] = $value;
			    }
			}

			$_data['order_create'] = current_time( 'mysql' );

			$result = $wpdb->insert($table_name,$_data);

			if ( ! $result ) {
				return false;
			}

			$mid = (int) $wpdb->insert_id;

			return $mid;
		}

		public static function update_order_meta($order_id, $data) {
			global $wpdb;

		    $table_name = $wpdb->prefix . 'nb_order_meta';

		    if(!is_array($data)) {
				return false;
			}

		    $_data = array();

			foreach($data as $key => $value) {
				if($key && in_array($key, self::$validate_input)) {
			    	$_data[$key] = $value;
			    }
			}

		    // $id = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $table_name WHERE order_id = %s", $order_id) );
			// if ( !$id ) {
			// 	self::nb_sync_order($order_id, 'update');
			// 	return false;
			// }


			$where = array(
		        'order_id'    => $order_id,
		    );

		    $_data['order_update'] = current_time( 'mysql' );

		    $result = $wpdb->update( $table_name, $_data, $where );

			if ( ! $result ) {
				return false;
			}

			return true;
		}
		public static function is_imported($order_id) {
			global $wpdb;

		    $table_name = $wpdb->prefix . 'nb_order_meta';

		    $id = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $table_name WHERE order_id = %s", $order_id) );
			if ( $id ) {
				return $id;
			}

			return '';
		}

		public function update_log($order_id, $message, $action = false) {
			$log = self::get_order_meta($order_id, 'log');
			$new_log = $log.','.$message;
			if($action) {
				self::update_order_meta($order_id, array('log' => $new_log));
			}
		}

		public static function nb_sync_order($order_id, $s = '') {
		   global $wpdb;

		   $is_imported = self::is_imported($order_id);

		   if(!$order_id) {
		   	return array(
				'flag' 	=> 0,
				'id'	=> '',
			);
		   }

		    $specialist_id = get_post_meta( $order_id , '_specialist_id' , true);
		    $order_status = get_post_meta( $order_id , '_order_status' , true);
		    $order_time_completed_str = get_post_meta( $order_id , '_order_time_completed_str' , true);
		    $order_time_completed = $order_time_completed_str ? date("Y-m-d H:i:s", $order_time_completed_str) : null;
		    $order_time_out = get_post_meta( $order_id , '_order_time_out' , true);
			$order_time_out_str = '';
			if($order_time_out) {
			    $_order_time_out = str_replace(' pm', '', $order_time_out);
			    $_order_time_out = str_replace(' am', '', $_order_time_out);
			    $order_time_out_date=date_create_from_format("d/m/Y H:i", $_order_time_out);
			    $_order_time_out_date = date_format($order_time_out_date,"Y-m-d H:i:s");
			    $order_time_out_str = strtotime($_order_time_out_date);
			}
		    $_billing_first_name = get_post_meta( $order_id , '_billing_first_name' , true);
		    $_billing_last_name = get_post_meta( $order_id , '_billing_last_name' , true);
		    $payment_method = get_post_meta( $order_id , '_payment_method' , true);
		    $payment_status = get_post_meta( $order_id , '_payment_status' , true);

		    $user_id = get_post_meta( $order_id , '_customer_user' , true);
		    $billing_first_name = $_billing_first_name ? $_billing_first_name : get_user_meta( $user_id, 'billing_first_name' , true );
		    $billing_last_name = $_billing_last_name ? $_billing_last_name : get_user_meta( $user_id, 'billing_last_name' , true );
		    $billing_company = get_user_meta( $user_id, 'billing_company' , true );

		    $order = wc_get_order( $order_id );
		    $delivery = $order->get_shipping_method();
		    $post = get_post($order_id);
		    $wc_order_status = $post->post_status;
		    $post_date = $post->post_date;

		    $table_name = $wpdb->prefix . 'nb_order_meta';

		    $log = self::get_order_meta($order_id, 'log');
		    $current_tatus = self::get_order_meta($order_id, 'order_status');
		    $new_log = $log.',SYNC:'.$current_tatus.':'.$order_status. '-' .$s;

		    $data = array(
				'order_id' => $order_id,
				'specialist_id' => $specialist_id,
				'order_status' => $order_status,
				'post_date' => $post_date,
				'order_time_completed' => $order_time_completed,
				'order_time_completed_str' => $order_time_completed_str,
				'order_time_out' => $order_time_out,
				'order_time_out_str' => $order_time_out_str,
				'billing_first_name' => esc_sql($billing_first_name),
				'billing_last_name' => esc_sql($billing_last_name),
				'billing_company' => esc_sql($billing_company),
				'payment_method' => esc_sql($payment_method),
				'payment_status' => esc_sql($payment_status),
				'delivery' => esc_sql($delivery),
				'order_create' => self::get_order_meta($order_id, 'order_create') ? self::get_order_meta($order_id, 'order_create') : current_time( 'mysql' ),
				'order_update' => current_time( 'mysql' ),
				'log'	=> $new_log,
			);

			if($is_imported) {
				$where = array(
			        'order_id'    => $order_id,
			    );
				$wpdb->update( $table_name, $data, $where);
			} else {
				$wpdb->insert( $table_name, $data);
				$order_id = $wpdb->insert_id;
			}

			return array(
				'flag' 	=> 1,
				'id'	=> $order_id,
			);
		}

		public static function get_list_order_id($page, $from, $to, $limit = 10000) {
			global $wpdb;

			$offset = ($page - 1) * $limit;

			$where = [];

			if($from) {
				$where[] = 'wp_posts.ID >= '. $from;
			}

			if($to) {
				$where[] = 'wp_posts.ID <= '. $to;
			}

			$where_str = implode(' AND ', $where);


			$sql = "SELECT ID from `wp_posts` WHERE wp_posts.post_type = 'shop_order' AND `post_date` > '2023-01-01 00:00:00' ORDER BY wp_posts.ID ASC LIMIT ${offset}, ${limit}";

			if($where_str) {
				$sql = "SELECT ID from `wp_posts` WHERE wp_posts.post_type = 'shop_order' AND `post_date` > '2023-01-01 00:00:00' AND ". $where_str ." ORDER BY wp_posts.ID ASC LIMIT ${offset}, ${limit}";
			}

			$order_ids = $wpdb->get_results($sql, 'ARRAY_A');

			return $order_ids;
		} 

	}
}

?>