<?php
/**
* Template Name: Botak Tools

*/

if ( ! current_user_can( 'manage_options' ) ) { 
	echo '<body id="error-page"><div class="wp-die-message">Sorry, you are not allowed to access this page.</div></body>';
	return;
}

?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>

<!-- Change specialist -->
<div class="change-specialist border border-primary m-4 p-4">
	<h3 class="my-2">Change specialist</h3>
	<form method="post" class="p-4">
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">User id old</label>
			<input type="text" name="id-old" class="form-control" id="exampleFormControlInput1">
		</div>
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">User id new</label>
			<input type="text" name="id-new" class="form-control" id="exampleFormControlInput1">
		</div>
		<div class="mb-3">
			<select class="form-select" name="type" aria-label="Default select example">
				<option selected>Open this select menu</option>
				<option value="1">Get all user</option>
				<option value="2">Get all order</option>
				<option value="3">Change</option>
			</select>
		</div>
		<input type="hidden" name="change-specialist" value="change-specialist">
		<button class="btn btn-primary">Submit</button>
	</form>

	<?php
	global $wpdb;
	if(isset($_POST['change-specialist']) && $_POST['change-specialist'] == 'change-specialist' ) {
		$id_old = isset($_POST['id-old']) ? $_POST['id-old'] : '';
		$id_new = isset($_POST['id-new']) ? $_POST['id-new'] : '';
		$type = isset($_POST['type']) ? $_POST['type'] : '';
		switch ($type) {
			case '1':
				$query1 = "SELECT * FROM `wp_usermeta` WHERE `meta_key` LIKE 'specialist' AND `meta_value` LIKE " . $id_old;
				$result1 = $wpdb->get_results($query1);
				echo '<pre>';
				var_dump($result1);
				echo '<pre>';
				break;
			case '2':
				$query2 = "SELECT * FROM `wp_postmeta` WHERE `meta_key` LIKE '_specialist_id' AND `meta_value` LIKE " . $id_old;
				$result2 = $wpdb->get_results($query2);
				echo '<pre>';
				var_dump($result2);
				echo '<pre>';
				break;
			case '3':
				if($id_old && $id_new) {
					$wpdb->update(
						'wp_usermeta', 
						array(
							'meta_value'=>$id_new,
						),
						array(
							'meta_key'=> 'specialist',
							'meta_value'=> $id_old,
						)
					);
					$wpdb->update(
						'wp_postmeta', 
						array(
							'meta_value'=>$id_new,
						),
						array(
							'meta_key'=> '_specialist_id',
							'meta_value'=> $id_old,
						)
					);
				}
				break;
			
			default:
				// code...
				break;
		}
	}


	?>
</div>


<!-- Change status for order -->
<div class="change-status border border-primary m-4 p-4">
	<?php
	$alert = '';
	$list_status = array(
		array(
			"name" 	=> "Pending",
        	"value" => "Pending"
		),
		array(
			"name" 	=> "New",
        	"value" => "New"
		),
		array(
			"name"	=> "Ongoing",
        	"value"	=> "Ongoing"
		),
		array(
			"name"	=> "Completed",
        	"value"	=> "Completed"
		),
		array(
			"name"	=> "Collected",
        	"value"	=> "Collected"
		),
		array(
			"name"	=> "Cancelled",
        	"value"	=> "Cancelled"
		),
	);

	if(isset($_POST['change-status']) && $_POST['change-status'] == 'change-status' ) {
		$order_id = isset($_POST['order-id']) ? $_POST['order-id'] : '';
		$status = isset($_POST['order-status']) ? $_POST['order-status'] : '';
		if($order_id && $status) {
			$wc_status = '';
			switch ($status) {
				case 'Pending':
					$wc_status = 'wc-pending';
					break;
				case 'New':
					$wc_status = 'wc-pending';
					break;
				case 'Ongoing':
					$wc_status = 'wc-processing';
					break;
				case 'Completed':
					$wc_status = 'wc-completed';
					break;
				case 'Collected':
					$wc_status = 'wc-completed';
					break;
				case 'Cancelled':
					$wc_status = 'wc-cancelled';
					break;
				
				default:
					// code...
					break;
			}
			$order = wc_get_order($order_id);
			if($order) {
				update_post_meta( $order_id , '_order_status' , $status);
				$args = array(
					'comment_post_ID' => $order_id,
					'comment_author' => 'admin',
					'comment_agent' => 'Manual update',
					'comment_content' => $status,
					'comment_author_email' => '',
					'comment_type' => 'order_log',
					'comment_approved' => 2,

				);
				wp_insert_comment($args);
				if( $wc_status ) {
					wp_update_post(array(
					    'ID'    =>  $order_id,
					    'post_status'   =>  $wc_status
					));
				}
				$alert = 'Updated!';
			}
		}
	}
	?>
	<h3 class="my-2">Change status</h3>
	<?php 
	if($alert) {
		echo '<div class="alert alert-success" role="alert">'. $alert .'</div>';
	}
	?>
	<form method="post" class="p-4">
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">Order ID</label>
			<input type="text" name="order-id" class="form-control" id="exampleFormControlInput1">
		</div>
		<div class="mb-3">
			<select class="form-select" name="order-status" aria-label="Default select example">
				<option selected>Open this select menu</option>
				<?php
				foreach ($list_status as $key => $value) {
					echo '<option value="'. $value['value'] .'">'. $value['name'] .'</option>';
				}
				?>
			</select>
		</div>
		<input type="hidden" name="change-status" value="change-status">
		<button class="btn btn-primary">Submit</button>
	</form>
</div>


<!-- Get invoice & quotation template -->
<div class="invoice-template border border-primary m-4 p-4">
	<?php
	$quotation_url = '';
	$invoice_url = '';

	if(isset($_POST['pdf-template']) && $_POST['pdf-template'] == 'pdf-template' ) {
		$order_id = isset($_POST['order-id']) ? $_POST['order-id'] : '';
		$quotation_id = isset($_POST['quotation-id']) ? $_POST['quotation-id'] : '';
		if($order_id) {
			$invoice_url = 'muathuvang';
		} else if($quotation_id) {
			global $botakit;
	        $html = generate_quote_pdf($quotation_id);
	        $botakit->_content = $html;
	        $filename = 'quotation-' . $quotation_id . '.pdf';
	        $botakit->generate_pdf_template($filename);
	        $pdf_path = $botakit->_file_to_save . '/' . $filename;
	        $quotation_url = convertLinkDesign($pdf_path);
		}
	}
	?>
	<h3 class="my-2">Invoice & quotation template</h3>
	<?php 
	if($quotation_url ) {
		echo '<div class="alert alert-success" role="alert"><a href="' . $quotation_url .'"><b>View</b></a></div>';
	} else if($invoice_url) {
		echo '<div class="alert alert-success" role="alert"><a href="' . $invoice_url .'"><b>View</b></a></div>';
	}
	?>
	<form method="post" class="p-4">
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">Order ID</label>
			<input type="text" name="order-id" class="form-control" id="exampleFormControlInput1">
		</div>
		<div class="mb-3">
			<label for="exampleFormControlInput1" class="form-label">Quotation ID</label>
			<input type="text" name="quotation-id" class="form-control" id="exampleFormControlInput1">
		</div>
		<input type="hidden" name="pdf-template" value="pdf-template">
		<button class="btn btn-primary">Get pdf file</button>
	</form>
</div>


<!-- Test email templates -->
<div class="invoice-template border border-primary m-4 p-4">
	<?php 
	$titles = array(
	   "A1" => 'Order Confirmed',
	   "A2" => 'Order Complete',
	   "C1" => 'Artwork Amendment',
	   "C2" => 'Order Processed',
	   "D1" => 'Well come',
	   "E1" => 'Password Reset',
	   "I1" => 'Payment Failed',
	   "K1" => 'Order Refunded',
	);

	if( isset($_POST['action']) && $_POST['action'] == 'submit' ) {
	    $order_id   = isset($_POST['order_id']) ? $_POST['order_id']: '';
	    $email      = isset($_POST['email']) ? $_POST['email']: '';
	    $template   = isset($_POST['template']) ? $_POST['template']: '';
	    if(!$order_id) return;
	    $order = wc_get_order($order_id);

	    $to = $email;
	    $subject = isset($titles[$template]) ? $titles[$template] . ' TEST' : "ORDER COMPLETED TEST";
	    $content_type = 'text/html; charset=UTF-8';
	    $from_name = 'ratruong2.5@gmail.com';
	    $from_address = get_option( 'woocommerce_email_from_address' );
	    add_filter( 'wp_mail_from', 'nb_get_from_address' );
	    add_filter( 'wp_mail_from_name', 'nb_get_from_name' );
	    add_filter( 'wp_mail_content_type', 'nb_get_content_type' );

	    $headers = 'Content-Type: ' . $content_type . "\r\n";
	    $headers .= 'Reply-To: ' . $from_name . ' <' . $to . ">\r\n";
	    ob_start();

	    include(CUSTOM_BOTAKSIGN_PATH . "includes/email-test/email_header.php");


	    include(CUSTOM_BOTAKSIGN_PATH . "includes/email-test/". $template .".php");

	    include(CUSTOM_BOTAKSIGN_PATH . "includes/email-test/email_footer.php");
	    $message = ob_get_contents();
	    ob_end_clean();
	    wp_mail($to, $subject, $message, $headers);
	}
	?>
	<h3 class="my-2">Test email templates</h3>
	<form action="" method="post" class="p-4">
	    <div class="row mb-3">
	        <div class="col-sm-3">
	            <label><b>To email</b></label>
	            <input type="email" name="email" class="form-control" id="email" width="120px" value="on9opera@hotmail.com">
	        </div>
	        <div class="col-sm-3">
	            <label><b>Order Id</b></label>
	            <input type="number" name="order_id" class="form-control" id="input-order-id" width="120px" value="0">
	        </div>
	        <div class="col-sm-3">
	            <label><b>Email template</b></label>
	            <select class="form-select" name="template">
	                <option value="A1">Order Confirmed</option>
	                <option value="A2">Order Complete</option>
	                <option value="C1">Artwork Amendment</option>
	                <option value="C2">Order Processed</option>
	                <option value="D1">Well come</option>
	                <option value="E1">Password Reset</option>
	                <option value="I1">Payment Failed</option>
	                <option value="K1">Order Refunded</option>
	            </select>
	        </div>
	    </div>
	    <div class="col-sm-3">
	        <input type="hidden" name="action" value="submit">
	        <button type="submit" class="btn btn-primary mb-3">Send email</button>
	    </div>
	</form>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>