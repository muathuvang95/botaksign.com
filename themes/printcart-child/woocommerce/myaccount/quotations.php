<?php
global $wpdb, $cxecrt, $cxecrt_options;
$user_ID = get_current_user_id();
$arr_object = $wpdb->get_results("SELECT ID, post_date FROM $wpdb->posts WHERE (post_author = " . $user_ID . " AND post_status = 'publish' AND post_type = 'stored-carts') order by ID desc");
if (count($arr_object) > 0) {
    $expiration_days = $cxecrt_options['cxecrt_cart_expiration_time'];
    $opt_in_settings = $cxecrt_options['cxecrt_cart_expiration_active'];
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.css" />';
    foreach ($arr_object as $key => $item) {
        $col_date_1 = '';
        $col_date_2 = '';
        $cart_retrieved = $item->post_date;
        $cart_status = get_post_meta($item->ID, '_cxecrt_status', true);
        $col_status = '<span>' . ($cart_status == 1 ? 'Approved' : 'Pending') . '</span>';
        if (!is_array($cart_retrieved) && !empty($cart_retrieved)) {
            $full_date = date('d/m/Y', strtotime($cart_retrieved));
            $col_date_1 .= '<span>Created : ' . $full_date . '</span>';
            if ($expiration_days && $expiration_days > 0 && $opt_in_settings) {
                $date = date('Y-m-d', strtotime($cart_retrieved));
                $exp_date = date('d/m/Y', strtotime($date . ' + ' . $expiration_days . ' days'));
                $check_expiring = strtotime($date . ' + ' . $expiration_days . ' days') < ( strtotime("now") + 8*3600 ) ? 'expiring' : '';
                $col_date_2 .= '<span class="'. $check_expiring .'">Valid Till : ' . $exp_date . '</span>';
            }
        }
        ?>
        <div class="item-quotation-cart nb-item-quotation-cart" rel="<?php echo esc_attr($item->ID); ?>">
	        <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table" style="margin-bottom: 0px;">
		        <thead>
			        <tr>
				        <th class="woocommerce-orders-table__header title woocommerce-orders-table__header-order-number">
				        	<span class="nobr">Quotation # <?php echo esc_html($item->ID); ?></span>
				        </th>
				        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status">
				        	<span class="nobr"><?php echo $col_date_1; ?></span>
				        </th>
				        <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total">
				        	<span class="nobr"><?php echo $col_date_2; ?></span>
				        </th>
			        </tr>
		        </thead>
		        <tbody>
		        	<tr class="nb-row">
		        		<td colspan="3">
		        			<div class="accordion nb-accordion-quotation" id="accordionQuotation">
								<div class="accordion-item">
									<div id="collapseQuotation<?php echo $item->ID; ?>" class="accordion-collapse collapse" aria-labelledby="headingBilling">
										<div class="accordion-body">
											<div class="wrap-detail-cart">
									        	<?php
									            $cxecrt->backup_current_cart();
									            $cxecrt->load_cart_from_post($item->ID);

									            wc_get_template('myaccount/quotation-details.php');

									            $cxecrt->restore_current_cart();
									            ?>
								            </div>
										</div>
									</div>
								</div>
							</div>
		        		</td>
		        	</tr>
			        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
				        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" colspan="3">
					        <div class="wrap-action-quotation">
					            <div class="col-right">
						            <?php if ($cart_status == 1) { ?>
					                <a href="#" rel="' . $item->ID . '" class="btn btn-load-cart">
					                	<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
								        width="19.000000pt" height="19.000000pt" viewBox="0 0 114.000000 90.000000"
								        preserveAspectRatio="xMidYMid meet">
									        <g transform="translate(0.000000,90.000000) scale(0.100000,-0.100000)"
									        fill="#696969" stroke="none">
										        <path d="M166 755 c-12 -33 4 -45 65 -45 58 0 59 0 74 -36 8 -20 15 -49 15
										        -64 0 -15 6 -50 14 -76 14 -48 32 -128 42 -190 4 -20 1 -34 -10 -43 -21 -18
										        -20 -57 2 -70 16 -10 16 -12 0 -25 -39 -30 -9 -96 44 -96 45 0 81 72 43 86 -8
										        4 -15 12 -15 20 0 11 33 14 180 14 147 0 180 -3 180 -14 0 -8 -7 -16 -15 -20
										        -8 -3 -15 -16 -15 -29 0 -50 70 -76 103 -39 22 24 22 68 1 76 -16 6 -16 8 0
										        24 9 10 16 28 16 40 0 22 -1 22 -224 22 -147 0 -227 4 -231 11 -4 5 -4 21 -1
										        33 6 26 29 31 181 41 44 3 114 12 155 20 41 8 98 16 125 17 l50 3 3 147 3 148
										        -298 2 -298 3 -3 28 -3 27 -89 0 c-69 0 -89 -3 -94 -15z m482 -152 c3 -81 15
										        -92 47 -43 32 48 85 53 85 8 0 -24 -136 -157 -160 -157 -24 -1 -160 132 -160
										        157 0 45 53 40 85 -8 33 -49 45 -39 45 38 0 75 4 84 35 80 18 -3 20 -11 23
										        -75z"/>
									        </g>
								        </svg>
								        <span class="nb-action">Load To Cart</span>
								    </a>
							        <a class="btn btn-download">
							        	<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
								        width="16.000000pt" height="16.000000pt" viewBox="0 0 26.000000 34.000000"
								        preserveAspectRatio="xMidYMid meet">
									        <g transform="translate(0.000000,34.000000) scale(0.050000,-0.050000)"
									        fill="#696969" stroke="none">
										        <path d="M69 594 c-6 -15 -8 -134 -5 -265 l6 -239 180 0 180 0 6 185 6 185
										        -71 0 c-69 0 -71 2 -71 80 l0 80 -111 0 c-72 0 -114 -9 -120 -26z m217 -379
										        c9 14 26 25 39 25 12 0 1 -23 -25 -50 l-47 -50 -47 43 c-49 47 -47 79 4 37 26
										        -22 30 -11 33 83 l4 107 11 -110 c7 -67 18 -100 28 -85z"/>
										        <path d="M320 553 c0 -69 4 -73 64 -73 l63 0 -58 65 c-33 36 -61 69 -64 73 -3
										        5 -5 -24 -5 -65z"/>
									        </g>
								        </svg>
							        	<span class="nb-action">Download</span>
							        </a>
						        	<?php } ?>
						            <a class="btn btn-remove">
						            	<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
								        width="17.000000pt" height="17.000000pt" viewBox="0 0 60.000000 87.000000"
								        preserveAspectRatio="xMidYMid meet">
									        <g transform="translate(0.000000,87.000000) scale(0.100000,-0.100000)"
									        fill="#696969" stroke="none">
										        <path d="M200 725 l0 -45 -59 0 c-43 0 -60 -4 -65 -15 -14 -38 -2 -40 214 -40
										        192 0 211 1 216 17 9 30 -5 38 -67 38 l-59 0 0 45 0 45 -90 0 -90 0 0 -45z
										        m118 -17 c15 -15 -2 -38 -28 -38 -10 0 -22 6 -29 13 -22 27 31 51 57 25z"/>
										        <path d="M80 355 c0 -296 -16 -275 207 -275 l160 0 26 30 27 30 0 222 0 223
										        -210 0 -210 0 0 -230z m358 175 c1 0 2 -7 2 -15 0 -12 -22 -15 -127 -17 -179
										        -2 -180 -2 -169 27 5 15 23 16 149 11 78 -3 143 -5 145 -6z m-268 -225 c0
										        -138 -2 -165 -15 -165 -11 0 -15 28 -17 155 -2 85 -1 160 0 165 2 6 10 10 18
										        10 11 0 14 -29 14 -165z m60 0 c0 -140 -2 -165 -15 -165 -13 0 -15 25 -15 165
										        0 140 2 165 15 165 13 0 15 -25 15 -165z m60 0 c0 -140 -2 -165 -15 -165 -13
										        0 -15 25 -15 165 0 140 2 165 15 165 13 0 15 -25 15 -165z m148 3 l-3 -163
										        -57 -3 -58 -3 0 159 c0 87 3 162 7 165 3 4 30 7 60 7 l53 0 -2 -162z"/>
									        </g>
								        </svg>
								        <span class="nb-action">Remove</span>
								    </a>
								    <a class="btn btn-view-detail-cart" data-bs-toggle="collapse" href="#collapseQuotation<?php echo $item->ID; ?>" role="button" aria-expanded="false" data-bs-target="#collapseQuotation<?php echo $item->ID; ?>" aria-controls="collapseQuotation<?php echo $item->ID; ?>">
								    	<span class="nb-show nb-action-accordion">
								    		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
												<path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
											</svg>
								    	</span>
								    	<span class="nb-hidden nb-action-accordion">
								    		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-up-fill" viewBox="0 0 16 16">
											  	<path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
											</svg>
								    	</span>
				            			
								    </a>
					            </div>
				        	</div>
					    </td>
			        </tr>
		        </tbody>
	        </table>
    	</div>
    <?php
    }
    ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.min.js"></script>
    <?php
}
