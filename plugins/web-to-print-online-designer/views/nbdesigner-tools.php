<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbdesign-migrate nbd-tool-section nbd-hide-deprecated">
    <h2><?php  esc_html_e('Migrate website domain', 'web-to-print-online-designer'); ?></h2>
    <p><?php  esc_html_e('Update url, path: cliparts, fonts...', 'web-to-print-online-designer'); ?></p>
    <div>
        <table class="form-table" id="nbdesigner-migrate-info">
            <?php wp_nonce_field('nbdesigner-migrate-key', '_nbdesigner_migrate_nonce'); ?>
            <tr valign="top" class="" >
                <th scope="row" class="titledesc"><?php  esc_html_e("Old domain", 'web-to-print-online-designer'); ?> </th>
                <td class="forminp-text">
                    <input type="email" class="regular-text" name="old_domain" placeholder="http://old-domain.com"/>
                    <div class="description">
                        <small id="nbdesigner_key_mes"><?php esc_html_e('Fill your old domain, example: "http://old-domain.com".', 'web-to-print-online-designer'); ?></small>
                    </div>                    
                </td>
            </tr>     
            <tr valign="top" class="" > 
                <th scope="row" class="titledesc"><?php  esc_html_e("New domain", 'web-to-print-online-designer'); ?> </th>
                <td class="forminp-text">
                    <input type="email" class="regular-text" name="new_domain" placeholder="http://new-domain.com"/>
                    <div class="description">
                        <small id="nbdesigner_key_mes"><?php esc_html_e('Fill your new domain, example: "http://new-domain.com".', 'web-to-print-online-designer'); ?></small>
                    </div>                    
                </td>                
            </tr>
        </table>
        <p class="submit">
            <button class="button-primary" id="nbdesigner_update_data_migrate" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update", 'web-to-print-online-designer'); ?></button>
            <button class="button-primary" id="nbdesigner_resote_data_migrate" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Restore", 'web-to-print-online-designer'); ?></button>
            <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_migrate_loading" />
        </p>	        
    </div>
</div>
<div class="nbdesign-migrate nbd-tool-section nbd-hide-deprecated">
    <h2><?php  esc_html_e('Theme check', 'web-to-print-online-designer'); ?></h2>
    <div id="nbdesign-theme-check">
        <?php wp_nonce_field('nbdesigner-check-theme-key', '_nbdesigner_check_theme_nonce'); ?>
        <button class="button-primary" id="nbdesigner_check_theme"><?php  esc_html_e("Start check", 'web-to-print-online-designer'); ?></button>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_check_theme_loading" />
        <div class="theme_check_note"></div>
    </div>
    <div id="nbdesigner-result-check-theme" class="nbd-admin-tool-margin-bottom-15"></div>
</div>
<div class="nbdesigner-editor nbd-tool-section">
    <h2>
        <?php  esc_html_e('Edit custom CSS for NBDesigner frontend', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_custom_css_loading" />
    </h2>
    <div id="nbdesigner_custom_css_con">
        <?php wp_nonce_field('nbdesigner-custom-css', '_nbdesigner_custom_css'); ?>
        <textarea cols="70" rows="30" name="nbdsigner_custom_css" id="nbdsigner_custom_css" ><?php echo esc_html( $custom_css ); ?></textarea>
    </div>
    <div class="nbd-admin-tool-margin-top-15">
        <button class="button-primary" id="nbdesigner_custom_css" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php esc_html_e('Update Custom CSS', 'web-to-print-online-designer') ?></button>
        <small><?php esc_html_e('Using bad CSS code could break the appearance of your plugin', 'web-to-print-online-designer') ?></small>
    </div>
    <script language="javascript">
            jQuery( document ).ready( function($) {
                var editorCodeMirror = CodeMirror.fromTextArea( document.getElementById( "nbdsigner_custom_css" ), {lineNumbers: true, lineWrapping: true} );
                $('#nbdesigner_custom_css').on('click', function(e){
                    var formdata = jQuery('#nbdesigner_custom_css_con').find('input').serialize();
                    var content = editorCodeMirror.getValue();
                    formdata = formdata + '&action=nbdesigner_custom_css&content=' + encodeURIComponent( content );
                    jQuery('#nbdesigner_custom_css_loading').removeClass('nbdesigner_loaded');
                    jQuery.post(admin_nbds.url, formdata, function(_data){
                        jQuery('#nbdesigner_custom_css_loading').addClass('nbdesigner_loaded');
                        var data = JSON.parse(_data);
                        if (data.flag == 1) {
                            swal(admin_nbds.nbds_lang.complete, data.mes, "success");
                        }else{
                            swal({
                                title: "Oops!",
                                text: data.mes,
                                imageUrl: admin_nbds.assets_images + "dinosaur.png"
                            });
                        }              
                    });                
                });   
            });
            
    </script>    
</div>
<div class="nbdesigner-editor nbd-tool-section">
    <h2>
        <?php  esc_html_e('Edit custom JS for NBDesigner frontend', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_custom_js_loading" />
    </h2>
    <div id="nbdesigner_custom_js_con">
        <?php wp_nonce_field('nbdesigner-custom-css', '_nbdesigner_custom_css'); ?>
        <textarea cols="70" rows="30" name="nbdsigner_custom_js" id="nbdsigner_custom_js" ><?php echo esc_html( $custom_js ); ?></textarea>
    </div>
    <div class="nbd-admin-tool-margin-top-15">
        <button class="button-primary" id="nbdesigner_custom_js" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php esc_html_e('Update Custom JS', 'web-to-print-online-designer') ?></button>
        <small><?php esc_html_e('Using bad JS code could break the appearance of your plugin', 'web-to-print-online-designer') ?></small>
    </div>
    <script language="javascript">
            jQuery( document ).ready( function($) {
                var editorCodeMirrorJS = CodeMirror.fromTextArea( document.getElementById( "nbdsigner_custom_js" ), {lineNumbers: true, lineWrapping: true} );
                $('#nbdesigner_custom_js').on('click', function(e){
                    var formdata = jQuery('#nbdesigner_custom_js_con').find('input').serialize();
                    var content = editorCodeMirrorJS.getValue();
                    formdata = formdata + '&action=nbdesigner_custom_js&content=' + content;
                    jQuery('#nbdesigner_custom_js_loading').removeClass('nbdesigner_loaded');
                    jQuery.post(admin_nbds.url, formdata, function(_data){
                        jQuery('#nbdesigner_custom_js_loading').addClass('nbdesigner_loaded');
                        var data = JSON.parse(_data);
                        if (data.flag == 1) {
                            swal(admin_nbds.nbds_lang.complete, data.mes, "success");
                        }else{
                            swal({
                                title: "Oops!",
                                text: data.mes,
                                imageUrl: admin_nbds.assets_images + "dinosaur.png"
                            });
                        }              
                    });                
                });   
            });
            
    </script>    
</div>
<div class="update-setting-data nbd-tool-section  nbd-hide-deprecated" >
    <h2><?php  esc_html_e('Update product design setting data', 'web-to-print-online-designer'); ?></h2>
    <div>
        <?php wp_nonce_field('nbdesigner-update-product', '_nbdesigner_update_product'); ?>
        <button class="button nbdesigner-delete" id="nbdesigner_update_product" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update v1.7.0", 'web-to-print-online-designer'); ?></button>
        <button class="button nbdesigner-delete" id="nbdesigner_update_variation_v180" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update v1.8.0", 'web-to-print-online-designer'); ?></button>
        <!--<button class="button nbdesigner-delete" id="nbdesigner_update_template" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update templates", 'web-to-print-online-designer'); ?></button>-->
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_update_product_loading" />        
        <p><small><?php esc_html_e('Make sure backup data before update avoid lost data!', 'web-to-print-online-designer') ?></small></p>
    </div>
</div>
<div id="nbd-clear-transients-con" class="nbd-tool-section">
    <h2><?php  esc_html_e('Clear transients', 'web-to-print-online-designer'); ?></h2>
    <div>
        <?php wp_nonce_field('nbd-clear-transients', '_nbdesigner_cupdate_product'); ?>
        <button class="button nbdesigner-delete" id="nbd-clear-transients" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Clear transients", 'web-to-print-online-designer'); ?></button>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_clear_transients_loading" />   
        <p><small><?php esc_html_e('This tool will clear the NBD product transients cache!', 'web-to-print-online-designer'); ?></small></p>
    </div>
</div>
<div id="nbd-setup-wizard" class="nbd-tool-section">
    <h2><?php esc_html_e('Create default NBDesigner pages', 'web-to-print-online-designer'); ?></h2>
    <?php wp_nonce_field('nbd-create-pages', '_nbdesigner_update_product'); ?>
    <button class="button-primary" id="nbd-create-pages" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php esc_html_e('Create pages', 'web-to-print-online-designer'); ?></button>
    <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_create_pages_loading" />   
    <p><strong class="nbd-admin-tool-note"><?php esc_html_e('Note', 'web-to-print-online-designer'); ?>: </strong><?php esc_html_e('This tool will install all the missing NBDesigner pages. Pages already defined and set up will not be replaced.', 'web-to-print-online-designer'); ?></p>
</div>
<div id="nbd-logs" class="nbd-tool-section">
    <h2><?php esc_html_e('Logs', 'web-to-print-online-designer'); ?><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'remove_log' ), admin_url( 'admin.php?page=nbdesigner_tools' ) ), 'remove_log' ) ); ?>" class="button-primary nbd-admin-tool-delete-log" ><?php esc_html_e('Delete log', 'web-to-print-online-designer'); ?></a></h2>
    <form action="<?php echo admin_url( 'admin.php?page=wc-status&tab=logs' ); ?>" method="post">
        
    </form>
    <div class="nbd-admin-tool-debug-log">
        <?php if(file_exists(NBDESIGNER_LOG_DIR . '/debug.log')): ?>
        <pre><?php echo esc_html( file_get_contents( NBDESIGNER_LOG_DIR . '/debug.log' ) ); ?></pre>
        <?php endif; ?>
    </div>
</div>
<div id="nbd-fix-pdf-font" class="nbd-tool-section">
    <h2>
        <?php  esc_html_e('Fix pdf font', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_fix_font_loading" />
    </h2>
    <div id="nbdesigner_custom_css_con">
        <?php wp_nonce_field('nbdesigner-fix-font', '_nbdesigner_fix_font'); ?>
        <input id="pdf_font" />
    </div>
    <?php
        $custom_fonts = array();
        if(file_exists( NBDESIGNER_DATA_DIR . '/fonts.json') ){
            $custom_fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' ) );        
        } 
        $google_fonts = array();
        if(file_exists( NBDESIGNER_DATA_DIR . '/googlefonts.json') ){
            $google_fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/googlefonts.json' ) );        
        }
        $fonts = array_merge($google_fonts, $custom_fonts);
    ?>
    <script language="javascript">
        jQuery( document ).ready( function($) {
            var fonts = <?php echo json_encode( $fonts ); ?>,
            source = fonts.map(function( font ){
                return {
                    label: font.name,
                    value: font.alias,
                    type: font.type
                };
            });
            $( "#pdf_font" ).autocomplete({
                source: source,
                select: function( event, ui ) {
                    event.preventDefault();
                    $("#pdf_font").val(ui.item.label);
                    fix_pdf_font(ui);
                },
                focus: function(event, ui) {
                    event.preventDefault();
                    $("#pdf_font").val(ui.item.label);
                    //fix_pdf_font(ui);
                }
            });
            function fix_pdf_font( ui ){
                var formdata = new FormData();
                formdata.append('alias', ui.item.value);
                formdata.append('type', ui.item.type.toLowerCase());
                formdata.append('action', 'nbd_fix_pdf_font');
                jQuery('#nbdesigner_fix_font_loading').removeClass('nbdesigner_loaded');
                jQuery('#nbd-fix-pdf-font').addClass('nbd-loading');
                $.ajax({
                    url: admin_nbds.url,
                    data: formdata,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        jQuery('#nbdesigner_fix_font_loading').addClass('nbdesigner_loaded');
                        jQuery('#nbd-fix-pdf-font').removeClass('nbd-loading');
                        if(parseInt(data.flag) == 1){
                            alert('Success!');
                        }else {
                            alert('Oops! Try again!');
                        }
                    }
                });
            };
        });
    </script>
</div>