<div class="<?php if( $active_elements ) echo 'active'; ?> tab" id="tab-element" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-element" data-type="element" data-offset="20">
    <div class="nbd-search">
        <input ng-class="(!(resource.element.type == 'icon' || resource.element.type == 'flaticon') || !resource.element.onclick) ? 'nbd-disabled' : ''" ng-keyup="$event.keyCode == 13 && getMedia(resource.element.type, 'search')" type="text" name="search" placeholder="<?php esc_html_e('Search element', 'web-to-print-online-designer'); ?>" ng-model="resource.element.contentSearch"/>
        <i class="icon-nbd icon-nbd-fomat-search"></i>
    </div>     
    <div class="tab-main tab-scroll" >
        <div class="nbd-items-dropdown">
            <div class="main-items">
                <div class="items">
                    <div ng-if="!!settings.nbes_enable_settings && ( settings.nbes_enable_settings.background == 1 || settings.nbes_enable_settings.combination == 1 )" class="item item-color" data-type="color" data-api="false" ng-click="onClickTab('color', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-fill-color" ></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Color','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_draw'] == 'yes' && !settings.is_mobile" class="item" data-type="draw" data-api="false" ng-click="onClickTab('draw', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-drawing"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Draw','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_shapes'] == 'yes'" class="item" data-type="shapes" data-api="false" ng-click="onClickTab('shape', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-shapes"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Shapes','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_icons'] == 'yes'" class="item" data-type="icons" data-api="false" ng-click="onClickTab('icon', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-diamond"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Icons','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_flaticon'] == 'yes' && settings['nbdesigner_flaticon_api_key'] != ''" class="item" data-type="flaticon" data-api="false" ng-click="onClickTab('flaticon', 'element')">
                        <div class="main-item">
                            <div class="item-icon" style="padding: 20px 5px">
                                <i class="icon-nbd" style="font-size: 30px;background: #0E2A47;padding: 0 5px;border-radius: 4px;">
                                    <svg style="vertical-align: middle;" width="60px" viewBox="0 0 561 103" xmlns="http://www.w3.org/2000/svg"><g fill="none"><path d="M141.596 29.675c0-3.777 2.985-6.767 6.764-6.767h34.438a6.115 6.115 0 016.15 6.15 6.11 6.11 0 01-6.15 6.149h-27.674v13.091h23.719a6.112 6.112 0 016.151 6.15 6.109 6.109 0 01-6.151 6.149h-23.719v17.574c0 3.773-2.986 6.761-6.764 6.761-3.779 0-6.764-2.989-6.764-6.761V29.675zm52.248-.526c0-3.781 2.985-6.767 6.764-6.767 3.776 0 6.763 2.985 6.763 6.767v42.957h25.039a6.114 6.114 0 016.149 6.153 6.112 6.112 0 01-6.149 6.15h-31.802c-3.779 0-6.764-2.986-6.764-6.768V29.149zm48.047 46.561l21.438-48.407c1.492-3.341 4.215-5.357 7.906-5.357h.792c3.686 0 6.323 2.017 7.815 5.357l21.439 48.407c.436.967.701 1.845.701 2.723 0 3.602-2.809 6.501-6.414 6.501-3.161 0-5.269-1.845-6.499-4.655l-4.132-9.661h-27.059l-4.301 10.102c-1.144 2.631-3.426 4.214-6.237 4.214-3.517 0-6.24-2.81-6.24-6.325 0-.969.351-1.932.791-2.899zm38.041-17.044l-8.521-20.297-8.526 20.297h17.047zm34.932-23.279H301.86c-3.429 0-6.239-2.813-6.239-6.238 0-3.429 2.811-6.24 6.239-6.24h39.533c3.426 0 6.237 2.811 6.237 6.24 0 3.425-2.811 6.238-6.237 6.238h-13.001v42.785c0 3.773-2.99 6.761-6.764 6.761-3.779 0-6.764-2.989-6.764-6.761V35.387z" fill="#FFF"/><path d="M352.615 29.149c0-3.781 2.985-6.767 6.767-6.767 3.774 0 6.761 2.985 6.761 6.767v49.024a6.711 6.711 0 01-6.761 6.761c-3.781 0-6.767-2.989-6.767-6.761V29.149zm21.517 24.687v-.179c0-17.481 13.178-31.801 32.065-31.801 9.22 0 15.459 2.458 20.557 6.238 1.402 1.054 2.637 2.985 2.637 5.357 0 3.692-2.985 6.59-6.681 6.59-1.845 0-3.071-.702-4.044-1.319-3.776-2.813-7.729-4.393-12.562-4.393-10.364 0-17.831 8.611-17.831 19.154v.173c0 10.542 7.291 19.329 17.831 19.329 5.715 0 9.492-1.756 13.359-4.834 1.049-.874 2.458-1.491 4.039-1.491 3.429 0 6.325 2.813 6.325 6.236 0 2.106-1.056 3.78-2.282 4.834-5.539 4.834-12.036 7.733-21.878 7.733-18.095.001-31.535-13.97-31.535-31.627zm58.877 0v-.179c0-17.481 13.79-31.801 32.766-31.801 18.981 0 32.592 14.143 32.592 31.628v.173c0 17.483-13.785 31.807-32.769 31.807-18.973 0-32.589-14.144-32.589-31.628zm51.215 0v-.179c0-10.539-7.725-19.326-18.626-19.326-10.893 0-18.449 8.611-18.449 19.154v.173c0 10.542 7.73 19.329 18.626 19.329 10.901-.001 18.449-8.609 18.449-19.151zm22.009-24.515c0-3.774 2.99-6.763 6.767-6.763h1.401c3.252 0 5.183 1.583 7.029 3.953l26.093 34.265V29.059a6.675 6.675 0 016.681-6.677 6.672 6.672 0 016.671 6.677v48.934c0 3.78-2.987 6.765-6.764 6.765h-.436c-3.257 0-5.188-1.581-7.034-3.953l-27.056-35.492v32.944a6.676 6.676 0 01-13.351 0V29.321h-.001z" fill="#69E781"/><path d="M48.372 56.137h12.517L72.045 37.6H37.186L25.688 18.539h57.825L94.668 0H9.271a9.265 9.265 0 00-8.073 4.716 9.265 9.265 0 00.134 9.343l50.38 83.501a9.266 9.266 0 007.938 4.476 9.264 9.264 0 007.935-4.476l2.898-4.804-22.111-36.619z" fill="#FFF"/><path d="M93.575 18.539h.031v.004l21.652.004 2.705-4.488a9.262 9.262 0 00.133-9.343A9.26 9.26 0 00110.026 0h-5.294L93.575 18.539zm-5.284 8.817l-23.566 39.13 10.794 17.918 34.423-57.048z" fill="#69E781"/></g></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Flaticon','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
<!--                    <div class="item" data-type="lines" data-api="false" ng-click="onClickTab('line', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-line"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Lines','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>-->
                    <div ng-if="settings['nbdesigner_enable_qrcode'] == 'yes'" class="item" data-type="qr-code" data-api="false" ng-click="onClickTab('qrcode', 'element')">
                        <div class="main-item">
                            <div class="item-icon"><i class="icon-nbd icon-nbd-qrcode"></i></div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Bar/QR-Code','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_vcard'] == 'yes' && settings.vcard_fields.length > 0" class="item" data-type="vcard" data-api="false" ng-click="onClickTab('vcard', 'element')">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-vcard">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                                        <path fill="#06d79c" d="M28 25h-3v-1c0.553 0 1-0.448 1-1 0-0.553-0.447-1-1-1h-2c-0.553 0-1 0.447-1 1 0 0.552 0.447 1 1 1v1h-13v-1c0.553 0 1-0.448 1-1 0-0.553-0.447-1-1-1h-2c-0.553 0-1 0.447-1 1 0 0.552 0.447 1 1 1v1h-4c-1.104 0-2-0.896-2-2v-14c0-1.104 0.896-2 2-2h24c1.104 0 2 0.896 2 2v14c0 1.104-0.896 2-2 2zM10 9.812c-1.208 0-2.188 1.287-2.188 2.875s0.98 2.875 2.188 2.875 2.188-1.287 2.188-2.875-0.98-2.875-2.188-2.875zM13.49 16.688c-0.539-0.359-2.091-0.598-2.091-0.598s-1.006 1.075-1.433 1.075c-0.428 0-1.434-1.075-1.434-1.075s-1.552 0.238-2.090 0.598c-0.539 0.358-0.777 2.261-0.777 2.261h8.615c0.001-0.001-0.157-1.84-0.79-2.261zM26 11h-9v1h9v-1zM26 13h-9v1h9v-1zM26 15h-9v1h9v-1zM26 17h-9v1h9v-1z"></path>
                                    </svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('vCard','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div  ng-if="settings['nbdesigner_enable_frame'] == 'yes'" class="item" data-type="frame" data-api="false" ng-click="onClickTab('frame', 'element')">
                        <div class="main-item">
                            <div class="item-icon">
                                <i class="icon-vcard">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#38b6ff" d="M7 19h10V4H7v15zm-5-2h4V6H2v11zM18 6v11h4V6h-4z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
                                </i>
                            </div>
                            <div class="item-info">
                                <span class="item-name"><?php esc_html_e('Frame','web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pointer"></div>
            </div>
            <div class="result-loaded">
                <div class="content-items">
                    <div class="content-item type-color" data-type="color">
                        <div class="main-type">
                            <div ng-if="!!settings.nbes_enable_settings && settings.nbes_enable_settings.combination == 1">
                                <span class="heading-title"><?php esc_html_e('Combination colors','web-to-print-online-designer'); ?></span>
                                <div class="nbes-colors">
                                    <div class="nbes-color" ng-repeat="cbg_code in settings.nbes_settings.combination_colors.bg_codes track by $index">
                                        <div ng-style="{'background-color': cbg_code}" class="bg_color" 
                                             ng-click="selectCombinationColor( $index )"
                                             title="{{settings.nbes_settings.combination_colors.bg_names[$index] + ' - ' + settings.nbes_settings.combination_colors.fg_names[$index]}}">
                                            <span ng-style="{'color': settings.nbes_settings.combination_colors.fg_codes[$index]}">Aa</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div ng-if="!!settings.nbes_enable_settings && settings.nbes_enable_settings.background == 1 && settings.nbes_enable_settings.combination != 1">
                                <span class="heading-title"><?php esc_html_e('Background colors','web-to-print-online-designer'); ?></span>
                                <div class="nbes-colors">
                                    <div class="nbes-color bg-color" ng-repeat="bg_code in settings.nbes_settings.background_colors.codes track by $index">
                                        <div ng-style="{'background-color': bg_code}" class="bg_color" 
                                             ng-click="_changeBackgroundCanvas($index)"
                                             title="{{settings.nbes_settings.background_colors.names[$index]}}">
                                            <span ng-style="{'color': bg_code}"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-draw" data-type="draw">
                        <div class="main-type">
                            <span class="heading-title"><?php esc_html_e('Free Drawing','web-to-print-online-designer'); ?></span>
                            <div class="brush" >
                                <h3 class="color-palette-label" ><?php esc_html_e('Choose ','web-to-print-online-designer'); ?></h3>
                                <button class="nbd-button nbd-dropdown">
                                    <?php esc_html_e('Brush','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-arrow-drop-down"></i>
                                    <div class="nbd-sub-dropdown" data-pos="left">
                                        <ul class="tab-scroll">
                                            <li ng-click="resource.drawMode.brushType = 'Pencil';changeBush()" ng-class="resource.drawMode.brushType == 'Pencil' ? 'active' : ''"><span><?php esc_html_e('Pencil','web-to-print-online-designer'); ?></span></li>
                                            <li ng-click="resource.drawMode.brushType = 'Circle';changeBush()" ng-class="resource.drawMode.brushType == 'Circle' ? 'active' : ''"><span><?php esc_html_e('Circle','web-to-print-online-designer'); ?></span></li>
                                            <li ng-click="resource.drawMode.brushType = 'Spray';changeBush()" ng-class="resource.drawMode.brushType == 'Spray' ? 'active' : ''"><span><?php esc_html_e('Spray','web-to-print-online-designer'); ?></span></li>
                                        </ul>
                                    </div>
                                </button>
                            </div>
                            <ul class="main-ranges" >
                                <li class="range range-brightness">
                                    <label><?php esc_html_e('Brush width ','web-to-print-online-designer'); ?></label>
                                    <div class="main-track">
                                        <input class="slide-input" type="range" step="1" min="1" max="100" ng-change="changeBush()" ng-model="resource.drawMode.brushWidth">
                                        <span class="range-track"></span>
                                    </div>
                                    <span class="value-display">{{resource.drawMode.brushWidth}}</span>
                                </li>
                            </ul>
                            <div class="color">
                                <h3 class="color-palette-label" ><?php esc_html_e('Brush color','web-to-print-online-designer'); ?></h3>
                                <ul class="main-color-palette nbd-perfect-scroll" >
                                    <li class="color-palette-add" ng-init="showBrushColorPicker = false" ng-click="showBrushColorPicker = !showBrushColorPicker;" ng-style="{'background-color': currentColor}"></li>
                                    <li ng-repeat="color in listAddedColor track by $index" ng-click="resource.drawMode.brushColor=color; changeBush()" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background-color': color}"></li>
                                </ul>
                                <div class="pinned-palette default-palette" >
                                    <h3 class="color-palette-label" ><?php esc_html_e('Default palette','web-to-print-online-designer'); ?></h3>
                                    <ul class="main-color-palette" ng-repeat="palette in resource.defaultPalette" >
                                        <li ng-class="{'first-left': $first, 'last-right': $last}" ng-repeat="color in palette track by $index" ng-click="resource.drawMode.brushColor=color; changeBush()" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
                                    </ul>
                                </div>
                                <div class="nbd-text-color-picker" id="nbd-bg-color-picker" ng-class="showBrushColorPicker ? 'active' : ''" >
                                    <spectrum-colorpicker
                                        ng-model="currentColor"
                                        options="{
                                                preferredFormat: 'hex',
                                                color: '#fff',
                                                flat: true,
                                                showButtons: false,
                                                showInput: true,
                                                containerClassName: 'nbd-sp'
                                        }">
                                    </spectrum-colorpicker>
                                    <div style="text-align: <?php echo (is_rtl()) ? 'right' : 'left'?>">
                                        <button class="nbd-button" ng-click="addColor();changeBush(currentColor);showBrushColorPicker = false;"><?php esc_html_e('Choose','web-to-print-online-designer'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="nbd-color-palette-inner" >
                                <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'" >
                                    <ul class="main-color-palette tab-scroll">
                                        <li class="color-palette-item color-palette-add" ng-click="stageBgColorPicker.status = !stageBgColorPicker.status;" ></li>
                                        <li ng-repeat="color in listAddedColor track by $index"
                                            ng-click="changeBackgroundCanvas(color)"
                                            class="color-palette-item"
                                            data-color="{{color}}"
                                            title="{{color}}"
                                            ng-style="{'background-color': color}">
                                        </li>
                                    </ul>
                                    <div class="nbd-text-color-picker" id="nbd-stage-bg-color-picker" ng-class="stageBgColorPicker.status ? 'active' : ''">
                                        <spectrum-colorpicker
                                            ng-model="stageBgColorPicker.currentColor"
                                            options="{
                                            preferredFormat: 'hex',
                                            color: '#fff',
                                            flat: true,
                                            showButtons: false,
                                            showInput: true,
                                            containerClassName: 'nbd-sp'
                                            }">
                                        </spectrum-colorpicker>
                                        <div>
                                            <button class="nbd-button"
                                                ng-click="changeBackgroundCanvas(stageBgColorPicker.currentColor);">
                                                    <?php esc_html_e('Choose', 'web-to-print-online-designer'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'">
                                    <h3 class="color-palette-label default" ><?php esc_html_e('Default palette', 'web-to-print-online-designer'); ?></h3>
                                    <ul class="main-color-palette tab-scroll" ng-repeat="palette in resource.defaultPalette">
                                        <li ng-class="{'first-left': $first, 'last-right': $last}"
                                            ng-repeat="color in palette track by $index"
                                            ng-click="changeBackgroundCanvas(color)"
                                            class="color-palette-item"
                                            data-color="{{color}}"
                                            title="{{color}}"
                                            ng-style="{'background': color}">
                                        </li>
                                    </ul>
                                </div>
                                <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'no'">
                                    <h3 class="color-palette-label"><?php esc_html_e('Color palette', 'web-to-print-online-designer'); ?></h3>
                                    <ul class="main-color-palette settings" >
                                        <li ng-repeat="color in __colorPalette track by $index" ng-class="{'first-left': $first, 'last-right': $last}" ng-click="changeBackgroundCanvas(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
                                    </ul>   
                                </div>
                                <div><button class="nbd-button" ng-click="removeBackgroundCanvas()"><?php esc_html_e('Remove background', 'web-to-print-online-designer'); ?></button></div>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-shapes" data-type="shapes" id="nbd-shape-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art)" ng-repeat="art in resource.shape.data" repeat-end="onEndRepeat('shape')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-icons" data-type="icons" id="nbd-icon-wrap">
                        <div class="cliparts-category" ng-class="resource.icon.cat.length > 0 ? '' : 'nbd-hiden'">
                            <div class="nbd-button nbd-dropdown nbd-cat-dropdown">
                                <span>{{resource.icon.filter.currentCat.name}}</span>
                                <i class="icon-nbd icon-nbd-chevron-right rotate90"></i>
                                <div class="nbd-sub-dropdown" data-pos="center">
                                    <ul class="nbd-perfect-scroll">
                                        <li ng-click="changeCat('icon', cat)" ng-repeat="cat in resource.icon.cat"><span>{{cat.name}}</span><span>{{cat.total}}</span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art, $index)" ng-repeat="art in resource.icon.data" repeat-end="onEndRepeat('icon')">
                                <div class="mansory-item__inner">
                                    <img ng-src="{{art.url}}" /><span class="photo-desc">{{art.name}}</span>
                                    <?php if(!$valid_license): ?>
                                    <span class="nbd-pro-mark-wrap" ng-if="$index > 20">
                                        <svg class="nbd-pro-mark" fill="#F3B600" xmlns="http://www.w3.org/2000/svg" viewBox="-505 380 12 10"><path d="M-503 388h8v1h-8zM-494 382.2c-.4 0-.8.3-.8.8 0 .1 0 .2.1.3l-2.3.7-1.5-2.2c.3-.2.5-.5.5-.8 0-.6-.4-1-1-1s-1 .4-1 1c0 .3.2.6.5.8l-1.5 2.2-2.3-.8c0-.1.1-.2.1-.3 0-.4-.3-.8-.8-.8s-.8.4-.8.8.3.8.8.8h.2l.8 3.3h8l.8-3.3h.2c.4 0 .8-.3.8-.8 0-.4-.4-.7-.8-.7z"></path></svg>
                                        <?php esc_html_e('Pro','web-to-print-online-designer'); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content-item type-flaticon" data-type="flaticon" id="nbd-flaticon-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addArt(art, true, true)" ng-repeat="art in resource.flaticon.data" repeat-end="onEndRepeat('flaticon')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-lines" data-type="lines" id="nbd-line-wrap">
                        <div class="mansory-wrap">
                            <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art)" ng-repeat="art in resource.line.data" repeat-end="onEndRepeat('line')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                        </div>
                    </div>
                    <div class="content-item type-qrcode" data-type="qr-code">
                        <div class="main-type">
                            <div class="main-input">
                                <input ng-model="resource.qrText" type="text" class="nbd-input input-qrcode" name="qr-code" placeholder="https://yourcompany.com">
                            </div>
                            <button ng-class="resource.qrText != '' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="addQrCode()"><?php esc_html_e('Create QRCode','web-to-print-online-designer'); ?></button>
                            <button ng-class="resource.qrText != '' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="addBarCode()"><?php esc_html_e('Create BarCode','web-to-print-online-designer'); ?></button>
                            <div class="main-qrcode">
                                
                            </div>
                            <svg id="barcode" ></svg>
                        </div>
                    </div>
                    <div ng-if="settings['nbdesigner_enable_vcard'] == 'yes'" class="content-item type-vcard" data-type="vcard">
                        <p><?php esc_html_e('Your information','web-to-print-online-designer'); ?></p>
                        <div ng-repeat="field in settings.vcard_fields" class="md-input-wrap">
                            <input id="vf-{{field.key}}" ng-model="field.value" ng-class="field.value.length > 0 ? 'holder' : ''"/>
                            <label for="vf-{{field.key}}" >{{field.name}}<label/>
                        </div>
                        <p>
                            <span class="nbd-button" ng-click="generateVcard()"><?php esc_html_e('Generate','web-to-print-online-designer'); ?></span>
                        </p>
                    </div>
                    <div class="content-item type-frame" data-type="frame">
                        <div class="frames-wrapper">
                            <div class="frame-wrap" ng-repeat="frame in resource.frames track by $index" ng-click="addFrame(frame)">
                                <photo-frame data-frame="frame"></photo-frame>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nbdesigner-gallery" id="nbdesigner-gallery">
                </div>
                <div class="loading-photo" >
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
            <div class="info-support">
                <span>Facebook</span>
                <i class="icon-nbd icon-nbd-clear close-result-loaded" ng-click="onClickTab('', 'element')"></i>
            </div>
        </div>
    </div>
</div>