<?php
/**
* Template Name: Fix font design

*/
get_header();

function botak_export_pdfs( $nbd_item_key){
    if( !class_exists('TCPDF') ){
        require_once( NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php' );
    }
    require_once( NBDESIGNER_PLUGIN_DIR . 'includes/fpdi/autoload.php' );

    $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    $folder         = $path . '/customer-pdfs';
    $output_file    = $folder .'/'. $nbd_item_key . '.pdf';
    $result         = array();
    if( !file_exists( $folder ) ) {
        wp_mkdir_p( $folder );
    }
    botak_cloud_export_pdfs( $nbd_item_key);
}

function botak_cloud_export_pdfs( $nbd_item_key){
    require_once( NBDESIGNER_PLUGIN_DIR.'includes/class-output.php' );

    return Nbdesigner_Output::_export_pdfs( $nbd_item_key);
}
if ( ! current_user_can( 'manage_options' ) ) { 
	echo '<body id="error-page"><div class="wp-die-message">Sorry, you are not allowed to access this page.</div></body>'; 
} else {
	?>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

	<div class="container mt-4">
		<div class="justify-content">
			<form action="" method="post">
				<div class="row">
					<div class="col-sm-3">
						<input type="number" name="order_id" class="form-control" id="input-order-id" width="120px" value="0">
						<label><b>Fix design lost font</b></label>
					</div>
					<div class="col-sm-3">
						<input type="number" name="order_id_old" class="form-control" id="input-order-id" width="120px" value="0">
						<label><b>Fix when missing PDF</b></label>
					</div>
					<div class="col-sm-3">
						<input type="text" name="key_id_imagick" class="form-control" id="input-order-id" width="120px" value="0">
						<label><b>Fix by Imagick</b></label>
					</div>
					<div class="col-sm-3">
						<input type="text" name="key_id_api" class="form-control" id="input-order-id" width="120px" value="0">
						<label><b>Fix by API</b></label>
					</div>		
					<div class="col-sm-3">
						<input type="hidden" name="action" value="submit">
						<button type="submit" class="btn btn-primary mb-3">Fix font & Download</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	<?php
	if( isset($_POST['action']) && $_POST['action'] == 'submit' ) {
		$order_id = isset($_POST['order_id']) ? $_POST['order_id']: '';
		$order_id_old = isset($_POST['order_id_old']) ? $_POST['order_id_old']: '';
		$key_id_api = isset($_POST['key_id_api']) ? $_POST['key_id_api']: '';
		$key_id_imagick = isset($_POST['key_id_imagick']) ? $_POST['key_id_imagick']: '';
		if( $order_id ) {
			$order = wc_get_order($order_id);
			if(!$order) { echo 'Order not exist!'; return; }
			//zip file s3
			$listPrefix_s3 = array();
		    $product_s3 = $order->get_items();
		    $no = 1;
		    foreach($product_s3 as $order_item_id => $product) {
		        $nbu_item_key_s3 = wc_get_order_item_meta($order_item_id, '_nbu');  
		        $nbd_item_key_s3 = wc_get_order_item_meta($order_item_id, '_nbd'); 
		        if($nbu_item_key_s3) {
		        	$list_nbu_Prefix_s3[] = $nbu_item_key_s3;
		        }
		        if($nbd_item_key_s3) {
		        	botak_export_pdfs($nbd_item_key_s3);
		        	$list_nbd_Prefix_s3[] = $nbd_item_key_s3;
		        }     
		    }
		 //    if($list_nbu_Prefix_s3) {
		 //    	$listNbuPrefixStr = implode(",",$list_nbu_Prefix_s3);
		 //    }
			// if($list_nbd_Prefix_s3) {
			// 	$listNbdPrefixStr = implode(",",$list_nbd_Prefix_s3);
			// }
			// $url_s3 = get_home_url().'/s3_zip/index.php?order_id='.$order_id;
			// if($listNbuPrefixStr) {
			// 	$url_s3.= '&prefix_nbu='.$listNbuPrefixStr;
			// }
			// if($listNbdPrefixStr) {
			// 	$url_s3.= '&prefix_nbd='.$listNbdPrefixStr;
			// }
			// $link_s3 = '';
			// if( count($list_nbu_Prefix_s3) || count($list_nbd_Prefix_s3)  ) {
			// 	// $link_s3 = str_replace( 'http' , 'https' ,v3_getLinkAWS($order_id) );
			// 	$link_s3 = $url_s3.'&t='.strtotime('now');
			// }
			// wp_redirect( $link_s3 );
		}
		if( $order_id_old ) {
			$_order = wc_get_order($order_id_old);
			if(!$_order) { echo 'Order not exist!'; return; }
			//zip file s3
		    $products = $_order->get_items();
		    $no = 1;
		    foreach($products as $order_item_id => $product) { 
		        $nbd_item_key_s3 = wc_get_order_item_meta($order_item_id, '_nbd'); 
		        if($nbd_item_key_s3) {
		        	custom_nbd_export_pdfs($nbd_item_key_s3);
		        } 
		    }    
		}
		if( $key_id_imagick ) {
			custom_nbd_export_pdfs($key_id_imagick);
			echo "log Imagick";
		}
		if( $key_id_api ) {
			$design_detail = botak_cloud_export_pdfs($key_id_api);
			echo '<pre>';
			var_dump($design_detail);
			echo '</pre>';
			echo "log Api";
		}
	}
}
