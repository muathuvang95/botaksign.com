<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
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
 * @version     3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}
do_action( 'woocommerce_after_single_product_summary_custom' );

global $wpdb;
$products       = array();
$product_id     = get_the_ID();
if( isset( $product_id ) && $product_id ) {
    $materials  = get_post_meta( $product_id , 'materials' );
    if( isset($materials[0]) && ( is_array($materials[0]) || is_object($materials[0]) ) ) {
        foreach( $materials[0] as $material_id ) {
            $arr_object = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON pm.post_id = p.ID WHERE ( p.post_type = 'product' AND meta_key = 'materials' AND (meta_value LIKE '%:\"" . $material_id . "\";%' OR meta_value = " . $material_id . "))");
            if ( count($arr_object) > 0 ) {
                foreach ($arr_object as $obj) {
                    array_push($products, $obj->post_id);
                }
            }
        }
    }
}

if ( count($products) > 0 ) {
    $keys = array_rand( $products, 4 );
    ?>

    <section style="margin-top: 256px;" class="related">

        <h2><?php esc_html_e('Related products', 'printcart'); ?></h2>

        <?php woocommerce_product_loop_start(); ?>
            <?php if( isset($keys) && count($keys) > 0 ) { ?>
                <?php foreach ( $keys as $key ) : ?>

                    <?php
                        $post_object = get_post( $products[$key] );

                        setup_postdata( $GLOBALS['post'] =& $post_object );

                        wc_get_template_part( 'content', 'product' ); ?>

                <?php endforeach; ?>
            <?php } ?>

        <?php woocommerce_product_loop_end(); ?>
    </section>

<?php 
} else {
    if ($related_products) : ?>

    <section style="margin-top: 256px;" class="related">

        <h2><?php esc_html_e('Related products', 'printcart'); ?></h2>

        <?php woocommerce_product_loop_start(); ?>

            <?php foreach ( $related_products as $related_product ) : ?>

                <?php
                    $post_object = get_post( $related_product->get_id() );

                    setup_postdata( $GLOBALS['post'] =& $post_object );

                    wc_get_template_part( 'content', 'product' ); ?>

            <?php endforeach; ?>

        <?php woocommerce_product_loop_end(); ?>
    </section>

<?php endif;
}

wp_reset_postdata();
/*
if ($related_products) : ?>

    <section style="margin-top: 256px;" class="related">

        <h2><?php esc_html_e('Related products', 'printcart'); ?></h2>

        <?php woocommerce_product_loop_start(); ?>

            <?php foreach ( $related_products as $related_product ) : ?>

                <?php
                    $post_object = get_post( $related_product->get_id() );

                    setup_postdata( $GLOBALS['post'] =& $post_object );

                    wc_get_template_part( 'content', 'product' ); ?>

            <?php endforeach; ?>

        <?php woocommerce_product_loop_end(); ?>
    </section>

<?php endif;

wp_reset_postdata();
*/
