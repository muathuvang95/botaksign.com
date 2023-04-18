<?php if (!defined('ABSPATH')) exit; ?>
<div nbo-adv-dropdown class="nbd-option-field nbd-field-ad-dropdown-wrap <?php echo $class; ?>" data-id="<?php echo $field['id']; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
    <?php include( $currentDir .'/options-builder/field-header.php' ); ?>
    <?php
    $user = wp_get_current_user();
    $role = $user->roles[0];
    $role_options = array();
    $have_role_use = false;
    $have_check_default = false;
    foreach ($field['general']['role_options'] as $key => $value) {
        if($value['role_name'] == $role ) {
            $role_options_1 = $value['options'];
            $have_role_use = true;
        }
        if($value['check_default'] == 'on' || $value['check_default'] == '1') {
            $have_check_default = true;
            $role_options_2 = $value['options'];
        }   
    }
    if($have_role_use) {
        $role_options = $role_options_1;
    }
    if(!$have_role_use && $have_check_default ) {
        $role_options = $role_options_2;
    }
    ?>
    <?php if(isset($field['general']['attributes']) && $field['nbd_type'] !== 'terms_conditions' && !empty($role_options)): ?>
    <div class="nbd-field-content">
        <div>
            <select ng-change="check_valid()" name="nbd-field[<?php echo $field['id']; ?>][value]" class="nbo-dropdown" ng-model="nbd_fields['<?php echo $field['id']; ?>'].value">
            <?php 
                foreach ($role_options as $key => $attr): 
            ?>
                <option value="<?php echo $key; ?>"
                    <?php 
                        if( isset($form_values[$field['id']]) ){
                            $fvalue = (is_array($form_values[$field['id']]) && isset($form_values[$field['id']]['value'])) ? $form_values[$field['id']]['value'] : $form_values[$field['id']];
                            selected( $fvalue, $key ); 
                        }else{
                            selected( isset($attr['check_default']) ? $attr['check_default'] : 'off', 'on' ); 
                        }
                    ?>><?php echo $attr['name']; ?></option>
            <?php endforeach; ?>
            </select>
            <div class="nbo-ad-result">
                <span class="nbo-ad-result-name">{{nbd_fields['<?php echo $field['id']; ?>'].value_name}} - {{time_work[nbd_fields['<?php echo $field['id']; ?>'].value]}} Hours</span>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                </svg>
            </div>
            <div class="nbo-ad-pseudo-list">
                <?php 
                    foreach ($role_options as $key => $attr):
                ?>
                <div class="nbo-ad-list-item" 
                     ng-click="select_adv_attr('<?php echo $field['id']; ?>', '<?php echo $key; ?>');updateMapOptions('<?php echo $field['id']; ?>')"
                     ng-class="nbd_fields['<?php echo $field['id']; ?>'].value == '<?php echo $key; ?>' ? 'active' : ''" nbo-disabled-type="class" >
                    <div class="nbo-ad-item-main">
                        <?php if($attr['name']): ?>
                            <div class="nbo-ad-item-title"><?php echo $attr['name']; ?> - <span><b>{{time_work[<?php echo $key; ?>]}} Hours</b></span></div>
                            <div class="nbo-ad-item-description"><?php echo $attr['des']; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if( isset($attr['check_default']) && ( $attr['check_default'] == 'on' || $attr['check_default'] == '1' ) ): ?>
                    <span class="nbo-recomand" title="<?php _e('Recommended', 'web-to-print-online-designer'); ?>">
                        <svg class="octicon octicon-bookmark" viewBox="0 0 10 16" version="1.1" width="10" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M9 0H1C.27 0 0 .27 0 1v15l5-3.09L10 16V1c0-.73-.27-1-1-1zm-.78 4.25L6.36 5.61l.72 2.16c.06.22-.02.28-.2.17L5 6.6 3.12 7.94c-.19.11-.25.05-.2-.17l.72-2.16-1.86-1.36c-.17-.16-.14-.23.09-.23l2.3-.03.7-2.16h.25l.7 2.16 2.3.03c.23 0 .27.08.09.23h.01z"></path></svg>
                    </span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="nbo-invalid-option" 
                ng-class="nbd_fields['<?php echo $field['id']; ?>'].valid === false ? 'active' : ''"
                ng-if="nbd_fields['<?php echo $field['id']; ?>'].valid === false">{{nbd_fields['<?php echo $field['id']; ?>'].invalidOption}} <?php _e('is not available', 'web-to-print-online-designer'); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
