<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class NB_AWS {
    public $paypalConfig;
    public $dbConfig;
    public $apiContext;
    public $enableSandbox;
    
    function __construct() {
        $this->action_hook();
        $this->init();
    }
    
    function init() {
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/aws/aws-setting.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/aws.php' );
    }
    
    function action_hook() {
        add_filter('nbdesigner_settings_tabs', array('NBD_Aws_Setting', 'add_setting_aws'), 20, 1);
        add_filter('nbdesigner_settings_blocks', array('NBD_Aws_Setting', 'add_settings_blocks'), 20, 1);
        add_filter('nbdesigner_settings_options', array('NBD_Aws_Setting', 'add_settings_options'), 20, 1);
    }
}

new NB_AWS();