<?php
get_header();
$term_current = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
?>
<style type="text/css">
    .archive.tax-matealize_cat #secondary {
        -webkit-box-flex: 0;
        -ms-flex: 0 0 calc(100% - 75%);
        flex: 0 0 calc(100% - 75%);
        max-width: calc(100% - 75%);
    }
    .archive.tax-matealize_cat .shop-main .pt-product-meta {
        margin: 0px;
    padding: 10px 15px !important;
    }
    @media only screen and (min-width: 1024px) {
        .archive.tax-matealize_cat .shop-main {
            -webkit-box-flex: 0;
            -ms-flex: 0 0 75%;
            flex: 0 0 75%;
            max-width: 75%;
        }
    }
    @media only screen and (max-width: 991px) {
        .archive.tax-matealize_cat #secondary {
            display: none;
        }
    }
</style>
<?php if (function_exists('yoast_breadcrumb')) {?>

<?php 
    $_post = get_queried_object();

    if (isset($_post->ID)) {
        $page_cover = get_post_meta($_post->ID, 'page_cover', true);
        $height = get_post_meta($_post->ID, 'page_height', true);
        $heading_title = $_post->post_title;
    } else if (isset($_post->term_id)) {
        $page_cover = get_term_meta($_post->term_id, 'nbcore_blog_archive_cover', true);
        $height = get_term_meta($_post->term_id, 'nbcore_blog_archive_height', true);
        $heading_title = $_post->name;
    }

    if (isset($height) && !$height) {
        $height = 300;
    }
    if (isset($page_cover) && !empty($page_cover)) {
    ?>
    <div class="page-cover-header" <?php printf('style%s', '="background-image: url(' . esc_url($page_cover) . '); height: ' . esc_attr($height) . 'px;"'); ?>>
        <div class="page-cover-wrap">
            <div class="page-cover-block">
                <h1><?php echo esc_attr($heading_title); ?></h1>
                <?php
                    if (function_exists('woocommerce_breadcrumb')) {
                        if (printcart_get_options('nbcore_wc_breadcrumb')) {
                            woocommerce_breadcrumb();
                        }
                    }
                ?>
            </div>
        </div>
    </div>
<?php } ?>


    <div class="nb-page-title-wrap single-breadcrum">
        <div class="container">
            <nav class="woocommerce-breadcrumb" itemprop="breadcrumb"><a href="<?php echo home_url(); ?>">Home</a><span>/</span><a href="<?php echo home_url(); ?>/guides/">Guides</a><span>/</span><a href="#">Material Library</a></nav>
            <div id="print-breadcrumb">
                <?php yoast_breadcrumb('<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">', '</nav>');?>
            </div>
        </div>
    </div>
<?php }?>
<div class="container">
    <div class="row">

        <aside id="secondary" class="widget-area" role="complementary"><div class="sidebar-wrapper">
            <div id="printshop_pcat_widget-3" class="widget widget_printshop_pcat_widget">
                <h3 class="widget-title">Materials</h3>
                <ul class="product_categories">
                    <?php
                        $terms = get_terms(array(
                                'taxonomy' => 'matealize_cat',
                                'hide_empty' => true,
                                'pad_counts' => true,
                        ));
                        foreach ($terms as $term) {
                    ?>
                       <li><a href="<?php echo get_term_link($term->slug, 'matealize_cat'); ?>"><?php echo $term->name; ?></a><span class="pc-count"><?php echo $term->count; ?></span></li>
                   <?php }?>
               </ul>
           </div>
       </div>
   </aside>

   <main class="shop-main three-columns left-images split-reviews-form horizontal-tabs" role="main">
    <h2><?php echo $term_current->name != null ? $term_current->name : 'All Materials'; ?></h2>
    <div class="products row grid-type">
        <?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array('post_type' => 'materials', 'posts_per_page' => 12, 'paged' => $paged,
	'tax_query' => array(
		array(
			'taxonomy' => 'matealize_cat',
			'field' => 'term_id',
			'terms' => $term_current->term_id,
		),
	));
$myQuery = new WP_Query($args);
?>
        <?php if ($myQuery->have_posts()): ?>
            <?php
while ($myQuery->have_posts()):
	$myQuery->the_post();
	$gallery = get_field('images', get_the_ID());
	$thumb = (count($gallery) > 0 ? '<img src="' . $gallery[0] . '" alt="' . get_the_title() . '" />' : '');
	?>
								               <div class="pt-product-meta product">
								                 <div class="product-image">
								                     <a href="<?php the_permalink();?>"><?php echo $thumb; ?></a>
								                 </div>
								                 <h4 class="product-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h4>
								                 <span class="code"><?php echo get_field('material_code', get_the_ID()); ?></span>
								             </div>

								             <?php
endwhile;
$total_pages = $myQuery->max_num_pages;

if ($total_pages > 1) {

	$current_page = max(1, get_query_var('paged'));

	echo '<div class="paginate-link"><div class="paginate-container">' . paginate_links(array(
		'base' => get_pagenum_link(1) . '%_%',
		'format' => '/page/%#%',
		'current' => $current_page,
		'total' => $total_pages,
		'prev_text' => __('« '),
		'next_text' => __(' »'),
	)) . '</div></div>';
}
wp_reset_postdata();
?>
   <?php endif;?>
</div>
</main>

</div>
</div>
<?php
get_footer();
