<?php
/**
 * WooCommerce Save & Share Cart Settings
 *
 * @author        cxThemes
 * @category    Settings
 * @version     2.1.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_Email_Cart_Settings')):

    /**
     * WC_Email_Cart_Settings
     */
    class WC_Email_Cart_Settings
    {

        protected $id = '';
        protected $label = '';

        /**
         * Constructor.
         */
        public function __construct()
        {

            $this->id = 'email_cart_settings';
            $this->label = __('Save & Share Cart Settings', 'email-cart');

            add_action('plugins_loaded', array($this, 'botaksign_user_role'));
            // add the menu itme
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('wp_ajax_getDownloadAWS', array($this, 'botaksign_getDownloadAWS'));
            add_action('woocommerce_settings_email_cart_settings', array($this, 'output'));
            add_action('pre_get_posts', array($this, 'botaksign_pre_get_posts'));
            add_action('woocommerce_settings_save_email_cart_settings', array($this, 'save'));

            add_filter('woocommerce_disable_admin_bar', array($this, '_wc_disable_admin_bar'), 10, 1);
            add_filter('woocommerce_prevent_admin_access', array($this, '_wc_prevent_admin_access'), 10, 1);
            add_action( 'admin_menu', array($this, 'custom_remove_menu_pages') );
        }

        function _wc_disable_admin_bar($prevent_admin_access)
        {
            if (current_user_can('specialist') || current_user_can('production') || current_user_can('customer_service')) {
                return false;
            }
            return $prevent_admin_access;
        }

        function _wc_prevent_admin_access($prevent_admin_access)
        {
            if (current_user_can('specialist') || current_user_can('production') || current_user_can('customer_service')) {
                return false;
            }
            return $prevent_admin_access;
        }

        public function botaksign_getDownloadAWS()
        {
            //do bên js để dạng json nên giá trị trả về dùng phải encode
            $id_order = (isset($_POST['id_order'])) ? esc_attr($_POST['id_order']) : '';
            $user = wp_get_current_user();
            if (in_array('administrator', (array)$user->roles) || in_array('specialist', (array)$user->roles)) {
                $link_down = $this->getLinkAWS($id_order);
                if ($link_down != '') {
                    update_post_meta($_POST['id_order'], '_cxecrt_status_od', 2);
                    update_post_meta($_POST['id_order'], '_resend_artwork', 0);

                    wp_send_json_success($link_down);
                }
                wp_send_json_success('false');
            } else {
                wp_send_json_success('false');
            }
            die(); //bắt buộc phải có khi kết thúc
        }
        public function botaksign_pre_get_posts($query) {
            $order_file = isset($_POST['order_file'])? $_POST['order_file']: '';
            $order_type = isset($_POST['order_type'])? $_POST['order_type'] : '';
            $order_no = isset($_POST['order_no'])? $_POST['order_no'] : '' ;
            $order_date = isset($_POST['order_date'])? $_POST['order_date'] : '' ;
            $order_status = isset($_POST['opt_status'])? $_POST['opt_status']: '';
            // if($order_file != '0') {
            //     //$query->set( 'post_type', array( 'post', 'the_custom_pt' ) );
            // }
            if($order_type != '') {
                $query->set( 'meta_query', array(
                    array(
                        'key'     => 'order_type',
                        'compare' => '=',
                        'value'   => $order_type,
                    )
                ) );
            }

        }
        public function botaksign_user_role() 
        {
            $shop_manager_role = get_role('subscriber');
            $shop_manager_role->add_cap('nb_custom_dashboard');
            $shop_capabilities = $shop_manager_role->capabilities;
            $shop_capabilities['list_users'] = true;
            $shop_capabilities['read'] = true;
            // $shop_capabilities['manage_options'] = true;
            $shop_capabilities['view_admin_dashboard'] = true;
            $user_product = add_role('production', 'Production', $shop_capabilities);
            $user_specialist = add_role('specialist', 'Specialist', $shop_capabilities);
            $user_cs = add_role('customer_service', 'Customer Service', $shop_capabilities);
            if (null === $user_product) {
                $user_product = get_role('production');
                $user_product->remove_cap('list_users');
            }
            if (null === $user_cs) {
                $user_cs = get_role('customer_service');
                $user_cs->remove_cap('list_users');
            }

            //cutom roles designer
            $designer_capabilities[ 'view_admin_dashboard' ] = true;
            $designer_capabilities[ 'read' ] = true;
            $designer_capabilities[ 'edit_posts' ] = true;
            $designer_capabilities[ 'edit_product' ] = true;
            $designer_capabilities[ 'read_product' ] = true;
            $designer_capabilities[ 'delete_product' ] = true;
            $designer_capabilities[ 'edit_products' ] = true;
            $designer_capabilities[ 'edit_others_products' ] = true;
            $designer_capabilities[ 'publish_products' ] = true;
            $designer_capabilities[ 'read_private_products' ] = true;
            $designer_capabilities[ 'delete_products' ] = true;
            $designer_capabilities[ 'delete_private_products' ] = true;
            $designer_capabilities[ 'delete_published_products' ] = true;
            $designer_capabilities[ 'delete_others_products' ] = true;
            $designer_capabilities[ 'edit_private_products' ] = true;
            $designer_capabilities[ 'edit_published_products' ] = true;
            $designer_capabilities[ 'manage_product_terms' ] = true;
            $designer_capabilities[ 'edit_product_terms' ] = true;
            $designer_capabilities[ 'delete_product_terms' ] = true;
            $designer_capabilities[ 'assign_product_term' ] = true;
            $designer_capabilities[ 'edit_published_posts' ] = true;
            // custom role nbd template
            $designer_capabilities[ 'manage_nbd_template' ] = true;
            $designer_capabilities[ 'edit_nbd_template' ] = true;
            $designer_capabilities[ 'delete_nbd_template' ] = true;
            $designer_capabilities[ 'manage_nbd_product' ] = true;
            // remove_role('designer');  
            $user_designer = add_role('designer', 'Designer', $designer_capabilities);

        }

        public function custom_remove_menu_pages() {
            $user = wp_get_current_user();
            if ( in_array('designer', $user->roles) ) {
                remove_menu_page('EWD-UFAQ-Options');
            }
        }

        /**
         * Add a submenu item to the WooCommerce menu
         */
         public function admin_menu()
        {
            // add_menu_page('Order Dashboard', 'Order Dashboard', 'manage_options', 'mybts/order_dashboard', array($this, 'order_dashboard_admin_page'), 'dashicons-tickets', 6);
            $user = get_userdata(get_current_user_id());
            if ($user->roles[0] == 'administrator') {
                add_submenu_page(
                    'edit.php?post_type=stored-carts', __("Settings", 'email-cart'), __("Settings", 'email-cart'), 'manage_options', $this->id, array($this, 'admin_page')
                );
            }
        }
        
        public function order_dashboard_admin_page()
        {
            global $wpdb;
            $user = get_userdata(get_current_user_id());
            if (in_array('administrator', $user->roles)) {
                //sort order by order type
                $query = "SELECT * FROM {$wpdb->prefix}posts AS p WHERE p.post_type='shop_order'";
                // $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = 'order_type' AND pm.meta_value = '2' UNION ALL SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = 'order_type' AND pm.meta_value = '1' UNION ALL SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = 'order_type' AND pm.meta_value = '0'";
            } else {
//                if ($user->roles[0] == 'specialist') {
//                    $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE pm.meta_key='_customer_user' AND pm.meta_value IN (SELECT `user_id` FROM {$wpdb->prefix}usermeta WHERE `meta_key` = 'specialist' AND `meta_value` = " . get_current_user_id() . ") AND p.post_type='shop_order' AND p.post_status = 'wc-processing'";
//                } elseif ($user->roles[0] == 'production') {
//                    $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = '_cxecrt_status_od_p' AND p.post_status = 'wc-processing'";
//                } elseif ($user->roles[0] == 'customer_service') {
//                    $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = '_cxecrt_status_od_cs' AND p.post_status = 'wc-processing'";
//                }
                if (in_array('specialist', $user->roles)) {
                    $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE pm.meta_key='_customer_user' AND pm.meta_value IN (SELECT `user_id` FROM {$wpdb->prefix}usermeta WHERE `meta_key` = 'specialist' AND `meta_value` = " . get_current_user_id() . ") AND p.post_type='shop_order'";
                } elseif (in_array('production', $user->roles)) {
                    $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = '_cxecrt_status_od' AND ((pm.meta_value >= 5 AND pm.meta_value <= 8) OR (pm.meta_value = 2))";
                } elseif (in_array('customer_service', $user->roles)) {
                    $query = "SELECT * FROM {$wpdb->prefix}posts AS p INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key = '_cxecrt_status_od' AND pm.meta_value >= 8";
                }
            }
            $total = $wpdb->get_var("SELECT COUNT(1) FROM (${query}) AS combined_table");
            $items_per_page = 10;
            $page = isset($_GET['cpage']) ? abs((int)$_GET['cpage']) : 1;
            $offset = ($page * $items_per_page) - $items_per_page;

            //sort order by order type
            if (in_array('administrator', $user->roles)) {
                $results = $wpdb->get_results($query . " ORDER BY ID DESC, ID DESC LIMIT ${offset}, ${items_per_page}");
            } else {
                $results = $wpdb->get_results($query . " ORDER BY ID DESC LIMIT ${offset}, ${items_per_page}");
            }
            $group_a = $group_b = false;
            if (in_array('administrator', $user->roles) || in_array('specialist', $user->roles)) {
                $group_a = true;
            }
            if (in_array('customer_service', $user->roles)) {
                $group_b = true;
            }
            $condition = isset($_GET['post_type'])? $_GET['post_type'] : '';
            if($condition == 'shop_order') {
                $args = array(
                    'post_type'         => 'shop_order',
                    'posts_per_page'    => -1,
                    'post_status'       => array('wc-pending' , 'wc-processing' , 'wc-on-hold' , 'wc-completed' , 'wc-cancelled' , 'wc-refunded' , 'wc-failed' , 'trash'),
                );
                $meta_query = array();
                $order_file = isset($_GET['order_file'])? intval($_GET['order_file']) : '' ;
                $order_type = isset($_GET['order_type'])? intval($_GET['order_type']) : '' ;
                $order_no = isset($_GET['order_no'])? intval($_GET['order_no']) : '' ;
                $order_date = isset($_GET['order_date'])? $_GET['order_date'] : '' ;
                $order_company = isset($_GET['order_company'])? $_GET['order_company'] : '' ;
                $order_name = isset($_GET['order_name'])? $_GET['order_name'] : '' ;
                //$order_status = isset($_GET['order_status'])? $_GET['order_status'] : '' ;
                if($_GET['opt_status'] != '0') {
                    $order_status = $_GET['order_status'];
                } else {
                    $order_status = 1;
                }

                if($order_no != '') {
                    $args['p'] = $order_no;
                } 

                if($order_type != '3') {
                    $meta_query = array(
                        'key' => 'order_type',
                        'value' => $order_type,
                        'compare' => '='
                    );
                }
                else {
                    // $meta_query = array(
                    //     'key' => 'order_type',
                    //     'value' => '',
                    //     'compare' => '!='
                    // );
                }

                if($order_company != '') {
                    
                    $meta_query = array(
                        'key' => '_billing_company',
                        'value' => $order_company,
                        'compare' => 'LIKE'
                    );
                }

                if($order_name != '') {
                    $meta_query = array(
                        array(
                            'relation' => 'OR',
                            array(
                                'key' => '_billing_first_name',
                                'value' => $order_name,
                                'compare' => 'LIKE'
                            ),
                            array(
                                'key' => '_billing_last_name',
                                'value' => $order_name,
                                'compare' => 'LIKE'
                            )
                        )
                    );
                }
                if($order_date) {
                    $date = explode( '-' , $order_date);
                    $args['date_query'][] = array(
                        'column' => 'post_date',
                        'year' => $date[0],
                        'month' => $date[1],
                        'day' => $date[2],
                    );
                }
                $args['meta_query'][] = $meta_query;
                $query = new WP_Query($args);
                $query_posts = $query->posts;
                $results = array();
                if($order_file != '0') {
                    $query_posts = array();
                    foreach ($query->posts as $key => $value) {
                        if($order_file == '1') {
                            if($this->getLinkAWS($value->ID) == '') {
                                $query_posts[] = $value;
                            }
                        }
                        else if($order_file == '2') {
                            if($this->getLinkAWS($value->ID) != '') {
                                $query_posts[] = $value;
                            }
                        }
                    }
                }
                $total = count($query_posts);
                foreach ($query_posts as $key => $value) {
                    if( $key >= 10*($page-1) && $key < 10*$page ) {
                        $results[] = $value;
                    }
                }
            }

            ?>
            <link rel="stylesheet"
                  href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css"/>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <style>
                #order-dashboard-table {
                    width: 100%;
                    border: 1px solid #bebebe;
                }

                #order-dashboard-table th[fulltable-field-name="show"] {
                    width: 50px;
                }

                #order-dashboard-table th[fulltable-field-name="date_in"] input.fulltable-filter,
                #order-dashboard-table td[fulltable-field-name="status"] select[name="opt_status"] {
                    width: 145px !important;
                }

                #order-dashboard-table .btn-act {
                    border-radius: 3px;
                    text-align: center;
                    cursor: pointer;
                }

                #order-dashboard-table .btn-act.btn-dl {
                    min-width: 100px;
                }

                #order-dashboard-table .btn-dl svg {
                    position: relative;
                    top: 2px;
                }

                #order-dashboard-table .btn-dl span {
                    position: relative;
                    bottom: 6px;
                }

                #order-dashboard-table .btn-dl a {
                    position: relative;
                    bottom: 6px;
                }

                #order-dashboard-table .generate_link_payment {
                    display: block;
                    text-align: center;
                    cursor: pointer;
                }

                #order-dashboard-table .btn-act {
                    cursor: pointer;
                }

                .toplevel_page_mybts-order_dashboard .paginate_od {
                    float: right;
                    margin-top: 10px;

                }
                .toplevel_page_mybts-order_dashboard .paginate_od .page-numbers {
                    color: #0071a1;
                    border-color: #0071a1;
                    background: #f3f5f6;
                    vertical-align: top;
                }
                .toplevel_page_mybts-order_dashboard .paginate_od .page-numbers {
                    display: inline-block;
                    text-decoration: none;
                    font-size: 13px;
                    line-height: 2.15384615;
                    min-height: 30px;
                    margin: 0;
                    padding: 0 10px;
                    cursor: pointer;
                    border-width: 1px;
                    border-style: solid;
                    -webkit-appearance: none;
                    border-radius: 3px;
                    white-space: nowrap;
                    box-sizing: border-box;
                }
                .toplevel_page_mybts-order_dashboard .paginate_od .page-numbers.current {
                    background: #0071a1;
                    color: #fff;
                }
                #swal2-content .col-left {
                    width: 50%;
                    float: left;
                }

                #swal2-content .col-right {
                    width: 50%;
                    display: inline-block;
                }

                #swal2-content #sig-canvas {
                    text-align: center;
                    border: 1px solid #ccc;
                }

                .custom-signature {
                    border: 0px !important;
                }

                #sig-clearBtn {
                    position: absolute;
                    right: 40px;
                    bottom: 0px;
                    cursor: pointer;
                }

                #swal2-content {
                    text-align: left;
                }

                #swal2-content h4 {
                    margin: 10px 0;
                }

                .swal2-header {
                    align-items: unset;
                }

                table.tbpro {
                    width: 100%;
                    border-bottom: 1px solid #ccc;
                }

                table#order-dashboard-table {
                    position: relative;
                }

                table#order-dashboard-table .num-artwork {
                    background-color: red;
                    color: #fff;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    position: absolute;
                    top: -10px;
                    right: 0px;
                }

                table.tbpro th {
                    border-bottom: 1px solid #ccc;
                }

                .swal2-content {
                    border-top: 1px solid #ccc;
                }

                h2.swal2-title {
                    font-size: 25px;
                    margin-bottom: 15px;
                }

                h2.swal2-title .status-od {
                    font-size: 18px;
                    position: absolute;
                    right: 30px;
                    background: #c6e1c6;
                    color: #719468;
                    padding: 5px;
                    border-radius: 5px;
                    top: -7px;
                }

                .swal2-actions {
                    display: none !important;
                }

                .btn-download-od {
                    float: right;
                    background: #ebebec;
                    border: 0px;
                    border-radius: 3px;
                    padding: 5px 10px;
                    font-size: 16px;
                    margin-top: 15px;
                    font-weight: bold;
                    color: #717273;
                    cursor: pointer;
                }

                #sig-submitBtn {
                    background: #ebebec;
                    border: 0px;
                    border-radius: 3px;
                    padding: 8px 15px;
                    font-size: 16px;
                    margin-top: 15px;
                    font-weight: bold;
                    color: #717273;
                    cursor: pointer;
                    display: block;
                    margin: 10px auto;
                }

                .disable-act {
                    opacity: 0.5;
                    pointer-events: none;
                }

                #swal2-content .btn-add-row {
                    width: 99%;
                    padding: 3px;
                    border: 1px solid #ccc;
                    border-radius: 3px;
                    font-size: 18px;
                    font-weight: bold;
                    text-align: center;
                    cursor: pointer;
                    margin-top: 5px;
                    display: block;
                }

                #swal2-content .btn-update-status-od {
                    float: right;
                    background: #f4f5f6;
                    border: 0px;
                    font-weight: bold;
                    color: #8e8f90;
                    padding: 8px;
                    cursor: pointer;
                }

                table.table-aaod tbody tr:first-child td:last-child {
                    opacity: 0.5;
                    pointer-events: none;
                }

                .all-option-expand {
                    font-size: 15px;
                }

                .all-option-expand .more {
                    display: none;
                }

                .all-option-expand .ao-expand {
                    padding-left: 10px;
                }

                .all-option-expand .btn-expand {
                    font-weight: 500;
                    cursor: pointer;
                    width: 75px;
                    position: relative;
                }

                .all-option-expand .btn-expand .arrow2 {
                    background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAPCAYAAADkmO9VAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABzSURBVDhP7dE7DoAgEEVRd4Ed1rgw1sD2KFkQBcU8opHEDwMTtbDwJq+hOMmEAS/3g8/rgiklhBDgnEOMcXvla4IL5r3HbAwmrWGt7aIsuMdGpdZJ0CpYw6ToBWxhZS30AEqwMg6tnkxE4p1jP+VuXweBDKrKS+cGqUnvAAAAAElFTkSuQmCC) no-repeat;
                    position: absolute;
                    bottom: 0px;
                    right: 0px;
                    width: 20px;
                    height: 15px;
                    display: block;
                }

                .all-option-expand .btn-expand.active .arrow2 {
                    transform: rotate(180deg);
                }

                .toplevel_page_mybts-order_dashboard .swal2-header, .toplevel_page_mybts-order_dashboard #swal2-content {
                    text-align: left;
                    align-items: unset;
                }
                .btn-signature {
                    background-color: #fff;
                }
                .btn-signature.disabled-sig {
                    pointer-events: none;
                    opacity: 0.5;
                    background-color: grey;
                }
            </style>
            <div class="wrap">
                <h2>Order Dashboard</h2>
            </div>
                <table class="fulltable fulltable-editable" id="order-dashboard-table" border="1" cellspacing="0"
                       cellpadding="5">
                    <thead>
                        <form id="nb-form-table" method="get" action="admin.php">
                            <input type="hidden" name="page" value="mybts/order_dashboard">
                            <input type="hidden" name="post_type" value="shop_order">
                            <tr>
                                <th fulltable-field-name="show">Show</th>
                                <?php if ($group_a) { ?>
                                    <th fulltable-field-name="order_files">Order Files
                                        <span class="fulltable-filter">
                                            <select name="order_file" class="fulltable-filter" type="text" style="height: 30px!important">
                                                <option value="0"select>Select</option>
                                                <option value="1">No download file</option>
                                                <option value="2">Download</option>
                                            </select>
                                        </span>
                                    </th>
                                <?php } ?>
                                <th fulltable-field-name="order_type" style="min-width: 100px;">Order Type
                                    <span class="fulltable-filter">
                                        <select name="order_type" class="fulltable-filter" type="text"  style="height: 30px!important">
                                            <option value="3" selected>Select</option>
                                            <option value="0">Standard</option>
                                            <option value="1">RUSH</option>
                                            <option value="2">Super RUSH</option>
                                        </select>
                                    </span>
                                </th>
                                <th fulltable-field-name="order_no" style="min-width: 90px;">Order No.
                                    <span class="fulltable-filter">
                                        <input name="order_no" class="fulltable-filter" type="text">
                                    </span>
                                </th>
                                <th fulltable-field-name="date_in" style="min-width: 145px;">Date In
                                    <span class="fulltable-filter">
                                        <input name="order_date" class="fulltable-filter" type="date">
                                    </span>
                                </th>
                                <th fulltable-field-name="status">Status
                                   <!--  <span class="fulltable-filter">
                                        <select id="order_status" name="order_status">
                                            <option value="0" selected>Select</option>
                                            <option value="1">Order Received</option>
                                            <option value="2">Processing</option>
                                            <option value="3">Artwork Amendment</option>
                                            <option value="4">Outsource</option>
                                            <option value="5">Printing</option>
                                            <option value="6">Finishing 1</option>
                                            <option value="7">Finishing 2</option>
                                            <option value="8">QC / Packing</option>
                                            <option value="9">Collection Point</option>
                                            <option value="10">Delivery</option>
                                            <option value="11">Completed</option>
                                        </select>
                                    </span> -->
                                </th>
                                <?php if ($group_a) { ?>
                                    <th fulltable-field-name="name" style="min-width: 70px;">Name
                                        <span class="fulltable-filter">
                                            <input name="order_name" class="fulltable-filter" type="text">
                                        </span>

                                    </th>
                                    <th fulltable-field-name="company" style="min-width: 80px;">Company
                                        <span class="fulltable-filter">
                                            <input name="order_company" class="fulltable-filter" type="text">
                                        </span>
                                    </th>
                                <?php } ?>
                                <th fulltable-field-name="est_completion">Est. Completion</th>
                                <?php if ($group_b || $user->roles[0] == 'administrator') { ?>
                                    <th fulltable-field-name="collection">Collection</th>
                                <?php } ?>
                                <?php if ($group_a) {
                                    $de = countResendArtwork();
                                    ?>
                                    <th fulltable-field-name="artwork_amendment" rel="<?php echo $de; ?>">Artwork Amendment</th>
                                <?php } ?>
                            </tr>
                            <input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1" />
                        </form>
                    </thead>
                    <tbody>
                    <?php foreach ($results as $p) {
                        $order = wc_get_order($p->ID);
                        $list_item = $order->get_items('line_item');
                        $order_type = "Standard";
                        $class_order_type = 'order-standard';
                        $otype = get_post_meta($p->ID, 'order_type', true);
                        $post_status = get_post_status($p->ID);
                        $background_color = '';
                        if($post_status == 'wc-cancelled') {
                            $background_color = 'style="background-color: #e5e5e5"';
                        }
                        switch ($otype) {
                            case '1':
                                if ($order_type != 'Super RUSH') {
                                    $order_type = 'RUSH';
                                    $class_order_type = 'order-rush';
                                }
                                break;
                            case '2':
                                $order_type = 'Super RUSH';
                                $class_order_type = 'order-super-rush';
                                break;
                        };
                        $reupaw = get_post_meta($p->ID, '_resend_artwork', true);
                        if ($reupaw == 1) {
                            $class_order_type = 'order-reupload-aw';
                        }
                        ?>
                        <tr rel="<?php echo $reupaw; ?>" class="<?php echo $class_order_type; ?>" <?php echo $background_color; ?> >
                            <td>
                               <span class="btn-act btn-viewdt" rel="<?php echo $p->ID; ?>" rel2="<?php echo wp_create_nonce('generate_wpo_wcpdf'); ?>" rel3="<?php echo $order->get_payment_method(); ?>">
                                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="40.000000pt"
                                         height="15.000000pt"
                                         viewBox="0 0 90.000000 54.000000" preserveAspectRatio="xMidYMid meet">
                                        <g transform="translate(0.000000,54.000000) scale(0.100000,-0.100000)"
                                           fill="#000000" stroke="none">
                                            <path d="M305 439 c-70 -34 -88 -49 -134 -107 l-33 -42 27 -35 c43 -57 76 -84
                                                  143 -115 88 -42 177 -42 264 0 72 35 90 49 136 108 l34 42 -34 43 c-46 58 -64
                                                  72 -136 107 -87 42 -181 42 -267 -1z m218 -66 c32 -31 37 -43 37 -83 0 -68
                                                  -52 -120 -120 -120 -40 0 -52 5 -83 37 -32 31 -37 43 -37 83 0 40 5 52 37 83
                                                  31 32 43 37 83 37 40 0 52 -5 83 -37z"/>
                                            <path d="M402 327 c-28 -30 -28 -48 1 -75 30 -28 48 -28 75 1 28 30 28 48 -1
                                                  75 -30 28 -48 28 -75 -1z"/>
                                        </g>
                                    </svg>
                                </span>
                            </td>
                            <?php if ($group_a) { ?>
                                <td>
                                    <div class="btn-act btn-dl">
                                        <?php if ($this->getLinkAWS($p->ID) !== ''): ?>
                                            <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                                 width="15.000000pt" height="20.000000pt" viewBox="0 0 72.000000 69.000000"
                                                 preserveAspectRatio="xMidYMid meet">
                                                <g transform="translate(0.000000,69.000000) scale(0.100000,-0.100000)"
                                                   fill="#000000" stroke="none">
                                                    <path d="M144 577 c-2 -7 -3 -114 -2 -237 l3 -225 205 0 205 0 3 162 2 163
                                            -77 -3 -78 -2 2 78 3 77 -130 0 c-97 0 -132 -3 -136 -13z m241 -267 c0 -68 2
                                            -75 20 -76 11 -1 26 -2 33 -3 13 -1 17 -31 3 -31 -4 0 -24 -13 -42 -30 -42
                                            -37 -56 -37 -98 0 -18 17 -38 30 -42 30 -14 0 -10 30 4 31 6 1 21 2 32 3 18 1
                                            20 7 17 65 -4 84 0 93 40 89 l33 -3 0 -75z"/>
                                                    <path d="M444 556 c-3 -8 -4 -29 -2 -48 3 -31 6 -33 42 -36 60 -5 66 12 21 58
                                            -42 43 -53 48 -61 26z"/>
                                                </g>
                                            </svg>
                                            <span class="download-aws" id-order="<?php echo $p->ID; ?>">Download</span>
                                        <?php else: ?>
                                            No download file
                                        <?php endif; ?>
                                    </div>

                                </td>
                            <?php } ?>
                            <td><span><?php echo $order_type; ?></span></td>
                            <td><span><?php echo $p->ID; ?></span></td>
                            <td><span><?php echo date('d/m/Y', strtotime($p->post_date)); ?></span></td>
                            <?php
                            //                        $opt_status = get_post_meta($p->ID, '_cxecrt_status_od' . cxecrt_get_key_by_role_user(), true);
                            $opt_status = get_post_meta($p->ID, '_cxecrt_status_od', true);
                            if (!$opt_status) {
                                $opt_status = 1;
                            }
                            ?>
                            <td class="opt_status_od_co" rel="<?php echo $p->ID; ?>" fulltable-field-name="status">
                                <select id="opt_status" name="opt_status">
                                    <option value="0">--Please select--</option>
                                    <?php if ($group_a) { ?>
                                        <option value="1" <?php selected($opt_status, 1); ?>>Order Received</option>
                                    <?php } ?>
                                    <?php if (in_array('specialist', $user->roles) || in_array('administrator', $user->roles) || in_array('production', $user->roles)) { ?>
                                        <option value="2" <?php selected($opt_status, 2); ?>>Processing</option>
                                    <?php } ?>
                                    <?php if ($group_a) { ?>
                                        <option value="3" <?php selected($opt_status, 3); ?>>Artwork Amendment</option>
                                        <option value="4" <?php selected($opt_status, 4); ?>>Outsource</option>
                                    <?php } ?>
                                    <?php if (in_array('production', $user->roles) || in_array('administrator', $user->roles)) { ?>
                                        <option value="5" <?php selected($opt_status, 5); ?>>Printing</option>
                                        <option value="6" <?php selected($opt_status, 6); ?>>Finishing 1</option>
                                        <option value="7" <?php selected($opt_status, 7); ?>>Finishing 2</option>
                                    <?php } ?>
                                    <?php if (in_array('production', $user->roles) || in_array('customer_service', $user->roles) || in_array('administrator', $user->roles)) { ?>
                                        <option value="8" <?php selected($opt_status, 8); ?>>QC / Packing</option>
                                    <?php } ?>
                                    <?php if (in_array('customer_service', $user->roles) || in_array('administrator', $user->roles)) { ?>
                                        <option value="9" <?php selected($opt_status, 9); ?>>Collection Point</option>
                                        <option value="10" <?php selected($opt_status, 10); ?>>Delivery</option>
                                        <option value="11" <?php selected($opt_status, 11); ?>>Completed</option>
                                    <?php } ?>
                                </select>
                            </td>
                            <?php if ($group_a) { ?>
                                <td>
                                    <span><?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></span>
                                </td>
                                <td><span><?php echo $order->get_billing_company(); ?></span>
                                </td>
                            <?php } ?>
                            <td><span><?php echo show_est_completion($order)['total_time']; ?></span></td>
                            <?php if ($group_b || in_array('administrator', $user->roles)) { ?>
                                <td>
                                    <div title="Signature" class="btn-act btn-signature <?php if($opt_status==11) { echo 'disabled-sig'; } ?>" rel="<?php echo $p->ID; ?>">
                                        <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                             width="15.000000pt" height="15.000000pt" viewBox="0 0 75.000000 75.000000"
                                             preserveAspectRatio="xMidYMid meet">
                                            <g transform="translate(0.000000,75.000000) scale(0.100000,-0.100000)"
                                               fill="#000000" stroke="none">
                                                <path d="M473 538 c-36 -40 -35 -45 23 -102 30 -31 58 -56 62 -56 16 0 62 56
                                            62 76 0 28 -76 104 -104 104 -13 0 -32 -10 -43 -22z"/>
                                                <path d="M366 455 c-11 -8 -33 -15 -49 -15 -51 0 -82 -27 -101 -89 -10 -31
                                            -24 -68 -32 -83 -8 -14 -14 -35 -14 -45 0 -11 -7 -28 -15 -39 -19 -26 -20 -74
                                            0 -74 8 0 15 -7 15 -15 0 -20 58 -20 84 0 11 8 29 15 40 15 12 0 30 7 40 15
                                            11 8 27 15 37 15 27 0 98 35 114 56 8 10 15 32 15 47 0 15 7 42 16 59 l16 31
                                            -68 68 c-38 38 -71 69 -74 69 -3 0 -14 -7 -24 -15z m4 -145 c33 -33 19 -74
                                            -30 -85 -16 -3 -55 -31 -87 -61 -54 -50 -83 -66 -83 -45 0 9 34 51 82 102 14
                                            15 29 42 33 58 11 50 51 65 85 31z"/>
                                            </g>
                                        </svg>
                                    </div>
                                </td>
                            <?php } ?>
                            <?php if ($group_a) {
                                ?>
                                <td class="col_artwork_amendment">
                                    <span class="btn-act generate_link_payment <?php echo($opt_status != 3 || $this->getLinkAWS($p->ID) == '' ? 'disable-act' : ''); ?>"
                                          rel="<?php echo $p->ID; ?>">
                                <svg version="1.0"
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="16.000000pt" height="16.000000pt"
                                     viewBox="0 0 126.000000 126.000000"
                                     preserveAspectRatio="xMidYMid meet">
                                    <g transform="translate(0.000000,126.000000) scale(0.100000,-0.100000)"
                                       fill="#000000" stroke="none">
                                        <path d="M810 1046 c-24 -7 -77 -52 -170 -146 -138 -138 -160 -172 -160 -246
                                        0 -82 63 -187 108 -182 40 5 51 51 21 91 -18 25 -24 45 -24 90 l0 57 123 122
                                        122 123 60 0 c54 0 63 -3 92 -33 30 -29 33 -38 33 -92 l0 -60 -63 -63 c-70
                                        -71 -79 -105 -34 -135 23 -15 26 -13 100 64 54 56 81 92 90 122 26 90 7 163
                                        -61 232 -65 65 -146 85 -237 56z"/>
                                        <path d="M680 684 c-14 -35 -13 -37 12 -70 18 -22 23 -41 23 -86 l0 -58 -123
                                        -123 -122 -122 -60 0 c-54 0 -63 3 -92 33 -30 29 -33 38 -33 92 l0 60 63 63
                                        c65 65 78 101 47 127 -26 21 -61 2 -127 -69 -64 -70 -88 -119 -88 -183 1 -116
                                        114 -228 230 -228 79 0 110 20 250 160 138 138 160 172 160 246 0 115 -110
                                        239 -140 158z"/>
                                    </g>
                                </svg></span>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <div class="ícsyv" style="display: none;">
                <?php
                $arr_product = [];
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'product_type',
                            'field' => 'slug',
                            'terms' => 'service',
                        ),
                    ),
                );
                $the_query = new WP_Query($args);
                foreach ($results as $p) {
                    $order = wc_get_order($p->ID);
                    if ($order) {
                        $_invoice_number  = '';
                        if($order->get_payment_method() != 'cod' ) {
                            $_invoice_number = get_post_meta( $order->get_id(), '_wcpdf_invoice_number', true);
                        }
                        $order_data = $order->get_data();
                        ?>
                        <div class="dtbill_<?php echo $p->ID; ?>">
                            <div class="col-left">
                                <h4>Billing details</h4>
                                <p><?php echo $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name']; ?>
                                    <?php echo($order_data['billing']['company'] != '' ? '<br/>' . $order_data['billing']['company'] : ''); ?>
                                    <?php echo($order->get_billing_address_1() != '' ? '<br/>' . $order->get_billing_address_1() : ''); ?></p>
                                <p><strong>Email</strong><br/><a
                                            href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a>
                                </p>
                                <p><strong>Phone</strong><br/><a
                                            href="tel:<?php echo $order->get_billing_phone(); ?>"><?php echo $order->get_billing_phone(); ?></a>
                                </p>
                                <p><strong>Payment via</strong><br/><?php echo $order->get_payment_method_title(); ?>
                                </p>
                            </div>
                            <div class="col-right">
                                <?php if ($group_b || $user->roles[0] == 'administrator') { ?>
                                    <h4>Signature</h4>
                                    <img id="sig-image"
                                         src="<?php echo get_post_meta($p->ID, '_cxecrt_signature', true); ?>"
                                         style="width: 100%; border: 1px solid #ccc;"/>
                                         <p><b>INVOICE NO.:</b></p>
                                        <p class="wcpdf_in"><?php echo $_invoice_number; ?></p>

                                        <p><b>Invoice Date:</b></p>
                                        <p><?php echo date_format( date_create(get_post_meta( $order->get_id(), '_wcpdf_invoice_date_formatted', true)) , "F  d  Y" ); ?></p> 

                                        <p><b>Order Number:</b></p>
                                        <p><?php echo $order->get_order_number(); ?></p>

                                        <p><b>Order Date:</b></p>
                                        <p><?php echo date_format($order->get_date_created() , "F  d  Y"); ?></p>

                                        <p><b>Payment Method:</b></p>
                                        <p><?php echo $order->get_payment_method_title(); ?></p>
                                <?php } ?>
                            </div>
                            <br/>
                            <table class="tbpro" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th align="right">Quantity</th>
                                    <th align="right">Tax</th>
                                    <th align="right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $items = $order->get_items();
                                foreach ($items as $item) {
                                    $formatted_meta_data = $item->get_formatted_meta_data('_', true);
                                    if (function_exists('get_product')) {
                                        if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                            $_product = wc_get_product($item['variation_id']);
                                        else:
                                            $_product = wc_get_product($item['product_id']);
                                        endif;
                                    } else {
                                        if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                            $_product = new WC_Product_Variation($item['variation_id']);
                                        else:
                                            $_product = new WC_Product($item['product_id']);
                                        endif;
                                    }
                                    if (isset($_product) && $_product != false) {
                                        ?>
                                        <tr>
                                            <td><span style="text-transform: capitalize;"><?php echo $_product->get_title(); ?></span>
                                                <br/>
                                                <div class="all-option-expand">
                                                    <?php
                                                    $num = 1;
                                                    $ext = false;
                                                    foreach ($formatted_meta_data as $k => $v) {
                                                        if ($num==1) {
                                                            if(strtolower($v->key)!='sku') {
                                                                $ext = true;
                                                                echo 'SKU : -';
                                                                echo '<div class="btn-expand">Options <span class="arrow2"></span></div><div class="ao-expand more">';
                                                                echo $v->key . ' : ' . $v->value . '<br/>';
                                                            } else {
                                                                echo $v->key . ' : ' . $v->value . '<br/>';
                                                            }
                                                        } else {
                                                            if ($num == 2 && $ext==false) {
                                                                echo '<div class="btn-expand">Options <span class="arrow2"></span></div><div class="ao-expand more">';
                                                            }
                                                            echo $v->key . ' : ' . $v->value . '<br/>';
                                                            if (count($formatted_meta_data) > 2 && count($formatted_meta_data) == $num) {
                                                                echo '</div>';
                                                            }
                                                        }
                                                        $num++;
                                                    } ?>
                                                </div>
                                            </td>
                                            <td align="right"><?php echo($item['quantity'] > 0 ? $item['quantity'] : 0); ?></td>
                                            <td align="right"><?php echo wc_price($item['subtotal_tax']); ?></td>
                                            <td align="right"><?php echo wc_price($item['line_total']); ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php 
                           if( !( $order->get_payment_method() == 'omise_paynow' && ($order->get_status() == 'on-hold' || $order->get_status() == 'failed' || $order->get_status() == 'cancelled' ) ) ) { ?>
                                <input type="button" class="btn btn-download-od" rel="<?php echo $p->ID; ?>"
                                   value="Download"/>
                            <?php 
                            } ?>
                        </div>
                        <?php if ($this->getLinkAWS($p->ID) !== '') {
                            $check_data_form = get_post_meta($p->ID, '_data_artwwork_form', true);
                            parse_str($check_data_form, $searcharray);
                            ?>
                            <div class="tb_artwork_<?php echo $p->ID; ?>">
                                <h4 class="code" rel="<?php echo $p->ID; ?>">#<?php echo $p->ID; ?></h4>
                                <form action="#" method="post" class="frm-data-aa">
                                    <table class="table-aaod">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>Issue</th>
                                            <th>Service</th>
                                            <th style="font-size: 13px;">Able to adjust?</th>
                                            <?php if ($check_data_form) { ?>
                                                <th>Status</th>
                                            <?php } ?>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (count($searcharray) > 0) {
                                            foreach ($searcharray['aa_item_pro'] as $k => $v) { ?>
                                                <tr class="row-aa-oi">
                                                    <td style="font-size: 15px;">Order Item</td>
                                                    <td>
                                                        <select name="aa_item_pro[]">
                                                            <?php
                                                            if($items) {
                                                                foreach ($items as $item) {
                                                                    if (function_exists('get_product')) {
                                                                        if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                                                            $_product = wc_get_product($item['variation_id']);
                                                                        else:
                                                                            $_product = wc_get_product($item['product_id']);
                                                                        endif;
                                                                    } else {
                                                                        if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                                                            $_product = new WC_Product_Variation($item['variation_id']);
                                                                        else:
                                                                            $_product = new WC_Product($item['product_id']);
                                                                        endif;
                                                                    }
                                                                    if (isset($_product) && $_product != false) {
                                                                        if (isset($item['variation_id']) && $item['variation_id'] > 0) {
                                                                            $arr_product[$p->ID]['idpro'] = $item['variation_id'];
                                                                        } else {
                                                                            $arr_product[$p->ID]['idpro'] = $item['product_id'];
                                                                        }
                                                                        $arr_product[$p->ID]['data'] = $_product;
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $arr_product[$p->ID]['idpro']; ?>"
                                                                            <?php if ($searcharray['aa_item_pro'][$k] == $arr_product[$p->ID]['idpro']) { ?> selected <?php } ?>><?php echo $_product->get_title(); ?></option>
                                                                <?php 
                                                                }
                                                            } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="aa_item_issue[]">
                                                            <?php while (have_rows('list_issue', 'option')): the_row(); ?>
                                                                <option value="<?php echo get_sub_field('issue'); ?>"
                                                                        <?php if (str_replace('+', '', $searcharray['aa_item_issue'][$k]) == get_sub_field('issue')) { ?>selected <?php } ?>><?php echo get_sub_field('issue'); ?></option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="aa_item_service[]">
                                                            <?php
                                                            while ($the_query->have_posts()) {
                                                                $the_query->the_post();
                                                                ?>
                                                                <option rel="<?php echo wc_get_product(get_the_ID())->get_price(); ?>"
                                                                        <?php if ($searcharray['aa_item_service'][$k] == get_the_ID()) { ?>selected <?php } ?>
                                                                        value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
                                                                <?php
                                                            }
                                                            wp_reset_postdata();
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="aa_item_ata[]">
                                                            <option value="y"
                                                                    <?php if ($searcharray['aa_item_ata'][$k] == 'y') { ?>selected <?php } ?>>
                                                                Yes
                                                            </option>
                                                            <option value="n"
                                                                    <?php if ($searcharray['aa_item_ata'][$k] == 'n') { ?>selected <?php } ?>>
                                                                No
                                                            </option>
                                                        </select>
                                                    </td>
                                                    <?php if ($check_data_form) { ?>
                                                        <td></td>
                                                    <?php } ?>
                                                    <td>
                                            <span class="act-remove-row"
                                                  style="position: relative; left: 5px; cursor: pointer;">
                                                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                                     width="18.000000pt" height="25.000000pt"
                                                     viewBox="0 0 78.000000 102.000000"
                                                     preserveAspectRatio="xMidYMid meet">
                                                    <g transform="translate(0.000000,102.000000) scale(0.100000,-0.100000)"
                                                       fill="#000000" stroke="none">
                                                        <path d="M280 803 c-16 -6 -38 -33 -55 -68 -9 -16 -21 -20 -63 -20 -61 -1 -79
                                                        -13 -63 -43 6 -11 18 -22 26 -26 14 -5 16 -30 14 -206 -3 -188 -2 -201 16
                                                        -215 16 -11 58 -14 207 -15 l187 0 21 27 c20 26 21 37 19 215 -2 170 0 190 15
                                                        201 11 8 17 23 14 38 -3 22 -7 24 -57 24 -51 0 -57 2 -77 33 -13 18 -24 38
                                                        -27 45 -4 13 -151 21 -177 10z m141 -64 c34 -18 10 -29 -60 -29 -73 0 -87 6
                                                        -70 27 14 16 102 18 130 2z m97 -91 c13 -13 18 -331 6 -362 -5 -14 -29 -16
                                                        -165 -16 -145 0 -159 2 -159 18 0 9 -1 92 -2 183 -1 91 2 171 6 177 10 17 297
                                                        17 314 0z"/>
                                                        <path d="M267 593 c-4 -3 -7 -64 -7 -134 0 -119 1 -128 20 -134 11 -3 24 -3
                                                        30 0 6 4 10 61 10 141 0 134 0 134 -23 134 -13 0 -27 -3 -30 -7z"/>
                                                        <path d="M402 588 c-19 -19 -17 -242 3 -258 8 -7 22 -10 30 -6 23 8 18 269 -5
                                                        274 -9 1 -21 -3 -28 -10z"/>
                                                    </g>
                                                </svg>
                                            </span>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr class="row-aa-oi">
                                                <td style="font-size: 15px;">Order Item</td>
                                                <td>
                                                    <select name="aa_item_pro[]">
                                                        <?php
                                                        foreach ($items as $item) {
                                                            if (function_exists('get_product')) {
                                                                if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                                                    $_product = wc_get_product($item['variation_id']);
                                                                else:
                                                                    $_product = wc_get_product($item['product_id']);
                                                                endif;
                                                            } else {
                                                                if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                                                    $_product = new WC_Product_Variation($item['variation_id']);
                                                                else:
                                                                    $_product = new WC_Product($item['product_id']);
                                                                endif;
                                                            }
                                                            if (isset($_product) && $_product != false) {
                                                                if (isset($item['variation_id']) && $item['variation_id'] > 0) {
                                                                    $arr_product[$p->ID]['idpro'] = $item['variation_id'];
                                                                } else {
                                                                    $arr_product[$p->ID]['idpro'] = $item['product_id'];
                                                                }
                                                                $arr_product[$p->ID]['data'] = $_product;
                                                            }
                                                            ?>
                                                            <option value="<?php echo $arr_product[$p->ID]['idpro']; ?>"><?php echo $_product->get_title(); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="aa_item_issue[]">
                                                        <?php while (have_rows('list_issue', 'option')): the_row(); ?>
                                                            <option value="<?php echo get_sub_field('issue'); ?>"><?php echo get_sub_field('issue'); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="aa_item_service[]">
                                                        <?php
                                                        while ($the_query->have_posts()) {
                                                            $the_query->the_post();
                                                            ?>
                                                            <option rel="<?php echo wc_get_product(get_the_ID())->get_price(); ?>"
                                                                    value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
                                                            <?php
                                                        }
                                                        wp_reset_postdata();
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="aa_item_ata[]">
                                                        <option value="y">Yes</option>
                                                        <option value="n">No</option>
                                                    </select>
                                                </td>
                                                <td>
                                            <span class="act-remove-row"
                                                  style="position: relative; left: 5px; cursor: pointer;">
                                                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                                     width="18.000000pt" height="25.000000pt"
                                                     viewBox="0 0 78.000000 102.000000"
                                                     preserveAspectRatio="xMidYMid meet">
                                                    <g transform="translate(0.000000,102.000000) scale(0.100000,-0.100000)"
                                                       fill="#000000" stroke="none">
                                                        <path d="M280 803 c-16 -6 -38 -33 -55 -68 -9 -16 -21 -20 -63 -20 -61 -1 -79
                                                        -13 -63 -43 6 -11 18 -22 26 -26 14 -5 16 -30 14 -206 -3 -188 -2 -201 16
                                                        -215 16 -11 58 -14 207 -15 l187 0 21 27 c20 26 21 37 19 215 -2 170 0 190 15
                                                        201 11 8 17 23 14 38 -3 22 -7 24 -57 24 -51 0 -57 2 -77 33 -13 18 -24 38
                                                        -27 45 -4 13 -151 21 -177 10z m141 -64 c34 -18 10 -29 -60 -29 -73 0 -87 6
                                                        -70 27 14 16 102 18 130 2z m97 -91 c13 -13 18 -331 6 -362 -5 -14 -29 -16
                                                        -165 -16 -145 0 -159 2 -159 18 0 9 -1 92 -2 183 -1 91 2 171 6 177 10 17 297
                                                        17 314 0z"/>
                                                        <path d="M267 593 c-4 -3 -7 -64 -7 -134 0 -119 1 -128 20 -134 11 -3 24 -3
                                                        30 0 6 4 10 61 10 141 0 134 0 134 -23 134 -13 0 -27 -3 -30 -7z"/>
                                                        <path d="M402 588 c-19 -19 -17 -242 3 -258 8 -7 22 -10 30 -6 23 8 18 269 -5
                                                        274 -9 1 -21 -3 -28 -10z"/>
                                                    </g>
                                                </svg>
                                            </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </form>
                                <div class="btn-add-row">+</div>
                                <h2>Total (<?php echo get_woocommerce_currency_symbol(); ?>) : <span class="sum_price">0.00</span>
                                </h2>
                                <input type="button" class="btn btn-update-status-od" rel="<?php echo $p->ID; ?>"
                                       value="Update Status"/>
                            </div>
                        <?php }
                    }
                }
                ?>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.js"></script>
            <script>
                jQuery(document).ready(function () {
                    jQuery(".download-aws").click(function () {
                        var tr_reup = jQuery(this).parents('tr.order-reupload-aw');
                        var id_download = jQuery(this).attr("id-order");
                        jQuery.ajax({
                            type: "post", //Phương thức truyền post hoặc get
                            dataType: "json", //Dạng dữ liệu trả về xml, json, script, or html
                            url: '<?php echo admin_url('admin-ajax.php'); ?>', //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
                            data: {
                                action: "getDownloadAWS", //Tên action
                                id_order: id_download, //Biến truyền vào xử lý. $_POST['website']
                            },
                            context: this,
                            beforeSend: function () {
                                //Làm gì đó trước khi gửi dữ liệu vào xử lý
                            },
                            success: function (response) {
                                //Làm gì đó khi dữ liệu đã được xử lý
                                if (response.success) {
                                    if (response.data == 'false') {
                                        alert('There was an error');
                                    }
                                    if (response.data !== 'false') {
                                        if(tr_reup.length>0) {
                                            tr_reup.attr('class', tr_reup.find('td[fulltable-field-name="order_type"] span').text().toLowerCase().replace(' ','-'));
                                        }
                                        window.location.replace(response.data);
                                    }
                                } else {
                                    alert('There was an error');
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                //Làm gì đó khi có lỗi xảy ra
                                console.log('The following error occured: ' + textStatus, errorThrown);
                            }
                        })
                    });
                });
            </script>
            <div class="paginate_od">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('cpage', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo;'),
                    'next_text' => __('&raquo;'),
                    'total' => ceil($total / $items_per_page),
                    'current' => $page,
                ));
                ?>
            </div>
            <?php
        }

        public function admin_page()
        {

            // Save settings if data has been posted
            if (!empty($_POST)) {
                $this->save_settings();
            }

            // Add any posted messages
            if (!empty($_GET['wc_error'])) {
                //self::add_error( stripslashes( $_GET['wc_error'] ) );
                if (!empty($_GET['wc_message'])) {
                    //self::add_message( stripslashes( $_GET['wc_message'] ) );
                    //self::show_messages();
                }
            }
            ?>
            <form method="post" id="mainform" action="" enctype="multipart/form-data">
                <div class="cxecrt-wrap cxecrt-wrap-settings woocommerce">

                    <a class="cxecrt-back" href="<?php echo esc_url(admin_url('edit.php?post_type=stored-carts')); ?>">
                        <span class="dashicons dashicons-arrow-left"></span> Back</a>

                    <h1><?php _e('Quotations', 'email-cart'); ?><span
                                class="dashicons dashicons-arrow-right"></span><?php _e('Settings', 'email-cart'); ?>
                    </h1>

                    <?php
                    $settings = $this->get_settings();
                    WC_Admin_Settings::output_fields($settings);
                    ?>

                    <p class="submit">
                        <input name="save" class="button-primary" type="submit"
                               value="<?php _e('Save changes', 'email-cart'); ?>"/>
                        <?php wp_nonce_field('woocommerce-settings'); ?>
                    </p>

                </div>
            </form>

            <?php
        }

        /**
         * Get settings array
         *
         * @return array
         */
        public static function get_settings()
        {
            global $cxecrt_settings;

            if (isset($cxecrt_settings)) {
                return $cxecrt_settings;
            }

            $settings = array(
                // --------------------

                array(
                    'id' => 'cxecrt_settings',
                    'name' => __('General Settings', 'email-cart'),
                    'type' => 'title',
                    'desc' => '',
                ),
                array(
                    'id' => 'cxecrt_cart_expiration_active',
                    'name' => __('Automatically Delete Old Carts', 'email-cart'),
                    'label' => '',
                    'desc' => __('', 'email-cart'),
                    'desc_tip' => __('Saving a large number of carts can lead to a large database. Automatically clearing the cart list will keep this under control. Check this on to set number of days.', 'email-cart'),
                    'type' => 'checkbox',
                    'default' => 'no',
                ),
                array(
                    'id' => 'cxecrt_cart_expiration_time',
                    'name' => __('Automatically delete carts older than (days)', 'email-cart'),
                    'desc' => __('Any cart that becomes older than the number of days specified will be automatically deleted. In the off chance that a customer attempts to retrieved an old cart a friendly notice will be displayed.', 'email-cart'),
                    'type' => 'number',
                    'default' => '0',
                ),
                array(
                    'id' => 'cxecrt_settings',
                    'type' => 'sectionend',
                ),
                // --------------------
                //          array(
                //              'id'   => 'cxecrt_interface_settings_title',
                //              'name' => __( "Main Interface (Popup Modal & Button)", 'email-cart' ),
                //              'desc' => '',
                //              'type' => 'title',
                //
                //          ),
                //          array(
                //              'id'       => 'cxecrt_show_cart_page_button',
                //              'name'     => __( "Show Button on Cart Page", 'email-cart' ),
                //              'desc'     => __( "", 'email-cart' ),
                //              'desc_tip' => __( "If you don't like the look of our button you can choose not to show it, then create your own button linking to your share cart page like this http://....../cart/#email-cart (#email-cart is essential).", 'email-cart' ),
                //              'type'     => 'checkbox',
                //              'default'  => 'yes',
                //          ),
                //          array(
                //              'id'          => 'cxecrt_button_text',
                //              'name'        => __( 'Button Text', 'email-cart' ),
                //              'desc'        => '&nbsp;',
                //              'type'        => 'text',
                //              'default'     => __( "Save & Share Cart", 'email-cart' ),
                //              'placeholder' => __( "Save & Share Cart", 'email-cart' ),
                //              'css'         => 'min-width:500px;',
                //              'autoload'    => false,
                //          ),
                //          array(
                //              'id'          => 'cxecrt_popop_title_text',
                //              'name'        => __( "Popup Modal Title", 'email-cart' ),
                //              'desc'        => '&nbsp;',
                //              'type'        => 'text',
                //              'default'     => __( "Save & Share Cart", 'email-cart' ),
                //              'placeholder' => __( "Save & Share Cart", 'email-cart' ),
                //              'css'         => 'min-width:500px;',
                //              'autoload'    => false,
                //          ),
                //          array(
                //              'id'          => 'cxecrt_popop_intro_text',
                //              'name'        => __( "Popup Modal Intro", 'email-cart' ),
                //              'desc'        => '',
                //              'type'        => 'textarea',
                //              'default'     => __( "Your Shopping Cart will be saved and you'll be given a link. You, or anyone with the link, can use it to retrieve your Cart at any time.", 'email-cart' ),
                //              'placeholder' => __( "Your Shopping Cart will be saved and you'll be given a link. You, or anyone with the link, can use it to retrieve your Cart at any time.", 'email-cart' ),
                //              'css'         => 'height: 100px;',
                //              'autoload'    => false,
                //          ),
                //          array(
                //              'id'          => 'cxecrt_popop_send_email_intro_text',
                //              'name'        => __( "Popup Modal 'Send Cart' Intro", 'email-cart' ),
                //              'desc'        => '',
                //              'type'        => 'textarea',
                //              'default'     => __( "Your Shopping Cart will be saved with Product pictures and information, and Cart Totals. Then send it to yourself, or a friend, with a link to retrieve it at any time.", 'email-cart' ),
                //              'placeholder' => __( "Your Shopping Cart will be saved with Product pictures and information, and Cart Totals. Then send it to yourself, or a friend, with a link to retrieve it at any time.", 'email-cart' ),
                //              'css'         => 'height: 100px;',
                //              'autoload'    => false,
                //          ),
                //
                //          array(
                //              'id'   => 'cxecrt_interface_settings_title',
                //              'type' => 'sectionend',
                //          ),
                // --------------------
                array(
                    'id' => 'cxecrt_specialist_email_settings_title',
                    'name' => __("Email Settings (for Specialist)", 'email-cart'),
                    'desc' => __("System will generate email & send to specialist to inform them about quote of customer disapproved.", 'email-cart'),
                    'type' => 'title',
                ),
                array(
                    'id' => 'cxecrt_specialist_email_subject',
                    'name' => __('Subject', 'email-cart'),
                    'desc' => __("This is the pre-populated text in the 'Subject' field for Customers emailing their cart. They can then personlize it before sending.", 'email-cart'),
                    'type' => 'text',
                    'default' => sprintf(__("Shopping Cart sent to you via %s", 'email-cart'), get_bloginfo("name")),
                    'placeholder' => sprintf(__("Shopping Cart sent to you via %s", 'email-cart'), get_bloginfo("name")),
                    'css' => 'min-width:500px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_specialist_email_message',
                    'name' => __('Message', 'email-cart'),
                    'desc' => __("This is the pre-populated text in the 'Message' field for Customers emailing their cart. They can then personlize it before sending. The Email message will be styled using the WooCommerce Email Template and the cart added - with it's product pictures and totals.", 'email-cart'),
                    'type' => 'textarea',
                    'default' => sprintf(__("Have a look at this Shopping Cart sent by a friend via %s", 'email-cart'), get_bloginfo("name")),
                    'placeholder' => sprintf(__("Have a look at this Shopping Cart sent by a friend via %s", 'email-cart'), get_bloginfo("name")),
                    'css' => 'height: 100px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_specialist_email_settings_title',
                    'type' => 'sectionend',
                ),
                // --------------------
                array(
                    'id' => 'cxecrt_customer_email_settings_title',
                    'name' => __("Email Settings (for Customers and Guests)", 'email-cart'),
                    'desc' => __("System will generate email & send to customer to inform them about approval.", 'email-cart'),
                    'type' => 'title',
                ),
                array(
                    'id' => 'cxecrt_customer_email_subject',
                    'name' => __('Subject', 'email-cart'),
                    'desc' => __("This is the pre-populated text in the 'Subject' field for Customers emailing their cart. They can then personlize it before sending.", 'email-cart'),
                    'type' => 'text',
                    'default' => sprintf(__("Shopping Cart sent to you via %s", 'email-cart'), get_bloginfo("name")),
                    'placeholder' => sprintf(__("Shopping Cart sent to you via %s", 'email-cart'), get_bloginfo("name")),
                    'css' => 'min-width:500px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_customer_email_message',
                    'name' => __('Message', 'email-cart'),
                    'desc' => __("This is the pre-populated text in the 'Message' field for Customers emailing their cart. They can then personlize it before sending. The Email message will be styled using the WooCommerce Email Template and the cart added - with it's product pictures and totals.", 'email-cart'),
                    'type' => 'textarea',
                    'default' => sprintf(__("Have a look at this Shopping Cart sent by a friend via %s", 'email-cart'), get_bloginfo("name")),
                    'placeholder' => sprintf(__("Have a look at this Shopping Cart sent by a friend via %s", 'email-cart'), get_bloginfo("name")),
                    'css' => 'height: 100px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_customer_email_settings_title',
                    'type' => 'sectionend',
                ),
                // --------------------
                array(
                    'id' => 'cxecrt_admin_email_settings_title',
                    'name' => __("Email Settings (for CS)", 'email-cart'),
                    'desc' => __("When new qtn. submitted, CS will receive an email informing them.", 'email-cart'),
                    'type' => 'title',
                ),
                array(
                    'id' => 'cxecrt_admin_email_cs',
                    'name' => __('Email CS', 'email-cart'),
                    'desc' => __("Email CS will receive an email informing when new quotation submitted. Each email is separated by a sign ','", 'email-cart'),
                    'default' => '',
                    'placeholder' => '',
                    'type' => 'text',
                    'css' => 'min-width:500px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_admin_email_subject',
                    'name' => __('Subject', 'email-cart'),
                    'desc' => __("This is the pre-populated text in the 'Subject' field for Shop Managers emailing their cart. They can then personlize it before sending.", 'email-cart'),
                    'default' => sprintf(__("Shopping Cart sent to you by %s", 'email-cart'), get_bloginfo("name")),
                    'placeholder' => sprintf(__("Shopping Cart sent to you by %s", 'email-cart'), get_bloginfo("name")),
                    'type' => 'text',
                    'css' => 'min-width:500px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_admin_email_message',
                    'name' => __('Message', 'email-cart'),
                    'desc' => __("This is the pre-populated text in the 'Message' field for Shop Managers emailing their cart. They can then personlize it before sending. The Email message will be styled using the WooCommerce Email Template and the cart added - with it's product pictures and totals.", 'email-cart'),
                    'default' => sprintf(__("Have a look at this Shopping Cart from %s", 'email-cart'), get_bloginfo("name")),
                    'placeholder' => sprintf(__("Have a look at this Shopping Cart from %s", 'email-cart'), get_bloginfo("name")),
                    'type' => 'textarea',
                    'css' => 'height: 100px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_admin_email_settings_title',
                    'type' => 'sectionend',
                ),
                // --------------------
                array(
                    'id' => 'cxecrt_admin_pdf_settings_title',
                    'name' => __("PDF Settings", 'email-cart'),
                    'desc' => '',
                    'type' => 'title',
                ),
                array(
                    'id' => 'cxecrt_admin_url_logo',
                    'name' => __('URL Logo', 'email-cart'),
                    'desc' => __("URL logo image in pdf file.", 'email-cart'),
                    'default' => '',
                    'placeholder' => '',
                    'type' => 'text',
                    'css' => 'min-width:500px;',
                    'autoload' => false,
                ),
                array(
                    'id' => 'cxecrt_admin_email_settings_title',
                    'type' => 'sectionend',
                ),
            );

            return $settings;
        }

        /**
         * Save Settings.
         *
         * Loops though the woocommerce options array and outputs each field.
         *
         * @access public
         * @return bool
         */
        public static function save_settings()
        {

            if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'woocommerce-settings')) {
                die(__('Action failed. Please refresh the page and retry.', 'email-cart'));
            }

            $settings = self::get_settings();

            if (empty($_POST)) {
                return false;
            }

            // Options to update will be stored here
            $update_options = array();

            // Loop options and get values to save
            foreach ($settings as $value) {

                if (!isset($value['id'])) {
                    continue;
                }

                $type = isset($value['type']) ? sanitize_title($value['type']) : '';

                // Get the option name
                $option_value = null;

                switch ($type) {

                    // Standard types
                    case "checkbox":

                        if (isset($_POST[$value['id']])) {
                            $option_value = 'yes';
                        } else {
                            $option_value = 'no';
                        }

                        break;

                    case "textarea":

                        if (isset($_POST[$value['id']])) {
                            $option_value = wp_kses_post(trim(stripslashes($_POST[$value['id']])));
                        } else {
                            $option_value = '';
                        }

                        break;

                    case "text":
                    case 'email':
                    case 'number':
                    case "select":
                    case "color":
                    case 'password':
                    case "single_select_page":
                    case "single_select_country":
                    case 'radio':

                        if ($value['id'] == 'woocommerce_price_thousand_sep' || $value['id'] == 'woocommerce_price_decimal_sep') {

                            // price separators get a special treatment as they should allow a spaces (don't trim)
                            if (isset($_POST[$value['id']])) {
                                $option_value = wp_kses_post(stripslashes($_POST[$value['id']]));
                            } else {
                                $option_value = '';
                            }
                        } elseif ($value['id'] == 'woocommerce_price_num_decimals') {

                            // price separators get a special treatment as they should allow a spaces (don't trim)
                            if (isset($_POST[$value['id']])) {
                                $option_value = absint($_POST[$value['id']]);
                            } else {
                                $option_value = 2;
                            }
                        } elseif ($value['id'] == 'woocommerce_hold_stock_minutes') {

                            // Allow > 0 or set to ''
                            if (!empty($_POST[$value['id']])) {
                                $option_value = absint($_POST[$value['id']]);
                            } else {
                                $option_value = '';
                            }

                            wp_clear_scheduled_hook('woocommerce_cancel_unpaid_orders');

                            if ($option_value != '') {
                                wp_schedule_single_event(time() + (absint($option_value) * 60), 'woocommerce_cancel_unpaid_orders');
                            }
                        } else {

                            if (isset($_POST[$value['id']])) {
                                $option_value = woocommerce_clean(stripslashes($_POST[$value['id']]));
                            } else {
                                $option_value = '';
                            }
                        }

                        break;

                    // Special types
                    case "multiselect":
                    case "multi_select_countries":

                        // Get countries array
                        if (isset($_POST[$value['id']])) {
                            $selected_countries = array_map('wc_clean', array_map('stripslashes', (array)$_POST[$value['id']]));
                        } else {
                            $selected_countries = array();
                        }

                        $option_value = $selected_countries;

                        break;

                    case "image_width":

                        if (isset($_POST[$value['id']]['width'])) {

                            $update_options[$value['id']]['width'] = woocommerce_clean(stripslashes($_POST[$value['id']]['width']));
                            $update_options[$value['id']]['height'] = woocommerce_clean(stripslashes($_POST[$value['id']]['height']));

                            if (isset($_POST[$value['id']]['crop'])) {
                                $update_options[$value['id']]['crop'] = 1;
                            } else {
                                $update_options[$value['id']]['crop'] = 0;
                            }
                        } else {
                            $update_options[$value['id']]['width'] = $value['default']['width'];
                            $update_options[$value['id']]['height'] = $value['default']['height'];
                            $update_options[$value['id']]['crop'] = $value['default']['crop'];
                        }

                        break;

                    // Custom handling
                    default:

                        do_action('woocommerce_update_option_' . $type, $value);

                        break;
                }

                if (!is_null($option_value)) {
                    // Check if option is an array
                    if (strstr($value['id'], '[')) {

                        parse_str($value['id'], $option_array);

                        // Option name is first key
                        $option_name = current(array_keys($option_array));

                        // Get old option value
                        if (!isset($update_options[$option_name])) {
                            $update_options[$option_name] = get_option($option_name, array());
                        }

                        if (!is_array($update_options[$option_name])) {
                            $update_options[$option_name] = array();
                        }

                        // Set keys and value
                        $key = key($option_array[$option_name]);

                        $update_options[$option_name][$key] = $option_value;

                        // Single value
                    } else {
                        $update_options[$value['id']] = $option_value;
                    }
                }

                // Custom handling
                do_action('woocommerce_update_option', $value);
            }

            // Now save the options
            foreach ($update_options as $name => $value) {

                $current_option = get_option($name);
                $current_default = cxecrt_get_default($name);

                if ($value === $current_default) {
                    delete_option($name);
                } else if ($value !== $current_option) {
                    update_option($name, $value);
                }
            }

            return true;
        }

        public function getLinkAWS($id_order)
        {
            global $wpdb;
            if (is_plugin_active('upload-aws-custom-db/upload-aws-custom-db.php')) {
                $sql = "SELECT link_download FROM $wpdb->prefix" . "aws_order_download WHERE id_order = $id_order";
                $results = $wpdb->get_results($sql);
                if (isset($results[0])) {
                    $link = $results[0]->link_download;
                    $link = preg_replace("/^http:/i", "https:", $link);
                    return $link;
                }
            }
            return '';
        }

    }

endif;

return new WC_Email_Cart_Settings();
                        