<?php
get_header();
$work_id = get_the_ID();
wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), NBT_VER);
wp_enqueue_style('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css', array(), NBT_VER);
wp_enqueue_script('bootsrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), NBT_VER, true);
wp_enqueue_script('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), NBT_VER, true);
?>
<style>
    .swiper-container {
        width: 100%;
        height: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    .swiper-slide {
        background-size: cover;
        background-position: center;
    }
    .gallery-top {
        height: 80%;
        width: auto;
    }
    .gallery-thumbs {
        height: 20%;
        box-sizing: border-box;
        padding: 10px 0;
    }
    .gallery-thumbs .swiper-slide {
        height: 100%;
        opacity: 0.4;
    }
    .gallery-thumbs .swiper-slide-thumb-active {
        opacity: 1;
    }

</style>
<?php if (function_exists('yoast_breadcrumb')) { ?>
    <div class="nb-page-title-wrap single-breadcrum">
        <div class="container">
            <?php yoast_breadcrumb('<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">', '</nav>'); ?>
        </div>
    </div>
<?php } ?>
<div class="container">
    <div class="row">
        <main class="shop-main left-images bottom-thumb split-reviews-form horizontal-tabs related-4-columns upsells-3-columns" role="main">
            <div class="woocommerce-notices-wrapper"></div>
            <div class="product type-product status-publish first instock product_cat-custom-cutting product_cat-printing-products has-post-thumbnail taxable shipping-taxable purchasable product-type-simple">
                <div class="single-product-wrap">
                    <?php echo do_shortcode('[show_gallery_work work_id="' . $work_id . '"]'); ?>
                    <div class="summary entry-summary">
                        <h1 class="product_title entry-title"><?php the_title(); ?></h1>
                        <div class="work_meta">
                            <span class="work-code"><?php echo get_field('work_code', $work_id); ?></span>
                        </div>
                        <div class="work_des">
                            <?php the_content(); ?>
                        </div>
                    </div><!-- .summary -->
                </div>
            </div>

        </main><!--.main-shop-->
    </div><!--.row-->
</div>
<?php
get_footer();
