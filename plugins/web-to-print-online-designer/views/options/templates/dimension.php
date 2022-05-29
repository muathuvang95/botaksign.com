<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.dimension">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Dimension range:', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <table class="nbd-table">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e('Min', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Max', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Step', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Default value', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?php _e('Width', 'web-to-print-online-designer'); ?></th>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.min_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][min_width]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.max_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][max_width]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.step_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][step_width]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.default_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][default_width]" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Height', 'web-to-print-online-designer'); ?></th>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.min_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][min_height]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.max_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][max_height]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.step_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][step_height]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.default_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][default_height]" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
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
    <div class="nbd-field-info" ng-if="field.general.measure == 'y'">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Price base on design area:', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-field-info nbd-field-product-size-price" ng-repeat="(role_index, role) in field.general.role_measure_range">
                <div class="nbd-product-size-price-block">
                    <div class="nbd-product-size-price-header">
                        <div class="nbd-product-size-price-block title" ng-show="!role.isExpand">{{role.role_name}}</div>
                        <div class="nbd-field-info-1" ng-show="role.isExpand">
                            <label>
                                <b><?php _e('User Role', 'web-to-print-online-designer'); ?></b>
                            </label>
                        </div>
                        <div class="nbd-field-info-2" ng-show="role.isExpand">
                            <select name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][role]" class="select-role" ng-change="changeRoleMeasure(fieldIndex, role_index)" ng-model="role.role">
                                <option value="all" label="All" ng-selected="'all' === role.role">All</option>
                                <?php
                                    global $wp_roles;
                                    $all_roles = $wp_roles->roles;
                                ?>
                                <?php foreach ($all_roles as $key => $role): ?>
                                    <option value="<?php echo $key?>" label="<?php echo $role['name']?>" ng-selected="'<?php echo $key;?>' === role.role"><?php echo $role['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="nbd-field-info-3 field-action">
                            <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteRoleMeasureRange(fieldIndex, role_index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                            <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyRoleMeasureRange(fieldIndex, role_index)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                            <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleRoleMeasureRange(fieldIndex, role_index, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!role.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="role.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                        </div>
                    </div>
                    <div class="nbd-product-size-price-body" ng-show="role.isExpand">
                        <div class="nbd-table-wrap" style="overflow: hidden;">
                            <div>
                                <label><b>Use as default</b></label>
                                <input ng-checked="role.default" ng-click="update_default_role_measure_range( fieldIndex, role_index )" name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][default]" type="checkbox" ng-true-value="'on'" ng-false-value="'off'"/>
                            </div>
                            <div class="nbd-margin-10"></div>
                            <div class="component-container" ng-repeat="(measureIndex, measure) in role.multi_measure">
                                <div class="component-header">
                                    <label><b>Component {{measureIndex + 1}}</b></label>
                                    <div class="nbd-field-info-3 field-action">
                                        <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteComponentMeasure(fieldIndex, role_index, measureIndex)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                        <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyComponentMeasure(fieldIndex, role_index, measureIndex)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                                        <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleComponentMeasure(fieldIndex, role_index, measureIndex, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!measure.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="measure.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                                    </div>
                                </div>
                                <div ng-show="measure.isExpand">
                                    <div class="nbd-margin-10"></div>
                                    <div class="nbd-field-info" ng-show="field.general.measure_price === 'y'">
                                        <div class="nbd-field-info-1">
                                            <div>
                                                <label>
                                                    <b><?php _e('Calculation Method', 'web-to-print-online-designer'); ?></b>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="nbd-field-info-2">
                                            <select name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][caculation_method]" ng-model="measure.caculation_method">
                                                <option value="area"><?php _e('Area', 'web-to-print-online-designer'); ?></option>
                                                <option value="perimeter"><?php _e('Perimeter', 'web-to-print-online-designer'); ?></option>
                                                <option value="top-bottom"><?php _e('Top & Bottom', 'web-to-print-online-designer'); ?></option>
                                                <option value="left-right"><?php _e('Left & Right', 'web-to-print-online-designer'); ?></option>
                                                <option value="custom-formula"><?php _e('Custom Formula', 'web-to-print-online-designer'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="nbd-field-info" ng-show="measure.caculation_method === 'custom-formula'">
                                        <div class="nbd-field-info-1">
                                            <div>
                                                <label>
                                                    <b><?php _e('Custom Formular', 'web-to-print-online-designer'); ?></b>
                                                    <nbd-tip data-tip="<?php _e('Enter your price formular with 2 parameters: {width}, {height}. <br/> Ex: ({width} + {height}) * 2<br/>Notice: Use dots instead of commas for decimal.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="nbd-field-info-2">
                                            <input name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][custom_formular]" type="text" ng-model="measure.custom_formular"/>
                                        </div>
                                    </div>
                                    <div class="nbd-field-info">
                                        <div class="nbd-field-info-1">
                                            <div>
                                                <label>
                                                    <b><?php _e('Minimum Additional Price', 'web-to-print-online-designer'); ?></b>
                                                    <nbd-tip data-tip="<?php _e('Set minimum additional price.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="nbd-field-info-2">
                                            <input name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][minimum_price]" type="number" string-to-number ng-model="measure.minimum_price" step="any" ng-min="0" />
                                        </div>
                                    </div>
                                    <?php //Calculate price in unit or area range  ?>
                                    <div class="nbd-field-info" style="margin-top: 10px;">
                                        <div class="nbd-field-info-1">
                                            <div>
                                                <label>
                                                    <b><?php _e('Calculate additional price base on ', 'web-to-print-online-designer'); ?></b>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="nbd-field-info-2">
                                            <select name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_type]" ng-model="measure.measure_type">
                                                <option value="u"><?php _e('Price per Unit', 'web-to-print-online-designer'); ?></option>
                                                <option value="r"><?php _e('Value break', 'web-to-print-online-designer'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <table class="nbd-table nbo-measure-range" ng-if="measure.measure_type == 'r'">
                                        <thead>
                                            <tr>
                                                <th class="check-column">
                                                    <input class="nbo-measure-range-select-all" type="checkbox" ng-click="select_all_measure_range(fieldIndex, role_index, measureIndex, $event)">
                                                </th>
                                                <th class="range-column" style="padding-right: 30px;">
                                                    <span class="column-title" data-text="<?php esc_attr_e( 'Measurement Range', 'web-to-print-online-designer' ); ?>"><?php _e( 'Measurement Range', 'web-to-print-online-designer' ); ?></span>
                                                    <nbd-tip data-tip="<?php _e( 'Configure the starting-ending range, inclusive, of measurements to match this rule.  The first matched rule will be used to determine the price.  The final rule can be defined without an ending range to match all measurements greater than or equal to its starting range.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                                                </th>
                                                <th class="price-column">
                                                    <div class="measure-unit">
                                                        <span class="title"><?php _e('Price/Unit', 'web-to-print-online-designer'); ?></span>
                                                        <select name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_unit]" class="select-measure-unit" ng-model="measure.measure_unit">
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
                                            <tr ng-repeat="(rangeIndex, range) in measure.measure_range">
                                                <td>
                                                    <input type="checkbox" class="nbo-measure-range-checkbox" ng-model="range[3]">
                                                </td>
                                                <td>
                                                    <span>
                                                        <span class="nbd-table-price-label"><?php echo _e('From', 'web-to-print-online-designer'); ?></span>
                                                        <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_range][{{rangeIndex}}][0]" ng-model="range[0]" class="nbd-short-ip">
                                                    </span>
                                                    <span>
                                                        <span class="nbd-table-price-label" style="margin-left: 10px;"><?php echo _e('To', 'web-to-print-online-designer'); ?></span>
                                                        <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_range][{{rangeIndex}}][1]" ng-model="range[1]" class="nbd-short-ip">
                                                    </span>
                                                </td>
                                                <td>
                                                    <input string-to-number type="number" min="0" step="0.01" name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_range][{{rangeIndex}}][2]" ng-model="range[2]" class="nbd-short-ip">
                                                </td>
                                            </tr> 
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3">
                                                    <button ng-click="add_measure_range(fieldIndex, role_index, measureIndex)" style="float: left;" type="button" class="button button-primary nbd-pricing-table-add-rule"><?php _e( 'Add Rule', 'web-to-print-online-designer' ); ?></button>
                                                    <button ng-click="delete_measure_range(fieldIndex, role_index, measureIndex, $event)" style="float: right;" type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php _e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                                                </th>
                                            </tr>
                                        </tfoot> 
                                    </table>
                                    <table class="nbd-table nbo-measure-range" ng-if="measure.measure_type == 'u'">
                                        <thead>
                                            <tr>
                                                <th class="price-column">
                                                    <div class="measure-unit">
                                                        <span class="title"><?php _e('Price/Unit', 'web-to-print-online-designer'); ?></span>
                                                        <select name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_unit]" class="select-measure-unit" ng-model="measure.measure_unit">
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
                                                    <input string-to-number type="number" min="0" step="0.01" name="options[fields][{{fieldIndex}}][general][role_measure_range][{{role_index}}][multi_measure][{{measureIndex}}][measure_range][0][2]" ng-model="measure.measure_range[0][2]" class="nbd-short-ip">
                                                </td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div style="display: inline-block; width: 100%;">
                            <a class="button" ng-click="addComponentMeasure(fieldIndex, role_index)" style="float: right;"><span class="dashicons dashicons-plus"></span> <?php _e('Add component', 'web-to-print-online-designer'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display: inline-block; width: 100%;">
                <a class="button" ng-click="addRoleMeasureRange(fieldIndex)" style="float: right;"><span class="dashicons dashicons-plus"></span> <?php _e('Add role block', 'web-to-print-online-designer'); ?></a>
            </div>
        </div>
        <?php /*
        <div class="nbd-field-info-2">
            <table class="nbd-table nbo-measure-range">
                <thead>
                    <tr>
                        <th class="check-column">
                            <input class="nbo-measure-range-select-all" type="checkbox" ng-click="select_all_measurement_range(fieldIndex, $event)">
                        </th>
                        <th class="range-column" style="padding-right: 30px;">
                            <span class="column-title" data-text="<?php esc_attr_e( 'Measurement Range', 'web-to-print-online-designer' ); ?>"><?php _e( 'Measurement Range', 'web-to-print-online-designer' ); ?></span>
                            <nbd-tip data-tip="<?php _e( 'Configure the starting-ending range, inclusive, of measurements to match this rule.  The first matched rule will be used to determine the price.  The final rule can be defined without an ending range to match all measurements greater than or equal to its starting range.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                        </th>
                        <th class="price-column">
                            <span ng-show="field.general.measure_type == 'u'"><?php _e('Price per Unit', 'web-to-print-online-designer'); ?> <?php echo ' ('.get_woocommerce_currency_symbol() . '/' . nbdesigner_get_option('nbdesigner_dimensions_unit').'<sup>2</sup>)'; ?></span>
                            <span ng-show="field.general.measure_type == 'r'"><?php _e('Fixed amount', 'web-to-print-online-designer'); ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(mrIndex, mr) in field.general.measure_range">
                        <td>
                            <input type="checkbox" class="nbo-measure-range-checkbox" ng-model="mr[3]">
                        </td>
                        <td>
                            <span>
                                <span class="nbd-table-price-label"><?php echo _e('From', 'web-to-print-online-designer'); ?></span>
                                <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][measure_range][{{mrIndex}}][0]" ng-model="mr[0]" class="nbd-short-ip">
                            </span>
                            <span>
                                <span class="nbd-table-price-label" style="margin-left: 10px;"><?php echo _e('To', 'web-to-print-online-designer'); ?></span>
                                <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][measure_range][{{mrIndex}}][1]" ng-model="mr[1]" class="nbd-short-ip">
                            </span>
                        </td>
                        <td>
                            <input string-to-number type="number" step="any" name="options[fields][{{fieldIndex}}][general][measure_range][{{mrIndex}}][2]" ng-model="mr[2]" class="nbd-short-ip">
                        </td>
                    </tr> 
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">
                            <button ng-click="add_measurement_range(fieldIndex)" style="float: left;" type="button" class="button button-primary nbd-pricing-table-add-rule"><?php _e( 'Add Rule', 'web-to-print-online-designer' ); ?></button>
                            <button ng-click="delete_measurement_ranges(fieldIndex, $event)" style="float: right;" type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php _e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                        </th>
                    </tr>
                </tfoot> 
            </table>
        </div>
         */?>
    </div>
    <div class="nbd-field-info" style="margin-top: 10px;" ng-if="field.general.measure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Calculate price base on number of sides/pages', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][measure_base_pages]" ng-model="field.general.measure_base_pages">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
<?php echo '</script>';