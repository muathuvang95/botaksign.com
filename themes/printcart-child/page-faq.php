<?php
get_header();
wp_enqueue_style('bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), NBT_VER);
wp_enqueue_script('bootsrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js', array('jquery'), NBT_VER, true);
?>
    <style>
        .custom-tab-faq {
            width: 100%;
            border-bottom: 2px solid #ccc;
            padding-bottom: 15px;
        }

        .custom-tab-faq li {
            list-style: none;
            float: left;
            width: calc(100% / 5);
            text-align: center;
        }

        .custom-tab-faq li span {
            display: block;
            font-size: 25px;
            vertical-align: bottom;
        }

        .custom-tab-faq li.active:before {
            content: '';
            width: 15px;
            border-bottom: 1px solid #ccc;
        }

        .custom-tab-faq li.active:after {
            border: solid #ccc;
            content: '';
            transform: rotate(-135deg);
            display: inline-block;
            padding: 8px;
            border-width: 0 2px 2px 0;
            position: relative;
            top: 31px;
            background: #fff;
        }

        .custom-tab-faq a {
            text-decoration: none;
            color: #000;
        }

        .custom-tab-faq li.active a {
            text-underline-position: under;
        }

        .custom-tab-faq li.active span, .custom-tab-faq a:hover {
            color: rgb(40, 196, 117);
        }

        .tab-content {
            width: 100%;
            margin-top: 15px;
        }

        .tab-content .content-tab-faq {
            display: none;
        }

        .tab-content .content-tab-faq.active {
            display: block;
        }

        .tab-content .content-tab-faq h5 {
            font-size: 25px;
            font-weight: bold;
            color: #28c475;
        }

        .tab-content .content-tab-faq a.link-cat-term-faq {
            font-size: 17px;
            color: #000;
            margin: 7px 0;
            display: block;
            text-decoration: unset;
        }

        .tab-content .col-faq {
            margin-bottom: 30px;
        }

        @media only screen and (max-width: 600px) {
            .custom-tab-faq {
                padding-left: 0px;
            }
        }
    </style>
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
if (isset($page_cover) && !empty($page_cover)) { ?>
    <div class="page-cover-header" <?php printf('style%s', '="background-image: url(' . esc_url($page_cover) . '); height: ' . esc_attr($height) . 'px;"'); ?>>
        <div class="page-cover-wrap">
            <div class="page-cover-block">
                <h1><?php echo esc_attr($heading_title); ?></h1>
                <div><?php echo do_shortcode('[ultimate-faq-search]'); ?></div>
            </div>
        </div>
    </div>
<?php } ?>

    <div class="nb-page-title-wrap single-breadcrum">
        <div class="container">
            <nav class="woocommerce-breadcrumb" itemprop="breadcrumb"><a href="<?php echo home_url(); ?>">Home</a><span>/</span><a
                        href="<?php echo home_url('/guides'); ?>">Guides</a><span>/</span><a href="#">FAQ</a></nav>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <?php
            $index_active = get_field('default_tab_index_number', 'option');
            if (have_rows('faq_tab', 'option')): ?>
                <ul class="custom-tab-faq">
                    <?php
                    $d = 1;
                    while (have_rows('faq_tab', 'option')): the_row();
                        $image = get_sub_field('icon');
                        $title = get_sub_field('title');
                        ?>
                        <li class="<?php echo($index_active == $d ? 'active' : ''); ?>" rel="<?php echo $d; ?>">
                            <a href="javascript:void(0);">
                                <img src="<?php echo $image['url']; ?>" height="40"/>
                                <span><?php echo $title; ?></span>
                            </a>
                        </li>
                        <?php
                        $d++;
                    endwhile;
                    ?>
                </ul>
                <div class="tab-content" rel="<?php echo $index_active; ?>">
                    <?php
                    $d = 1;
                    while (have_rows('faq_tab', 'option')): the_row();
                        $terms = get_sub_field('choose_categories');
                        ?>
                        <div id="faq-content-<?php echo $d; ?>"
                             class="content-tab-faq <?php echo($index_active == $d ? 'active' : ''); ?>">
                            <ul class="row">
                                <?php
                                foreach ($terms as $term):
                                    $top_level_terms = get_terms(array(
                                        'taxonomy' => 'ufaq-category',
                                        'parent' => $term->term_id,
                                        'hide_empty' => false,
                                    ));
                                    ?>
                                    <div class="col-faq col-md-4 col-sm-6 col-6">
                                        <h5><?php echo esc_html($term->name); ?></h5>
                                        <?php foreach ($top_level_terms as $top_level_term) { ?>
                                            <a href="<?php echo esc_url(get_term_link($top_level_term)); ?>"
                                               class="link-cat-term-faq"><?php echo esc_html($top_level_term->name); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php
                        $d++;
                    endwhile;
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        jQuery(function ($) {
            jQuery('body').on('click', '.custom-tab-faq li', function (event) {
                $('.custom-tab-faq li, .tab-content .content-tab-faq').removeClass('active');
                $(this).addClass('active');
                $('.tab-content #faq-content-' + $(this).attr('rel')).addClass('active');
            });
        });
    </script>
<?php get_footer(); ?>