<?php 
/**
* Template Name: Order Detail PDF

*/

$token = isset( $_GET['token'] ) ? $_GET['token'] : '';
$order_id = isset( $_GET['order_id'] ) ? $_GET['order_id'] : '';

$user_id = get_current_user_id();

if( $token) {
    $_token = base64_decode($token);
    if(strpos($_token, '|' . $order_id) > 0) {
        $logged_in_cookie = str_replace( '|' . $order_id, '', $_token );
        $user_id = wp_validate_auth_cookie($logged_in_cookie, 'logged_in');
    }
}

$user = get_userdata($user_id);

if(!$user) {
    get_template_part( 404 ); exit();
}

if ( !in_array('administrator', $user->roles) && !in_array('production', $user->roles) && !in_array('specialist', $user->roles) && !in_array('customer_service', $user->roles) ) {
    echo '<body id="error-page"><div class="wp-die-message">Sorry, you are not allowed to access this page.</div></body>';
    exit();
}

require_once CUSTOM_BOTAKSIGN_PATH . 'invoice-template.php';
$botakit = Botaksign_Invoice_Template::get_instance();
if ($order_id) {
    $html = v3_generate_order_detail_pdf($order_id);
    $botakit->_content = $html;
    $filename = 'order-' . $order_id . '.pdf';
    $botakit->generate_pdf_template($filename , false);
    $pdf_path = $botakit->_file_to_save . '/' . $filename;
    $link_down = convertLinkDesign($pdf_path);
    header('Content-type: application/pdf');
  
    header('Content-Disposition: inline; filename="' . $filename . '"');
      
    header('Content-Transfer-Encoding: binary');
      
    header('Accept-Ranges: bytes');
      
    // Read the file
    @readfile($link_down);
    ?>
    <!-- <style type="text/css">
        .link-order-detail {
            position: fixed;
            top: calc(50% - 15px);
            left: calc(50% - 25px) ;
            padding: 15px 25px;
            color: #fff;
            background: #25c474;
            font-weight: 700;
            font-size: 20px;
            border-radius: 50px;
            text-decoration: none;
        }
    </style>
    <a class="link-order-detail" href="<?php echo $link_down; ?>" target="_blank">Download order detail</a> -->
    <?php
}