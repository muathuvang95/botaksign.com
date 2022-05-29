<?php
function NB_register_widget()
{
    register_widget( 'NB_Artwork_Service_Widgets');
}
add_action( 'widgets_init', 'NB_register_widget' );

class NB_Artwork_Service_Widgets extends WP_Widget {
    function __construct() {
        $widget_options = array( 
            'classname' => 'nb-service-widgets',
            'description' => 'Select and show your services',
        );
        parent::__construct( 'nb-service-widgets', 'Services Widget', $widget_options );
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
        $title = $instance['title'];

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output
        if (array_key_exists('ids', $instance)) {
            $array_id = explode(',', $instance['ids']);
            $services = [];
            foreach ($array_id as $id) {
                $data = [];
                $service = wc_get_product((int) trim($id)); //convert string id to int id
                if (is_object($service)) {
                    $data = [
                        'id'            => $service->get_id(),
                        'title'         => $service->get_title(),
                        'price'         => $service->get_price() ? $service->get_price() : 0,
                        'image'         => $service->get_image(),
                        'description'   => $service->get_short_description() != "" ? $service->get_short_description() : $service->get_description(),
                    ];
                }

                if (count($data)) {
                    $services[] = $data;
                }
            }
            foreach ($services as $s) {
                ?>
                <div class="row service-block">
                    <div class="col image-block">
                        <?php echo $s['image']; ?>
                    </div>
                    <div class="col content-block">
                        <h4 class="title">
                            <?php echo $s['title']; ?> &#8250;
                        </h4>
                        <p class="description">
                            <?php echo $s['description']; ?>
                        </p>
                    </div>
                    <div class="col price-block">
                        <h4 class="title">
                            From
                        </h4>
                        <p class="price">
                            <?php echo $s['price']; ?>
                        </p>
                    </div>
                </div>
                <?php
            }
        }
        echo $args['after_widget'];
    }

    // Widget Backend 
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpb_widget_domain');
        }
        if (isset($instance['ids'])) {
            $ids = $instance['ids'];
        } else {
            $ids = __('', 'wpb_widget_domain');
        }
        // Widget admin form
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
            <input class="widefat" id="<?php // echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('ids'); ?>"><?php _e('Services ID:'); ?></label> 
            <input class="widefat" id="<?php // echo $this->get_field_id('ids'); ?>" name="<?php echo $this->get_field_name('ids'); ?>" type="text" value="<?php echo esc_attr($ids); ?>" placeholder="Ex: 123, 456"/>
            <span>Enter services ID, separated by commas. Ex: 251, 365</span>
        </p>
    <?php
    }
}