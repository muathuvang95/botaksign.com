<?php
get_header();
wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), NBT_VER);
wp_enqueue_script('bootsrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), NBT_VER, true);

$banner_img = 'https://demo2.cmsmart.net/printcart_tf/printcart-business/wp-content/uploads/2018/08/blog-cover.jpg';
if (get_theme_mod('botaksign_setting_banner_faq_control') != '') {
	$banner_img = get_theme_mod('botaksign_setting_banner_faq_control');
}
$term_current = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$name_tab = $term_current->name;
$ancestors = get_ancestors($term_current->term_id, get_query_var('taxonomy'));
$tab_active = 0;
$cat_in_tab = array();
if (count($ancestors) > 0) {
	while (have_rows('faq_tab', 'option')): the_row();
		$title = get_sub_field('title');
		$cats = get_sub_field('choose_categories');
		foreach ($cats as $cat):
			if ($cat->term_id == $ancestors[0]) {
				$tab_active = $ancestors[0];
				$cat_in_tab = $cats;
				$name_tab = $title;
				break;
			}
		endforeach;
	endwhile;
}
?>

<style>
	.ufaq-faq-body:not(.ewd-ufaq-hidden) {
		animation: opac 0.8s;
	}
	.tax-ufaq-category .widget, .tax-ufaq-category .widget .widget-title {
		margin-bottom: 0px;
		cursor: pointer;
	}
	.tax-ufaq-category .widget ul.product_categories {
		display: none;
	}
	.tax-ufaq-category .widget.active ul.product_categories {
		display: block;
	}
	.nav-faq {
		font-size: 20px;
		margin: 0 0 15px;
		padding: 0 0 15px;
		border-bottom: 2px #d7d7d7 solid;
	}
	.nav-faq .lv1 {
		font-size: 40px;
		color: #28c475;
	}
	#ufaq-faq-list {
		width: 100%;
	}
	.ufaq-faq-div, .ufaq-faq-body {
		padding: 12px 10px;
	}
	.nav-faq .box-list {
		position: absolute;
		background: #fff;
		padding: 10px;
		border: 1px solid #ccc;
		display: none;
		z-index: 9999;
	}
	.nav-faq .box-list ul {
		list-style: none;
		padding-left: 0px;
	}

	@media only screen and (max-width: 600px) {
		.widget-area .sidebar-wrapper {
			display: none;
		}
		.nav-faq .level:after {
			content: '';
			border: solid black;
			border-width: 0 3px 3px 0;
			display: inline-block;
			padding: 3px;
			transform: rotate(45deg);
			-webkit-transform: rotate(45deg);
			position: relative;
			left: 10px;
			bottom: 3px;
			margin-right: 10px;
		}
		.nav-faq .level {
			border-bottom: 1px solid #ccc;
		}
		.nav-faq .lv2 {
			padding-bottom: 3px;
		}
	}
</style>

<div class="page-cover-header" style="background-image: url(<?php echo $banner_img; ?>); height: 300px;">
	<div class="page-cover-wrap">
		<div class="page-cover-block">
			<h1><?php echo $name_tab; ?></h1>
			<div><?php echo do_shortcode('[ultimate-faq-search]'); ?></div>
		</div>
	</div>
</div>

<div class="nb-page-title-wrap single-breadcrum">
	<div class="container">
		<nav class="woocommerce-breadcrumb" itemprop="breadcrumb"><a href="<?php echo home_url(); ?>">Home</a><span>/</span><a href="<?php echo home_url('/guides'); ?>">Guides</a><span>/</span><a href="<?php echo home_url('/faq'); ?>">FAQ</a><span>/</span><a href="#"><?php echo $name_tab; ?></a></nav>
	</div>
</div>

<div class="container">
	<div class="row">

		<aside class="widget-area col-md-3" role="complementary">
			<div class="sidebar-wrapper">
				<?php
if (count($cat_in_tab) > 0) {
	$d = 0;
	foreach ($cat_in_tab as $cat) {
		?>
						<div class="widget widget_printshop_pcat_widget <?php echo ($tab_active != 0 && $tab_active == $cat->term_id ? 'active' : ''); ?>">
							<h3 class="widget-title" rel="<?php echo $d; ?>"><?php echo esc_html($cat->name); ?></h3>
							<ul class="product_categories">
								<?php
$terms = get_terms(array(
			'taxonomy' => 'ufaq-category',
			'parent' => $cat->term_id,
			'hide_empty' => false,
		));
		foreach ($terms as $term) {
			?>
									<li class="<?php echo ($term->term_id == $term_current->term_id ? 'active' : ''); ?>"><a href="<?php echo get_term_link($term->slug, 'ufaq-category'); ?>"><?php echo $term->name; ?></a></li>
								<?php }?>
							</ul>
						</div>
						<?php
$d++;
	}
}
?>
			</div>
		</aside>

		<main class="shop-main three-columns left-images split-reviews-form horizontal-tabs col-md-9" role="main">
			<div class="nav-faq">
				<strong class="level lv1"></strong> <span style="padding: 0 5px;">/</span> <span class="level lv2"></span>
				<div class="box-list"></div>
			</div>
			<div class="products row grid-type">
				<?php
echo do_shortcode('[ultimate-faqs include_category="' . $term_current->slug . '"]');
?>
			</div>
		</main>

	</div>
</div>

<script>
	jQuery(document).ready(function($)
	{
		$('body').on('click', '.sidebar-wrapper .widget_printshop_pcat_widget .widget-title', function (event) {
			$('.tax-ufaq-category .sidebar-wrapper .widget_printshop_pcat_widget').removeClass('active');
			$(this).parent('.widget_printshop_pcat_widget').addClass('active');
		});
	});
</script>
<?php
get_footer();
