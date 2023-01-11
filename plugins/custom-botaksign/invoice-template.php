<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';

$botakit = Botaksign_Invoice_Template::get_instance();

class Botaksign_Invoice_Template {
	public $_header;
	public $_content;
	public $_footer;
	public $_file_to_save;
	private static $instance;
	
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}
	
	private function __construct() {
		$this->_header = '<div class="main-page-number"><span class="page-number">Page {PAGENO} of {nb}</span></div>
            <table style="width:100%" id="header-logo">
                <tr>
                    <td align="left" style="width:50%"><img style="max-height: 70px" class="logo" src="' . (cxecrt_get_option('cxecrt_admin_url_logo') != ''?cxecrt_get_option('cxecrt_admin_url_logo'):plugin_dir_path(__FILE__) . '/assets/images/logo.png') . '"></td>
                </tr>
            </table>';
		$this->_content = 'No content.';
		$this->_footer = '<div id="footer-text"> 
                <h1 style="font-weight:600" class="footer-title">Botak Sign Pte Ltd</h1>
                <span class="footer-text-1">22 Yio Chu Kang Road, #01-34 Highland Centre, Singapore 545535</span>
                <span class="footer-text-2">&emsp;Tel: 6286 2298</span>
                <span class="footer-text-3">&emsp;Fax: 6383 5071</span>
                <span class="footer-text-4">info.botaksign.com.sg</span>
                <span class="footer-text-5">&emsp;www.botaksign.com.sg</span>
                <span class="footer-text-6">&emsp;GST Reg No.: 20-0101037M</span>
            </div>';
        $upload_dir = wp_upload_dir();
		$basedir = $upload_dir['basedir'].'/nbdesigner/order-detail';
		if (!file_exists($basedir)) {
            wp_mkdir_p($basedir);
        } 
		$this->_file_to_save = $basedir;
	}
	
	public function init(){
		global $fontDirs, $fontData;
		$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
		$fontDirs = $defaultConfig['fontDir'];
		$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
		$fontData = $defaultFontConfig['fontdata'];
	}
	
	public function generate_pdf_template($filename , $header = true) {
		global $fontDirs, $fontData;
		$mpdf = new \Mpdf\Mpdf([
			'fontDir' => array_merge($fontDirs, [
				plugin_dir_path(__FILE__) . '/assets/fonts',
			]),
			'fontdata' => $fontData + [
				'segoe-bold' => [
					'R' => 'segoe-ui-bold.ttf',
					'I' => 'Segoe-UI-Regular.ttf'
				],
				'Myriad-Pro-Light' => [
					'R' => 'Myriad-Pro-Light.otf'
				],
				'Myriad-Pro-Semibold' => [
					'R' => 'Myriad-Pro-Semibold.OTF'
				],
				//custom botak font
				'robotom' => [
					'R' => 'Roboto-Medium.ttf',
				],
				'roboto' => [
					'R' => 'Roboto-Regular.ttf',
				],
				'robotol' => [
					'R' => 'Roboto-Light.ttf',
				],
				'Cooper-Black-Regular' => [
					'R' => 'Cooper-Black-Regular.ttf',
				],
			],
			'setAutoTopMargin' => 'stretch',
			'setAutoBottomMargin' => 'stretch',
			'autoPadding' => 'stretch',
			'useFixedNormalLineHeight' => true,
			'simpleTables' => true,
			'default_font' => 'roboto'
		]);
		if($header) {
			$mpdf->SetHTMLHeader($this->_header);
			$mpdf->SetHTMLFooter($this->_footer);
		}
		$mpdf->AddPage();
		$stylesheet = file_get_contents(plugin_dir_path(__FILE__) . '/assets/css/templates/style_template_invoice.css'); // external css
		$full_path = $this->_file_to_save . '/' . $filename;
		$mpdf->WriteHTML($stylesheet,1);
		$arr_pt = explode('<div class="minh-phan-trang"></div>', $this->_content);
		if(count($arr_pt)>1) {
			for($i=0; $i<count($arr_pt); $i++) {
				if($i!=0) {
					$mpdf->AddPage();
				}
				$mpdf->WriteHTML($arr_pt[$i]);
			}
		} else {
			$mpdf->WriteHTML($this->_content);
		}
		$mpdf->Output($full_path, 'F');
	}
}
?>