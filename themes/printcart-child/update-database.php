<?php
/**
* Template Name: Update DataBase

*/
// get_header();
// global $wpdb;
// $sql = "SELECT ID FROM wp_posts WHERE wp_posts.post_type = 'shop_order'";
// $options = $wpdb->get_results($sql, 'ARRAY_A');

// foreach ($options as $key => $value) {
//     $order_id = $value['ID'];
//     $order = wc_get_order($order_id);
//     $timeline_str_f = strtotime('00:00 24-01-2021');
//     $timeline_str_t = strtotime('23:59 31-01-2021');
//     $date_create_str = $order->get_date_created()->getTimestamp() + 8*3600;

//     if($order_id) {
//         if( $date_create_str < $timeline_str_t && $date_create_str > $timeline_str_f && get_post_meta( $order_id , '_order_status' , true) == 'Collected' ) {
//             wp_update_post(array(
//                 'ID'    =>  $order_id,
//                 'post_status'   =>  'wc-completed'
//             ));
//         } 
//     }
// }
?>
<!-- <style type="text/css">
    #update-database {
        background: #067b38; 
        padding: 5px 15px; 
        border: 1px #2ecc71 solid; 
        border-radius: 5px; 
        color: #fff; 
        cursor: pointer;
    }
    #update-database:hover {
        background: #fff; 
        color: #067b38; 
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<div class="container">
    <h3>Update Status Order Vue</h3>
    <input type="button" name="" id="update-database" value="Update DataBase">
    <div class="results"></div>
    <input type="button" name="" id="show_id" value="Show ID Order" style="margin: 10px;">
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#update-database').on('click' , function() {
            $.ajax({
                type : "post",
                dataType : "json", 
                url : '<?php echo admin_url('admin-ajax.php');?>', 
                data : {
                    action: "update_database",
                },
                context: this,
                beforeSend: function(){
            
                },
                success: function(response) {
                    //Làm gì đó khi dữ liệu đã được xử lý
                    if(response.data.end) {
                        $('.results').prepend('<h3>Đã Xong</h3>');
                    }
                },
                error: function( jqXHR, textStatus, errorThrown ){
                    console.log( 'The following error occured: ' + textStatus, errorThrown );
                }
            })

        })
    })
</script>

 -->
<?php 
// global $wpdb;
// $sql = "SELECT ID FROM wp_users";
// $results = $wpdb->get_results($sql);
// $s1 = '<b> specialist : Erick </b> <br>';
// $s2 = '<b> specialist : Sam </b> <br>';
// $s3 = '<b> specialist : Terry </b> <br>';

// foreach ($results as $key => $value) {
//     $user_id = $value->ID;
//     $specialist = get_user_meta($user_id , 'specialist' , true);
//     if($specialist  == 802) {
//         $s1 .= $user_id . '<br>';
//         //update_user_meta($user_id , 'specialist' , 802);
//     }
//     if($specialist  == 343) {
//         $s2 .= $user_id . '<br>';
//         //update_user_meta($user_id , 'specialist' , 343);
//     }
//     if($specialist  == 596) {
//         $s3 .= $user_id . '<br>';
//         //update_user_meta($user_id , 'specialist' , 596);
//     }
// }
// echo $s1 . '<br>--------------------------<br>';
// echo $s2 . '<br>--------------------------<br>';
// echo $s3 . '<br>--------------------------<br>';


// echo 'Update Specialist';
// global $wpdb;
// $sql = "SELECT ID FROM wp_users";
// $results = $wpdb->get_results($sql);
// $s1 = 'Start';
// foreach ($results as $key => $value) {
//     $user_id = $value->ID;
//     $specialist = get_user_meta($user_id , 'specialist' , true);
//     echo  $specialist;
//     if($specialist  == 596) {
//         $s1 .= $user_id . '<br>';
//         // update_user_meta($user_id , 'specialist' , 596);
//     }
// }
// echo $s1;
// echo 'ddaa2';
// $order_id = 27032;
// $order = wc_get_order($order_id);
// $order_items = $order->get_items('line_item');
// foreach ( $order_items as $key => $value ) {
//     var_dump($value->get_product_id());
//     $product = wc_get_product($value->get_product_id());
//     if( wc_get_product($value->get_product_id()) && !$product->is_type( 'service' ) ){
//         $count_item_service++;
//     }
// }

// clone File s3
// global $wpdb;
// $list_items = '[{"folder":"28379561579742929","y":"2020","m":"01"},{"folder":"6ea95691579754088","y":"2020","m":"01"},{"folder":"c025881580349019","y":"2020","m":"01"},{"folder":"34c9f961593396794","y":"2020","m":"06"},{"folder":"a12e741602062267","y":"2020","m":"10"},{"folder":"f95a5861602062578","y":"2020","m":"10"},{"folder":"23aa2931602121600","y":"2020","m":"10"},{"folder":"c6767231602121708","y":"2020","m":"10"},{"folder":"cd2fa841602122406","y":"2020","m":"10"},{"folder":"de7a9451602122453","y":"2020","m":"10"},{"folder":"9fc83821602126337","y":"2020","m":"10"},{"folder":"b3640891602133077","y":"2020","m":"10"},{"folder":"d289a801602133173","y":"2020","m":"10"},{"folder":"383d4421602133210","y":"2020","m":"10"},{"folder":"a0546351602133271","y":"2020","m":"10"},{"folder":"60e6b131602133343","y":"2020","m":"10"},{"folder":"ffea2661602133378","y":"2020","m":"10"},{"folder":"16364961602133403","y":"2020","m":"10"},{"folder":"d9ea1611602133449","y":"2020","m":"10"},{"folder":"75652241602137622","y":"2020","m":"10"},{"folder":"08458221602137656","y":"2020","m":"10"},{"folder":"fb842631602137688","y":"2020","m":"10"},{"folder":"d09d7441602137709","y":"2020","m":"10"},{"folder":"a1caa651602137736","y":"2020","m":"10"},{"folder":"0d4b6981602137767","y":"2020","m":"10"},{"folder":"cb5b2271602137809","y":"2020","m":"10"},{"folder":"c3a27571602137845","y":"2020","m":"10"},{"folder":"89c6071602137872","y":"2020","m":"10"},{"folder":"0691b391602137903","y":"2020","m":"10"},{"folder":"7311f451602137960","y":"2020","m":"10"},{"folder":"60e41771602137981","y":"2020","m":"10"},{"folder":"7f324601602138014","y":"2020","m":"10"},{"folder":"26a90801602138071","y":"2020","m":"10"},{"folder":"3fc21111602138102","y":"2020","m":"10"},{"folder":"7f097501602138152","y":"2020","m":"10"},{"folder":"8269191602141073","y":"2020","m":"10"},{"folder":"192b9341602232352","y":"2020","m":"10"},{"folder":"ea65b441602236216","y":"2020","m":"10"},{"folder":"13017751602301786","y":"2020","m":"10"},{"folder":"4a095721602473233","y":"2020","m":"10"},{"folder":"88f7e381602302623","y":"2020","m":"10"},{"folder":"7bd35501602474014","y":"2020","m":"10"},{"folder":"fe4be351602474270","y":"2020","m":"10"},{"folder":"122b661602485789","y":"2020","m":"10"},{"folder":"72e07291602491150","y":"2020","m":"10"},{"folder":"c4f86351602491642","y":"2020","m":"10"},{"folder":"6cf81921602736376","y":"2020","m":"10"},{"folder":"417ea831604369418","y":"2020","m":"11"},{"folder":"39cca341604369281","y":"2020","m":"11"},{"folder":"50c88861604369511","y":"2020","m":"11"},{"folder":"8207c291604369608","y":"2020","m":"11"},{"folder":"8e552421604370211","y":"2020","m":"11"},{"folder":"c76c4201604370557","y":"2020","m":"11"},{"folder":"c3681391610071969","y":"2021","m":"01"},{"folder":"8139711610088764","y":"2021","m":"01"},{"folder":"5e2a8c56f2","y":"2021","m":"08"},{"folder":"12d10321627898293","y":"2021","m":"08"},{"folder":"3a693560f2","y":"2021","m":"08"},{"folder":"76c2699c51","y":"2021","m":"08"},{"folder":"506b4a4925","y":"2021","m":"08"},{"folder":"d6c30cc9ac","y":"2021","m":"08"},{"folder":"8fdb5dba84","y":"2021","m":"08"},{"folder":"8f237341611808858","y":"2021","m":"01"},{"folder":"959b2ebf61","y":"2021","m":"01"},{"folder":"b4d7260c4f","y":"2021","m":"01"},{"folder":"d950cb8f59","y":"2021","m":"08"},{"folder":"1e816861620297818","y":"2021","m":"05"},{"folder":"32523221611908131","y":"2021","m":"01"},{"folder":"5691d291620297955","y":"2021","m":"05"},{"folder":"6256091620297998","y":"2021","m":"05"},{"folder":"b13d1141620298051","y":"2021","m":"05"},{"folder":"e32e2271620298103","y":"2021","m":"05"},{"folder":"b24fc941620298146","y":"2021","m":"05"},{"folder":"31bfa391620298289","y":"2021","m":"05"},{"folder":"24d42781620298409","y":"2021","m":"05"},{"folder":"9c299441620298492","y":"2021","m":"05"},{"folder":"31da3351620298631","y":"2021","m":"05"},{"folder":"66b0e141620298719","y":"2021","m":"05"},{"folder":"cf4f3211620298786","y":"2021","m":"05"},{"folder":"67322231620298876","y":"2021","m":"05"},{"folder":"70ed1201620298944","y":"2021","m":"05"},{"folder":"f132e971620298997","y":"2021","m":"05"},{"folder":"c5d84241620299038","y":"2021","m":"05"},{"folder":"d3d32861620362836","y":"2021","m":"05"},{"folder":"1483b561620362727","y":"2021","m":"05"},{"folder":"f70a141620363553","y":"2021","m":"05"},{"folder":"0c6db791620363785","y":"2021","m":"05"},{"folder":"026d8671620372039","y":"2021","m":"05"},{"folder":"b40a9981620371194","y":"2021","m":"05"},{"folder":"efcc8831620370870","y":"2021","m":"05"},{"folder":"75dab361620372104","y":"2021","m":"05"},{"folder":"46f99381621577391","y":"2021","m":"05"},{"folder":"bc5709db4d","y":"2021","m":"05"},{"folder":"fee4d914c5","y":"2021","m":"05"},{"folder":"dd47b58000","y":"2021","m":"05"},{"folder":"eb4c27b593","y":"2021","m":"05"},{"folder":"5fa50c480e","y":"2021","m":"05"},{"folder":"e58962082b","y":"2021","m":"06"},{"folder":"21e3f591623321674","y":"2021","m":"06"},{"folder":"a667261bd8","y":"2021","m":"06"},{"folder":"ba30808284","y":"2021","m":"06"},{"folder":"37cdf841622778815","y":"2021","m":"06"},{"folder":"bfc2c401622787885","y":"2021","m":"06"},{"folder":"81d8218b86","y":"2021","m":"06"},{"folder":"6c3dcc71c2","y":"2021","m":"06"},{"folder":"f2dba3400f","y":"2021","m":"06"},{"folder":"a6c4a14d0a","y":"2021","m":"06"},{"folder":"bf0941a767","y":"2021","m":"06"},{"folder":"16e54344b4","y":"2021","m":"06"},{"folder":"cdc0b301623307960","y":"2021","m":"06"},{"folder":"c971978af6","y":"2021","m":"07"},{"folder":"2021f411623381397","y":"2021","m":"06"},{"folder":"ba3005cb0d","y":"2021","m":"06"},{"folder":"994d53bcbc","y":"2021","m":"06"},{"folder":"69001268f5","y":"2021","m":"06"},{"folder":"411ead0af3","y":"2021","m":"06"},{"folder":"1ab0aa1030","y":"2021","m":"06"},{"folder":"ed231d679d","y":"2021","m":"06"},{"folder":"67cd62d876","y":"2021","m":"06"},{"folder":"b69e9cd538","y":"2021","m":"06"},{"folder":"6138815759","y":"2021","m":"06"},{"folder":"32114dc690","y":"2021","m":"06"},{"folder":"4fd6aa6f6a","y":"2021","m":"06"},{"folder":"485b3901623395025","y":"2021","m":"06"},{"folder":"41ebe18f5b","y":"2021","m":"06"},{"folder":"0987625d3c","y":"2021","m":"06"},{"folder":"9ec913672d","y":"2021","m":"06"},{"folder":"0216853ee4","y":"2021","m":"06"},{"folder":"d7c89afbee","y":"2021","m":"06"},{"folder":"084d159135","y":"2021","m":"06"},{"folder":"30a705f8fc","y":"2021","m":"06"},{"folder":"343df11623397849","y":"2021","m":"06"},{"folder":"0fa48733cb","y":"2021","m":"06"},{"folder":"5d14b1cab1","y":"2021","m":"06"},{"folder":"db56bb27aa","y":"2021","m":"06"},{"folder":"1e624f6529","y":"2021","m":"06"},{"folder":"640dddf1f8","y":"2021","m":"06"},{"folder":"c75965ec88","y":"2021","m":"06"},{"folder":"3afc6b693c","y":"2021","m":"06"},{"folder":"52b88491623467119","y":"2021","m":"06"},{"folder":"2b46d8f546","y":"2021","m":"06"},{"folder":"a343dd7048","y":"2021","m":"06"},{"folder":"9027821c45","y":"2021","m":"06"},{"folder":"c4401a92b7","y":"2021","m":"06"},{"folder":"8f900a54af","y":"2021","m":"06"},{"folder":"bf7a021623478691","y":"2021","m":"06"},{"folder":"77ca42acfa","y":"2021","m":"06"},{"folder":"16c78a0c88","y":"2021","m":"06"},{"folder":"9aac3825a4","y":"2021","m":"06"},{"folder":"25622e6b62","y":"2021","m":"06"},{"folder":"c3820bfbb7","y":"2021","m":"06"},{"folder":"bcdacb3ecf","y":"2021","m":"06"},{"folder":"814d4901623824404","y":"2021","m":"06"},{"folder":"90d50941623986872","y":"2021","m":"06"},{"folder":"47fce361623935005","y":"2021","m":"06"},{"folder":"2acba8473f","y":"2021","m":"06"},{"folder":"53b8f581624070248","y":"2021","m":"06"},{"folder":"b5e5e431623986491","y":"2021","m":"06"},{"folder":"19f99331623980608","y":"2021","m":"06"},{"folder":"13ee698e02","y":"2021","m":"06"},{"folder":"52775261624075491","y":"2021","m":"06"},{"folder":"dfb9f7e7ed","y":"2021","m":"06"},{"folder":"3169c681624352685","y":"2021","m":"06"},{"folder":"933bb3a286","y":"2021","m":"06"},{"folder":"ac2fa9f780","y":"2021","m":"06"},{"folder":"d7fe54023c","y":"2021","m":"06"},{"folder":"5d71b07ddc","y":"2021","m":"06"},{"folder":"6ba5b430b9","y":"2021","m":"06"},{"folder":"2e7e5d0f39","y":"2021","m":"06"},{"folder":"9e8095dc36","y":"2021","m":"06"},{"folder":"18b45d8b51","y":"2021","m":"06"},{"folder":"b14cbb76a3","y":"2021","m":"06"},{"folder":"46552ddebd","y":"2021","m":"06"},{"folder":"5420d1e678","y":"2021","m":"06"},{"folder":"48157391624356933","y":"2021","m":"06"},{"folder":"03d16eff52","y":"2021","m":"06"},{"folder":"67cb7c17f3","y":"2021","m":"06"},{"folder":"5c4ee0223a","y":"2021","m":"06"},{"folder":"06f9e33bd2","y":"2021","m":"06"},{"folder":"5eeda28fed","y":"2021","m":"06"},{"folder":"b12af1b20a","y":"2021","m":"06"},{"folder":"3227722a1e","y":"2021","m":"06"},{"folder":"96d91371624427726","y":"2021","m":"06"},{"folder":"a73373fe02","y":"2021","m":"06"},{"folder":"61636661624609932","y":"2021","m":"06"},{"folder":"417c0ade62","y":"2021","m":"06"},{"folder":"fea2789fca","y":"2021","m":"06"},{"folder":"1b9cdf93f3","y":"2021","m":"06"},{"folder":"8ec6424f2f","y":"2021","m":"06"},{"folder":"fee995806d","y":"2021","m":"06"},{"folder":"c0589911624680037","y":"2021","m":"06"},{"folder":"56a6a961625026156","y":"2021","m":"06"},{"folder":"8975d981625211937","y":"2021","m":"07"},{"folder":"41137d8392","y":"2021","m":"07"},{"folder":"cf03ba7e03","y":"2021","m":"07"},{"folder":"89b01e0a08","y":"2021","m":"07"},{"folder":"665496b75f","y":"2021","m":"07"},{"folder":"97fdfd75f6","y":"2021","m":"07"},{"folder":"ea38e751625215250","y":"2021","m":"07"},{"folder":"fb65c6957a","y":"2021","m":"07"},{"folder":"2a23f6319e","y":"2021","m":"07"},{"folder":"4604172316","y":"2021","m":"07"},{"folder":"a1349a1054","y":"2021","m":"07"},{"folder":"cdc23f9ebb","y":"2021","m":"07"},{"folder":"daf8c650e6","y":"2021","m":"07"},{"folder":"1402fb154e","y":"2021","m":"07"},{"folder":"c7d14321626860040","y":"2021","m":"07"},{"folder":"27c6e791626860107","y":"2021","m":"07"},{"folder":"c48d6771626860158","y":"2021","m":"07"},{"folder":"da817231626860204","y":"2021","m":"07"},{"folder":"f4ef4431626861406","y":"2021","m":"07"},{"folder":"05e7ff64d6","y":"2021","m":"07"},{"folder":"936758f0f0","y":"2021","m":"07"},{"folder":"3db60541626864022","y":"2021","m":"07"},{"folder":"101a9d05e2","y":"2021","m":"07"},{"folder":"a795c0da21","y":"2021","m":"07"},{"folder":"6af692e847","y":"2021","m":"07"},{"folder":"e64c299597","y":"2021","m":"07"},{"folder":"fec26ff08b","y":"2021","m":"07"},{"folder":"4ec1b16842","y":"2021","m":"07"},{"folder":"491bd45445","y":"2021","m":"07"},{"folder":"6694f801627293894","y":"2021","m":"07"},{"folder":"9983a971627308100","y":"2021","m":"07"},{"folder":"e1bda21627308141","y":"2021","m":"07"},{"folder":"a298ffff79","y":"2021","m":"07"},{"folder":"dea465b029","y":"2021","m":"07"},{"folder":"b2d0ccbaa1","y":"2021","m":"07"},{"folder":"ef6df50855","y":"2021","m":"07"},{"folder":"9b769843b1","y":"2021","m":"07"},{"folder":"e771d421627529711","y":"2021","m":"07"},{"folder":"0efac71627639189","y":"2021","m":"07"},{"folder":"7502cdf71f","y":"2021","m":"07"},{"folder":"95c32ad601","y":"2021","m":"07"},{"folder":"4eecb961628759063","y":"2021","m":"08"},{"folder":"8e19bc9a0e","y":"2021","m":"08"},{"folder":"2f27b0c1b6","y":"2021","m":"08"},{"folder":"d27e09ad45","y":"2021","m":"08"},{"folder":"79e29601628763291","y":"2021","m":"08"},{"folder":"08ca969fd8","y":"2021","m":"08"},{"folder":"a83b281214","y":"2021","m":"08"},{"folder":"4529eef074","y":"2021","m":"08"},{"folder":"342066a296","y":"2021","m":"08"},{"folder":"3b303471f3","y":"2021","m":"08"},{"folder":"f08edfc831","y":"2021","m":"08"},{"folder":"d58d9d7a94","y":"2021","m":"08"},{"folder":"138a0811629703514","y":"2021","m":"08"},{"folder":"9e6b950e4d","y":"2021","m":"08"},{"folder":"60f5e5a254","y":"2021","m":"08"},{"folder":"354147e195","y":"2021","m":"08"},{"folder":"8286dc6792","y":"2021","m":"08"},{"folder":"2af90031c2","y":"2021","m":"08"},{"folder":"2968261629802962","y":"2021","m":"08"},{"folder":"9bebc3ace7","y":"2021","m":"08"},{"folder":"b9b22bdbf5","y":"2021","m":"08"},{"folder":"fee2fc2363","y":"2021","m":"08"},{"folder":"43eb3111630493362","y":"2021","m":"09"},{"folder":"92ee4dfae7","y":"2021","m":"09"},{"folder":"531a369f3d","y":"2021","m":"09"},{"folder":"46240891631331607","y":"2021","m":"09"},{"folder":"028ee8833c","y":"2021","m":"09"},{"folder":"a8e415a635","y":"2021","m":"09"},{"folder":"5115784490","y":"2021","m":"09"},{"folder":"f2ff5992d1","y":"2021","m":"09"},{"folder":"8381ad4abd","y":"2021","m":"09"},{"folder":"2a14d36f80","y":"2021","m":"09"},{"folder":"bc44c7e2b2","y":"2021","m":"09"},{"folder":"8ba6432ece","y":"2021","m":"09"},{"folder":"ac6fa53aae","y":"2021","m":"09"},{"folder":"c4253161631845806","y":"2021","m":"09"},{"folder":"2cce95b996","y":"2021","m":"09"},{"folder":"dad3afce97","y":"2021","m":"09"},{"folder":"7e2bd00781","y":"2021","m":"09"},{"folder":"9501e761631862162","y":"2021","m":"09"},{"folder":"0d2930a477","y":"2021","m":"09"},{"folder":"509a4fec14","y":"2021","m":"09"},{"folder":"d40e987fa8","y":"2021","m":"09"},{"folder":"8393b5f623","y":"2021","m":"09"},{"folder":"7a4d09537d","y":"2021","m":"09"},{"folder":"1933051314","y":"2021","m":"09"},{"folder":"66d35d08f9","y":"2021","m":"09"},{"folder":"15ed368467","y":"2021","m":"09"},{"folder":"a1ff5211632717595","y":"2021","m":"09"},{"folder":"e7d12e9b9c","y":"2021","m":"09"},{"folder":"d522747f54","y":"2021","m":"09"},{"folder":"219b257202","y":"2021","m":"09"},{"folder":"c95b0a2989","y":"2021","m":"09"},{"folder":"17c00f1490","y":"2021","m":"09"}]';

// // $folder_download = 'C:\Users\NetBase\Desktop\design_template';
// $folder_download = NBDESIGNER_CUSTOMER_DIR;
// $folder = '00805341621494253';
// $awsAccessKey = 'AKIAX4QORCYMGSQXVT55';
// $awsSecretKey = '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';
// $amazonRegion = 'ap-southeast-1';
// use Aws\S3\S3Client;
// use Aws\Credentials\Credentials;
// $bucket = 'bts-design-template';
// $credentials = new Credentials("$awsAccessKey", "$awsSecretKey");

// //Instantiate the S3 client with your AWS credentials
// $s3Client = S3Client::factory(array(
//     'credentials' => $credentials,
//     'region' => "$amazonRegion",
//     'version' => 'latest'));
// $not_download = array();
// $download = array();
// $not_download_1 = array();
// $download_1 = array();
// $folder_S3 = 'Design 2021/';
// $folder_S3_1 = 'design_2020/';
// $isset_folder = 0;
// foreach(json_decode($list_items) as $items) {
//     if($items->y == '2021') {
//         if( !is_dir($folder_download.'/'.$items->folder) ) {
//             $s3Client->downloadBucket($folder_download.'/'.$items->folder , $bucket, $folder_S3.$items->m.'/'.$items->folder);
//             if( is_dir($folder_download.'/'.$items->folder) ) {
//                 $download[] = $items->folder;
//             } else {
//                 $not_download[] = $items->folder;
//             }
//         } else {
//             $not_download[] = $items->folder;
//         }
//     } else {
//        if( !is_dir($folder_download.'/'.$items->folder) ) {
//             $s3Client->downloadBucket($folder_download.'/'.$items->folder , $bucket, $folder_S3_1.$items->folder);
//             if( is_dir($folder_download.'/'.$items->folder) ) {
//                 $download_1[] = $items->folder;
//             } else {
//                 $not_download_1[] = $items->folder;
//             }
//         } 
//         else {
//             $not_download_1[] = $items->folder;
//         }
//     }
//     if( is_dir($folder_download.'/'.$items->folder) ) {
//         $isset_folder++;
//     }
// }
// echo '<b>Thư mục đã copy: '.$isset_folder.'</b> <br>';
// echo '<b>Thư mục download là:</b> <br>';
// echo '<pre>';
// var_dump($download);
// echo '</pre>';

// echo '<b>Thư mục chưa download là:</b> <br>';
// echo '<pre>';
// var_dump($not_download);
// echo '</pre>';

// echo '<b>Thư mục download 2020 là:</b> <br>';
// echo '<pre>';
// var_dump($download_1);
// echo '</pre>';

// echo '<b>Thư mục chưa download 2020 là:</b> <br>';
// echo '<pre>';
// var_dump($not_download_1);
// echo '</pre>';

// $list_items = '[{"folder":"28379561579742929","y":"2020","m":"01"},{"folder":"6ea95691579754088","y":"2020","m":"01"},{"folder":"c025881580349019","y":"2020","m":"01"},{"folder":"34c9f961593396794","y":"2020","m":"06"},{"folder":"a12e741602062267","y":"2020","m":"10"},{"folder":"f95a5861602062578","y":"2020","m":"10"},{"folder":"23aa2931602121600","y":"2020","m":"10"},{"folder":"c6767231602121708","y":"2020","m":"10"},{"folder":"cd2fa841602122406","y":"2020","m":"10"},{"folder":"de7a9451602122453","y":"2020","m":"10"},{"folder":"9fc83821602126337","y":"2020","m":"10"},{"folder":"b3640891602133077","y":"2020","m":"10"},{"folder":"d289a801602133173","y":"2020","m":"10"},{"folder":"383d4421602133210","y":"2020","m":"10"},{"folder":"a0546351602133271","y":"2020","m":"10"},{"folder":"60e6b131602133343","y":"2020","m":"10"},{"folder":"ffea2661602133378","y":"2020","m":"10"},{"folder":"16364961602133403","y":"2020","m":"10"},{"folder":"d9ea1611602133449","y":"2020","m":"10"},{"folder":"75652241602137622","y":"2020","m":"10"},{"folder":"08458221602137656","y":"2020","m":"10"},{"folder":"fb842631602137688","y":"2020","m":"10"},{"folder":"d09d7441602137709","y":"2020","m":"10"},{"folder":"a1caa651602137736","y":"2020","m":"10"},{"folder":"0d4b6981602137767","y":"2020","m":"10"},{"folder":"cb5b2271602137809","y":"2020","m":"10"},{"folder":"c3a27571602137845","y":"2020","m":"10"},{"folder":"89c6071602137872","y":"2020","m":"10"},{"folder":"0691b391602137903","y":"2020","m":"10"},{"folder":"7311f451602137960","y":"2020","m":"10"},{"folder":"60e41771602137981","y":"2020","m":"10"},{"folder":"7f324601602138014","y":"2020","m":"10"},{"folder":"26a90801602138071","y":"2020","m":"10"},{"folder":"3fc21111602138102","y":"2020","m":"10"},{"folder":"7f097501602138152","y":"2020","m":"10"},{"folder":"8269191602141073","y":"2020","m":"10"},{"folder":"192b9341602232352","y":"2020","m":"10"},{"folder":"ea65b441602236216","y":"2020","m":"10"},{"folder":"13017751602301786","y":"2020","m":"10"},{"folder":"4a095721602473233","y":"2020","m":"10"},{"folder":"88f7e381602302623","y":"2020","m":"10"},{"folder":"7bd35501602474014","y":"2020","m":"10"},{"folder":"fe4be351602474270","y":"2020","m":"10"},{"folder":"122b661602485789","y":"2020","m":"10"},{"folder":"72e07291602491150","y":"2020","m":"10"},{"folder":"c4f86351602491642","y":"2020","m":"10"},{"folder":"6cf81921602736376","y":"2020","m":"10"},{"folder":"417ea831604369418","y":"2020","m":"11"},{"folder":"39cca341604369281","y":"2020","m":"11"},{"folder":"50c88861604369511","y":"2020","m":"11"},{"folder":"8207c291604369608","y":"2020","m":"11"},{"folder":"8e552421604370211","y":"2020","m":"11"},{"folder":"c76c4201604370557","y":"2020","m":"11"},{"folder":"c3681391610071969","y":"2021","m":"01"},{"folder":"8139711610088764","y":"2021","m":"01"},{"folder":"5e2a8c56f2","y":"2021","m":"08"},{"folder":"12d10321627898293","y":"2021","m":"08"},{"folder":"3a693560f2","y":"2021","m":"08"},{"folder":"76c2699c51","y":"2021","m":"08"},{"folder":"506b4a4925","y":"2021","m":"08"},{"folder":"d6c30cc9ac","y":"2021","m":"08"},{"folder":"8fdb5dba84","y":"2021","m":"08"},{"folder":"8f237341611808858","y":"2021","m":"01"},{"folder":"959b2ebf61","y":"2021","m":"01"},{"folder":"b4d7260c4f","y":"2021","m":"01"},{"folder":"d950cb8f59","y":"2021","m":"08"},{"folder":"1e816861620297818","y":"2021","m":"05"},{"folder":"32523221611908131","y":"2021","m":"01"},{"folder":"5691d291620297955","y":"2021","m":"05"},{"folder":"6256091620297998","y":"2021","m":"05"},{"folder":"b13d1141620298051","y":"2021","m":"05"},{"folder":"e32e2271620298103","y":"2021","m":"05"},{"folder":"b24fc941620298146","y":"2021","m":"05"},{"folder":"31bfa391620298289","y":"2021","m":"05"},{"folder":"24d42781620298409","y":"2021","m":"05"},{"folder":"9c299441620298492","y":"2021","m":"05"},{"folder":"31da3351620298631","y":"2021","m":"05"},{"folder":"66b0e141620298719","y":"2021","m":"05"},{"folder":"cf4f3211620298786","y":"2021","m":"05"},{"folder":"67322231620298876","y":"2021","m":"05"},{"folder":"70ed1201620298944","y":"2021","m":"05"},{"folder":"f132e971620298997","y":"2021","m":"05"},{"folder":"c5d84241620299038","y":"2021","m":"05"},{"folder":"d3d32861620362836","y":"2021","m":"05"},{"folder":"1483b561620362727","y":"2021","m":"05"},{"folder":"f70a141620363553","y":"2021","m":"05"},{"folder":"0c6db791620363785","y":"2021","m":"05"},{"folder":"026d8671620372039","y":"2021","m":"05"},{"folder":"b40a9981620371194","y":"2021","m":"05"},{"folder":"efcc8831620370870","y":"2021","m":"05"},{"folder":"75dab361620372104","y":"2021","m":"05"},{"folder":"46f99381621577391","y":"2021","m":"05"},{"folder":"bc5709db4d","y":"2021","m":"05"},{"folder":"fee4d914c5","y":"2021","m":"05"},{"folder":"dd47b58000","y":"2021","m":"05"},{"folder":"eb4c27b593","y":"2021","m":"05"},{"folder":"5fa50c480e","y":"2021","m":"05"},{"folder":"e58962082b","y":"2021","m":"06"},{"folder":"21e3f591623321674","y":"2021","m":"06"},{"folder":"a667261bd8","y":"2021","m":"06"},{"folder":"ba30808284","y":"2021","m":"06"},{"folder":"37cdf841622778815","y":"2021","m":"06"},{"folder":"bfc2c401622787885","y":"2021","m":"06"},{"folder":"81d8218b86","y":"2021","m":"06"},{"folder":"6c3dcc71c2","y":"2021","m":"06"},{"folder":"f2dba3400f","y":"2021","m":"06"},{"folder":"a6c4a14d0a","y":"2021","m":"06"},{"folder":"bf0941a767","y":"2021","m":"06"},{"folder":"16e54344b4","y":"2021","m":"06"},{"folder":"cdc0b301623307960","y":"2021","m":"06"},{"folder":"c971978af6","y":"2021","m":"07"},{"folder":"2021f411623381397","y":"2021","m":"06"},{"folder":"ba3005cb0d","y":"2021","m":"06"},{"folder":"994d53bcbc","y":"2021","m":"06"},{"folder":"69001268f5","y":"2021","m":"06"},{"folder":"411ead0af3","y":"2021","m":"06"},{"folder":"1ab0aa1030","y":"2021","m":"06"},{"folder":"ed231d679d","y":"2021","m":"06"},{"folder":"67cd62d876","y":"2021","m":"06"},{"folder":"b69e9cd538","y":"2021","m":"06"},{"folder":"6138815759","y":"2021","m":"06"},{"folder":"32114dc690","y":"2021","m":"06"},{"folder":"4fd6aa6f6a","y":"2021","m":"06"},{"folder":"485b3901623395025","y":"2021","m":"06"},{"folder":"41ebe18f5b","y":"2021","m":"06"},{"folder":"0987625d3c","y":"2021","m":"06"},{"folder":"9ec913672d","y":"2021","m":"06"},{"folder":"0216853ee4","y":"2021","m":"06"},{"folder":"d7c89afbee","y":"2021","m":"06"},{"folder":"084d159135","y":"2021","m":"06"},{"folder":"30a705f8fc","y":"2021","m":"06"},{"folder":"343df11623397849","y":"2021","m":"06"},{"folder":"0fa48733cb","y":"2021","m":"06"},{"folder":"5d14b1cab1","y":"2021","m":"06"},{"folder":"db56bb27aa","y":"2021","m":"06"},{"folder":"1e624f6529","y":"2021","m":"06"},{"folder":"640dddf1f8","y":"2021","m":"06"},{"folder":"c75965ec88","y":"2021","m":"06"},{"folder":"3afc6b693c","y":"2021","m":"06"},{"folder":"52b88491623467119","y":"2021","m":"06"},{"folder":"2b46d8f546","y":"2021","m":"06"},{"folder":"a343dd7048","y":"2021","m":"06"},{"folder":"9027821c45","y":"2021","m":"06"},{"folder":"c4401a92b7","y":"2021","m":"06"},{"folder":"8f900a54af","y":"2021","m":"06"},{"folder":"bf7a021623478691","y":"2021","m":"06"},{"folder":"77ca42acfa","y":"2021","m":"06"},{"folder":"16c78a0c88","y":"2021","m":"06"},{"folder":"9aac3825a4","y":"2021","m":"06"},{"folder":"25622e6b62","y":"2021","m":"06"},{"folder":"c3820bfbb7","y":"2021","m":"06"},{"folder":"bcdacb3ecf","y":"2021","m":"06"},{"folder":"814d4901623824404","y":"2021","m":"06"},{"folder":"90d50941623986872","y":"2021","m":"06"},{"folder":"47fce361623935005","y":"2021","m":"06"},{"folder":"2acba8473f","y":"2021","m":"06"},{"folder":"53b8f581624070248","y":"2021","m":"06"},{"folder":"b5e5e431623986491","y":"2021","m":"06"},{"folder":"19f99331623980608","y":"2021","m":"06"},{"folder":"13ee698e02","y":"2021","m":"06"},{"folder":"52775261624075491","y":"2021","m":"06"},{"folder":"dfb9f7e7ed","y":"2021","m":"06"},{"folder":"3169c681624352685","y":"2021","m":"06"},{"folder":"933bb3a286","y":"2021","m":"06"},{"folder":"ac2fa9f780","y":"2021","m":"06"},{"folder":"d7fe54023c","y":"2021","m":"06"},{"folder":"5d71b07ddc","y":"2021","m":"06"},{"folder":"6ba5b430b9","y":"2021","m":"06"},{"folder":"2e7e5d0f39","y":"2021","m":"06"},{"folder":"9e8095dc36","y":"2021","m":"06"},{"folder":"18b45d8b51","y":"2021","m":"06"},{"folder":"b14cbb76a3","y":"2021","m":"06"},{"folder":"46552ddebd","y":"2021","m":"06"},{"folder":"5420d1e678","y":"2021","m":"06"},{"folder":"48157391624356933","y":"2021","m":"06"},{"folder":"03d16eff52","y":"2021","m":"06"},{"folder":"67cb7c17f3","y":"2021","m":"06"},{"folder":"5c4ee0223a","y":"2021","m":"06"},{"folder":"06f9e33bd2","y":"2021","m":"06"},{"folder":"5eeda28fed","y":"2021","m":"06"},{"folder":"b12af1b20a","y":"2021","m":"06"},{"folder":"3227722a1e","y":"2021","m":"06"},{"folder":"96d91371624427726","y":"2021","m":"06"},{"folder":"a73373fe02","y":"2021","m":"06"},{"folder":"61636661624609932","y":"2021","m":"06"},{"folder":"417c0ade62","y":"2021","m":"06"},{"folder":"fea2789fca","y":"2021","m":"06"},{"folder":"1b9cdf93f3","y":"2021","m":"06"},{"folder":"8ec6424f2f","y":"2021","m":"06"},{"folder":"fee995806d","y":"2021","m":"06"},{"folder":"c0589911624680037","y":"2021","m":"06"},{"folder":"56a6a961625026156","y":"2021","m":"06"},{"folder":"8975d981625211937","y":"2021","m":"07"},{"folder":"41137d8392","y":"2021","m":"07"},{"folder":"cf03ba7e03","y":"2021","m":"07"},{"folder":"89b01e0a08","y":"2021","m":"07"},{"folder":"665496b75f","y":"2021","m":"07"},{"folder":"97fdfd75f6","y":"2021","m":"07"},{"folder":"ea38e751625215250","y":"2021","m":"07"},{"folder":"fb65c6957a","y":"2021","m":"07"},{"folder":"2a23f6319e","y":"2021","m":"07"},{"folder":"4604172316","y":"2021","m":"07"},{"folder":"a1349a1054","y":"2021","m":"07"},{"folder":"cdc23f9ebb","y":"2021","m":"07"},{"folder":"daf8c650e6","y":"2021","m":"07"},{"folder":"1402fb154e","y":"2021","m":"07"},{"folder":"c7d14321626860040","y":"2021","m":"07"},{"folder":"27c6e791626860107","y":"2021","m":"07"},{"folder":"c48d6771626860158","y":"2021","m":"07"},{"folder":"da817231626860204","y":"2021","m":"07"},{"folder":"f4ef4431626861406","y":"2021","m":"07"},{"folder":"05e7ff64d6","y":"2021","m":"07"},{"folder":"936758f0f0","y":"2021","m":"07"},{"folder":"3db60541626864022","y":"2021","m":"07"},{"folder":"101a9d05e2","y":"2021","m":"07"},{"folder":"a795c0da21","y":"2021","m":"07"},{"folder":"6af692e847","y":"2021","m":"07"},{"folder":"e64c299597","y":"2021","m":"07"},{"folder":"fec26ff08b","y":"2021","m":"07"},{"folder":"4ec1b16842","y":"2021","m":"07"},{"folder":"491bd45445","y":"2021","m":"07"},{"folder":"6694f801627293894","y":"2021","m":"07"},{"folder":"9983a971627308100","y":"2021","m":"07"},{"folder":"e1bda21627308141","y":"2021","m":"07"},{"folder":"a298ffff79","y":"2021","m":"07"},{"folder":"dea465b029","y":"2021","m":"07"},{"folder":"b2d0ccbaa1","y":"2021","m":"07"},{"folder":"ef6df50855","y":"2021","m":"07"},{"folder":"9b769843b1","y":"2021","m":"07"},{"folder":"e771d421627529711","y":"2021","m":"07"},{"folder":"0efac71627639189","y":"2021","m":"07"},{"folder":"7502cdf71f","y":"2021","m":"07"},{"folder":"95c32ad601","y":"2021","m":"07"},{"folder":"4eecb961628759063","y":"2021","m":"08"},{"folder":"8e19bc9a0e","y":"2021","m":"08"},{"folder":"2f27b0c1b6","y":"2021","m":"08"},{"folder":"d27e09ad45","y":"2021","m":"08"},{"folder":"79e29601628763291","y":"2021","m":"08"},{"folder":"08ca969fd8","y":"2021","m":"08"},{"folder":"a83b281214","y":"2021","m":"08"},{"folder":"4529eef074","y":"2021","m":"08"},{"folder":"342066a296","y":"2021","m":"08"},{"folder":"3b303471f3","y":"2021","m":"08"},{"folder":"f08edfc831","y":"2021","m":"08"},{"folder":"d58d9d7a94","y":"2021","m":"08"},{"folder":"138a0811629703514","y":"2021","m":"08"},{"folder":"9e6b950e4d","y":"2021","m":"08"},{"folder":"60f5e5a254","y":"2021","m":"08"},{"folder":"354147e195","y":"2021","m":"08"},{"folder":"8286dc6792","y":"2021","m":"08"},{"folder":"2af90031c2","y":"2021","m":"08"},{"folder":"2968261629802962","y":"2021","m":"08"},{"folder":"9bebc3ace7","y":"2021","m":"08"},{"folder":"b9b22bdbf5","y":"2021","m":"08"},{"folder":"fee2fc2363","y":"2021","m":"08"},{"folder":"43eb3111630493362","y":"2021","m":"09"},{"folder":"92ee4dfae7","y":"2021","m":"09"},{"folder":"531a369f3d","y":"2021","m":"09"},{"folder":"46240891631331607","y":"2021","m":"09"},{"folder":"028ee8833c","y":"2021","m":"09"},{"folder":"a8e415a635","y":"2021","m":"09"},{"folder":"5115784490","y":"2021","m":"09"},{"folder":"f2ff5992d1","y":"2021","m":"09"},{"folder":"8381ad4abd","y":"2021","m":"09"},{"folder":"2a14d36f80","y":"2021","m":"09"},{"folder":"bc44c7e2b2","y":"2021","m":"09"},{"folder":"8ba6432ece","y":"2021","m":"09"},{"folder":"ac6fa53aae","y":"2021","m":"09"},{"folder":"c4253161631845806","y":"2021","m":"09"},{"folder":"2cce95b996","y":"2021","m":"09"},{"folder":"dad3afce97","y":"2021","m":"09"},{"folder":"7e2bd00781","y":"2021","m":"09"},{"folder":"9501e761631862162","y":"2021","m":"09"},{"folder":"0d2930a477","y":"2021","m":"09"},{"folder":"509a4fec14","y":"2021","m":"09"},{"folder":"d40e987fa8","y":"2021","m":"09"},{"folder":"8393b5f623","y":"2021","m":"09"},{"folder":"7a4d09537d","y":"2021","m":"09"},{"folder":"1933051314","y":"2021","m":"09"},{"folder":"66d35d08f9","y":"2021","m":"09"},{"folder":"15ed368467","y":"2021","m":"09"},{"folder":"a1ff5211632717595","y":"2021","m":"09"},{"folder":"e7d12e9b9c","y":"2021","m":"09"},{"folder":"d522747f54","y":"2021","m":"09"},{"folder":"219b257202","y":"2021","m":"09"},{"folder":"c95b0a2989","y":"2021","m":"09"},{"folder":"17c00f1490","y":"2021","m":"09"}]';

// $folder_download = 'C:\Users\NetBase\Desktop\design_template';
// $folder_download = NBDESIGNER_CUSTOMER_DIR;
// $folder = '00805341621494253';
// $awsAccessKey = 'AKIAX4QORCYMGSQXVT55';
// $awsSecretKey = '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';
// $amazonRegion = 'ap-southeast-1';
// use Aws\S3\S3Client;
// use Aws\Credentials\Credentials;
// $bucket = 'bts-design-template';
// $credentials = new Credentials("$awsAccessKey", "$awsSecretKey");

// //Instantiate the S3 client with your AWS credentials
// $s3Client = S3Client::factory(array(
//     'credentials' => $credentials,
//     'region' => "$amazonRegion",
//     'version' => 'latest'));
// $not_download = array();
// $download = array();
// $not_download_1 = array();
// $download_1 = array();
// $folder_S3 = 'Design 2021/';
// $folder_S3_1 = 'design_2020/';
// $isset_folder = 0;
// $not_isset_folder = array();

// // $s3Client->downloadBucket($folder_download.'/test/ffea2661602133378' , $bucket, $folder_S3_1.'ffea2661602133378');
// foreach(json_decode($list_items) as $items) {
//     if($items->y == '2021') {
//         if( !is_dir($folder_download.'/'.$items->folder) ) {
//             $s3Client->downloadBucket($folder_download.'/'.$items->folder , $bucket, $folder_S3.$items->m.'/'.$items->folder);
//             if( is_dir($folder_download.'/'.$items->folder) ) {
//                 $download[] = $items->folder;
//             } else {
//                 $not_download[] = $items->folder;
//             }
//         } else {
//             $not_download[] = $items->folder;
//         }
//     } else {
//        if( !is_dir($folder_download.'/'.$items->folder) ) {
//             $s3Client->downloadBucket($folder_download.'/'.$items->folder , $bucket, $folder_S3_1.$items->folder);
//             if( is_dir($folder_download.'/'.$items->folder) ) {
//                 $download_1[] = $items->folder;
//             } else {
//                 $not_download_1[] = $items->folder;
//             }
//         } 
//         else {
//             $not_download_1[] = $items->folder;
//         }
//     }
//     if( !is_dir($folder_download.'/'.$items->folder) ) {
//         $not_isset_folder[] = $items->folder;
//     }
//     echo $folder_download.'/'.$items->folder;
// }
// // echo '<b>Tất cả: '.count(json_decode($list_items)).'</b> <br>';
// echo '<b>Thư mục chưa copy: </b> <br>';
// echo '<pre>';
// var_dump($not_isset_folder);
// echo '</pre>';

// function _botak_check_link_exists_s3($link) {
//     $file_headers = @get_headers($link);
//     if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 403 Forbidden') {
//         return false;
//     }
//     else {
//         return true;
//     }
// }

// $dirs = array_filter(glob(NBDESIGNER_TEMP_DIR.'/2021/*'), 'is_dir');

// $format_dirs = array();

// foreach( $dirs as $dir) {
//     $folder_a = explode('/', $dir);
//     $m_folder = end($folder_a);
//     $month_dirs = array_filter(glob(NBDESIGNER_TEMP_DIR.'/2021/'.$m_folder.'/*'), 'is_dir');
//     $format_dirs[$m_folder] = array();
//     foreach( $month_dirs as $m_dir) {
//         $d_folder_a = explode('/', $m_dir);
//         $d_folder = end($d_folder_a);
//         $format_dirs[$m_folder][$d_folder] = array();
//         $day_dirs = array_filter(glob(NBDESIGNER_TEMP_DIR.'/2021/'.$m_folder.'/'.$d_folder.'/*'), 'is_file');
//         foreach( $day_dirs as $d_dir) {
//             $file = explode('uploads/nbdesigner/design-templates/', $d_dir);
//             $_file = end($file);
//             $link_s3 = 'https://botaksignorder.s3.ap-southeast-1.amazonaws.com/design-templates/'.$_file;
//             if(_botak_check_link_exists_s3($link_s3)) {
//                 $format_dirs[$m_folder][$d_folder][] = array( 
//                     'link'      => $link_s3,
//                     'status'    => 'S3'
//                 );
//             } else {
//                 $path_image = NBDESIGNER_TEMP_DIR.'/'.$_file;
//                 // $_link_s3 = nbd_upload_file_custom_to_s3( $_file , $path_image , 'design-templates' );
//                 $src  = 'Local';
//                 // if($_link_s3 ) {
//                 //     $src = 'S3_new';
//                 // }
//                 $format_dirs[$m_folder][$d_folder][] = array( 
//                     'link'      => $link_s3,
//                     // 'path'      => $path_image,
//                     // 'file'      => $_file,
//                     'status'    => $src
//                 );
//             }
//         }
//     }
// }

//  Update Specialist id cho order 1 thang tro lai
// global $wpdb;
// $sql_order = 'SELECT *  FROM `wp_posts` WHERE `post_date` >= "2022-01-01 12:43:21" AND `post_type` LIKE "shop_order" ORDER BY `post_date` DESC';
// $_orders = $wpdb->get_results($sql_order );
// foreach($_orders as $_order) {
//     $order_id = $_order->ID;
//     $current_user = get_post_meta($order_id, '_customer_user', true);
//     if($current_user) {
//         $specialist = get_user_meta($current_user, 'specialist', true);
//         if($specialist) {
//             update_post_meta($order_id, '_specialist_id', $specialist);
//         }
//     }
// }

// test Delivery
// echo '<pre>';
// var_dump(test_get_delivery_plotter());
// echo '</pre>';
echo 'Check link s3';
// $order_id = 63199;
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
// 

$keys = array('a5598861647590597', '823a7891647591076', '6d590391647620413', '7f5bc221647620505', 'bb407631647620759', '2331d991647620948', '0485151647621607', '7d59f241647622384', '0d878561647622434', '2e5c9201647623359');

// if( $result ){
//     if(move_uploaded_file($_FILES['file']["tmp_name"],$path['full_path'])){
//         $link_s3 = nbd_upload_file_custom_to_s3($path['date_path'] , $path['full_path'] , 'design-templates' );
//         if(!$link_s3) {
//             $result     = false;
//             $res['mes'] = esc_html__('An error occurred while uploading the file to AWS!', 'web-to-print-online-designer');
//             unlink($path['full_path']);
//         }
//         $origin_path = $path['full_path'];
//         $res['mes'] = esc_html__('Upload success !', 'web-to-print-online-designer');
//     }else{
//         $result     = false;
//         $res['mes'] = esc_html__('Error occurred with file upload!', 'web-to-print-online-designer');
//     }
// }
// if( $result ){
//     $res['src']     = $link_s3;
//     $res['flag']    = 1;
    
//     if( $ext == 'pdf' ){
//         $new_pdf_width          = $new_pdf_height = 880;
//         $_dpi                   = 300;
//         $new_name               = str_replace( ".pdf", ".png", $new_name );
//         // try {
//         //     $im = new Imagick();
//         //     $im->setResolution( $_dpi, $_dpi );
//         //     $im->setSize( $new_pdf_width, $new_pdf_height );
//         //     $im->readImage( $path['full_path'] . '[0]' );
//         //     $im->setImageFormat( 'png32' );
//         //     $im->setImageUnits( imagick::RESOLUTION_PIXELSPERINCH );
//         //     $im->setResolution( $_dpi, $_dpi );
//         //     $path['full_path']  = str_replace( ".pdf", ".png", $path['full_path'] );
//         //     $im->writeImage( $path['full_path'] );
//         //     $im->clear();
//         //     $im->destroy();
//         //     $ext                    = 'png';
//         //     $res['origin_pdf']      = $path['date_path'];
//         //     $res['src']             = str_replace( ".pdf", ".png", $res['src'] );
//         // } catch( Exception $e ) {
//         //     $res['flag']    = 0;
//         //     $res['mes']     = esc_html__( 'Error occurred when convert pdf!', 'web-to-print-online-designer' );
//         // }
//         NBD_Image::cloud_pdf2image( $path['full_path'], $_dpi, 'png' );
//         $img_path   = str_replace( ".pdf", "_1.png", $path['full_path'] );
//         if( file_exists( $img_path ) ){
//             $path['full_path']      = str_replace( ".pdf", ".png", $path['full_path'] );
//             $ext                    = 'png';
//             $res['origin_pdf']      = $path['date_path'];
//             $res['src']             = str_replace( ".pdf", ".png", $res['src'] );
//             copy( $img_path, $path['full_path'] );
//         }else{
//             $res['flag']    = 0;
//             $res['mes']     = esc_html__( 'Error occurred when convert pdf!', 'web-to-print-online-designer' );
//         }
//         $path_convert   = str_replace( ".pdf", ".png", $path['date_path']);
//         $link_convert   = nbd_upload_file_custom_to_s3( $path_convert , $path['full_path'] , 'design-templates' );
//     }

//     if( nbdesigner_get_option( 'nbdesigner_enable_generate_photo_thumb', 'no' ) == 'yes' ){
//         $resizable_extensions = array( 'jpg', 'jpeg', 'png' );
//         if( in_array( $ext, $resizable_extensions ) ){
//             $preview_size               = apply_filters( 'nbd_max_photo_thumb_size', 800 );

//             $exif = exif_read_data($path['full_path']);
//             if(isset($exif['Orientation']) && $exif['Orientation'] > 4) {
//                 list( $height, $width )     = getimagesize( $path['full_path'] );
//             } else {
//                 list( $width, $height )     = getimagesize( $path['full_path'] );
//             }
//             if( $width > $preview_size || $height > $preview_size ){
//                 $infos          = pathinfo( $path['full_path']);
//                 $path_preview   = $infos['dirname'] . '/' . $infos['filename'] . '_preview.' . $infos['extension'];
//                 if( $ext == 'png' ){
//                     NBD_Image::nbdesigner_resize_imagepng( $path['full_path'], $preview_size, $preview_size, $path_preview );
//                 } else {
//                     NBD_Image::nbdesigner_resize_imagejpg( $path['full_path'], $preview_size, $preview_size, $path_preview );
//                 }
//                 $_path_preview = str_replace( $infos["filename"] , $infos["filename"] . '_preview' , $path['date_path'] );
//                 $_path_preview = str_replace( '.pdf' , '.png' , $_path_preview );  
//                 $s3_preview = nbd_upload_file_custom_to_s3( $_path_preview , $path_preview , 'design-templates' );
//                 if( file_exists( $path_preview ) ){
//                     $res['origin_url']  = $res['src'];
//                     $res['src']         = $s3_preview;
//                     $res['width']       = $width;
//                     $res['height']      = $height;
//                 }
//             }
//         }
//     }
// } else {
//     $res['flag'] = 0;
// }


//check File image in design template
// $resources  = (array)json_decode( file_get_contents( 'https://bts-design-template.s3.ap-southeast-1.amazonaws.com/all-file-design-templates-27-04-2022.json' ) );

// $newUrlList = [];
// foreach ($resources as $key => $url) {
//     if( strpos( $url, '//botaksign.com/wp-content/uploads/nbdesigner/') ) {
//         $newurl = str_replace('https://botaksign.com/wp-content/uploads/nbdesigner/', 'https://botaksignorder.s3.ap-southeast-1.amazonaws.com/', $url);
//         $newUrlList[] = $newurl;
//     } else if( strpos( $url, '//botaksignorder.s3.ap-southeast-1.amazonaws.com/') ) {
//         $newUrlList[] = $url;
//     } else {
//         if( strpos( $url, '2020/') ) {
//             $newUrlList[] = 'https://botaksignorder.s3.ap-southeast-1.amazonaws.com/temp'.$url;
//         } else {
//             $newUrlList[] = 'https://botaksignorder.s3.ap-southeast-1.amazonaws.com/design-templates'.$url;
//         }
//     }
// }
// echo '<pre>';
// var_dump($newUrlList);
// 



//Test 
//

$order = wc_get_order('44208');

$items = $order->get_items();

echo '<pre>';

var_dump($items);

echo '</pre>';