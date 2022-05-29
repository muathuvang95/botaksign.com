<?php
/*WordPress Menus API.*/
function add_new_menu_items()
{
    //add a new menu item. This is a top level menu item i.e., this menu item can have sub menus
    add_submenu_page(
        "options-general.php",
        "Working Time",
        "Working Time",
        "manage_options",
        "working-time-options",
        "working_time_options_page"
    );
}

function working_time_options_page()
{
    $working_time_options = get_option('working-time-options', true);
    ?>  
        <style type="text/css">
            #add-new-holiday {
                background: #007305;
                border-color: #007305;
            }
            .button-holiday {
                outline: none!important;
                border-radius: 10%;
                padding: 0 10px;
                margin: 0 10px 0 0;
                color: #fff;
                text-decoration: none;
                text-shadow: none;
                border: none;
                font-size: 13px;
                line-height: 2.15384615;
                min-height: 30px;
                margin: 0;
                padding: 0 10px;
                cursor: pointer;
                border-width: 1px;
                border-style: solid;
                -webkit-appearance: none;
                border-radius: 3px;
                white-space: nowrap;
                box-sizing: border-box;
                display: inline-block;
            }
        </style>
        <script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/angular-1.6.9.min.js'; ?>"></script>
        <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <h1>Working Time</h1>
        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_style('jquery-ui');
                ?>
                <table style="width: 100%;">
                    <tbody>
                        <tr>
                            <td style="width: 50%; padding-right: 10px;">
                                <div class="working-time-title">Production Days</div>
                                <?php foreach ($days as $day): ?>
                                    <div class="working-time-container">
                                        <div class="working-time-checkbox">
                                            <input name="<?php echo 'working-time-options[working-days]' . '[' . $day . ']'; ?>" type="checkbox" value="<?= $day ?>" <?php echo array_key_exists($day, $working_time_options['working-days']) ? 'checked' : '' ?> />
                                            <label for="<?php echo 'working-time-options[working-days]' . '[' . $day . ']'; ?>"><?= $day ?></label> 
                                        </div>
                                        <div class="time-setup">
                                            <div class="open-time">
                                                <label for="<?php echo 'working-time-options[' . $day . ']' . '[open-time]'; ?>">Open time</label> 
                                                <input name="<?php echo 'working-time-options[' . $day . ']' . '[open-time]'; ?>" type="time" value="<?php echo isset($working_time_options[$day]['open-time']) ? $working_time_options[$day]['open-time'] : '' ?>"/>
                                            </div>
                                            <div class="close-time">
                                                <label for="<?php echo 'working-time-options[' . $day . ']' . '[close-time]'; ?>">Close time</label> 
                                                <input name="<?php echo 'working-time-options[' . $day . ']' . '[close-time]'; ?>" type="time" value="<?php echo isset($working_time_options[$day]['close-time']) ? $working_time_options[$day]['close-time'] : '' ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <td style="width: 50%; padding-right: 10px;">
                                <div class="working-time-title">Collection Days</div>
                                <?php foreach ($days as $day): ?>
                                    <div class="working-time-container">
                                        <div class="working-time-checkbox">
                                            <input name="<?php echo 'working-time-options[collection-days]' . '[' . $day . ']'; ?>" type="checkbox" value="<?= $day ?>" <?php echo array_key_exists($day, $working_time_options['collection-days']) ? 'checked' : '' ?> />
                                            <label for="<?php echo 'working-time-options[collection-days]' . '[' . $day . ']'; ?>"><?= $day ?></label> 
                                        </div>
                                        <div class="time-setup">
                                            <div class="open-time">
                                                <label for="<?php echo 'working-time-options[' . $day . ']' . '[col-open-time]'; ?>">Open time</label> 
                                                <input name="<?php echo 'working-time-options[' . $day . ']' . '[col-open-time]'; ?>" type="time" value="<?php echo isset($working_time_options[$day]['col-open-time']) ? $working_time_options[$day]['col-open-time'] : '' ?>"/>
                                            </div>
                                            <div class="close-time">
                                                <label for="<?php echo 'working-time-options[' . $day . ']' . '[col-close-time]'; ?>">Close time</label> 
                                                <input name="<?php echo 'working-time-options[' . $day . ']' . '[col-close-time]'; ?>" type="time" value="<?php echo isset($working_time_options[$day]['col-close-time']) ? $working_time_options[$day]['col-close-time'] : '' ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                    <div class="public-holiday" id="public-holiday">
                        <div class="working-time-title">Select Public Holidays</div><span><input class="button-holiday" type="button" id="add-new-holiday" value="Add New"></span>
                        <div class="holiday-container">
                            <table style="width: 100%;" id="table-holiday">
                                <tbody>
                                    <?php 
                                    if(isset($working_time_options["holidays"])) {
                                        foreach ($working_time_options["holidays"]['start-holiday'] as $key => $value): ?>
                                            <tr class="row-holiday">
                                                <td style="width: 40%;">
                                                    <div class="title-holiday">
                                                        <label><b><h3>Holiday</h3></b></label>
                                                    </div>
                                                </td>
                                                <td style="width: 30%;"><div class="start-holiday">
                                                        <label><b>Start of holiday:</b></label>
                                                        <br/>
                                                        <input type="date" name="<?= 'working-time-options[holidays][start-holiday][]'; ?>" value="<?php echo isset($working_time_options[$day]['close-time']) ? $value : '' ?>" />
                                                    </div>
                                                </td>
                                                <td style="width: 30%;">
                                                    <div class="end-holiday">
                                                        <label><b>End of holiday:</b></label>
                                                        <br/>
                                                        <input type="date" name="<?= 'working-time-options[holidays][end-holiday][]'; ?>" value="<?php echo isset($working_time_options[$day]['close-time']) ? $working_time_options["holidays"]['end-holiday'][$key] : '' ?>" />
                                                    </div>
                                                </td>
                                                <td style="width: 10%;">
                                                    <div class="delete-row delete-holiday"  onclick="delete_holiday(this)" style="cursor: pointer;">
                                                        <svg width="24.000000000000004" height="24.000000000000004" xmlns="http://www.w3.org/2000/svg">
                                                            <g>
                                                                <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/>
                                                            </g>
                                                            <g>
                                                                <title>Layer 1</title>
                                                                <path id="svg_1" d="m22.145665,3.683883c0.313,0.313 0.313,0.826 0,1.139l-6.276,6.27c-0.313,0.312 -0.313,0.826 0,1.14l6.273,6.272c0.313,0.313 0.313,0.826 0,1.14l-2.285,2.277c-0.314,0.312 -0.828,0.312 -1.142,0l-6.271,-6.271c-0.313,-0.313 -0.828,-0.313 -1.141,0l-6.276,6.267c-0.313,0.313 -0.828,0.313 -1.141,0l-2.282,-2.28c-0.313,-0.313 -0.313,-0.826 0,-1.14l6.278,-6.269c0.313,-0.312 0.313,-0.826 0,-1.14l-6.273,-6.273c-0.314,-0.313 -0.314,-0.827 0,-1.14l2.284,-2.278c0.315,-0.312 0.828,-0.312 1.142,0.001l6.27,6.27c0.314,0.314 0.828,0.314 1.141,0.001l6.276,-6.267c0.312,-0.312 0.826,-0.312 1.141,0l2.282,2.281z"/>
                                                            </g>
                                                        </svg>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php 
                                        endforeach;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="save_working_time_options">
                <?php
                // Add the submit button to serialize the options
                submit_button();

            ?>         
        </form>
    </div>
    <script type="text/javascript">
        function delete_holiday(e) {
            //var n_row = jQuery('#public-holiday .row-holiday').length;
            jQuery(e).parents('.row-holiday').remove();
        }
        jQuery(document).ready(function($) {
            $('#add-new-holiday').on('click' , function() {
                $('#table-holiday > tbody:last-child').append('<tr class="row-holiday"><td style="width: 40%;"><div class="title-holiday"><label><b><h3>Holiday</h3></b></label></div></td><td style="width: 30%;"><div class="start-holiday"><label><b>Start of holiday:</b></label><br/><input type="date" name="working-time-options[holidays][start-holiday][]" value="" /></div></td><td style="width: 30%;"><div class="end-holiday"><label><b>End of holiday:</b></label><br/><input type="date" name="working-time-options[holidays][end-holiday][]" value="" /></div></td><td style="width: 10%;"><div class="delete-row delete-holiday" onclick="delete_holiday(this)" style="cursor: pointer;"><svg width="24.000000000000004" height="24.000000000000004" xmlns="http://www.w3.org/2000/svg"><g><rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"/></g><g><path id="svg_1" d="m22.145665,3.683883c0.313,0.313 0.313,0.826 0,1.139l-6.276,6.27c-0.313,0.312 -0.313,0.826 0,1.14l6.273,6.272c0.313,0.313 0.313,0.826 0,1.14l-2.285,2.277c-0.314,0.312 -0.828,0.312 -1.142,0l-6.271,-6.271c-0.313,-0.313 -0.828,-0.313 -1.141,0l-6.276,6.267c-0.313,0.313 -0.828,0.313 -1.141,0l-2.282,-2.28c-0.313,-0.313 -0.313,-0.826 0,-1.14l6.278,-6.269c0.313,-0.312 0.313,-0.826 0,-1.14l-6.273,-6.273c-0.314,-0.313 -0.314,-0.827 0,-1.14l2.284,-2.278c0.315,-0.312 0.828,-0.312 1.142,0.001l6.27,6.27c0.314,0.314 0.828,0.314 1.141,0.001l6.276,-6.267c0.312,-0.312 0.826,-0.312 1.141,0l2.282,2.281z"/></g></svg></div></td></tr>');
            });
        })
    </script>
    <?php
}

//this action callback is triggered when wordpress is ready to add new items to menu.
add_action("admin_menu", "add_new_menu_items");
add_action( 'admin_post_save_working_time_options', 'save_working_time_options' );   
add_action( 'admin_post_nopriv_save_working_time_options', 'save_working_time_options' ); 

function save_working_time_options() {
    if (isset($_POST['working-time-options'])) {
        update_option('working-time-options', $_POST['working-time-options']);
    }
    wp_redirect(
        add_query_arg(array(
            'page'      => 'working-time-options',
        ), admin_url('options-general.php'))
    );
}