<?php
/*
 * interfaces.php - cart list page modifications
 *
 *
 */

class WCEC_Cart_Index_Page {

    public function __construct() {
        global $start_date, $end_date;

        $current_month = date("j/n/Y", mktime(0, 0, 0, 1, date("m"), date("Y")));

        $start_date = ( isset($_GET['start_date']) ) ? $_GET['start_date'] : '';
        $end_date = ( isset($_GET['end_date']) ) ? $_GET['end_date'] : '';

        if (!$start_date)
            $start_date = $current_month;
        if (!$end_date)
            $end_date = date('Ymd', current_time('timestamp'));

        $start_date = strtotime($start_date);
        $end_date = strtotime($end_date);

		if ( ! get_transient( 'my_schedule_botaksign_auto_delete_quote' ) ) {
			set_transient( 'my_schedule_botaksign_auto_delete_quote', true, 1440 * MINUTE_IN_SECONDS );
			add_action( 'init', array($this, 'my_schedule_botaksign_auto_delete_quote') );
		}
        // Add custom filter to post list.
        /*
          add_filter( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ), 1000 );
          add_filter( 'posts_where', array( $this, 'filter_where' ) );
         */

        add_action('admin_menu', array($this, 'hide_add_new_carts'));
        // add_action( 'views_edit-stored-carts', array( $this,'cxecrt_remove_cart_views' ) ); // Remove the All / Published / Trash view.
        add_action('manage_stored-carts_posts_custom_column', array($this, 'cxecrt_manage_cart_columns'), 1, 1);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_index'));

        add_filter('manage_edit-stored-carts_columns', array($this, 'cxecrt_carts_columns'));
        add_filter('manage_edit-stored-carts_sortable_columns', array($this, 'cxecrt_carts_sort'));
        add_filter('request', array($this, 'cart_column_orderby'));
        // add_filter( 'bulk_actions-edit-' . 'stored-carts', '__return_empty_array' ); // Remove bulk edit
        add_filter('parse_query', array($this, 'woocommerce_carts_search_custom_fields'));
        add_filter('get_search_query', array($this, 'woocommerce_carts_search_label'));

        // Top interface
        //add_action( 'admin_notices', array( $this, 'render_top_interface' ) );
    }
	
	function my_schedule_botaksign_auto_delete_quote(){
		global $wpdb, $cxecrt_options;
		$expiration_days = $cxecrt_options['cxecrt_cart_expiration_time'];
		$opt_in_settings = $cxecrt_options['cxecrt_cart_expiration_active'];
		if($expiration_days && $opt_in_settings) {
			$tems = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'stored-carts' AND `post_date` < NOW() - INTERVAL {$expiration_days} DAY");
			if(count($tems)>0) {
				$arr_id = [];
				foreach($tems as $p) {
					array_push($arr_id, $p->ID);
				}
				if(count($arr_id)>0) {
					$delete_meta_sql = "DELETE FROM " . $wpdb->prefix . "postmeta WHERE post_id IN(" . implode(',', $arr_id) . ")";
					$wpdb->query($delete_meta_sql);
					$delete_sql = "DELETE FROM " . $wpdb->prefix . "posts WHERE ID IN(" . implode(',', $arr_id) . ")";
					$wpdb->query($delete_sql);
				}
			}
		}
	}

    /*
     * Include require init scripts for the index page.
     */

    function woocommerce_carts_search_label($query) {
        global $pagenow, $typenow;

        if ('edit.php' != $pagenow)
            return $query;
        if ($typenow != 'stored-carts')
            return $query;
        if (!get_query_var('cart_search'))
            return $query;

        return $_GET['s'];
    }

    function woocommerce_carts_search_custom_fields($wp) {
        global $pagenow, $wpdb;

        if ('edit.php' != $pagenow)
            return $wp;
        if (!isset($wp->query_vars['s']) || !$wp->query_vars['s'])
            return $wp;
        if ($wp->query_vars['post_type'] != 'stored-carts')
            return $wp;

        $search_fields = array(
            '_cxecrt_cart_items'
        );

        // Query matching custom fields - this seems faster than meta_query
        $post_ids = $wpdb->get_col($wpdb->prepare('SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key IN ( ' . '"' . implode('","', $search_fields) . '"' . ' ) AND meta_value LIKE "%%%s%%"', esc_attr($_GET['s'])));
        // Query matching excerpts and titles
        $post_ids = array_merge($post_ids, $wpdb->get_col($wpdb->prepare('
			SELECT ' . $wpdb->posts . '.ID
			FROM ' . $wpdb->posts . '
			LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id
			LEFT JOIN ' . $wpdb->users . ' ON ' . $wpdb->postmeta . '.meta_value = ' . $wpdb->users . '.ID
			WHERE
				post_excerpt 	LIKE "%%%1$s%%" OR
				post_title 		LIKE "%%%1$s%%" OR
				user_login		LIKE "%%%1$s%%" OR
				user_nicename	LIKE "%%%1$s%%" OR
				user_email		LIKE "%%%1$s%%" OR
				display_name	LIKE "%%%1$s%%"
			', esc_attr($_GET['s'])
                )));

        // Add ID
        $search_order_id = str_replace('Order #', '', $_GET['s']);
        if (is_numeric($search_order_id))
            $post_ids[] = $search_order_id;

        // Add blank ID so not all results are returned if the search finds nothing
        $post_ids[] = 0;

        // Remove s - we don't want to search order name
        unset($wp->query_vars['s']);

        // so we know we're doing this
        $wp->query_vars['cart_search'] = true;

        // Search by found posts
        $wp->query_vars['post__in'] = $post_ids;
    }

    public function enqueue_index() {
        global $pagenow;

        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'stored-carts') {

            wp_enqueue_script('woocommerce_admin');
            wp_enqueue_script('jquery');

            wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');
            //wp_enqueue_style( 'email_cart_admin_index_css', plugins_url() . '/woocommerce-email-cart/assets/css/email_cart_admin_index.css' );
        }
    }

    /**
     * hide_add_new_carts()
     *
     * Hide the "New Carts" link
     */
    public function hide_add_new_carts() {
        global $submenu;
        // replace my_type with the name of your post type
        unset($submenu['edit.php?post_type=stored-carts'][10]);
    }

    /*
     *
     * cxecrt_carts_columns( $columns )
     *
     * Rename Columns for the new "Cart" post type
     */

    public function cxecrt_carts_columns($columns) {
        global $cxecrt_options;

        $columns = array(
            'cb' => 'checky',
            'cart_no' => __('Quotation No.', 'email-cart'),
            'cart_status' => __('Status', 'email-cart'),
            // 'title'       => $columns['title'],
            'cart_sent' => __('Date Created', 'email-cart'),
            'cart_retrieved' => __('Expiry', 'email-cart'),
            'cart_author' => __('Requested By', 'email-cart'),
            'cart_company' => __('Company', 'email-cart'),
            'cart_assignment' => __('Assignment', 'email-cart'),
                //'actions'        => __( '&nbsp;', 'email-cart' ),
        );

        if ($cxecrt_options['show_products_on_index']) {
            // $columns['products'] = __( 'Products', 'email-cart' );
        }

        return $columns;
    }

    /**
     * my_edit_carts_columns( $columns )
     *
     * Declare our new columns as sortable columns ( except the action column, for obvious reasons )
     */
    public function cxecrt_carts_sort($columns) {

        $custom = array(
            'cart_sent' => 'cart_sent',
        );

        return wp_parse_args($custom, $columns);
    }

    /*
     *
     * cart_column_orderby( $vars )
     *
     * Hook for the actual sorting on the custom columns (when the post request comes back)
     *
     */

    public function cart_column_orderby($vars) {

        return $vars;
    }

    /*
     *
     * cxecrt_remove_cart_views( $views )
     *
     * Remove drag-over action items on carts page
     *
     */

    public function cxecrt_remove_cart_views($views) {

        unset($views['all']);
        unset($views['publish']);
        unset($views['trash']);
        return $views;
    }

    /*
     *
     * cxecrt_manage_cart_columns( $column, $post_id )
     *
     * Add cases for our custom columns (status, updated, actions )
     *
     */

    public function cxecrt_manage_cart_columns($column, $post_id = '') {
        global $post, $cxecrt_options;
        $user = get_userdata( get_current_user_id() );
        $cart = new WCEC_Saved_Cart();
        $cart->load_saved_cart($post->ID);

        $post_url = admin_url('post.php?post=' . $post->ID . '&action=edit');

        $expiration_days = $cxecrt_options['cxecrt_cart_expiration_time'];
        $opt_in_settings = $cxecrt_options['cxecrt_cart_expiration_active'];

        switch ($column) {

            case 'cart_no':
                ?>
                <a class="button btn-download-quotation" title="Download / View" rel="<?php echo $post->ID; ?>">
                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                         width="12.000000pt" height="9.000000pt" viewBox="0 0 126.000000 99.000000"
                         preserveAspectRatio="xMidYMid meet">
                        <g transform="translate(0.000000,99.000000) scale(0.100000,-0.100000)"
                           fill="#0073aa" stroke="none">
                            <path d="M175 847 c-3 -6 -4 -181 -3 -387 l3 -375 475 0 475 0 0 325 0 325
                                  -209 3 -210 2 -62 60 -62 60 -201 0 c-152 0 -203 -3 -206 -13z m537 -344 l3
                                  -118 60 -3 c33 -1 66 -5 74 -8 10 -3 -17 -36 -84 -104 -55 -55 -104 -100 -109
                                  -100 -18 0 -206 194 -197 203 5 5 35 10 68 11 l58 1 3 118 3 117 59 0 59 0 3
                                  -117z"/>
                        </g>
                    </svg>
                </a>
                <a class="button btn-save-quotation" title="Update / Save" rel="<?php echo $post->ID; ?>">
                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                         width="12.000000pt" height="11.000000pt" viewBox="0 0 126.000000 117.000000"
                         preserveAspectRatio="xMidYMid meet">
                        <g transform="translate(0.000000,117.000000) scale(0.100000,-0.100000)"
                           fill="#0073aa" stroke="none">
                            <path d="M140 560 l0 -480 480 0 480 0 0 423 0 422 -56 58 -56 57 -424 0 -424
                                  0 0 -480z m181 213 l-3 -153 272 0 271 0 -2 144 c-1 79 1 148 5 155 10 16 74
                                  3 97 -20 17 -17 19 -42 19 -355 1 -185 -1 -338 -3 -340 -5 -5 -708 -6 -713 -1
                                  -3 3 -7 694 -5 720 1 5 16 7 34 5 l32 -3 -4 -152z m417 141 c4 -5 6 -60 4
                                  -121 l-3 -113 -59 0 -58 0 -4 113 c-2 61 -1 117 3 122 9 13 107 12 117 -1z
                                  m-250 -2 c7 -5 15 -31 18 -59 7 -55 -14 -103 -45 -103 -31 0 -46 92 -25 158 4
                                  14 32 16 52 4z"/>
                        </g>
                    </svg>
                </a>
                <a class="button btn-delete-quotation" title="Delete" rel="<?php echo $post->ID; ?>">
                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                         width="10.000000pt" height="11.000000pt" viewBox="0 0 105.000000 117.000000"
                         preserveAspectRatio="xMidYMid meet">
                        <g transform="translate(0.000000,117.000000) scale(0.100000,-0.100000)"
                           fill="#0073aa" stroke="none">
                            <path d="M267 1033 c-33 -33 -37 -42 -37 -90 l0 -53 -56 0 c-51 0 -59 -4 -105
                                  -42 -69 -60 -66 -78 11 -78 l60 0 0 -313 c0 -291 1 -315 18 -330 17 -15 55
                                  -17 344 -17 302 0 326 1 341 18 15 16 17 54 17 330 l0 312 60 0 c54 0 60 2 60
                                  21 0 12 -18 38 -41 60 -39 37 -45 39 -105 39 l-64 0 0 53 c0 48 -4 57 -37 90
                                  l-36 37 -197 0 -197 0 -36 -37z m390 -41 c21 -16 35 -83 21 -97 -8 -8 -344 -9
                                  -357 -1 -16 10 0 80 23 98 18 15 43 18 156 18 113 0 139 -3 157 -18z m141
                                  -519 l-3 -298 -295 0 -295 0 -3 298 -2 297 300 0 300 0 -2 -297z"/>
                            <path d="M320 470 l0 -240 30 0 30 0 0 240 0 240 -30 0 -30 0 0 -240z"/>
                            <path d="M470 470 l0 -240 30 0 30 0 0 240 0 240 -30 0 -30 0 0 -240z"/>
                            <path d="M620 470 l0 -240 30 0 30 0 0 240 0 240 -30 0 -30 0 0 -240z"/>
                        </g>
                    </svg>
                </a>
                <?php
                echo $post->ID;
                break;

            case 'cart_status':
                $cart_status = get_post_meta($post->ID, '_cxecrt_status', true);
                ?>
                <select id="cart_status" name="cart_status">
                    <option value="0" <?php selected($cart_status, 0); ?>>Pending</option>
                    <option value="1" <?php selected($cart_status, 1); ?>>Approved</option>
					<option value="2" <?php selected($cart_status, 2); ?>>Disapproved</option>
                </select>
                <?php
                break;

            case 'cart_assignment':
                $args = array(
                    'role' => 'specialist',
                    'orderby' => 'user_nicename',
                    'order' => 'ASC'
                );
                $users = get_users($args);
                $cart_assignment = get_post_meta($post->ID, '_cxecrt_cart_assignment', true);
                echo '<select id="cart_assignment" name="cart_assignment" '.($user->roles[0]=='administrator'?'':'disabled="disabled"').'><option value="0">Choose Specialist </option>';
                if (count($users)>0) {
                    foreach ($users as $user) {
                        ?>
                        <option value="<?php echo $user->ID; ?>" <?php selected($cart_assignment, $user->ID); ?>><?php echo $user->display_name; ?></option>
                        <?php
                    }
                } else {
                    if ($user->roles[0]=='specialist') {
                        ?>
                        <option value="<?php echo $user->ID; ?>" selected><?php echo $user->display_name; ?></option>
                        <?php
                    }
                }
                echo '</select>';
                break;

            case 'cart_author':

                echo $cart->get_cart_author_display();

                break;

            case 'cart_company':
                echo get_user_meta($cart->cart_author_id, 'billing_company', '')[0];
                break;

            case 'cart_name':

                echo $cart->get_cart_title();

                break;

            case 'cart_sent' :
                $full_date = date('d / m / Y', strtotime($post->post_date));
                ?>
                <span class="dashicons dashicons-calendar"></span>
                <?php echo esc_attr($full_date); ?>
                <?php
                break;

            case 'cart_retrieved' :
                if ($expiration_days && $expiration_days > 0 && $opt_in_settings) {
                    $date = date('Y-m-d', strtotime($post->post_date));
                    $exp_date = date('d / m / Y', strtotime($date . ' + ' . $expiration_days . ' days'));
                    ?>
                    <span title="<?php echo esc_attr($full_date); ?>">
                        <span class="dashicons dashicons-calendar"></span> <?php echo esc_attr($exp_date); ?>
                    </span>
                    <?php
                }
                break;

            case 'products' :

                $cartitems = get_post_meta($post->ID, '_cxecrt_cart_items', true);
                $items_arr = str_replace(array('O:17:"WC_Product_Simple"', 'O:10:"WC_Product"'), 'O:8:"stdClass"', $cartitems);

                if (isset($cartitems) && $cartitems != false) {
                    $order_items = (array) maybe_unserialize($items_arr);
                } else {
                    break;
                }

                $loop = 0;

                if (sizeof($order_items) > 0 && $order_items != false) {
                    foreach ($order_items as $item) :


                        if (function_exists('get_product')) {
                            if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                                $_product = get_product($item['variation_id']);
                            else :
                                $_product = get_product($item['product_id']);
                            endif;
                        }
                        else {
                            if (isset($item['variation_id']) && $item['variation_id'] > 0) :
                                $_product = new WC_Product_Variation($item['variation_id']);
                            else :
                                $_product = new WC_Product($item['product_id']);
                            endif;
                        }
                        if (isset($_product) && $_product != false) {
                            echo "<a href='" . get_admin_url('', 'post.php?post=' . $_product->id . '&action=edit') . "'>" . $_product->get_title() . "</a>";
                            if (isset($_product->variation_data)) {
                                echo ' ( ' . wc_get_formatted_variation($_product->get_variation_data(), true) . ' )';
                            }
                            if ($item['quantity'] > 1)
                                echo " x" . $item['quantity'];
                        }
                        if ($loop < sizeof($order_items) - 1)
                            echo ", ";
                        $loop++;
                    endforeach;
                }
                else {
                    echo "<span style='color:lightgray;'>" . __("No Products", "email-cart") . "</span>";
                }

                break;

            case 'actions':
                ?>
                <a class="button btn-delete-quotation">
                    <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                         width="15.000000pt" height="17.000000pt" viewBox="0 0 105.000000 117.000000"
                         preserveAspectRatio="xMidYMid meet">
                        <g transform="translate(0.000000,117.000000) scale(0.100000,-0.100000)"
                           fill="#000000" stroke="none">
                            <path d="M267 1033 c-33 -33 -37 -42 -37 -90 l0 -53 -56 0 c-51 0 -59 -4 -105
                                  -42 -69 -60 -66 -78 11 -78 l60 0 0 -313 c0 -291 1 -315 18 -330 17 -15 55
                                  -17 344 -17 302 0 326 1 341 18 15 16 17 54 17 330 l0 312 60 0 c54 0 60 2 60
                                  21 0 12 -18 38 -41 60 -39 37 -45 39 -105 39 l-64 0 0 53 c0 48 -4 57 -37 90
                                  l-36 37 -197 0 -197 0 -36 -37z m390 -41 c21 -16 35 -83 21 -97 -8 -8 -344 -9
                                  -357 -1 -16 10 0 80 23 98 18 15 43 18 156 18 113 0 139 -3 157 -18z m141
                                  -519 l-3 -298 -295 0 -295 0 -3 298 -2 297 300 0 300 0 -2 -297z"/>
                            <path d="M320 470 l0 -240 30 0 30 0 0 240 0 240 -30 0 -30 0 0 -240z"/>
                            <path d="M470 470 l0 -240 30 0 30 0 0 240 0 240 -30 0 -30 0 0 -240z"/>
                            <path d="M620 470 l0 -240 30 0 30 0 0 240 0 240 -30 0 -30 0 0 -240z"/>
                        </g>
                    </svg>
                </a>
                <?php
                break;

            default :

                break;
        }
    }

    /*
     *
     * Print Available cart actions
     *
     */

    public function restrict_manage_posts() {
        global $pagenow;

        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && 'stored-carts' == $_GET['post_type']) {
            ?>
            <label for="from"><?php _e('From:', 'email-cart'); ?></label> 
            <input type="text" name="start_date" id="from" readonly value="<?php echo esc_attr(date('Y-m-d', $start_date)); ?>" /> 
            <label for="to"><?php _e('To:', 'email-cart'); ?></label> 
            <input type="text" name="end_date" id="to" readonly value="<?php echo esc_attr(date('Y-m-d', $end_date)); ?>" /> 
            <script type="text/javascript">
                jQuery(function () {
            <?php $this->woocommerce_datepicker_js_carts(); ?>
                });
            </script>
            <?php
        }
    }

    /**
     * Adds a date range to the WHERE portion of our query
     *
     * @param string $where The current WHERE portion of the query
     * @return string $where The updated WHERE portion of the query
     */
    public function filter_where($where = '') {
        global $pagenow;

        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'stored-carts') {

            global $cxecrt_options, $start_date, $end_date, $offset;

            if (isset($_GET['lifetime']) || !isset($_GET['mv'])) {

                $args = array(
                    'numberposts' => 1,
                    'offset' => 0,
                    'orderby' => 'post_date',
                    'order' => 'ASC',
                    'post_type' => 'stored-carts',
                    'post_status' => 'publish',
                );

                $post = get_posts($args);
                if (isset($post[0]))
                    $post = $post[0];
                if (isset($post) && sizeof($post) > 0)
                    $start_date = strtotime($post->post_date) - (86400); // Add on a day for good measure.
            }

            $start = date('Y-m-d G:i:s', $start_date);
            $end = date('Y-m-d G:i:s', $end_date + 86400);

            if (!isset($_GET['mv'])) {
                //If not isset -> set with dumy value
                $_GET['action'] = "empty";
            }

            $where .= " AND post_date > '" . $start . "' AND post_date < '" . $end . "'";

            if (isset($_GET['author'])) {
                if ($_GET['author'] == "-1")
                    $where .= " AND post_author = ''";
            }
        }

        return $where;
    }

    /**
     * JS for the datepicker on the table (changes from woocommerce stock include removing the minimum date )
     */
    public function woocommerce_datepicker_js_carts() {
        ?>
        var dates = jQuery( "#posts-filter #from, #posts-filter #to" ).datepicker({
        defaultDate: "",
        dateFormat: "yy-mm-dd",
        //changeMonth: true,
        //changeYear: true,
        numberOfMonths: 1,
        maxDate: "+0D",
        showButtonPanel: true,
        showOn: "button",
        buttonImage: "<?php echo WC()->plugin_url(); ?>/assets/images/calendar.png",
        buttonImageOnly: true,
        onSelect: function( selectedDate ) {
        var option = this.id == "from" ? "minDate" : "maxDate",
        instance = jQuery( this ).data( "datepicker" ),
        date = jQuery.datepicker.parseDate(
        instance.settings.dateFormat ||
        jQuery.datepicker._defaults.dateFormat,
        selectedDate, instance.settings );
        dates.not( this ).datepicker( "option", option, date );
        }
        });
        <?php
    }

    /**
     * Index top interface.
     */
    public function render_top_interface() {
        global $screen;

        // Bail if not Cart Index page.
        if (!isset($screen->id) || 'edit-stored-carts' !== $screen->id)
            return;
        ?>
        <div class="updated cxecrt-admin-notice">
            <p><?php _e('<strong>Save & Share Cart</strong> allows you and your customers to send a link to a pre-stocked cart to any email address. Admins can send the email from here, and your customers send using the Save & Share Cart button on your shop\'s cart page. The list below shows Sent Cart activity.', 'email-cart'); ?></p>
            <p>
                <a href="<?php echo esc_url(cxecrt_get_woocommerce_cart_url() . '#cxecrt-save-cart'); ?>" class="button button-primary cxecrt-button">
        <?php _e('Save & Share Cart', 'email-cart'); ?> <span class="dashicons dashicons-cart"></span>
                </a>
                &nbsp;
                <a href="<?php echo esc_url(admin_url('options-general.php?page=email_cart_settings')); ?>" class="button cxecrt-button">
        <?php _e('Settings', 'email-cart'); ?> <span class="dashicons dashicons-admin-generic"></span>
                </a>
            </p>
        </div>
        <?php
    }

}
?>