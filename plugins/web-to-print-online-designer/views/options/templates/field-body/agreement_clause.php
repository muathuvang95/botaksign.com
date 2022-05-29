<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_agreement_clause">'; ?>
    <div class="nbd-field-info" ng-show="field.nbd_type && field.nbd_type == 'terms_conditions'">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Agreement Clause', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <textarea name="options[fields][{{fieldIndex}}][general][agreement_clause]" ng-model="field.general.agreement_clause.value"></textarea>
            </div>
        </div>
    </div>
<?php echo '</script>';