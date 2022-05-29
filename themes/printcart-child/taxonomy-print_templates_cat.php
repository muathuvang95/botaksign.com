<?php
get_header();
global $paged;
$term_current = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
?>
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

        <div class="container" >
            <nav class="woocommerce-breadcrumb" itemprop="breadcrumb"><a href="<?php echo home_url(); ?>">Home</a><span>/</span><a href="<?php echo home_url(); ?>/guides/">Guides</a><span>/</span><a href="#">Print Template</a></nav>
            <div id="print-breadcrumb">
              <?php yoast_breadcrumb('<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">', '</nav>');?>
          </div>

      </div>
  </div>
  <div class="infographic">
    <div class="container" >
        <?php echo $term_current->description; ?>
    </div>
</div>
<?php }?>
<div class="container">
    <div class="row">
        <main class="shop-main three-columns left-images split-reviews-form horizontal-tabs" role="main">
            <h2><?php echo $term_current->name; ?></h2>
            <h1>Templates</h1>
            <div class="products row grid-type">
                <?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array('post_type' => 'print_templates', 'posts_per_page' => 12, 'paged' => $paged,
	'tax_query' => array(
		array(
			'taxonomy' => 'print_templates_cat',
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
	                       <div class="pt-product-meta wrap-custom-print-template">
	                           <h4 class="product-title"><?php the_title();?></h4>
	                           <div class="des"><?php echo get_field('short_description', get_the_ID()); ?></div>
	                           <span class="code"><?php echo get_field('material_code', get_the_ID()); ?></span>
	                           <?php if (get_field('link', get_the_ID()) != '') {?>
	                               <a href="<?php echo get_field('link', get_the_ID()); ?>" target="_blank" class="link-pt">
	                                   <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
	                                   width="20.000000pt" height="20.000000pt" viewBox="0 0 117.000000 105.000000"
	                                   preserveAspectRatio="xMidYMid meet">
	                                   <g transform="translate(0.000000,105.000000) scale(0.100000,-0.100000)"
	                                   fill="#000000" stroke="none">
	                                   <path d="M795 987 c-46 -9 -60 -20 -185 -146 -138 -139 -160 -173 -160 -247 0
	                                   -82 63 -187 108 -182 40 5 51 51 21 91 -18 25 -24 45 -24 90 l0 57 123 122
	                                   122 123 60 0 c54 0 63 -3 92 -33 30 -29 33 -38 33 -92 l0 -60 -63 -63 c-70
	                                   -71 -79 -105 -34 -135 23 -15 26 -13 100 64 54 56 81 92 90 122 26 90 7 163
	                                   -62 233 -38 38 -56 48 -103 58 -31 6 -59 10 -62 10 -3 -1 -28 -6 -56 -12z"/>
	                                   <path d="M650 624 c-14 -35 -13 -37 12 -70 18 -22 23 -41 23 -86 l0 -58 -123
	                                   -123 -122 -122 -60 0 c-54 0 -63 3 -92 33 -30 29 -33 38 -33 92 l0 60 63 63
	                                   c65 65 78 101 47 127 -26 21 -61 2 -127 -69 -85 -93 -106 -164 -74 -256 20
	                                   -58 94 -128 150 -143 118 -31 153 -15 316 148 138 138 160 172 160 246 0 115
	                                   -110 239 -140 158z"/>
	                               </g>
	                           </svg>
	                       </a>
	                   <?php }?>
	                   <?php if (get_field('file_upload', get_the_ID()) != '') {?>
	                       <a href="<?php echo get_field('file_upload', get_the_ID()); ?>" target="_blank" class="link-file-upload">
	                           <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
	                           width="26.000000pt" height="25.000000pt" viewBox="0 0 126.000000 105.000000"
	                           preserveAspectRatio="xMidYMid meet">
	                           <g transform="translate(0.000000,105.000000) scale(0.100000,-0.100000)"
	                           fill="#000000" stroke="none">
	                           <path d="M567 944 c-4 -4 -7 -58 -7 -121 l0 -113 -84 0 c-47 0 -87 -4 -90 -9
	                           -8 -13 212 -226 234 -226 22 0 242 213 234 226 -3 5 -43 9 -89 9 l-84 0 -3
	                           118 -3 117 -50 3 c-28 2 -54 0 -58 -4z"/>
	                           <path d="M258 508 c-35 -55 -119 -239 -116 -255 3 -17 31 -18 478 -18 467 0
	                           475 0 478 20 1 11 -26 76 -60 145 l-63 125 -68 3 -68 3 -109 -106 c-59 -58
	                           -112 -105 -117 -105 -4 0 -55 47 -113 105 l-105 105 -61 0 c-53 0 -64 -3 -76
	                           -22z"/>
	                           <path d="M144 155 c-4 -8 -4 -22 0 -30 5 -13 64 -15 478 -13 l473 3 0 25 0 25
	                           -473 3 c-414 2 -473 0 -478 -13z"/>
	                       </g>
	                   </svg>
	               </a>
	           <?php }?>
	       </div>

	       <?php
endwhile;
$total_pages = $myQuery->max_num_pages;

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
wp_reset_postdata();
?>
<?php endif;?>
</div>
</main>
<aside id="secondary" class="widget-area" role="complementary"><div class="sidebar-wrapper">
    <div id="printshop_pcat_widget-3" class="widget widget_printshop_pcat_widget">
        <h3 class="widget-title">Templates</h3>
        <ul class="product_categories">
            <?php
$terms = get_terms(array(
	'taxonomy' => 'print_templates_cat',
	'hide_empty' => true,
));
foreach ($terms as $term) {
	?>
               <li><a href="<?php echo get_term_link($term->slug, 'print_templates_cat'); ?>"><?php echo $term->name; ?></a><span class="pc-count"><?php echo $term->count; ?></span></li>
           <?php }?>
       </ul>
   </div>
</div>
</aside>
</div>
</div>
<?php
get_footer();
