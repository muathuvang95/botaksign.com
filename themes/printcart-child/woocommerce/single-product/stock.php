<?php
/**
 * Single Product stock.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$product_id = $product->get_id();
$items = nbd_get_items_product_grouped($product_id);
if(is_array($items)) {
    $f_product = wc_get_product($items[0]['id']);
    if( isset($f_product) && $f_product ) {
        $f_availability = $f_product->get_availability();
        $class = $f_availability['class'];
        $availability = $f_availability['availability'];
    }
}
?>
<style type="text/css">
    .single-product-wrap .clearfix p.stock {
        display: block!important;
    }
    p.stock.in-stock {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .botak-out-of-stock {
        color: #FF0000FF;

    }
    .botak-wrap-stock {
            padding: 10px 15px;
        background: #f7f3f3;
        box-shadow: 1px 1px #b4b4b4;
        border-radius: 5px;
        font-size: 18px;
        margin: 0 0 15px 0;
        font-weight: 600;
    }
</style>
<?php 
//global $BTS_Page_ID;
//$BTS_This_Page_ID=$BTS_Page_ID;
$current_user=wp_get_current_user();
$User_ID=$current_user->ID;
$BTS_This_Sku=$product->get_sku();
$BTS_This_Title=get_the_title($Item_ID);
// Change the Stock Out Description on 9th June 2023
/** <span class="stock botak-out-of-stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ).'!'; ?></span>
    <span class="stock-desc">(Prices will appear when stocks are available)</span> **/
if($availability == 'Out of stock'){
    ?>
    <div class="botak-wrap-stock">
        <?php
		$BTS_Product_Group_ID=$_SESSION['BTS_Product_Group_ID'];
		if($BTS_Product_Group_ID){$BTS_woosg=get_post_meta($BTS_Product_Group_ID,'woosg_ids', true);}
		if($BTS_woosg){ // Product_ID/0 and comma
			$Item_Temp_Array=explode(',',$BTS_woosg);			
			$BTS_Item_TD=False;
			if(is_array($Item_Temp_Array)){
				foreach($Item_Temp_Array AS $Item_Temp_ID){
					$Item_ID=explode('/',$Item_Temp_ID); $Item_ID=$Item_ID[0];
					//$Item_SKU=get_post_meta($Item_ID,'_sku', true); use post title instead (sku is too technical)
					$Item_SKU=get_the_title($Item_ID);
					$Item_Stock_Qty=floor(get_post_meta($Item_ID,'_stock', true));
					$Item_Stock_Status=get_post_meta($Item_ID,'_stock_status', true);
					if($Item_Stock_Status=='instock'){$BTS_Item_TD.="<br>$Item_SKU ($Item_Stock_Qty in stock)";}
					}
				}
			} // end of woosg
        if($User_ID==1){//keep for my test						
            echo "<span class='stock-desc'>$BTS_This_Title is <b style='color:red;'>Out Of Stock</b>.<br>You might want to check $BTS_Item_TD</span>";			
            }else{
			// $BTS_Item_TD=False;	
			switch($BTS_Item_TD){
				case true : echo "<span class='stock-desc'>Currently $BTS_This_Sku is <b style='color:red;'>Out Of Stock</b>.<br>You might want to check $BTS_Item_TD</span>"; break;
				default : echo "<span class='stock-desc'>Currently $BTS_This_Sku is <b style='color:red;'>Out Of Stock</b>.<br>You might want to check other model.</span>";
				}			
			//echo "<span class='stock-desc'>Currently $BTS_This_Sku is <b style='color:red;'>Out Of Stock</b>.<br>You might want to check other model.</span>";
            }
        ?>
    </div>
    <?php 
} else {?>
    <p class="stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ); ?></p>
<?php 
} ?>
