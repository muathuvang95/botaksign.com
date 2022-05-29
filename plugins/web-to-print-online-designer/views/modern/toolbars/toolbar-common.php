<div class="toolbar-seperate"></div>
<div class="toolbar-common">
    <ul class="nbd-main-menu" ng-hide="stages[currentStage].states.isMask">
        <?php do_action('nbd_modern_before_layer_common_menu'); ?>
        <li ng-if="settings.is_mobile" class="menu-item item-opacity ipad-mini-hidden" ng-click="copyLayers()">
            <i class="icon-nbd icon-nbd-content-copy nbd-tooltip-hover" title="<?php esc_html_e('Duplicate','web-to-print-online-designer'); ?>"></i>
        </li>
        <li ng-if="settings.is_mobile" class="menu-item item-opacity ipad-mini-hidden" ng-click="deleteLayers()">
            <i class="icon-nbd icon-nbd-delete nbd-tooltip-hover" title="<?php esc_html_e('Delete','web-to-print-online-designer'); ?>"></i>
        </li>
        <?php if( is_user_logged_in() ): ?>
        <li ng-if="false" class="menu-item item-opacity item-store-layer" ng-click="storeLayers()">
            <i class="icon-nbd nbd-tooltip-hover" title="<?php esc_html_e('Store layers as your own design','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#888" d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
            </i>
        </li>
        <?php endif; ?>
        <li class="menu-item item-opacity item-angle ipad-mini-hidden" data-range="true" ng-show="stages[currentStage].states.enableRotate">
            <i class="icon-nbd icon-nbd-refresh nbd-tooltip-hover" title="<?php esc_html_e('Angle','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu" data-pos="center">
                <div class="main-ranges" >
                    <div class="range range-opacity">
                        <label><?php esc_html_e('Angle','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" type="range" step="0.1" min="0" max="360" ng-change="setLayerAttribute('angle', stages[currentStage].states.angle)" ng-model="stages[currentStage].states.angle">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.angle}}</span>
                    </div>
                </div>
            </div>
        </li>
        <li class="menu-item item-opacity item--opacity" data-range="true" ng-show="stages[currentStage].states.enableOpacity">
            <i class="icon-nbd icon-nbd-opacity nbd-tooltip-hover" title="<?php esc_html_e('Opacity','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu" data-pos="center">
                <div class="main-ranges">
                    <div class="range range-opacity">
                        <label><?php esc_html_e('Opacity','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" type="range" step="1" min="0" max="100" ng-change="setTextAttribute('opacity', stages[currentStage].states.opacity / 100)" ng-model="stages[currentStage].states.opacity">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.opacity}}</span>
                    </div>
                </div>
            </div>
        </li>
        <li class="menu-item item-stack ipad-mini-hidden" ng-class="stages[currentStage].states.isLayer ? '' : 'nbd-disabled'">
            <i class="icon-nbd icon-nbd-layer-stack nbd-tooltip-hover" title="<?php esc_html_e('Layer stack','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu" data-pos="right">
                <ul>
                    <li class="sub-menu-item" ng-click="setStackPosition('bring-front')">
                        <i class="icon-nbd icon-nbd-bring-to-front"></i>
                        <span><?php esc_html_e('Bring to Front','web-to-print-online-designer'); ?></span>
                        <span class="keyboard">{{ 'M-S-]' | keyboardShortcut }}</span>
                    </li>
                    <li class="sub-menu-item" ng-click="setStackPosition('bring-forward')">
                        <i class="icon-nbd icon-nbd-bring-forward"></i>
                        <span><?php esc_html_e('Bring Forward','web-to-print-online-designer'); ?></span>
                        <span class="keyboard">{{ 'M-]' | keyboardShortcut }}</span>
                    </li>
                    <li class="sub-menu-item" ng-click="setStackPosition('send-backward')">
                        <i class="icon-nbd icon-nbd-sent-to-backward"></i>
                        <span><?php esc_html_e('Send to Backward','web-to-print-online-designer'); ?></span>
                        <span class="keyboard">{{ 'M-[' | keyboardShortcut }}</span>
                    </li>
                    <li class="sub-menu-item" ng-click="setStackPosition('send-back')">
                        <i class="icon-nbd icon-nbd-send-to-back"></i>
                        <span><?php esc_html_e('Send to Back','web-to-print-online-designer'); ?></span>
                        <span class="keyboard">{{ 'M-S-[' | keyboardShortcut }}</span>
                    </li>
                </ul>
            </div>
        </li>
        <li class="menu-item item-position">
            <i class="icon-nbd icon-nbd-baseline-tune nbd-tooltip-hover" title="<?php esc_html_e('Layer position','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu" data-pos="right">
                <ul>
                    <i class="icon-nbd-clear nbd-close-sub-menu"></i>
                    <li class="title">
                        <span><?php esc_html_e('Layer position','web-to-print-online-designer'); ?></span>
                        <i class="colse"></i>
                    </li>
                    <li ng-click="translateLayer('vertical')"><i class="icon-nbd icon-nbd-fomat-vertical-align-center nbd-tooltip-hover" title="<?php esc_html_e('Center horizontal','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('top-left')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate-90" title="<?php esc_html_e('Top left','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('top-center')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate-45" title="<?php esc_html_e('Top center','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('top-right')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover" title="Top right"></i></li>
                    <li ng-click="translateLayer('horizontal')"><i class="icon-nbd icon-nbd-fomat-vertical-align-center nbd-tooltip-hover rotate90" title="<?php esc_html_e('Center vertical','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('middle-left')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate-135" title="<?php esc_html_e('Middle left','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('center')"><i class="icon-nbd icon-nbd-bottom-center nbd-tooltip-hover middle-center" title="<?php esc_html_e('Middle center','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('middle-right')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate45" title="<?php esc_html_e('Middle right','web-to-print-online-designer'); ?>"></i></li>
                    <li class="intro"><i class="icon-nbd icon-nbd-info-circle nbd-tooltip-hover" title="<?php esc_html_e('Intro','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('bottom-left')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate-180" title="<?php esc_html_e('Bottom left','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('bottom-center')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate135" title="<?php esc_html_e('Bottom center','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="translateLayer('bottom-right')"><i class="icon-nbd icon-nbd-fomat-top-left nbd-tooltip-hover rotate90" title="<?php esc_html_e('Bottom right','web-to-print-online-designer'); ?>"></i></li>
                </ul>
            </div>
        </li>
    </ul>
</div>