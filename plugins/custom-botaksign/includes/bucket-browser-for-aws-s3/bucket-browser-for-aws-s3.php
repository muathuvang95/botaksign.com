<?php
if (!function_exists('is_plugin_active'))
    require_once(ABSPATH . '/wp-admin/includes/plugin.php');
if (!is_plugin_active('nb-offload-media/nb-offload-media.php')) {
    require(dirname(__FILE__) . "/aws/aws-autoloader.php");
}

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\Exception\S3Exception;

add_action('admin_enqueue_scripts', 's3_browse_register_scripts');
function s3_browse_register_scripts()
{
    wp_register_script('browse-s3-js', plugins_url('/assets/js/script.js', __FILE__), array(), '', true);


    wp_register_style('browse-s3-css', plugins_url('/assets/css/styles.css', __FILE__));
    wp_enqueue_style('browse-s3-css');

}

//creates an entry on the admin menu for s3 uploader
add_action('admin_menu', 's3_browse_plugin_menu');

//creates a menu page with the following settings
function s3_browse_plugin_menu()
{
    add_submenu_page('tools.php', 'S3 Synch File & Folder', 'S3 Synch File & Folder', 'manage_options', 's3-browse-settings', 's3_browse_display_settings');
}

//on-load, sets up the following settings for the plugin
add_action('admin_init', 's3_browse_settings');

function s3_browse_settings()
{
    register_setting('browse-s3-plugin-settings-group', 's3_browse_aws_access_key');
    register_setting('browse-s3-plugin-settings-group', 's3_browse_aws_secret');
    register_setting('browse-s3-plugin-settings-group', 's3_browse_aws_region');
    register_setting('browse-s3-plugin-settings-group', 's3_browse_aws_bucket');
}

//displays the settings page
function s3_browse_display_settings()
{
//    $aws_bucket = esc_attr( get_option('s3_browse_aws_bucket') );
    echo "<h1>S3 Synch File & Folder</h1><form method=\"post\" action=\"options.php\" style='display: none;'>";

    settings_fields('browse-s3-plugin-settings-group');
    do_settings_sections('browse-s3-plugin-settings-group');

    echo "
    <style>.seperator { border-bottom: 1px solid black; }</style>
    <div>
<table class=\"form-table\">
    <tr><td colspan=\"3\"><h2>General AWS S3 Settings (All REQUIRED)</h2></td></tr> 
       <tr valign=\"top\">
        <th scope=\"row\">AWS Access Key</th>
        <td><input type=\"text\" name=\"s3_browse_aws_access_key\" value=\"" . esc_attr(get_option('s3_browse_aws_access_key')) . "\" /></td>
<td><p>Enter your AWS access key here.</p></td>
        </tr>
         
        <tr valign=\"top\">
        <th scope=\"row\">AWS Secret Key</th>
        <td><input type=\"text\" name=\"s3_browse_aws_secret\" value=\"" . esc_attr(get_option('s3_browse_aws_secret')) . "\" /></td>
<td><p>Shown only when creating account.</p></td>
        </tr>
        
        <tr valign=\"top\">
        <th scope=\"row\">AWS Region</th>
        <td><input type=\"text\" name=\"s3_browse_aws_region\" value=\"" . esc_attr(get_option('s3_browse_aws_region')) . "\" /></td>
<td><p>Get your region from your S3 url. Ex) us-west-1</p></td>
        </tr>
        
        <tr valign=\"top\" class=\"seperator\">
        <th scope=\"row\">AWS Bucket</th>
        <td><input type=\"text\" name=\"s3_browse_aws_bucket\" value=\"" . $aws_bucket . "\" /></td>
        <td></td>
        </tr></table>";

    submit_button();

    echo "</form><br><br>";

    echo "</div>";

    if (class_exists('NB_Offload_Media')) {
        $aws_bucket = NB_Offload_Media::$data['amazon_s3_bucket'];
        echo '<div id="btn-s3-synch-f2" class="button-syn-s3"><span>Import</span></div>';
        echo '<div id="btn-s3-export-f2" class="button-syn-s3"><span>Export</span></div>';
        echo '<div id="wrap-media-list-remove"></div>';
        if ($aws_bucket != '') {
            echo do_shortcode('[s3browse bucket=' . $aws_bucket . ']');
        }
    } else {
        echo '<div class="error">
            <p>Requires activation and configuration of <strong>NB Offload Media</strong> to be able to sync.</p>
        </div>';
    }
}


//shortcode-to-display-bucket
function s3_browse_shortcode_disp($atts)
{
    if (!class_exists('NB_Offload_Media')) {
        return;
    }

    $aws_access_key = NB_Offload_Media::$data['amazon_s3_credentials_key'];
    $aws_secret = NB_Offload_Media::$data['amazon_s3_credentials_secret'];
    $aws_region = NB_Offload_Media::$data['amazon_s3_region'];

    if ($aws_access_key == '' || $aws_secret == '' || $aws_region == '') {
        echo "Make sure your access key, secret key, and region are all entered.";
        return;
    }

    //Handles attribures. If none are specified, defaults to no scroll, 1st drive
    $atts = shortcode_atts(
        array(
            'bucket' => 'none',
        ), $atts, 's3browse');

    $bucket = $atts['bucket'];


    if ($bucket == 'none') {

        echo "You must enter a bucket name in your shortcode. [s3browse bucket=bucketname]";
        return;


    }

    echo "<div class=\"files-div\"><div class=\"filemanager\">

        <div class=\"search\">
            <input type=\"search\" placeholder=\"Find a file..\" />
        </div>

        <div class=\"breadcrumbs\"></div>

        <ul class=\"data\"></ul>

        <div class=\"nothingfound\">
            <div class=\"nofiles\"></div>
            <span>No files here.</span>
        </div>

    </div>
    </div>";


    $credentials = new Credentials("$aws_access_key", "$aws_secret");

//Instantiate the S3 client with your AWS credentials
    $s3Client = S3Client::factory(array(
        'credentials' => $credentials,
        'region' => "$aws_region",
        'version' => 'latest'));

    try {
        $objects = $s3Client->getIterator('ListObjects', array('Bucket' => $bucket));

        $path_array = array();
        $size_array = array();
        $link_array = array();

        foreach ($objects as $object) {
            if (!isset($objectarray)) {
                $objectarray = array();
            }
            //print_r($object);
            $name = $object['Key'];
//            write_log('key object:');
//            write_log($name);
            $size = $object['Size'];

            if ($object['Size'] != '0') {

                $base = basename($object['Key']);

                $cmd = $s3Client->getCommand('GetObject', [
                    'Bucket' => "$bucket",
                    'Key' => "$name",
                    'ResponseContentType' => 'application/octet-stream',
                    'ResponseContentDisposition' => 'attachment; filename="' . $base . '"',
                ]);

                $request = $s3Client->createPresignedRequest($cmd, '+60 minutes');

                $link = (string)$request->getUri();
                $path = 'Home/' . $name;

                $path_array[] = $path;
                $size_array[] = $size;
                $link_array[] = $link;

            }

        }

        function &placeInArray(array &$dest, array $path_array, $size, $pathorig, $link)
        {
            // If we're at the leaf of the tree, just push it to the array and return
            //echo $pathorig;
            //echo $size."<br>";

            global $folders_added;
            if (count($path_array) === 1) {
                if ($path_array[0] !== '') {
                    $file_array = array();
                    $file_array['name'] = $path_array[0];
                    $file_array['size'] = $size;
                    $file_array['type'] = 'file';
                    $file_array['path'] = $pathorig;
                    $file_array['link'] = $link;
                    array_push($dest['items'], $file_array);
                }
                return $dest;
            }

            // If not (if multiple elements exist in path_array) then shift off the next path-part...
            // (this removes $path_array's first element off, too)
            $path = array_shift($path_array);

            if ($path) {

                $newpath_temp = explode($path, $pathorig);
                $newpath = $newpath_temp[0] . $path . '/';
                // ...make a new sub-array for it...


                //if (!isset($dest['items'][$path])) {
                if (!in_array($newpath, $folders_added, true)) {
                    $dest['items'][] = array(

                        'name' => $path,
                        'type' => 'folder',
                        'path' => $newpath,
                        'items' => array()

                    );
                    $folders_added[] = $newpath;
                    //print_r($folders_added);
                }
                $count = count($dest['items']);
                $count--;
                //echo $count.'<br>';
                //print_r($dest['items'][$path]);

                // ...and continue the process on the sub-array
                return placeInArray($dest['items'][$count], $path_array, $size, $pathorig, $link);
            }

            // This is just here to blow past multiple slashes (an empty path-part), like
            // /path///to///thing
            return placeInArray($dest, $path_array, $size, $pathorig, $link);
        }

        $output = array();
        $folders_added = array();
        $i = 0;
        foreach ($path_array as $path) {
            $size = $size_array[$i];
            $link = $link_array[$i];
            placeInArray($output, explode('/', $path), $size, $path, $link);
            $i++;
        }


        $json_final = json_encode($output['items'][0]);

        //enques the js script and sends the json object to it.
        wp_enqueue_script('browse-s3-js');
        wp_localize_script('browse-s3-js', 's3_browse_vars', array(
                'json_array' => __($json_final)
            )
        );

    } catch (S3Exception $e) {

        echo $e->getMessage() . "\n";
    }


}

//shortcode for form
add_shortcode('s3browse', 's3_browse_shortcode_disp');

add_action('delete_attachment', 'delete_attachment_s3', 100, 1);

function delete_attachment_s3($postID) {
    if (!class_exists('NB_Offload_Media')) {
        return;
    }
    $url = NB_Offload_Media::$data['amazon_s3_url'];
    $key = urldecode(str_replace($url, '', get_post_meta($postID, '_nb_offload_media_url', true)));
    if ($key && strpos(get_post_meta($postID, '_nb_offload_media_url', true), $url)!==false) {
        $aws_access_key = NB_Offload_Media::$data['amazon_s3_credentials_key'];
        $aws_secret = NB_Offload_Media::$data['amazon_s3_credentials_secret'];
        $aws_region = NB_Offload_Media::$data['amazon_s3_region'];

        if ($aws_access_key == '' || $aws_secret == '' || $aws_region == '') {
            return;
        }

        $bucket = NB_Offload_Media::$data['amazon_s3_bucket'];
        if ($bucket == 'none') {
            return;
        }

        $credentials = new Credentials("$aws_access_key", "$aws_secret");

        $s3Client = S3Client::factory(array(
            'credentials' => $credentials,
            'region' => "$aws_region",
            'version' => 'latest'));

        $s3Client->deleteObject([
            'Bucket' => $bucket,
            'Key' => $key
        ]);
    }
}