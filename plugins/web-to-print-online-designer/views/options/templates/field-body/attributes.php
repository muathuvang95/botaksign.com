<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_attributes">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.attributes)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Attributes', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Attributes let you define extra product data, such as size or color.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>  
        <div class="nbd-field-info-2">
            <div ng-if="field.nbd_type !== 'production_time'">
                <div ng-repeat="(opIndex, op) in field.general.attributes.options" class="nbd-attribute-wrap">
                    <div ng-show="op.isExpand" class="nbd-attribute-img-wrap" ng-if="field.nbd_type !== 'terms_conditions'">
                        <div><?php _e('Swatch type', 'web-to-print-online-designer'); ?></div>
                        <div>
                            <select ng-model="op.preview_type" style="width: 110px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][preview_type]">
                                <option value="i"><?php _e('Image', 'web-to-print-online-designer'); ?></option>
                                <option value="c"><?php _e('Color', 'web-to-print-online-designer'); ?></option>
                            </select>
                        </div>
                        <div class="nbd-attribute-img-inner" ng-show="op.preview_type == 'i'">
                            <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_image(fieldIndex, $index, 'image', 'image_url')"></span>
                            <input ng-hide="true" ng-model="op.image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][image]"/>
                            <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, $index, 'image', 'image_url')" ng-src="{{op.image != 0 ? op.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                        </div>
                        <div class="nbd-attribute-color-inner" ng-show="op.preview_type == 'c'">
                            <input type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][color]" ng-model="op.color" class="nbd-color-picker" nbd-color-picker="op.color"/>
                            <span class="add-color2" ng-click="add_remove_second_color(fieldIndex, $index)"><span ng-show="!op.color2">+</span><span ng-show="op.color2">-</span></span>
                            <input ng-if="op.color2" type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][color2]" ng-model="op.color2" class="nbd-color-picker" nbd-color-picker="op.color2"/>
                        </div>
                        <?php //CS botak gallery option ?>
                        <div><?php _e('Product image', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-img-inner">
                            <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_image(fieldIndex, $index, 'product_image', 'product_image_url')"></span>
                            <input ng-hide="true" ng-model="op.product_image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][product_image]"/>
                            <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, $index, 'product_image', 'product_image_url')" ng-src="{{op.product_image_url ? op.product_image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                        </div>
                    </div>
                    <div ng-show="field.nbd_type == 'terms_conditions'">
                        <div class="nbd-attribute-name">
                            <textarea placeholder="<?php _e('Description', 'web-to-print-online-designer'); ?>" value="{{op.des}}" ng-model="op.des" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][des]" style="width: 100%;height: 300px;"></textarea>
                        </div> 
                    </div>
                    <div class="nbd-attribute-content-wrap" ng-show="op.isExpand" ng-if="field.nbd_type !== 'terms_conditions'">
                        <div><?php _e('Title', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-name">
                            <input required type="text" value="{{op.name}}" ng-model="op.name" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][name]"/>
                            <label><input type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][selected]" ng-checked="op.selected" ng-click="seleted_attribute(fieldIndex, 'attributes', $index)"/> <?php _e('Default', 'web-to-print-online-designer'); ?></label>
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div><?php _e('SKU:', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-name">
                            <input type="text" ng-model="op.sku" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sku]" />
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div><?php _e('Description', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-name">
                            <textarea placeholder="<?php _e('Description', 'web-to-print-online-designer'); ?>" value="{{op.des}}" ng-model="op.des" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][des]"></textarea>
                        </div> 
                        <div class="nbd-margin-10"></div>
                        <?php /* //Cs botak image gallery admin option ?>
                        <div class="nbd-attribute-img-gallery" ng-if="field.appearance.change_image_product.value == 'y'">
                            <div><?php _e('Product gallery', 'web-to-print-online-designer'); ?></div>
                            <div class="nbd-attribute-img-inner" ng-repeat="(imgIndex, img) in op.product_images">
                                <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_gallery_image(imgIndex, fieldIndex, opIndex)"></span>
                                <input ng-hide="true" ng-model="img.product_image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][product_images][{{imgIndex}}][product_image]"/>
                                <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_gallery_image(imgIndex, fieldIndex, opIndex)" ng-src="{{img.product_image_url ? img.product_image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                            </div>
                            <div class="nbd-attribute-img-inner add-product-gallery-image" ng-click="add_attribute_gallery_image(fieldIndex, opIndex)">
                                <div class="btn-add">+</div>
                            </div>
                        </div>
                        <div class="nbd-margin-10" ng-if="field.appearance.change_image_product.value == 'y'"></div>
                        <div class="nbd-enable-attribute-con" ng-if="field.appearance.change_image_product.value == 'y'">
                            <label><input type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][gallery_enable_con]" ng-true-value="'on'" ng-false-value="'off'" ng-model="op.gallery_enable_con" ng-checked="op.gallery_enable_con" /> <?php _e('Enable Gallery Conditional Logic', 'web-to-print-online-designer'); ?></label>
                        </div>
                        <div class="nbd-subattributes-wrapper" ng-if="field.appearance.change_image_product.value == 'y' && (op.gallery_enable_con == true || op.gallery_enable_con == 'on')">
                            <div>
                                <?php _e('Show this gallery if', 'web-to-print-online-designer'); ?>
                                <select ng-model="op.gallery_con_logic" style="width: inherit;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][gallery_con_logic]">
                                    <option value="a"><?php _e('all', 'web-to-print-online-designer'); ?></option>
                                    <option value="o"><?php _e('any', 'web-to-print-online-designer'); ?></option>
                                </select>
                                <?php _e('of these rules match:', 'web-to-print-online-designer'); ?>
                            </div>
                            <div class="nbd-margin-10"></div>
                            <div>
                                <div ng-repeat="(cdIndex, con) in op.gallery_depend">
                                    <select ng-model="con.id" style="width: 100px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][gallery_depend][{{cdIndex}}][id]">
                                        <option ng-repeat="cf in options.fields | filter: { id: field.id }:excludeField" value="{{cf.id}}">{{cf.general.title.value}}</option>
                                        <option value="qty"><?php _e('Quantity', 'web-to-print-online-designer'); ?></option>
                                    </select>
                                    <select ng-model="con.operator" style="width: 100px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][gallery_depend][{{cdIndex}}][operator]">
                                        <option ng-if="con.id != 'qty'" value="i"><?php _e('is', 'web-to-print-online-designer'); ?></option>
                                        <option ng-if="con.id != 'qty'" value="n"><?php _e('is not', 'web-to-print-online-designer'); ?></option>
                                        <option ng-if="con.id != 'qty'" value="e"><?php _e('is empty', 'web-to-print-online-designer'); ?></option>
                                        <option ng-if="con.id != 'qty'" value="ne"><?php _e('is not empty', 'web-to-print-online-designer'); ?></option>
                                        <option ng-if="con.id == 'qty'" value="eq"><?php _e('equal', 'web-to-print-online-designer'); ?></option>
                                        <option ng-if="con.id == 'qty'" value="gt"><?php _e('great than', 'web-to-print-online-designer'); ?></option>
                                        <option ng-if="con.id == 'qty'" value="lt"><?php _e('less than', 'web-to-print-online-designer'); ?></option>
                                    </select>
                                    <select ng-if="(con.operator == 'i' || con.operator == 'n') && con.id != 'qty'" ng-model="con.val" ng-repeat="vf in options.fields | filter: {id: con.id}:includeField"  
                                        name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][gallery_depend][{{cdIndex}}][val]" style="width: 100px;">
                                        <option ng-repeat="vop in vf.general.attributes.options" value="{{$index}}">{{vop.name}}</option>
                                    </select>
                                    <nbo-sub-attr-select ng-if="(con.operator == 'i' || con.operator == 'n') && con.id != 'qty' && con.id != '' && con.val != ''" 
                                        find="fieldIndex" oind="opIndex" cind="cdIndex" con="con" fields="options.fields" ></nbo-sub-attr-select>
                                    <input ng-if="con.id == 'qty'" type="text" ng-model="con.val" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][gallery_depend][{{cdIndex}}][val]" style="width: 100px !important; vertical-align: middle;" />
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="add_gallery_condition(fieldIndex, opIndex)"><span class="dashicons dashicons-plus"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="delete_gallery_condition(fieldIndex, opIndex, cdIndex)"><span class="dashicons dashicons-no-alt"></span></a>
                                </div>
                            </div>
                        </div>
                        */ ?>
                        <div class="nbd-margin-10"></div>
                        <?php /*<div><?php _e('Price', 'web-to-print-online-designer'); ?></div> */ //CS botak ?>
                        <div ng-show="field.general.depend_quantity.value != 'y' && ((field.nbd_type !== 'size' && field.nbd_type !== 'dimension' && field.nbd_type !== 'pricing_rates') || field.general.measure_price == 'n')" ng-if="field.nbd_type !== 'terms_conditions'"> <?php //CS botak break role measure ?>
                            <div><?php _e('Additional Price', 'web-to-print-online-designer'); ?></div>
                            <div>
                                <input autocomplete="off" ng-click="initFormulaPrice(op.price[0], 0, fieldIndex, opIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][price][0]" class="nbd-short-ip" type="text" ng-model="op.price[0]"/>
                            </div>
                        </div>
                        <div ng-if="field.nbd_type === 'pricing_rates'">
                            <div class="nbd-field-info nbd-field-pricing-rates" ng-repeat="(role_index, role) in op.role_pricing_rates">
                                <div class="nbd-pricing-rates title" ng-show="!role.isExpand">{{role.role_name}}</div>
                                <div class="nbd-pricing-rates" ng-show="role.isExpand">
                                    <div class="nbd-pricing-rates-header">
                                        <div class="nbd-field-info-1">
                                            <label>
                                                <b><?php _e('User Role', 'web-to-print-online-designer'); ?></b>
                                            </label>
                                        </div>
                                        <div class="nbd-field-info-2">
                                            <select name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_pricing_rates][{{role_index}}][role]" class="select-role" ng-change="changeRolePricingRates(fieldIndex, opIndex, role_index)" ng-model="role.role">
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
                                    <div class="nbd-pricing-rates-body">
                                        <div class="nbd-table-wrap" style="overflow: hidden;">
                                            <div><?php _e('Rate', 'web-to-print-online-designer'); ?></div>
                                            <div class="nbd-attribute-name">
                                                <input name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_pricing_rates][{{role_index}}][role_rate]" type="number" string-to-number ng-model="field.general.attributes.options[opIndex].role_pricing_rates[role_index].role_rate" step="0.01" ng-min="0"/>
                                            </div>
                                            <div class="nbd-margin-10"></div>
                                            <div><?php _e('Quantity', 'web-to-print-online-designer'); ?></div>
                                            <div class="nbd-attribute-name">
                                                <input name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_pricing_rates][{{role_index}}][role_quantity]" type="number" string-to-number ng-model="field.general.attributes.options[opIndex].role_pricing_rates[role_index].role_quantity" step="1" ng-min="0"/>
                                            </div>
                                            <div class="nbd-margin-10"></div>
                                            <div><?php _e('Quantity', 'web-to-print-online-designer'); ?></div>
                                            <div class="nbd-attribute-name">
                                                <select name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][role_pricing_rates][{{role_index}}][role_calc_method]" ng-model="field.general.attributes.options[opIndex].role_pricing_rates[role_index].role_calc_method">
                                                    <option value="area"><?php _e('Area', 'web-to-print-online-designer'); ?></option>
                                                    <option value="perimeter"><?php _e('Perimeter', 'web-to-print-online-designer'); ?></option>
                                                    <option value="quantity"><?php _e('Quantity', 'web-to-print-online-designer'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="nbd-field-info-3 field-action">
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteRolePricingRates(fieldIndex, opIndex, role_index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyRolePricingRates(fieldIndex, opIndex, role_index)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleRolePricingRates(fieldIndex, opIndex, role_index, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!role.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="role.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                                </div>
                            </div>
                            <div style="display: inline-block; width: 100%;">
                                <a class="button" ng-click="addRolePricingRates(fieldIndex, opIndex)" style="float: right;"><span class="dashicons dashicons-plus"></span> <?php _e('Add role block', 'web-to-print-online-designer'); ?></a>
                            </div>
                        </div>
                        <div class="nbd-table-wrap" ng-show="field.general.depend_quantity.value == 'y' && field.nbd_type !== 'size' && field.nbd_type !== 'dimension' && field.nbd_type !== 'pricing_rates'" >
                            <table class="nbd-table">
                                <tr>
                                    <th><?php _e('Quantity break', 'web-to-print-online-designer'); ?></th>
                                    <th ng-repeat="break in options.quantity_breaks">{{break.val}}</th>
                                </tr>
                                <tr>
                                    <td><?php _e('Additional Price', 'web-to-print-online-designer'); ?></td>
                                    <td ng-repeat="break in options.quantity_breaks">
                                        <input autocomplete="off" ng-click="initFormulaPrice(op.price[$index], $index, fieldIndex, opIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][price][{{$index}}]" class="nbd-short-ip" type="text" ng-model="op.price[$index]"/>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div ng-if="field.nbd_type == 'size'">
                            <div class="nbd-margin-10"></div><hr />
                            <div class="nboption-designer-inner">
                                <div class="option-attrs">
                                    <div class="nboption-info-box-inner">
                                        <label class="nboption-setting-box-label"><?php esc_html_e('Show bleed', 'web-to-print-online-designer'); ?> <span class="nbd-bleed-notation"></span></label>
                                        <div>
                                            <!--<input type="hidden" value="0" class="show_bleed" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][show_bleed][{{$index}}]"/>-->
                                            <input type="checkbox" class="show_bleed"
                                                    ng-true-value="'on'" ng-false-value="'off'"
                                                    name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][show_bleed]"
                                                    class="short nbd-dependence"
                                                    ng-model="op.show_bleed" ng-checked="op.show_bleed"
                                                    /> 
                                        </div>
                                    </div> 
                                    <div id="nbd-bleed{{fieldIndex}}" class="nbd-bleed-con nbd-independence" ng-if="op.show_bleed === 'on'">
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Bleed top', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any" min="0" style="margin-left: 15px;" 
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_top]" 
                                                       value="{{op.bleed_top === '' ? 0 : op.bleed_top}}"
                                                       class="short bleed_top">
                                            </div>
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Bleed bottom', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any" min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_bottom]" 
                                                       value="{{op.bleed_bottom === '' ? 0 : op.bleed_bottom}}"
                                                       class="short bleed_bottom">
                                            </div>
                                        </div>
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Bleed left', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any"  min="0" style="margin-left: 15px;"
                                                        ng-app=""name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_left]"
                                                        value="{{op.bleed_left === '' ? 0 : op.bleed_left}}" class="short bleed_left">
                                            </div>
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Bleed right', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any"  min="0"
                                                        ng-app=""name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_right]"
                                                        value="{{op.bleed_right === '' ? 0 : op.bleed_right}}" class="short bleed_right">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--- CS add new Bleed Corner Radius -->
                                <div class="option-attrs">
                                    <div class="nboption-info-box-inner">
                                        <label class="nboption-setting-box-label"><?php esc_html_e('Bleed Corner Radius', 'web-to-print-online-designer'); ?>
                                        <div style="padding-left: 15px">
                                            <!--<input type="hidden" value="0" class="show_bleed" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][show_bleed][{{$index}}]"/>-->
                                            ( Circle
                                            <input type="checkbox" class="show_bleed"
                                                    ng-true-value="'on'" ng-false-value="'off'"
                                                    name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_corner_radius]"
                                                    class="short nbd-dependence"
                                                    ng-model="op.bleed_corner_radius" ng-checked="op.bleed_corner_radius"  style="margin-top: -5px" 
                                                    />)
                                        </div>
                                    </div> 
                                    <div id="nbd-bleed-corner{{fieldIndex}}" class="nbd-bleed-con nbd-independence">
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/2-01.svg'; ?>"></label>
                                                <input type="number" step="any" min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_corner_top]" 
                                                       value="{{op.bleed_corner_top}}"
                                                       class="short bleed_corner_top">

                                            </div>
                                            <div>
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/3-01.svg'; ?>"></label>
                                                <input type="number" step="any" min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_corner_bottom]" 
                                                       value="{{op.bleed_corner_bottom}}"
                                                       class="short bleed_corner_bottom">

                                            </div>
                                        </div>
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <input type="number" step="any"  min="0"
                                                        ng-app=""name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_corner_left]"
                                                        value="{{op.bleed_corner_left}}" class="short bleed_corner_left">
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/1-01.svg'; ?>"></label>
                                            </div>
                                            <div>
                                                <input type="number" step="any"  min="0"
                                                        ng-app=""name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bleed_corner_right]"
                                                        value="{{op.bleed_corner_right}}" class="short bleed_corner_right">
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/4-01.svg'; ?>"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--- End  -->

                                <div class="option-attrs">
                                    <div class="nboption-info-box-inner">
                                        <label class="nboption-setting-box-label"><?php esc_html_e('Show safe zone', 'web-to-print-online-designer'); ?> <span class="nbd-safe-zone-notation"></span></label>
                                        <div>
                                            <input type="checkbox"
                                                    ng-true-value="'on'" ng-false-value="'off'"
                                                    class="show_safe_zone"
                                                    name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][show_safe_zone]"
                                                    class="short nbd-dependence"
                                                    ng-model="op.show_safe_zone" ng-checked="op.show_safe_zone"/>
                                        </div>
                                    </div>
                                    <div id="nbd-safe-zone{{fieldIndex}}" class="nbd-safe-zone-con nbd-independence" ng-if="op.show_safe_zone === 'on'">
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Magin top', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any"  min="0" style="margin-left: 15px;"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][margin_top]"
                                                       value="{{op.margin_top === '' ? 0 : op.margin_top}}"
                                                       class="short margin_top">
                                            </div>
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Magin bottom', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any"  min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][margin_bottom]"
                                                       value="{{op.margin_bottom === '' ? 0 : op.margin_bottom}}"
                                                       class="short margin_bottom">
                                            </div>
                                        </div>
                                        <div class="nboption-info-box-inner">                                         
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Magin left', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any"  min="0" style="margin-left: 15px;"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][margin_left]"
                                                       value="{{op.margin_left === '' ? 0 : op.margin_left}}"
                                                       class="short margin_left">
                                            </div>
                                            <div>
                                                <label class="nboption-setting-box-label"><?php esc_html_e('Magin right', 'web-to-print-online-designer'); ?></label>
                                                <input type="number" step="any"  min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][margin_right]"
                                                       value="{{op.margin_right === '' ? 0 : op.margin_right}}"
                                                       class="short margin_right">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!--- CS add new Safe Corner Radius -->
                                <div class="option-attrs">
                                    <div class="nboption-info-box-inner">
                                        <label class="nboption-setting-box-label"><?php esc_html_e('Safe cn-radius', 'web-to-print-online-designer'); ?>
                                        <div style="padding-left: 15px">
                                            (Circle
                                            <input type="checkbox"
                                                    ng-true-value="'on'" ng-false-value="'off'"
                                                    name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][safe_corner_radius]"
                                                    class="short nbd-dependence"
                                                    ng-model="op.safe_corner_radius" ng-checked="op.safe_corner_radius" style="margin-top: -5px" />)
                                        </div>
                                    </div>
                                    <div id="nbd-safe-corner{{fieldIndex}}" class="nbd-safe-zone-con nbd-independence">
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px;" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/2-01.svg'; ?>"></label>
                                                <input type="number" step="any"  min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][safe_corner_margin_top]"
                                                       value="{{op.safe_corner_margin_top}}"
                                                       class="short safe_corner_margin_top">
                                            </div>
                                            <div>
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/3-01.svg'; ?>"></label>
                                                <input type="number" step="any"  min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][safe_corner_margin_bottom]"
                                                       value="{{op.safe_corner_margin_bottom}}"
                                                       class="short safe_corner_margin_bottom">
                                            </div>
                                        </div>
                                        <div class="nboption-info-box-inner">
                                            <div>
                                                <input type="number" step="any"  min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][safe_corner_margin_left]"
                                                       value="{{op.safe_corner_margin_left}}"
                                                       class="short safe_corner_margin_left">
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px;" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/1-01.svg'; ?>"></label>
                                            </div>
                                            <div>
                                                <input type="number" step="any"  min="0"
                                                       name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][safe_corner_margin_right]"
                                                       value="{{op.safe_corner_margin_right}}"
                                                       class="short safe_corner_margin_right">
                                                <label class="nboption-setting-box-label" style="width: auto;max-width: auto;"><img style="width: 40px; height: 40px" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/round_corner/4-01.svg'; ?>"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="nboption-info-box-inner">
                                    <label class="nboption-setting-box-label"><?php esc_html_e('Show overlay', 'web-to-print-online-designer'); ?></label>
                                    <div>
                                        <!--<input type="hidden" value="0" class="show_bleed" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][show_bleed][{{$index}}]"/>-->
                                        <input type="checkbox" class="show_bleed"
                                                ng-true-value="'on'" ng-false-value="'off'"
                                                name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][show_overlay]"
                                                class="short nbd-dependence"
                                                ng-model="op.show_overlay" ng-checked="op.show_overlay"
                                                /> 
                                    </div>
                                </div> 
                                <div class="nbd-option-overlay" ng-if="op.show_overlay === 'on'">
                                    <div class="nbd-overlay-img-inner">
                                        <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_image(fieldIndex, $index, 'overlay_image', 'overlay_image_url')"></span>
                                        <input ng-hide="true" ng-model="op.overlay_image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][overlay_image]"/>
                                        <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, $index, 'overlay_image', 'overlay_image_url')" ng-src="{{op.overlay_image != 0 && op.overlay_image != null ? op.overlay_image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ng-include src="'field_body_attributes_conditional'" ng-if="field.nbd_type !== 'terms_conditions'"></ng-include>
                        <div class="nbd-margin-10"></div><hr />
                        <div class="nbd-enable-subattribute" ng-hide="field.nbd_type != '' && field.nbd_type != null">
                            <label><input ng-click="toggle_enable_subattr(fieldIndex, $index)" type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][enable_subattr]" ng-true-value="'on'" ng-false-value="'off'" ng-model="op.enable_subattr" ng-checked="op.enable_subattr" /> <?php _e('Enable sub attributes', 'web-to-print-online-designer'); ?></label>
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div class="nbd-subattributes-wrapper" ng-if="op.enable_subattr === true || op.enable_subattr == 'on'">
                            <div class="nbd-field-info">
                                <div class="nbd-field-info-1">
                                    <div><label><b><?php _e('Sub attributes type', 'web-to-print-online-designer'); ?></b></label></div>
                                </div>
                                <div class="nbd-field-info-2">
                                    <div>
                                        <select style="width: 150px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{$index}}][sattr_display_type]" ng-model="op.sattr_display_type" >
                                            <option value="d"><?php _e('Dropdown', 'web-to-print-online-designer'); ?></option>
                                            <option value="r"><?php _e('Radio button', 'web-to-print-online-designer'); ?></option>
                                            <option value="s"><?php _e('Swatch', 'web-to-print-online-designer'); ?></option>
                                            <option value="l"><?php _e('Label', 'web-to-print-online-designer'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="nbd-margin-10"></div>
                            <div ng-repeat="(sopIndex, sop) in op.sub_attributes" class="nbd-subattributes-wrap">
                                <div ng-show="sop.isExpand" class="nbd-attribute-img-wrap">
                                    <div><?php _e('Swatch type', 'web-to-print-online-designer'); ?></div>
                                    <div>
                                        <select ng-model="sop.preview_type" style="width: 110px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][preview_type]">
                                            <option value="i"><?php _e('Image', 'web-to-print-online-designer'); ?></option>
                                            <option value="c"><?php _e('Color', 'web-to-print-online-designer'); ?></option>
                                        </select>
                                    </div>
                                    <div class="nbd-attribute-img-inner" ng-show="sop.preview_type == 'i'">
                                        <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_sub_attribute_image(fieldIndex, opIndex, sopIndex)"></span>
                                        <input ng-hide="true" ng-model="sop.image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][image]"/>
                                        <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_sub_attribute_image(fieldIndex, opIndex, sopIndex)" ng-src="{{sop.image != 0 ? sop.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                                    </div>
                                    <div class="nbd-attribute-color-inner" ng-show="sop.preview_type == 'c'">
                                        <input type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][color]" ng-model="sop.color" class="nbd-color-picker" nbd-color-picker="sop.color"/>
                                    </div>
                                </div>
                                <div ng-show="sop.isExpand" class="nbd-attribute-content-wrap">
                                    <div><?php _e('Title', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbd-attribute-name">
                                        <input required type="text" value="{{sop.name}}" ng-model="sop.name" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][name]"/>
                                        <label><input type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][selected]" ng-checked="sop.selected" ng-click="seleted_sub_attribute(fieldIndex, 'attributes', opIndex, sopIndex)"/> <?php _e('Default', 'web-to-print-online-designer'); ?></label>
                                    </div>
                                    <div class="nbd-margin-10"></div>
                                    <div><?php _e('Description', 'web-to-print-online-designer'); ?></div>
                                    <div class="nbd-attribute-name">
                                        <textarea placeholder="<?php _e('Description', 'web-to-print-online-designer'); ?>" value="{{sop.des}}" ng-model="sop.des" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][des]"></textarea>
                                    </div>
                                    <div><?php _e('Price', 'web-to-print-online-designer'); ?></div>
                                    <div ng-show="field.general.depend_quantity.value != 'y'">
                                        <div><?php _e('Additional Price', 'web-to-print-online-designer'); ?></div>
                                        <div>
                                            <input autocomplete="off" ng-click="initFormulaPrice(sop.price[0], 0, fieldIndex, opIndex, sopIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][price][0]" class="nbd-short-ip" type="text" ng-model="sop.price[0]"/>
                                        </div>
                                    </div>
                                    <div class="nbd-table-wrap" ng-show="field.general.depend_quantity.value == 'y'" >
                                        <table class="nbd-table">
                                            <tr>
                                                <th><?php _e('Quantity break', 'web-to-print-online-designer'); ?></th>
                                                <th ng-repeat="break in options.quantity_breaks">{{break.val}}</th>
                                            </tr>
                                            <tr>
                                                <td><?php _e('Additional Price', 'web-to-print-online-designer'); ?></td>
                                                <td ng-repeat="break in options.quantity_breaks">
                                                    <input autocomplete="off" ng-click="initFormulaPrice(sop.price[$index], $index, fieldIndex, opIndex, sopIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][sub_attributes][{{sopIndex}}][price][{{$index}}]" class="nbd-short-ip" type="text" ng-model="sop.price[$index]"/>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <ng-include src="'field_body_sub_attributes_conditional'"></ng-include>
                                </div>
                                <div ng-show="!sop.isExpand" class="nbd-attribute-name-preview">{{sop.name}}</div>
                                <div class="nbd-attribute-action">
                                    <span class="nbo-sort-group">
                                        <span ng-click="sort_sub_attribute(fieldIndex, opIndex, sopIndex, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                                        <span ng-click="sort_sub_attribute(fieldIndex, opIndex, sopIndex, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
                                    </span>
                                    <a class="button nbd-mini-btn"  ng-click="remove_sub_attribute(fieldIndex, opIndex, sopIndex)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                    <a class="button nbd-mini-btn"  ng-click="toggle_expand_sub_attribute(fieldIndex, opIndex, sopIndex)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                                        <span ng-show="sop.isExpand" class="dashicons dashicons-arrow-up"></span>
                                        <span ng-show="!sop.isExpand" class="dashicons dashicons-arrow-down"></span>
                                    </a>
                                </div>
                            </div>
                            <div><a class="button" ng-click="add_sub_attribute(fieldIndex, opIndex)"><span class="dashicons dashicons-plus"></span> <?php _e('Add sub attribute', 'web-to-print-online-designer'); ?></a></div>
                            <div class="nbd-margin-10"></div>
                        </div>
                    </div> 
                    <div ng-show="!op.isExpand" class="nbd-attribute-name-preview" ng-if="field.nbd_type !== 'terms_conditions'">{{op.name}}</div>
                    <div class="nbd-attribute-action" ng-if="field.nbd_type !== 'terms_conditions'">
                        <span class="nbo-sort-group">
                            <span ng-click="sort_attribute(fieldIndex, $index, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                            <span ng-click="sort_attribute(fieldIndex, $index, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
                        </span>
                        <a class="button nbd-mini-btn"  ng-click="remove_attribute(fieldIndex, 'attributes', $index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                        <a class="button nbd-mini-btn"  ng-click="toggle_expand_attribute(fieldIndex, opIndex)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                            <span ng-show="op.isExpand" class="dashicons dashicons-arrow-up"></span>
                            <span ng-show="!op.isExpand" class="dashicons dashicons-arrow-down"></span>
                        </a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div><a class="button" ng-click="add_attribute(fieldIndex, 'attributes')" ng-if="field.nbd_type !== 'terms_conditions'"><span class="dashicons dashicons-plus"></span> <?php _e('Add attribute', 'web-to-print-online-designer'); ?></a></div>                        
            </div>
            <div ng-if="field.nbd_type === 'production_time'">
                <div class="nbd-attribute-wrap nbd-field-production-time" ng-repeat="(role_index, role_options) in options.fields[fieldIndex].general.role_options" style="clear: both; overflow: auto">
                    <div class="nbd-attribute-name-preview" ng-show="!role_options.isExpand">{{role_options.role_name_d}}</div>
                    <div class="select-role-options" ng-show="role_options.isExpand" style="clear: both; overflow: auto">
                        <input type="hidden" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][role_name_d]" value="{{role_options.role_name_d}}">
                        <div style="width: 120px ; float: left;">
                            <label>
                                <b><?php _e('User Role', 'web-to-print-online-designer'); ?></b>
                            </label>
                        </div>
                        <div style="float: left; width: calc(100% - 150px);">
                            <div class="nbd-table-wrap" style="overflow: hidden;">
                                <select name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][role_name]" class="select-role-options" ng-change="changeRoleOption(fieldIndex, role_index)" ng-model="role_options.role_name">
                                    <?php
                                        global $wp_roles;
                                        $all_roles = $wp_roles->roles;
                                    ?>
                                    <?php foreach ($all_roles as $key => $role): ?>
                                        <option value="<?php echo $key?>" label="<?php echo $role['name'] ?>" ng-selected="'<?php echo $key;?>' === role.role_name" style="width: 100px"><?php echo esc_attr($role['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="nbd-margin-10"></div>
                    <div ng-show="role_options.isExpand" style="width: 120px ; float: left; height: 1px">
                        <b><?php _e('Use as default', 'web-to-print-online-designer'); ?></b><input style="margin-left: 5px;" type="checkbox" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][check_default]" ng-checked="role_options.check_default" ng-click="check_default_attribute_option(fieldIndex, role_index)">
                    </div>
                    <div class="nbd-attribute-options" ng-show="role_options.isExpand" style="float: left; width: calc(100% - 150px); padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                        <div class="nbd-attribute-wrap" ng-repeat="(ro_index, ro) in role_options.options">
                            <div class="nbd-option-role nbd-attribute-options nbd-attribute-production-time">
                                <div class="nbd-attribute-name-preview" ng-show="!ro.isExpand">{{ro.name}}</div>
                                <div ng-show="ro.isExpand" class="nbd-attribute-img-wrap" style="width: 100%">
                                    <label><?php _e('Swatch image', 'web-to-print-online-designer'); ?></label>
                                    <span class="nbd-attribute-img-inner">
                                        <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_attribute_image_production_time(fieldIndex,  role_index , ro_index , 'image', 'image_url')"></span>
                                        <input ng-hide="true" ng-model="ro.image" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][image]"/>
                                        <input ng-hide="true" ng-model="ro.image_url" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][image_url]"/>
                                        <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image_production_time(fieldIndex,  role_index , ro_index , 'image', 'image_url')" ng-src="{{ro.image != 0 ? ro.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                                    </span>
                                </div>
                                <div ng-show="ro.isExpand">
                                    <label><?php _e('Order type', 'web-to-print-online-designer'); ?></label>
                                    <div>
                                        <select ng-model="ro.name" style="width: 110px;" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][name]">
                                            <option value="Standard"><?php _e('Standard', 'web-to-print-online-designer'); ?></option>
                                            <option value="RUSH"><?php _e('RUSH', 'web-to-print-online-designer'); ?></option>
                                            <option value="Super RUSH"><?php _e('Super RUSH', 'web-to-print-online-designer'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div ng-show="ro.isExpand">
                                    <input type="hidden" value="{{role_options.options.name === 'Standard' ? 0 : (role_options.options.name === 'RUSH' ? 1 : 2)}}" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][priority]"/>
                                    <div class="nbd-margin-10"></div>
                                    <table>
                                        <tr>
                                            <td>
                                                <div class="col" style="padding: 0 20px">
                                                    <div><?php _e('% Markup', 'web-to-print-online-designer'); ?></div>
                                                    <div>
                                                        <input required type="number" string-to-number value="{{ro.markup_percent}}" min="0" max="100" ng-model="ro.markup_percent" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][markup_percent]"/>
                                                    </div>
                                                </div>  
                                            </td>
                                            <td>
                                                <div class="col" style="padding: 0 20px">
                                                    <div><?php _e('Min.Markup($)', 'web-to-print-online-designer'); ?></div>
                                                <div>
                                                    <input required type="number" string-to-number value="{{ro.min_markup_percent}}" min="0" ng-model="ro.min_markup_percent" name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][min_markup_percent]"/>
                                                </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="nbd-margin-10"></div>
                                    <div><?php _e('Production Time', 'web-to-print-online-designer'); ?></div>
                                    <div>
                                        <table class="nbd-table">
                                            <tr>
                                                <th class="check-column">
                                                    <input class="nbo-measure-range-select-all" type="checkbox" ng-click="select_all_time_quantity_breaks(fieldIndex,role_index, ro_index, $event)">
                                                </th>
                                                <th><?php _e('Quantity', 'web-to-print-online-designer'); ?></th>
                                                <th><?php _e('Time (Hours)', 'web-to-print-online-designer'); ?></th>
                                            </tr>
                                            <tr ng-repeat="(index, break) in options.fields[fieldIndex].general.role_options[role_index].options[ro_index].time_quantity_breaks">
                                                <td><input type="checkbox" class="nbo-measure-range-checkbox" ng-model="break.selected"></td>
                                                <td><input name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][time_quantity_breaks][{{index}}][qty]" min="1" value="{{break.qty}}" class="nbd-short-ip" type="number" string-to-number ng-model="break.qty"/ >{{break.val}}</td>
                                                <td><input name="options[fields][{{fieldIndex}}][general][role_options][{{role_index}}][options][{{ro_index}}][time_quantity_breaks][{{index}}][time]" class="nbd-short-ip" type="number" min="0" string-to-number ng-model="break.time"/></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div style="margin-top: 5px;" ng-show="ro.isExpand">
                                    <a class="button" ng-click="add_production_time_break(fieldIndex ,role_index, ro_index)"><span class="dashicons dashicons-plus"></span> <?php _e('Add more', 'web-to-print-online-designer'); ?></a>
                                    <a ng-click="delete_production_time_break(fieldIndex, role_index, ro_index, $event)" style="float: right;" type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php _e( 'Delete Selected', 'web-to-print-online-designer' ); ?></a>
                                </div>
                                <div class="nbd-attribute-action">
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteAttrRoleOption(fieldIndex, role_index, ro_index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyAttrRoleOption(fieldIndex, role_index, ro_index)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleAttrRoleOption(fieldIndex ,role_index, ro_index, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!ro.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="ro.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                                </div>
                            </div>
                        </div>
                        <div><a class="button" ng-click="add_attribute_role_options(fieldIndex, role_index)"><span class="dashicons dashicons-plus"></span> <?php _e('Add attribute', 'web-to-print-online-designer'); ?></a></div>
                        <div class="nbd-margin-10"></div>
                    </div>
                    <div class="nbd-attribute-action">
                        <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteRoleOption(fieldIndex, role_index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                        <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyRoleOption(fieldIndex, role_index)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                        <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleRoleOption(fieldIndex ,role_index, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!role_options.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="role_options.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                    </div>
                </div>
                <div><a class="button" ng-click="add_role_options(fieldIndex)"><span class="dashicons dashicons-plus"></span> <?php _e('Add role block', 'web-to-print-online-designer'); ?></a></div>                        
            </div>
        </div>
    </div>
<?php echo '</script>';