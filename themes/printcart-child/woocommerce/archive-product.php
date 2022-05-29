<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     3.4.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop');
?>

<?php
$grouped_ids = get_ids_product_grouped();
/**
 * woocommerce_before_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 */
if (function_exists('is_shop') && is_shop() && !is_search()) {
    $shop_banner_url = printcart_get_options('nbcore_shop_banner');
    if ($shop_banner_url) {
        // echo '<div class="shop-banner"><img src="' . esc_url(wp_get_attachment_url(absint($shop_banner_url))) . '" /></div>';
    }
    ?>
    <div class="nb-page-title-wrap single-breadcrum">
        <div class="container">
            <div class="nb-page-title">
                <?php woocommerce_breadcrumb(); ?>
            </div>
        </div>
    </div>
    <div class="container">
        <?php echo do_shortcode(get_post(wc_get_page_id ( 'shop' ))->post_content); ?>
        <?php /*<div class="product-items grid-type">
                <?php echo do_shortcode('[show_shop_popular_product per_cat="20"]'); ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
                <script>
                    jQuery(function($) {
                        $('.product-items .shop-popular-product .products').addClass('owl-carousel');
                        jQuery(".owl-carousel").owlCarousel({
                            items: 4,
                            loop: false,
                            margin: 0,
                            nav: true,
                            navText: ["<i class=\'fa fa-caret-left\'></i>", "<i class=\'fa fa-caret-right\'></i>"],
                            autoplay: true,
                            autoplayHoverPause: true,
                            responsive: {0: {items: 2}, 600: {items: 3}, 1000: {items: 4}}
                        });
                    });
                </script>
                <a class="view-all" href="#">View All Products</a>
            </div> */ ?>
    </div>
    <?php /* <div class="container">
        <h2 class="archive-title-css">Shop</h2>
        <div class="products row grid-type">
            <?php echo do_shortcode('[show_category_product_sc]'); ?>
        </div>
    </div> */?>
    <?php
} else {

    /**
     * woocommerce_archive_description hook.
     *
     * @hooked woocommerce_taxonomy_archive_description - 10
     * @hooked woocommerce_product_archive_description - 10
     */
    //do_action( 'woocommerce_archive_description' );
    ?>

    <?php
    if (have_posts()):
        global $wp_query;
        $cat = $wp_query->get_queried_object();
        if (isset($cat->term_id)):
            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            if (is_numeric($thumbnail_id) && $thumbnail_id) {
                $image = wp_get_attachment_url($thumbnail_id);
                ?>
                <div class="shop-thumbnail">
                    <?php if (is_product_category()) { ?>
                        <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
                    <?php } ?>
                    <img src="<?php echo esc_url($image); ?>">
                </div>
                <?php
            } else {
                $shop_banner_url = printcart_get_options('nbcore_shop_banner');
                if ($shop_banner_url) {
                    echo '<div class="shop-banner"><img src="' . esc_url(wp_get_attachment_url(absint($shop_banner_url))) . '" /></div>';
                }
            }
        else:
            $shop_banner_url = printcart_get_options('nbcore_shop_banner');
            if ($shop_banner_url) {
                echo '<div class="shop-banner"><img src="' . esc_url(wp_get_attachment_url(absint($shop_banner_url))) . '" /></div>';
            }
        endif;
        ?>
        <?php
        $term_pp = get_queried_object();
        if (get_field('options_layout', $term_pp) == 1) {
            ?>
            <div class="nb-page-title-wrap single-breadcrum">
                <div class="container">
                    <div class="nb-page-title">
                        <?php woocommerce_breadcrumb(); ?>
                    </div>
                </div>
            </div>
            <div class="container">
                <?php if (get_field('show_popular_products', $term_pp) == 'y' && get_term_meta($cat->term_id, 'display_type', true)!='subcategories') { ?>
                    <div class="wc-minh-crs">
                        <?php echo do_shortcode('[custom_bestselling_product_by_categories cats="' . $cat->term_id . '" per_cat="8" columns="0" link_text="View All Products"]'); ?>
                        <a class="view-all" href="<?php echo get_term_link($cat, 'product_cat'); ?>">View All Products</a>
                    </div>
                <?php } ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
                <div class="wrap-dao-required">
                    <h2 class="archive-title-css"><?php echo $cat->name; ?></h2>
                    <?php echo do_shortcode('[product_categories parent="' . $cat->term_id . '" hide_empty="0" columns="3"]'); ?>
                </div>
            </div>
            <?php
        } else {
            do_action('woocommerce_before_main_content');

            if (printcart_get_options('nbcore_shop_action')):
                ?>

                <div class="shop-action">
                    <?php
                    /**
                     * woocommerce_before_shop_loop hook.
                     *
                     * @hooked woocommerce_result_count - 20
                     * @hooked woocommerce_catalog_ordering - 30
                     */

                    //CS botak side bar
                    if (printcart_get_options('product_sticky_sidebar')) {
                        $product_class = ' sticky-wrapper sticky-sidebar';
                    }

                    if (function_exists('is_woocommerce') && is_woocommerce()) {
                        if (is_product()) {
                            if ('no-sidebar' !== printcart_get_options('nbcore_pd_details_sidebar') && is_active_sidebar('product-sidebar')) {
                                echo '<aside class="widget-area mb-shop-category" role="complementary"><div class="sidebar-wrapper' . esc_attr($product_class) . '">';
                                dynamic_sidebar('product-sidebar');
                                echo '</div></aside>';
                            }
                        } else {
                            if ('no-sidebar' !== printcart_get_options('nbcore_shop_sidebar') && is_active_sidebar('shop-sidebar')) {
                                echo '<aside class="widget-area mb-shop-category" role="complementary"><div class="sidebar-wrapper' . esc_attr(isset($shop_class) ? $shop_class : '') . '">';
                                dynamic_sidebar('shop-sidebar');
                                echo '</div></aside>';
                            }
                        }
                    }
                    do_action('woocommerce_before_shop_loop');
                    ?>
                </div>
            <?php endif; ?>

            <?php woocommerce_product_loop_start(); ?>
            <div class="decription">
                <?php echo category_description($cat->term_id); ?> 
            </div>
            <?php woocommerce_product_subcategories(); ?>

            <?php while (have_posts()): the_post(); ?>

                <?php if($grouped_ids) {
                    $flag = 0;
                    foreach ($grouped_ids as $key => $value) {
                        if(get_the_ID() == $value) {
                            $flag =1;

                        }
                    }
                } 
                if($flag == 1) {
                    break;
                }
                ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php
            /**
             * woocommerce_after_shop_loop hook.
             *
             * @hooked woocommerce_pagination - 10
             */
            do_action('woocommerce_after_shop_loop');
            
            do_action('woocommerce_after_main_content');
        }
        ?>


    <?php elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))): ?>

        <?php wc_get_template('loop/no-products-found.php'); ?>

    <?php endif; ?>

    <?php
    //if (isset($cat->parent) && $cat->parent != 0) {
    //    do_action('woocommerce_after_main_content');
    //}
}
/**
 * woocommerce_after_main_content hook.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
?>


<?php get_footer('shop'); ?>