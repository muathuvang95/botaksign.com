<?php

class NB_Gallery_Image {
    protected $field;
            
    function __construct() {
        $this->action_hook();
    }
    
    function action_hook() {
        add_action( 'admin_menu', array($this, 'add_menu_pages') );
        //add_action( 'nbd_menu', array($this, 'add_sub_menu'), 90 );
        add_action( 'admin_post_upload_gallery_image', array($this, 'upload_gallery_image') );   
        add_action( 'admin_post_nopriv_upload_gallery_image', array($this, 'upload_gallery_image') ); 
        add_action( 'admin_post_update_gallery_image', array($this, 'update_gallery_image') );   
        add_action( 'admin_post_nopriv_update_gallery_image', array($this, 'update_gallery_image') ); 
        add_action( 'admin_post_upload_water_mark', array($this, 'upload_water_mark') );
        add_action( 'admin_post_nopriv_upload_water_mark', array($this, 'upload_water_mark') );
        add_action( 'admin_post_delete_gallery_image', array($this, 'delete_gallery_image') );
        add_action( 'admin_post_nopriv_delete_gallery_image', array($this, 'delete_gallery_image') ); 
        add_action( 'rest_api_init', array($this, 'define_endpoint') ); 
        add_action( 'wp_ajax_gallery_get_image_info', array($this, 'gallery_get_image_info') );
        add_action( 'wp_ajax_nopriv_gallery_get_image_info', array($this, 'gallery_get_image_info') );
        add_action( 'wp_ajax_get_original_image', array($this, 'get_original_image') );
        add_action( 'wp_ajax_nopriv_get_original_image', array($this, 'get_original_image') );
        // Hooking up our function to theme setup
        add_action( 'init', array($this, 'create_posttype') );
        add_action('woocommerce_after_cart_item_name', array($this, 'render_gallery_item'), 20, 2);
        add_action('admin_notices', array($this, 'admin_notice'));
    }
    
    function add_menu_pages() {
        add_menu_page('Gallery Image', 'Gallery Image', 'manage_options', 'gallery-image', '', 'dashicons-chart-area' );
        add_submenu_page('gallery-image', 'Gallery Image', 'Gallery Image', 'manage_options', 'gallery-image', array($this, 'image_gallery_page'));
        add_submenu_page('gallery-image', 'Add new', 'Add new', 'manage_options', 'add-new',  array($this, 'upload_image_form'));
        add_submenu_page('gallery-image', 'Watermark', 'Watermark', 'manage_options', 'watermark',  array($this, 'water_mark'));
        //add_submenu_page(
        //    'nbdesigner', esc_html__('Gallery Image', 'web-to-print-online-designer'), esc_html__('Gallery Image', 'web-to-print-online-designer'), 'manage_nbd_tool', 'gallery_image', array($this, 'upload_image_form')
        //);
    }
    
    function admin_notice(){
        global $pagenow;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        if ( $pagenow == 'admin.php' ) {
            switch ($status) {
                case 'delete-success': 
                    echo '
                        <div class="notice notice-success is-dismissible">
                            <p>Delete image success!</p>
                        </div>
                    ';
                    break;
                case 'delete-failed': 
                    echo '
                        <div class="notice notice-error is-dismissible">
                            <p>Delete image failed!.</p>
                        </div>
                    ';
                    break;
                case 'update-success': 
                    echo '
                        <div class="notice notice-success is-dismissible">
                            <p>Update image success!</p>
                        </div>
                    ';
                    break;
                case 'upload-success': 
                    echo '
                        <div class="notice notice-success is-dismissible">
                            <p>Upload image success!</p>
                        </div>
                    ';
                    break;
                case 'upload-failed': 
                    echo '
                        <div class="notice notice-error is-dismissible">
                            <p>Type or Size of image invalid!</p>
                        </div>
                    ';
                    break;
            }
        }
    }
    
    function upload_image_form() {
        if (!defined('ABSPATH')) exit; // Exit if accessed directly
            wp_enqueue_style('bootstrap', NBDESIGNER_PLUGIN_URL .'assets/css/bootstrap.min.css');
            wp_enqueue_style("peyment-form", NBDESIGNER_PLUGIN_URL."/assets/css/payment-form.css");
        ?>
            <div class="form-gallery-container">
                <h1>Add new image</h1>
                <div class="nbgallery-content-full">
                    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST"  enctype="multipart/form-data">
                        <table>
                            <tr>
                                <th valign="top">Image Field</th>
                                <td valign="top">
                                    <input type="file" name="file" required="true">
                                    <div class="nbd-admin-font-tip">
                                        <?php esc_html_e('Allow extensions: png, jpg, jpeg', 'web-to-print-online-designer'); ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th valign="top">Image title</th>
                                <td valign="top">
                                    <input type="text" name="title" required="true">
                                </td>
                            </tr>
                            <tr>
                                <th valign="top">Image price</th>
                                <td valign="top">
                                    <input type="number" name="price" step="0.1" value="1.5" required="true">
                                </td>
                            </tr>
                            <input type="hidden" name="action" value="upload_gallery_image">
                        </table>
                        <input type="submit" name="upload" value="Upload Image">
                    </form>
                </div>
            </div>
        <?php 
    }
    
    function water_mark() {
        if (!defined('ABSPATH')) exit; // Exit if accessed directly
        wp_enqueue_style('bootstrap', NBDESIGNER_PLUGIN_URL .'assets/css/bootstrap.min.css');
        wp_enqueue_style("peyment-form", NBDESIGNER_PLUGIN_URL."/assets/css/payment-form.css");
        $image_url = NBDESIGNER_PLUGIN_URL . 'assets/images/botak-mark/botak-mark.png';
        $image_path = NBDESIGNER_PLUGIN_DIR . 'assets/images/botak-mark/botak-mark.png';
        ?>
            <div class="form-gallery-container upload-watermark">
                <h1>Watermark</h1>
                <div class="nbgallery-content-full">
                    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST"  enctype="multipart/form-data">
                        <table>
                            <tr>
                                <th valign="top">Image</th>
                                <td valign="top">
                                    <div class="image">
                                        <?php if (file_exists($image_path)): ?>
                                            <img src="<?php echo $image_url; ?>"/>
                                        <?php else: ?>
                                            <p>You have no watermark!</p>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="file" required="true">
                                    <div class="nbd-admin-font-tip">
                                        <?php esc_html_e('Allow extensions: png', 'web-to-print-online-designer'); ?>
                                    </div>
                                </td>
                            </tr>
                            <input type="hidden" name="action" value="upload_water_mark">
                        </table>
                        <?php if (file_exists($image_path)): ?>
                            <input type="submit" name="upload" value="Change watermark">
                        <?php else: ?>
                            <input type="submit" name="upload" value="Upload watermark">
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php 
    }
    
    function image_gallery_page() {
        if (!defined('ABSPATH')) exit; // Exit if accessed directly
        wp_enqueue_style('bootstrap', NBDESIGNER_PLUGIN_URL .'assets/css/bootstrap.min.css');
        wp_enqueue_style("peyment-form", NBDESIGNER_PLUGIN_URL."/assets/css/payment-form.css");
        if (isset($_GET['task']) && isset($_GET['image-id']) && $_GET['task'] == "edit") {
            $image_id = $_GET['image-id'];
            $image = get_post($image_id);
            $image_url = get_post_meta($image_id, 'nbd_image_original', true);
            $image_price = get_post_meta($image_id, "nbd_image_price",true);
        ?>
            <div class="form-gallery-container">
                <h1>Edit image</h1>
                <div class="nbgallery-content-full">
                    <div class="image">
                        <img src="<?php echo $image_url; ?>"/>
                    </div>
                    <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST"  enctype="multipart/form-data">
                        <table>
                            <tr>
                                <th valign="top">Image price</th>
                                <td valign="top">
                                    <input type="number" name="price" step="0.1" value="<?php echo $image_price; ?>" required="true">
                                </td>
                            </tr>
                            <tr>
                                <th valign="top">Image title</th>
                                <td valign="top">
                                    <input type="text" name="title" value="<?php echo $image->post_title; ?>" required="true">
                                </td>
                            </tr>
                            <input type="hidden" name="image_id" value="<?php echo $image_id; ?>">
                            <input type="hidden" name="action" value="update_gallery_image">
                        </table>
                        <div class="action">
                            <input type="submit" name="upload" value="Update Image">
                            <button type="button" id="btn-delete-image">Delete Image</button>
                        </div>
                    </form>
                </div>
                <div class="popup-confirm-delete" id="popup-confirm-delete">
                    <div class="popup-container">
                        <span class="close-sevice-popup">x</span>
                        <div class="popup-body">
                            <form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST"  enctype="multipart/form-data">
                                <h2 class="title">Confirm deletion?</h2>
                                <p class="content">You will not be able to recover this file!</p>
                                <input type="hidden" name="action" value="delete_gallery_image">
                                <input type="hidden" name="image_id" value="<?php echo $image_id; ?>">
                                <div class="action">
                                    <button id="delete" type="submit">Delete</button>
                                    <button type="button" id="cancel">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function ($) {
                    $('#btn-delete-image').click(function(e) {
                        $("#popup-confirm-delete").show();
                    })
                    $('#popup-confirm-delete #delete, #popup-confirm-delete #cancel').click(function(e) {
                        $("#popup-confirm-delete").hide();
                    })
                })
            </script>
        <?php 
        } else {
            $paged = isset( $_REQUEST['paged']) ? max( 1, ( int ) $_REQUEST['paged'] ) : 1;
            $per_page = 18;

            $args = array(
                'posts_per_page'    => $per_page,
                'post_type'         => 'nbd_image_gallery',
                'orderby'           => 'date',
                'order'             => 'DESC',
                'offset'            => $paged ? ($paged - 1) * $per_page : 0
            );
            $query = new WP_Query($args);
            $posts = $query->get_posts();
            $gallery = [];
            foreach ($posts as $image) {
                $gallery[] = [
                    'id'        => $image->ID,
                    'img_url'   => $image->guid
                ];
            }
            ?>
                <div class="nbgallery-container">
                    <h1>Gallery Image <a href="<?php echo add_query_arg(array('page' => 'add-new'), admin_url('admin.php')) ?>" style="margin-top: 5px;margin-left: 12px;" class="btn button">Add new</a></h1>
                    <div class="row block-gallery">
                        <?php foreach($gallery as $i): ?>
                            <div class="col-md-2 col-xs-3 block-img">
                                <a href="<?php echo add_query_arg( array(
                                    'page'      => 'gallery-image',
                                    'task'      => 'edit',
                                    'image-id'  => $i['id'],
                                ), $_SERVER['REQUEST_URI'] );?>">
                                    <svg enable-background="new 0 0 91 91" height="30" width="30" id="Layer_1" version="1.1" viewBox="0 0 91 91" width="91px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M65.689,31.201l-4.153-4.153c-1.23-1.231-2.867-1.91-4.609-1.91s-3.379,0.678-4.609,1.909L29.229,50.134   c-0.095,0.095-0.173,0.202-0.241,0.313c-0.02,0.032-0.035,0.064-0.053,0.098c-0.048,0.092-0.088,0.187-0.118,0.285   c-0.007,0.021-0.021,0.041-0.026,0.063l-4.008,14.826c-0.159,0.588,0.008,1.216,0.438,1.646c0.323,0.322,0.757,0.498,1.202,0.498   c0.148,0,0.297-0.02,0.443-0.059l14.189-3.836c0.113,0.023,0.229,0.036,0.344,0.036c0.436,0,0.87-0.166,1.202-0.498L65.689,40.42   c1.231-1.231,1.91-2.869,1.909-4.61C67.599,34.069,66.921,32.432,65.689,31.201z M41.4,59.9l-8.564-8.564l18.229-18.229   l8.565,8.563L41.4,59.9z M28.837,63.75l2.47-9.135l6.665,6.665L28.837,63.75z M63.285,38.016l-1.25,1.25l-8.565-8.563l1.252-1.251   c1.178-1.178,3.232-1.178,4.41,0l4.153,4.154c0.589,0.589,0.914,1.373,0.914,2.206S63.875,37.427,63.285,38.016z"/><rect height="3.4" transform="matrix(0.7071 -0.7071 0.7071 0.7071 -18.8662 46.5096)" width="15.087" x="39.166" y="44.328"/></g></svg>
                                </a>
                                <img src="<?php echo $i['img_url']; ?>"/> 
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="pagination">
                        <?php
                            $total_pages = $query->max_num_pages;

                            if ($total_pages > 1) {
                                echo '<div style="width: 100%;">' . paginate_links(array(
                                    'base' => add_query_arg('paged','%#%'),
                                    'format' => '',
                                    'current' => max(1, $paged),
                                    'total' => $total_pages,
                                    'prev_text' => __('« '),
                                    'next_text' => __(' »'),
                                )) . '</div>';
                            }
                            wp_reset_postdata();
                        ?>
                    </div>
                </div>
            <?php
        }
    }
    
    function upload_gallery_image($request) {
        if (!count($_FILES)) {
            wp_redirect(
                add_query_arg(array(
                    'page'      => 'add-new',
                    'status'    => 'upload-failed'
                ), admin_url('admin.php'))
            );
        }
        $image_price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
        $image_title = isset($_POST['title']) ? $_POST['title'] : '';

        $folder_gallery = wp_get_upload_dir()["basedir"] . '/gallery-src';
        
        if (!is_dir($folder_gallery)) {
            mkdir($folder_gallery);
        }
        $folder_image = $folder_gallery . "/" . uniqid();
        if (!is_dir($folder_image)) {
            mkdir($folder_image);
        }
        $img_path = $folder_image . "/" . basename( $_FILES['file']['name'] );
        
        $upload = move_uploaded_file( $_FILES['file']['tmp_name'], $img_path );

        if ($upload) {
            $url_img_watermark = $img_path;
            $watermark_path = NBDESIGNER_PLUGIN_DIR . 'assets/images/botak-mark/botak-mark.png';
            $watermark_url = NBDESIGNER_PLUGIN_URL . 'assets/images/botak-mark/botak-mark.png';
            if (file_exists($watermark_path)) {
                $im = imagecreatefromstring(file_get_contents($img_path));
                
                $watermark_before = imagecreatefromstring(file_get_contents($watermark_url));

                // Set the margins for the stamp and get the height/width of the stamp image
                $sx_before = imagesx($watermark_before);
                $sy_before = imagesy($watermark_before);
                
                //Resize water mark to fit image upload
                $watermark_resize = NBD_Image::nbdesigner_resize_imagepng($watermark_path, ($sx_before * imagesy($im)) / ( $sy_before * 5), imagesy($im)/5);

                // Set the margins for the stamp and get the height/width of the stamp image
                $sx_after = imagesx($watermark_resize);
                $sy_after = imagesy($watermark_resize);
                
                // Copy the stamp image onto our photo using the margin offsets and the photo 
                // width to calculate positioning of the stamp. 
                imagecopy($im, $watermark_resize, (imagesx($im) - $sx_after)/2, (imagesy($im) - $sy_after)/2, 0, 0, $sx_after, $sy_after);

                // Output and free memory
                $folder_image_watermark = wp_get_upload_dir()["basedir"] . '/gallery-image';
                if (!is_dir($folder_image_watermark)) {
                    mkdir($folder_image_watermark);
                }

                //save image have water mark
                $image_have_watermark = $folder_image_watermark . "/" . basename( $_FILES['file']['name'] );
                imagepng($im, $image_have_watermark);
                imagedestroy($im);
            }
            
            //Convert path to url
            $url_img_original = $this->convert_path_to_url($img_path);
            $url_img_watermark = $this->convert_path_to_url($image_have_watermark);
            
            $image_id = $this->getImgID($url_img_watermark, $image_title);
            if ($image_id) {
                update_post_meta($image_id, 'nbd_image_original', $url_img_original);
                update_post_meta($image_id, 'nbd_image_watermark', $url_img_watermark);
                update_post_meta($image_id, 'nbd_image_price', $image_price);
                update_post_meta($image_id, 'nbd_galery_image', true);
            }
        }
        
        wp_redirect(
            add_query_arg(array(
                'page'      => 'gallery-image',
                'status'    => 'upload-success'
            ), admin_url('admin.php'))
        );
    }
    
    function upload_water_mark($request) {
        if (!count($_FILES)) {
            wp_redirect(
                add_query_arg(array(
                    'page'      => 'watermark',
                    'status'    => 'upload-failed'
                ), admin_url('admin.php'))
            );
        }
        
        $file_type = $_FILES['file']['type'];

        if ( $file_type != "image/png" ){
            wp_redirect(
                add_query_arg(array(
                    'page'      => 'watermark',
                    'status'    => 'upload-failed'
                ), admin_url('admin.php'))
            );
            return;
        }
        
        $img_path = NBDESIGNER_PLUGIN_DIR . 'assets/images/botak-mark/botak-mark.png';
        
        $upload = move_uploaded_file( $_FILES['file']['tmp_name'], $img_path );
        
        if ($upload) {
            wp_redirect(
                add_query_arg(array(
                    'page'      => 'watermark',
                    'status'    => 'upload-success'
                ), admin_url('admin.php'))
            );
        } else {
            wp_redirect(
                add_query_arg(array(
                    'page'      => 'watermark',
                    'status'    => 'upload-failed'
                ), admin_url('admin.php'))
            );
        }
    }
    
    function update_gallery_image($request) {
        $image_price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
        $image_title = isset($_POST['title']) ? $_POST['title'] : '';
        $image_id = isset($_POST['image_id']) ? $_POST['image_id'] : 0;
        if ($image_id) {
            update_post_meta($image_id, 'nbd_image_price', $image_price);
        }

        if ($image_title != '') {
            wp_update_post(array(
                'ID'           => $image_id,
                'post_title'   => $image_title
            ));
        }
        
        wp_redirect(
            add_query_arg(array(
                'page'      => 'gallery-image',
                'task'      => 'edit',
                'image-id'  => $image_id,
                'status'    => 'update-success'
            ), admin_url('admin.php'))
        );
    }
    
    function delete_gallery_image($request) {
        $image_id = isset($_POST['image_id']) ? $_POST['image_id'] : 0;
        if ($image_id) {
            $url_img_original = get_post_meta($image_id, 'nbd_image_original', true);
            $url_img_watermark = get_post_meta($image_id, 'nbd_image_watermark', true);
            $path_img_original = Nbdesigner_IO::convert_url_to_path($url_img_original);
            $path_img_watermark = Nbdesigner_IO::convert_url_to_path($url_img_watermark);
            unlink($path_img_original);
            unlink($path_img_watermark);
            $delete = wp_delete_post($image_id);
            if (is_object($delete)) {
                wp_redirect(
                    add_query_arg(array(
                        'page'      => 'gallery-image',
                        'status'    => 'delete-success'
                    ), admin_url('admin.php'))
                );
                return;
            } else {
                wp_redirect(
                    add_query_arg(array(
                        'page'      => 'gallery-image',
                        'status'    => 'delete-failed'
                    ), admin_url('admin.php'))
                );
                return;
            }
        }

        wp_redirect(
            add_query_arg(array(
                'page'      => 'gallery-image',
                'status'    => 'delete-failed'
            ), admin_url('admin.php'))
        );
        return;
    }
    
    /**
     * @param string $image_url url to download image in $site
     * @param string $image_title name of image
     * @return int ID of image
     */
    function getImgID($image_url, $image_title){
        try {
            $type = getimagesize($image_url)["mime"];

            $post_info = array(
                'guid'           => $image_url,
                'post_mime_type' => $type,
                'post_title'     => $image_title,
                'post_content'   => '',
                'post_status'    => 'publish',
                'post_type'      => 'nbd_image_gallery',
            );
            // Create the attachment
            $attach_id = wp_insert_post( $post_info );

            return $attach_id;
        } catch(Exception $e) {
            return 0;
        }
    }
    
    // Our custom post type function
    function create_posttype() {
        register_post_type( 'nbd_image_gallery',
        // CPT Options
            array(
                'labels' => array(
                    'name' => __( 'Image Gallery' ),
                    'singular_name' => __( 'Image Gallery' )
                ),
                'description'         => 'This is where you can add new image template',
                'public'              => true,
                'show_ui'             => true,
                'capability_type'     => 'nbd_image_gallery',
                'map_meta_cap'        => true,
                'publicly_queryable'  => true,
                'exclude_from_search' => false,
                'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
                'rewrite'             => false,
                'query_var'           => true,
                'show_in_rest'        => true,
            )
        );
    }
    
    //Convert path img to url img
    function convert_path_to_url($path) {
        $upload_dir = wp_upload_dir();
        $basedir    = $upload_dir['basedir'];
        $arr        = explode('/', $basedir);
        $upload     = $arr[count($arr) - 1];
        if(is_multisite() && !is_main_site()) {
            $upload = $arr[count($arr) - 3].'/'.$arr[count($arr) - 2].'/'.$arr[count($arr) - 1];
        }
        
        return content_url( substr($path, strrpos($path, '/' . $upload )) );
    }
    
    function define_endpoint() {
        register_rest_route(
            'nbd/v1',
            '/library',
            array(
                'methods'   => 'GET',
                'callback'  => array($this, 'get_list_template')
            )
        );
    }
    
    function get_list_template($request) {
        $per_page = 20;
        $page = isset( $_GET['page']) ? max( 1, ( int ) $_GET['page'] ) : 1;

        $args = array(
            'posts_per_page'    => $per_page,
            'post_type'         => 'nbd_image_gallery',
            'orderby'           => 'date',
            'order'             => 'DESC',
            'offset'            => $page ? ($page - 1) * $per_page : 0
        );
        
        $query = new WP_Query($args);
        $posts = $query->get_posts();
        $totalPage = $query->max_num_pages;
        $totalImage = $query->post_count;
        
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
        $imgs_paid = get_user_meta($user_id, 'nbd_user_image_gallery_paid', true);
        if (!$imgs_paid || !is_array($imgs_paid)) {
            $imgs_paid = [];
        }
        
        foreach ($posts as $image) {
            $price = get_post_meta($image->ID, 'nbd_image_price', true);
            list($width, $height) = getimagesize($image->guid);
            $gallery[] = [
                'id'        => $image->ID,
                'src'       => in_array(strval($image->ID), $imgs_paid) || (float) $price == 0 ? get_post_meta($image->ID, 'nbd_image_original', true) : $image->guid,
                'width'     => $width,
                'height'    => $height,
                'img_title' => $image->post_title,
                'is_paid'   => in_array(strval($image->ID), $imgs_paid) ? true : false,
                'price'     => $price ? (float) $price : 0
            ];
        }
        
        $data = [
            "gallery"       => $gallery,
            "total"         => $totalImage,
            "limit"         => $per_page,
            "pagesCurrent"  => $page,
            "pagesTotal"    => $totalPage
        ];
        
        return wp_send_json($data, 200);
    }
    
    function gallery_get_image_info($request) {
        $data = [
            'img_id'    => 0,
            'img_title' => "",
            'is_paid' => false,
            'img_url'   => "",
            'img_price' => 0
        ];
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        if (array_key_exists('img_id', $_POST)) {
            $id = $_POST['img_id'];
            if ($user_id) {
                //Get list img is bought
                $list_img_bought = get_user_meta($user_id, 'list_img_bought', true);
                if (is_array($list_img_bought)) {
                    if (in_array($id, $list_img_bought)) {
                        $data = [
                            'is_paid' => true
                        ];
                        wp_send_json($data, 200);
                    }
                }
            }
            $img_obj = get_post($id);
            if (is_object($img_obj)) {
                $img_price = get_post_meta($id, 'nbd_image_price', true);
                $data = [
                    'img_id'    => $id,
                    'img_title' => $img_obj->post_title,
                    'is_paid'   => false,
                    'img_url'   => $img_obj->guid,
                    'img_price' => $img_price ?  (float) $img_price : 0
                ];
            }
        }
        
        wp_send_json($data, 200);
        
        die;
    }
    
    function get_original_image($request) {
        $user_id = get_current_user_id();
        if ($user_id == 0) {
            wp_send_json(array(
                'status'    => 'error',
                'message'   => 'Login is required!'
            ), 200);
        }
        
        if (isset($_POST['img_ids'])) {
            if (is_array($_POST['img_ids'])) {
                $zip_files = [];
                foreach ($_POST['img_ids'] as $img_id) {
                    $original_img = get_post_meta($img_id, 'nbd_image_original', true);
                    $img_file = Nbdesigner_IO::convert_url_to_path($original_img);
                    $zip_files[] = $img_file;
                };
                
                $pathZip = NBDESIGNER_DATA_DIR.'/download/template-gallery-customer-'.$user_id.'.zip';
                $nameZip = 'template-gallery-customer-'.$user_id.'.zip';
                
                //Zip file
                if(file_exists($pathZip)){
                    unlink($pathZip);
                }
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive();
                    if ($zip->open($pathZip, ZIPARCHIVE::CREATE )!==TRUE) {
                      exit("cannot open <$pathZip>\n");
                    }
                    foreach( $zip_files as $key => $file ) {
                        $name = basename($file);
                        $zip->addFile($file, $name);
                    }
                    $zip->close();
                }else{
                    require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
                    $archive = new PclZip($pathZip);
                    foreach($zip_files as $file){
                        $path_arr = explode('/', $file);
                        $dir = dirname($file).'/';                
                        $archive->add($file, PCLZIP_OPT_REMOVE_PATH, $dir, PCLZIP_OPT_ADD_PATH, $path_arr[count($path_arr) - 2]);               
                    }            
                }
                
                if (file_exists($pathZip)) {
                    $url_zip = Nbdesigner_IO::wp_convert_path_to_url($pathZip);
                    wp_send_json(array(
                        'status'    => 'success',
                        'message'   => 'Success',
                        'data'      => base64_encode($url_zip) // return download file url as base64 endcode
                    ), 200);
                } else {
                    wp_send_json(array(
                        'status'    => 'error',
                        'message'   => "Can't zip files"
                    ), 200);
                }
            }
        }
        
        wp_send_json(array(
            'status'    => 'error',
            'message'   => "There are no photos to buy!"
        ), 200);

        die();
    }
    
    function render_gallery_item($cart_item, $cart_item_key) {
        $images_paid = WC()->session->get($cart_item_key. '_images_paid');
        if ($images_paid != null) {
            $images_paid = unserialize($images_paid);
            ?>
            <div class="images-paids-block">
                <div class="title">
                    Premium Images<span></span>
                </div>
                <?php foreach ($images_paid as $img_id): ?>
                    <?php
                        $image = get_post($img_id);
                        $image_title = $image->post_title;
                        $image_url = get_post_meta($img_id, 'nbd_image_original', true);
                        $image_price = get_post_meta($img_id, 'nbd_image_price', true);
                    ?>
                    <div class="image-block">
                        <div class="image">
                            <img src="<?php echo $image_url; ?>" />
                        </div>
                        <div class="info">
                            <p class="image-title"><?php echo $image_title; ?></p>
                            <p class="image-price"><?php echo wc_price($image_price); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php }
    }
}

new NB_Gallery_Image();