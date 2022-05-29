<?php
/**
* Template Name: Update DataBase

*/
// foreach($plotting_options as $plotting_option) {
//     if($plotting_option['date'] == 'same_day') {
//         $periods = explode('-' , $plotting_option['period_calc'] );
//         $time_from = $date . ' ' . $periods[0];
//         $time_to = $date . ' ' . $periods[1];
//         $str_date_f = strtotime($time_from);
//         $str_date_t = strtotime($time_to);
//         $query_test = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) INNER JOIN wp_woocommerce_order_items ON ( wp_posts.ID = wp_woocommerce_order_items.order_id ) WHERE wp_posts.post_type = 'shop_order' AND (wp_postmeta.meta_key = '_nbo_enable') AND (wp_woocommerce_order_items.order_item_type = 'shipping' AND wp_woocommerce_order_items.order_item_name = 'Delivery')  AND ( wp_postmeta.meta_key = '_order_time_completed_str' AND wp_postmeta.meta_value BETWEEN '${str_date_f}' AND '${str_date_t}')";
//         $order_test = $wpdb->get_results($query_test );
//     }
//     if($plotting_option['date'] == 'next_day') {
//         $_periods = explode('-' , $plotting_option['period_calc'] );
//         $_time_from = $date . ' ' . $_periods[0];
//         $_time_to = $date . ' ' . $_periods[1];
//         $_str_date_f = strtotime($_time_from) - 24*3600;
//         $_str_date_t = strtotime($_time_to) - 24*3600;
//         $_query_test = "SELECT ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) INNER JOIN wp_woocommerce_order_items ON ( wp_posts.ID = wp_woocommerce_order_items.order_id ) WHERE wp_posts.post_type = 'shop_order' AND (wp_postmeta.meta_key = '_nbo_enable') AND (wp_woocommerce_order_items.order_item_type = 'shipping' AND wp_woocommerce_order_items.order_item_name = 'Delivery')  AND ( wp_postmeta.meta_key = '_order_time_completed_str' AND wp_postmeta.meta_value BETWEEN '${_str_date_f}' AND '${_str_date_t}')";
//         $_order_test = $wpdb->get_results($_query_test );
//     }
// }


// SELECT * FROM `wp_woocommerce_order_itemmeta` INNER JOIN `wp_woocommerce_order_items` on wp_woocommerce_order_itemmeta.order_item_id = wp_woocommerce_order_items.order_item_id INNER JOIN wp_posts ON wp_woocommerce_order_items.order_id = wp_posts.ID WHERE wp_posts.post_type = 'shop_order' AND wp_posts.post_date <= '2021-04-01 00:00:00'


//zip file s3
// $path    = 'C:\Users\votua\OneDrive\Desktop\fixed/';
// $files = scandir($path);
// $files = array_diff(scandir($path), array('.', '..'));
// foreach($files as $file){
// $order_id = "123";
// $order = wc_get_order($order_id);
// if($order ) {
//     $product_s3 = $order->get_items();
//     foreach($product_s3 as $order_item_id => $product) {
//         $nbd_item_key_s3 = wc_get_order_item_meta($order_item_id, '_nbd'); 
//         if($nbd_item_key_s3) {
//             $path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key_s3;
//             $resources = (array)json_decode( file_get_contents( $path. '/design.json' ) );
//             foreach($resources as $k1 => $side ) {
//                 if( isset($side->objects) ) {
//                     foreach($side->objects as $k2 => $layer) {
//                         if($layer->type == 'image') {
//                             $image_url = $layer->origin_url;
//                             $exif = exif_read_data($image_url);
//                             echo $order_item_id.'---------'.$exif['Orientation'].'<br>';
//                             if(isset($exif['Orientation']) && $exif['Orientation'] > 4) {
//                                 list( $height, $width )     = getimagesize( $image_url );
//                             } else {
//                                 list( $width, $height )     = getimagesize( $image_url );
//                             }
//                             $resources[$k1]->objects[$k2]->width = $width;
//                             $resources[$k1]->objects[$k2]->height = $height;
//                             $resources[$k1]->objects[$k2]->origin_width = $width;
//                             $resources[$k1]->objects[$k2]->origin_height = $height;
                            
//                             $json = json_encode($resources);
//                             // file_put_contents($path. '/design.json', $json);
//                         }
//                     }
//                 }
//             }
//         }     
//     }
// }

// $row = 1;
// $data_folder = [];
// if (($handle = fopen(get_stylesheet_directory()."/s3.csv", "r")) !== FALSE) {
//     while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//         $row++;
//         $folder = $data[0];
//         $strtime = '';
//         if( strripos( $data[0] , '_preview') > 0 ) {
//             $strtime = (int) substr(  $data[0] , (strripos( $data[0] , '_preview') - 10) , 10);
//         } else {
//             $strtime = (int) substr(  $data[0] , (strripos( $data[0] , '/') - 10) , 10);
//         }
//         if($strtime != 'Key' && $strtime < 1630454400) {
//             $data_folder[] = array(
//                 'folder' => $data[0],
//                 'date'  => $strtime,
//                 'date_modifiled'    => date('H:i d/m/Y' , $strtime),
//             );
//         }
//     }
//     fclose($handle);
// }
// file_put_contents(get_stylesheet_directory()."/test.json", json_encode($data_folder));
// echo '<pre>';
// var_dump($data_folder);


$fileRemove = json_decode( file_get_contents(get_stylesheet_directory()."/test.json") );

try{
    $awsAccessKey = 'AKIAX4QORCYMGSQXVT55';
    $awsSecretKey = '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';
    $amazonRegion = 'ap-southeast-1';
    $bucket = 'botaksignorder';

    // $awsAccessKey = 'AKIASEY7SWHUG4V37LH4';
    // $awsSecretKey = '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';
    // $amazonRegion = 'us-east-1';
    // $bucket = 'botaksign-dev';

    $s3Client = new Aws\S3\S3Client(array(
        'version' => 'latest',
        'region'  => $amazonRegion,
        'credentials' => array(
            'key'    => $awsAccessKey,
            'secret' => $awsSecretKey,
        )
    ));




    // foreach($fileRemove as $key => $value) {
    //     $folder = $value->folder;
    //     if($folder && $folder != 'reupload-design/') {
    //         if( 26000 <= $key && $key < 27000) {
    //             $result = $s3Client->deleteMatchingObjects( $bucket, $folder);
    //         } 
    //     }
    // }
    // $result = $s3Client->deleteMatchingObjects( $bucket, 'reupload-design/2fff1461617520838/');
    echo '<pre>';
    var_dump($result);
}
catch(Exception $e) {

   exit($e->getMessage());
} 

function cs_remove_obj_from_s3($uri) {
    $s3 = botak_access_key_s3()['s3'];
    $bucket = botak_access_key_s3()['bucket'];
    if($uri) {
        $s3->deleteObject($bucket , $uri);
    }
}

function listFileInFolder($uri) {

    $s3 = new Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => 'ap-southeast-1',
        'credentials' => array(
            'key' => $awsAccessKey,
            'secret' => $awsSecretKey
        )
    ]);
    if( substr($uri, -1) != '/') {
        $uri .= '/';
    }
    $results = array();
    $objects = $s3->getIterator('ListObjects', array('Bucket' => $bucket, 'Prefix' => '/'));
    // echo '<pre>';
    // var_dump($s3);
    // foreach ($objects as $key => $object) {

    //     $file_headers = @get_headers(nbd_get_url_s3().$object['Key']);
    //     if($file_headers && $file_headers[0] == 'HTTP/1.1 200 OK') {
    //         $results[] = nbd_get_url_s3().$object['Key'];
    //     }
    // }
    // return $results;
}
// listFileInFolder('reupload-design/');