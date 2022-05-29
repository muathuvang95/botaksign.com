<?php if (!defined('ABSPATH')) exit; ?>
<div class="section-container">
    <p class="section-title"><input class="nbd-ip-readonly" value="<?php _e('Product Gallery', 'web-to-print-online-designer'); ?>" readonly=""></p>
    <div class="nbd-section-wrap">
        <div class="nbd-field-info nbd-field-gallery-options" ng-repeat="(galleryIndex, gallery) in options.gallery_options">
            <div class="gallery-options-header">
                <div class="nbd-field-info-1 title">Variation {{galleryIndex + 1}}</div>
                <div class="nbd-field-info-2 action">
                    <span class="nbo-sort-group">
                        <span ng-click="sortOptionGallery(galleryIndex, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                        <span ng-click="sortOptionGallery(galleryIndex, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
                    </span>
                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="deleteOptionGallery(galleryIndex)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="copyOptionGallery(galleryIndex)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleOptionGallery(galleryIndex, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!gallery.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="gallery.isExpand" class="dashicons dashicons-arrow-up"></span></a>
                </div>
            </div>
            <div class="gallery-options-setup" ng-show="gallery.isExpand">
                <div>
                    <div><?php _e('SKU:', 'web-to-print-online-designer'); ?></div>
                    <input type="text" ng-model="gallery.sku" name="options[gallery_options][{{galleryIndex}}][sku]" style="width: 120px !important; vertical-align: middle;" />
                    <div class="nbd-margin-10"></div>
                    <div class="nbd-attribute-img-gallery">
                        <div><?php _e('Product gallery', 'web-to-print-online-designer'); ?></div>
                        <div class="nbd-attribute-img-inner" ng-repeat="(imgIndex, img) in gallery.product_images">
                            <span class="dashicons dashicons-no remove-attribute-img" ng-click="remove_option_gallery_image(imgIndex, galleryIndex)"></span>
                            <input ng-hide="true" ng-model="img.product_image" name="options[gallery_options][{{galleryIndex}}][product_images][{{imgIndex}}][product_image]"/>
                            <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_option_gallery_image(imgIndex, galleryIndex)" ng-src="{{img.product_image_url ? img.product_image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                        </div>
                        <div class="nbd-attribute-img-inner add-product-gallery-image" ng-click="add_option_gallery_image(galleryIndex)">
                            <div class="btn-add">+</div>
                        </div>
                    </div>
                    <div class="nbd-margin-10"></div>
                    <div class="nbd-enable-attribute-con" <?php //ng-if="field.appearance.change_image_product.value == 'y'" ?>>
                        <label><input type="checkbox" name="options[gallery_options][{{galleryIndex}}][gallery_enable_con]" ng-true-value="'on'" ng-false-value="'off'" ng-model="gallery.gallery_enable_con" ng-checked="gallery.gallery_enable_con" /> <?php _e('Enable Gallery Conditional Logic', 'web-to-print-online-designer'); ?></label>
                    </div>
                    <div class="nbd-margin-10"></div>
                    <div class="nbd-subattributes-wrapper" ng-if="gallery.gallery_enable_con == true || gallery.gallery_enable_con == 'on'">
                        <div>
                            <?php _e('Show this gallery if', 'web-to-print-online-designer'); ?>
                            <select ng-model="gallery.gallery_con_logic" style="width: inherit;" name="options[gallery_options][{{galleryIndex}}][gallery_con_logic]">
                                <option value="a"><?php _e('all', 'web-to-print-online-designer'); ?></option>
                                <option value="o"><?php _e('any', 'web-to-print-online-designer'); ?></option>
                            </select>
                            <?php _e('of these rules match:', 'web-to-print-online-designer'); ?>
                        </div>
                        <div class="nbd-margin-10"></div>
                        <div>
                            <div ng-repeat="(cdIndex, con) in gallery.gallery_depend">
                                <select ng-model="con.id" style="width: 120px;" name="options[gallery_options][{{galleryIndex}}][gallery_depend][{{cdIndex}}][id]">
                                    <option ng-repeat="cf in options.fields | filter: { id: field.id }:excludeField" value="{{cf.id}}">{{cf.general.title.value}}</option>
                                    <option value="qty"><?php _e('Quantity', 'web-to-print-online-designer'); ?></option>
                                </select>
                                <select ng-model="con.operator" style="width: 120px;" name="options[gallery_options][{{galleryIndex}}][gallery_depend][{{cdIndex}}][operator]">
                                    <option ng-if="con.id != 'qty'" value="i"><?php _e('is', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id != 'qty'" value="n"><?php _e('is not', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id != 'qty'" value="e"><?php _e('is empty', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id != 'qty'" value="ne"><?php _e('is not empty', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id == 'qty'" value="eq"><?php _e('equal', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id == 'qty'" value="gt"><?php _e('great than', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id == 'qty'" value="lt"><?php _e('less than', 'web-to-print-online-designer'); ?></option>
                                </select>
                                <select ng-if="(con.operator == 'i' || con.operator == 'n') && con.id != 'qty'" ng-model="con.val" ng-repeat="vf in options.fields | filter: {id: con.id}:includeField"  
                                    name="options[gallery_options][{{galleryIndex}}][gallery_depend][{{cdIndex}}][val]" style="width: 120px;">
                                    <option ng-repeat="vop in vf.general.attributes.options" value="{{$index}}">{{vop.name}}</option>
                                </select>
                                <input ng-if="con.id == 'qty'" type="text" ng-model="con.val" name="options[gallery_options][{{galleryIndex}}][gallery_depend][{{cdIndex}}][val]" style="width: 120px !important; vertical-align: middle;" />
                                <a class="nbd-field-btn nbd-mini-btn button" ng-click="add_option_gallery_condition(galleryIndex)"><span class="dashicons dashicons-plus"></span></a>
                                <a class="nbd-field-btn nbd-mini-btn button" ng-click="delete_option_gallery_condition(galleryIndex, cdIndex)"><span class="dashicons dashicons-no-alt"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="display: inline-block; width: 100%;">
            <a class="button" ng-click="add_gallery_variation()" style="float: right;"><span class="dashicons dashicons-plus"></span> <?php _e('Add variation', 'web-to-print-online-designer'); ?></a>
        </div>
    </div>
</div>