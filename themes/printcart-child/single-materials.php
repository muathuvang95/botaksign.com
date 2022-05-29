<?php
get_header();
$material_id = get_the_ID();
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
        height: auto;
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

    .shop-main:not(.wide) .single-product-wrap .entry-summary {
        min-height: 500px;
    }
</style>
<?php if (function_exists('yoast_breadcrumb')) {?>
    <div class="nb-page-title-wrap single-breadcrum">
        <div class="container">
            <?php yoast_breadcrumb('<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">', '</nav>');?>
        </div>
    </div>
<?php }?>
<div class="container">
    <div class="row">
        <main class="shop-main left-images bottom-thumb split-reviews-form horizontal-tabs related-4-columns upsells-3-columns" role="main">
            <div class="woocommerce-notices-wrapper"></div>
            <div class="product type-product status-publish first instock product_cat-custom-cutting product_cat-printing-products has-post-thumbnail taxable shipping-taxable purchasable product-type-simple">
                <div class="single-product-wrap">
                    <?php echo do_shortcode('[show_gallery_material material_id="' . $material_id . '"]'); ?>
                    <div class="summary entry-summary">
                        <h1 class="product_title entry-title"><?php the_title();?></h1>
                        <div class="material_meta">
                            <span class="material-code"><?php echo get_field('material_code', $material_id); ?></span>
                            <p><a class="material-store-url" href="<?php echo get_field('store_url', $material_id); ?>">Go to Material Purchase Page >>></a></p>
                        </div>
                        <div class="material_des">
                            <?php the_content();?>
                        </div>

                        <?php
$attrs = wp_get_post_terms($material_id, array('matealize_attributes'));
foreach ($attrs as $attr) {
	?>
                           <div class="material_attrs">
                            <h5><?php echo $attr->name; ?></h5>
                            <div class="content"><?php echo $attr->description; ?></div>
                        </div>
                        <?php
}
?>

                    <?php if (have_rows('files', $material_id)): ?>
                        <div class="material_document">
                            <h5>- Supporting Documents / Certification :</h5>
                            <?php
while (have_rows('files', $material_id)):
	the_row();
	$file = get_sub_field('file_upload');
	if ($file):
	?>
	                                   <p><a href="<?php echo $file['url']; ?>" class="link-down" target="_blank"><?php echo $file['title']; ?></a>
	                                       <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-download" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="svg-inline--fa fa-file-download fa-w-12 fa-5x"><path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z" class=""></path></svg></p>
	                                       <?php
endif;
endwhile;
?>
                           </div>
                       <?php endif;?>
                   </div><!-- .summary -->
               </div>

               <section class="related">
                <?php
echo do_shortcode('[show_relate_product_material material_id="' . $material_id . '"]');
?>
            </section>


        </div>

    </main><!--.main-shop-->
</div><!--.row-->
</div>
<?php
get_footer();
