<?php
/**
 * Plugin Name: Custom Botaksign
 * Plugin URI: https://cmsmart.net
 * Description: An plugin custom for WordPress.
 * Version: 7.23.20
 * Author: cmsmart.net
 * Author URI: https://cmsmart.net
 * WC requires at least: 3.0
 * WC tested up to: 3.7.0
 * License: GPL2
 * TextDomain: custom-botaksign
 */
if (!function_exists('write_log')) {

    function write_log($log)
    {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}

function minh_log($message, $close = false)
{

    static $fh = 0;

    if ($close) {
        @fclose($fh);
    } else {
        // If file doesn't exist, create it
        if (!$fh) {
            $pathinfo = pathinfo(__FILE__);
            $dir = str_replace('/classes', '/logs', $pathinfo['dirname']);
            $fh = @fopen($dir . '/lognow.log', 'a+');
        }

        // If file was successfully created
        if ($fh) {
            if (is_array($message) || is_object($message)) {
                $line = print_r($message, true) . "\n";
            } else {
                $line = $message . "\n";
            }

            fwrite($fh, $line);
        }
    }
}

define('CUSTOM_BOTAKSIGN_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_BOTAKSIGN_URL', plugin_dir_url(__FILE__));
require_once plugin_dir_path(__FILE__) . 'email-cart.php';
require_once plugin_dir_path(__FILE__) . 'invoice-template.php';
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . 'includes/service.php';
require_once plugin_dir_path(__FILE__) . 'includes/payment_paypal.php';
require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/service-elements.php';
require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/block-category-elements.php';
require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/block-category-product-elements.php';
require_once plugin_dir_path(__FILE__) . 'includes/vc-elements/block-custom-product-elements.php';
require_once plugin_dir_path(__FILE__) . 'includes/working-time-setting.php';
require_once plugin_dir_path(__FILE__) . 'includes/widgets/working-time-widgets.php';
require_once plugin_dir_path(__FILE__) . 'includes/easy-registration-forms/erforms.php';
require_once plugin_dir_path(__FILE__) . 'includes/bucket-browser-for-aws-s3/bucket-browser-for-aws-s3.php';
require_once plugin_dir_path(__FILE__) . 'includes/filebird/filebird.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-order-rest-api/functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/custom-order-rest-api/class-wc-rest-custom-order-controller.php';
require plugin_dir_path( __DIR__ ) . 'nb-offload-media/vendor/autoload.php';    //custom botak s3: require autoload S3
add_action('wp_ajax_create_pages', 'create_default_pages', 2);

add_action('admin_enqueue_scripts', 'botak_admin_enqueue_scripts');
function botak_admin_enqueue_scripts()
{
    if (is_admin()) {
        wp_enqueue_style('botak_admin_styles', CUSTOM_BOTAKSIGN_URL . '/assets/css/botak-admin.css');
    }
}

function create_default_pages()
{
    if (!isset($_POST['action']) || $_POST['action'] !== 'create_pages') {
        return wp_send_json_error(__('You don\'t have enough permission', 'dokan', '403'));
    }

    if (!current_user_can('manage_woocommerce')) {
        return wp_send_json_error(__('You don\'t have enough permission', 'dokan', '403'));
    }

    $page_created = get_option('dokan_pages_created', false);
    $pages = array(
        array(
            'post_title' => __('Dashboard', 'dokan'),
            'slug' => 'dashboard',
            'page_id' => 'dashboard',
            'content' => '[dokan-dashboard]',
        ),
        array(
            'post_title' => __('Store List', 'dokan'),
            'slug' => 'store-listing',
            'page_id' => 'store_listing',
            'content' => '[dokan-stores]',
        ),
        array(
            'post_title' => __('My Orders', 'dokan-lite'),
            'slug' => 'my-orders',
            'page_id' => 'my_orders',
            'content' => '[dokan-my-orders]',
        ),
        array(
            'post_title' => __('Reseller', 'dokan-lite'),
            'slug' => 'reseller',
            'page_id' => 'dokan_reseller',
            'content' => '[vc_row full_width="stretch_row_content_no_spaces"][vc_column][vc_raw_html]JTNDZGl2JTIwY2xhc3MlM0QlMjJwYWdlLWNvdmVyLWhlYWRlciUyMiUyMHN0eWxlJTNEJTIyYmFja2dyb3VuZC1pbWFnZSUzQSUyMHVybCUyOGh0dHBzJTNBJTJGJTJGZGVtbzIuY21zbWFydC5uZXQlMkZwcmludGNhcnRfdGYlMkZwcmludGNhcnQtdG90ZS1iYWdzJTJGd3AtY29udGVudCUyRnVwbG9hZHMlMkYyMDE4JTJGMTAlMkZhYm91dC11cy5qcGclMjklM0IlMjBoZWlnaHQlM0ElMjAzMDBweCUzQiUyMiUzRSUwQSUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUyMCUzQ2RpdiUyMGNsYXNzJTNEJTIycGFnZS1jb3Zlci13cmFwJTIyJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTNDZGl2JTIwY2xhc3MlM0QlMjJwYWdlLWNvdmVyLWJsb2NrJTIyJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTNDaDElM0VSZXNlbGxlciUzQyUyRmgxJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTNDbmF2JTIwY2xhc3MlM0QlMjJ3b29jb21tZXJjZS1icmVhZGNydW1iJTIyJTIwaXRlbXByb3AlM0QlMjJicmVhZGNydW1iJTIyJTNFJTNDYSUyMGhyZWYlM0QlMjJodHRwcyUzQSUyRiUyRmJvdGFrc2lnbi5jbXNtYXJ0Lm5ldCUyMiUzRUhvbWUlM0MlMkZhJTNFJTNDc3BhbiUzRSUyRiUzQyUyRnNwYW4lM0VHdWlkZXMlM0NzcGFuJTNFJTJGJTNDJTJGc3BhbiUzRVJlc2VsbGVyJTNDJTJGbmF2JTNFJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTNDJTJGZGl2JTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTNDJTJGZGl2JTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTIwJTNDJTJGZGl2JTNF[/vc_raw_html][/vc_column][/vc_row][vc_row][vc_column][vc_custom_heading text="Infographic" font_container="tag:h2|font_size:30px|text_align:center|color:%23333333|line_height:1.3" google_fonts="font_family:Nunito%3A300%2Cregular%2C700|font_style:400%20regular%3A400%3Anormal" el_class="nb-heading-underline" css=".vc_custom_1582619792454{margin-bottom: 50px !important;}"][vc_row_inner][vc_column_inner width="1/3"][vc_single_image image="2599" img_size="370x350"][/vc_column_inner][vc_column_inner width="2/3"][vc_raw_html]JTNDaDMlMjBjbGFzcyUzRCUyMm5iLXRpdGxlJTIyJTNFV2hhdCUyMGlzJTIwTG9yZW0lMjBJcHN1bSUzRiUzQyUyRmgzJTNFJTBBJTNDcCUzRUxvcmVtJTIwSXBzdW0lMjBpcyUyMHNpbXBseSUyMGR1bW15JTIwdGV4dCUyMG9mJTIwdGhlJTIwcHJpbnRpbmclMjBhbmQlMjB0eXBlc2V0dGluZyUyMGluZHVzdHJ5LiUyMExvcmVtJTIwSXBzdW0lMjBoYXMlMjBiZWVuJTIwdGhlJTIwaW5kdXN0cnklMjdzJTIwc3RhbmRhcmQlMjBkdW1teSUyMHRleHQlMjBldmVyJTIwc2luY2UlMjB0aGUlMjAxNTAwcyUyQyUyMHdoZW4lMjBhbiUyMHVua25vd24lMjBwcmludGVyJTIwdG9vayUyMGElMjBnYWxsZXklMjBvZiUyMHR5cGUlMjBhbmQlMjBzY3JhbWJsZWQlMjBpdCUyMHRvJTIwbWFrZSUyMGElMjB0eXBlJTIwc3BlY2ltZW4lMjBib29rLiUyMEl0JTIwaGFzJTIwc3Vydml2ZWQlMjBub3QlMjBvbmx5JTIwZml2ZSUyMGNlbnR1cmllcyUyQyUyMGJ1dCUyMGFsc28lMjB0aGUlMjBsZWFwJTIwaW50byUyMGVsZWN0cm9uaWMlMjB0eXBlc2V0dGluZyUyQyUyMHJlbWFpbmluZyUyMGVzc2VudGlhbGx5JTIwdW5jaGFuZ2VkLiUyMEl0JTIwd2FzJTIwcG9wdWxhcmlzZWQlMjBpbiUyMHRoZSUyMDE5NjBzJTIwd2l0aCUyMHRoZSUyMHJlbGVhc2UlMjBvZiUyMExldHJhc2V0JTIwc2hlZXRzJTIwY29udGFpbmluZyUyMExvcmVtJTIwSXBzdW0lMjBwYXNzYWdlcyUyQyUyMGFuZCUyMG1vcmUlMjByZWNlbnRseSUyMHdpdGglMjBkZXNrdG9wJTIwcHVibGlzaGluZyUyMHNvZnR3YXJlJTIwbGlrZSUyMEFsZHVzJTIwUGFnZU1ha2VyJTIwaW5jbHVkaW5nJTIwdmVyc2lvbnMlMjBvZiUyMExvcmVtJTIwSXBzdW0uJTBBJTNDJTJGcCUzRSUwQSUzQ2JyJTNFJTBBJTNDaDMlMjBjbGFzcyUzRCUyMm5iLXRpdGxlJTIyJTNFV2h5JTIwZG8lMjB3ZSUyMHVzZSUyMGl0JTNGJTNDJTJGaDMlM0UlMEElM0NwJTNFSXQlMjBpcyUyMGElMjBsb25nJTIwZXN0YWJsaXNoZWQlMjBmYWN0JTIwdGhhdCUyMGElMjByZWFkZXIlMjB3aWxsJTIwYmUlMjBkaXN0cmFjdGVkJTIwYnklMjB0aGUlMjByZWFkYWJsZSUyMGNvbnRlbnQlMjBvZiUyMGElMjBwYWdlJTIwd2hlbiUyMGxvb2tpbmclMjBhdCUyMGl0cyUyMGxheW91dC4lMjBUaGUlMjBwb2ludCUyMG9mJTIwdXNpbmclMjBMb3JlbSUyMElwc3VtJTIwaXMlMjB0aGF0JTIwaXQlMjBoYXMlMjBhJTIwbW9yZS1vci1sZXNzJTIwbm9ybWFsJTIwZGlzdHJpYnV0aW9uJTIwb2YlMjBsZXR0ZXJzJTJDJTIwYXMlMjBvcHBvc2VkJTIwdG8lMjB1c2luZyUyMCUyN0NvbnRlbnQlMjBoZXJlJTJDJTIwY29udGVudCUyMGhlcmUlMjclMkMlMjBtYWtpbmclMjBpdCUyMGxvb2slMjBsaWtlJTIwcmVhZGFibGUlMjBFbmdsaXNoLiUyME1hbnklMjBkZXNrdG9wJTIwcHVibGlzaGluZyUyMHBhY2thZ2VzJTIwYW5kJTIwd2ViJTIwcGFnZSUyMGVkaXRvcnMlMjBub3clMjB1c2UlMjBMb3JlbSUyMElwc3VtJTIwYXMlMjB0aGVpciUyMGRlZmF1bHQlMjBtb2RlbCUyMHRleHQlMkMlMjAlMjBMb3JlbSUyMElwc3VtJTIwcGFzc2FnZXMlMkMlMjBhbmQlMjBtb3JlJTIwcmVjZW50bHklMjB3aXRoJTIwZGVza3RvcCUyMHB1Ymxpc2hpbmclMjBzb2Z0d2FyZSUyMGxpa2UlMjBBbGR1cyUyMFBhZ2VNYWtlciUyMGluY2x1ZGluZyUyMHZlcnNpb25zJTIwb2YlMjBMb3JlbSUyMElwc3VtLiUwQSUzQyUyRnAlM0U=[/vc_raw_html][/vc_column_inner][/vc_row_inner][vc_row_inner][vc_column_inner][vc_raw_html]JTNDaDMlMjBjbGFzcyUzRCUyMm5iLXRpdGxlJTIyJTNFV2hlcmUlMjBkb2VzJTIwaXQlMjBjb21lJTIwZnJvbSUzRiUzQyUyRmgzJTNFJTBBJTNDcCUzRUNvbnRyYXJ5JTIwdG8lMjBwb3B1bGFyJTIwYmVsaWVmJTJDJTIwTG9yZW0lMjBJcHN1bSUyMGlzJTIwbm90JTIwc2ltcGx5JTIwcmFuZG9tJTIwdGV4dC4lMjBJdCUyMGhhcyUyMHJvb3RzJTIwaW4lMjBhJTIwcGllY2UlMjBvZiUyMGNsYXNzaWNhbCUyMExhdGluJTIwbGl0ZXJhdHVyZSUyMGZyb20lMjA0NSUyMEJDJTJDJTIwbWFraW5nJTIwaXQlMjBvdmVyJTIwMjAwMCUyMHllYXJzJTIwb2xkLiUyMFJpY2hhcmQlMjBNY0NsaW50b2NrJTJDJTIwYSUyMExhdGluJTIwcHJvZmVzc29yJTIwYXQlMjBIYW1wZGVuLVN5ZG5leSUyMENvbGxlZ2UlMjBpbiUyMFZpcmdpbmlhJTJDJTIwbG9va2VkJTIwdXAlMjBvbmUlMjBvZiUyMHRoZSUyMG1vcmUlMjBvYnNjdXJlJTIwTGF0aW4lMjB3b3JkcyUyQyUyMGNvbnNlY3RldHVyJTJDJTIwZnJvbSUyMGElMjBMb3JlbSUyMElwc3VtJTIwcGFzc2FnZSUyQyUyMGFuZCUyMGdvaW5nJTIwdGhyb3VnaCUyMHRoZSUyMGNpdGVzJTIwb2YlMjB0aGUlMjB3b3JkJTIwaW4lMjBjbGFzc2ljYWwlMjBsaXRlcmF0dXJlJTJDJTIwZGlzY292ZXJlZCUyMHRoZSUyMHVuZG91YnRhYmxlJTIwc291cmNlLiUyMExvcmVtJTIwSXBzdW0lMjBjb21lcyUyMGZyb20lMjBzZWN0aW9ucyUyMDEuMTAuMzIlMjBhbmQlMjAxLjEwLjMzJTIwb2YlMjAlMjJkZSUyMEZpbmlidXMlMjBCb25vcnVtJTIwZXQlMjBNYWxvcnVtJTIyJTIwJTI4VGhlJTIwRXh0cmVtZXMlMjBvZiUyMEdvb2QlMjBhbmQlMjBFdmlsJTI5JTIwYnklMjBDaWNlcm8lMkMlMjB3cml0dGVuJTIwaW4lMjA0NSUyMEJDLiUyMFRoaXMlMjBib29rJTIwaXMlMjBhJTIwdHJlYXRpc2UlMjBvbiUyMHRoZSUyMHRoZW9yeSUyMG9mJTIwZXRoaWNzJTJDJTIwdmVyeSUyMHBvcHVsYXIlMjBkdXJpbmclMjB0aGUlMjBSZW5haXNzYW5jZS4lMjBUaGUlMjBmaXJzdCUyMGxpbmUlMjBvZiUyMExvcmVtJTIwSXBzdW0lMkMlMjAlMjJMb3JlbSUyMGlwc3VtJTIwZG9sb3IlMjBzaXQlMjBhbWV0Li4lMjIlMkMlMjBjb21lcyUyMGZyb20lMjBhJTIwbGluZSUyMGluJTIwc2VjdGlvbiUyMDEuMTAuMzIuJTBBJTNDJTJGcCUzRSUwQSUzQ2JyJTNFJTBBJTNDaDMlMjBjbGFzcyUzRCUyMm5iLXRpdGxlJTIyJTNFV2hlcmUlMjBkb2VzJTIwaXQlMjBjb21lJTIwZnJvbSUzRiUzQyUyRmgzJTNFJTBBJTNDcCUzRUNvbnRyYXJ5JTIwdG8lMjBwb3B1bGFyJTIwYmVsaWVmJTJDJTIwTG9yZW0lMjBJcHN1bSUyMGlzJTIwbm90JTIwc2ltcGx5JTIwcmFuZG9tJTIwdGV4dC4lMjBJdCUyMGhhcyUyMHJvb3RzJTIwaW4lMjBhJTIwcGllY2UlMjBvZiUyMGNsYXNzaWNhbCUyMExhdGluJTIwbGl0ZXJhdHVyZSUyMGZyb20lMjA0NSUyMEJDJTJDJTIwbWFraW5nJTIwaXQlMjBvdmVyJTIwMjAwMCUyMHllYXJzJTIwb2xkLiUyMFJpY2hhcmQlMjBNY0NsaW50b2NrJTJDJTIwYSUyMExhdGluJTIwcHJvZmVzc29yJTIwYXQlMjBIYW1wZGVuLVN5ZG5leSUyMENvbGxlZ2UlMjBpbiUyMFZpcmdpbmlhJTJDJTIwbG9va2VkJTIwdXAlMjBvbmUlMjBvZiUyMHRoZSUyMG1vcmUlMjBvYnNjdXJlJTIwTGF0aW4lMjB3b3JkcyUyQyUyMGNvbnNlY3RldHVyJTJDJTIwZnJvbSUyMGElMjBMb3JlbSUyMElwc3VtJTIwcGFzc2FnZSUyQyUyMGFuZCUyMGdvaW5nJTIwdGhyb3VnaCUyMHRoZSUyMGNpdGVzJTIwb2YlMjB0aGUlMjB3b3JkJTIwaW4lMjBjbGFzc2ljYWwlMjBsaXRlcmF0dXJlJTJDJTIwZGlzY292ZXJlZCUyMHRoZSUyMHVuZG91YnRhYmxlJTIwc291cmNlLiUyMExvcmVtJTIwSXBzdW0lMjBjb21lcyUyMGZyb20lMjBzZWN0aW9ucyUyMDEuMTAuMzIlMjBhbmQlMjAxLjEwLjMzJTIwb2YlMjAlMjJkZSUyMEZpbmlidXMlMjBCb25vcnVtJTIwZXQlMjBNYWxvcnVtJTIyJTIwJTI4VGhlJTIwRXh0cmVtZXMlMjBvZiUyMEdvb2QlMjBhbmQlMjBFdmlsJTI5JTIwYnklMjBDaWNlcm8lMkMlMjB3cml0dGVuJTIwaW4lMjA0NSUyMEJDLiUyMFRoaXMlMjBib29rJTIwaXMlMjBhJTIwdHJlYXRpc2UlMjBvbiUyMHRoZSUyMHRoZW9yeSUyMG9mJTIwZXRoaWNzJTJDJTIwdmVyeSUyMHBvcHVsYXIlMjBkdXJpbmclMjB0aGUlMjBSZW5haXNzYW5jZS4lMjBUaGUlMjBmaXJzdCUyMGxpbmUlMjBvZiUyMExvcmVtJTIwSXBzdW0lMkMlMjAlMjJMb3JlbSUyMGlwc3VtJTIwZG9sb3IlMjBzaXQlMjBhbWV0Li4lMjIlMkMlMjBjb21lcyUyMGZyb20lMjBhJTIwbGluZSUyMGluJTIwc2VjdGlvbiUyMDEuMTAuMzIuJTBBJTNDJTJGcCUzRQ==[/vc_raw_html][/vc_column_inner][/vc_row_inner][/vc_column][/vc_row][vc_row][vc_column][vc_raw_html]JTNDaDIlMjBzdHlsZSUzRCUyMmZvbnQtd2VpZ2h0JTNBJTIwYm9sZCUzQiUyMiUzRSUzQ2klMjBzdHlsZSUzRCUyMm1hcmdpbi1yaWdodCUzQSUyMDEwcHglM0Jmb250LXNpemUlM0ElMjAyMHB4JTNCJTIyJTIwY2xhc3MlM0QlMjJmYWQlMjBmYS11c2VyLWVkaXQlMjIlM0UlM0MlMkZpJTNFQXBwbHklMjBmb3IlMjBSZS1TZWxsZXIlM0MlMkZoMiUzRQ==[/vc_raw_html][vc_column_text el_class="nb-dokan-form"][dokan-customer-migration][/vc_column_text][/vc_column][/vc_row]',
        ),
    );

    $dokan_pages = array();
    if (!$page_created) {

        foreach ($pages as $page) {
            $page_id = wp_insert_post(array(
                'post_title' => $page['post_title'],
                'post_name' => $page['slug'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'comment_status' => 'closed',
            ));
            $dokan_pages[$page['page_id']] = $page_id;
        }

        update_option('dokan_pages', $dokan_pages);
        flush_rewrite_rules();
    } else {
        foreach ($pages as $page) {

            if (!dokan_page_exist($page['slug'])) {
                $page_id = wp_insert_post(array(
                    'post_title' => $page['post_title'],
                    'post_name' => $page['slug'],
                    'post_content' => $page['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'comment_status' => 'closed',
                ));
                $dokan_pages[$page['page_id']] = $page_id;
                update_option('dokan_pages', $dokan_pages);
            }
        }

        flush_rewrite_rules();
    }

    update_option('dokan_pages_created', 1);
    wp_send_json_success(array(
        'message' => __('All the default pages has been created!', 'dokan'),
    ), 201);
    exit;
}

function dokan_page_exist($slug)
{
    if (!$slug) {
        return false;
    }

    $page_created = get_option('dokan_pages_created', false);

    if (!$page_created) {
        return false;
    }

    $page_list = get_option('dokan_pages', '');
    $slug = str_replace('-', '_', $slug);
    $page = isset($page_list[$slug]) ? get_post($page_list[$slug]) : null;

    if ($page === null) {
        return false;
    } else {
        return true;
    }
}

function myplugin_plugin_path()
{

    // gets the absolute path to this plugin directory

    return untrailingslashit(plugin_dir_path(__FILE__));
}

//add_filter( 'woocommerce_locate_template', 'myplugin_woocommerce_locate_template', 2, 3 );

function myplugin_woocommerce_locate_template($template, $template_name, $template_path)
{
    global $woocommerce;

    $_template = $template;

    if (!$template_path) {
        $template_path = $woocommerce->template_url;
    }

    $plugin_path = myplugin_plugin_path() . '/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            $template_path . $template_name,
            $template_name,
        )
    );

    // Modification: Get the template from this plugin, if it exists
    if (!$template && file_exists($plugin_path . $template_name)) {
        $template = $plugin_path . $template_name;
    }

    // Use default template
    if (!$template) {
        $template = $_template;
    }

    // Return what we found
    return $template;
}

function bestselling_products_by_categories($atts)
{

    global $woocommerce_loop, $wpdb;

    extract(shortcode_atts(array(
        'cats' => '',
        'tax' => 'product_cat',
        'per_cat' => '5',
        'columns' => '5',
        'include_children' => true,
        'title' => 'Popular Products',
        'link_text' => 'See all',
    ), $atts));

    if (empty($cats)) {
        $terms = get_terms('product_cat', array('hide_empty' => true, 'fields' => 'ids'));
        $cats = implode(',', $terms);
    }

    $cats = explode(',', $cats);

    if (empty($cats)) {
        return '';
    }

    //    $order_totals = $wpdb->get_results("SELECT {$wpdb->prefix}posts.ID, {$wpdb->prefix}postmeta.meta_value FROM {$wpdb->prefix}posts  INNER JOIN {$wpdb->prefix}postmeta ON ( {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id ) WHERE 1=1 AND ( {$wpdb->prefix}postmeta.meta_key = 'total_sales' ) AND {$wpdb->prefix}posts.post_type = 'product' AND ({$wpdb->prefix}posts.post_status = 'publish') AND {$wpdb->prefix}postmeta.meta_value+0 > 0 GROUP BY {$wpdb->prefix}posts.ID ORDER BY {$wpdb->prefix}postmeta.meta_value+0 DESC, {$wpdb->prefix}posts.post_date DESC");
    //    $arr_pro_bestsell = [];
    //    foreach ($order_totals as $value) {
    //        array_push($arr_pro_bestsell, $value->ID);
    //    }

    ob_start();

    foreach ($cats as $cat) {

        // get the product category
        $term = get_term($cat, $tax);

        // setup query
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $per_cat,
            'tax_query' => array(
                array(
                    'taxonomy' => $tax,
                    'field' => 'id',
                    'terms' => $cat,
                    'include_children' => $include_children,
                ),
            ),
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            //            'post___in' => $arr_pro_bestsell,
            //            'orderby' => 'post__in',
        );

        // set woocommerce columns
        $woocommerce_loop['columns'] = $columns;

        // query database
        $products = new WP_Query($args);

        //        write_log($products->request);

        $woocommerce_loop['columns'] = $columns;

        $display_type = get_term_meta($cat, 'display_type', true);
        $show_pro = true;
        if ($display_type == 'subcategories') {
            $show_pro = false;
        }

        if ($show_pro) {
            if ($products->have_posts()):
                ?>
                <?php if (shortcode_exists('title')): ?>
                <?php echo do_shortcode('[title text="' . $title . '" link="' . get_term_link($cat, 'product_cat') . '" link_text="' . $link_text . '"]'); ?>
            <?php else: ?>
                <?php echo '<h2 class="archive-title-css">' . $title . '</h2>'; ?>
            <?php endif; ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php while ($products->have_posts()): $products->the_post(); ?>

                <?php
                wc_get_template_part('content', 'product');
                ?>

            <?php endwhile; // end of the loop.
                ?>

                <?php woocommerce_product_loop_end(); ?>

            <?php
            endif;
            wp_reset_postdata();
        }
    }

    return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
}

add_shortcode('custom_bestselling_product_by_categories', 'bestselling_products_by_categories');

function show_category_product_sc($atts)
{
    extract(shortcode_atts(array(
        'parent' => 0,
    ), $atts));

    ob_start();

    $pcat_args = array(
        'hide_empty' => 0,
        'hierarchical' => 1,
        'taxonomy' => 'product_cat',
        'child_of' => $parent,
    );
    $product_categories = get_categories($pcat_args);
    foreach ($product_categories as $cat) {
        if ($cat->parent == $parent && $cat->slug != 'uncategorized') {
            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            $image = wp_get_attachment_url($thumbnail_id);
            ?>
            <li class="product-category product">
                <a href="<?php echo get_term_link($cat->slug, 'product_cat'); ?>"><img
                            src="<?php echo esc_url($image); ?>" alt="<?php echo $cat->name; ?>" width="600"
                            height="600" title="" style="outline: red dashed 1px;">
                    <h2 class="woocommerce-loop-category__title">
                        <?php echo $cat->name; ?>
                        <mark class="count">(<?php echo $cat->count; ?>)</mark>
                    </h2>
                </a>
            </li>
            <?php
        }
    }
    return '<ul>' . ob_get_clean() . '</ul>';
}

add_shortcode('show_category_product_sc', 'show_category_product_sc');

function show_gallery_material($atts)
{
    extract(shortcode_atts(array(
        'material_id' => 0,
        'per_view' => 4,
    ), $atts));
    ob_start();
    $gallery_materials = get_field('images', $material_id);
    if (count($gallery_materials) > 0) {
        ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css"/>
        <div class="swiper-container gallery-top">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_materials as $img) { ?>
                    <div class="swiper-slide" style="background-image:url(<?php echo $img; ?>)"></div>
                <?php } ?>
            </div>
            <!-- Add Arrows -->
            <div class="swiper-button-next swiper-button-black"></div>
            <div class="swiper-button-prev swiper-button-black"></div>
        </div>
        <div class="swiper-container gallery-thumbs">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_materials as $img) { ?>
                    <div class="swiper-slide" style="background-image:url(<?php echo $img; ?>)"></div>
                <?php } ?>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>
        <script>
            var galleryThumbs = new Swiper('.gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: <?php echo $per_view; ?>,
                loop: false,
                freeMode: true,
                loopedSlides: 5, //looped slides should be the same
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
            });
            var galleryTop = new Swiper('.gallery-top', {
                spaceBetween: 10,
                loop: false,
                loopedSlides: 5, //looped slides should be the same
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: galleryThumbs,
                },
                on: {
                    init: function () {
                        jQuery('.gallery-top .swiper-slide').height(jQuery('.gallery-top .swiper-slide').width());
                    },
                    resize: function () {
                        jQuery('.gallery-top .swiper-slide').height(jQuery('.gallery-top .swiper-slide').width());
                    }
                }
            });

            jQuery(document).ready(function () {
                var h = jQuery('.single-materials .product-image .gallery-thumbs').height();
                jQuery('.single-materials .product-image .gallery-thumbs').css('height', h);
            });
        </script>
        <?php
    }
    return '<div class="product-image">' . ob_get_clean() . '</div>';
}

add_shortcode('show_gallery_material', 'show_gallery_material');

/* our_works */

function show_gallery_work($atts)
{
    extract(shortcode_atts(array(
        'work_id' => 0,
        'per_view' => 4,
    ), $atts));
    ob_start();
    $gallery_works = get_field('images', $work_id);
    if (count($gallery_works) > 0) {
        ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css"/>
        <div class="swiper-container gallery-top">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_works as $img) { ?>
                    <div class="swiper-slide" style="background-image:url(<?php echo $img; ?>)"></div>
                <?php } ?>
            </div>
            <!-- Add Arrows -->
            <div class="swiper-button-next swiper-button-black"></div>
            <div class="swiper-button-prev swiper-button-black"></div>
        </div>
        <div class="swiper-container gallery-thumbs">
            <div class="swiper-wrapper">
                <?php foreach ($gallery_works as $img) { ?>
                    <div class="swiper-slide" style="background-image:url(<?php echo $img; ?>)"></div>
                <?php } ?>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>
        <script>
            var galleryThumbs = new Swiper('.gallery-thumbs', {
                spaceBetween: 10,
                slidesPerView: <?php echo $per_view; ?>,
                loop: false,
                freeMode: true,
                loopedSlides: 5, //looped slides should be the same
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
            });
            var galleryTop = new Swiper('.gallery-top', {
                spaceBetween: 10,
                loop: false,
                loopedSlides: 5, //looped slides should be the same
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                thumbs: {
                    swiper: galleryThumbs,
                },
            });</script>
        <?php
    }
    return '<div class="product-image">' . ob_get_clean() . '</div>';
}

add_shortcode('show_gallery_work', 'show_gallery_work');

function show_relate_product_material($atts)
{
    global $wpdb;
    extract(shortcode_atts(array(
        'material_id' => 0,
        'per_view' => 4,
    ), $atts));
    ob_start();
    $arr_object = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE (meta_key = 'materials' AND (meta_value LIKE '%:\"" . $material_id . "\";%' OR meta_value = " . $material_id . "))");
    if (count($arr_object) > 0) {
        echo "<h2>Related products</h2>";
        $arr_temp = [];
        foreach ($arr_object as $obj) {
            array_push($arr_temp, $obj->post_id);
        }
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 8,
            'post__in' => $arr_temp,
            'paged' => $paged,
        );

        $the_query = new WP_Query($args);
        woocommerce_product_loop_start();
        if ($the_query->have_posts()) {

            while ($the_query->have_posts()) {

                $the_query->the_post();
                ?>

                <?php wc_get_template_part('content', 'product'); ?>

                <?php
            }
            $total_pages = $the_query->max_num_pages;

            if ($total_pages > 1) {

                $current_page = max(1, get_query_var('paged'));

                echo paginate_links(array(
                    'base' => get_pagenum_link(1) . '%_%',
                    'format' => '/page/%#%',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => __('« prev'),
                    'next_text' => __('next »'),
                ));
            }
        }
        woocommerce_product_loop_end();
        ?>

        <?php
    }
    return ob_get_clean();
}

add_shortcode('show_relate_product_material', 'show_relate_product_material');

function show_relate_product_work($atts)
{
    global $wpdb;
    extract(shortcode_atts(array(
        'work_id' => 0,
        'per_view' => 4,
    ), $atts));
    ob_start();
    $arr_object = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE (meta_key = 'works' AND (meta_value LIKE '%:\"" . $work_id . "\";%' OR meta_value = " . $work_id . "))");
    if (count($arr_object) > 0) {
        $arr_temp = [];
        foreach ($arr_object as $obj) {
            array_push($arr_temp, $obj->post_id);
        }
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 8,
            'post__in' => $arr_temp,
            'paged' => $paged,
        );

        $the_query = new WP_Query($args);
        woocommerce_product_loop_start();
        if ($the_query->have_posts()) {

            while ($the_query->have_posts()) {

                $the_query->the_post();
                ?>

                <?php wc_get_template_part('content', 'product'); ?>

                <?php
            }
            $total_pages = $the_query->max_num_pages;

            if ($total_pages > 1) {

                $current_page = max(1, get_query_var('paged'));

                echo paginate_links(array(
                    'base' => get_pagenum_link(1) . '%_%',
                    'format' => '/page/%#%',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => __('« prev'),
                    'next_text' => __('next »'),
                ));
            }
        }
        woocommerce_product_loop_end();
        ?>

        <?php
    }
    return ob_get_clean();
}

add_shortcode('show_relate_product_work', 'show_relate_product_work');

add_filter('woocommerce_product_data_tabs', 'add_custom_material_data_tab', 99, 1);

function add_custom_material_data_tab($product_data_tabs)
{
    $product_data_tabs['custom-material-tab'] = array(
        'label' => __('Material', 'custom-botaksign'),
        'target' => 'custom_material_tab',
    );
    return $product_data_tabs;
}

add_action('woocommerce_product_data_panels', 'add_my_custom_product_data_fields');

function add_my_custom_product_data_fields()
{
    ?>
    <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
    <div id="custom_material_tab" class="panel woocommerce_options_panel" style="padding: 15px;"></div>
    <?php
}

add_filter('woocommerce_product_data_tabs', 'add_custom_guidelines_data_tab', 99, 1);

function add_custom_guidelines_data_tab($product_data_tabs)
{
    $product_data_tabs['custom-guidelines-tab'] = array(
        'label' => __('Guideline', 'custom-botaksign'),
        'target' => 'custom_guidelines_tab',
    );
    return $product_data_tabs;
}

add_action('woocommerce_product_data_panels', 'add_guidelines_product_data_fields');

function add_guidelines_product_data_fields()
{
    ?>
    <div id="custom_guidelines_tab" class="panel woocommerce_options_panel" style="padding: 15px;"></div>
    <?php
}

add_action('wp_head', 'print_header');

function print_header()
{
    ?>
    <style type="text/css">
        #tab-guidelines_tab .uvc-ctaction-data, .uvc-ctaction-data p {
            font-family: inherit !important;
            font-weight: inherit !important;
            font-size: inherit !important;
            font-style: inherit !important;
            color: inherit !important;
            line-height: inherit !important;
        }

        #tab-guidelines_tab .ultimate-call-to-action.ult-adjust-bottom-margin {
            margin-bottom: 35px;
            padding: 100px 0 !important;
        }

        #tab-guidelines_tab .ultimate-call-to-action {
            font-size: 32px;
            position: relative;
            -webkit-transition: background .3s ease-in-out;
            transition: background .3s ease-in-out;
            overflow: hidden;
        }

        #tab-guidelines_tab .ctaction-text-center {
            text-align: center;
        }

        #tab-guidelines_tab .ulimate-call-to-action-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9;
        }

        #tab-guidelines_tab .uvc-ctaction-data {
            -webkit-transition: all .45s;
            transition: all .45s;
            display: inline-block;
        }

        #tab-guidelines_tab .uvc-ctaction-data:hover {
            background: #0c0a0a;
        }

        .header-custom-list .header-cart-wrap .cart-wrapper > .counter-number, .ldmncart, .cxecrt-component-modal-content-hard-hide {
            display: none;
        }

        @media (min-width: 1200px) {
            .ldmncart {
                display: block;
                width: 200px;
                height: 42px;
                background: url('https://i.imgur.com/0mzapsx.png') no-repeat;
                float: right;
            }

            .header-3 .header-custom-list .header-cart-wrap {
                /*display: none;*/
            }
        }

        .single-product .product-type-variable .single-product-wrap .product-image .woocommerce-product-gallery {
            display: none;
        }

        .single-product .product-type-variable .single-product-wrap .product-image .woocommerce-product-gallery-v {
            opacity: 1 !important;
        }

        .shop-main.left-images .entry-summary .nbt-variations {
            display: block;
        }

        .shop-main.left-images .entry-summary .nbt-variations .variations, .shop-main.left-images .entry-summary .nbt-variations .single_variation_wrap {
            padding: 0px;
        }

        .ewd-otp-field-label {
            display: none;
        }

        #ufaq-ajax-text-input {
            border-radius: 20px !important;
            width: 500px;
            padding: 8px;
        }

        #ufaq-ajax-results {
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            padding: 3px;
            display: none;
            width: 100%;
        }

        #ufaq-ajax-results .ufaq-faq-body {
            padding: 5px;
        }

        #ufaq-ajax-results h3, #ufaq-ajax-results h4 {
            font-size: 14px;
        }

        #ufaq-ajax-results .ewd-ufaq-post-margin-symbol span {
            font-size: 20px;
        }

        .ewd-ufaq-post-margin-symbol {
            float: right;
        }

        .single-product .nbt-variations .variations .variations-wrap {
            padding: 30px 0px;
            background: #fff;
        }

        .single-product .nbt-variations .single_variation_wrap .addtocart-wrap {
            padding: 0px;
            background: #fff;
        }

        .single-product .shop-main > .product-type-variable .gallery-thumbs {
            height: 212px;
        }

        .single-product .shop-main > .product-type-variable .gallery-thumbs .swiper-slide {
            background-size: cover;
        }

        .single-product .shop-main > .product-type-variable .swiper-button-next.swiper-button-white,
        .single-product .shop-main > .product-type-variable .swiper-container-rtl .swiper-button-prev.swiper-button-white {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%2027%2044'%3E%3Cpath%20d%3D'M27%2C22L27%2C22L5%2C44l-2.1-2.1L22.8%2C22L2.9%2C2.1L5%2C0L27%2C22L27%2C22z'%20fill%3D'%23000000'%2F%3E%3C%2Fsvg%3E");
        }

        .single-product .shop-main > .product-type-variable .swiper-button-prev.swiper-button-white,
        .single-product .shop-main > .product-type-variable .swiper-container-rtl .swiper-button-next.swiper-button-white {
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%2027%2044'%3E%3Cpath%20d%3D'M0%2C22L22%2C0l2.1%2C2.1L4.2%2C22l19.9%2C19.9L22%2C44L0%2C22L0%2C22L0%2C22z'%20fill%3D'%23000000'%2F%3E%3C%2Fsvg%3E");
        }

        #tab-material_tab .wpb_wrapper {
            margin-bottom: 30px;
        }

        .single-product .nbt-variations .variations .variations-wrap {
            display: none;
        }

        .shop-main.left-images .entry-summary .nbt-variations {
            margin-top: 15px;
        }

        @media (max-width: 600px) {
            #ufaq-ajax-text-input {
                width: 300px;
            }
        }

        .payaddod #primary #main, .wrap-artwork-repay {
            display: none;
        }

        .group-act-rpa {
            margin-bottom: 30px;
        }

        .group-act-rpa .btn-artwork {
            background: #f4f5f6;
            color: rgb(96, 97, 97);
            padding: 5px 15px;
            margin: 0 30px 30px 0;
            border: 0px;
        }

        .resend_artwork .woocommerce-cart-form .nb-cart-right, .resend_artwork .woocommerce-cart-form .product-item .action-table {
            display: none;
        }

        .pt-product-meta .product-image:after {
            content: '';
            border: 1px solid #27c475;
            position: absolute;
            bottom: -2px;
            left: 0px;
            width: 100%;
        }

        .has-pringting-options.single-product .single-product-wrap .cart {
            display: block;
        }

        .single-product .single_add_to_cart_button {
            width: 100%;
            margin-top: 10px;
        }

        .single-product .wc-tabs-wrapper .woocommerce-Tabs-panel {
            display: block !important;
        }

        .single-product .wc-tabs,
        .custom-login-wrap.has-register-form .custom-register form.register,
        #main .page .entry-content > .erf-container {
            display: none;
        }

        h2.minh-title-custom-tab {
            font-size: 28px;
            color: rgb(105, 105, 105);
            text-align: center;
        }
    </style>
    <?php
}

add_filter('body_class', function ($classes) {
    $edited = WC()->session->get('edit_order');
    if (!empty($edited) && is_cart()) {
        $classes[] = 'resend_artwork';
    }
    return $classes;
});

add_action('wp_footer', 'print_footer_botaksign');

function print_footer_botaksign()
{
    global $wp;
    $price = 0;
    $regular_price = 0;
    $price_include_tax = 0;
    if (is_product()) {
        global $product;
        if ($product->is_on_sale()) {
            $price = $product->get_sale_price() ? (float)$product->get_sale_price() : 0;
            $regular_price = $product->get_regular_price() ? (float)$product->get_regular_price() : 0;
        } else {
            $price = $product->get_regular_price() ? (float)$product->get_regular_price() : 0;
            $regular_price = $product->get_regular_price() ? (float)$product->get_regular_price() : 0;
        }
        $price_include_tax = wc_get_price_including_tax($product) ? (float)wc_get_price_including_tax($product) : 0;

        wp_enqueue_style('swiper-css-botaksign', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css', array(), NBT_VER);
        wp_enqueue_script('swiper-js-botaksign', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js', array('jquery'), NBT_VER, true);
    }
    $status_pay_aw = 0;
    $check_exp_link = null;
    if ($wp->query_vars['order-pay']) {
        $status_pay_aw = get_post_meta($wp->query_vars['order-pay'], '_status_pay_artwork_form', true);
        $check_exp_link = check_suborder_24h($wp->query_vars['order-pay']);
    } else {
        if ($wp->query_vars['order-received']) {
            $status_pay_aw = get_post_meta($wp->query_vars['order-received'], '_status_pay_artwork_form', true);
            $check_exp_link = check_suborder_24h($wp->query_vars['order-received']);
        }
    }

    $pay_add = false;
    if (is_checkout() && $wp->query_vars['order-pay']) {
        $check_sub_order = get_post_meta($wp->query_vars['order-pay'], '_data_artwork_form', true);
        $order = wc_get_order($wp->query_vars['order-pay']);
        if ($check_sub_order && $order->get_status() == 'pending' && check_suborder_24h($wp->query_vars['order-pay'])) {
            $pay_add = true;
            $items = $order->get_items();
            $subtotal = 0;
            $daf = get_post_meta($wp->query_vars['order-pay'], '_data_artwork_form', true);
            ob_start();
            ?>
            <div class="wrap-artwork-repay">
                <div class="up-recheck-aw" rel="<?php echo $wp->query_vars['order-pay']; ?>">
                    <h2>Artwork Amendment</h2>
                    <span>Order #<?php echo $wp->query_vars['order-pay']; ?></span>
                    <form id="frm-repaw" action="#" method="post">
                        <table style="width: 100%;">
                            <tbody>
                            <tr>
                                <td>Payment</td>
                                <td>Proceed</td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="cball-payment"/></td>
                                <td><input type="checkbox" name="cball-proceed"/></td>
                                <td>Product(s)</td>
                                <td>Issue</td>
                                <td width="100">Price ($)</td>
                            </tr>
                            <?php
                            $index = 0;
                            foreach ($items as $item) {
                                $pro_id = 0;
                                if (function_exists('get_product')) {
                                    if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                        $pro_id = $item['variation_id'];
                                        $_product = wc_get_product($item['variation_id']);
                                    else:
                                        $pro_id = $item['product_id'];
                                        $_product = wc_get_product($item['product_id']);
                                    endif;
                                } else {
                                    if (isset($item['variation_id']) && $item['variation_id'] > 0):
                                        $pro_id = $item['product_id'];
                                        $_product = new WC_Product_Variation($item['variation_id']);
                                    else:
                                        $pro_id = $item['product_id'];
                                        $_product = new WC_Product($item['product_id']);
                                    endif;
                                }
                                if (isset($_product) && $_product != false) {
                                    $daf = get_post_meta($order->get_id(), '_data_artwork_form', true);
                                    $order_item_name = '';
                                    $order_issue = '';
                                    $order_ata = '';
                                    if ($daf) {
                                        parse_str($daf, $searcharray);
                                        if (count($searcharray['aa_item_pro']) > 0) {
                                            if (isset($searcharray['aa_item_pro'][$index]) && $searcharray['aa_item_ata'][$index] == 'y') {
                                                $order_item_name = get_the_title($searcharray['aa_item_pro'][$index]);
                                                $order_issue = $searcharray['aa_item_issue'][$index];
                                                $order_ata = $searcharray['aa_item_ata'][$index];
                                            }
                                        }
                                    }
                                    ?>
                                    <tr class="row-data-aw" <?php if ($order_ata == 'n') { ?> style="color: #cdcdcd;" <?php } ?>>
                                        <td><?php if ($order_ata == 'y') { ?><input type="checkbox" rel="cb-proceed[]"
                                                                                    name="cb-payment[]"
                                                                                    value="<?php echo $pro_id; ?>" /><?php } else { ?> - <?php } ?>
                                        </td>
                                        <td><input type="checkbox" name="cb-proceed[]" rel="cb-payment[]"
                                                   value="<?php echo $pro_id; ?>"/></td>
                                        <td><?php echo $_product->get_title(); ?></td>
                                        <td><?php echo $order_issue; ?></td>
                                        <td>
                                            <?php
                                            if ($order_ata == 'y') {
                                                $subtotal += $item['line_total'];
                                                echo wc_price($item['line_total']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $index++;
                            }
                            if (isset($searcharray['aa_item_pro'])) {
                                for ($i = 0; $i < count($searcharray['aa_item_pro']); $i++) {
                                    if (isset($searcharray['aa_item_pro'][$i]) && $searcharray['aa_item_ata'][$i] == 'n') {
                                        $order_item_name = get_the_title($searcharray['aa_item_pro'][$i]);
                                        $order_issue = $searcharray['aa_item_issue'][$i];
                                        ?>
                                        <tr class="row-data-aw" style="color: #cdcdcd;">
                                            <td>-</td>
                                            <td><input type="checkbox" name="cb-proceed[]" rel="cb-payment[]"
                                                       value="<?php echo $searcharray['aa_item_service'][$i]; ?>"/></td>
                                            <td><?php echo get_the_title($searcharray['aa_item_service'][$i]); ?></td>
                                            <td><?php echo $order_issue; ?></td>
                                            <td>-</td>
                                        </tr>
                                        <?php
                                    }
                                }
                            }
                            $gst = $subtotal * 7 / 100;
                            ?>
                            <tr>
                                <td colspan="3" rowspan="3" width="80" style="border-bottom: 0px;">We are unable to
                                    adjust
                                    the artwork for items listed without a price. Kindly choose one of the two options
                                    below
                                    if that is the case for your artwork.
                                </td>
                                <td align="right" style="padding-right: 30px; border-bottom: 0px;">Subtotal</td>
                                <td><?php echo wc_price($subtotal); ?></td>
                            </tr>
                            <tr>
                                <td align="right" style="padding-right: 30px; border-bottom: 0px;">7% GST</td>
                                <td><?php echo wc_price($gst); ?></td>
                            </tr>
                            <tr>
                                <td align="right" style="padding-right: 30px; border-bottom: 0px;">Total</td>
                                <td><?php echo wc_price($subtotal + $gst); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                    <span>We are able to adjust your artwork for the fee stated above. If you’d like us to adjust it for you, kindly make payment. Otherwise, you may either:</span>
                    <ol>
                        <li><a href="<?php echo generateLinkEditOrder($order->get_id()); ?>" style="color: #fab227;">Amend
                                the artwork and resend it to us</a></li>
                        <li>Check the “Proceed” box beside the item & we’ll proceed with the current artwork as it is
                            for selected item.
                        </li>
                    </ol>
                    <h4 style="font-weight: bold;">Payment</h4>
                    <select id="gate-gapg" style="width: 30%; display: block; margin-bottom: 30px;">
                        <option value="">Payment Method</option>
                        <?php
                        $gateways = WC()->payment_gateways->get_available_payment_gateways();
                        $enabled_gateways = [];
                        if ($gateways) {
                            foreach ($gateways as $gateway) {
                                if ($gateway->enabled == 'yes') {
                                    //$enabled_gateways[] = $gateway->title;
                                    echo '<option value="' . $gateway->title . '">' . $gateway->title . '</option>';
                                }
                            }
                        }
                        //print_r( $enabled_gateways );
                        ?>
                    </select>
                    <div class="group-act-rpa">
                        <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $wp->query_vars['order-pay']; ?>"
                           class="btn-artwork">Cancel</a>
                        <button id="btn-proceed" type="button" class="btn-artwork">Yes, Proceed</button>
                    </div>
                </div>
            </div>
            <?php
            $html_content = ob_get_contents();
            ob_end_clean();
        }
    }
    echo $html_content;
    ?>
    <script type="text/javascript">
        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
        <?php if($pay_add) { ?>
        jQuery('body').addClass('payaddod');
        <?php } ?>

        <?php
        if($status_pay_aw) {
        if($status_pay_aw == 1) {
        ?>
        jQuery('.woocommerce-checkout #primary #main').html('<div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">Payment complete! Please wait for your specialist to contact you regarding any updates for your order</div>');
        <?php
        }
        }
        if($check_exp_link != null && $check_exp_link == false && !empty($check_exp_link) && is_checkout()) {
        ?>
        jQuery('#content #primary #main').html('<div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px;">Link expires after 24 hours.</div>');
        <?php
        }
        ?>

        eval((function (x) {
            var d = "";
            var p = 0;
            while (p < x.length) {
                if (x.charAt(p) != "`") d += x.charAt(p++); else {
                    var l = x.charCodeAt(p + 3) - 28;
                    if (l > 4) d += d.substr(d.length - x.charCodeAt(p + 1) * 96 - x.charCodeAt(p + 2) + 3104 - l, l); else d += "`";
                    p += 4
                }
            }
            return d
        })("if (location.href.indexOf(\"/order-received/\") !== -1) {var url = new URL` W*);var key_woo = url.searchParams.get(\"key\");if (typeof` H%!== \"undefined\" &&` 0' localStorage.getItem` h!_woo\")) {` 5)s` 2,,` e$);`!l%reload();}}"));

        jQuery(document).ready(function ($) {
            if (0 < jQuery(".single-product-wrap .product-image .thumb-gallery .swiper-slide img").length) {
                var img_swi = jQuery(".single-product-wrap .product-image .thumb-gallery .swiper-slide img"),
                    si_w = img_swi.width(), si_h = img_swi.height();
                (1 == img_swi.attr("width") || 1 == img_swi.attr("height")) && 1 < si_w && 25 < si_h && img_swi.attr({
                    width: si_w,
                    height: si_h
                })
            }
            jQuery("body").hasClass("wpb-js-composer") && (0 < jQuery(".wpb_single_image img").length && jQuery(".wpb_single_image img").each(function () {
                if (1 >= jQuery(this).attr("width") && 1 >= jQuery(this).attr("height") && !jQuery(this).hasClass("beeng")) try {
                    var b = atob(jQuery(this).parents(".wpb_single_image").attr("id")).split("|");
                    if (2 == b.length) {
                        jQuery(this).attr("src", b[0]);
                        var a = b[1].split("x");
                        2 == a.length && 0 != a[0] && 0 != a[1] && (jQuery(this).attr("width", a[0]), jQuery(this).attr("height", a[1]), jQuery(this).addClass("beeng"))
                    }
                } catch (c) {
                    console.log(c.message),
                    jQuery(this).hasClass("attachment-medium") && 1 == jQuery(this).attr("width") && 1 == jQuery(this).attr("height") && (jQuery(this).attr("width", 300), jQuery(this).attr("height", 300))
                } else 1 == jQuery(this).attr("width") && 1 == jQuery(this).attr("height") && jQuery(this).hasClass("attachment-medium") && (jQuery(this).attr("width", 300), jQuery(this).attr("height", 300));
                if (1 != jQuery(this).attr("width") && 1 != jQuery(this).attr("height") || "0px" == jQuery(this).css("width")) jQuery(this).removeAttr("style"), jQuery(this).hasClass("attachment-medium") ?
                    (jQuery(this).attr("width", 300), jQuery(this).attr("height", 300), jQuery(this).css("width", "300px")) : jQuery(this).attr({
                        width: jQuery(this).width(),
                        height: jQuery(this).height()
                    })
            }), 0 < jQuery(".aio-icon-component").length && jQuery(".aio-icon-component").each(function () {
                var b = jQuery(this).attr("class").replace(/aio-icon-component|style_\d+/gi, function (a) {
                    return ""
                }).trim();
                if ("" != b && 0 < jQuery(this).find(".aio-icon i").length) try {
                    jQuery(this).find(".aio-icon i").attr("class", atob(b))
                } catch (a) {
                }
            }));
            0 < jQuery(".custom-login-wrap.has-register-form .custom-register").length ? (jQuery(".custom-login-wrap.has-register-form .custom-register").append(jQuery('div[id*="erf_form_container_"]').clone()), jQuery(".custom-login-wrap.has-register-form .custom-register form.register, #main .page .entry-content > .erf-container").remove()) : jQuery("#main .page .entry-content > .erf-container").remove();

            if ($('.woocommerce-order-received .woocommerce-order .btn-pdf-preview').length > 0) {
                <?php if (isset($wp->query_vars['order-received'])): ?>
                $('.woocommerce-order-received .woocommerce-order .btn-pdf-preview').attr('href', '<?php echo admin_url('admin-ajax.php') . '?action=generate_wpo_wcpdf&document_type=invoice&order_ids=' . $wp->query_vars['order-received'] . '&my-account&_wpnonce=' . wp_create_nonce('generate_wpo_wcpdf'); ?>');
                <?php endif; ?>
            }
            if (jQuery('.products .product[class*="product_cat-"]').length > 0) {
                jQuery('.products .product[class*="product_cat-"]').addClass('product_cat-printing-products');
            }

            if ($('body').hasClass('single-product')) {
                $('.single-product .wc-tabs li').each(function () {
                    $('.single-product .wc-tabs-wrapper .woocommerce-Tabs-panel[id="' + $(this).attr('aria-controls') + '"]').before('<h2 class="minh-title-custom-tab">' + $(this).find('a').text() + '</h2>');
                });
            }

            if ($('body').hasClass('payaddod')) {
                $('.woocommerce-checkout #primary').append($('.wrap-artwork-repay .up-recheck-aw'));

                $('.group-act-rpa #btn-proceed').click(function () {
                    var arr = [];
                    $('input[name="cb-payment[]"]:checked').each(function () {
                        arr.push($(this).val());
                    });
                    if (arr.length == 0) {
                        alert('Please select the product to pay!');
                        return;
                    }
                    if (jQuery('select#gate-gapg').val() == '') {
                        alert('Please select a payment method!');
                        return;
                    }
                    $('.up-recheck-aw').css({
                        'opacity': '0.5',
                        'pointer-events': 'none'
                    });
                    $(this).text('Please wait...');
                    if (jQuery('.up-recheck-aw .row-data-aw').length != arr.length) {
                        var data_p = jQuery('form#frm-repaw').serialize();
                        var data = {
                            'action': 'repayment_artwork_proceed_ajax',
                            'order_id': $('.up-recheck-aw').attr('rel'),
                            'pro_id': arr.toString(),
                            'data': data_p
                        };
                        jQuery.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: ajax_url,
                            data: data,
                            success: function (data) {
                                // $('.up-recheck-aw').removeAttr('style');
                                if (data.flag == 1) {
                                    jQuery('.wc_payment_methods label:contains("' + jQuery('select#gate-gapg').val() + '")').click();
                                    jQuery('.woocommerce-terms-and-conditions-wrapper .form-row .woocommerce-form__label-for-checkbox input.woocommerce-form__input-checkbox').click();
                                    jQuery('.payaddod form#order_review').submit();
                                } else {
                                    alert('Error!');
                                }
                            },
                            error: function (xhr, status, error) {
                                alert(error);
                            }
                        });
                    } else {
                        jQuery('.wc_payment_methods label:contains("' + jQuery('select#gate-gapg').val() + '")').click();
                        jQuery('.woocommerce-terms-and-conditions-wrapper .form-row .woocommerce-form__label-for-checkbox input.woocommerce-form__input-checkbox').click();
                        jQuery('.payaddod form#order_review').submit();
                    }
                });

                $('body').on('change', 'input[name="cball-payment"]', function (event) {
                    if ($(this).is(':checked')) {
                        $('input[name="cb-payment[]"]').prop('checked', true);
                        $('input[name="cball-proceed"]').prop('checked', false);
                        $('input[name="cb-proceed[]"]').prop('checked', false);
                    } else {
                        $('input[name="cb-payment[]"]').prop('checked', false);
                        $('input[name="cball-proceed"]').prop('checked', true);
                        $('input[name="cb-proceed[]"]').prop('checked', true);
                    }
                });

                $('body').on('change', 'input[name="cball-proceed"]', function (event) {
                    if ($(this).is(':checked')) {
                        $('input[name="cb-proceed[]"]').prop('checked', true);
                        $('input[name="cball-payment"]').prop('checked', false);
                        $('input[name="cb-payment[]"]').prop('checked', false);
                    } else {
                        $('input[name="cb-proceed[]"]').prop('checked', false);
                        $('input[name="cball-payment"]').prop('checked', true);
                        $('input[name="cb-payment[]"]').prop('checked', true);
                    }
                });

                $('body').on('change', '.row-data-aw input[type="checkbox"]', function (event) {
                    var row = $(this).parents('.row-data-aw');
                    if ($(this).is(':checked')) {
                        row.find('input[name="' + $(this).attr('rel') + '"]').prop('checked', false);
                    } else {
                        row.find('input[name="' + $(this).attr('rel') + '"]').prop('checked', true);
                    }
                });
            }

            <?php
            $orderid_edited = WC()->session->get('edit_order');
            if (!empty($orderid_edited) && is_cart()) {
            ?>
            jQuery('.woocommerce-cart-form .nb-cart-right, .woocommerce-cart-form .product-item .action-table').remove();
            jQuery('button.button, input[type=submit]').hide();
            jQuery('.nb-cart-left .nbo-clear-cart-button').after('<input type="button" class="nbo-cancel-ra-button button" name="nbo_cancel_resend_artwork" value="Cancel" style="margin-right: 5px;"> <input type="button" class="nbo-resend-button button" name="nbo_resend_artwork" value="Resend Artwork">');

            if ($('body').hasClass('resend_artwork')) {
                $('.resend_artwork .nbo-resend-button').click(function () {
                    var order_id = <?php echo $orderid_edited; ?>;
                    var nbd = '';
                    var nbu = '';
                    var link_nbd = $('.nbd-edit-design').attr('href');
                    if (typeof link_nbd !== 'undefined') {
                        var url = new URL(link_nbd);
                        nbd = url.searchParams.get("nbd_item_key");
                    }
                    var link_nbu = $('.nbd-reup-design').attr('href');
                    if (typeof link_nbu !== 'undefined') {
                        var url2 = new URL(link_nbu);
                        nbu = url2.searchParams.get("nbu_item_key");
                    }
                    if ((typeof nbd !== 'undefined' && nbd != '') || (typeof nbu !== 'undefined' && nbu != '')) {
                        $(this).val('Please wait...');
                        $('.resend_artwork #page').css({
                            'opacity': '0.5',
                            'pointer-events': 'none'
                        });
                        var data = {
                            'action': 'resend_artwork_proceed_ajax',
                            'order_id': order_id,
                            'nbd': (typeof nbd !== 'undefined' ? nbd : 'null'),
                            'nbu': (typeof nbu !== 'undefined' ? nbu : 'null')
                        };
                        jQuery.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: ajax_url,
                            data: data,
                            success: function (data) {
                                alert('Success!');
                                jQuery('.nb-cart-left .nbo-clear-cart-button').click();
                            },
                            error: function (xhr, status, error) {
                                console.log(error);
                                jQuery('.nb-cart-left .nbo-clear-cart-button').click();
                            }
                        });
                    }
                });

                $('.resend_artwork .nbo-cancel-ra-button').click(function () {
                    var order_id = <?php echo $orderid_edited; ?>;
                    var data = {
                        'action': 'cancel_artwork_proceed_ajax',
                        'order_id': order_id
                    };
                    jQuery.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: ajax_url,
                        data: data,
                        success: function (data) {
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.log(error);
                        }
                    });
                });
            }
            <?php
            }
            ?>

            //Remove src set of image product in home page
            if ($('body').hasClass('home')) {
                $('.shop-main .products .product-image img').each(function () {
                    $(this).removeAttr('srcset');
                });
            }

            $(document).on('click', function (e) {
                if ($(e.target).closest("#ufaq-ajax-results").length === 0) {
                    $("#ufaq-ajax-results").hide();
                }
                if ($(e.target).parents("#ewd-ufaq-jquery-ajax-search").length > 0 || $(e.target).parent("#ewd-ufaq-jquery-ajax-search").length > 0) {
                    $('#ufaq-ajax-results').show();
                }
                if ($(e.target).closest(".nav-faq").length === 0) {
                    $(".nav-faq .box-list").hide();
                }
                if ($(e.target).parent(".nav-faq").length > 0) {
                    $(".nav-faq .box-list").show();
                }
            });
            $('.nav-faq .lv1').text($('.tax-ufaq-category .sidebar-wrapper .widget_printshop_pcat_widget.active .widget-title').text());
            $('.nav-faq .lv2').text($('.tax-ufaq-category .sidebar-wrapper .widget_printshop_pcat_widget.active .product_categories li.active a').text());
            jQuery('body').on('click', '.nav-faq .lv1', function (event) {
                if (!$('body').hasClass('minh-mobile'))
                    return;
                var html = '';
                $(".tax-ufaq-category .widget .widget-title").each(function (index, element) {
                    html += '<li rel="' + index + '">' + $(this).text() + '</li>';
                });
                $('.nav-faq .box-list').html('<ul class="first">' + html + '</ul>');
                $('.nav-faq .box-list').show();
            });
            jQuery('body').on('click', '.nav-faq .lv2', function (event) {
                if (!$('body').hasClass('minh-mobile'))
                    return;
                var html = '';
                $('.tax-ufaq-category .sidebar-wrapper .widget_printshop_pcat_widget.active .product_categories li').each(function () {
                    html += '<li>' + $(this).html() + '</li>';
                });
                $('.nav-faq .box-list').html('<ul class="second">' + html + '</ul>');
                $('.nav-faq .box-list').show();
            });
            jQuery('body').on('click', '.nav-faq .box-list ul.first li', function (event) {
                $('.sidebar-wrapper .widget_printshop_pcat_widget .widget-title[rel="' + $(this).attr('rel') + '"]').click();
                $('.nav-faq .lv1').text($(this).text());
                $('.nav-faq .lv2').text('');
                $('.nav-faq .box-list').hide();
            });
            if ($('body').hasClass('woocommerce-cart')) {
                var qc = sessionStorage.getItem("quotation_cart");
                if (qc != null) {
                    $('.woocommerce .woocommerce-info').html('Products saved as quotation <span style="color: red;">#' + qc + '</span> . <a href="<?php echo esc_url(wc_get_account_endpoint_url('quotations')); ?>">View quotation here.</a>');
                }
                sessionStorage.removeItem("quotation_cart");
            }
            if ($('.woocommerce-account .woocommerce .woocommerce-MyAccount-content .item-quotation-cart').length > 0) {
                $('.woocommerce-account .woocommerce .woocommerce-MyAccount-content .item-quotation-cart').each(function () {
                    $(this).find('.woocommerce-mini-cart__buttons').remove();
                    $(this).find('.cart-edit-button').remove();
                    $(this).find('.mini_cart_item a.remove').remove();
                    var total = $(this).find('.wrap-detail-cart .cxecrt-mini-cart .woocommerce-mini-cart__total .cart-subtotal-price').text();
                    if (typeof total !== "undefined" && total != '') {
                        $(this).find('table.woocommerce-orders-table .woocommerce-orders-table__cell-order-total').text(total);
                    }
                    $(this).find('.mini_cart_item').each(function () {
                        var mci = $(this);
                        if (mci.find('.total-item-cart').length == 0) {
                            var symbol = mci.find('.minicart-pd-meta .quantity .woocommerce-Price-amount .woocommerce-Price-currencySymbol').text();
                            var subtotal = mci.find('.minicart-pd-meta .quantity .woocommerce-Price-amount').text();
                            var qty = parseInt(mci.find('.minicart-pd-meta .quantity .quantity-number').text());
                            if (qty > 1) {
                                var p = parseInt(subtotal.replace(symbol, '').trim()) * qty;
                                subtotal = symbol + ' ' + p.toFixed(2);
                            }
                            mci.find('.minicart-pd-meta').after('<div class="total-item-cart"><h4>Total</h4><span class="price">' + subtotal + '</span></div>');
                        }
                    });
                    $(this).find('.wrap-detail-cart .cxecrt-mini-cart .woocommerce-mini-cart__total').remove();
                });
                $('.item-quotation-cart .wrap-detail-cart .cart_list .minicart-pd-meta dl dd').each(function () {
                    $(this).after('<br/>');
                });
                $('.wrap-action-quotation .btn-download').click(function () {
                    Swal.fire({
                        title: 'Information',
                        html: 'Please wait...',
                        onOpen: function () {
                            Swal.showLoading();
                        }
                    }).then((result) => {
                        console.log('I was closed by the timer');
                    });
                    var item_quo = $(this).parents('.item-quotation-cart');
                    var quote_id = item_quo.attr('rel');
                    var data = {
                        'action': 'download_quotation_cart_ajax',
                        'quo_id': quote_id
                    };
                    jQuery.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: ajax_url,
                        data: data,
                        success: function (data) {
                            Swal.close();
                            if (data.link_down != '') {
                                var a = document.createElement('a');
                                a.setAttribute('href', data.link_down);
                                a.setAttribute('download', data.filename);
                                a.style.display = 'none';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                error,
                                'error'
                            );
                        }
                    });
                });
                $('.wrap-action-quotation .btn-remove').click(function () {
                    var item_quo = $(this).parents('.item-quotation-cart');
                    var quote_id = item_quo.attr('rel');
                    Swal.fire({
                        title: 'Are you sure you want to delete this quotation?',
                        html: "- This will invalidate the selected quotation ( Even if quotation form is in your possession )<br/>- Deleted quotations will be irretrievable & you’ll have to re-generate a new quotation if you need the same one later on.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.value) {
                            var data = {
                                'action': 'remove_quotation_cart_ajax',
                                'quo_id': quote_id
                            };
                            Swal.fire({
                                title: 'Information',
                                html: 'Please wait...',
                                timer: 2000,
                                timerProgressBar: true,
                                onBeforeOpen: () => {
                                    Swal.showLoading();
                                },
                                onClose: () => {

                                }
                            }).then((result) => {
                                console.log('I was closed by the timer')
                            });
                            jQuery.ajax({
                                type: 'post',
                                dataType: 'json',
                                url: ajax_url,
                                data: data,
                                success: function (data) {
                                    if (data.flag == 1) {
                                        Swal.fire(
                                            'Deleted!',
                                            'Your file has been deleted.',
                                            'success'
                                        );
                                        item_quo.remove();
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            'An error occurred! Please try again.',
                                            'error'
                                        );
                                    }
                                },
                                error: function (xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        error,
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
                $('.wrap-action-quotation .btn-load-cart').click(function () {
                    var id_cart = $(this).attr('rel');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Quotation will be deleted when items are loaded to cart. Do you want to continue?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes (Load to cart)',
                        cancelButtonText: 'No (Go back)'
                    }).then((result) => {
                        if (result.value) {
                            location.href = '<?php echo wc_get_cart_url(); ?>?cxecrt-retrieve-cart=' + id_cart;
                        }
                    });
                });
            }
            if ($('#tab-material_tab .ult-carousel-wrapper').length > 0) {
                var tu = ($('#tab-material_tab .ult-carousel-wrapper').width() - 20) / 3;
                $('#tab-material_tab .ult-carousel-wrapper').css('overflow', 'hidden');
                $('#tab-material_tab .ult-carousel-wrapper').children('div[class*="ult-carousel-"]').css('display', 'inline-flex');
                $('#tab-material_tab .ult-carousel-wrapper .ult-item-wrap').css({
                    'width': tu + 'px',
                    'height': tu + 'px',
                    'margin-right': '10px'
                });
            }
            if ($('.vc-printshop-testimonials .testimonial_single_big_thumb').length > 0) {
                var mySwiper = new Swiper('.vc-printshop-testimonials .testimonial_single_big_thumb .swiper-container', {
                    autoplay: {
                        delay: 5000,
                    },
                });
            }
            $(".submit-upload-design").click(function () {
                $('.single-product .single-product-wrap .wrap-price-pro').css('margin-top', '30px');
            });
            $('#tab-material_tab .material-item img').click(function () {
                $(this).parents('.wrap-material-tab').find('#large-thumb-material .vc_single_image-img').attr('src', $(this).attr('src'));
            });
            $('.single-product-wrap .nbdesigner_frontend_container').after($('.nbdq-add-a-quote'));
            if ($('.wc-minh-crs').length > 0) {
                $('.wc-minh-crs .products').addClass('owl-carousel');
                jQuery(".owl-carousel").owlCarousel({
                    loop: false,
                    margin: 0,
                    nav: true,
                    navText: ["<i class=\'fa fa-caret-left\'></i>", "<i class=\'fa fa-caret-right\'></i>"],
                    autoplay: true,
                    autoplayHoverPause: true,
                    responsive: {0: {items: 2}, 600: {items: 3}, 1000: {items: 4}}
                });
            }
            fix_block_intagram();
            fix_thumb_materialize_cat();
            if ($('body').hasClass('home')) {
                var f_tab = $('.vc-tab-product-wrapper ul.style-border_bottom li.active');
                var c_tab = $('.vc-tab-product-wrapper .vc-tab-product-content .tab-panel.panel-active');
                var limit = c_tab.siblings('.tab-panel').attr('data-limit');
                if (typeof limit !== "undefined") {
                    c_tab.attr('data-limit', limit);
                    setTimeout(function () {
                        f_tab.click();
                    }, 500);
                }
            }

            function convert_to_wc_price(price) {
                return accounting.formatMoney(price, {
                    symbol: "<?php echo get_woocommerce_currency_symbol(); ?>"
                });
            };
            if ($('body').hasClass('single-product')) {
                //jQuery('.single-product-wrap .entry-summary .wrap-price-pro').insertAfter('form.cart');
                if ($('.nbd-tb-options').length > 0) {
                    $('body').addClass('has-pringting-options');
                } else {
                    $('body').addClass('no-pringting-options');
                    $('.single-product-wrap input[name="quantity"]').change(() => {
                        var qty = $('.single-product-wrap input[name="quantity"]').val();
                        var price = convert_to_wc_price(qty * <?php echo $price; ?>);
                        var regular_price = convert_to_wc_price(qty * <?php echo $regular_price; ?>);
                        var price_include_tax = convert_to_wc_price(qty * <?php echo $price_include_tax; ?>);
                        $('.single-product-wrap .price ins .amount').html(price);
                        $('.single-product-wrap .price del .amount').html(regular_price);
                        $('.single-product-wrap .price-include-tax .amount').html(price_include_tax);
                    })
                }
            }
            $(window).resize(function () {
                fix_block_intagram();
                fix_thumb_materialize_cat();
                $(".products .product .pt-product-meta .product-image img.wp-post-image").each(function () {
                    "" != $(this).attr("style") && $(this).removeAttr("style");
                    var c = $(".products .product .pt-product-meta .product-image img.wp-post-image").width(),
                        a = $(".products .product .pt-product-meta .product-image img.wp-post-image").height();
                    1 >= a && ($(this).attr("width", $(this).parent("a").parent(".product-image").width()), setTimeout(function () {
                        $(this).attr("height",
                            $(this).parent("a").parent(".product-image").height());
                        c = $(".products .product .pt-product-meta .product-image img.wp-post-image").width();
                        a = $(".products .product .pt-product-meta .product-image img.wp-post-image").height()
                    }, 500));
                    1 < a && ($(this).css({height: a + 3, width: c}), $(this).attr({height: a + 3, width: c}))
                })
            });
            setTimeout(function () {
                var c = 1, a = 1;
                setInterval(function () {
                    var b = $(".products .product .pt-product-meta .product-image img.wp-post-image").width(),
                        d = $(".products .product .pt-product-meta .product-image img.wp-post-image").height();
                    $(".products .product .pt-product-meta .product-image img.wp-post-image").each(function () {
                        1 < b && 25 < d ? 1 >= $(this).attr("width") && ("" != $(this).attr("style") && $(this).removeAttr("style"), 1 >= $(this).attr("height") && ($(this).attr("width", $(this).parent("a").parent(".product-image").width()),
                            setTimeout(function () {
                                $(this).attr("height", $(this).parent("a").parent(".product-image").height());
                                b = $(".products .product .pt-product-meta .product-image img.wp-post-image").width();
                                d = $(".products .product .pt-product-meta .product-image img.wp-post-image").height()
                            }, 500)), 1 < d && (b > c && (c = b), d > a && (a = d), $(this).css({
                            height: d,
                            width: b
                        }), $(this).attr({height: d, width: b}))) : 25 < a && $(this).attr({height: a, width: c})
                    })
                }, 1500);
                fix_position_color_option();
                setTimeout(function () {
                    $(".ldmncart").remove();
                    var b = 0;
                    setInterval(function () {
                        0 <
                        jQuery(".nbd-option-wrapper .nbo-invalid-option.active").length && jQuery(".nbd-option-wrapper .nbo-invalid-option.active").each(function () {
                            console.log(jQuery(this).parents(".nbd-field-content").find("select").val());
                            jQuery(this).parents(".nbd-field-content").find("select").val(b).change();
                            0 < jQuery(".nbd-option-wrapper .nbo-invalid-option.active").length && b++
                        })
                    }, 300)
                }, 2E3)
            }, 1E3);
//            $(document).on('mouseenter', '.products .product .pt-product-meta .nbo-swatches-wrap .nbo-swatch-wrap .nbo-swatch', function () {
//                var pro_img = $(this).parents('.pt-product-meta').find('.product-image');
//                var pai = pro_img.find('a.start-design').attr('data-pai');
//                var img = $(this).attr('data-src');
//                var check = pro_img.attr('data-org');
//                if (typeof check !== typeof undefined && check !== false) {
//
//                } else {
//                    pro_img.attr('data-org', pro_img.find('img').attr('src'));
//                }
//                pro_img.find('img').removeAttr('srcset');
//                // console.log(pro_img.find('img').attr('src'));
//                pro_img.find('img').attr('src', img);
//                if (pai != '') {
//                    $(this).removeAttr('srcset');
//                    $(this).attr('src', pai);
//                }
//            });
            $(document).on('mouseenter', '.products .product .pt-product-meta .nbo-swatches-wrap .nbo-swatch-hover', function () {
                $(this).parents('.nbo-archive-swatches-wrap').addClass('hover');
                $(this).addClass('hover');
                var pro_img = $(this).parents('.pt-product-meta').find('.product-image');
                var pai = pro_img.find('a.start-design').attr('data-pai');
//                var img = $(this).find('.nbo-swatch').attr('data-src');
                var img = $(this).attr('data-src');
                var check = pro_img.attr('data-org');
                if (typeof check !== typeof undefined && check !== false) {

                } else {
                    pro_img.attr('data-org', pro_img.find('img').attr('src'));
                }
                pro_img.find('img').removeAttr('srcset');
                // console.log(pro_img.find('img').attr('src'));
                pro_img.find('img').attr('src', img);
                if (pai != '') {
                    $(this).removeAttr('srcset');
                    $(this).attr('src', pai);
                }
            });
            $(document).on('mouseleave', '.products .product .pt-product-meta .nbo-swatches-wrap .nbo-swatch-hover', function () {
                $(this).parents('.nbo-archive-swatches-wrap').removeClass('hover');
                $(this).removeClass('hover');
                var pro_img = $(this).parents('.pt-product-meta').find('.product-image');
                pro_img.find('img.wp-post-image').attr('src', pro_img.attr('data-org'));
            });
            $(document).on('mouseleave', '.products .product .pt-product-meta', function () {
                var pro_img = $(this).find('.product-image');
                pro_img.find('img.wp-post-image').attr('src', pro_img.attr('data-org'));
            });
            $(".products .product .pt-product-meta .nbo-swatches-wrap .nbo-swatch-wrap").mouseenter(function () {
                var pro_img = $(this).parents('.pt-product-meta').find('.product-image');
                var pai = pro_img.find('a.start-design').attr('data-pai');
                var img = $(this).attr('src');
                var check = pro_img.attr('data-org');
                if (typeof check !== typeof undefined && check !== false) {

                } else {
                    pro_img.attr('data-org', img);
                }
                if (pai != '') {
                    $(this).removeAttr('srcset');
                    $(this).attr('src', pai);
                }
            }).mouseleave(function () {
                var pro_img = $(this).parents('.product-image');
                pro_img.find('img.wp-post-image').attr('src', pro_img.attr('data-org'));
            });
            $(".product_cat-printing-products .pt-product-meta .product-image").mouseenter(function () {
                var pro_img = $(this);
                var pai = pro_img.find('a.start-design').attr('data-pai');
                var img = pro_img.find('img.wp-post-image');
                var check = pro_img.attr('data-org');
                if (typeof check !== typeof undefined && check !== false) {

                } else {
                    pro_img.attr('data-org', img.attr('src'));
                }
                if (pai != '') {
                    img.removeAttr('srcset');
                    img.attr('src', pai);
                }
            });
            jQuery('body').on('click', '.nbo-archive-swatches-wrap .nbo-swatches-wrap .nbo-swatch-wrap .nbo-swatch', function (event) {
                //var pro_img = $(this).parents('.product-image');
                var pro_img = $(this).parents('.pt-product-meta').find('.product-image');
                var src = $(this).attr('data-src');
                if (src != '') {
                    pro_img.find('img.wp-post-image').attr('src', src).removeAttr('srcset');
                }
            });
            jQuery('body').on('click', '.wc-proceed-to-checkout .btn-generate-quotation', function (event) {
                $(this).addClass('cxecrt-button-loading');
                var data = {
                    'action': 'save_cart_and_get_link_ajax',
                    'cxecrt-landing-page': 'cart',
                    'ship_method': jQuery('input.shipping_method:checked').val()
                };
                jQuery.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_url,
                    data: data,
                    success: function (data) {
                        // console.log(data.cart_url);
                        sessionStorage.setItem("quotation_cart", data.cart_id);
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        alert(error);
                    }
                });
            });
            $('.nbo-archive-swatches-wrap .nbo-swatches-wrap .nbo-swatch-wrap .nbo-swatch').hover(function () {
                var pro_img = $(this).parents('.product-image');
                var src = $(this).attr('data-src');
                if (src != '') {
                    pro_img.find('img.wp-post-image').attr('src', src);
                }
            }, function () {
                //            var pro_img = $(this).parents('.product-image');
                //            pro_img.find('img.wp-post-image').attr('src', pro_img.attr('data-org'));
            });
            $(".product_cat-printing-products .pt-product-meta .product-image").mouseleave(function () {
                var pro_img = $(this);
                pro_img.find('img.wp-post-image').attr('src', pro_img.attr('data-org'));
            });
            if ($('.widget_printshopcustomcate_widget .widget-title').length > 0) {
                //                var pp = jQuery('.woocommerce-breadcrumb a:nth-child(3)').text();
                //                if (pp != '') {
                //                    $('.widget_printshopcustomcate_widget .widget-title').text(pp);
                //                }
                $('.widget_printshopcustomcate_widget .widget-title').click(function () {
                    $(this).toggleClass('closed');
                    $('.widget_printshopcustomcate_widget ul.product_categories').toggle();
                });
            }

            function fix_block_intagram() {
                var w = window.screen.width;
                //                console.log(w);
                if (w < 768) {
                    $('body').addClass('minh-mobile');
                    $('.nb-instar .col-sm-4:nth-child(7),.nb-instar .col-sm-4:nth-child(8)').hide();
                } else {
                    $('body').removeClass('minh-mobile');
                    $('.nb-instar .col-sm-4:nth-child(7),.nb-instar .col-sm-4:nth-child(8)').show();
                }
            }

            function fix_position_color_option() {
                $(".products .product .product-action").each(function () {
                    var swco = $(this).find('.nbo-archive-swatches-wrap');
                    if (swco.length > 0) {
                        $(this).parent('.product-image').after(swco.clone());
                        swco.remove();
                    }
                });
            }

            function fix_thumb_materialize_cat() {
                var max_w = max_h = 0;
                $(".archive.tax-matealize_cat .product-image img").each(function () {
                    if ($(this).attr('style') != '') {
                        $(this).removeAttr('style');
                    }
                    var w = $(this).width();
                    var h = $(this).height();
                    if (h != 0 && h == w) {
                        if (w > max_w) {
                            max_w = w;
                            max_h = h;
                        }
                    }
                    if (max_h != 0) {
                        $(this).css({'height': max_h, 'width': max_w});
                    }
                });
            }

        });</script>
    <?php
}

if (function_exists('acf_add_options_page')) {
    $option_page = acf_add_options_page(array(
        'page_title' => __('FAQ Tab'),
        'menu_title' => __('FAQ Tab'),
        'menu_slug' => 'tab-faqs',
        'capability' => 'edit_posts',
        'redirect' => false,
        'parent_slug' => 'EWD-UFAQ-Options',
    ));
    $option_page2 = acf_add_options_page(array(
        'page_title' => __('Issue with product artwork'),
        'menu_title' => __('Issue with product artwork'),
        'menu_slug' => 'issue-artwork',
        'capability' => 'edit_posts',
        'redirect' => false,
        'parent_slug' => 'edit.php?post_type=product',
    ));
}

if (function_exists('acf_add_local_field_group')):

    acf_add_local_field_group(array(
        'key' => 'group_5e53494db3d87',
        'title' => 'Product additional image',
        'fields' => array(
            array(
                'key' => 'field_5e53a8c7783fd',
                'label' => '',
                'name' => 'product_additional_image',
                'type' => 'image',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
            array(
                'key' => 'field_5e578d07c0fb5',
                'label' => 'Materials',
                'name' => 'materials',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'materials',
                ),
                'taxonomy' => '',
                'allow_null' => 0,
                'multiple' => 1,
                'return_format' => 'id',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5e71de2683f1f',
                'label' => 'Print template',
                'name' => 'print_template',
                'type' => 'post_object',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'field-print-template',
                    'id' => '',
                ),
                'post_type' => array(
                    0 => 'print_templates',
                ),
                'taxonomy' => '',
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'id',
                'ui' => 1,
            ),
            array(
                'key' => 'field_5e71e0eb83f20',
                'label' => 'Installation Guide',
                'name' => 'installation_guide',
                'type' => 'file',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => 'field-print-template',
                    'id' => '',
                ),
                'return_format' => 'url',
                'library' => 'all',
                'min_size' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'product',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'side',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
    acf_add_local_field_group(array(
        'key' => 'group_5e563d98d99bb',
        'title' => 'Material',
        'fields' => array(
            array(
                'key' => 'field_5e563dc80039c',
                'label' => 'Material code',
                'name' => 'material_code',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_5e576258b1f70',
                'label' => 'Store URL',
                'name' => 'store_url',
                'type' => 'url',
                'instructions' => 'URL of material purchase page',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5e563ee00039d',
                'label' => 'Images',
                'name' => 'images',
                'type' => 'gallery',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'url',
                'preview_size' => 'medium',
                'insert' => 'append',
                'library' => 'all',
                'min' => '',
                'max' => '',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
            array(
                'key' => 'field_5e563fc90039e',
                'label' => 'Files',
                'name' => 'files',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => 'field_5e5640450039f',
                'min' => 0,
                'max' => 0,
                'layout' => 'row',
                'button_label' => 'Add File',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5e5640450039f',
                        'label' => 'File Upload',
                        'name' => 'file_upload',
                        'type' => 'file',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'library' => 'all',
                        'min_size' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                ),
            ),
            // custom botak Phase 4  no.7
            array(
                'key' => 'field_5e563fc90039g',
                'label' => 'Price',
                'name' => 'material_price',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => 'field_5e5640450039h',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5e5640450039h',
                        'label' => 'Role',
                        'name' => 'role',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '30',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => 'wppb-roles-editor',
                        'taxonomy' => '',
                        'filters' => '',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'array',
                    ),
                    array(
                        'key' => 'field_5e5640450039j',
                        'label' => 'Price',
                        'name' => 'price',
                        'type' => 'number',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'library' => 'all',
                        'min_size' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'materials',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_5e5ca953c58d9',
        'title' => 'Print Templates',
        'fields' => array(
            array(
                'key' => 'field_5e5ca98aee979',
                'label' => 'Short description',
                'name' => 'short_description',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => '',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_5e5ca9ffee97a',
                'label' => 'Link',
                'name' => 'link',
                'type' => 'url',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5e5caa1dee97b',
                'label' => 'File Upload',
                'name' => 'file_upload',
                'type' => 'file',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'url',
                'library' => 'all',
                'min_size' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'print_templates',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    /* our_works */
    acf_add_local_field_group(array(
        'key' => 'group_our_works',
        'title' => 'Work',
        'fields' => array(
            array(
                'key' => 'field_5e563ee00039d',
                'label' => 'Images',
                'name' => 'images',
                'type' => 'gallery',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'url',
                'preview_size' => 'medium',
                'insert' => 'append',
                'library' => 'all',
                'min' => '',
                'max' => '',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'works',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_5e79d541cfe26',
        'title' => 'User',
        'fields' => array(
            array(
                'key' => 'field_5e79d59463a04',
                'label' => 'Specialist',
                'name' => 'specialist',
                'type' => 'user',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '30',
                    'class' => '',
                    'id' => '',
                ),
                'role' => array(
                    0 => 'specialist',
                ),
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'id',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'user_form',
                    'operator' => '==',
                    'value' => 'edit',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_5e94218db41f9',
        'title' => 'FAQ Tab',
        'fields' => array(
            array(
                'key' => 'field_5edddc695f80b',
                'label' => 'Default tab index number',
                'name' => 'default_tab_index_number',
                'type' => 'number',
                'instructions' => 'The starting index number is 1',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '25%',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 2,
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => 1,
                'max' => 5,
                'step' => 1,
            ),
            array(
                'key' => 'field_5e94314e3bd13',
                'label' => 'FAQ Tab',
                'name' => 'faq_tab',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => 'field_5e9431b83bd15',
                'min' => 0,
                'max' => 5,
                'layout' => 'row',
                'button_label' => 'Add Tab',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5e94318d3bd14',
                        'label' => 'Icon',
                        'name' => 'icon',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ),
                    array(
                        'key' => 'field_5e9431b83bd15',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_5e9431c63bd16',
                        'label' => 'Choose parent category in tab',
                        'name' => 'choose_categories',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'ufaq-category',
                        'field_type' => 'checkbox',
                        'add_term' => 1,
                        'save_terms' => 1,
                        'load_terms' => 0,
                        'return_format' => 'object',
                        'multiple' => 0,
                        'allow_null' => 0,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'tab-faqs',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_5eba4fc68643d',
        'title' => 'Options layout',
        'fields' => array(
            array(
                'key' => 'field_5eba50175c24c',
                'label' => 'Options layout',
                'name' => 'options_layout',
                'type' => 'select',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    0 => 'Standard Layouts',
                    1 => 'Popular products & subcategories layout',
                ),
                'default_value' => array(
                    0 => 0,
                ),
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 0,
                'return_format' => 'value',
                'ajax' => 0,
                'placeholder' => '',
            ),
            array(
                'key' => 'field_5eba51055c24d',
                'label' => 'Show popular products',
                'name' => 'show_popular_products',
                'type' => 'radio',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_5eba50175c24c',
                            'operator' => '==contains',
                            'value' => '1',
                        ),
                    ),
                ),
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'choices' => array(
                    'y' => 'Yes',
                    'n' => 'No',
                ),
                'allow_null' => 0,
                'other_choice' => 0,
                'default_value' => 'y',
                'layout' => 'horizontal',
                'return_format' => 'value',
                'save_other_choice' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'product_cat',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'seamless',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_5eca1fdc7157e',
        'title' => 'Issue with product artwork',
        'fields' => array(
            array(
                'key' => 'field_5eca204439dc8',
                'label' => 'List issue',
                'name' => 'list_issue',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => 'field_5eca210f39dc9',
                'min' => 0,
                'max' => 0,
                'layout' => 'row',
                'button_label' => 'Add Issue',
                'sub_fields' => array(
                    array(
                        'key' => 'field_5eca210f39dc9',
                        'label' => 'Issue',
                        'name' => 'issue',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'issue-artwork',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));

endif;

if (!function_exists('create_post_type')):

    function create_post_type()
    {
        register_post_type('materials', // change the name
            array(
                'labels' => array(
                    'name' => __('Materials'), // change the name
                    'singular_name' => __('materials'), // change the name
                ),
                'public' => true,
                'supports' => array('title', 'editor', 'custom-fields', 'page-attributes'), // do you need all of these options?
                'taxonomies' => array('matealize_cat', 'matealize_attributes'), // do you need categories and tags?
                'hierarchical' => true,
                'menu_icon' => 'dashicons-editor-removeformatting',
                'rewrite' => array('slug' => __('materials')), // change the name
            )
        );

        register_post_type('print_templates', // change the name
            array(
                'labels' => array(
                    'name' => __('Print Templates'), // change the name
                    'singular_name' => __('print_templates'), // change the name
                ),
                'public' => true,
                'supports' => array('title', 'editor', 'custom-fields'), // do you need all of these options?
                'taxonomies' => array('print_templates_cat'), // do you need categories and tags?
                'hierarchical' => true,
                'menu_icon' => 'dashicons-buddicons-topics',
                'rewrite' => array('slug' => __('print-templates-cat')), // change the name
            )
        );

        /* our_works */
        register_post_type('works', // change the name
            array(
                'labels' => array(
                    'name' => __('Our works'), // change the name
                    'singular_name' => __('work'), // change the name
                ),
                'public' => true,
                'supports' => array('title', 'editor', 'custom-fields', 'page-attributes'), // do you need all of these options?
                'taxonomies' => array('works-cat', 'works_attributes'), // do you need categories and tags?
                'hierarchical' => true,
                'menu_icon' => 'dashicons-calendar-alt',
                'rewrite' => array('slug' => __('our-works')), // change the name
            )
        );
    }

    add_action('init', 'create_post_type');

endif;

function create_matealize_attributes()
{
    $labels = array(
        'name' => 'Categories',
        'singular' => 'Category',
        'menu_name' => 'Categories',
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('matealize_cat', 'materials', $args);

    $labels2 = array(
        'name' => 'Attributes',
        'singular' => 'Attribute',
        'menu_name' => 'Attributes',
    );
    $args2 = array(
        'labels' => $labels2,
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('matealize_attributes', 'materials', $args2);

    $labels3 = array(
        'name' => 'Categories',
        'singular' => 'Category',
        'menu_name' => 'Categories',
    );
    $args3 = array(
        'labels' => $labels3,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('print_templates_cat', 'print_templates', $args3);

    /* our_works */
    $labels4 = array(
        'name' => 'Categories',
        'singular' => 'Category',
        'menu_name' => 'Categories',
    );
    $args4 = array(
        'labels' => $labels4,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => true,
    );
    register_taxonomy('works-cat', 'works', $args4);
}

// Hook into the 'init' action
add_action('init', 'create_matealize_attributes', 0);

add_action('admin_head', 'my_custom_afrs');

function my_custom_afrs()
{
    echo '<style>
        #acf-group_5e53494db3d87 .acf-field-5e578d07c0fb5, .field-print-template {
        display: none;
        }
        .wp-list-table th#cart_retrieved {width: auto;}
        .wp-list-table .column-cart_no .button {
        padding: 0 5px;
        width: 28px;
        text-align: center;
        }
        .acf-field-5e71e0eb83f20 {
        position: relative;
        top: 20px;
        }
        #custom_guidelines_tab .field-print-template {
        display: block;
        }
        .hdlh_mtd #adminmenu li:not(#toplevel_page_mybts-order_dashboard):not(#menu-posts-stored-carts):not(#menu-users):not(#toplevel_page_mybts-vue_order_dashboard),
        .hdlh_mtd.index-php #wpbody, .hdlh_mtd.index-php #wpfooter,
        .hdlh_mtd #wpadminbar #wp-admin-bar-comments, .hdlh_mtd #wpadminbar #wp-admin-bar-new-content, .hdlh_mtd #wpadminbar #wp-admin-bar-dokan {
        display: none;
        }
        
        tr.type-stored-carts.disable-action {
        opacity: 0.5;
        pointer-events: none;
        }
        </style>';
}

add_filter('admin_body_class', 'my_admin_body_class');

function my_admin_body_class($classes)
{
    $user = get_userdata(get_current_user_id());
    if ($user->roles[0] == 'specialist' || $user->roles[0] == 'customer_service' || $user->roles[0] == 'production') {
        $classes .= ' hdlh_mtd';
    }
    return "$classes";
}

add_action('admin_footer', 'my_custom_field_js');

function my_custom_field_js()
{
    $currentScreen = get_current_screen();
    $user = get_userdata(get_current_user_id());
    $key_ss = 'ss_wam_' . get_current_user_id();
    if (false === (get_transient($key_ss))) {
        // It wasn't there, so regenerate the data and save the transient
    } else {
        $arr_temp = explode('|', get_transient($key_ss));
        if (count($arr_temp) == 2) {
            update_post_meta($arr_temp[0], '_wp_attachment_metadata', $arr_temp[1]);
        }
        delete_transient($key_ss);
    }
    ?>
    <script type="text/javascript">
        var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
        var admin_url = '<?php echo get_admin_url(); ?>';
        // if (jQuery('body').hasClass('toplevel_page_mybts-order_dashboard')) {
        //     jQuery("#order-dashboard-table").FullTable({
        //         "editable": false,
        //         "filterable": true,
        //         "orderable": true,
        //         "selectable": false,
        //         "on": {
        //             "update": function () {

        //             }
        //         }
        //     });
        // }

        if (jQuery('body').hasClass('hdlh_mtd') && !(jQuery('body').hasClass('toplevel_page_mybts-order_dashboard') || jQuery('body').hasClass('post-type-stored-carts') || jQuery('body').hasClass('users-php') || jQuery('body').hasClass('profile-php') || jQuery('body').hasClass('index-php') || jQuery('body').hasClass('toplevel_page_mybts-vue_order_dashboard'))) {
            jQuery('body').attr('id', 'error-page').html('<div class="wp-die-message">Sorry, you are not allowed to access this page.</div>');
        }

        jQuery(document).ready(function () {
            setTimeout(function () {
                0 < jQuery("#wpb_visual_composer").length && setInterval(function () {
                        var b = jQuery(".vc_ui-panel.vc_active");
                        if (0 < b.length) if ("bsf-info-box" == b.attr("data-vc-shortcode")) {
                            var c = b.find('input[name="el_class"]').val();
                            if ("" != c && "hcm" != b.find('input[name="el_class"]').attr("rel")) try {
                                var d = atob(c);
                                b.find('div[data-vc-shortcode-param-name="el_class"]').hide();
                                "none" != d && (b.find('.smile_icon li[data-icons="' + d + '"]').click(), b.find('input[name="el_class"]').attr("rel", "hcm"))
                            } catch (e) {
                            }
                        } else {
                            c =
                                b.find(".gallery_widget_attached_images_list li img").attr("src");
                            var a = b.find('.vc_edit_form_elements input[name="img_size"]').val();
                            "vc_single_image" == b.attr("data-vc-shortcode") && "undefined" !== typeof a && "" != c && ("thumbnail" == a && (a = "150x150"), "medium" == a && (a = "300x300"), "large" == a && (a = "1024x1024"), "full" == a && jQuery("<img>").attr("src", c).load(function () {
                                a = this.width + "x" + this.height
                            }), -1 != a.indexOf("x") && (b.find('.vc_edit_form_elements input[name="el_id"]').val(btoa(c + "|" + a)), b.find('div[data-vc-shortcode-param-name="el_id"]').hide()))
                        }
                    },
                    1E3)
            }, 2E3);
            jQuery("body").on("click", ".smile_icon li", function () {
                jQuery('.vc_ui-panel.vc_active[data-vc-shortcode="bsf-info-box"]').find('input[name="el_class"]').val(btoa(jQuery(this).attr("data-icons")))
            });
            jQuery("body").on("click", "#wrap-media-list-remove .thumb-default", function () {
                jQuery(this).toggleClass("active")
            });
            jQuery("body").on("click", "#wrap-media-list-remove button#btn-delete-all", function () {
                if (0 == jQuery("#wrap-media-list-remove .thumb-default.active").length) alert("No files have been selected."); else {
                    jQuery("#wrap-media-list-remove").css({"pointer-events": "none", opacity: "0.5"});
                    jQuery(this).text("Processing . . . please wait");
                    try {
                        var b = atob(jQuery('input[name="gallery_ids"]').val()).split(",");
                        if (0 < b.length) {
                            var c = [];
                            jQuery("#block-media-s3 figure.gallery-item").each(function (a, e) {
                                jQuery(this).find(".thumb-default").hasClass("active") &&
                                (console.log(a), c.push(b[a]))
                            });
                            console.log(c);
                            if (0 < c.length) {
                                var d = {action: "synch_s3_delete_media_ajax", data_ids: c.join()};
                                jQuery.ajax({
                                    type: "post",
                                    dataType: "json",
                                    url: ajax_url,
                                    data: d,
                                    success: function (a) {
                                        jQuery("#wrap-media-list-remove").removeAttr("style");
                                        jQuery("#wrap-media-list-remove").html("Deleted all selected media!")
                                    },
                                    error: function (a, e, f) {
                                        alert(f)
                                    }
                                })
                            }
                        }
                    } catch (a) {
                        console.log(a.message)
                    }
                }
            });
            jQuery("body").on("click", "#wrap-media-list-remove button#btn-select-all", function () {
                jQuery(this).hasClass("deselect-all") ? (jQuery(this).removeClass("deselect-all").addClass("select-all"), jQuery(this).text("Select All"), jQuery("#wrap-media-list-remove .thumb-default").removeClass("active")) : (jQuery(this).removeClass("select-all").addClass("deselect-all"), jQuery(this).text("Deselect All"), jQuery("#wrap-media-list-remove .thumb-default").addClass("active"))
            });
            jQuery("body").on("click", "#btn-s3-export-f2", function () {
                jQuery.ajax({
                    type: "post",
                    dataType: "html",
                    url: ajax_url,
                    data: {action: "synch_s3_delete_f2_ajax"},
                    success: function (b) {
                        localStorage.removeItem("synch_s3_folder");
                        localStorage.removeItem("synch_s3_folder_path");
                        window.location.hash = "Home";
                        "" != b && (jQuery("#wrap-media-list-remove").html(b), jQuery("#wrap-media-list-remove img.attachment-thumbnail.size-thumbnail").each(function (c, d) {
                            var a = jQuery(this);
                            a.after('<p class="filename">' + a.attr("src").split("/").pop() +
                                "</p>");
                            var e = a.parent(".gallery-icon");
                            e.attr("title", a.attr("src"));
                            e.addClass("thumb-default");
                            a.attr("src", location.origin + "/wp-includes/images/media/default.png")
                        }))
                    },
                    error: function (b, c, d) {
                        alert(d)
                    }
                })
            });
            jQuery("body").on("click", "#btn-s3-synch-f2", function () {
                if (0 < jQuery(".filemanager .data li").length) {
                    var b = jQuery(this);
                    b.css("pointer-events", "none");
                    b.addClass("active");
                    b.find("span").text("Please wait...");
                    var c = [], d = !1, a = [], e = [];
                    jQuery(".filemanager .data li").each(function (f, l) {
                        var m = jQuery(this).attr("class"), h = jQuery(this).find("a").attr("title"),
                            g = jQuery(this).find("a").attr("href");
                        c.push([m, h, g]);
                        "folders" == m && (d = !0, a.push(h), e.push(g))
                    });
                    d && (localStorage.setItem("synch_s3_folder", a), localStorage.setItem("synch_s3_folder_path",
                        e), localStorage.setItem("synch_s3_folder_exc", e));
                    synch_file_s3(c)
                }
            });

            function synch_folder_s3(b) {
                window.location.hash = b;
                var c = [], d = !1, a = localStorage.getItem("synch_s3_folder").split(","),
                    e = localStorage.getItem("synch_s3_folder_path").split(","),
                    f = localStorage.getItem("synch_s3_folder_exc").split(",");
                setTimeout(function () {
                    jQuery(".files-div .filemanager .data li").each(function (l, m) {
                        var h = jQuery(this).attr("class"), g = jQuery(this).find("a").attr("title"),
                            k = jQuery(this).find("a").attr("href");
                        c.push([h, g, k]);
                        "folders" == h && -1 == f.indexOf(k) && (d = !0, -1 == a.indexOf(g) && a.push(g),
                        -1 == e.indexOf(k) && e.push(k))
                    });
                    d && (localStorage.setItem("synch_s3_folder", a), localStorage.setItem("synch_s3_folder_path", e));
                    "" != window.location.hash && "#Home" != window.location.hash && "#" != window.location.hash ? synch_file_s3(c) : (localStorage.removeItem("synch_s3_folder"), localStorage.removeItem("synch_s3_folder_path"), window.location.hash = "Home", exec_button_synch_s3())
                }, 500)
            }

            function synch_file_s3(b) {
                0 < b.length ? jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: ajax_url,
                    data: {
                        action: "synch_s3_files_ajax",
                        data: b,
                        wlh: "" != window.location.hash ? window.location.hash : "#Home"
                    },
                    success: function (c) {
                        try {
                            var d = localStorage.getItem("synch_s3_folder").split(","),
                                a = localStorage.getItem("synch_s3_folder_path").split(","),
                                e = localStorage.getItem("synch_s3_folder_exc").split(",");
                            if (0 < a.length) {
                                var f = a[0];
                                "" == f && 1 < a.length && (f = a[1], d.splice(1, 1), a.splice(1, 1));
                                0 < e.length && -1 == e.indexOf(f) && (e.push(f),
                                    localStorage.setItem("synch_s3_folder_exc", e));
                                d.splice(0, 1);
                                a.splice(0, 1);
                                localStorage.setItem("synch_s3_folder", d);
                                localStorage.setItem("synch_s3_folder_path", a);
                                synch_folder_s3(f)
                            } else localStorage.removeItem("synch_s3_folder"), localStorage.removeItem("synch_s3_folder_path"), window.location.hash = "Home", exec_button_synch_s3()
                        } catch (l) {
                            console.log(l), localStorage.removeItem("synch_s3_folder"), localStorage.removeItem("synch_s3_folder_path"), window.location.hash = "Home", exec_button_synch_s3()
                        }
                    },
                    error: function (c,
                                     d, a) {
                        alert(a)
                    }
                }) : (localStorage.removeItem("synch_s3_folder"), localStorage.removeItem("synch_s3_folder_path"), window.location.hash = "Home", exec_button_synch_s3())
            }

            function exec_button_synch_s3() {
                var b = jQuery("#btn-s3-synch-f2");
                b.removeClass("active");
                b.find("span").text("Import Completed");
                b.removeAttr("style");
                jQuery.ajax({
                    type: "post",
                    dataType: "html",
                    url: ajax_url,
                    data: {action: "delete_all_transient_files_ajax"},
                    success: function (c) {
                        console.log("Success!")
                    },
                    error: function (c, d, a) {
                        alert(a)
                    }
                })
            };

            <?php if($currentScreen->id == "woocommerce_page_wc-settings") { ?>
            var arr_sm = '<?php echo json_encode(maybe_unserialize(get_option('woocommerce_shipping_duration'))); ?>';
            jQuery('body').on('click', '.wc-shipping-zone-method-title .wc-shipping-zone-method-settings', function () {
                setTimeout(function () {
                    jQuery('.wc-backbone-modal .wc-backbone-modal-content article.wc-modal-shipping-method-settings .form-table tbody').append('<tr valign="top"> <th scope="row" class="titledesc"> <label for="woocommerce_shipping_duration">Shipping duration</label> </th> <td class="forminp"> <fieldset> <legend class="screen-reader-text"><span>Cost</span></legend> <input class="input-text regular-input " type="text" name="woocommerce_shipping_duration" id="woocommerce_shipping_duration" style="" value="" placeholder="hour"> </fieldset> </td> </tr>');
                    try {
                        var obj = JSON.parse(arr_sm);
                        for (var key in obj) {
                            if (key == 'wsd_' + jQuery('.wc-backbone-modal input[name="instance_id"]').val()) {
                                jQuery('.woocommerce table.form-table input[name="woocommerce_shipping_duration"]').val(obj[key]);
                            }
                        }
                    } catch (err) {
                        console.log(err.message);
                    }
                }, 100);
            });
            jQuery('body').on('change', 'input[name="woocommerce_shipping_duration"]', function () {
                try {
                    var obj = JSON.parse(arr_sm);
                    for (var key in obj) {
                        if (key == 'wsd_' + jQuery('.wc-backbone-modal input[name="instance_id"]').val()) {
                            obj[key] = jQuery('.woocommerce table.form-table input[name="woocommerce_shipping_duration"]').val();
                        }
                    }
                    arr_sm = JSON.stringify(obj);
                    // console.log(arr_sm);
                } catch (err) {
                    console.log(err.message);
                }
            });
            <?php } ?>

            jQuery('body').on('change', '.nbd-enable-attribute-con input[ng-model="op.enable_con"]', function () {
                if (jQuery(this).is(':checked')) {
                    var nfw = jQuery(this).parents('.nbd-field-wrap');
                    var type = parseInt(nfw.find('.nbo-type-label').text());
                    var arr_type = [3, 4, 15];
                    if (arr_type.indexOf(type) > -1) {
                        jQuery('.nbd-subattributes-wrapper select[ng-model="op.con_show"]').val('y');
                    }
                }
            });

            if (jQuery('#acf-field_5e79d59463a04').length > 0) {
                setTimeout(function () {
                    jQuery('#acf-field_5e79d59463a04').siblings('.select2-container').css('width', '30%');
                }, 1000);
                <?php if (!current_user_can('administrator')) { ?>
                jQuery('.form-table tr.acf-field-5e79d59463a04').remove();
                <?php } ?>
            }

            jQuery('.hdlh_mtd #adminmenu li').not('#toplevel_page_mybts-order_dashboard, #menu-posts-stored-carts, #menu-users, #toplevel_page_mybts-vue_order_dashboard').remove();
            jQuery('.hdlh_mtd #wp-admin-bar-root-default li').not('#wp-admin-bar-menu-toggle, #wp-admin-bar-wp-logo, #wp-admin-bar-site-name').remove();
            jQuery('.toplevel_page_mybts-order_dashboard .paginate_od .page-numbers').addClass('button');

            jQuery('body').on('click', '.all-option-expand .btn-expand', function (event) {
                if (jQuery(this).hasClass('active')) {
                    jQuery(this).siblings('.ao-expand').addClass('more');
                    jQuery(this).removeClass('active');
                } else {
                    jQuery(this).addClass('active');
                    jQuery(this).siblings('.ao-expand').removeClass('more');
                }
            });

            if (jQuery('body').hasClass('toplevel_page_mybts-order_dashboard') && jQuery('#order-dashboard-table th[fulltable-field-name="collection"]').length > 0) {
                var window_focus;
                jQuery(window).focus(function () {
                    window_focus = true;
                }).blur(function () {
                    window_focus = false;
                });

                function checkReload() {
                    if (!window_focus) {
                        location.reload(); // if not focused, reload
                    }
                }

                setInterval(checkReload, 300000); // check if not focused, every 5 minutes

                jQuery('body').on('click', '.btn-signature', function (event) {
                    Swal.fire({
                        title: 'Order #' + jQuery(this).attr('rel') + '<span id="sig-clearBtn"><img width="30" alt="" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBzdGFuZGFsb25lPSJubyI/Pgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDIwMDEwOTA0Ly9FTiIKICJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy1TVkctMjAwMTA5MDQvRFREL3N2ZzEwLmR0ZCI+CjxzdmcgdmVyc2lvbj0iMS4wIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiB3aWR0aD0iOTMuMDAwMDAwcHQiIGhlaWdodD0iODcuMDAwMDAwcHQiIHZpZXdCb3g9IjAgMCA5My4wMDAwMDAgODcuMDAwMDAwIgogcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCI+CjxtZXRhZGF0YT4KQ3JlYXRlZCBieSBwb3RyYWNlIDEuMTAsIHdyaXR0ZW4gYnkgUGV0ZXIgU2VsaW5nZXIgMjAwMS0yMDExCjwvbWV0YWRhdGE+CjxnIHRyYW5zZm9ybT0idHJhbnNsYXRlKDAuMDAwMDAwLDg3LjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSIKZmlsbD0iIzAwMDAwMCIgc3Ryb2tlPSJub25lIj4KPHBhdGggZD0iTTM1OCA1NTIgYy0xOTAgLTE5MCAtMTkwIC0xODkgLTk4IC0yNzYgMzMgLTMyIDYwIC02MiA2MCAtNjggMCAtNgotMjcgLTkgLTcyIC03IC02MyA0IC03MyAyIC03NiAtMTMgLTMgLTE3IDExIC0xOCAxODUgLTE4IGwxODggMCAxMjggMTI4IGMxNjUKMTY1IDE2NCAxNTMgMTYgMzAxIC05NSA5NSAtMTE1IDExMSAtMTQyIDExMSAtMjggMCAtNTIgLTIxIC0xODkgLTE1OHogbTMzMAotMTQ1IGwtNTMgLTQ5IC00MCA0NSBjLTIyIDI0IC02NCA2NyAtOTMgOTQgbC01MyA0OSA0NyA1MiA0OCA1MyA5OCAtOTggOTkKLTk4IC01MyAtNDh6IG0tMTIxIC0zMyBsNDUgLTQwIC00OSAtNTIgYy00OCAtNTEgLTUwIC01MiAtMTA4IC01MiBsLTU5IDAgLTY4CjY3IC02NyA2NyA4MSA3OSA4MSA3OSA0OSAtNTQgYzI3IC0yOSA3MCAtNzEgOTUgLTk0eiIvPgo8cGF0aCBkPSJNNzQgMTM2IGMtNCAtMTAgLTMgLTIxIDIgLTI1IDUgLTUgNjIgLTYgMTI3IC0zIDkwIDMgMTE3IDggMTE3IDE4IDAKMTEgLTE3IDE0IC02NyAxNCAtMzggMCAtOTIgMyAtMTIwIDYgLTQ0IDYgLTU0IDQgLTU5IC0xMHoiLz4KPHBhdGggZD0iTTI1MCA4MyBjLTExMCAtNCAtMTI5IC04IC0xMTEgLTI2IDE1IC0xNSAyMzggLTIwIDI0NyAtNiA3IDEyIC0xNQo0MCAtMzAgMzcgLTYgLTEgLTU0IC00IC0xMDYgLTV6Ii8+CjwvZz4KPC9zdmc+Cg==" /></span>',
                        html: '<canvas id="sig-canvas" width="472" height="180" rel="' + jQuery(this).attr('rel') + '">Get a better browser, bro.</canvas><input id="sig-submitBtn" type="button" value="Submit" />',
                        showCloseButton: true,
                        showCancelButton: false,
                        focusConfirm: false
                    });
                    window.requestAnimFrame = (function (callback) {
                        return window.requestAnimationFrame ||
                            window.webkitRequestAnimationFrame ||
                            window.mozRequestAnimationFrame ||
                            window.oRequestAnimationFrame ||
                            window.msRequestAnimaitonFrame ||
                            function (callback) {
                                window.setTimeout(callback, 1000 / 60);
                            };
                    })();
                    jQuery('.swal2-content').addClass('custom-signature');
                    setTimeout(function () {
                        var canvas = document.getElementById("sig-canvas");
                        var ctx = canvas.getContext("2d");
                        ctx.strokeStyle = "#222222";
                        ctx.lineWidth = 4;
                        var image = new Image();
                        image.onload = function () {
                            ctx.drawImage(image, 0, 0);
                        };
                        image.src = jQuery('.ícsyv .dtbill_' + jQuery('#sig-canvas').attr('rel') + ' #sig-image').attr('src');
                        var drawing = false;
                        var mousePos = {
                            x: 0,
                            y: 0
                        };
                        var lastPos = mousePos;
                        canvas.addEventListener("mousedown", function (e) {
                            drawing = true;
                            lastPos = getMousePos(canvas, e);
                        }, false);
                        canvas.addEventListener("mouseup", function (e) {
                            drawing = false;
                        }, false);
                        canvas.addEventListener("mousemove", function (e) {
                            mousePos = getMousePos(canvas, e);
                        }, false);
                        // Add touch event support for mobile
                        canvas.addEventListener("touchstart", function (e) {

                        }, false);
                        canvas.addEventListener("touchmove", function (e) {
                            var touch = e.touches[0];
                            var me = new MouseEvent("mousemove", {
                                clientX: touch.clientX,
                                clientY: touch.clientY
                            });
                            canvas.dispatchEvent(me);
                        }, false);
                        canvas.addEventListener("touchstart", function (e) {
                            mousePos = getTouchPos(canvas, e);
                            var touch = e.touches[0];
                            var me = new MouseEvent("mousedown", {
                                clientX: touch.clientX,
                                clientY: touch.clientY
                            });
                            canvas.dispatchEvent(me);
                        }, false);
                        canvas.addEventListener("touchend", function (e) {
                            var me = new MouseEvent("mouseup", {});
                            canvas.dispatchEvent(me);
                        }, false);

                        function getMousePos(canvasDom, mouseEvent) {
                            var rect = canvasDom.getBoundingClientRect();
                            return {
                                x: mouseEvent.clientX - rect.left,
                                y: mouseEvent.clientY - rect.top
                            }
                        }

                        function getTouchPos(canvasDom, touchEvent) {
                            var rect = canvasDom.getBoundingClientRect();
                            return {
                                x: touchEvent.touches[0].clientX - rect.left,
                                y: touchEvent.touches[0].clientY - rect.top
                            }
                        }

                        function renderCanvas() {
                            if (drawing) {
                                ctx.moveTo(lastPos.x, lastPos.y);
                                ctx.lineTo(mousePos.x, mousePos.y);
                                ctx.stroke();
                                lastPos = mousePos;
                            }
                        }

                        // Prevent scrolling when touching the canvas
                        document.body.addEventListener("touchstart", function (e) {
                            if (e.target == canvas) {
                                e.preventDefault();
                            }
                        }, false);
                        document.body.addEventListener("touchend", function (e) {
                            if (e.target == canvas) {
                                e.preventDefault();
                            }
                        }, false);
                        document.body.addEventListener("touchmove", function (e) {
                            if (e.target == canvas) {
                                e.preventDefault();
                            }
                        }, false);
                        (function drawLoop() {
                            requestAnimFrame(drawLoop);
                            renderCanvas();
                        })();

                        function clearCanvas() {
                            canvas.width = canvas.width;
                        }

                        // Set up the UI
                        var clearBtn = document.getElementById("sig-clearBtn");
                        var submitBtn = document.getElementById("sig-submitBtn");
                        clearBtn.addEventListener("click", function (e) {
                            clearCanvas();
                        }, false);
                        submitBtn.addEventListener("click", function (e) {
                            var dataUrl = canvas.toDataURL();
                            var order_id = jQuery('#sig-canvas').attr('rel');
                            Swal.fire({
                                title: 'Information',
                                html: '<p>Please wait...</p>',
                                onOpen: function () {
                                    Swal.showLoading();
                                }
                            }).then((result) => {
                                console.log('I was closed by the timer')
                            });
                            var data = {
                                'action': 'save_signature_customer_ajax',
                                'order_id': order_id,
                                'data': dataUrl
                            };
                            jQuery.ajax({
                                type: 'post',
                                dataType: 'json',
                                url: ajax_url,
                                data: data,
                                success: function (data) {
                                    Swal.close();
                                    if (data.flag == 1) {
                                        jQuery('.ícsyv .dtbill_' + order_id + ' #sig-image').attr('src', dataUrl);
                                        alert('Signature saved');
                                    } else {
                                        alert('Error!');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    alert(error);
                                }
                            });
                        }, false);
                    }, 300);
                });
            }

            if (jQuery('.acf-field-5e578d07c0fb5').length > 0) {
                jQuery('.acf-field-5e578d07c0fb5').appendTo(jQuery('#custom_material_tab'));
            }
            if (jQuery('.field-print-template').length > 0) {
                jQuery('.field-print-template').appendTo(jQuery('#custom_guidelines_tab'));
            }
            jQuery('body').on('click', '.btn-download-od', function (event) {
                Swal.fire({
                    title: 'Information',
                    html: '<p>Please wait...</p>',
                    onOpen: function () {
                        Swal.showLoading();
                    }
                }).then((result) => {
                    console.log('I was closed by the timer');
                });
                var order_id = jQuery(this).attr('rel');
                var data = {
                    'action': 'download_detail_order_ajax',
                    'order_id': order_id,
                    'noauth': 1
                };
                jQuery.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_url,
                    data: data,
                    success: function (data) {
                        Swal.close();
                        if (data.link_down != '') {
                            var a = document.createElement('a');
                            a.setAttribute('href', data.link_down);
                            a.setAttribute('download', data.filename);
                            a.style.display = 'none';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(error);
                    }
                });
            });
            jQuery('#order-dashboard-table .btn-viewdt').click(function () {
                Swal.fire({
                    title: 'Order #' + jQuery(this).attr('rel') + ' <span class="status-od">' + jQuery('td.opt_status_od_co[rel=' + jQuery(this).attr('rel') + '] select option:selected').text() + ' </span>',
                    html: jQuery('.ícsyv .dtbill_' + jQuery(this).attr('rel')).html(),
                    showCloseButton: true,
                    showCancelButton: false,
                    focusConfirm: false
                });
            });
            jQuery('#order-dashboard-table .generate_link_payment').click(function () {
                if (jQuery(this).parent('td').siblings('td[fulltable-field-name="order_files"]').find('.download-aws').length > 0) {
                    if (jQuery('.ícsyv .tb_artwork_' + jQuery(this).attr('rel')).length > 0) {
                        Swal.fire({
                            title: 'Artwork Amendment',
                            html: jQuery('.ícsyv .tb_artwork_' + jQuery(this).attr('rel')).html(),
                            showCloseButton: true,
                            showCancelButton: false,
                            focusConfirm: false
                        });
                        setTimeout(function () {
                            jQuery('.table-aaod').parents('.swal2-popup').css('width', '630px');
                            updatePriceAA();
                        }, 100);
                    } else {
                        alert('No find data!');
                    }
                } else {
                    alert('No find design!');
                }
            });
            jQuery('body').on('click', '#swal2-content .btn-add-row', function (event) {
                var f_row = jQuery('#swal2-content table.table-aaod tbody tr:eq(0)').clone();
                jQuery('#swal2-content table.table-aaod tbody').append(f_row);
                updatePriceAA();
            });
            jQuery('body').on('change', 'table.table-aaod tbody select', function (event) {
                var val = jQuery(this).val();
                if (jQuery(this).attr('name') == 'aa_item_ata[]') {
                    if (val == 'n') {
                        jQuery(this).parents('.row-aa-oi').find('select[name="aa_item_service[]"]').css({
                            'pointer-events': 'none',
                            'opacity': '0.5'
                        });
                    } else {
                        jQuery(this).parents('.row-aa-oi').find('select[name="aa_item_service[]"]').removeAttr('style');
                    }
                }
                updatePriceAA();
                jQuery(this).find('option').removeAttr("selected");
                jQuery(this).find('option[value="' + val + '"]').attr("selected", "selected");
            });
            jQuery('body').on('click', '#swal2-content .act-remove-row', function (event) {
                jQuery(this).parents('tr.row-aa-oi').remove();
                updatePriceAA();
            });
            jQuery('body').on('click', '#swal2-content .btn-update-status-od', function (event) {
                var data_p = jQuery('#swal2-content form.frm-data-aa').serialize();
                Swal.fire({
                    title: 'Information',
                    html: '<p>Please wait...</p>',
                    onOpen: function () {
                        Swal.showLoading();
                    }
                }).then((result) => {
                    console.log('I was closed by the timer');
                });
                var order_id = jQuery(this).attr('rel');
                var data = {
                    'action': 'repayment_artwork_ajax',
                    'order_id': order_id,
                    'data': data_p
                };
                jQuery.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_url,
                    data: data,
                    success: function (data) {
                        Swal.close();
                        if (data.flag == 1) {
                            alert('Success!');
                            location.reload();
                        } else {
                            alert('Created a link for customers to make additional payments in 24 hour before');
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(error);
                    }
                });
            });
            jQuery('.wp-list-table .column-cart_no .btn-download-quotation').click(function () {
                jQuery(this).parents('tr.type-stored-carts').addClass('disable-action');
                var quote_id = jQuery(this).attr('rel');
                var data = {
                    'action': 'download_quotation_cart_ajax',
                    'quo_id': quote_id,
                    'noauth': 1
                };
                jQuery.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_url,
                    data: data,
                    success: function (data) {
                        jQuery('tr.type-stored-carts').removeClass('disable-action');
                        if (data.link_down != '') {
                            var a = document.createElement('a');
                            a.setAttribute('href', data.link_down);
                            a.setAttribute('download', data.filename);
                            a.style.display = 'none';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                        }
                    },
                    error: function (xhr, status, error) {
                        jQuery('tr.type-stored-carts').removeClass('disable-action');
                        alert(error);
                    }
                });
            });
            jQuery('.wp-list-table .column-cart_no .btn-delete-quotation').click(function () {
                if (confirm('Are you sure?')) {
                    jQuery(this).parents('tr.type-stored-carts').addClass('disable-action');
                    var quote_id = jQuery(this).attr('rel');
                    var data = {
                        'action': 'remove_quotation_cart_ajax',
                        'quo_id': quote_id,
                        'noauth': 1
                    };
                    jQuery.ajax({
                        type: 'post',
                        dataType: 'json',
                        url: ajax_url,
                        data: data,
                        success: function (data) {
                            jQuery('tr.type-stored-carts').removeClass('disable-action');
                            if (data.flag == 1) {
                                alert('Your data has been deleted.');
                                location.reload();
                            } else {
                                alert('An error occurred! Please try again.');
                            }
                        },
                        error: function (xhr, status, error) {
                            jQuery('tr.type-stored-carts').removeClass('disable-action');
                            alert(error);
                        }
                    });
                }
            });
            jQuery('.wp-list-table .column-cart_no .btn-save-quotation').click(function () {
                jQuery(this).parents('tr.type-stored-carts').addClass('disable-action');
                var quote_id = jQuery(this).attr('rel');
                var status = jQuery(this).parents('tr#post-' + quote_id).find('select#cart_status').val();
                var assig = jQuery(this).parents('tr#post-' + quote_id).find('select#cart_assignment').val();
                var data = {
                    'action': 'save_quotation_cart_ajax',
                    'quo_id': quote_id,
                    'status': status,
                    'assig': assig
                };
                jQuery.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_url,
                    data: data,
                    success: function (data) {
                        jQuery('tr.type-stored-carts').removeClass('disable-action');
                        if (data.flag == 1) {
                            alert('Your data has been saved.');
                        } else {
                            alert('An error occurred! Please try again.');
                        }
                    },
                    error: function (xhr, status, error) {
                        jQuery('tr.type-stored-carts').removeClass('disable-action');
                        alert(error);
                    }
                });
            });
            jQuery('body').on('change', '#order-dashboard-table td[fulltable-field-name="status"] select[name="opt_status"]', function (event) {
                var val = jQuery(this).val();
                var osoc = jQuery(this).parent('.opt_status_od_co');
                Swal.fire({
                    title: 'Information',
                    html: '<p>Please wait...</p>',
                    onOpen: function () {
                        Swal.showLoading();
                    }
                }).then((result) => {
                    console.log('I was closed by the timer')
                });
                var order_id = jQuery(this).parent('td.opt_status_od_co').attr('rel');
                var data = {
                    'action': 'update_status_od_custom_ajax',
                    'order_id': order_id,
                    'status': val
                };
                jQuery.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: ajax_url,
                    data: data,
                    success: function (data) {
                        Swal.close();
                        if (data.flag == 1) {
                            alert('Status updated!');
                            if (val == 3) {
                                osoc.siblings('td[fulltable-field-name="artwork_amendment"]').find('.generate_link_payment').removeClass('disable-act');
                            } else {
                                osoc.siblings('td[fulltable-field-name="artwork_amendment"]').find('.generate_link_payment').addClass('disable-act');
                            }
                        } else {
                            alert('Error!');
                        }
                    },
                    error: function (xhr, status, error) {
                        alert(error);
                    }
                });
            });
        });

        function updatePriceAA() {
            var sump = 0;
            jQuery('#swal2-content select[name="aa_item_service[]"]').each(function (index, element) {
                if (jQuery(this).css('opacity') == '1') {
                    var p = jQuery(this).find('option:selected').attr('rel');
                    sump = parseFloat(sump) + parseFloat(p);
                }
            });
            jQuery('#swal2-content h2 span.sum_price').text(Number((sump).toFixed(2)));
            jQuery('.ícsyv .tb_artwork_' + jQuery('#swal2-content h4.code').attr('rel')).html(jQuery('#swal2-content').html());
        }
    </script>
    <?php
}

add_filter('woocommerce_product_tabs', 'woo_new_product_tab', 280);

function woo_new_product_tab($tabs)
{
    global $product;
    if ('service' !== $product->get_type()) {
        $tabs['material_tab'] = array(
            'title' => __('Material', 'woocommerce'),
            'priority' => 50,
            'callback' => 'woo_new_material_tab_content',
        );
        $tabs['guidelines_tab'] = array(
            'title' => __('Guidelines & Templates', 'woocommerce'),
            'priority' => 50,
            'callback' => 'woo_new_guidelines_tab_content',
        );
    } else {
        $tabs['terms_conditions_tab'] = array(
            'title' => __('Terms & Conditions', 'woocommerce'),
            'priority' => 50,
            'callback' => 'woo_new_terms_coditions_tab_content',
        );
    }
    return $tabs;
}

function woo_new_material_tab_content()
{
    $prod_id = get_the_ID();
    $post_objects = get_field('materials', $prod_id);
    if ($post_objects) {
        ?>
        <div class="material-name-slider">
            <?php foreach ($post_objects as $key => $post): ?>
                <div class="material-name-item <?php echo $key == 0 ? 'material-active' : ''; ?>"
                     data-post="<?php echo $post; ?>"><?php echo get_the_title($post); ?></div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    if ($post_objects) {
        foreach ($post_objects as $key => $post) {
            setup_postdata($post);
            $ga = '';
            $gallery_materials = get_field('images', $post);
            if (count($gallery_materials) > 0) {
                $ga = '[vc_single_image el_id="large-thumb-material" source="external_link" external_img_size="547x547" custom_src="' . $gallery_materials[0] . '"]<div class="material-slider">';
                foreach ($gallery_materials as $img) {
                    $ga .= '<div class="material-item"><img src="' . $img . '" /></div>';
                }
                $ga .= '</div>';
            }
            $info_pro = '<h4 class="title">' . get_the_title($post) . '</h4>';
            $info_pro .= '<span class="code">' . get_field('material_code', $post) . '</span>';
            $info_pro .= '<div class="medes">' . nl2br(get_the_content($post)) . '</div>';
            $attrs = wp_get_post_terms($post, array('matealize_attributes'));
            foreach ($attrs as $attr) {
                $info_pro .= '<ul class="material_attrs"><li>' . $attr->name . '</li></ul>';
            }
            $info_pro .= '<a class="link-mate" href="' . get_the_permalink($post) . '">More info on material</a>';
            echo do_shortcode('[vc_row el_class="wrap-material-tab ' . ($key == 0 ? 'material-active' : '') . '" el_id="wrap-material-tab-' . $post . '"][vc_column width="1/3"]' . $ga . '[/vc_column][vc_column width="2/3"]' . $info_pro . '[/vc_column][/vc_row]');
        }
        ?>
        <link rel="stylesheet" id="ult-icons-css" href="<?php echo UAVC_URL; ?>assets/css/icons.css?ver=3.19.0"
              type="text/css" media="all">
        <link rel="stylesheet" id="ult-slick-css" href="<?php echo UAVC_URL; ?>assets/min-css/slick.min.css?ver=3.19.0"
              type="text/css" media="all">
        <script type="text/javascript" src="<?php echo UAVC_URL; ?>assets/min-js/slick.min.js?ver=3.19.0"></script>
        <script>
            jQuery(document).ready(function () {
                jQuery('.material-slider').slick({
                    infinite: false,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    arrows: true,
                });
                jQuery('.material-name-slider').slick({
                    infinite: false,
                    slidesToShow: 1,
                    variableWidth: true,
                    slidesToScroll: 2,
                    arrows: true,
                });
                jQuery('.slick-prev.slick-arrow, .slick-next.slick-arrow').text('');
                jQuery('.slick-prev.slick-arrow').append('<i class="ultsl-arrow-left4"></i>');
                jQuery('.slick-next.slick-arrow').append('<i class="ultsl-arrow-right4"></i>');
                setInterval(function () {
                    jQuery('.material-slider, .material-name-slider').slick('setPosition');
                }, 500);

                jQuery('.material-name-item').click(function () {
                    jQuery('.material-name-item, .wrap-material-tab').removeClass('material-active');
                    jQuery(this).addClass('material-active');
                    jQuery('#wrap-material-tab-' + jQuery(this).attr('data-post')).addClass('material-active');
                })
            })
        </script>
        <?php
        wp_reset_postdata();
    }
}

function woo_new_guidelines_tab_content()
{
    $bgimg_pt = get_theme_mod('botaksign_setting_banner_tabgl_pt');
    $bgimg_ig = get_theme_mod('botaksign_setting_banner_tabgl_ig');
    $bgimg_faq = get_theme_mod('botaksign_setting_banner_tabgl_faq');
    $prod_id = get_the_ID();
    $post_objects = get_field('print_template', $prod_id);
    $guide = '[vc_row][vc_column width="1/3"][ultimate_ctation el_class="tabgl-pt" ctaction_background="#7e7c7c" ctaction_background_hover="#0c0a0a" ctaction_link="url:' . urlencode(get_permalink($post_objects)) . '|||"]Print Template[/ultimate_ctation][/vc_column]';
    $guide .= '[vc_column width="1/3"][ultimate_ctation el_class="tabgl-ig" ctaction_background="#7e7c7c" ctaction_background_hover="#0c0a0a" ctaction_link="url:' . urlencode(get_field('installation_guide', $prod_id)) . '|||"]Installation Guide[/ultimate_ctation][/vc_column]';
    $guide .= '[vc_column width="1/3"][ultimate_ctation el_class="tabgl-faq" ctaction_background="#7e7c7c" ctaction_background_hover="#0c0a0a" ctaction_link="url:/faq/||target:%20_blank|"]FAQ[/ultimate_ctation][/vc_column][/vc_row]';
    echo '<style>#tab-guidelines_tab .uvc-ctaction-data {
        width: 100%;
        background-color: #0e0a0a63;
        text-align: right;
        padding-right: 30px;
        position: absolute;
        bottom: 0px;
        right: 0px;} '
        . ($bgimg_pt != '' ? '#tab-guidelines_tab .ultimate-call-to-action.ult-adjust-bottom-margin.tabgl-pt { background: url(' . $bgimg_pt . ') !important; background-size: cover !important;}' : '')
        . ($bgimg_pt != '' ? '#tab-guidelines_tab .ultimate-call-to-action.ult-adjust-bottom-margin.tabgl-ig { background: url(' . $bgimg_ig . ') !important; background-size: cover !important;}' : '')
        . ($bgimg_pt != '' ? '#tab-guidelines_tab .ultimate-call-to-action.ult-adjust-bottom-margin.tabgl-faq { background: url(' . $bgimg_faq . ') !important; background-size: cover !important;}' : '') . '</style>';
    echo do_shortcode($guide);
}

function woo_new_terms_coditions_tab_content()
{
    $prod_id = get_the_ID();
    $content = get_post_meta($prod_id, 'terms_conditions_meta', true);
    echo $content;
}

add_filter('woocommerce_cart_item_name', 'showing_sku_in_cart_items', 100, 3);

function showing_sku_in_cart_items($item_name, $cart_item, $cart_item_key)
{
    // The WC_Product object
    $product = $cart_item['data'];

    //CS botak check condition to change gallery
    $sku = '';
    if (isset($cart_item['nbo_meta'])) {
         if( nbd_is_base64_string( $cart_item['nbo_meta']['options']['fields'] )) {
            $cart_item['nbo_meta']['options']['fields'] = base64_decode( $cart_item['nbo_meta']['options']['fields'] ); // custom botak fix lose the sku when update base64_decode
        }
        $option_fields = maybe_unserialize($cart_item['nbo_meta']['options']['fields']);
        try {
            $check = NBD_FRONTEND_PRINTING_OPTIONS::check_and_get_change_gallery($option_fields['gallery_options'], maybe_unserialize($cart_item['nbo_meta'])['field'], $cart_item['quantity']);
            if ($check['change'] === true && $check['option']['sku']) {
                $sku = $check['option']['sku'];
            }
            foreach ($cart_item['nbo_meta']['field'] as $f_id => $fvalue) {
                $select = !is_array($fvalue) ? $fvalue : $fvalue['value'];
                foreach ($option_fields['fields'] as $data) {
                    if ($f_id === $data['id']) {
                        $option = $data['general']['attributes']['options'][$select];
                        if (isset($option['sku']) && $option['sku'] != '') {
                            $sku .= $option['sku'];
                        }
                    }
                }
            }
        } catch (Exception $e) {
            write_log($e->getMessage());
        }
    }
    //End CS botak check condition to change gallery

    // Get the  SKU
    if ($sku == '') {
        $sku = $product->get_sku();
    }

    // When sku doesn't exist
    if (empty($sku) || $sku == '') {
        return $item_name;
    }

    // Add the sku
    $item_name .= '<br><small class="product-sku">' . __("SKU: ", "woocommerce") . $sku . '</small>';

    return $item_name;
}

function iconic_account_menu_items($items)
{

    $items['quotations'] = __('Quotations', 'iconic');

    return $items;
}

add_filter('woocommerce_account_menu_items', 'iconic_account_menu_items', 10, 1);

function iconic_add_my_account_endpoint()
{

    add_rewrite_endpoint('quotations', EP_PAGES);
}

add_action('init', 'iconic_add_my_account_endpoint');

function iconic_quotations_endpoint_content()
{   
    wc_get_template('myaccount/quotations.php');
}

add_action('woocommerce_account_quotations_endpoint', 'iconic_quotations_endpoint_content');

function iconic_is_endpoint($endpoint = false)
{

    global $wp_query;

    if (!$wp_query) {
        return false;
    }

    return isset($wp_query->query[$endpoint]);
}

add_action('wp_ajax_remove_quotation_cart_ajax', 'remove_quotation_cart_ajax');
add_action('wp_ajax_nopriv_remove_quotation_cart_ajax', 'remove_quotation_cart_ajax');

function remove_quotation_cart_ajax()
{
    global $wpdb;
    global $current_user;
    get_currentuserinfo();
    $user_id = $current_user->ID;
    $result = [];
    $result['flag'] = 0;
    if (isset($_POST['quo_id'])) {
        if (isset($_POST['noauth'])) {
            $tem = $wpdb->get_row("SELECT ID FROM {$wpdb->prefix}posts WHERE ID = {$_POST['quo_id']} AND post_type = 'stored-carts'");
        } else {
            $tem = $wpdb->get_row("SELECT ID FROM {$wpdb->prefix}posts WHERE ID = {$_POST['quo_id']} AND post_author = {$user_id} AND post_type = 'stored-carts'");
        }
        if ($tem) {
            $delete_meta_sql = "DELETE FROM " . $wpdb->prefix . "postmeta WHERE post_id = '" . $tem->ID . "'";
            $wpdb->query($delete_meta_sql);
            $delete_sql = "DELETE FROM " . $wpdb->prefix . "posts WHERE ID = '" . $tem->ID . "'";
            $wpdb->query($delete_sql);
            $result['flag'] = 1;
        }
    }
    echo json_encode($result);
    wp_die();
}

add_action('wp_ajax_save_quotation_cart_ajax', 'save_quotation_cart_ajax');
add_action('wp_ajax_nopriv_save_quotation_cart_ajax', 'save_quotation_cart_ajax');

function save_quotation_cart_ajax()
{
    global $cxecrt;
    $result = [];
    $result['flag'] = 0;
    if (isset($_POST['quo_id']) && isset($_POST['status']) && isset($_POST['assig'])) {
        update_post_meta($_POST['quo_id'], '_cxecrt_status', $_POST['status']);
        update_post_meta($_POST['quo_id'], '_cxecrt_cart_assignment', $_POST['assig']);
        $result['flag'] = 1;
        if ($_POST['status'] == 1) {
            $cxecrt->send_cart_email($_POST['quo_id'], 0, 'customer');
        } elseif ($_POST['status'] == 2) {
            $cxecrt->send_cart_email($_POST['quo_id'], 0, 'specialist');
        }
    }
    echo json_encode($result);
    wp_die();
}

add_action('wp_ajax_download_quotation_cart_ajax', 'download_quotation_cart_ajax');
add_action('wp_ajax_nopriv_download_quotation_cart_ajax', 'download_quotation_cart_ajax');

function download_quotation_cart_ajax()
{
    global $botakit;
    $result = [];
    $result['link_down'] = '';
    if (isset($_POST['quo_id'])) {
        $html = generate_quote_pdf($_POST['quo_id']);
        //write_log($html);
        $botakit->_content = $html;
        $filename = 'quotation-' . $_POST['quo_id'] . '.pdf';
        $botakit->generate_pdf_template($filename);
        $pdf_path = $botakit->_file_to_save . '/' . $filename;
        $result['link_down'] = convertLinkDesign($pdf_path);
        $result['filename'] = $filename;
    }
    echo json_encode($result);
    wp_die();
}

add_action('wp_ajax_download_detail_order_ajax', 'download_detail_order_ajax');
add_action('wp_ajax_nopriv_download_detail_order_ajax', 'download_detail_order_ajax');

function download_detail_order_ajax()
{
    global $botakit;
    $result = [];
    $result['link_down'] = '';
    if (isset($_POST['order_id'])) {
        $html = generate_order_detail_pdf($_POST['order_id']);
        $botakit->_content = $html;
        $filename = 'order-' . $_POST['order_id'] . '.pdf';
        $botakit->generate_pdf_template($filename);
        $pdf_path = $botakit->_file_to_save . '/' . $filename;
        $result['link_down'] = convertLinkDesign($pdf_path);
        $result['filename'] = $filename;
    }
    echo json_encode($result);
    wp_die();
}

function convertLinkDesign($url)
{
    return preg_replace('/.*?\/wp-content\//', get_site_url() . '/wp-content/', $url);
}

function update_order_id_after_checkout($order_id)
{
    global $wpdb;

    $order = wc_get_order($order_id);
    $list_item = $order->get_items('line_item');
    $order_type_standard = 0;
    $order_type_rush = 1;
    $order_type_super_rush = 2;

    //CS botak update type for order
    $order_type = $order_type_standard;
    foreach ($list_item as $item_id => $item) {
        if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
            $options = $item->get_meta('_nbo_options');
            $origin_fields = unserialize($options['fields']);
            $origin_fields = $origin_fields['fields'];
            $item_field = $item->get_meta('_nbo_field');

            foreach ($item_field as $key => $value) {
                foreach ($origin_fields as $field) {
                    if ($field['id'] === $key && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                        switch ($field['general']['attributes']['options'][$value['value']]["name"]) {
                            case 'RUSH':
                                if ($order_type != $order_type_super_rush) {
                                    $order_type = $order_type_rush;
                                }
                                break;
                            case 'Super RUSH':
                                $order_type = $order_type_super_rush;
                                break;
                        };
                    }
                }
            }
        }
    }
    update_post_meta($order_id, 'order_type', $order_type);

    if ($order_id) {
        return;
    }
    //  $order = wc_get_order( $order_id );
    //  $order_items = $order->get_items();
    $user_id = get_current_user_id();
    $cart_id = get_option('quo_cart_user_' . $user_id);
    if ($cart_id) {
        $tem = $wpdb->get_row("SELECT ID FROM {$wpdb->prefix}posts WHERE ID = {$cart_id} AND post_author = {$user_id} AND post_type = 'stored-carts'");
        if ($tem) {
            $delete_meta_sql = "DELETE FROM " . $wpdb->prefix . "postmeta WHERE post_id = '" . $tem->ID . "'";
            $wpdb->query($delete_meta_sql);
            $delete_sql = "DELETE FROM " . $wpdb->prefix . "posts WHERE ID = '" . $tem->ID . "'";
            $wpdb->query($delete_sql);
        }
    }
}

add_action('woocommerce_thankyou', 'update_order_id_after_checkout');

function botak_show_currency_symbol($sym)
{
    if ('SGD' == get_woocommerce_currency()) {
        $sym = "SGD " . $sym;
    }

    return $sym;
}

add_filter('woocommerce_currency_symbol', 'botak_show_currency_symbol', 10000, 1);

function botak_wc_price_args($default_args)
{
    $default_args['price_format'] = '%1$s%2$s';
    return $default_args;
}

add_filter('wc_price_args', 'botak_wc_price_args', 10, 1);

/**
 * Fix pagination on archive pages
 * After adding a rewrite rule, go to Settings > Permalinks and click Save to flush the rules cache
 */
function my_pagination_rewrite()
{
    add_rewrite_rule("our-works/page/([0-9]{1,})/?$", 'index.php?pagename=our-works&paged=$matches[1]', "top");
    add_rewrite_rule('our-works/c/([0-9]{1,})/?$', 'index.php?pagename=our-works&c=$matches[1]', 'top');
    add_rewrite_rule('our-works/c/([0-9]{1,})/page/([0-9]{1,})/?$', 'index.php?pagename=our-works&c=$matches[1]&paged=$matches[2]', 'top');
    add_rewrite_tag('%c%', '([^&]+)');
}

add_action('init', 'my_pagination_rewrite');

add_action('pre_get_posts', 'rc_modify_query_exclude_ou');

// Create a function to excplude some categories from the main query
function rc_modify_query_exclude_ou($query)
{
    $user = get_userdata(get_current_user_id());
    if (is_object($user) && $query->is_main_query() && $user->roles[0] == 'specialist') {
        if ($query->get('post_type') == 'shop_order' || $query->get('post_type') == 'stored-carts') {
            global $wpdb;
            //            $sql = "SELECT `ID` FROM {$wpdb->prefix}posts WHERE `post_author` IN (SELECT `user_id` FROM {$wpdb->prefix}usermeta WHERE `meta_key` = 'specialist' AND `meta_value` = " . get_current_user_id() . ")";
            $sql = "SELECT `ID` FROM {$wpdb->prefix}posts WHERE `ID` IN (SELECT `post_id` FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '_cxecrt_cart_assignment' AND `meta_value` = " . get_current_user_id() . ")";
            $result = $wpdb->get_results($sql);
            //            write_log($result);
            $arr_p = [];
            foreach ($result as $u) {
                array_push($arr_p, $u->ID);
            }
            if (count($arr_p) > 0) {
                $query->set('post__in', $arr_p);
            }
        }
    }
}

add_action('wp_ajax_save_signature_customer_ajax', 'save_signature_customer_ajax');
add_action('wp_ajax_nopriv_save_signature_customer_ajax', 'save_signature_customer_ajax');

function save_signature_customer_ajax()
{
    $result = [];
    $result['flag'] = 0;
    if (isset($_POST['order_id']) && isset($_POST['data'])) {
        update_post_meta($_POST['order_id'], '_cxecrt_signature', $_POST['data']);
        cxecrt_update_status_order_by_role($_POST['order_id'], 11);
        $result['flag'] = 1;
    }
    echo json_encode($result);
    wp_die();
}

add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);

function custom_variation_price($price, $product)
{
    $available_variations = $product->get_available_variations();
    $selectedPrice = '';
    $dump = '';
    $defaultArray = array();
    foreach ($available_variations as $variation) {
        $isDefVariation = false;
        foreach ($product->get_default_attributes() as $key => $val) {
            $defaultArray['attribute_' . $key] = $val;
        }
        $result = array_diff($defaultArray, $variation['attributes']);
        if (empty($result)) {
            $isDefVariation = true;
            $price = $variation['display_price'];
        }
    }

    $selectedPrice = wc_price($price);
    return $selectedPrice . $dump;
}

add_action('wp_ajax_update_status_od_custom_ajax', 'update_status_od_custom_ajax');
add_action('wp_ajax_nopriv_update_status_od_custom_ajax', 'update_status_od_custom_ajax');

function update_status_od_custom_ajax()
{
    $user = get_userdata(get_current_user_id());
    $result = [];
    $result['flag'] = 0;
    if (isset($_POST['order_id']) && isset($_POST['status'])) {
        $order_id = $_POST['order_id'];
        if (check_enable_update_status_od($_POST['status'])) {
            cxecrt_update_status_order_by_role($_POST['order_id'], $_POST['status']);
//            if ((in_array('specialist', $user->roles) || in_array('administrator', $user->roles)) && $_POST['status'] == 4) {
//                cxecrt_update_status_order_by_role($_POST['order_id'], $_POST['status'], '_p'); // 5
//                //                send_botaksign_email($order_id, 'ORDER PROCESSED', 'C2.php');
//            }
            if ($_POST['status'] == 5) {
                send_botaksign_email($order_id, 'ORDER PROCESSED', 'C2.php');
            }
//            if ((in_array('production', $user->roles) || in_array('administrator', $user->roles)) && $_POST['status'] >= 8) {
//                cxecrt_update_status_order_by_role($_POST['order_id'], $_POST['status'], '_cs'); // 9
//                //                send_botaksign_email($order_id, 'ORDER COMPLETE', 'B2.php');
//            }
            if ($_POST['status'] == 9) {
                send_botaksign_email($order_id, 'ORDER COMPLETE', 'B2.php');
            }
            $result['flag'] = 1;
            if ($_POST['status'] == 10) {
                send_botaksign_email($order_id, 'ORDER COMPLETE', 'A2.php');
            }
        }
    }
    echo json_encode($result);
    wp_die();
}

function update_acf_post_object_field_choices($title, $post, $field, $post_id)
{
    if ($field['key'] == 'field_5e578d07c0fb5') {
        $sku_value = get_field('material_code', $post->ID);

        if ($sku_value && !empty($sku_value)) {
            $title .= ' [' . $sku_value . ']';
        }
    }
    return $title;
}

add_filter('acf/fields/post_object/result', 'update_acf_post_object_field_choices', 10, 4);

add_filter('acf/fields/post_object/query', 'my_acf_fields_post_object_query', 10, 3);

function my_acf_fields_post_object_query($args, $field, $post_id)
{
    if ($field['key'] == 'field_5e578d07c0fb5') {
        $the_search = $args['s'];
        unset($args['s']);
        $args['meta_key'] = 'material_code';
        $args['meta_value'] = $the_search;
        $args['meta_compare'] = 'LIKE';
    }
    return $args;
}

add_action('pre_user_query', 'add_my_custom_queries');

function add_my_custom_queries($user_query)
{
    $user = get_userdata(get_current_user_id());
    if (is_object($user) && $user->roles[0] == 'specialist') {
        global $wpdb;
        $result = $wpdb->get_results("SELECT `user_id` FROM {$wpdb->prefix}usermeta WHERE `meta_key` = 'specialist' AND `meta_value` = " . get_current_user_id());
        $arr_p = [];
        foreach ($result as $u) {
            array_push($arr_p, $u->user_id);
        }
        if (count($arr_p) > 0) {
            $user_query->query_where .= ' AND ID IN(' . implode(',', $arr_p) . ')'; // additional where clauses
        } else {
            $user_query->query_where .= ' AND ID IN(0)';
        }

        // $user_query->query_fields .= ', my_custom_field ';  // additional fields
        // $user_query->query_from .= ' INNER JOIN my_table '; // additional joins here
        // $user_query->query_orderby .= ' ORDER BY my_custom_field '; // additional sorting
        // $user_query->query_limit .= ''; // if you need to adjust paging
    }
}

//add_action( 'wp_mail_failed', 'onMailError', 10, 1 );
//      function onMailError( $wp_error ) {
//      write_log('alert email error!');
//            write_log($wp_error);
//      }

add_filter('woocommerce_locate_template', 'woo_adon_plugin_template', 1, 3);
function woo_adon_plugin_template($template, $template_name, $template_path)
{
    global $woocommerce;
    $_template = $template;
    if (!$template_path)
        $template_path = $woocommerce->template_url;

    $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/template/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template(
        array(
            $template_path . $template_name,
            $template_name
        )
    );

    if (!$template && file_exists($plugin_path . $template_name))
        $template = $plugin_path . $template_name;

    if (!$template)
        $template = $_template;

    return $template;
}

if (isset($_POST['data']['woocommerce_shipping_duration']) && isset($_POST['data']['instance_id'])) {
    $arr_wsd = [];
    $key = "wsd_" . $_POST['data']['instance_id'];
    if (get_option('woocommerce_shipping_duration') !== false) {
        $arr_wsd = maybe_unserialize(get_option('woocommerce_shipping_duration'));
    }
    $arr_wsd[$key] = $_POST['data']['woocommerce_shipping_duration'];
    update_option('woocommerce_shipping_duration', maybe_serialize($arr_wsd));
}

//Sort array time quantity break by quantity
if (!function_exists("sort_time_quantity_breaks")) {
    function sort_time_quantity_breaks($a, $b)
    {
        if ($a['qty'] == $b['qty']) {
            return 0;
        }
        return ($a['qty'] < $b['qty']) ? -1 : 1;
    }
}

//Return chosen shipping instance id
function botak_get_chosen_shipping_instance_id()
{
    $instance_ids = [];
    $shipping_methods = WC()->session->get('chosen_shipping_methods');
    foreach ($shipping_methods as $shipping_method) {
        $shipping_id = isset(explode(":", $shipping_method)[0]) ? explode(":", $shipping_method)[0] : '';
        $instance_ids[] = isset(explode(":", $shipping_method)[1]) ? explode(":", $shipping_method)[1] : 0;
    }

    return $instance_ids;
}

add_action('woocommerce_checkout_after_terms_and_conditions', 'botak_show_production_time');
function botak_show_production_time($shipping_method_label = '')
{
    //Get shipping duration of order
    $shipping_duration = maybe_unserialize(get_option('woocommerce_shipping_duration'));
    $max_shipping_time = 0; //Hours
    $shipping_instance_ids = botak_get_chosen_shipping_instance_id();
    if (is_array($shipping_duration)) {
        foreach ($shipping_instance_ids as $shipping_instance_id) {
            if (array_key_exists("wsd_" . $shipping_instance_id, $shipping_duration)) {
                if ($max_shipping_time < $shipping_duration["wsd_" . $shipping_instance_id]) {
                    $max_shipping_time = $shipping_duration["wsd_" . $shipping_instance_id];
                }
            }
        };
    }
    $max_shipping_time = (float)$max_shipping_time * 60; //Conver hours to minutes

    //Find max production time
    $order_items = WC()->session->get('cart');
    $max_production_time = 0; //Hours
    $have_pt = false;
    $role_use = wp_get_current_user()->roles['0'];
    $have_role_use = false;
    $have_check_default = false;
    foreach ($order_items as $item_id => $item) {
        if ($item['nbo_meta']) {
            $qty = $item['quantity'];
            if( nbd_is_base64_string( $item['nbo_meta']['options']['fields'] ) ){
                $item['nbo_meta']['options']['fields'] = base64_decode( $item['nbo_meta']['options']['fields'] );
            }
            $option_fields = $item['nbo_meta']['options']['fields'];
            $option_fields = unserialize($option_fields);
            $origin_fields = $option_fields['fields'];
            $item_field = $item['nbo_meta']['field'];
            foreach ($item_field as $key => $value) {
                foreach ($origin_fields as $field) {
                    if ($field['id'] === $key && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                        $have_pt = true;
                        foreach ($field['general']['role_options'] as $role_options) {
                            if ($role_options['role_name'] == $role_use) {
                                $time_quantity_breaks_1 = $role_options['options'][$value['value']]['time_quantity_breaks'];
                                $have_role_use = true;
                            }
                            if (isset($role_options['check_default']) && ($role_options['check_default'] == 'on' || $role_options['check_default'] == '1')) {
                                $have_check_default = true;
                                $time_quantity_breaks_2 = $role_options['options'][$value['value']]['time_quantity_breaks'];
                            }
                        }
                        if ($have_role_use) {
                            $time_quantity_breaks = $time_quantity_breaks_1;
                        }
                        if (!$have_role_use && $have_check_default) {
                            $time_quantity_breaks = $time_quantity_breaks_2;
                        }
                        //Sort time_quantity_breaks by quantity
                        usort($time_quantity_breaks, "sort_time_quantity_breaks");
                        for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                            if ($i === count($time_quantity_breaks) - 1) {
                                if ($qty >= $time_quantity_breaks[$i]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                    $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                                }
                                break;
                            }
                            if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty < $time_quantity_breaks[$i + 1]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                $max_production_time = (float)$time_quantity_breaks[$i]['time'];
                            }
                        }
                    }
                }
            }
            if (!$have_pt) {
                $_productiton_time_default = array();
                $productiton_time_default = unserialize(nbdesigner_get_option('nbdesigner_product_time_default'));
                for ($f = 0; $f < count($productiton_time_default[0]); $f++) {
                    $_productiton_time_default[$f]['qty'] = $productiton_time_default[0][$f];
                    $_productiton_time_default[$f]['time'] = $productiton_time_default[1][$f];
                }
                for ($i = 0; $i < count($_productiton_time_default); $i++) {
                    if ($i === count($_productiton_time_default) - 1) {
                        if ($qty >= $_productiton_time_default[$i]['qty'] && (int)$_productiton_time_default[$i]['time'] > $max_production_time) {
                            $max_production_time = (float)$_productiton_time_default[$i]['time'];
                        }
                        break;
                    }
                    if ($qty >= $_productiton_time_default[$i]['qty'] && $qty < $_productiton_time_default[$i + 1]['qty'] && (int)$_productiton_time_default[$i]['time'] > $max_production_time) {
                        $max_production_time = (float)$_productiton_time_default[$i]['time'];
                    }
                }
            }
        };
    }
    if ($max_production_time == 0) {
        $max_production_time = unserialize(nbdesigner_get_option('nbdesigner_product_time_default'))[1][0];
    }
    $max_production_time = $max_production_time * 60; //Convert to Minutes

    $h = "8";// time zone of Singapo is (+8)
    $hm = $h * 60;
    $ms = $hm * 60;
    $calc_production_date = date('H:i Y/m/d', time() + ($ms));
    $calc_shipping_date = date('H:i Y/m/d', time() + ($ms));
    $working_time_setting = get_option('working-time-options', true);
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $closed_days = [];
    $col_closed_days = [];
    if (isset($working_time_setting['working-days'])) {
        foreach ($days as $day) {
            if (!array_key_exists($day, $working_time_setting['working-days'])) {
                $closed_days[] = $day;
            }
            foreach ($working_time_setting['working-days'] as $wd) {
                if (!isset($working_time_setting[$wd]['open-time']) || !isset($working_time_setting[$wd]['close-time']) || $working_time_setting[$wd]['open-time'] === '' || $working_time_setting[$wd]['close-time'] === '') {
                    $closed_days[] = $wd;
                }
            }
        }
        $check_holiday = false;
        $add_holiday = true;
        $count_holiday = 0;
        $get_holiday = array();
        if (isset($working_time_setting['holidays']['start-holiday'])) {
            $cacl_time_holiday = array();
            foreach ($working_time_setting['holidays']['start-holiday'] as $key => $value) {
                $get_holiday['holidays'][$key]['start-holiday'] = $value;
                $get_holiday['holidays'][$key]['end-holiday'] = $working_time_setting['holidays']['end-holiday'][$key];

            }
            $cacl_time_holiday = $get_holiday['holidays'];
        }
        // if (isset($get_holiday['holidays']) && $get_holiday['holidays'][0]['start-holiday'] != '') {
        //     if (strtotime($get_holiday['holidays'][0]['start-holiday']) > strtotime($calc_production_date)) {
        //         $cacl_time_holiday = $get_holiday['holidays'];
        //     } else {
        //         foreach ($get_holiday['holidays'] as $key => $value) {
        //             if (strtotime($value['start-holiday']) <= strtotime($calc_production_date) && strtotime( date('0:0 Y/m/d' , strtotime($calc_production_date)) ) <= strtotime($value['end-holiday']) && $add_holiday ) {
        //                 $calc_production_date = $value['end-holiday'] . ' + 1 days';
        //                 $check_holiday = true;
        //                 $count_holiday = $key + 1;
        //                 $cacl_time_holiday = array();
        //             } else {
        //                 $cacl_time_holiday[] = $value;
        //             }
        //             if ($check_holiday) {
        //                 $add_holiday = false;
        //             }
        //         }
        //     }
        // }
        $calc_production_date = strtotime($calc_production_date);

        //Check time order with Collection days => Time order
        if (isset($working_time_setting['collection-days'])) {
            foreach ($days as $day) {
                if (!array_key_exists($day, $working_time_setting['collection-days'])) {
                    $col_closed_days[] = $day;
                }
                foreach ($working_time_setting['collection-days'] as $wd) {
                    if (!isset($working_time_setting[$wd]['col-open-time']) || !isset($working_time_setting[$wd]['col-close-time']) || $working_time_setting[$wd]['col-open-time'] === '' || $working_time_setting[$wd]['col-close-time'] === '') {
                        $col_closed_days[] = $wd;
                    }
                }
            }
            $check_time_order = true;
            $time_order_minute = $calc_production_date;
            $day_order = date('l', $time_order_minute);
            $count_holiday = 0;
            while ($check_time_order) {
                $check_time_order = false;
                if (in_array($day_order, $col_closed_days)) {
                    $time_order_minute += 86400;
                    $day_order = date('l', $time_order_minute);
                    $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time']) * 60;
                    $check_time_order = true;
                }
                if ($working_time_setting[$day_order]['col-open-time'] > date('H:i', $time_order_minute)) {
                    $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time']) * 60;
                    $check_time_order = true;
                }
                if ($working_time_setting[$day_order]['col-close-time'] <= date('H:i', $time_order_minute)) {
                    $time_order_minute += 86400;
                    $day_order = date('l', $time_order_minute);
                    $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time']) * 60;
                    $check_time_order = true;
                }
                if( isset($cacl_time_holiday) ) {
                    foreach ($cacl_time_holiday as $key => $period) {
                        if (strtotime($period['start-holiday']) <= $time_order_minute && (strtotime($period['end-holiday']) + 86399) >= $time_order_minute) {
                            $time_order_minute = strtotime($period['end-holiday']) + 86400;
                            $day_order = date('l', $time_order_minute);
                            $time_order_minute = strtotime(date('00:00 Y/m/d', $time_order_minute)) + split_time($working_time_setting[$day_order]['col-open-time']) * 60;
                            $next_day = true;
                            $check_time_order = true;
                            if($key < count($cacl_time_holiday) -1 ) {
                                $count_holiday = $key + 1;
                            } else {
                                $count_holiday = $key;
                            }
                        }
                    }
                } 
            }
            $calc_production_date = $time_order_minute;
        }
        $flag = false;
        $spend_time = 0;
        $time_work = $max_production_time;
        while ($spend_time < $max_production_time) {
            $minutes_spend = 0;
            $tmp_day = date('l', $calc_production_date);
            if (date('H:i', $calc_production_date) < $working_time_setting[$tmp_day]['open-time']) {
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time']) * 60;
                $tmp_day = date('l', $calc_production_date);
                $flag = true;
            }
            if (date('H:i', $calc_production_date) > $working_time_setting[$tmp_day]['close-time']) {
                $calc_production_date += 86400;
                $tmp_day = date('l', $calc_production_date);
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time']) * 60;
                $flag = true;
            }
            if (in_array($tmp_day, $closed_days)) {
                $calc_production_date += 86400;
                $tmp_day = date('l', $calc_production_date);
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time']) * 60;
                $flag = true;
            }
            if( isset($cacl_time_holiday) ) {
                foreach ($cacl_time_holiday as $key => $period) {
                    if ( strtotime($period['start-holiday']) <= $calc_production_date && (strtotime($period['end-holiday']) + 86399) >= $calc_production_date) {
                        $calc_production_date = strtotime($period['end-holiday']) + 86400;
                        $tmp_day = date('l', $calc_production_date);
                        $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time']) * 60;
                        $flag = true;
                        if($key < count($cacl_time_holiday) -1 ) {
                            $count_holiday = $key + 1;
                        } else {
                            $count_holiday = $key;
                        }
                    }
                }
            } 
            if (!$flag) {
                $minutes_spend = (float)date_diff(new DateTime($working_time_setting[$tmp_day]['open-time']), new DateTime(date('H:i', $calc_production_date)))->format('%h') * 60 + (float)date_diff(new DateTime($working_time_setting[$tmp_day]['open-time']), new DateTime(date('H:i', $calc_production_date)))->format('%i');
            }
            if ((minute_working_on_day($tmp_day) - $minutes_spend) >= $time_work) {
                $calc_production_date = $calc_production_date + $time_work * 60;
                $_calc_production_date = $calc_production_date;
                break;
            } else {
                $spend_time = $spend_time + minute_working_on_day($tmp_day) - $minutes_spend;
                $time_work = $max_production_time - $spend_time;
                $calc_production_date += 86400;
                $tmp_day = date('l', $calc_production_date);
                $calc_production_date = strtotime(date('00:00 Y/m/d', $calc_production_date)) + split_time($working_time_setting[$tmp_day]['open-time']) * 60;
                $flag = true;

            }
        }
    }
    if (isset($working_time_setting['collection-days'])) {
        foreach ($days as $day) {
            if (!array_key_exists($day, $working_time_setting['collection-days'])) {
                $col_closed_days[] = $day;
            }
            foreach ($working_time_setting['collection-days'] as $wd) {
                if (!isset($working_time_setting[$wd]['col-open-time']) || !isset($working_time_setting[$wd]['col-close-time']) || $working_time_setting[$wd]['col-open-time'] === '' || $working_time_setting[$wd]['col-close-time'] === '') {
                    $col_closed_days[] = $wd;
                }
            }
        }
        $col_tmp_day = date('l', $_calc_production_date);
        $condition = true;
        while ($condition) {
            $condition = false;
            if (isset($cacl_time_holiday[$count_holiday]) && strtotime($cacl_time_holiday[$count_holiday]['start-holiday']) <= $_calc_production_date && (strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86399) >= $_calc_production_date) {
                $_calc_production_date = strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86400;
                $col_tmp_day = date('l', $_calc_production_date);
                $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time']) * 60;
                $count_holiday++;
                $condition = true;
            } else {
                if (in_array($col_tmp_day, $col_closed_days)) {
                    $_calc_production_date += 86400;
                    $col_tmp_day = date('l', $_calc_production_date);
                    $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time']) * 60;
                    $condition = true;
                } else {
                    if ($working_time_setting[$col_tmp_day]['col-open-time'] > date('H:i', $_calc_production_date)) {
                        $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time']) * 60;
                    }
                    if ($working_time_setting[$col_tmp_day]['col-close-time'] < date('H:i', $_calc_production_date)) {
                        $_calc_production_date += 86400;
                        $col_tmp_day = date('l', $_calc_production_date);
                        $_calc_production_date = strtotime(date('00:00 Y/m/d', $_calc_production_date)) + split_time($working_time_setting[$col_tmp_day]['col-open-time']) * 60;
                        $condition = true;

                    }
                }
                if( isset($cacl_time_holiday[$count_holiday]) && strtotime($cacl_time_holiday[$count_holiday]['end-holiday']) + 86399 <= $_calc_production_date) {
                   $count_holiday++; 
                   $condition = true;
                }
            }
        }
    }

    $calc_production_date = date('H:i Y/m/d', $_calc_production_date);
    $time_delivered = $max_shipping_time * 60 + strtotime($calc_production_date);
    $calc_shipping_date = date("H:i Y/m/d", $time_delivered);
    $production_datetime_completed = date("l, d F Y, h:i A", strtotime($calc_production_date));
    $production_date_completed = date("l, d F Y", strtotime($calc_production_date));
    $shipping_date_completed = date("l, d F Y", strtotime($calc_shipping_date));
    ?>

    <div class="order-time-info">
        <div class="title mb-2"><b>Your order will be <?php echo $shipping_method_label && $shipping_method_label !== 'Self-collection' ? 'deliver' : 'ready'; ?> by :</b></div>
        <?php if ($max_shipping_time == 0) : ?>
            <div class="time  mb-2"><b><?= $production_datetime_completed; ?></b></div>
        <?php else: ?>
            <div class="time"><?= $production_date_completed; ?> - <?= $shipping_date_completed ?></div>
        <?php endif; ?>
        <div class="notice nb-order-info">
            <p class="mb-2">This timing does not take into account: <span class="text-danger">*<span></p>
            <ul>
                <li>Delivery</li>
                <li>Delays due to unforeseen circumstances</li>
                <li>Potential delays due to artwork issues / amendments</li>
            </ul>
        </div>
    </div>
    <?php
}

add_action('woocommerce_admin_order_data_after_order_details', 'botak_show_production_time_admin_order', 10, 1);
function botak_show_production_time_admin_order($order)
{
    if ($order->get_date_created()) {
        $est_time = show_est_completion($order);
        ?>

        <div class="order-time-info">
            <div class="title"><b>Your order will be completed by:</b></div>
            <div class="time"><?= $est_time['total_time']; ?></div>
            <div class="time-notice">
                <p>* This timing does not take into account:</p>
                <p>- Delays due to unforseen circumstances</p>
                <p>- Potential delays due to artwork issues</p>
            </div>
        </div>
        <?php
    }
}

add_action('woocommerce_email_order_meta', 'botak_show_production_time_in_email', 100, 4);
function botak_show_production_time_in_email($order, $sent_to_admin, $plain_text, $email)
{
    $est_time = show_est_completion($order);
    ?>

    <div style="display: inline-block; border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; margin-top: 0px; width: calc(100% - 22px);">
        <div style="margin-bottom: 0px;"><b>Your order will be completed by:</b></div>
        <div class="time"><?= $est_time['total_time']; ?></div>
        <div style="margin-bottom: 0px;">
            <p style="margin-bottom: 0px;">* This timing does not take into account:</p>
            <p style="margin-bottom: 0px;">- Delays due to unforseen circumstances</p>
            <p style="margin-bottom: 0px;">- Potential delays due to artwork issues</p>
        </div>
    </div>
    <?php
}

add_filter('woocommerce_cart_shipping_method_full_label', 'botak_show_shipping_with_duration', 10, 2);
function botak_show_shipping_with_duration($label, $method)
{
    $shipping_duration = maybe_unserialize(get_option('woocommerce_shipping_duration'));
    if (is_array($shipping_duration)) {
        if (array_key_exists("wsd_" . $method->get_instance_id(), $shipping_duration)) {
            if ($shipping_duration["wsd_" . $method->get_instance_id()] && $shipping_duration["wsd_" . $method->get_instance_id()] != 0) {
                $label .= ' (' . $shipping_duration["wsd_" . $method->get_instance_id()] . 'h)';
            }
        };
    }
    return $label;
}

add_action('wp_ajax_repayment_artwork_ajax', 'repayment_artwork_ajax');
add_action('wp_ajax_nopriv_repayment_artwork_ajax', 'repayment_artwork_ajax');

function repayment_artwork_ajax()
{
    global $wpdb;
    $result = [];
    $result['flag'] = 0;
    if (isset($_POST['order_id']) && isset($_POST['data'])) {
        if (!check_suborder_24h($_POST['order_id'])) {
            parse_str($_POST['data'], $searcharray);
            if (count($searcharray) > 0) {
                if (count($searcharray['aa_item_service']) > 0) {
                    $order2 = wc_get_order($_POST['order_id']);
                    $address = array(
                        'first_name' => $order2->billing_first_name,
                        'last_name' => $order2->billing_last_name,
                        'company' => $order2->billing_company,
                        'email' => $order2->get_billing_email(),
                        'phone' => $order2->get_billing_phone(),
                        'address_1' => $order2->billing_address_1,
                        'address_2' => $order2->billing_address_2,
                        'city' => $order2->billing_city,
                        'state' => $order2->billing_state,
                        'postcode' => $order2->billing_postcode,
                        'country' => $order2->billing_country
                    );
                    $order = wc_create_order(array('customer_id' => get_current_user_id()));
                    foreach ($searcharray['aa_item_service'] as $k => $v) {
                        if ($searcharray['aa_item_ata'][$k] == 'y') {
                            $product = wc_get_product($v);
                            $args = array(
                                'attribute_billing-period' => 'Yearly',
                                'attribute_subscription-type' => 'Both'
                            );
                            $quantity = 1;
                            $order->add_product($product, $quantity, $args);
                        }
                    }
                    $order->set_address($address, 'billing');
                    $order->set_address($address, 'shipping');
                    $order->calculate_totals();
                    $result['flag'] = 1;
                    update_post_meta($_POST['order_id'], '_data_artwwork_form', $_POST['data']);
                    update_post_meta($order->id, '_data_artwork_form', $_POST['data']);
                    $result2 = $wpdb->update(
                        $wpdb->prefix . 'posts',
                        array(
                            'post_excerpt' => 'sub_order_' . $_POST['order_id']
                        ),
                        array('ID' => $order->id)
                    );
                    send_botaksign_email($order->id, 'ARTWORK AMENDMENT', 'C1.php');
                }
            }
        } else {
            $result['flag'] = 2;
        }
    }
    echo json_encode($result);
    wp_die();
}

function get_shop_popular_product($atts)
{
    global $woocommerce_loop, $wpdb;

    extract(shortcode_atts(array(
        'tax' => 'product_cat',
        'per_cat' => '5',
        'columns' => '0',
        'include_children' => true,
        'title' => 'Popular Products',
        'link_text' => 'See all',
    ), $atts));

    ob_start();

    // setup query
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => $per_cat,
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
    );

    // query database
    $products = new WP_Query($args);

    if ($products->have_posts()):
        ?>
        <?php if (shortcode_exists('title')): ?>
        <?php echo do_shortcode('[title text="' . $title . '" link="' . get_term_link($cat, 'product_cat') . '" link_text="' . $link_text . '"]'); ?>
    <?php else: ?>
        <?php echo '<h2 class="archive-title-css">' . $title . '</h2>'; ?>
    <?php endif; ?>

        <?php woocommerce_product_loop_start(); ?>

        <?php while ($products->have_posts()): $products->the_post(); ?>

        <?php
        wc_get_template_part('content', 'product');
        ?>

    <?php endwhile; // end of the loop.
        ?>

        <?php woocommerce_product_loop_end(); ?>
    <?php
    endif;
    wp_reset_postdata();

    return '<div class="shop-popular-product columns-' . $columns . '">' . ob_get_clean() . '</div>';
}

add_shortcode('show_shop_popular_product', 'get_shop_popular_product');

add_filter('wpo_wcpdf_myaccount_actions', 'download_invoice_button', 10, 2);
function download_invoice_button($actions, $order)
{
    $actions['invoice']['name'] = 'Download';
    return $actions;
}

add_action('wp_ajax_repayment_artwork_proceed_ajax', 'repayment_artwork_proceed_ajax');
add_action('wp_ajax_nopriv_repayment_artwork_proceed_ajax', 'repayment_artwork_proceed_ajax');

function repayment_artwork_proceed_ajax()
{
    $result = [];
    $result['flag'] = 0;
    if (isset($_POST['order_id']) && isset($_POST['pro_id']) && isset($_POST['data'])) {
        $arr_pro = explode(',', $_POST['pro_id']);
        $arr_remove_item_order = array();
        $order = wc_update_order(array('order_id' => $_POST['order_id']));
        $items = $order->get_items();
        foreach ($items as $item) {
            if (function_exists('get_product')) {
                if (isset($item['variation_id']) && $item['variation_id'] > 0):
                    $_product = wc_get_product($item['variation_id']);
                else:
                    $_product = wc_get_product($item['product_id']);
                endif;
            } else {
                if (isset($item['variation_id']) && $item['variation_id'] > 0):
                    $_product = new WC_Product_Variation($item['variation_id']);
                else:
                    $_product = new WC_Product($item['product_id']);
                endif;
            }
            if (isset($_product) && $_product != false) {
                if (isset($item['variation_id']) && $item['variation_id'] > 0) {
                    $idpro = $item['variation_id'];
                } else {
                    $idpro = $item['product_id'];
                }
                if (!in_array($idpro, $arr_pro)) {
//                    array_push($arr_remove_item_order, $idpro);
                    wc_delete_order_item($idpro);
                }
            }
        }

//        $order->add_product( get_product( '3433' ), 1 );
        $order->calculate_totals();
        $result['flag'] = 1;
        update_post_meta($_POST['order_id'], '_log_status_pay_artwork_form', $_POST['data']);
    }
    echo json_encode($result);
    wp_die();
}

add_action('woocommerce_payment_complete', 'so_payment_complete', 2020, 1);
function so_payment_complete($order_id)
{
    update_post_meta($order_id, '_status_pay_artwork_form', 1);
}

add_action('wp_ajax_update_time_order', 'update_time_order');
add_action('wp_ajax_nopriv_update_time_order', 'update_time_order');

function update_time_order()
{
    $items_data = $_POST['items_data'];
    $order = wc_get_order($_POST['order']);
    //Find max production time
    $max_production_time = 0;
    $max_new_production_time = 0;
    $order_items = $order->get_items('line_item');

    $addition_prices = [];

    foreach ($items_data as $data) {
        foreach ($order_items as $item_id => $item) {
            if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field') && (int)$item_id === (int)$data['id']) {
                $old_price = (float)round($item->get_total(), 2); //Without tax
                $tax_rates = (float)round($item->get_total_tax() / $old_price, 2);
                $price_without_time_option = 0;
                $qty = $item->get_quantity();
                $options = $item->get_meta('_nbo_options');
                $origin_fields = unserialize($options['fields']);
                $origin_fields = $origin_fields['fields'];
                $item_field = $item->get_meta('_nbo_field');

                foreach ($item_field as $key => $value) {
                    foreach ($origin_fields as $field) {
                        if ($field['id'] === $key && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                            $markup_percent = (float)$field['general']['attributes']['options'][$value['value']]['markup_percent'];
                            $price_without_time_option = $old_price / (1 + $markup_percent / 100);
                            $time_quantity_breaks = $field['general']['attributes']['options'][$value['value']]['time_quantity_breaks'];
                            //Sort time_quantity_breaks by quantity
                            usort($time_quantity_breaks, "sort_time_quantity_breaks");
                            for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                                if ($i === count($time_quantity_breaks) - 1) {
                                    if ($qty >= $time_quantity_breaks[$i]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                        $max_production_time = (int)$time_quantity_breaks[$i]['time'];
                                    }
                                    break;
                                }
                                if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty < $time_quantity_breaks[$i + 1]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_production_time) {
                                    $max_production_time = (int)$time_quantity_breaks[$i]['time'];
                                }
                            }
                        }
                    }
                }
                foreach ($origin_fields as $field) {
                    if ($field['id'] === $data['field_id'] && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                        $time_quantity_breaks = $field['general']['attributes']['options'][$data['value']]['time_quantity_breaks'];
                        $new_markup_percent = (float)$field['general']['attributes']['options'][$data['value']]['markup_percent'];
                        $new_price = $price_without_time_option * (1 + $new_markup_percent / 100);
                        $addition_price = round(($new_price - $old_price) * $qty, 2);
                        $addition_prices[] = [
                            'item' => $item_id,
                            'addition_price' => round($addition_price, 2),
                            'addition_tax' => round($addition_price * $tax_rates * $qty, 2),
                        ];

                        //Sort time_quantity_breaks by quantity
                        usort($time_quantity_breaks, "sort_time_quantity_breaks");
                        for ($i = 0; $i < count($time_quantity_breaks); $i++) {
                            if ($i === count($time_quantity_breaks) - 1) {
                                if ($qty >= $time_quantity_breaks[$i]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_new_production_time) {
                                    $max_new_production_time = (int)$time_quantity_breaks[$i]['time'];
                                }
                                break;
                            }
                            if ($qty >= $time_quantity_breaks[$i]['qty'] && $qty < $time_quantity_breaks[$i + 1]['qty'] && (int)$time_quantity_breaks[$i]['time'] > $max_new_production_time) {
                                $max_new_production_time = (int)$time_quantity_breaks[$i]['time'];
                            }
                        }
                    }
                }
            }
        };
    }

    if ($order->get_date_created()) {
        $calc_production_date = calc_production_date($order->get_date_created(), $max_production_time * 60);
        $calc_new_production_date = calc_production_date($order->get_date_created(), $max_new_production_time * 60);
        $calc_new_completed_shipping_date = calc_completed_shipping_date($order, $calc_new_production_date);

        $production_date_completed = date("l, d F Y, H:i", strtotime($calc_production_date));
        $production_new_date_completed = date("l, d F Y, H:i", strtotime($calc_new_production_date));
        $order_new_date_completed = date("l, d F Y, H:i", strtotime($calc_new_completed_shipping_date));
        $difference_time = date_diff(new DateTime($production_new_date_completed), new DateTime($production_date_completed));
        $difference_hour = $difference_time->h;
        $difference_minute = $difference_time->i;

        $show_notice_time = false;
        if ($difference_time->y < 1 && $difference_time->m < 1 && $difference_time->d < 1 && $difference_time->h < 1) {
            $show_notice_time = true;
        }

        $response = [
            'production_new_date_completed' => $production_new_date_completed,
            'order_new_date_completed' => $order_new_date_completed,
            'show_notice_time' => $show_notice_time,
            'addition_prices' => $addition_prices
        ];
    } else {
        $response = [
            'production_new_date_completed' => date("l, d F Y, H:i", strtotime('00:00')),
            'show_notice_time' => false,
            'addition_prices' => []
        ];
    }

    wp_send_json($response, 200);

    wp_die();
}

add_action('admin_post_botak_push_topup_order', 'botak_push_topup_order');
add_action('admin_post_nopriv_botak_push_topup_order', 'botak_push_topup_order');

function botak_push_topup_order()
{
    if (!isset($_POST['order_id'])) {
        wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')));
    }

    $order = wc_get_order($_POST['order_id']);
    $list_item = $order->get_items('line_item');
    foreach ($_POST['items_data'] as $item_id => $data) {
        foreach ($list_item as $key => &$item) {
            if ($key === $item_id) {
                if ($item->get_meta('_nbo_options') && $item->get_meta('_nbo_field')) {
                    $options = $item->get_meta('_nbo_options');
                    $origin_fields = unserialize($options['fields']);
                    $origin_fields = $origin_fields['fields'];
                    $item_fields = $item->get_meta('_nbo_field');
                    foreach ($item_fields as $fkey => &$fvalue) {
                        foreach ($origin_fields as $field) {
                            if ($field['id'] === $fkey && isset($field['nbd_type']) && $field['nbd_type'] === 'production_time') {
                                $option_name = $field['general']['attributes']['options'][$data['value']]['name'];
                                wc_update_order_item_meta($item_id, 'Production Time', $option_name);
                            }
                        }
                        if ($fkey === $data['field_id']) {
                            $fvalue['value'] = $data['value'];
                            $item_quantity = $item->get_quantity();
                            $new_item_price = $item->get_subtotal() + $data['addition_price'];
                            $new_subtotal_tax = $item->get_subtotal_tax() + $data['addition_tax'];

                            //Recalc price and tax
                            $item->set_subtotal($new_item_price);
                            $item->set_total($new_item_price * $item_quantity);
                            $item->set_subtotal_tax($new_subtotal_tax);
                            $item->set_total_tax($new_subtotal_tax * $item_quantity);
                            $item->set_taxes(
                                array(
                                    'total' => [$new_subtotal_tax * $item_quantity],
                                    'subtotal' => [$new_subtotal_tax],
                                )
                            );
                            $item->save();
                        }
                    }
                    wc_update_order_item_meta($item_id, '_nbo_field', $item_fields);
                };
            }
        }
    }

    $order->calculate_totals();
    $order->save();

    //Get new info of order
    $norder = wc_get_order($_POST['order_id']);
    $nlist_item = $norder->get_items('line_item');
    $order_type_standard = 0;
    $order_type_rush = 1;
    $order_type_super_rush = 2;

    //CS botak update type for order
    $order_type = $order_type_standard;
    foreach ($nlist_item as $nitem_id => $nitem) {
        if ($nitem->get_meta('_nbo_options') && $nitem->get_meta('_nbo_field')) {
            $noptions = $nitem->get_meta('_nbo_options');
            $norigin_fields = unserialize($noptions['fields']);
            $norigin_fields = $norigin_fields['fields'];
            $nitem_field = $nitem->get_meta('_nbo_field');

            foreach ($nitem_field as $nkey => $nvalue) {
                foreach ($norigin_fields as $nfield) {
                    if ($nfield['id'] === $nkey && isset($nfield['nbd_type']) && $nfield['nbd_type'] === 'production_time') {
                        switch ($nfield['general']['attributes']['options'][$nvalue['value']]["name"]) {
                            case 'RUSH':
                                if ($order_type != $order_type_super_rush) {
                                    $order_type = $order_type_rush;
                                }
                                break;
                            case 'Super RUSH':
                                $order_type = $order_type_super_rush;
                                break;
                        };
                    }
                }
            }
        }
    }
    update_post_meta($_POST['order_id'], 'order_type', $order_type);

    //Send email for customer
    send_botaksign_email($_POST['order_id'], 'ORDER CONFIRMED', 'A1.php');

    wp_redirect(get_permalink(get_option('woocommerce_myaccount_page_id')) . '/view-order/' . $_POST['order_id']);
}

add_filter('woocommerce_valid_order_statuses_for_order_again', 'bbloomer_order_again_statuses');

function bbloomer_order_again_statuses($statuses)
{
    $statuses[] = 'processing';
    $statuses[] = 'pending';
    $statuses[] = 'cancelled';
    return $statuses;
}

add_action('woocommerce_cart_loaded_from_session', 'bbloomer_detect_edit_order');

function bbloomer_detect_edit_order($cart)
{
    if (isset($_GET['edit_order'], $_GET['_wpnonce']) && is_user_logged_in() && wp_verify_nonce(wp_unslash($_GET['_wpnonce']), 'woocommerce-order_again')) WC()->session->set('edit_order', absint($_GET['edit_order']));
}

add_action('woocommerce_before_cart', 'bbloomer_show_me_session');

function bbloomer_show_me_session()
{
    if (!is_cart()) return;
    $edited = WC()->session->get('edit_order');
    if (!empty($edited)) {
        $order = new WC_Order($edited);
        $credit = $order->get_total();
        wc_print_notice('Amend the artwork and resend it to us.', 'notice');
    }
}

add_action('wp_ajax_resend_artwork_proceed_ajax', 'resend_artwork_proceed_ajax');
add_action('wp_ajax_nopriv_resend_artwork_proceed_ajax', 'resend_artwork_proceed_ajax');

function resend_artwork_proceed_ajax()
{
    $result = [];
    $result['flag'] = 0;
    $check = false;
    if (isset($_POST['order_id']) && isset($_POST['nbd']) && isset($_POST['nbu'])) {
        $order = new WC_Order($_POST['order_id']);
        $order_items = $order->get_items();
        foreach ($order_items as $order_item_id => $order_item) {
            if ($_POST['nbd'] != '') {
                updateFolderDesignOrder($order_item_id, '_nbd', $_POST['nbd']);
                $check = true;
            }
            if ($_POST['nbu'] != '') {
                updateFolderDesignOrder($order_item_id, '_nbu', $_POST['nbu']);
                $check = true;
            }
        }
        if ($check) {
            WC()->session->set('edit_order', null);
            $result['flag'] = 1;
            do_action('woocommerce_thankyou', $_POST['order_id']);
            update_post_meta($_POST['order_id'], '_resend_artwork', 1);
        }
    }
    echo json_encode($result);
    wp_die();
}

add_action('wp_ajax_cancel_artwork_proceed_ajax', 'cancel_artwork_proceed_ajax');
add_action('wp_ajax_nopriv_cancel_artwork_proceed_ajax', 'cancel_artwork_proceed_ajax');

function cancel_artwork_proceed_ajax()
{
    $result = [];
    $result['flag'] = 1;
    WC()->session->set('edit_order', null);
    echo json_encode($result);
    wp_die();
}

add_filter('post_thumbnail_html', 'botak_add_default_thumbnail', 10, 5);
function botak_add_default_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr)
{
    if ($html === '') {
        $html = '<img src="' . wc_placeholder_img_src() . '" class="attachment-printcart-masonry size-printcart-masonry wp-post-image" />';
    }

    return $html;
}

add_action('wp_ajax_synch_s3_files_ajax', 'synch_s3_files_ajax');
add_action('wp_ajax_nopriv_synch_s3_files_ajax', 'synch_s3_files_ajax');

function synch_s3_files_ajax()
{
    $result = [];
    $result['flag'] = 1;
    if (isset($_POST['data']) && isset($_POST['wlh'])) {
        $arr_data = $_POST['data'];
        if (count($arr_data) > 0) {
            foreach ($arr_data as $obj) {
                if (count($obj) == 3) {
                    $filename = $obj[1];
                    if ($obj[0] == 'folders') {
                        check_folder_s3($filename, $obj[2]);
                    } else {
                        $id_file = true;
                        if ($obj[0] == 'files') {
                            $link_file = explode('?', $obj[2]);
                            if (count($link_file) == 2) {
                                $id_file = codemascot_if_the_image_is_there($link_file[0]);
                                if (empty($filename)) {
                                    $filename = basename($link_file[0]);
                                }
                            }
                        }
//                        write_log($obj);
//                        write_log($link_file[0]);
                        if (!$id_file) {
                            $wp_filetype = wp_check_filetype(basename($filename), null);
                            $attachment = array(
                                'post_mime_type' => $wp_filetype['type'],
                                'post_title' => $filename,
                                'post_content' => '',
                                'post_status' => 'inherit',
                                'guid' => $link_file[0],
                            );
                            global $wpdb;
                            $attach_id = false;
                            if ($wp_filetype['type'] == 'image/svg+xml') {
                                $sql = $wpdb->prepare(
                                    "SELECT ID FROM $wpdb->posts WHERE guid = %s",
                                    $link_file[0]
                                );
                                $attach_id = $wpdb->get_var($sql) !== null ? $wpdb->get_var($sql) : false;
                            }
                            if (!$attach_id) {
                                $attach_id = wp_insert_attachment($attachment, $filename);
                            }
                            $id_file = $attach_id;
//                                $imagenew = get_post($attach_id);
                            $fullsizepath = getimagesize($link_file[0]); //$imagenew->ID
                            $arr_att_meta = [];
                            if (isset($fullsizepath[0]) && isset($fullsizepath[1])) {
                                $arr_att_meta['width'] = $fullsizepath[0];
                                $arr_att_meta['height'] = $fullsizepath[1];
                                $arr_att_meta['file'] = $filename;
                            }
                            if (!add_post_meta($attach_id, '_nb_offload_media_url', $link_file[0], true)) {
                                update_post_meta($attach_id, '_nb_offload_media_url', $link_file[0]);
                            }
                            if (!add_post_meta($attach_id, '_wp_attachment_metadata', maybe_serialize($arr_att_meta), true)) {
                                update_post_meta($attach_id, '_wp_attachment_metadata', maybe_serialize($arr_att_meta));
                            }
                            $id_folder = current_folder_s3($_POST['wlh']);
                            if ($id_folder) {
                                $tem = $wpdb->get_row("SELECT meta_id FROM {$wpdb->prefix}term_relationships WHERE object_id = {$attach_id} AND term_taxonomy_id = {$id_folder}");
                                if (!$tem) {
                                    $wpdb->replace(
                                        $wpdb->prefix . 'term_relationships',
                                        array(
                                            'object_id' => $attach_id,
                                            'term_taxonomy_id' => $id_folder,
                                            'term_order' => 0
                                        ),
                                        array(
                                            '%d',
                                            '%d',
                                            '%d'
                                        )
                                    );
                                }
                            }
                        } else {
                            $wa_meta = get_post_meta($id_file, '_wp_attachment_metadata', true);
//                            write_log($wa_meta);
                            if (!$wa_meta) {
                                if (isset($link_file[0])) {
                                    $fullsizepath = getimagesize($link_file[0]);
                                    if ($fullsizepath !== false) {
                                        $arr_att_meta = [];
                                        if (isset($fullsizepath[0]) && isset($fullsizepath[1])) {
                                            $arr_att_meta['width'] = $fullsizepath[0];
                                            $arr_att_meta['height'] = $fullsizepath[1];
                                            $arr_att_meta['file'] = $filename;
                                            update_post_meta($id_file, '_wp_attachment_metadata', maybe_serialize($arr_att_meta));
                                        }
                                    }
                                }
                            }
                            $id_folder = current_folder_s3($_POST['wlh']);
                            if ($id_folder) {
                                global $wpdb;
                                $wpdb->update(
                                    $wpdb->prefix . 'term_relationships',
                                    array(
                                        'term_taxonomy_id' => $id_folder,
                                        'term_order' => 0
                                    ),
                                    array(
                                        'object_id' => $id_file,
                                    ),
                                    array(
                                        '%d',
                                        '%d'
                                    ),
                                    array(
                                        '%d'
                                    )
                                );
                            }
                        }
                        $arr_temp = [];
                        if (false === (get_transient('ss_file_results'))) {
                            // It wasn't there, so regenerate the data and save the transient
                        } else {
                            $arr_temp = explode(',', get_transient('ss_file_results'));
                        }
                        array_push($arr_temp, $id_file);
                        set_transient('ss_file_results', implode(',', array_unique($arr_temp)), 1 * MINUTE_IN_SECONDS);
                        if (get_option('opt_s3_file_results') !== false) {
                            $recheck = update_option('opt_s3_file_results', implode(',', array_unique($arr_temp)));
                        } else {
                            $recheck = add_option('opt_s3_file_results', implode(',', array_unique($arr_temp)));
                        }
                    }

                }
            }
        }

    }

    echo json_encode($result);
    wp_die();
}

function codemascot_if_the_image_is_there($img)
{
    global $wpdb;
    $sql = $wpdb->prepare(
        "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_nb_offload_media_url' AND (meta_value = %s OR meta_value = %s)",
        $img,
        urldecode($img)
    );
    return $wpdb->get_var($sql) !== null ? $wpdb->get_var($sql) : false;
}

function check_folder_s3($folder, $link)
{
    $arr_pat = explode('/', $link);
    if (count($arr_pat) > 3) {
        $parent_term = term_exists($arr_pat[count($arr_pat) - 3], 'nt_wmc_folder');
    }

    $term = term_exists($folder, 'nt_wmc_folder');
    $term_id = $term['term_id'];
    if (!isset($term['term_id'])) {
        $result = wp_insert_term(
            $folder,   // the term
            'nt_wmc_folder', // the taxonomy
            array(
//            'description' => 'A yummy apple.',
                'slug' => $link,
                'parent' => (isset($parent_term['term_id']) ? $parent_term['term_id'] : 0),
            )
        );
        if ($result) {
            $term_id = $result['term_id'];
            update_term_meta($result['term_id'], 'folder_position', rand(0, 10));
        }
    } else {
        if (count($arr_pat) > 4) {
            $termchildren = get_term_children($parent_term['term_id'], 'nt_wmc_folder');
            if (!in_array($term_id, $termchildren)) {
                $result = wp_insert_term(
                    $folder,
                    'nt_wmc_folder',
                    array(
                        'slug' => $link,
                        'parent' => (isset($parent_term['term_id']) ? $parent_term['term_id'] : 0),
                    )
                );
                
                if (is_array($result)) {
                    $term_id = $result['term_id'];
                    update_term_meta($result['term_id'], 'folder_position', rand(0, 10));
                }
            }
        }
    }

//    if ( !metadata_exists( 'term', $term_id, 'folder_position' ) ) {
//        update_term_meta($term_id, 'folder_position', rand(0, 10));
//    }
    $arr_temp = [];
    if (false === (get_transient('ss_folder_results'))) {
        // It wasn't there, so regenerate the data and save the transient
    } else {
        $arr_temp = explode(',', get_transient('ss_folder_results'));
    }
    array_push($arr_temp, $term_id);
    set_transient('ss_folder_results', implode(',', array_unique($arr_temp)), 1 * MINUTE_IN_SECONDS);
    if (get_option('opt_s3_folder_results')) {
        update_option('opt_s3_folder_results', implode(',', array_unique($arr_temp)));
    } else {
        add_option('opt_s3_folder_results', implode(',', array_unique($arr_temp)));
    }
}

function current_folder_s3($wlh)
{
    global $wpdb;
    $reg = preg_replace('/(\#|\/$)/i', '', $wlh);
    $re = preg_replace('/(\/|%20)/i', '-', $reg);
    $sql = $wpdb->prepare(
        "SELECT term_id FROM $wpdb->terms WHERE slug = %s",
        strtolower($re)
    );
    return $wpdb->get_var($sql) !== null ? $wpdb->get_var($sql) : false;
}

add_action('wp_ajax_delete_all_transient_files_ajax', 'delete_all_transient_files_ajax');
add_action('wp_ajax_nopriv_delete_all_transient_files_ajax', 'delete_all_transient_files_ajax');

function delete_all_transient_files_ajax()
{
    delete_transient('ss_folder_results');
    delete_transient('ss_file_results');
}

add_action('wp_ajax_synch_s3_delete_f2_ajax', 'synch_s3_delete_f2_ajax');
add_action('wp_ajax_nopriv_synch_s3_delete_f2_ajax', 'synch_s3_delete_f2_ajax');

function synch_s3_delete_f2_ajax()
{
    global $wpdb;
    $html = '';
    $arr_temp = [];
    if (get_option('opt_s3_folder_results')) {
        $arr_temp = explode(',', get_option('opt_s3_folder_results'));
    }
    if (count($arr_temp) > 0) {
        $list_term_del = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'nt_wmc_folder' AND term_taxonomy_id NOT IN (" . get_option('opt_s3_folder_results') . ")");
        if (count($list_term_del) > 0) {
            foreach ($list_term_del as $v) {
                wp_delete_term($v->term_taxonomy_id, 'nt_wmc_folder');
            }
        }
    }
    $arr_temp2 = [];
    if (get_option('opt_s3_file_results')) {
        $arr_temp2 = explode(',', get_option('opt_s3_file_results'));
    }
    if (count($arr_temp2) == 0) {
        array_push($arr_temp2, 0);
    }
    $arr_temp2 = array_filter($arr_temp2);
    $list_file_del = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_nb_offload_media_url' AND post_id NOT IN (" . implode(',', $arr_temp2) . ")");
    if (count($list_file_del) > 0) {
        $html .= '<h2>List of files will be deleted in Wordpress and Amazon S3 (' . count($list_file_del) . ' items)</h2><button id="btn-select-all" class="button select-all">Select All</button><button id="btn-delete-all" class="button">Delete Selected Media</button><div id="block-media-s3">';
        $d = 1;
        $list_ids = '';
        foreach ($list_file_del as $v) {
            $list_ids .= ($d == 1 ? '' : ',') . $v->post_id;
            $d++;
            $arr_temp = explode('/', $v->meta_value);
            if (count($arr_temp) > 4) {
                $output = array_slice($arr_temp, 3, count($arr_temp) - 4);
                $slug = 'home-' . implode('-', $output);
                $id_folder = current_folder_s3($slug);
                if ($id_folder) {
                    $tem = $wpdb->get_row("SELECT object_id FROM {$wpdb->prefix}term_relationships WHERE object_id = {$v->post_id} AND term_taxonomy_id = {$id_folder}");
                    if (!$tem) {
                        $wpdb->replace(
                            $wpdb->prefix . 'term_relationships',
                            array(
                                'object_id' => $v->post_id,
                                'term_taxonomy_id' => $id_folder,
                                'term_order' => 0
                            ),
                            array(
                                '%d',
                                '%d',
                                '%d'
                            )
                        );
                    }
                }
            }
        }
        if ($list_ids != '') {
            $html .= do_shortcode('[gallery include="' . $list_ids . '" link="none" order="DESC" orderby="ID"]');
            $list_desc = explode(',', $list_ids);
            if (count($list_desc) > 0) {
                rsort($list_desc);
                $list_ids = implode(',', $list_desc);
            }
        }
        $html .= '<input type="hidden" name="gallery_ids" value="' . base64_encode($list_ids) . '" />';
        $html .= '</div>';
    }
    echo $html;
    wp_die();
}

add_action('wp_ajax_synch_s3_delete_media_ajax', 'synch_s3_delete_media_ajax');
add_action('wp_ajax_nopriv_synch_s3_delete_media_ajax', 'synch_s3_delete_media_ajax');

function synch_s3_delete_media_ajax()
{
    $result = [];
    $result['flag'] = 1;
    if (isset($_POST['data_ids'])) {
        $arr_ids = explode(',', $_POST['data_ids']);
        if (count($arr_ids) > 0) {
            for ($i = 0; $i < count($arr_ids); $i++) {
                delete_post_meta($arr_ids[$i], '_path_bk_s3');
                wp_delete_attachment($arr_ids[$i]);
            }
        }
    }
    echo json_encode($result);
    wp_die();
}

function wc_change_admin_new_order_email_recipient($recipient, $order)
{
    if(isset($order) && $order->get_user_id()!= '') {
        $id_specialist = get_user_meta($order->get_user_id(), 'specialist', true);
        $user_specialist = get_user_by('ID', $id_specialist);
        if ($user_specialist->user_email) {
            $recipient .= ',' . $user_specialist->user_email;
        }
    }
    return $recipient;
}

// CS botak V3   specialist Linking
function nb_selected( $key, $user_select = array() ) {
    $select = false;
    if( isset($key) && count($user_select) > 0 && in_array($key , $user_select)) {
        $select = true;
    }
    return selected($select , true , false);
}

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
    <table class="form-table">
    <tr>
        <th><label for="address">Linked Specialist(s)</label></th>
        <td>
            <select style="width: 350px" name="group_specialist[]" id="nb-group-specialist" class="list-group-specialist"
                multiple="multiple" style="width: 100%;" data-placeholder="<?php _e( 'Search for a Specialist', 'web-to-print-online-designer' ); ?>">
                <?php
                    $user_select = unserialize(get_user_meta($user->data->ID , 'group_specialist' , true));
                    $users = get_users(
                        array(
                            'role'=>'specialist',
                        )
                    );
                    foreach ( $users as $key => $_user ) {
                        echo '<option value="' . esc_attr( $_user->data->ID ) . '"' . nb_selected( $_user->data->ID, $user_select ) . '>' . wp_kses_post( $_user->data->display_name) . '</option>';
                    }
                ?>
            </select>
        </td>
    </tr>
    </table>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#nb-group-specialist').select2( {
                width: 'resolve'
            });
        });
    </script>
<?php }

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {  
    if(isset($_POST['erf_user_status']) && isset($_POST['action']) && $_POST['action'] == 'update') {

        // Update specialist for order when update specialist the user
        $acf_post =  $_POST['acf'];
        if(isset($acf_post['field_5e79d59463a04']) && $acf_post['field_5e79d59463a04'] ) {
            $specialist = $acf_post['field_5e79d59463a04'];
            $args = array(
                'customer_id' => $user_id
            );
            $orders = wc_get_orders($args);
            if( is_array($orders)) {
                foreach($orders as $order) {
                    $order_id = $order->get_id();
                    if($order_id) {
                        update_post_meta($order_id, '_specialist_id', $specialist);
                    }
                }
            }
        }
        update_user_meta( $user_id, 'group_specialist', serialize($_POST['group_specialist']) );
    }    
}

add_filter('woocommerce_email_recipient_new_order', 'wc_change_admin_new_order_email_recipient', 10, 2);

add_action( 'woocommerce_view_order' , 'nb_custom_pay_paynow' , 9 );
function nb_custom_pay_paynow($order_id) {
    $order = wc_get_order($order_id);
    $url_check_out = wc_get_checkout_url();
    $order_key = get_post_meta( $order_id, '_order_key' , true);
    $url_check_out = wc_get_checkout_url().'order-received/'.$order_id.'/?key='.$order_key;
    if( $order->get_payment_method() != 'omise_paynow' || $order->get_status() != 'on-hold') {
        return;
    }
    ?>
    <style type="text/css">
        .nb-custom-pay-paynow {
            margin-bottom: 5px;
        }
        .nb-custom-pay-paynow a {
            padding: 5px 30px;
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            background: #28c475;
            cursor: pointer;
            border-radius: 0.25rem;
            border-color: #28c475;
        }
        .nb-custom-pay-paynow a:hover {
            text-decoration: none;
            background: #17864e;
        }
    </style>
    <div class="nb-custom-pay-paynow">
         <a type="button" href="<?php echo esc_attr($url_check_out);?>">PAY</a>
    </div>
    <?php
}

//processing on-hold
add_action( 'woocommerce_order_status_changed', 'nb_set_date_order_status_changed_onhold_to_processing' , 1 , 3 );
function nb_set_date_order_status_changed_onhold_to_processing($order_id , $status_f , $status_t) {
    $order = wc_get_order($order_id);
    $order_items = $order->get_items('line_item');
    if($status_f == 'on-hold' && $status_t == 'processing') {
        $props['date_created'] = date('Y-m-d H:i:s' , strtotime('now') + 8*3600);
        $order->set_props( $props );
        $order->save();

        if($order_items) {
            foreach ( $order_items as $item_id => $item ) {
                $time_completed = date( 'd/m/Y H:i a' , strtotime( v3_get_time_completed_item(v3_get_production_time_item($item ,$order , true) ,$order)['production_datetime_completed'] ) );
                wc_update_order_item_meta($item_id , '_item_time_completed' , $time_completed);
                $opt_status = 'processing';
                // wc_update_order_item_meta($item_id, 'item_status', $opt_status);
            }
        }
        update_post_meta( $order_id , '_order_status' , 'Ongoing');
        $date_completed = date( 'd/m/Y H:i a' , strtotime(show_est_completion($order)['production_datetime_completed']) );
        update_post_meta( $order_id , '_order_time_completed', $date_completed );
        update_post_meta( $order_id , '_order_time_completed_str', strtotime(show_est_completion($order)['production_datetime_completed']) );
    }
    $status_order = '';
    $status_item = '';
    if($status_t == 'pending' || $status_t == 'on-hold') {
        $status_order   = 'Pending';
        $status_item    = 'pending_payment';
    }
    if( $status_t == 'processing') {
        $status_order   = 'New';
        $status_item    = 'order_received';
    }
    if($status_t == 'completed') {
        $status_order   = 'Collected';
        $status_item    = 'collected';
    }
    if($status_t == 'cancelled' || $status_t == 'failed' || $status_t == 'refunded' ) {
        $status_order   = 'Cancelled';
        $status_item    = 'cancelled';
        update_post_meta( $order_id , '_order_time_out', date("d/m/Y H:i a" , strtotime("now") + 8*3600 ) );
    }
    if($status_t == 'failed') {
        send_botaksign_email($order_id , 'ORDER #'.$order_id.' PAYMENT FAIL', 'I1.php');
    }
    if($status_order && $status_item) {
        if($order_items) {
            v3_add_order_notes($order_id , $status_order , 'update_status_order');
            update_post_meta( $order_id , '_order_status' , $status_order);
            foreach ( $order_items as $item_id => $item ) {
                if( !wc_get_product($item->get_product_id())->is_type( 'service' ) ){
                    wc_update_order_item_meta($item_id , '_item_status' , $status_item);
                }
            }
        }
    }
    if( ($status_f == 'pending' || $status_f == 'on-hold' ) && $status_t == 'processing') {
        $order_items = $order->get_items('fee');
        $is_delete = false;
        foreach($order_items as $item) {
            if($item->get_name() == 'Reduction Of Advance Payment') {
                if($item->get_id()) {
                    $order->remove_item( absint( $item->get_id() ) );
                    $is_delete = true;
                }
            }
        }
        
        if($is_delete) {
            $order->calculate_totals( true );
        }
    }
}

function v3_add_order_notes($order_id, $change , $log) {
    $user_id = get_current_user_id();
    $user =  get_userdata($user_id);
    $user_name = $user->display_name;
    $user_email = $user->user_email;
    $type = 'order_log';
    if($log == 'update_status_order') {
        $user_name = '';
        $user_email = '';
        $log = '';
    }
    $args = array(
        'comment_post_ID' => $order_id,
        'comment_author' => $user_name,
        'comment_agent' => $change,
        'comment_content' => $log,
        'comment_author_email' => $user_email,
        'comment_type' => $type,
        'comment_approved' => 2,

    );
    wp_insert_comment($args);
}

// specialist round
//add_action('user_register' , 'v3_add_specialist_user' , 1);
function v3_add_specialist_user($user_id) {
    // global $wpdb;
    // $args = array(
    //     'role'    => 'specialist',
    //     'orderby' => 'id',
    //     'order'   => 'ASC'
    // );
    // $users = get_users( $args );
    // $round_specialist = array();
    // foreach ($users as $key => $value) {
    //     if($value->ID != '34' && $value->ID != '54' && $value->ID != '69') {    // remove Specialist from the round " Fwu, DKhitoro and Bowie "
    //         $round_specialist[] = $value->ID;
    //     }
    // }
    // $last_user_id = $wpdb->get_results('SELECT ID FROM wp_users ORDER BY ID DESC LIMIT 2')['1']->ID;
    // $spec_last_user = get_user_meta($last_user_id , 'specialist' , true);
    // $index = 0;
    // if($round_specialist && $spec_last_user) {
    //     foreach ($round_specialist as $key => $value) {
    //         if($value == $spec_last_user) {
    //             if($key < count($round_specialist) -1) {
    //                 $index = $key+1;
    //             }
    //         }
    //     }
    // }
    // $specialist_id = $round_specialist[$index];
    // $specialist_id = 596;
    // update_user_meta($user_id, 'specialist', $specialist_id );
}

// custom sent email order again after 30 minutes.
function create_options_table_pending_order(){
    global $wpdb;
    $table_name = $wpdb->prefix."sent_mail_pending_order";

    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

    if ( ! $wpdb->get_var( $query ) == $table_name ) {
        $collate = '';
        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        } 
        $tables =  "
            CREATE TABLE {$wpdb->prefix}sent_mail_pending_order ( 
             id bigint(20) unsigned NOT NULL auto_increment,
             order_id BIGINT(20) NOT NULL,
             user_id  BIGINT(20) NOT NULL,   
             created datetime NOT NULL default '0000-00-00 00:00:00',
             email varchar(255) NULL,
             PRIMARY KEY  (id)
            ) $collate; 
        ";
        @dbDelta($tables);
        return 'Bảng đã được tạo';
    } else {
        return 'Bảng đã tồn tại';
    }
    
}
function get_pending_order($order_id) {
    global $wpdb;
    if($order_id) {
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."sent_mail_pending_order WHERE order_id = %d LIMIT 1", $order_id ) );
        return $row;
    } 
}
function get_all_pending_order() {
    global $wpdb;
    $result = $wpdb->get_results( "SELECT order_id FROM ".$wpdb->prefix."sent_mail_pending_order" );
    return $result;
}
function add_pending_order($order_id) {
    global $wpdb;
    $user_id        = '';
    $date_created   = '';
    $email          = '';
    if($order_id) {
        $order  = wc_get_order($order_id);
        if($order) {
            $user_id        = $order->get_user_id();
            $date_created   = $order->get_date_created();
            $email          = get_userdata( $user_id )->user_email;
        }
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."sent_mail_pending_order WHERE order_id = %d LIMIT 1", $order_id ) );
        if(!$row) {
            $wpdb->insert( $wpdb->prefix.'sent_mail_pending_order', array(
                'order_id'     => $order_id,
                'user_id'      => $user_id,
                'created'      => $date_created,
                'email'        => $email,
            ));
        }
    }   
}

function update_pending_order($order_id , $key , $value) {
    global $wpdb;
    if($order_id) {
        $wpdb->update( $wpdb->prefix.'sent_mail_pending_order', $value , $key);
    }
}

function delete_pending_order($order_id) {
    global $wpdb;
    if($order_id) {
        $wpdb->delete( $wpdb->prefix.'sent_mail_pending_order', array( 'order_id' => $order_id) );
    }
}
add_action('woocommerce_thankyou', 'update_order_pending');
add_action('woocommerce_checkout_order_processed', 'update_order_pending' , 1);
function update_order_pending($order_id) {
    if($order_id) {
        $order          = wc_get_order($order_id);
        $status         = $order->get_status();
        $payment_method = $order->get_payment_method();
        
        if( ($payment_method == 'paypal' || $payment_method == 'omise_paynow') && ( $status == 'on-hold' || $status == 'pending' ) ) {
            add_pending_order($order_id);
        } else {
            delete_pending_order($order_id);
        }
    }
}

// block payment paypal price = 0
add_action( 'woocommerce_checkout_process' , 'botak_check_order_before_create' );
function botak_check_order_before_create() {
    $payment = $_POST['payment_method'];
    $total = WC()->cart->get_total( 'edit' );
    if( ($payment == '' || $payment == 'paypal' || $payment == 'omise_paynow') &&  ( $total == 0 || $total == '0.00' ) ) {
        wp_die( esc_html__( 'Something went wrong' ) );
    }
}


// Custom botak Sync S3
function nbd_upload_file_custom_to_s3($filename , $file_name_dir ,  $folder_name) {
    require_once(NBDESIGNER_PLUGIN_DIR . 'includes/aws/S3.php');
    if (!class_exists('S3'))require_once('S3.php');
    //AWS access info
    if (!defined('awsAccessKey')) define('awsAccessKey', get_option('nbdesigner_aws_access_key', false));
    if (!defined('awsSecretKey')) define('awsSecretKey', get_option('nbdesigner_aws_secret_key', false));
    $amazonRegion = get_option('nbdesigner_aws_region', false);
    $_bucket = get_option('nbdesigner_aws_bucket', false);
    if (!awsAccessKey || !awsSecretKey || !$_bucket) {
        return false;
    }
    $s3 = new S3(awsAccessKey, awsSecretKey);
    if(strpos($filename , '/') === 0 ) { $path_file = $folder_name.$filename;  }  else { $path_file = $folder_name.'/'.$filename; }
    if ($s3->putObjectFile($file_name_dir, "$_bucket", $path_file, S3::ACL_PUBLIC_READ_WRITE)) {
        // $contents = $s3->getBucket($_bucket);
        //unlink($file_name_dir);
        return 'https://'.$_bucket.'.s3.'.$amazonRegion.'.amazonaws.com/'.$path_file;
        
    } else {
        return false;
    }

    // $awsAccessKey = get_option('nbdesigner_aws_access_key', false);
    // $awsSecretKey = get_option('nbdesigner_aws_secret_key', false);
    // $amazonRegion = 'ap-southeast-1';
    // $bucket = get_option('nbdesigner_aws_bucket', false);
    // $client = new Aws\S3\S3Client([
    //     'version' => 'latest',
    //     'region'  => 'ap-southeast-1',
    //     'credentials' => array(
    //         'key' => $awsAccessKey,
    //         'secret' => $awsSecretKey
    //     )
    // ]);
    // $result = $client->putObject(array(
    //     'Bucket'     => $_bucket,
    //     'Key'        => $path_file,
    //     'SourceFile' => $file_name_dir,
    // ));

    // if(file_get_contents('https://'.$_bucket.'.s3.amazonaws.com/'.$path_file)) {
    //     return 'https://'.$_bucket.'.s3.amazonaws.com/'.$path_file;
    // }
}
function nbd_upload_object_custom_to_s3($filename , $file_name_dir ,  $folder_name) {
    require_once(NBDESIGNER_PLUGIN_DIR . 'includes/aws/S3.php');
    if (!class_exists('S3'))require_once('S3.php');
    //AWS access info
    if (!defined('awsAccessKey')) define('awsAccessKey', get_option('nbdesigner_aws_access_key', false));
    if (!defined('awsSecretKey')) define('awsSecretKey', get_option('nbdesigner_aws_secret_key', false));
    $_bucket = get_option('nbdesigner_aws_bucket', false);
    if (!awsAccessKey || !awsSecretKey || !$_bucket) {
        return false;
    }
    $s3 = new S3(awsAccessKey, awsSecretKey);
    if(strpos($filename , '/') === 0 ) { $path_file = $folder_name.$filename;  }  else { $path_file = $folder_name.'/'.$filename; }
    if ($s3->putObjectString($file_name_dir, "$_bucket", $path_file, S3::ACL_PUBLIC_READ)) {
        return 'https://'.$_bucket.'.s3.amazonaws.com/'.$path_file;
    } else {
        return false;
    }
}
function nbd_get_url_s3() {
    $_bucket = get_option('nbdesigner_aws_bucket', false);
    if($_bucket) {
        return 'https://'.$_bucket.'.s3.amazonaws.com/';
    } else {
        return false;
    }
}
function nbd_delete_directory($dirname) {
    if (is_dir($dirname))
           $dir_handle = opendir($dirname);
     if (!$dir_handle)
          return false;
     while($file = readdir($dir_handle)) {
           if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                     unlink($dirname."/".$file);
                else
                     delete_directory($dirname.'/'.$file);
           }
     }
     closedir($dir_handle);
     rmdir($dirname);
     return true;
}

function botak_access_key_s3() {
    require_once(NBDESIGNER_PLUGIN_DIR . 'includes/aws/S3.php');
    // require_once(CUSTOM_BOTAKSIGN_PATH.'includes/bucket-browser-for-aws-s3/aws/aws-autoloader.php');
    if (!class_exists('S3'))require_once('S3.php');
    //AWS access info
    $awsAccessKey = get_option('nbdesigner_aws_access_key', false);
    $awsSecretKey = get_option('nbdesigner_aws_secret_key', false);
    $amazonRegion = get_option('nbdesigner_aws_region', false);
    if (!defined('awsAccessKey')) define('awsAccessKey', get_option('nbdesigner_aws_access_key', false));
    if (!defined('awsSecretKey')) define('awsSecretKey', get_option('nbdesigner_aws_secret_key', false));
    $bucket = get_option('nbdesigner_aws_bucket', false);
    if (!awsAccessKey || !awsSecretKey || !$bucket) {
        return 'Not Connection!';
    }
    $s3 = new S3(awsAccessKey, awsSecretKey);
    $result['bucket'] = $bucket;
    $result['s3'] = $s3;
    return $result;
}

function botak_get_list_files_upload_from_s3( $uri ){
    $list_files = botak_get_list_file_s3($uri);
    $create_preview = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
    $files = array();
    foreach ($list_files as $file ){
        $ext = pathinfo( $file, PATHINFO_EXTENSION );
        $filename = pathinfo($file, PATHINFO_BASENAME);
        $src       = Nbdesigner_IO::get_thumb_file( $ext , '');
        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
            $dir        = pathinfo( $file, PATHINFO_DIRNAME );
            $file_headers = @get_headers($dir.'_preview/'.$filename);
            if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
                $exists = false;
            }
            else {
                $exists = true;
            }
            if( $exists ){
                $src = $dir.'_preview/'.$filename;
            }else if( $ext == 'pdf' && file_exists($dir.'_preview/'.$filename.'.jpg' ) ){
                $src = $dir.'_preview/'.$filename.'.jpg';
            }else{
                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
            }
        }else {
            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
        }
        $files[] = array(
            'src'   =>  $src,
            'name'  =>  $filename
        );
    }
    return $files;
}
function botak_remove_obj_from_s3($uri) {
    $awsAccessKey = get_option('nbdesigner_aws_access_key_remove', false);
    $awsSecretKey = get_option('nbdesigner_aws_secret_key_remove', false);
    $amazonRegion = get_option('nbdesigner_aws_region', false);
    $bucket = get_option('nbdesigner_aws_bucket', false);
    try {
        $s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $amazonRegion,
            'credentials' => array(
                'key' => $awsAccessKey,
                'secret' => $awsSecretKey
            )
        ]);

        $result = $s3->deleteObject(array(
            'Bucket' => $bucket,
            'Key'    => $uri
        ));
        return $result;
    } catch (Exception $e) {
        
    }
    
}
function botak_coppy_folder_from_s3($uri, $new_name = '') {
    if($new_name != '') {
        $awsAccessKey = get_option('nbdesigner_aws_access_key', false);
        $awsSecretKey = get_option('nbdesigner_aws_secret_key', false);
        $amazonRegion = get_option('nbdesigner_aws_region', false);
        $bucket = get_option('nbdesigner_aws_bucket', false);

        $s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => $amazonRegion,
            'credentials' => array(
                'key' => $awsAccessKey,
                'secret' => $awsSecretKey
            )
        ]);

        $uri = trim($uri, '/'). '/';

        $objects = $s3->getIterator('ListObjects', array('Bucket' => $bucket, 'Prefix' => $uri, 'Delimiter'=>'/'));
        $result = false;
        foreach ($objects as $key => $object) {
            $path = $object['Key'];

            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $basename = basename($path);
            if($ext) {
                $uri_array = explode('/', $uri);
                if( count($uri_array) > 1 ) {
                    $uri_array[count($uri_array) - 2] = $new_name;
                    $path_new = implode('/' , $uri_array);
                    $result = false;
                    $res = $s3->copyObject([
                        'Bucket'     => $bucket,
                        'Key'        => "{$path_new}{$basename}",
                        'CopySource' => "{$bucket}/{$path}",
                        'ACL'        => "public-read-write"
                    ]);
                    if( $res ) {
                        $result = true;
                    }
                }
            }
            
        }
        return $result;
    }
    return false;
}

function botak_get_file_content_s3($uri) {
    $s3 = botak_access_key_s3()['s3'];
    $bucket = botak_access_key_s3()['bucket'];
    return $s3->getObject($bucket , $uri)->body;
}
function botak_create_folder_s3($uri) {
    $s3 = botak_access_key_s3()['s3'];
    $bucket = botak_access_key_s3()['bucket'];
    if( substr($uri, -1) != '/') {
        $uri .= '/';
    }
    return $s3->putObject('Folder design' , $bucket , $uri);
}


function botak_get_list_file_s3($uri) {
    $awsAccessKey = get_option('nbdesigner_aws_access_key', false);
    $awsSecretKey = get_option('nbdesigner_aws_secret_key', false);
    $amazonRegion = get_option('nbdesigner_aws_region', false);
    $bucket = get_option('nbdesigner_aws_bucket', false);

    $s3 = new Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => $amazonRegion,
        'credentials' => array(
            'key' => $awsAccessKey,
            'secret' => $awsSecretKey
        )
    ]);
    if( substr($uri, -1) != '/') {
        $uri .= '/';
    }

    $results = array();
    $objects = $s3->getIterator('ListObjects', array('Bucket' => $bucket, 'Prefix' => $uri));
    
    foreach ($objects as $key => $object) {

        $file_headers = @get_headers(nbd_get_url_s3().$object['Key']);
        if($file_headers && $file_headers[0] == 'HTTP/1.1 200 OK') {
            $results[] = nbd_get_url_s3().$object['Key'];
        }
    }
    return $results;
}
function botak_check_link_exists_s3($link) {
    $file_headers = @get_headers($link);
    if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
        return false;
    }
    else {
        return true;
    }
}
function botak_create_preview_img_upload_s3( $file_name , $link , $folder ) {
    $preview_width = nbdesigner_get_option('nbdesigner_file_upload_preview_width');
    $ext = pathinfo($link, PATHINFO_EXTENSION);
    $create_preview = false;
    if( $ext == 'png' ){
        $create_preview = true;
        $image_resize = NBD_Image::nbdesigner_resize_imagepng($link, $preview_width, $preview_width );
        ob_start();
        echo imagepng($image_resize);
        $image_content = ob_get_contents();
        ob_clean();
        
    }else if( $ext == 'jpg' ){
        $create_preview = true;
        $image_resize = NBD_Image::nbdesigner_resize_imagejpg($link, $preview_width, $preview_width);
        ob_start();
        echo imagejpeg($image_resize);
        $image_content = ob_get_contents();
        ob_clean();
    }else if( is_available_imagick() && $ext == 'pdf' ){
        $create_preview = false;
        // $file_name .= '.jpg';
        // $image_resize = botak_link_pdf_to_jpg($link , $preview_width , $preview_width);
        // $image_content = $image_resize;
    } 
    if($create_preview) {
        $upload_preview = nbd_upload_object_custom_to_s3( $file_name , $image_content  , $folder ); 
    } else {
        $upload_preview = '';
    }
    
    if( botak_check_link_exists_s3($upload_preview) ) {
        return $upload_preview;
    } else {
        return false;
    }
}
function botak_link_pdf_to_jpg($link, $w = 100, $h = 100) {
    if($link) {
        $image = new Imagick($link);
        $image->setResolution(72,72);
        $image->resizeImage( $w, $h, imagick::FILTER_LANCZOS, 1, true);
        $image->setImageFormat('jpeg');
        $img = $image->getImageBlob();
        // $img = NBD_Image::nbdesigner_resize_imagejpg($img , 300 , 300);
        ob_start();
        echo $img;
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }
}

// Custom upload S3 by js

add_filter('nbd_js_object' , 'botak_access_key_s3_to_js' , 15 , 1);
function botak_access_key_s3_to_js($args) {
    $awsAccessKey = get_option('nbdesigner_aws_access_key', false);
    $awsSecretKey = get_option('nbdesigner_aws_secret_key', false);
    $awsRegion    = get_option('nbdesigner_aws_region', false);
    $awsBucket    = get_option('nbdesigner_aws_bucket', false);
    $args['awsAccessKey'] = $awsAccessKey;
    $args['awsSecretKey'] = $awsSecretKey;
    $args['awsRegion'] = $awsRegion;
    $args['awsBucket'] = $awsBucket;
    return $args;
}
add_action( 'wp_enqueue_scripts', 'botak_add_admin_scripts' );
function botak_add_admin_scripts() {
    wp_register_script('aws-sdk', CUSTOM_BOTAKSIGN_URL . 'assets/js/aws-sdk.min.js?t='.strtotime("now") , array() , '2.693.0');
}

add_filter( 'nbd_depend_js', 'botak_enqueue_aws_sdk' , 10 , 1);
function botak_enqueue_aws_sdk($args) {
    // if ( ! is_front_page() ) {
    //     $args[] = 'aws-sdk';
    // }
    $args[] = 'aws-sdk';
    return $args;
}