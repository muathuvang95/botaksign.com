<?php 
/**
* Template Name: Order Detail PDF

*/
require_once CUSTOM_BOTAKSIGN_PATH . 'invoice-template.php';
$botakit = Botaksign_Invoice_Template::get_instance();
$order_id = isset( $_GET['order_id'] ) ? $_GET['order_id'] : '';
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