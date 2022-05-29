<?php
function NB_register_widget()
{
    register_widget( 'Boatk_Working_Time_Widget');
}
add_action( 'widgets_init', 'NB_register_widget' );

class Boatk_Working_Time_Widget extends WP_Widget {
    function __construct() {
        $widget_options = array( 
            'classname' => 'nb-working-time-widget',
            'description' => 'Show working time of your store',
        );
        parent::__construct( 'nb-working-time-widget', 'Working Time Widget', $widget_options );
    }
    
    /**
     * Outputs the HTML for this widget.
     *
     * @param array $args An array of standard parameters for widgets in this theme
     * @param array $instance An array of settings for this widget instance
     *
     * @return void Echoes it's output
     */
    public function widget($args, $instance) {
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $working_days = [];
        $closed_days = [];
        $break_time = [];
        $break_time_tmp = [];
        $time_open = "00:00";
        $time_close = "00:00";
        $working_time_setting = get_option('working-time-options', false);
        if (isset($working_time_setting['working-days'])) {
            foreach ($days as $day) {
                if (!array_key_exists($day, $working_time_setting['working-days'])) {
                    $closed_days[] = $day;
                }
                if ($working_time_setting[$day]["open-time"] === $time_open && $working_time_setting[$day]["close-time"] === $time_close) {
                    $break_time_tmp[] = $day;
                } else {
                    if (count($break_time_tmp)) {
                        $break_time[] = [
                            "text" => (isset($time_open) ? date("g:i a", strtotime($time_open)) : date("g:i a", strtotime('00:00'))) . ' - ' . (isset($time_close) ? date("g:i a", strtotime($time_close)) : date("g:i a", strtotime('00:00'))),
                            "days" => $break_time_tmp
                        ];
                    }
                    $break_time_tmp = [$day];
                    $time_open = $working_time_setting[$day]["open-time"];
                    $time_close = $working_time_setting[$day]["close-time"];
                }
            }
            ?>
                <p>
                    <?php foreach ($break_time as $time): ?>
                        <?php if (count($time['days']) > 1): ?>
                            <?= $time['days'][0]; ?> - <?= $time['days'][count($time['days']) - 1]?>: <?= $time['text']; ?>
                        <?php else: ?>
                            <?= $time['days'][0]; ?>: <?= $time['text']; ?>
                        <?php endif; ?>
                        <br/>
                    <?php endforeach; ?>
                    <?php if (count($closed_days)): ?>
                        Closed on <?= implode(', ', $closed_days); ?> & Public Holidays
                    <?php endif; ?>
                </p>
            <?php
        }

        echo $args['after_widget'];
    }

    // Widget Backend 
    public function form($instance) {
    ?>
        <p>
            <label><b>Working Time Widget</b></label> 
        </p>
    <?php
    }
}