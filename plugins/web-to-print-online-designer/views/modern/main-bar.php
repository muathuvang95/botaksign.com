<?php
$custom_logo_id = get_theme_mod( 'custom_logo' );
$image          = wp_get_attachment_image_src( $custom_logo_id , 'full' );
$without_logo   = false;
if(!isset($image['0'])){
    $logo_option    = nbdesigner_get_option('nbdesigner_editor_logo');
    $logo_url       = wp_get_attachment_url( $logo_option );
    if(!$logo_url){
        $without_logo = true;
    }
}else{
    $logo_url = $image['0'];
}
?>
<div class="nbd-main-bar">
    <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="logo <?php if($without_logo) echo ' logo-without-image'; ?>">
        <?php if(!$without_logo): ?>
        <img src="<?php echo esc_url( $logo_url );?>" alt="online design">
        <?php else: ?>
        <?php esc_html_e('Home','web-to-print-online-designer'); ?>
        <?php endif; ?>
    </a>
    <i class="icon-nbd icon-nbd-menu menu-mobile"></i>
    <ul class="nbd-main-menu menu-left">
        <li class="menu-item item-edit">
            <span><?php esc_html_e('File','web-to-print-online-designer'); ?></span>
            <div class="sub-menu" data-pos="left">
                <ul>
                    <?php if( is_user_logged_in() ): ?>
                    <li ng-if="false" class="sub-menu-item flex space-between" ng-click="loadUserDesigns()">
                        <span><?php esc_html_e('Open My Logo','web-to-print-online-designer'); ?></span>
                    </li>
                    <li class="sub-menu-item flex space-between item-import-file" ng-click="loadMyDesign(null, false)">
                        <span><?php esc_html_e('Open My Design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-O' | keyboardShortcut }}</small>
                    </li>
                    <?php endif; ?>
                    <li class="sub-menu-item flex space-between item-import-file" ng-click="loadMyDesign(null, true)">
                        <span><?php esc_html_e('My Design in Cart','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-O' | keyboardShortcut }}</small>
                    </li>                    
                    <li class="sub-menu-item flex space-between item-import-file" ng-click="importDesign()">
                        <span><?php esc_html_e('Import Design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-I' | keyboardShortcut }}</small>
                    </li>
                    <li class="sub-menu-item flex space-between" ng-click="exportDesign()">
                        <span><?php esc_html_e('Export Design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-E' | keyboardShortcut }}</small>
                    </li>
                    <?php if( $settings['allow_customer_download_design_in_editor'] == 'yes' && ( $settings['nbdesigner_download_design_in_editor_png'] == '1' || $settings['nbdesigner_download_design_in_editor_pdf'] == '1' || $settings['nbdesigner_download_design_in_editor_jpg'] == '1' || $settings['nbdesigner_download_design_in_editor_svg'] == '1' ) ): ?>
                    <li class="sub-menu-item flex space-between hover-menu" data-animate="bottom-to-top">
                        <span class="title-menu"><?php esc_html_e('Download','web-to-print-online-designer'); ?></span>
                        <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90"></i>
                        <div class="hover-sub-menu-item">
                            <ul>
                                <?php if( $settings['nbdesigner_download_design_in_editor_png'] == '1' ): ?>
                                <li ng-click="saveDesign('png')"><span class="title-menu"><?php esc_html_e('PNG','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                                <?php if( $settings['nbdesigner_download_design_in_editor_jpg'] == '1' ): ?>
                                <li ng-click="saveData('download-jpg')"><span class="title-menu"><?php esc_html_e('CMYK JPG','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                                <?php if( $settings['nbdesigner_download_design_in_editor_svg'] == '1' ): ?>
                                <li ng-click="saveDesign('svg')"><span class="title-menu"><?php esc_html_e('SVG','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                                <?php if( $settings['nbdesigner_download_design_in_editor_pdf'] == '1' ): ?>
                                <li ng-click="saveData('download-pdf')"><span class="title-menu"><?php esc_html_e('PDF','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div id="nbd-overlay"></div>
        </li>
        <li class="menu-item item-edit">
            <span><?php esc_html_e('Edit','web-to-print-online-designer'); ?></span>
            <div class="sub-menu" data-pos="left">
                <ul>
                    <li class="sub-menu-item flex space-between" ng-click="_clearAllStage()">
                        <span><?php esc_html_e('Clear all design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-E' | keyboardShortcut }}</small>
                    </li>
<!--                    <li ng-if="settings.nbdesigner_save_for_later == 'yes'" class="sub-menu-item flex space-between" ng-click="saveForLater()">
                        <span><?php esc_html_e('Save for later','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-S' | keyboardShortcut }}</small>
                    </li>  -->
                    <li ng-if="settings.nbdesigner_save_for_later == 'yes'" class="sub-menu-item flex space-between" ng-click="prepareBeforeSaveForLater()">
                        <span><?php esc_html_e('Save for later','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-S' | keyboardShortcut }}</small>
                    </li> 
                    <li ng-if="settings.nbdesigner_enable_template_mapping == 'yes' && templateHolderFields.length > 0" class="sub-menu-item flex space-between" ng-click="showTemplateFieldsPopup( true )">
                        <span><?php esc_html_e('Fill out with your information','web-to-print-online-designer'); ?></span>
                    </li>
                </ul>
            </div>
            <div id="nbd-overlay"></div>
        </li>
        <li class="menu-item item-view">
            <span><?php esc_html_e('View','web-to-print-online-designer'); ?></span>
            <ul class="sub-menu" data-pos="left">
                <li ng-show="!settings.is_mobile" class="sub-menu-item flex space-between" ng-click="toggleRuler()" ng-class="settings.showRuler ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Ruler','web-to-print-online-designer'); ?></span>
                    <small>{{ 'M-R' | keyboardShortcut }}</small>
                </li>
                <li class="sub-menu-item flex space-between" ng-click="settings.showGrid = !settings.showGrid" ng-class="settings.showGrid ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Show grid','web-to-print-online-designer'); ?></span>
                    <small>{{ 'S-G' | keyboardShortcut }}</small>
                </li>
                <li class="sub-menu-item flex space-between" ng-click="settings.bleedLine = !settings.bleedLine" ng-class="settings.bleedLine ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Show bleed line','web-to-print-online-designer'); ?></span>
                    <small>{{ 'M-L' | keyboardShortcut }}</small>
                </li>
                <li ng-show="!settings.is_mobile" class="sub-menu-item flex space-between" ng-click="settings.showDimensions = !settings.showDimensions" ng-class="settings.showDimensions ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Show dimensions','web-to-print-online-designer'); ?></span>
                    <small>{{ 'S-D' | keyboardShortcut }}</small>
                </li>
                <li class="sub-menu-item flex space-between" ng-click="clearGuides()" ng-class="!(stages[currentStage].rulerLines.hors.length > 0 || stages[currentStage].rulerLines.vers.length > 0) ? 'nbd-disabled' : ''">
                    <span class="title-menu"><?php esc_html_e('Clear Guides','web-to-print-online-designer'); ?></span>
                    <small>{{ 'S-L' | keyboardShortcut }}</small>
                </li>
                <!--<li class="sub-menu-item flex space-between hover-menu" data-animate="bottom-to-top" ng-click="settings.snapMode.status = !settings.snapMode.status;" ng-class="settings.snapMode.status ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Snap to','web-to-print-online-designer'); ?></span>
                    <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90" ng-show="settings.snapMode.status"></i>
                    <div class="hover-sub-menu-item" ng-show="settings.snapMode.status">
                        <ul>
                            <li ng-click="settings.snapMode.type = 'layer'; $event.stopPropagation();" ng-class="settings.snapMode.type == 'layer' ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Layer','web-to-print-online-designer'); ?></span></li>
                            <li ng-click="settings.snapMode.type = 'bounding'; $event.stopPropagation();" ng-class="settings.snapMode.type == 'bounding' ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Bounding','web-to-print-online-designer'); ?></span></li>
                            <li ng-click="settings.snapMode.type = 'grid'; $event.stopPropagation();" ng-class="settings.snapMode.type == 'grid' ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Grid','web-to-print-online-designer'); ?></span></li>
                        </ul>
                    </div>
                </li>-->
		<li class="sub-menu-item flex space-between hover-menu" data-animate="bottom-to-top">
                    <span class="title-menu"><?php esc_html_e('Show warning','web-to-print-online-designer'); ?></span>
                    <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90"></i>
                    <div class="hover-sub-menu-item">
                        <ul>
                            <li ng-click="settings.showWarning.oos = !settings.showWarning.oos" ng-class="settings.showWarning.oos ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Out of stage','web-to-print-online-designer'); ?></span></li>
                            <li ng-click="settings.showWarning.ilr = !settings.showWarning.ilr" ng-class="settings.showWarning.ilr ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Image low resolution','web-to-print-online-designer'); ?></span></li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div id="nbd-overlay"></div>
        </li>
        <?php if( $show_nbo_option && ($settings['nbdesigner_display_product_option'] == '1' || wp_is_mobile() ) && !(isset( $_GET['src'] ) && $_GET['src'] == 'studio') ): ?>
        <li class="menu-item item-nbo-options" ng-click="getPrintingOptions()">
            <span><?php esc_html_e('Options','web-to-print-online-designer'); ?></span>
        </li>
        <?php endif; ?> 
        <li class="menu-item tour_start" ng-if="!settings.is_mobile" ng-click="startTourGuide()">
            <span class="nbd-tooltip-hover-right" title="<?php esc_html_e('Quick Help','web-to-print-online-designer'); ?>">?</span>
        </li>
        <?php do_action('nbd_modern_extra_menu'); ?>
    </ul>
    <ul class="nbd-main-menu menu-center">
        <li class="menu-item undo-redo" ng-click="undo()" ng-class="stages[currentStage].states.isUndoable ? 'in' : 'nbd-disabled'">
            <i class="icon-nbd-baseline-undo" ></i>
            <span class="nbd-font-size-12"><?php esc_html_e('Undo','web-to-print-online-designer'); ?></span>
        </li>
        <li class="menu-item undo-redo" ng-click="redo()" ng-class="stages[currentStage].states.isRedoable ? 'in' : 'nbd-disabled'">
            <i class="icon-nbd-baseline-redo" ></i>
            <span class="nbd-font-size-12"><?php esc_html_e('Redo','web-to-print-online-designer'); ?></span>
        </li>
    </ul>
    <ul class="nbd-main-menu menu-right">
        <li class="menu-item item-title animated slideInDown animate700 ipad-mini-hidden">
            <input type="text" name="title" class="title" placeholder="Title" ng-model="stages[currentStage].config.name"/>
        </li>
        <?php if( $enable_sticker_preview ): ?>
        <li title="<?php esc_html_e('Sticker cutline preview','web-to-print-online-designer'); ?>" ng-click="generateStickerCutline()" class="menu-item nbd-show-3d-preview nbd-change-product animated slideInDown animate700 main-menu-action" >
            <i class="nbd-svg-icon" style="margin-left: 0;" >
                <svg version="1.1" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" ><path d="M511.653,195.545h0.018c-11.444-45.606-34.042-86.166-67.982-119.27v-0.097C394.902,27.098,329.298,0.021,259.891,0.021 c-69.406,0-134.689,27.005-183.769,76.086C27.043,125.186,0,190.427,0,259.834s27.474,134.654,76.554,183.735 c33.107,33.106,74.409,56.647,120.015,68.089v-0.017c0,0.22,1.288,0.338,2.158,0.338c2.776,0,5.26-1.09,7.275-3.107L509,205.71 C511.647,203.062,512.575,198.727,511.653,195.545z M179.969,439.104l-0.021-0.051c-21.899-9.701-41.643-23.277-58.851-40.485 c-76.502-76.501-76.502-200.981,0-277.483c38.255-38.256,88.49-57.377,138.742-57.377c50.239,0,100.495,19.13,138.741,57.377 c17.209,17.209,30.783,36.953,40.485,58.852l0.072,0.021c-68.393,0.168-134.341,27.402-183.049,76.118 C207.375,304.779,180.142,370.728,179.969,439.104z M204.581,480.067c-1.461-8.557-2.461-17.209-2.983-25.878 c-4.137-68.081,21.19-134.824,69.491-183.115c44.875-44.884,105.674-69.928,168.688-69.928c4.797,0,9.615,0.145,14.433,0.439 c8.665,0.523,17.315,1.522,25.873,2.984L204.581,480.067z M462.426,180.93c-10.888-28.139-27.292-53.294-48.845-74.847 c-84.775-84.774-222.71-84.771-307.483,0c-84.772,84.773-84.772,222.709,0,307.482c21.554,21.552,46.709,37.957,74.848,48.846 c0.695,7.955,1.75,15.883,3.17,23.732c-34.877-11.654-66.769-31.329-93.02-57.582C46.021,383.49,21.198,323.564,21.198,259.822 S46.021,136.155,91.094,91.081c45.074-45.074,105-69.897,168.741-69.897c63.742,0,123.669,24.823,168.742,69.896 c26.251,26.251,45.926,58.144,57.582,93.02C478.308,182.679,470.38,181.624,462.426,180.93z"/></svg>
            </i>
        </li>
        <?php endif; ?>
        <li ng-if="settings.nbdesigner_share_design == 'yes'" class="menu-item item-share nbd-show-popup-share animated slideInDown animate800" ng-click="saveData('share')"><i class="icon-nbd icon-nbd-share2"></i></li>
        <?php if( $task == 'create_template' ): ?>
        <li class="menu-item item-process animated slideInDown animate900" id="save-template" ng-click="loadTemplateCat()">
            <span><?php esc_html_e('Save Template','web-to-print-online-designer'); ?></span><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i>
        </li>
        <?php elseif( $show_nbo_option && ($settings['nbdesigner_display_product_option'] == '1' || wp_is_mobile() ) && isset( $_GET['src'] ) && $_GET['src'] == 'studio' ): ?>
        <li class="menu-item item-process animated slideInDown animate900" id="save-template" ng-click="getPrintingOptions()">
            <span><?php esc_html_e('Process','web-to-print-online-designer'); ?></span><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i>
        </li>
        <?php else: ?>

        <?php //CS botak download image ?>
        <li class="menu-item btn-group nbd-download">
            <!-- <button type="button" class="btn nb-btn-dropdown-toggle" onclick="showDownloadDropdown()">
                <svg enable-background="new 0 0 482.239 482.239" height="16" width="16" viewBox="0 0 482.239 482.239" xmlns="http://www.w3.org/2000/svg"><path d="m0 447.793h482.239v34.446h-482.239z"/><path d="m396.091 223.863-24.287-24.354-113.462 113.462v-312.971h-34.446v312.971l-113.495-113.496-24.22 24.354 154.938 155.073z"/></svg>
                <span><?php //esc_html_e('Download','web-to-print-online-designer'); ?></span>
            </button> -->
            <div class="nb-dropdown-tab" id="nb-dropdown-tab">
                <div class="nb-box-payment-download" id="nb-box-payment-download">
                    <div class="nb-box-header">
                        <div class="nb-btn-back" onclick="showDownloadDropdown()"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" version="1.1" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"><g><path d="M448.9,929.8L10,503.3l81.3-78l436.8,426.6L448.9,929.8L448.9,929.8z M73.7,558.9V448.7H990v110.2H73.7L73.7,558.9z M91.4,582.2l-81.3-79l433.5-433l79.1,78L106.5,565L91.4,582.2z"/></g></svg></div>
                        <p>Download</p>
                        <div class="nb-btn-close" onclick="closeDownloadDropdown()">×</div>
                    </div>
                    <div class="nb-box-body">
                        <div class="nb-box-loading" id="nb-box-loading">
                            <div class="nb-box-loading-container">
                                <svg class="circular" viewBox="25 25 50 50">
                                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
                                </svg>
                                <p>Processing...</p>
                            </div>
                        </div>
                        <div class="nb-box-download" id="nb-box-download">
                            <div ng-if="dataImage.images_unpaid.length >= 1">
                                <div class="nb-form-row" id="nb-row-images">
                                    <div class="total-image" ng-click="toggleShowImages()" ng-if="dataImage.images_unpaid.length > 1">
                                        <div class="paid-image">{{dataImage.images_unpaid.length}} x Paid images</div>
                                        <div class="collapse-button">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="16" height="16" viewBox="0 0 960 560" enable-background="new 0 0 960 560" xml:space="preserve"><g id="Rounded_Rectangle_33_copy_4_1_"><path d="M480,344.181L268.869,131.889c-15.756-15.859-41.3-15.859-57.054,0c-15.754,15.857-15.754,41.57,0,57.431l237.632,238.937 c8.395,8.451,19.562,12.254,30.553,11.698c10.993,0.556,22.159-3.247,30.555-11.698l237.631-238.937 c15.756-15.86,15.756-41.571,0-57.431s-41.299-15.859-57.051,0L480,344.181z"/></g></svg>
                                        </div>
                                        <div class="total">${{dataImage.total.toFixed(2)}}</div>
                                    </div>
                                    <div class="total-image" ng-click="toggleShowImages()" ng-if="dataImage.images_unpaid.length == 1">
                                        <div class="paid-image">{{dataImage.images_unpaid.length}} x Paid images</div>
                                        <div class="collapse-button">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="16" height="16" viewBox="0 0 960 560" enable-background="new 0 0 960 560" xml:space="preserve"><g id="Rounded_Rectangle_33_copy_4_1_"><path d="M480,344.181L268.869,131.889c-15.756-15.859-41.3-15.859-57.054,0c-15.754,15.857-15.754,41.57,0,57.431l237.632,238.937 c8.395,8.451,19.562,12.254,30.553,11.698c10.993,0.556,22.159-3.247,30.555-11.698l237.631-238.937 c15.756-15.86,15.756-41.571,0-57.431s-41.299-15.859-57.051,0L480,344.181z"/></g></svg>
                                        </div>
                                        <div class="total">${{dataImage.total}}</div>
                                    </div>
                                    <div class="row-image" ng-if="dataImage.images_unpaid.length > 0">
                                        <div class="list-img" ng-repeat="img in dataImage.images_unpaid">
                                            <div class="block-img">
                                                <img ng-src="{{img.img_url}}"/>
                                            </div>
                                            <div class="block-price">
                                                <p class="title">{{img.img_title}}</p>
                                                <p class="price">${{img.img_price}}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <select id="nb-payment-method" onchange="changePaymentMethod()">
                                    <option value="stripe" selected="selected">Credit or debit card</option>
                                    <option value="paypal">Paypal</option>
                                </select>

                                <button class="btn nb-btn-dropdown-form" <?php //ng-click="saveDataAndDownload()" ?> onclick="showPaymentBox()">Continue</button>
                            </div>
                        </div>
                
                        <div class="nb-box-download" ng-if="dataImage.images_unpaid.length == 0 && (dataDownload.downloadImageUrl === '' || dataDownload.isPaid !== true)">
                            There are no photos to be paid!
                        </div>
                        
                        <div class="nb-box-payment" id="nb-box-payment" ng-class="dataImage.images_unpaid.length < 1 ? 'ng-hide' : ''">
                            <form ng-submit="submitStripe()" action="" method="post" id="nb-stripe-form">
                                <div class="form-row">
                                    <div class="group">
                                        <div id="card-number-element" class="field"></div>
                                        <div id="card-expiry-element" class="field"></div>
                                        <div id="card-cvc-element" class="field"></div>
                                    </div>

                                    <!-- Used to display form errors -->
                                    <div id="card-errors"></div>
                                    <input type="hidden" name="action" value="nbd_payment_stripe" />
                                </div>
                                <button id="nb-btn-submit-payment-stripe" class="btn nb-btn-dropdown-form">Submit Payment</button>
                            </form>
                            
                            <form ng-submit="submitPaypal()" action="" method="post" id="nb-paypal-form" style="display: none;">
                                <button id="nb-btn-submit-payment-paypal" class="btn nb-btn-dropdown-form">Submit Payment</button>
                            </form>
                        </div>
                        <div class="nb-box-success" id="nb-box-success" ng-if="dataDownload.downloadImageUrl !== '' && dataDownload.isPaid == true && dataImage.images_unpaid.length < 1">
                            <p>Payment success! Now you can download images without watermark!</p>
                            <button id="nb-btn-download-images" class="btn nb-btn-dropdown-form" ng-click="downloadImages()">Download</button>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        
        <li ng-class="printingOptionsAvailable ? '' : 'nbd-disabled'" class="menu-item item-process animated slideInDown animate900 save-data" data-overlay="overlay" 
            <?php if( $task == 'create' || ( $task == 'edit' && ( isset( $_GET['design_type'] ) && $_GET['design_type'] == 'template' ) ) ): ?>
            ng-click="prepareSaveTemplate()" 
            <?php else: ?>
            ng-click="saveData()" 
            <?php endif; ?> 
            data-tour="process" data-tour-priority="7">
            <span><?php esc_html_e('Process','web-to-print-online-designer'); ?></span><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i>
        </li>

        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>-->
        <script src="https://js.stripe.com/v3/"></script>
        <script>         
            function showDownloadDropdown() {
                document.getElementById("nb-dropdown-tab").style.display = "block";
                document.getElementById("nb-box-download").style.display = "block";
                document.getElementById("nb-box-payment").style.display = "none";
            }
            
            function closeDownloadDropdown() {
                document.getElementById("nb-dropdown-tab").style.display = "none";
                document.getElementById("nb-box-download").style.display = "block";
                document.getElementById("nb-box-payment").style.display = "none";
            }
            
            function showPaymentBox() {
                document.getElementById("nb-box-download").style.display = "none";
                document.getElementById("nb-box-payment").style.display = "block";
            }
            
            function changePaymentMethod() {
                var payment_method = document.getElementById("nb-payment-method");
                if (payment_method.options[payment_method.selectedIndex].value == "paypal") {
                    document.getElementById("nb-stripe-form").style.display = "none";
                    document.getElementById("nb-paypal-form").style.display = "block";
                } else if (payment_method.options[payment_method.selectedIndex].value == "stripe") {
                    document.getElementById("nb-stripe-form").style.display = "block";
                    document.getElementById("nb-paypal-form").style.display = "none";
                };
            }
            
            // Stripe API Key
            var stripe = Stripe('<?php echo get_option('nbdesigner_stripe_publishable_key', true); ?>');
            var elements = stripe.elements();

            // Custom Styling
            var style = {
                base: {
                    color: '#32325d',
                    lineHeight: '24px',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            var cardNumberElement = elements.create('cardNumber', {
                style: style
            });
            cardNumberElement.mount('#card-number-element');

            var cardExpiryElement = elements.create('cardExpiry', {
                style: style
            });
            cardExpiryElement.mount('#card-expiry-element');

            var cardCvcElement = elements.create('cardCvc', {
                style: style
            });
            cardCvcElement.mount('#card-cvc-element');

//            // Handle form stripe submission
//            var form_stripe = document.getElementById('nb-stripe-form');
//            form_stripe.addEventListener('submit', function(event) {
//                event.preventDefault();
//                document.getElementById("nb-box-loading").style.display = "flex";
//                stripe.createToken(cardNumberElement).then(function(result) {
//                    if (result.error) {
//                        // Inform the user if there was an error
//                        var errorElement = document.getElementById('card-errors');
//                        errorElement.textContent = result.error.message;
//                        document.getElementById("nb-box-loading").style.display = "none";
//                    } else {
//                        stripeTokenHandler(result.token);
//                    }
//                });
//            });
//
//            // Send Stripe Token to Server
//            function stripeTokenHandler(token) {
//                let form = new FormData();
//
//                form.append('action', 'nbd_payment_stripe');
//                form.append('stripeToken', token.id);
//                form.append('amount', NBDESIGNCONFIG['download_price']);
//                form.append('description', NBDESIGNCONFIG['download_description']);
//
//                axios.post("<?php echo site_url() . '/wp-admin/admin-ajax.php' ?>", form)
//                .then(function (response) {
//                    //handle success
//                    if (NBDESIGNCONFIG['dlf_after_save'] != '' && typeof NBDESIGNCONFIG['dlf_after_save'] != "undefined") {
//                        url = NBDESIGNCONFIG['dlf_after_save'].replace(/['"]+/g, ''); //split " and ' character
//                        window.location = window.atob(url);
//                    } else {
//                        alert("Some error occur when save file!");
//                    }
//                    document.getElementById("nb-box-loading").style.display = "none";
//                })
//                .catch(function (error) {
//                    //handle error
//                    if (error.response.status == 400) {
//                        alert(error.response.data);
//                    } else {
//                        alert("Some error occur! Please contact to admin!");
//                    }
//                    document.getElementById("nb-box-loading").style.display = "none";
//                });
//            }
//
//            // Handle form Paypal submission
//            var form_paypal = document.getElementById('nb-paypal-form');
//            form_paypal.addEventListener('submit', function(event) {
//                event.preventDefault();
//                submitPaypalRequest();
//            });
//
//            // Send Paypal Token to Server
//            function submitPaypalRequest() {
//                document.getElementById("nb-box-loading").style.display = "flex";
//                if (NBDESIGNCONFIG['dlf_after_save'] != '' && typeof NBDESIGNCONFIG['dlf_after_save'] != "undefined") {
//                    let form = new FormData();
//
//                    form.append('action', 'paypal_request');
//                    form.append('amount', NBDESIGNCONFIG['download_price']);
//                    form.append('description', NBDESIGNCONFIG['download_description']);
//                    form.append('rdrl', NBDESIGNCONFIG['dlf_after_save']);
//
//                    axios.post("<?php echo site_url() . '/wp-admin/admin-ajax.php' ?>", form)
//                    .then(function (response) {
//                        if (response.status == 200) {
//                            document.getElementById("nb-box-loading").style.display = "none";
//                            let url = response.data;
//                            window.open(url, '_blank');
//                        }
//                    })
//                    .catch(function (error) {
//                        document.getElementById("nb-box-loading").style.display = "none";
//                        alert(error);
//                    });
//                } else {
//                    document.getElementById("nb-box-loading").style.display = "none";
//                    alert("Some error occur when save file!");
//                }
//            }
        </script>

        <style>
            .StripeElement {
                background-color: white;
                padding: 8px 12px;
                border-radius: 4px;
                border: 1px solid transparent;
                box-shadow: 0 1px 3px 0 #e6ebf1;
                -webkit-transition: box-shadow 150ms ease;
                transition: box-shadow 150ms ease;
            }
            .StripeElement--focus {
                box-shadow: 0 1px 3px 0 #cfd7df;
            }
            .StripeElement--invalid {
                border-color: #fa755a;
            }
            .StripeElement--webkit-autofill {
                background-color: #fefde5 !important;
            }
        </style>
        <?php endif; ?>
    </ul>
</div>