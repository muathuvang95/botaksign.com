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

    Nbdesigner_Output::_export_pdfs( $nbd_item_key);
}
function pc_build_html_page( $nbd_item_key, $key, $svg_path, $page_settings, $font_css ){
    $pdf_temp_path = NBDESIGNER_TEMP_DIR . '/pdf-templates';
    if( !file_exists( $pdf_temp_path ) ) {
        wp_mkdir_p( $pdf_temp_path );
    }

    $temp_path  = $pdf_temp_path . '/' . $nbd_item_key . '/';
    $html_path  =  $temp_path . $key .'.html';
    $html_url   = NBDESIGNER_TEMP_URL . '/pdf-templates/' . $nbd_item_key . '/' . $key .'.html';
    if( !file_exists( $temp_path ) ) {
        wp_mkdir_p( $temp_path );
    }

    $svg_string = file_get_contents( $svg_path );
    $svg_string = preg_replace( "/<(?:\?xml|!DOCTYPE).*?>/", "", $svg_string );

    ob_start();
    include NBDESIGNER_PLUGIN_DIR . 'views/pdf-template.php'; 
    $template    = ob_get_clean();

    file_put_contents( $html_path, $template );
    return $html_url;
}
function pc_get_unit_ratio( $dpi, $unit ){
    switch ($unit) {
        case 'mm':
            $unit_ratio = 1 / 25.4;
            break;
        case 'in':
            $unit_ratio = 1;
            break;
        case 'ft':
            $unit_ratio = 1 / 12;
            break;
        case 'px':
            $unit_ratio = 1 / $dpi;
            break;
        default:
            $unit_ratio = 1 / 2.54;
            break;
    }
    return $unit_ratio;
}
function pc_build_font_css( $fonts ){
    $google_font_link = '';

    foreach( $fonts as $font ){
        $font_name = str_replace( ' ', '+', $font->name );

        if( $font->type == 'google' ){
            $google_font_link .= '<link rel="stylesheet" href="//fonts.googleapis.com/css?family=' . $font_name . ':400,400i,700,700i" />';
        }
    }

    $custom_font_style = '<style type="text/css">';
    foreach( $fonts as $font ){
        $font_name = str_replace( ' ', '+', $font->name );
        
        if( $font->type != 'google' ){
            $custom_font            = nbd_get_font_by_alias( $font->alias );
            $custom_font_variations = array();
            foreach( $custom_font->file as $key => $custom_font_url ){
                $custom_font_variations[$key] = NBDESIGNER_FONT_URL . '/' . $custom_font_url;
            }

            foreach( $custom_font_variations as $key => $custom_font_variation ){
                $font_style     = 'normal';
                $font_weight    = 'normal';
                switch( $key ){
                    case 'b':
                        $font_weight    = 'bold';
                        break;
                    case 'i':
                        $font_style     = 'italic';
                        break;
                    case 'bi':
                        $font_weight    = 'bold';
                        $font_style     = 'italic';
                        break;
                }
                $custom_font_style .= "@font-face {font-family: '" . $font->alias . "';src: url('" . $custom_font_variation . "') format('truetype');font-weight: " . $font_weight . ";font-style: " . $font_style . ";}";
            }
        }
    }
    $custom_font_style .= '</style>';

    return array(
        'google_font_link'  => $google_font_link,
        'custom_font_style' => $custom_font_style
    );
}
function pc_export_pdfs( $nbd_item_key, $watermark = false, $force = false, $showBleed = 'no', $extra = null, $need_pw = false ){
    $path           = NBDESIGNER_CUSTOMER_DIR .'/' . $nbd_item_key;
    $folder         = $path . '/customer-pdfs';
    $result         = array();
    $pages          = array();

    if( !file_exists( $folder ) ) {
        wp_mkdir_p( $folder );
    }

    $datas      = unserialize( file_get_contents( $path . '/product.json' ) );
    $option     = unserialize( file_get_contents( $path . '/option.json' ) );
    $config     = json_decode( file_get_contents( $path . '/config.json' ) );
    $dpi        = (float)$option['dpi'];
    $dpi        = $dpi > 0 ? $dpi : 300;
    $unit       = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
    $unit_ratio = pc_get_unit_ratio( $dpi, $unit );

    if( isset( $config->product ) && count( $config->product ) ){
        $datas = array();
        foreach( $config->product as $side ){
            $datas[] = (array)$side;
        }
    };

    $used_font_path = $path . '/used_font.json';
    $used_fonts     = json_decode( file_get_contents( $used_font_path ) );
    $font_css       = pc_build_font_css( $used_fonts );
    $requests       = array();
    $need_pdf_bg    = false;
    $has_raw_pdf    = false;
    
    foreach( $datas as $key => $data ){
        $page_settings = array(
            'width'         => $data['product_width'] * $unit_ratio . 'in',
            'height'        => $data['product_height'] * $unit_ratio . 'in',
            'design_width'  => $data['real_width'] * $unit_ratio . 'in',
            'design_height' => $data['real_height'] * $unit_ratio . 'in',
            'design_top'    => $data['real_top'] * $unit_ratio . 'in',
            'design_left'   => $data['real_left'] * $unit_ratio . 'in',
            'include_bg'    => false,
            'include_ov'    => false,
            'crop_mark'     => false,
            'watermark'     => $watermark
        );

        $pages[$key] = array(
            'width'         => $data['product_width'] * $unit_ratio,
            'height'        => $data['product_height'] * $unit_ratio,
            'design_top'    => $data['real_top'] * $unit_ratio,
            'design_left'   => $data['real_left'] * $unit_ratio,
            'has_raw_pdf'   => false
        );

        $include_bg = isset( $data['include_background'] ) ? $data['include_background'] : 1;
        $include_bg = ( $data['bg_type'] == 'image' ) ? $include_bg : 1;
        if( isset( $data['origin_bg_pdf'] ) && $data['origin_bg_pdf'] != '' && $data['bg_type'] == 'image' ){
            if( $include_bg ){
                $pages[$key]['origin_bg_pdf']   = NBDESIGNER_TEMP_DIR . $data['origin_bg_pdf'];
                $need_pdf_bg                    = true;
            }
            $include_bg = 0;
        }else{
            if( !$include_bg && $data['bg_type'] == 'image' ){
                $page_settings['width']         = $page_settings['design_width'];
                $page_settings['height']        = $page_settings['design_height'];
                $pages[$key]['width']           = $data['real_width'] * $unit_ratio;
                $pages[$key]['height']          = $data['real_height'] * $unit_ratio;
                $page_settings['design_top']    = 0;
                $page_settings['design_left']   = 0;
            }
        }

        if( $data['bg_type'] == 'color' ){
            $need_bg_color = true;

            if( isset( $config->areaDesignShapes ) && $config->areaDesignShapes[$key] ){
                $need_bg_color  = false;
            }
            if( $data['show_overlay'] == 1 && $data['include_overlay'] == 1 ){
                $need_bg_color  = true;
            }

            if( $need_bg_color ){
                $page_settings['include_bg']    = true;
                $page_settings['bg_type']       = 'color';
                $page_settings['bg_color']      = $data['bg_color_value'];
            }
        }

        $allow_exts     = array( 'jpg', 'jpeg', 'png', 'svg' );

        if( $include_bg && $data['bg_type'] == 'image' ){
            $product_bg     = is_numeric( $data['img_src'] ) ? wp_get_attachment_url( $data['img_src'] ) : $data['img_src'];
            if( Nbdesigner_IO::checkFileType( basename( $product_bg ), $allow_exts ) ){
                $page_settings['include_bg']    = true;
                $page_settings['bg_type']       = 'image';
                $page_settings['bg_src']        = $product_bg;
            }
        }

        if( $data['show_overlay'] == 1 && $data['include_overlay'] == 1 ){
            $overlay = is_numeric( $data['img_overlay'] ) ?  wp_get_attachment_url( $data['img_overlay'] ) : $data['img_overlay'];
            if( Nbdesigner_IO::checkFileType( basename( $overlay ), $allow_exts ) ){
                $page_settings['include_ov']    = true;
                $page_settings['ov_src']        = $overlay;
            }
        }

        if( $watermark ){
            $watermark_type                     = nbdesigner_get_option( 'nbdesigner_pdf_watermark_type' );
            $page_settings['watermark_type']    = $watermark_type;
            if( $watermark_type == 1 ){
                $watermark_image    = nbdesigner_get_option( 'nbdesigner_pdf_watermark_image', '' );
                $watermark_url      = wp_get_attachment_url( $watermark_image );
                if( $watermark_url ){
                    $page_settings['wm_src']        = $watermark_url;
                }else{
                    $page_settings['watermark'] = false;
                }
            } else {
                $default_text = get_bloginfo( 'name' );
                $page_settings['wm_text'] = nbdesigner_get_option( 'nbdesigner_pdf_watermark_text', $default_text );
            }
        }

        if( isset( $config->contour ) ){
            $page_settings['contour'] = $config->contour;
        }

        $pages[$key]['page_settings'] = $page_settings;

        $svg_path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/frame_' . $key . '_svg.svg';
        if( file_exists( $svg_path ) ){
            $html_url           = pc_build_html_page( $nbd_item_key, $key, $svg_path, $page_settings, $font_css );
            $url_segment        = urlencode( $html_url );
            $settings_segment   = base64_encode( json_encode( array(
                'width'         => $data['product_width'] * $unit_ratio . 'in',
                'height'        => $data['product_height'] * $unit_ratio . 'in'
            ) ) );

            $requests[] = array(
                'index'         => $key,
                'url'           => 'https://api.cloud2print.net/pdf/' . $url_segment . '/' . $settings_segment,
                'part_index'    => false
            );
        }
        echo '<pre>';
        var_dump($requests);
        echo '</pre>';
        
    }
}
pc_export_pdfs('17725521685196857');

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
			botak_cloud_export_pdfs($key_id_api); 
			echo "log Api";
		}
	}
}
