<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-field-info">
    <div class="nbd-field-info-1">
        <div><label><b><?php _e('Display type', 'web-to-print-online-designer'); ?></b></label></div>
    </div>
    <div class="nbd-field-info-2">
        <div>
            <select convert-to-number name="options[display_type]" ng-model="options.display_type">
                <option <?php selected( $options['display_type'], 1 ); ?> value="1"><?php _e('Default', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 2 ); ?> value="2"><?php _e('Price Matrix', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 3 ); ?> value="3"><?php _e('Bulk variation', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 4 ); ?> value="4"><?php _e('Group', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 5 ); ?> value="5"><?php _e('Step by step', 'web-to-print-online-designer'); ?></option>
                <option <?php selected( $options['display_type'], 6 ); ?> value="6"><?php _e('Show a part of fields in the popup', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
</div>
<div class="nbd-field-info" ng-if="options.display_type == 2">
    <p><?php _e('Allow fields with options: Data type - Multiple options | Enable - Yes | has at least one attribute | Field Conditional Logic - No', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Horizontal field', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[pm_hoz][]" multiple ng-model="options.pm_hoz">
                    <option value="{{field.field_index}}" ng-repeat="(fieldIndex, field) in availablePmHozFileds">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Vertical field', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[pm_ver][]" multiple ng-model="options.pm_ver">
                    <option  value="{{field.field_index}}" ng-repeat="(fieldIndex, field) in availablePmVerFileds">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div> 
    <div class="nbd-field-info">
        <table>
            <tbody>
                <tr></tr>
            </tbody>
        </table>
    </div>    
</div>
<div class="nbd-field-info" ng-if="options.display_type == 3">
    <p><?php _e('Allow fields with options: Enable - Yes | Field Conditional Logic - No', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Bulk form field', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[bulk_fields][]" multiple ng-model="options.bulk_fields">
                    <option value="{{field.field_index}}" ng-repeat="(fieldIndex, field) in availableBulkFileds">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="nbd-field-info" ng-show="options.display_type == 4">
    <p><b><?php _e('Manage option groups', 'web-to-print-online-designer'); ?></b></p>
    <div ng-repeat="(gIndex, group) in options.groups" class="nbd-group-wrap">
        <div class="nbd-group-img-wrap" ng-show="group.isExpand">
            <label><?php _e('Icon', 'web-to-print-online-designer'); ?></label>
            <div class="nbd-group-img-inner">
                <span class="dashicons dashicons-no remove-group-img" ng-click="remove_group_image( $index )"></span>
                <input ng-hide="true" ng-model="group.image" name="options[groups][{{$index}}][image]"/>
                <img title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_group_image( $index )" ng-src="{{group.image != 0 ? group.image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
            </div>
        </div>
        <div class="nbd-group-main" ng-show="group.isExpand">
            <div class="group-field">
                <label><?php _e('Title', 'web-to-print-online-designer'); ?></label>
                <input type="text" ng-model="group.title" name='options[groups][{{$index}}][title]' />
            </div>
            <div class="group-field">
                <label><?php _e('Description', 'web-to-print-online-designer'); ?></label>
                <textarea ng-model="group.des" value="{{group.des}}" name='options[groups][{{$index}}][des]' rows="5"></textarea>
            </div>
            <div class="group-field">
                <label><?php _e('Note', 'web-to-print-online-designer'); ?></label>
                <textarea ng-model="group.note" value="{{group.note}}" name='options[groups][{{$index}}][note]' rows="5"></textarea>
            </div>
            <div class="group-field">
                <label><?php _e('Number of column', 'web-to-print-online-designer'); ?></label>
                <select ng-model="group.cols" name='options[groups][{{$index}}][cols]' convert-to-number>
                    <option value="1">1 <?php _e('Column', 'web-to-print-online-designer'); ?></option>
                    <option value="2">2 <?php _e('Columns', 'web-to-print-online-designer'); ?></option>
                    <option value="3">3 <?php _e('Columns', 'web-to-print-online-designer'); ?></option>
                    <option value="4">4 <?php _e('Columns', 'web-to-print-online-designer'); ?></option>
                </select>
            </div>
            <div class="group-field">
                <label><?php _e('Group field list', 'web-to-print-online-designer'); ?></label>
                <select name="options[groups][{{$index}}][fields][]" multiple ng-model="group.fields">
                    <option value="{{field.id}}" ng-repeat="field in options.fields | filter: {id: gIndex}:availableGroupField">{{field.general.title.value}}</option>
                </select>
                <p><a class="button" ng-click="clear_group($index)"><span class="dashicons dashicons-no-alt"></span><?php _e('Clear all group fields', 'web-to-print-online-designer') ?></a></p>
            </div>
        </div> 
        <div ng-show="!group.isExpand" class="nbd-group-name-preview">{{group.title}}</div>
        <div class="nbd-group-actions">
            <span class="nbo-sort-group">
                <span ng-click="sort_group($index, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                <span ng-click="sort_group($index, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
            </span>
            <a class="button nbd-mini-btn" ng-click="remove_group($index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
            <a class="button nbd-mini-btn"  ng-click="toggle_expand_group($index)" title="<?php _e('Expend', 'web-to-print-online-designer'); ?>">
                <span ng-show="group.isExpand" class="dashicons dashicons-arrow-up"></span>
                <span ng-show="!group.isExpand" class="dashicons dashicons-arrow-down"></span>
            </a>
        </div>
    </div>
    <div>
        <a class="button" ng-click="add_group()"><span class="dashicons dashicons-plus"></span><?php _e('Add group', 'web-to-print-online-designer'); ?></a>
    </div>
</div>
<div class="nbd-field-info" ng-show="options.display_type == 6 && options.fields.length > 0">
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Show popup trigger button when', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[popup_trigger_field]" ng-model="options.popup_trigger_field" ng-change="updatePopupTriggerIndex()" style="width: 150px;">
                <option value="{{field.id}}" ng-repeat="field in allFields" >{{options.fields[field.field_index].general.title.value}}</option>
            </select> <?php _e('is', 'web-to-print-online-designer'); ?> 
            <span ng-if="options.popup_trigger_field !== ''">
                <span ng-if="options.fields[options.popup_trigger_index].general.data_type.value == 'i'">
                    <input name="options[popup_trigger_value]" ng-model="options.popup_trigger_value" type="text" style="width: 150px;" />
                </span>
                <span ng-if="options.fields[options.popup_trigger_index].general.data_type.value == 'm'">
                    <select name="options[popup_trigger_value]" ng-model="options.popup_trigger_value" style="width: 150px;">
                        <option value="{{$index}}" ng-repeat="pop in options.fields[options.popup_trigger_index].general.attributes.options">{{pop.name}}</option>
                    </select>
                </span>
            </span>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Popup fields', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[popup_fields][]" multiple ng-model="options.popup_fields" nbd-select2>
                    <option value="{{field.id}}" ng-repeat="(fieldIndex, field) in allFields">{{options.fields[field.field_index].general.title.value}}</option>
                </select>
            </div>
        </div>    
    </div>
</div>