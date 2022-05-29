<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_depend_quantity">'; ?>
    <div class="nbd-field-info" ng-show="field.nbd_type !== 'size' && field.nbd_type !== 'dimension' && field.nbd_type !== 'pricing_rates' && field.nbd_type !== 'production_time' && field.nbd_type !== 'terms_conditions' ">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Depend quantity breaks', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][depend_quantity]" ng-model="field.general.depend_quantity.value">
                    <option ng-repeat="op in field.general.depend_quantity.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';