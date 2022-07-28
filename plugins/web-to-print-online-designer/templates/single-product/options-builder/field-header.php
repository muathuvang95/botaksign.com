<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-field-header">
    <label for='nbd-field-<?php echo $field['id']; ?>'>
        <?php echo $field['general']['title']; ?>
        <?php if( $field['general']['required'] == 'y' ): ?>
        <span class="nbd-required">*</span>
        <?php endif; ?>
    </label> 
    <?php if( $field['general']['description'] != '' ): ?>
    <span data-position="<?php echo $tooltip_position; ?>" data-tip="<?php echo html_entity_decode( $field['general']['description'] ); ?>" class="nbd-help-tip"></span>
    <?php endif; ?>
    <?php if( $options['display_type'] == 5 ): ?>
        <span class="nbo-minus nbo-toggle" ng-click="toggle_field( $event )">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13H5v-2h14v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </span>
        <span class="nbo-plus nbo-toggle" ng-click="toggle_field( $event )">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </span>
    <?php endif; ?>
</div>
<?php if(isset($field['general']['agreement_clause']) && isset($field['nbd_type']) && $field['nbd_type'] == 'terms_conditions'): ?>
    <div class="terms_conditions_attr" style="padding: 15px;border: 2px solid #ddd;margin: 10px 0;border-radius: 5px;">
        <?php
            foreach ($field['general']['attributes']["options"] as $key => $attr): 
                $attr['sub_attributes'] = isset( $attr['sub_attributes'] ) ? $attr['sub_attributes'] : array();
                $show_subattr = ($enable_subattr == 'on' && count($attr['sub_attributes']) > 0) ? true : false;
                $field['general']['attributes']["options"][$key]['show_subattr'] = $show_subattr;
        ?>
            <?php if(isset($field['general']['attributes']) && $field['nbd_type'] == 'terms_conditions'): ?>
                <div class="nbo-ad-item-main <?php if( $show_subattr ) echo 'nbo-shrink'; ?>">
                    <div class="nbo-ad-item-descriptionsss"><?php echo $attr['des']; ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="agreement_box" style="display: flex;align-items: center;margin-top: 20px;">
            <input type="checkbox" class="agreement_clause_check" style="margin-right: 10px">
            <span><?php echo html_entity_decode( $field['general']['agreement_clause'] ); ?></span>
        </div> 
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            var $check = $('input[type="checkbox"].agreement_clause_check');
            $check.click( function(){
                if($check.prop("checked") == true){
                    $('.single-product .single-product-wrap .nbdesigner_frontend_container').removeClass('open');
                    $('.single_add_to_cart_button').attr('disabled', false);
                    $('.single_add_to_cart_button').removeClass('btn_disabled');
                } else if($check.prop("checked") == false){
                    $('.single-product .single-product-wrap .nbdesigner_frontend_container').addClass('open');
                    $('.single_add_to_cart_button').attr('disabled', true);
                    $('.single_add_to_cart_button').addClass('btn_disabled');
                }
            });
        });
    </script>
<?php endif; ?>

