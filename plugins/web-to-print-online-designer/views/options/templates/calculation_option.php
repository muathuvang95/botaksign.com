<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.calculation_option">'; ?>
    <div class="nbd-field-info" style="margin-top: 10px;">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Enable measure price base on design area', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('Measure price base on design area.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][measure]" ng-model="field.general.measure">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" ng-show="field.general.measure === 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Calculation Method', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][caculation_method]" ng-model="field.general.caculation_method">
                <option value="area"><?php _e('Area', 'web-to-print-online-designer'); ?></option>
                <option value="perimeter"><?php _e('Perimeter', 'web-to-print-online-designer'); ?></option>
                <option value="top-bottom"><?php _e('Top & Bottom', 'web-to-print-online-designer'); ?></option>
                <option value="left-right"><?php _e('Left & Right', 'web-to-print-online-designer'); ?></option>
                <option value="custom-formula"><?php _e('Custom Formula', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" ng-show="field.general.caculation_method === 'custom-formula'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Custom Formular', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('Enter your price formular with 2 parameters: {width}, {height}. <br/> Ex: ({width} + {height}) * 2<br/>Notice: Use dots instead of commas for decimal.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <input name="options[fields][{{fieldIndex}}][general][custom_formular]" type="text" ng-model="field.general.custom_formular"/>
        </div>
    </div>
    <div class="nbd-field-info" ng-show="field.general.measure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Minimum Additional Price', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('Set minimum additional price.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <input name="options[fields][{{fieldIndex}}][general][minimum_price]" type="number" string-to-number ng-model="field.general.minimum_price" step="any" ng-min="0" />
        </div>
    </div>
    <div class="nbd-field-info" ng-show="field.general.measure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Calculate additional price base on ', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <?php /*<select name="options[fields][{{fieldIndex}}][general][measure_base_on]" ng-model="field.general.measure_base_on">
                <option value="s"><?php _e('Size', 'web-to-print-online-designer'); ?></option>
                <option value="d"><?php _e('Custom dimension', 'web-to-print-online-designer'); ?></option>
            </select>
            <p>And</p> */ ?>
            <select name="options[fields][{{fieldIndex}}][general][measure_type]" ng-model="field.general.measure_type">
                <option value="u"><?php _e('Price per Unit', 'web-to-print-online-designer'); ?></option>
                <option value="r"><?php _e('Area breaks ( area range )', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" ng-show="field.general.measure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Option Attributes', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>  
        <div class="nbd-field-info-2">
            <div>
                <div ng-repeat="(opIndex, op) in field.general.attributes.options" class="nbd-attribute-wrap">
                    <div ng-show="op.isExpand" class="nbd-attribute-content-wrap nbd-calculation-wrap">
                        <div><b>{{op.name}}</b></div>
                        <div>
                            <div class="nbd-field-info nbd-field-calculation" ng-repeat="(role_index, role) in op.role_calculation_option">
                                <div class="nbd-calculation-option title" ng-show="!role.isExpand">{{role.role_name}}</div>
                                <div class="nbd-calculation-option" ng-show="role.isExpand">
                                    <div class="nbd-calculation-option-header">
                                        <div class="nbd-field-info-1">
                                            <label>
                                                <b><?php _e('User Role', 'web-to-print-online-designer'); ?></b>
                                            </label>
                                        </div>
                                        <div class="nbd-field-info-2">
                                            <select name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][role]" class="select-role" ng-change="changeRoleCalculationOption(fieldIndex, opIndex, role_index)" ng-model="role.role">
                                                <?php
                                                    global $wp_roles;
                                                    $all_roles = $wp_roles->roles;
                                                ?>
                                                <?php foreach ($all_roles as $key => $role): ?>
                                                    <option value="<?php echo $key?>" label="<?php echo $role['name']?>" ng-selected="'<?php echo $key;?>' === role.role"><?php echo $role['name']?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="nbd-calculation-option-body">
                                        <div class="nbd-table-wrap">
                                            <div>
                                                <label><b>Use as default</b></label>
                                                <input ng-checked="role.default" ng-click="update_default_calculation_measure_range( fieldIndex, opIndex, role_index )" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][default]" type="checkbox" ng-true-value="'on'" ng-false-value="'off'"/>
                                            </div>
                                            <table class="nbd-table nbo-measure-range" ng-if="field.general.measure_type == 'r'">
                                                <thead>
                                                    <tr>
                                                        <th class="check-column">
                                                            <input class="nbo-measure-range-select-all" type="checkbox" ng-click="select_all_calculation_measure_range(fieldIndex, opIndex, role_index, $event)">
                                                        </th>
                                                        <th class="range-column" style="padding-right: 30px;">
                                                            <span class="column-title" data-text="<?php esc_attr_e( 'Measurement Range', 'web-to-print-online-designer' ); ?>"><?php _e( 'Measurement Range', 'web-to-print-online-designer' ); ?></span>
                                                            <nbd-tip data-tip="<?php _e( 'Configure the starting-ending range, inclusive, of measurements to match this rule.  The first matched rule will be used to determine the price.  The final rule can be defined without an ending range to match all measurements greater than or equal to its starting range.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                                                        </th>
                                                        <th class="price-column">
                                                            <div class="measure-unit">
                                                                <span class="title"><?php _e('Price/Unit', 'web-to-print-online-designer'); ?></span>
                                                                <select name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][measure_unit]" class="select-calculation-measure-unit" ng-model="role.measure_unit">
                                                                    <option value="sqmm"><?php echo get_woocommerce_currency_symbol() . '/mm<sup>2</sup>'; ?></option>
                                                                    <option value="sqcm"><?php echo get_woocommerce_currency_symbol() . '/cm<sup>2</sup>'; ?></option>
                                                                    <option value="sqin"><?php echo get_woocommerce_currency_symbol() . '/in<sup>2</sup>'; ?></option>
                                                                    <option value="sqft"><?php echo get_woocommerce_currency_symbol() . '/ft<sup>2</sup>'; ?></option>
                                                                </select>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="(rangeIndex, range) in role.measure_range">
                                                        <td>
                                                            <input type="checkbox" class="nbo-measure-range-checkbox" ng-model="range[3]">
                                                        </td>
                                                        <td>
                                                            <span>
                                                                <span class="nbd-table-price-label"><?php echo _e('From', 'web-to-print-online-designer'); ?></span>
                                                                <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][measure_range][{{rangeIndex}}][0]" ng-model="range[0]" class="nbd-short-ip">
                                                            </span>
                                                            <span>
                                                                <span class="nbd-table-price-label" style="margin-left: 10px;"><?php echo _e('To', 'web-to-print-online-designer'); ?></span>
                                                                <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][measure_range][{{rangeIndex}}][1]" ng-model="range[1]" class="nbd-short-ip">
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <input string-to-number type="number" min="0" step="0.01" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][measure_range][{{rangeIndex}}][2]" ng-model="range[2]" class="nbd-short-ip">
                                                        </td>
                                                    </tr> 
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="3">
                                                            <button ng-click="add_calculation_measure_range(fieldIndex, opIndex, role_index)" style="float: left;" type="button" class="button button-primary nbd-pricing-table-add-rule"><?php _e( 'Add Rule', 'web-to-print-online-designer' ); ?></button>
                                                            <button ng-click="delete_calculation_measure_range(fieldIndex, opIndex, role_index, $event)" style="float: right;" type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php _e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                                                        </th>
                                                    </tr>
                                                </tfoot> 
                                            </table>
                                            <table class="nbd-table nbo-measure-range" ng-if="field.general.measure_type == 'u'">
                                                <thead>
                                                    <tr>
                                                        <th class="price-column">
                                                            <div class="measure-unit">
                                                                <span class="title"><?php _e('Price/Unit', 'web-to-print-online-designer'); ?></span>
                                                                <select name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][measure_unit]" class="select-calculation-measure-unit" ng-model="role.measure_unit">
                                                                    <option value="sqmm"><?php echo get_woocommerce_currency_symbol() . '/mm<sup>2</sup>'; ?></option>
                                                                    <option value="sqcm"><?php echo get_woocommerce_currency_symbol() . '/cm<sup>2</sup>'; ?></option>
                                                                    <option value="sqin"><?php echo get_woocommerce_currency_symbol() . '/in<sup>2</sup>'; ?></option>
                                                                    <option value="sqft"><?php echo get_woocommerce_currency_symbol() . '/ft<sup>2</sup>'; ?></option>
                                                                </select>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php //CS botak Calculate additional price based on Calculate additional price based on with only one field (range index = 0) ?>
                                                    <tr>
                                                        <td>
                                                            <input string-to-number type="number" min="0" step="0.01" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_calculation_option][{{role_index}}][measure_range][0][2]" ng-model="role.measure_range[0][2]" class="nbd-short-ip">
                                                        </td>
                                                    </tr> 
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="nbd-field-info-3 field-action">
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteRoleCalculationOption(fieldIndex, opIndex, role_index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyRoleCalculationOption(fieldIndex, opIndex, role_index)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleRoleCalculationOption(fieldIndex, opIndex, role_index, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!role.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="role.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                                </div>
                            </div>
                            <div style="display: inline-block; width: 100%;">
                                <a class="button" ng-click="addRoleCalculationOption(fieldIndex, opIndex)" style="float: right;"><span class="dashicons dashicons-plus"></span> <?php _e('Add role block', 'web-to-print-online-designer'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div ng-show="!op.isExpand" class="nbd-attribute-name-preview">{{op.name}}</div>
                    <div class="nbd-attribute-action">
                        <a class="button nbd-mini-btn"  ng-click="toggle_expand_attribute(fieldIndex, opIndex)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                            <span ng-show="op.isExpand" class="dashicons dashicons-arrow-up"></span>
                            <span ng-show="!op.isExpand" class="dashicons dashicons-arrow-down"></span>
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
<?php echo '</script>'; ?>