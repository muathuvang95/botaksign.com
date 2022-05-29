<div class="nbd-context-menu" id="nbd-context-menu" ng-style="ctxMenuStyle" ng-click="ctxMenuStyle.visibility = 'hidden'">
    <div class="main-context">
        <ul class="contexts">
            <li class="context-item" ng-click="setLayerAttribute('excludeFromExport', !stages[currentStage].states.excludeFromExport)" ng-show="settings.task == 'create_template' && !stages[currentStage].states.excludeFromExport"><i class="icon-nbd icon-nbd-clear"></i> <?php esc_html_e('Exclude from export','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="rotateLayer('reflect-hoz')" ng-show="stages[currentStage].states.isLayer"><i class="icon-nbd icon-nbd-reflect-horizontal"></i> <?php esc_html_e('Reflect Horizontal','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="rotateLayer('reflect-ver')" ng-show="stages[currentStage].states.isLayer"><i class="icon-nbd icon-nbd-reflect-vertical"></i> <?php esc_html_e('Reflect Vertical','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="fitToStage('width')" ng-show="stages[currentStage].states.isLayer && stages[currentStage].states.isImage">
                <i class="icon-nbd icon-nbd-24">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#888" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M0.938 5.203c-0.518 0-0.938 0.42-0.938 0.938v11.719c0 0.518 0.42 0.938 0.938 0.938s0.938-0.42 0.938-0.938v-11.719c0-0.518-0.42-0.938-0.938-0.938z"></path>
                        <path d="M23.063 5.203c-0.518 0-0.938 0.42-0.938 0.938v11.719c0 0.518 0.42 0.938 0.938 0.938s0.938-0.42 0.938-0.938v-11.719c0-0.518-0.42-0.938-0.938-0.938z"></path>
                        <path d="M16.453 11.063h-12.196l2.684-2.663c0.368-0.365 0.37-0.958 0.005-1.326s-0.958-0.37-1.326-0.005l-2.914 2.89c-0.537 0.532-0.832 1.241-0.832 1.994s0.296 1.462 0.832 1.994l2.914 2.89c0.183 0.181 0.421 0.272 0.66 0.272 0.241 0 0.482-0.093 0.666-0.277 0.365-0.368 0.362-0.961-0.005-1.326l-2.59-2.569h12.102c0.518 0 0.938-0.42 0.938-0.938s-0.42-0.938-0.938-0.938z"></path>
                        <path d="M21.293 9.959l-2.914-2.89c-0.368-0.365-0.961-0.362-1.326 0.005s-0.362 0.961 0.005 1.326l2.914 2.89c0.179 0.178 0.278 0.413 0.278 0.663s-0.099 0.486-0.278 0.663l-2.914 2.89c-0.368 0.365-0.37 0.958-0.005 1.326 0.183 0.185 0.424 0.277 0.666 0.277 0.239 0 0.477-0.091 0.66-0.272l2.914-2.89c0.537-0.532 0.832-1.241 0.832-1.994s-0.296-1.462-0.832-1.994z"></path>
                    </svg>
                </i> <?php esc_html_e('Fit to width','web-to-print-online-designer'); ?>
            </li>
            <li class="context-item" ng-click="fitToStage('height')" ng-show="stages[currentStage].states.isLayer && stages[currentStage].states.isImage">
                <i class="icon-nbd icon-nbd-24">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="#888" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M17.859 0h-11.719c-0.518 0-0.938 0.42-0.938 0.938s0.42 0.938 0.938 0.938h11.719c0.518 0 0.938-0.42 0.938-0.938s-0.42-0.938-0.938-0.938z"></path>
                        <path d="M17.859 22.125h-11.719c-0.518 0-0.938 0.42-0.938 0.938s0.42 0.938 0.938 0.938h11.719c0.518 0 0.938-0.42 0.938-0.938s-0.42-0.938-0.938-0.938z"></path>
                        <path d="M16.931 5.621l-2.89-2.914c-0.532-0.537-1.241-0.832-1.994-0.832s-1.462 0.296-1.994 0.832l-2.89 2.914c-0.365 0.368-0.362 0.961 0.005 1.326s0.961 0.362 1.326-0.005l2.569-2.59v12.195c0 0.518 0.42 0.938 0.938 0.938s0.938-0.42 0.938-0.938v-12.29l2.663 2.684c0.183 0.185 0.424 0.277 0.666 0.277 0.239 0 0.477-0.091 0.66-0.272 0.368-0.365 0.37-0.958 0.005-1.326z"></path>
                        <path d="M16.926 17.053c-0.368-0.365-0.961-0.362-1.326 0.005l-2.89 2.914c-0.178 0.179-0.413 0.278-0.663 0.278s-0.486-0.099-0.663-0.278l-2.89-2.914c-0.365-0.368-0.958-0.37-1.326-0.005s-0.37 0.958-0.005 1.326l2.89 2.914c0.532 0.537 1.241 0.832 1.994 0.832s1.462-0.296 1.994-0.832l2.89-2.914c0.365-0.368 0.362-0.961-0.005-1.326z"></path>
                    </svg>
                </i> <?php esc_html_e('Fit to height','web-to-print-online-designer'); ?>
            </li>
            <li class="context-item" ng-click="fitToStage()" ng-show="stages[currentStage].states.isLayer && stages[currentStage].states.isImage">
                <i class="icon-nbd">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="icon-nbd_stretch" fill="#888" width="24" height="24" viewBox="0 0 24 24"><defs><path id="a" d="M0 0h24v24H0z"/></defs><clipPath id="b"><use xlink:href="#a" overflow="visible"/></clipPath><path clip-path="url(#b)" d="M15 3l2.3 2.3-2.89 2.87 1.42 1.42L18.7 6.7 21 9V3zM3 9l2.3-2.3 2.87 2.89 1.42-1.42L6.7 5.3 9 3H3zm6 12l-2.3-2.3 2.89-2.87-1.42-1.42L5.3 17.3 3 15v6zm12-6l-2.3 2.3-2.87-2.89-1.42 1.42 2.89 2.87L15 21h6z"/><path clip-path="url(#b)" fill="none" d="M0 0h24v24H0z"/></svg>
                </i> <?php esc_html_e('Stretch','web-to-print-online-designer'); ?>
            </li>
            <li class="separator" ng-show="stages[currentStage].states.isLayer"></li>
            <li class="context-item" ng-click="setStackPosition('bring-front')" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"><i class="icon-nbd icon-nbd-bring-to-front"></i> <?php esc_html_e('Bring to Front','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="setStackPosition('bring-forward')" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"><i class="icon-nbd icon-nbd-bring-forward"></i> <?php esc_html_e('Bring Forward','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="setStackPosition('send-backward')" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"><i class="icon-nbd icon-nbd-sent-to-backward"></i> <?php esc_html_e('Send to Backward','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="setStackPosition('send-back')" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"><i class="icon-nbd icon-nbd-send-to-back"></i> <?php esc_html_e('Send to Back','web-to-print-online-designer'); ?></li>
            <li class="separator" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"></li>
            <li class="context-item" ng-click="translateLayer('vertical')" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"><i class="icon-nbd icon-nbd-fomat-vertical-align-center"></i> <?php esc_html_e('Center horizontal','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="translateLayer('horizontal')" ng-show="stages[currentStage].states.isLayer && !isTemplateMode"><i class="icon-nbd icon-nbd-fomat-vertical-align-center rotate90"></i> <?php esc_html_e('Center vertical','web-to-print-online-designer'); ?></li>
            <!--  Group  -->
            <li class="context-item" ng-click="alignLayer('vertical')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-fomat-vertical-align-center rotate90"></i> <?php esc_html_e('Align Vertical Center','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('horizontal')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-fomat-vertical-align-center"></i> <?php esc_html_e('Align Horizontal Center','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('left')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-fomat-vertical-align-top rotate-90"></i> <?php esc_html_e('Align Left','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('right')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-fomat-vertical-align-top rotate90"></i> <?php esc_html_e('Align Right','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('top')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-fomat-vertical-align-top"></i> <?php esc_html_e('Align Top','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('bottom')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-fomat-vertical-align-top rotate-180"></i> <?php esc_html_e('Align Bottom','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('dis-horizontal')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-dis-horizontal"></i> <?php esc_html_e('Distribute Horizontal','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="alignLayer('dis-vertical')" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-dis-vertical"></i> <?php esc_html_e('Distribute Vertical','web-to-print-online-designer'); ?></li>
            <!--  Template Mode  -->
            <li ng-class="stages[currentStage].states.elementUpload ? 'active' : ''" ng-click="setLayerAttribute('elementUpload', !stages[currentStage].states.elementUpload)" class="context-item" ng-show="stages[currentStage].states.isImage && isTemplateMode"><i class="icon-nbd icon-nbd-replace-image"></i> <?php esc_html_e('Replace Image','web-to-print-online-designer'); ?></li>
            <li ng-class="!stages[currentStage].states.forceLock ? '' : 'active'" class="context-item" ng-click="setLayerAttribute('forceLock', !stages[currentStage].states.forceLock)" ng-show="stages[currentStage].states.isLayer && isTemplateMode">
                <i class="icon-nbd icon-nbd-lock nbd-padding-left-3"></i> <?php esc_html_e('Lock all adjustment','web-to-print-online-designer'); ?>
            </li>
            <li ng-class="stages[currentStage].states.text.editable ? '' : 'active'" class="context-item" ng-click="setTextAttribute('editable', !stages[currentStage].states.text.editable)" ng-show="stages[currentStage].states.isText && isTemplateMode">
                <i class="icon-nbd icon-nbd-lock nbd-padding-left-3"></i> <?php esc_html_e('Lock edit','web-to-print-online-designer'); ?>
            </li>
            <li ng-class="!stages[currentStage].states.lockMovementX ? '' : 'active'" class="context-item" ng-click="setLayerAttribute('lockMovementX', !stages[currentStage].states.lockMovementX)" ng-show="stages[currentStage].states.isLayer && isTemplateMode">
                <i class="icon-nbd icon-nbd-arrows-h nbd-font-size-18"></i> <?php esc_html_e('Lock horizontal movement','web-to-print-online-designer'); ?>
            </li>       
            <li ng-class="!stages[currentStage].states.lockMovementY ? '' : 'active'" class="context-item" ng-click="setLayerAttribute('lockMovementY', !stages[currentStage].states.lockMovementY)" ng-show="stages[currentStage].states.isLayer && isTemplateMode">
                <i class="icon-nbd icon-nbd-arrows-v nbd-font-size-18 nbd-padding-left-5"></i> <?php esc_html_e('Lock vertical movement','web-to-print-online-designer'); ?>
            </li>
            <li ng-class="!stages[currentStage].states.lockScalingX ? '' : 'active'" class="context-item" ng-click="setLayerAttribute('lockScalingX', !stages[currentStage].states.lockScalingX)" ng-show="stages[currentStage].states.isLayer && isTemplateMode">
                <i class="icon-nbd icon-nbd-expand horizontal horizontal-x nbd-font-size-18"><sub>x</sub></i> <?php esc_html_e('Lock horizontal scaling','web-to-print-online-designer'); ?>
            </li>     
            <li ng-class="!stages[currentStage].states.lockScalingY ? '' : 'active'" class="context-item" ng-click="setLayerAttribute('lockScalingY', !stages[currentStage].states.lockScalingY)" ng-show="stages[currentStage].states.isLayer && isTemplateMode">
                <i class="icon-nbd icon-nbd-expand horizontal horizontal-y nbd-font-size-18"><sub>y</sub></i> <?php esc_html_e('Lock vertical scaling','web-to-print-online-designer'); ?>
            </li>
            <li ng-class="!stages[currentStage].states.lockRotation ? '' : 'active'" class="context-item" ng-click="setLayerAttribute('lockRotation', !stages[currentStage].states.lockRotation)" ng-show="stages[currentStage].states.isLayer && isTemplateMode">
                <i class="icon-nbd icon-nbd-refresh rotate180"></i> <?php esc_html_e('Lock rotation','web-to-print-online-designer'); ?>
            </li>
            <?php do_action( 'nbd_modern_extra_context_menu' ); ?>
            <!--  Template Mode  -->
            <li class="separator"></li>
            <li ng-if="settings.nbdesigner_enable_template_mapping == 'yes' && settings.template_fields.length > 0" class="context-item context-sub-menu" ng-show="stages[currentStage].states.isLayer && stages[currentStage].states.isText && isTemplateMode">
                <i class="icon-nbd icon-nbd-24">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#888" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                </i> <?php esc_html_e('Map layer with','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90 nbd-margin-left-auto" ></i>
                <ul class="second-contexts contexts left">
                    <li ng-click="mapLayerWith( field.key )" ng-repeat="field in settings.template_fields" ng-class="stages[currentStage].states.field_mapping == field.key ? 'active' : ''" class="context-item">{{field.name}}</li>
                </ul>
            </li>
            <li class="context-item" ng-click="copyLayers()"><i class="icon-nbd icon-nbd-content-copy"></i> <?php esc_html_e('Duplicate','web-to-print-online-designer'); ?></li>
            <li class="context-item"  ng-click="deactiveAllLayer()" ng-show="stages[currentStage].states.isGroup"><i class="icon-nbd icon-nbd-ungroup"></i> <?php esc_html_e('Ungroup','web-to-print-online-designer'); ?></li>
            <li class="context-item" ng-click="deleteLayers()"><i class="icon-nbd icon-nbd-delete"></i> <?php esc_html_e('Delete','web-to-print-online-designer'); ?></li>
        </ul>
    </div>
</div>