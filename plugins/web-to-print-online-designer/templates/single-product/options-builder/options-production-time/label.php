<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-option-field <?php echo $class; ?>" data-id="<?php echo $field['id']; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
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
    if(!empty($role_options)) {
    ?>
    <div class="nbd-field-content">
        <div class="nbd-label-wrap">
            <?php 
                foreach ($role_options as $key => $attr): 
            ?>
            <input ng-change="check_valid();updateMapOptions('<?php echo $field['id']; ?>')" value="<?php echo $key; ?>" ng-model="nbd_fields['<?php echo $field['id']; ?>'].value" name="nbd-field[<?php echo $field['id']; ?>][value]" 
                   type="radio" id='nbd-field-<?php echo $field['id'].'-'.$key; ?>' 
                <?php 
                    if( isset($form_values[$field['id']]) ){
                        $fvalue = (is_array($form_values[$field['id']]) && isset($form_values[$field['id']]['value'])) ? $form_values[$field['id']]['value'] : $form_values[$field['id']];
                        checked( $fvalue, $key ); 
                    }else{
                        selected( isset($attr['check_default']) ? $attr['check_default'] : 'off', 'on' ); 
                    }
                ?> />
            <label class="nbd-label" for='nbd-field-<?php echo $field['id'].'-'.$key; ?>' >
                <?php echo $attr['name']; ?> - <span><b>{{time_work[<?php echo $key; ?>]}} Hours</b></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    }
    ?>
</div>

