<div class="tabs-content">
    <i class="fa fa-times" aria-hidden="true"></i>
    <span class="hide-tablet"><i class="icon-nbd icon-nbd-fomat-top-left rotate-135"></i></span>
    <?php include 'tab-photo.php'; ?> 
    <?php if( $show_nbo_option && $settings['nbdesigner_display_product_option'] == '2' && !wp_is_mobile() ): ?>
    <?php include 'tab-product.php'; ?>
    <?php endif; ?>
    <?php if( $product_data["option"]['admindesign'] != "0" ): ?>
    <?php include 'tab-templates.php'; ?>
    <?php endif; ?>
    <?php include 'tab-typography.php'; ?>
    <?php include 'tab-clipart.php'; ?>    
    <?php if($show_elements_tab) include 'tab-elements.php'; ?>
    <?php if($settings["nbdesigner_hide_layer_tab"] == "no") include 'tab-layer.php'; ?>
</div>