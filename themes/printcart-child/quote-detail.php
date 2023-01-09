<?php 
/**
* Template Name: Quote Detail PDF

*/

$user = get_userdata(get_current_user_id());
if(!$user) {
    get_template_part( 404 ); exit();
}

if ( !in_array('administrator', $user->roles) && !in_array('production', $user->roles) && !in_array('specialist', $user->roles) && !in_array('customer_service', $user->roles) ) {
    echo '<body id="error-page"><div class="wp-die-message">Sorry, you are not allowed to access this page.</div></body>';
    exit();
}

require_once CUSTOM_BOTAKSIGN_PATH . 'invoice-template.php';
$botakit = Botaksign_Invoice_Template::get_instance();
if (isset($_GET['quo_id'])) {
    $html = generate_quote_pdf($_GET['quo_id']);
    // echo $html; die;
    //write_log($html);
    $botakit->_content = $html;
    $filename = 'quotation-' . $_GET['quo_id'] . '.pdf';
    $botakit->generate_pdf_template($filename);
    $pdf_path = $botakit->_file_to_save . '/' . $filename;
    $link_down = convertLinkDesign($pdf_path);

    header('Content-type: application/pdf');
  
    header('Content-Disposition: inline; filename="' . $filename . '"');
      
    header('Content-Transfer-Encoding: binary');
      
    header('Accept-Ranges: bytes');
      
    // Read the file
    @readfile($link_down);
}
