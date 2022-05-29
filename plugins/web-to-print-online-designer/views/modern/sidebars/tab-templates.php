<div class="tab tab-first <?php if( $active_template ) echo 'active'; ?>" id="tab-product-template"  
    <?php if( !( isset( $template_data ) && isset( $template_data['template_tags'] ) ) ): ?>
     nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-product-template" data-type="globalTemplate" data-offset="30"
    <?php endif; ?>
>
<!--    <div class="nbd-search" ng-if="settings.ui_mode != '1'">
        <input type="search" name="search" placeholder="search"/>
        <i class="icon-nbd icon-nbd-fomat-search"></i>
    </div>-->
    <div class="tab-template show" id="tab-template">
        <!--            <i class="icon-nbd icon-nbd-fomat-highlight-off close-template"></i>-->
        <div class="tab-main tab-scroll">
            <?php if( isset( $template_data ) && isset( $template_data['template_tags'] ) ) include 'custom-templates-section.php'; ?>
            <div class="nbd-templates">
                <div class="main-items">
                    <div class="items" >
                        <?php if( !( isset( $template_data ) && isset( $template_data['template_tags'] ) ) && $task != 'create_template' ): ?>
                        <div ng-style="{'display': settings.task == 'create_template' ? 'none' : 'inline-block' }" class="item" ng-repeat="temp in resource.templates" ng-click="insertTemplate(false, temp)">
                            <div class="main-item">
                                <div class="item-img" nbd-template-hover="{{temp.id}}">
                                    <img ng-src="{{temp.thumbnail}}" alt="<?php esc_html_e('Template','web-to-print-online-designer'); ?>">
                                </div>
                            </div>
                        </div>
                        <hr ng-hide="resource.templates.length == 0" class="seperate2" />
                        <div class="item" ng-repeat="temp in resource.globalTemplate.data" ng-click="insertGlobalTemplate(temp.id, $index)">
                            <div class="main-item" image-on-load="temp.thumbnail">
                                <div class="item-img item-img-global-tem" >
                                    <img ng-src="{{temp.thumbnail}}" alt="{{temp.name}}">
                                    <?php if(!$valid_license): ?>
                                    <span class="nbd-pro-mark-wrap" ng-if="$index > 4">
                                        <svg class="nbd-pro-mark" fill="#F3B600" xmlns="http://www.w3.org/2000/svg" viewBox="-505 380 12 10"><path d="M-503 388h8v1h-8zM-494 382.2c-.4 0-.8.3-.8.8 0 .1 0 .2.1.3l-2.3.7-1.5-2.2c.3-.2.5-.5.5-.8 0-.6-.4-1-1-1s-1 .4-1 1c0 .3.2.6.5.8l-1.5 2.2-2.3-.8c0-.1.1-.2.1-.3 0-.4-.3-.8-.8-.8s-.8.4-.8.8.3.8.8.8h.2l.8 3.3h8l.8-3.3h.2c.4 0 .8-.3.8-.8 0-.4-.4-.7-.8-.7z"></path></svg>
                                        <?php esc_html_e('Pro','web-to-print-online-designer'); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>  
                        <?php endif; ?>
                        <?php if( $task == 'create_template' ): ?>
                        <div class="create-global-template-wrap">
                            <div>
                                <input class="line-width" placeholder="Width" ng-model="lineConfig.width" />
                                <input class="line-dash1" placeholder="Dash" ng-model="lineConfig.dash1" />
                                <input class="line-dash2" placeholder="Dash" ng-model="lineConfig.dash2" />
                                <input class="line-color" placeholder="Stroke color" ng-model="lineConfig.color" />
                                <button class="nbd-button nbd-no-margin-left" ng-click="addLine()">Add Line</button>
                            </div>
                            <hr class="seperate"/>
                            <div>
                                <button class="nbd-button nbd-no-margin-left" ng-click="uploadSvgFile()">Upload SVG</button>
                                <button class="nbd-button nbd-no-margin-left" ng-click="addText()">Add Text</button>
                                <button class="nbd-button nbd-no-margin-left" ng-click="addShape('rect')">Rectangle</button>
                                <button class="nbd-button nbd-no-margin-left" ng-click="addShape('triangle')">Triangle</button>
                                <button class="nbd-button nbd-no-margin-left" ng-click="addShape('circle')">Circle</button>
                            </div>
                            <hr />
                            <div>
                                <input placeholder="Width" ng-model="templateSize.width" ng-keyup="$event.keyCode == 13 && changeTemplateDimension()" class="tem-dimesion" />
                                <input placeholder="Height" ng-model="templateSize.height" ng-keyup="$event.keyCode == 13 && changeTemplateDimension()" class="tem-dimesion" />
                                <button class="nbd-button nbd-no-margin-left" ng-click="changeTemplateDimension()">Apply</button>
                            </div>
                            <hr />
                            <div>
                                <button class="nbd-button nbd-no-margin-left" ng-click="_loadTemplateCat()">Load templates</button>
                                <select ng-change="changeGlobalTemplate()" ng-show="templateCats.length > 0" class="process-select select-global-tem-cat" ng-model="templateCat" id="category_template">
                                    <option ng-repeat="cat in templateCats" ng-value="{{cat.id}}"><span>{{cat.name}}</span></option>
                                </select>
                            </div>
                        </div>
                        <div class="item" ng-repeat="temp in resource.globalTemplate.data" ng-click="insertGlobalTemplate(temp.id, $index)">
                            <div class="main-item" image-on-load="temp.thumbnail">
                                <div class="item-img item-img-global-tem" >
                                    <img ng-src="{{temp.thumbnail}}" alt="{{temp.name}}">
                                    <?php if(!$valid_license): ?>
                                    <span class="nbd-pro-mark-wrap" ng-if="$index > 4">
                                        <svg class="nbd-pro-mark" fill="#F3B600" xmlns="http://www.w3.org/2000/svg" viewBox="-505 380 12 10"><path d="M-503 388h8v1h-8zM-494 382.2c-.4 0-.8.3-.8.8 0 .1 0 .2.1.3l-2.3.7-1.5-2.2c.3-.2.5-.5.5-.8 0-.6-.4-1-1-1s-1 .4-1 1c0 .3.2.6.5.8l-1.5 2.2-2.3-.8c0-.1.1-.2.1-.3 0-.4-.3-.8-.8-.8s-.8.4-.8.8.3.8.8.8h.2l.8 3.3h8l.8-3.3h.2c.4 0 .8-.3.8-.8 0-.4-.4-.7-.8-.7z"></path></svg>
                                        <?php esc_html_e('Pro','web-to-print-online-designer'); ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>    
                        <?php endif; ?>
                    </div>
                    <div class="pointer"></div>
                </div>
                <div class="loading-photo" >
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="nbd-tooltip_templates">
    <div id="tooltip_content_{{temp.id}}" ng-repeat="temp in resource.templates" class="nbd-perfect-scroll nbd-tooltip-template" nbd-perfect-scroll>
        <div class="nbd-tooltip_template-inner">
            <div class="nbd-img-container text-center {{$last ? 'nbd-img-last' : ''}}" ng-if="stages[$index]" ng-repeat="(iIndex, img) in temp.src" ng-click="insertPartTemplate(temp.id, iIndex)">
                <img ng-src="{{img}}" alt="" >
                <span >{{stages[$index].config.name}}</span>
            </div>
        </div>
    </div>
</div>