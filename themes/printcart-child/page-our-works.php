<?php
/**

 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *  
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package nbcore
 */

$taxonomy = "works-cat";
$term_id = "";
global $wp;
if ("" != get_query_var('c')) {
    $term_id = get_query_var('c');
}
$term_current = get_term_by('id', $term_id, $taxonomy);

get_header();
wp_enqueue_style('our-work', get_stylesheet_directory_uri() . '/css/our-work.css', array(), NBT_VER);
wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), NBT_VER);
wp_enqueue_style('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css', array(), NBT_VER);
wp_enqueue_script('bootsrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), NBT_VER, true);
wp_enqueue_script('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), NBT_VER, true);      
?>

<?php if (function_exists('yoast_breadcrumb')) { ?>

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
            <nav class="woocommerce-breadcrumb" itemprop="breadcrumb"><a href="<?php echo home_url(); ?>">Home</a><span>/</span><a href="<?php echo home_url('/about-us'); ?>">About Us</a><span>/</span><a href="#">Our Works</a></nav>
            <div class="workfilter">
                <select class="filter" onchange="location = this.value;">
                    <option value="<?php echo get_home_url() . '/our-works';?>">- Select category -</option>
                    <?php
                        $terms = get_terms(array(
                            'taxonomy' => $taxonomy,
                            'hide_empty' => true,
                        ));
                        foreach ($terms as $term) {
                            $url = get_home_url() . '/our-works/c/' . $term->term_id;
                    ?>
                        <option value="<?php echo $url; ?>" <?php echo is_object($term_current) ? ($term->term_id === $term_current->term_id ? "selected" : "") : "" ?>>
                            <?php echo $term->name; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
<?php } ?>

<div class="container">
    <div class="row">
        <main class="shop-main three-columns left-images split-reviews-form horizontal-tabs page-our-works" role="main">
            <h2><?php echo is_object($term_current) ? $term_current->name : "Our works";?></h2>
            <div class="products row grid-type">
                <?php
                    $current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $args = array('post_type' => 'works', 'posts_per_page' => 9, 'paged' => $current_page);
                    if (is_object($term_current)) {
                        $args['tax_query'] = array(
                            array (
                                'taxonomy'  => $taxonomy,
                                'field'     => 'term_id',
                                'terms'     => $term_current->term_id,
                            )
                        );
                    }
                    $myQuery = new WP_Query($args);
                ?>
                    <?php if ($myQuery->have_posts()): ?>
                        <?php while ($myQuery->have_posts()):
                            $w = $myQuery->the_post();
                            $gallery = get_field('images', get_the_ID());
                            $thumb = (count($gallery) > 0 ? '<img src="' . $gallery[0] . '" alt="' . get_the_title() . '" />' : '');
                        ?>
                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-6 col-320-12 work-block" data-toggle="modal" data-target="#modal-work-<?php echo get_the_ID(); ?>">
                            <div class="work-block-container">
                                <div class="product-image">
                                    <a>
                                        <?php echo $thumb; ?>
                                    </a>
                                </div>
                                <div class="product-description">
                                    <div class="product-description-container">
                                        <h4 class="product-title"><?php echo get_the_title(); ?></h4>
                                        <p class="product-content"><?php echo get_the_content(); ?></p>
                                    </div>
                                </div>
                                <div class="product-title-bottom">
                                    <h4 class="product-title"><?php echo get_the_title(); ?></h4>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade modal-work" id="modal-work-<?php echo get_the_ID(); ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <div class="work-imgs owl-carousel">
                                            <?php foreach ($gallery as $img_url): ?>
                                                <div class="item">
                                                    <img src="<?php echo $img_url; ?>"/>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="work-info">
                                            <h4 class="work-title"><?php echo get_the_title(); ?></h4>
                                            <p class="work-content"><?php echo get_the_content(); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        endwhile;
                        $total_pages = $myQuery->max_num_pages;

                        if ($total_pages > 1) {
                            $current_page = max(1, get_query_var('paged'));
                            $args_page = array(
                                'base' => get_pagenum_link(1) . '%_%',
                                'format' => 'page/%#%',
                                'current' => $current_page,
                                'total' => $total_pages,
                                'prev_text' => __('« '),
                                'next_text' => __(' »'),
                            );
                            echo '<div class="paginate-link"><div class="paginate-container">' . paginate_links($args_page) . '</div></div>';
                        }
                        wp_reset_postdata();
                    ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
    jQuery(function($) {
        $(".work-imgs").owlCarousel({
            items: 1,
            rewind: false,
            autoplay: false,
            center: true,
            autoplayHoverPause: true,
            autoplayTimeout: 5000,
            smartSpeed: 250, //slide speed smooth
            dots: true,
            loop: true,
            nav: false,
            margin: 20,
            center: false,
        });
        $(".work-block").hover(
            function () {
                $(this).addClass("hover");
            }, 
            function () {
                $(this).removeClass("hover");
            }
        );
    })
</script>
<?php get_footer(); ?>