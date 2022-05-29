<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package nbcore
 */

function printcart_get_header()
{
    $header_style = printcart_get_options('nbcore_header_style');
    get_template_part('template-parts/headers/' . $header_style);
}

function printcart_main_nav()
{
    $admin_url = get_admin_url() . 'customize.php?url=' . get_permalink() . '&autofocus%5Bsection%5D=menu_locations';

    if (has_nav_menu('primary')) {
        echo '<nav id="site-navigation" class="main-navigation" role="navigation">';
        echo '<button class="mobile-toggle-button icon-menu"></button>';
        echo '<div class="menu-main-menu-wrap">';
        echo '<div class="menu-main-menu-title"><h3>' . esc_html__('Navigation', 'printcart') . '</h3><span class="icon-cancel-circle"></span></div>';
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class' => 'nb-navbar',
            'link_before' => '<span>',
            'link_after' => '</span>',
        ));

        echo '</div></nav>';
    } else {
        echo '<li><a href="' . $admin_url . '">' . esc_html__('Assign a menu here', 'printcart') . '</a></li>';
    }
}

function printcart_sub_menu()
{
    $admin_url = get_admin_url() . 'customize.php?url=' . get_permalink() . '&autofocus%5Bsection%5D=menu_locations';

    if (has_nav_menu('header-sub')) {
        echo '<nav class="sub-navigation" role="navigation">';

        wp_nav_menu(array(
            'theme_location' => 'header-sub',
            'menu_class' => 'nb-header-sub-menu',
            'link_before' => '<span>',
            'link_after' => '</span>',
        ));

        echo '</nav>';
    } else {
        echo '<a href="' . $admin_url . '">' . esc_html__('Assign a menu for the Sub Menu ', 'printcart') . '</a>';
    }
}

function printcart_get_nav_mobile()
{
    if (has_nav_menu('primary')) {
        echo '<nav class="main-mobile-navigation" role="navigation">';

        echo '<button class="mobile-toggle-button icon-menu"></button>';

        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class' => 'nb-mobile-navbar',
            'link_before' => '<span>',
            'link_after' => '</span>',
        ));

        echo '</nav>';
    }
}

function printcart_header_class()
{
    $classes = array();

    $classes['header_style'] = printcart_get_options('nbcore_header_style');

    if (printcart_get_options('nbcore_header_fixed')) {
        $classes['header_fixed'] = 'fixed';
    }

    echo implode(' ', $classes);
}

function printcart_header_woo_section($account = TRUE)
{
    $header_style = printcart_get_options('nbcore_header_style');
    if(class_exists('WooCommerce')):
       if ($account): ?>
        <?php if (is_user_logged_in()):
         $current_user = wp_get_current_user();?>
         <div class="customer-action">
            <div class="top-title"><?php esc_html_e('My Account', 'printcart'); ?></div>
            <span class="display-name"><?php echo esc_attr($current_user->display_name);?></span>
            <div class="nb-account-dropdown">
                <?php wc_get_template('myaccount/navigation.php'); ?>
            </div>
        </div>
        <?php else: ?>
            <div class="customer-action">
                <div class="top-title"><?php esc_html_e('Hello Guest!', 'printcart');?></div>
                <div class="customer-links">
                    <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"
                       class="not-logged-in" title="<?php esc_attr_e('Login', 'printcart'); ?>"><?php esc_html_e('Login', 'printcart'); ?></a>
                    <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
                       <span>/</span>
                       <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" title="<?php esc_html_e('Register', 'printcart'); ?>"><?php esc_html_e('Register', 'printcart'); ?></a>
                    <?php endif; ?>
                   </div>
               </div>
           <?php endif; ?>
       <?php endif; ?>

       <div class="header-cart-wrap minicart-header">
        <div class="cart-wrapper">
            <span class="counter-number"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
            <div class="show-cart">
                <span class="text"><span class="df-text"><?php esc_html_e('Your Cart', 'printcart');?></span></span>
                <span class="price-wrapper"><span class="price"><?php echo WC()->cart->get_cart_total();?></span></span>
            </div>
        </div>
        <div class="mini-cart-section">
            <div class="mini-cart-container">
                <div class="mini-cart-wrap <?php echo esc_attr( 'cart-'.WC()->cart->get_cart_contents_count() );?>">
                    <?php wc_get_template( 'cart/mini-cart.php'); ?>
                </div>
            </div>
        </div>
    </div>


    <?php
endif;
}

function printcart_social_section($text = false)
{
    $facebook = printcart_get_options('nbcore_header_facebook');
    $twitter = printcart_get_options('nbcore_header_twitter');
    $linkedin = printcart_get_options('nbcore_header_linkedin');
    $instagram = printcart_get_options('nbcore_header_instagram');
    $blog = printcart_get_options('nbcore_header_blog');
    $pinterest = printcart_get_options('nbcore_header_pinterest');
    $ggplus = printcart_get_options('nbcore_header_ggplus');
    if ($facebook || $twitter || $linkedin || $instagram || $blog || $pinterest || $ggplus) {
        echo '<ul class="social-section">';
        if ($facebook) {
            echo '<li class="social-item"><a href="' . esc_url($facebook) . '"><i class="fa fa-facebook"></i></a></li>';
        }
        if ($twitter) {
            echo '<li class="social-item"><a href="' . esc_url($twitter) . '"><i class="fa fa-twitter"></i></a></li>';
        }
        if ($linkedin) {
            echo '<li class="social-item"><a href="' . esc_url($linkedin) . '"><i class="fa fa-linkedin"></i></a></li>';
        }
        if ($pinterest) {
            echo '<li class="social-item"><a href="' . esc_url($pinterest) . '"><i class="fa fa-pinterest-p"></i></a></li>';
        }
        if ($ggplus) {
            echo '<li class="social-item"><a href="' . esc_url($ggplus) . '"><i class="fa fa-google-plus"></i></a></li>';
        }
        if ($instagram) {
            echo '<li class="social-item"><a href="' . esc_url($instagram) . '"><i class="fa fa-instagram"></i></a></li>';
        }
        if ($blog) {
            echo '<li class="social-item"><a href="' . esc_url($blog) . '"><i class="fa fa-rss"></i></a></li>';
        }
        echo '</ul>';
    }
}

function printcart_search_section($dropdown = true)
{
    echo '<div class="header-search-wrap">';
    if ($dropdown) {
        echo '<span class="icon-header-search"><i class="icon-search-1"></i></span>';
    }
    get_search_form();
    echo '</div>';
}

function printcart_get_site_logo()
{
    $logo = printcart_get_options('nbcore_logo_upload');
    if ($logo) {
        $output = '<div class="main-logo img-logo">';
        $output .= '<a href="' . esc_url(home_url('/')) . '" title="' . get_bloginfo('description') . '">';
        $output .= '<img src="' . printcart_get_options('nbcore_logo_upload') . '" alt="' . esc_attr(get_bloginfo('name', 'display')) . '">';
        $output .= '</a>';
        $output .= '</div>';
    } else {
        $output = '<div class="main-logo img-logo" style="width: 130px;">';
        $output .= '<a href="' . esc_url(home_url('/')) . '" title="' . get_bloginfo('description') . '">';
        $output .= '<img src="' . get_template_directory_uri() . '/assets/netbase/images/logo_printshop.png" alt="' . esc_attr(get_bloginfo('name', 'display')) . '">';
        $output .= '</a>';
        $output .= '</div>';
    }
    print($output);
}

function printcart_get_footer_logo() {
    $logo = printcart_get_options('nbcore_footer_logo_upload');

    if ($logo) {
        $output = '<div class="footer-logo img-logo">';
        $output .= '<a href="' . esc_url(home_url('/')) . '" title="' . get_bloginfo('description') . '">';
        $output .= '<img src="' . printcart_get_options('nbcore_footer_logo_upload') . '" alt="' . esc_attr(get_bloginfo('name', 'display')) . '">';
        $output .= '</a>';
        $output .= '</div>';
    } else {
        $output = '<div class="footer-logo text-logo" style="display:none">';
        $output .= '<a href="' . esc_url(home_url('/')) . '" title="' . get_bloginfo('description') . '">';
        $output .= get_bloginfo('name');
        $output .= '</a>';
        $output .= '</div>';
    }
    print($output);
}

function printcart_featured_thumb()
{
    $blog_layout = printcart_get_options('nbcore_blog_archive_layout');
    
    if (has_post_thumbnail()):


        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'blog-home');
        if ( 'modern' === $blog_layout || is_search() ):
            ?>

            <div class="entry-image">
                <a href="<?php the_permalink(); ?>">
                    <?php
                    printf('<img src="%1$s" title="%2$s" />',
                        $thumb[0],
                        esc_attr(get_the_title())
                    );
                    ?>
                </a>
            </div>
        <?php else:
            $nbcore_blog_classic_columns = printcart_get_options('nbcore_blog_classic_columns');

            if( $nbcore_blog_classic_columns == 2 || $nbcore_blog_classic_columns == 3 ){
                $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'blog-home');
            }else{
                $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'blog-thumb');
            }
            ?>
            <div class="entry-image">
                <?php
                if(strpos($thumb[0] , 's3.ap-southeast-1.amazonaws.com') > 0 ) {
                    $size_image = getimagesize($thumb[0]);
                    $thumb[1] = $size_image['0'];
                    $thumb[2] = $size_image['1'];
                }
                printf('<img src="%1$s" title="%2$s" width="%3$s" height="%4$s" />',
                    $thumb[0],
                    esc_attr(get_the_title()),
                    $thumb[1],
                    $thumb[2]
                );
                ?>
                <div class="image-mask">
                    <a href="<?php the_permalink(); ?>"><span><?php esc_html_e('View post &rarr;', 'printcart'); ?></span></a>
                    <?php
                    $post = get_post();
                    $words = str_word_count(strip_tags($post->post_content));
                    $minutes = floor($words / 180);
                    if (1 < $minutes) {
                        $estimated_time = $minutes . ' minutes read';
                    } else {
                        $estimated_time = esc_html__('1 minute read', 'printcart');
                    }
                    echo '<div class="read-time"> ' . $estimated_time . '</div>';
                    ?>
                </div>

            </div>
        <?php endif;
    endif;
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function printcart_posted_on($mordern = false)
{
    $html = '';

    if (printcart_get_options('nbcore_blog_meta_date') ) {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf($time_string,
            esc_attr(get_the_date('c')),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date('c')),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            esc_html_x('%s', 'post date', 'printcart'), $time_string);

            if(printcart_get_options('nbcore_blog_archive_layout')=='blogs' || printcart_get_options('nbcore_blog_archive_layout')=='layout'){
                $html .= '<span class="posted-on">' . $posted_on . '</span>';
            }else{
                $html .= '<span class="posted-on"><i class="icon-calendar-empty"></i>' . $posted_on . '</span>';
            }
    };

    if (printcart_get_options('nbcore_blog_meta_author')) {
        $byline = sprintf(
            esc_html_x('%s', 'post author', 'printcart'),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
        );

        if(printcart_get_options('nbcore_blog_archive_layout')=='blogs' || printcart_get_options('nbcore_blog_archive_layout')=='layout'){
            $html .= '<span class="byline">By: ' . $byline . '</span>';
        }else{
            $html .= '<span class="byline"><i class="icon-user"></i>' . $byline . '</span>';
        }
    }

    if ('classic' !== printcart_get_options('nbcore_blog_archive_layout') && !$mordern ) {
        if (printcart_get_options('nbcore_blog_meta_read_time')) {
            $post = get_post();
            $words = str_word_count(strip_tags($post->post_content));
            $minutes = floor($words / 180);
            if (1 < $minutes) {
                $estimated_time = $minutes . ' minutes read';
            } else {
                $estimated_time = esc_html__('1 minute read', 'printcart');
            }

            $html .= '<span class="read-time"> ' . $estimated_time . '</span>';
        }
    }


    if ('' != $html) {
        $htmls = '<div class="entry-meta">' . $html ;

        if( get_comments_number() == '0' ) {
            $htmls .= '<span class="entry-comment">'.esc_html__( 'No Comments', 'printcart').'</span>';
        }else {
            $htmls .= '<span class="entry-comment">'.sprintf(esc_html__( '%d Comments', 'printcart' ), get_comments_number()).'</span>';
        }

        $htmls .= '</div>';


        print($htmls);
    }
}

function printcart_posted_date() {
    $html = '<div class="entry-meta">';

    if (printcart_get_options('nbcore_blog_meta_date')) {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if (get_the_time('U') !== get_the_modified_time('U')) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf($time_string,
            esc_attr(get_the_date('c')),
            esc_html(get_the_date()),
            esc_attr(get_the_modified_date('c')),
            esc_html(get_the_modified_date())
        );

        $posted_on = sprintf(
            esc_html_x('%s', 'post date', 'printcart'), $time_string);

            if(printcart_get_options('nbcore_blog_archive_layout')=='blogs' || printcart_get_options('nbcore_blog_archive_layout')=='layout'){
                $html .= '<span class="posted-on">' . $posted_on . '</span>';
            }else{
                $html .= '<span class="posted-on"><i class="icon-calendar-empty"></i>' . $posted_on . '</span>';
            }
    };

    if (!is_single()) {
        if (printcart_get_options('nbcore_blog_meta_author')) {
            $byline = sprintf(
                esc_html_x('%s', 'post author', 'printcart'),
                '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
            );

            $html .= '<span class="byline">' . esc_html__('by', 'printcart') .' '.$byline . '</span>';
        }
    }
    

    if( get_comments_number() == '0' ) {
        $html .= esc_html__( 'No Comments', 'printcart');
    }else {
        $html .= sprintf(esc_html__( '%d Comments', 'printcart' ), get_comments_number());
    }

    $html .= '</div>';

    print($html);

}

function printcart_get_categories()
{
    if (printcart_get_options('nbcore_blog_meta_category')):?>
        <div class="entry-cat">
            <?php echo get_the_category_list(', '); ?>
        </div>
    <?php endif;
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 * TODO entry-footer wrap div rearrange
 */
function printcart_get_tags()
{
    if (printcart_get_options('nbcore_blog_meta_tag')) {
        // Hide category and tag text for pages.
        if ('post' === get_post_type()) {
           
            /* translators: used between list items, there is a space after the comma */
            $tags_list = get_the_tag_list('', esc_html__(', ', 'printcart'));
            if ($tags_list) {
                echo '<div class="entry-tags">';
                    echo '<label>Tag:</label>';
                    printf('<span class="tags-links icon-tags">' . esc_html__('%1$s', 'printcart') . '</span>', $tags_list); // WPCS: XSS OK.
                echo '</div>';
            }
            
            
            if(printcart_get_options('nbcore_blog_single_show_social') && function_exists('nbcore_share_social')) {
                nbcore_share_social();
            }
        }
    }
}

function printcart_get_excerpt()
{
    echo '<p class="entry-summary">';
    $limit = printcart_get_options('nbcore_excerpt_length');
    $excerpt = wp_trim_words(get_the_excerpt(), $limit, ' [...]');
    echo esc_html($excerpt);
    echo '</p>';
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function printcart_categorized_blog()
{
    if (false === ($all_the_cool_cats = get_transient('nbcore_categories'))) {
        // Create an array of all the categories that are attached to posts.
        $all_the_cool_cats = get_categories(array(
            'fields' => 'ids',
            'hide_empty' => 1,
            // We only need to know if there is more than one category.
            'number' => 2,
        ));

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count($all_the_cool_cats);

        set_transient('nbcore_categories', $all_the_cool_cats);
    }

    if ($all_the_cool_cats > 1) {
        // This blog has more than 1 category so printcart_categorized_blog should return true.
        return true;
    } else {
        // This blog has only 1 category so printcart_categorized_blog should return false.
        return false;
    }
}

function printcart_paging_nav()
{
    // Don't print empty markup if there's only one page.
    if ($GLOBALS['wp_query']->max_num_pages < 2) {
        return;
    }

    $paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
    $pagenum_link = html_entity_decode(get_pagenum_link());
    $query_args = array();
    $url_parts = explode('?', $pagenum_link);

    if (isset ($url_parts[1])) {
        wp_parse_str($url_parts[1], $query_args);
    }

    $pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
    $pagenum_link = trailingslashit($pagenum_link) . '%_%';

    $format = $GLOBALS['wp_rewrite']->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
    $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit('page/%#%', 'paged') : '?paged=%#%';

    // Set up paginated links.
    $links = paginate_links(array(
        'nbcore' => $pagenum_link,
        'format' => $format,
        'total' => $GLOBALS['wp_query']->max_num_pages,
        'current' => $paged,
        'mid_size' => 1,
        'add_args' => array_map('urlencode', $query_args),
        'prev_text' => wp_kses(__('<i class=\'icon-left-arrow\'></i>', 'printcart'), array('i' => array('class' => array()))),
        'next_text' => wp_kses(__('<i class=\'icon-arrow-right\'></i>', 'printcart'), array('i' => array('class' => array()))),
    ));

    if ($links) :

        ?>
        <nav class="navigation paging-navigation <?php echo printcart_get_options('pagination_style'); ?>"
         role="navigation">
         <div class="pagination loop-pagination">
            <?php echo wp_kses($links, array(
                'a' => array(
                    'href' => array(),
                    'class' => array()
                ),
                'i' => array(
                    'class' => array()
                ),
                'span' => array(
                    'class' => array()
                )
            )); ?>
        </div><!--/ .pagination -->
    </nav><!--/ .navigation -->
    <?php
endif;
}

function printcart_page_title()
{
    if (printcart_get_options('show_title_section')) {
        $_post = get_queried_object();

        if(isset($_post->ID)){
            $page_cover = get_post_meta($_post->ID, 'page_cover', true);
            $height = get_post_meta($_post->ID, 'page_height', true);
            $heading_title = $_post->post_title;
        }else if( isset($_post->term_id) ) {
            $page_cover = get_term_meta($_post->term_id, 'nbcore_blog_archive_cover', true);
            $height = get_term_meta($_post->term_id, 'nbcore_blog_archive_height', true);
            $heading_title = $_post->name;
        }

        if( isset($height) && ! $height ) {
            $height = 300;
        }
        if( isset($page_cover) && !empty($page_cover) ) {?>

            <div class="page-cover-header" <?php printf('style%s', '="background-image: url('. esc_url($page_cover) .'); height: '. esc_attr($height) .'px;"');?>>
                <div class="page-cover-wrap">
                    <div class="page-cover-block">
                        <h1><?php echo esc_attr( $heading_title );?></h1>
                        <?php
                        if (function_exists('woocommerce_breadcrumb')) {
                            if (printcart_get_options('nbcore_wc_breadcrumb')) {
                                yoast_breadcrumb();
                            }
                        }?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            if (is_home() || is_front_page()) {
                if (printcart_get_options('home_page_title_section')) {
                    echo '<div class="nb-page-title-wrap"><div class="container"><div class="nb-page-title"><h1>';
                    esc_html_e('Home', 'printcart');
                    echo '</h1></div></div></div>';
                }

            } else {

                echo '<div class="nb-page-title-wrap"><div class="container"><div class="nb-page-title">';

                if (function_exists('woocommerce_breadcrumb')) {
                    if (printcart_get_options('nbcore_wc_breadcrumb')) {
                        yoast_breadcrumb();
                    }
                }

                echo '</div></div></div>';
            }
        }  
    }
}

function printcart_single_title($_post){
    $page_cover = printcart_get_options('nbcore_blog_single_cover');
    $heading_title = $_post->post_title;

    if($page_cover) {
        ?>
        <div class="page-cover-header" <?php printf('style%s', '="background-image: url('. esc_url($page_cover) .');"');?>>
            <div class="page-cover-wrap">
                <div class="page-cover-block">
                    <h1><?php echo esc_attr( $heading_title );?></h1>
                    <?php
                    if (function_exists('woocommerce_breadcrumb')) {
                        if (printcart_get_options('nbcore_wc_breadcrumb')) {
                            woocommerce_breadcrumb();
                        }
                    }?>
                </div>
            </div>
        </div>
        <?php
    }    
}

function printcart_default_options()
{
    return array(
        'nbcore_blog_archive_layout' => 'classic',
        'nbcore_blog_sidebar' => 'right-sidebar',
        'nbcore_excerpt_only' => false,
        'nbcore_excerpt_length' => '35',
        'nbcore_blog_single_sidebar' => 'right-sidebar',
        'nbcore_color_scheme' => 'scheme_1',
        'nbcore_primary_color' => '#4285f4',
        'nbcore_secondary_color' => '#fff',
        'nbcore_background_color' => '#ffffff',
        'nbcore_inner_background' => '#eee',
        'nbcore_heading_color' => '#444',
        'nbcore_body_color' => '#676c77',
        'nbcore_link_color' => '#4285f4',
        'nbcore_link_hover_color' => '#888888',
        'nbcore_divider_color' => '#d7d7d7',
        'nbcore_header_style' => 'header-1',
        'nbcore_logo_upload' => '',
        'nbcore_footer_logo_upload' => '',
        'nbcore_logo_width' => '200',
        'nbcore_header_fixed' => false,
        'nbcore_header_top_language' => true,
        'nbcore_header_top_currency' => true,
        'nbcore_header_top_login_link' => true,
        'nbcore_header_facebook' => '#',
        'nbcore_header_twitter' => '#',
        'nbcore_header_linkedin' => '#',
        'nbcore_header_pinterest' => '#',
        'nbcore_header_ggplus' => '#',
        'nbcore_header_instagram' => '#',
        'nbcore_header_blog' => '#',
        'nbcore_header_top_border' => '#eeeeee',
        'header_bgcolor' => '#ffffff',
        'nbcore_blog_width' => '75',
        'nbcore_blog_meta_date' => true,
        'sidebar_style' => 'sidebar-style-1',
        'nbcore_blog_sidebar_style' => 'sidebar-style-1',
        'nbcore_blog_meta_read_time' => true,
        'nbcore_blog_meta_author' => true,
        'nbcore_blog_meta_category' => true,
        'nbcore_blog_meta_tag' => true,
        'nbcore_blog_sticky_sidebar' => false,
        'nbcore_blog_style_sidebar' => false,
        'nbcore_blog_collapse_post' => false,
        'nbcore_blog_meta_align' => 'center',
        'show_title_section' => true,
        'nbcore_page_title_padding' => '18',
        'nbcore_page_title_color' => '#323232',
        'nbcore_page_404_bg' => '#444',
        'nbcore_page_404_text' => '#fff',
        'body_font_family' => 'google,Lato',
        'body_font_size' => '16',
        'nbcore_footer_head_fontsize' => '16',
        'nbcore_footer_text_fontsize' => '14',
        'nbcore_footer_middle_paddingtop' => '15',
        'nbcore_footer_middle_paddingbottom' => '15',
        'heading_font_family' => 'google,Merriweather',
        'subset_cyrillic' => false,
        'subset_greek' => false,
        'subset_vietnamese' => false,
        'nbcore_wc_breadcrumb' => true,
        'nbcore_wc_content_width' => '70',
        'nbcore_pa_swatch_style' => '',
        'nbcore_shop_title' => esc_html__('Shop', 'printcart'),
        'nbcore_shop_action' => true,
        'nbcore_product_image_mask' => false,
        'nbcore_product_rating' => true,
        'nbcore_product_meta_align' => 'left',
        'nbcore_product_hover'      => 'image',
        'nbcore_product_action_style' => 'horizontal',
        'nbcore_shop_sidebar' => 'left-sidebar',
        'nbcore_loop_columns' => 'three-columns',
        'nbcore_products_per_page' => '12',
        'nbcore_product_list' => 'grid-type',
        'nbcore_shop_content_width' => '75',
        'nbcore_grid_product_description' => false,
        'nbcore_show_separated_border' => false,
        'nbcore_pd_details_title' => true,
        'nbcore_pd_details_width' => '70',
        'nbcore_pd_details_sidebar' => 'right-sidebar',
        'nbcore_wc_sale' => '',
        'nbcore_product_image_border_color' => 'rgba(255, 255, 255, 0)',
        'nbcore_pd_images_width' => '40',
        'nbcore_pd_thumb_pos' => 'bottom-thumb',
        'nbcore_pd_meta_layout' => 'left-images',
        'nbcore_pd_featured_autoplay' => false,
        'nbcore_info_style' => 'horizontal-tabs',
        'nbcore_reviews_form' => 'full-width',
        'nbcore_reviews_round_avatar' => true,
        'nbcore_add_cart_style' => 'style-1',
        'nbcore_pd_show_social' => true,
        'nbcore_show_related' => true,
        'nbcore_pd_related_columns' => '4',
        'nbcore_show_upsells' => false,
        'nbcore_pd_upsells_columns' => '3',
        'nbcore_pb_background' => '#4285f4',
        'nbcore_pb_background_hover' => '#1565C0',
        'nbcore_pb_text' => '#ffffff',
        'nbcore_pb_text_hover' => '#ffffff',
        'nbcore_pb_border' => '#1e88e5',
        'nbcore_pb_border_hover' => '#1565C0',
        'nbcore_sb_background' => 'transparent',
        'nbcore_sb_background_hover' => '#1e88e5',
        'nbcore_sb_text' => '#1e88e5',
        'nbcore_sb_text_hover' => '#ffffff',
        'nbcore_sb_border' => '#1e88e5',
        'nbcore_sb_border_hover' => '#1e88e5',
        'nbcore_button_padding' => '30',
        'nbcore_button_border_radius' => '0',
        'nbcore_button_border_width' => '2',
        'nbcore_cart_layout' => 'cart-layout-2',
        'nbcore_show_cross_sells' => true,
        'nbcore_cross_sells_per_row' => '4',
        'nbcore_cross_sells_limit' => '6',
        'home_page_title_section' => false,
        'nbcore_show_footer_top' => true,
        'nbcore_footer_top_layout' => 'layout-1',
        'nbcore_footer_top_color' => '#fff',
        'nbcore_footer_top_bg' => '#4285f4',
        'nbcore_show_footer_bot' => true,
        'nbcore_footer_heading_up' => false,
        'nbcore_footer_bot_layout' => 'layout-9',
        'nbcore_footer_bot_color' => '#fff',
        'nbcore_footer_bot_color_hover' => '#4285f4',
        'nbcore_footer_bot_bg' => '#222325',
        'nbcore_footer_abs_color' => '#999999',
        'nbcore_footer_abs_bg' => '#191919',
        'nbcore_top_section_padding' => '10',
        'nbcore_header_top_hotline' => '1.866.614.8002',
        'nbcore_middle_section_padding' => '20',
        'nbcore_bot_section_padding' => '0',
        'nbcore_header_top_bg' => '#4285f4',
        'nbcore_header_top_color' => '#fff',
        'nbcore_header_top_hovercolor' => '#e2e2e2',
        'nbcore_header_middle_bg' => '#fff',
        'nbcore_header_middle_color' => '#666',
        'nbcore_header_bot_bg' => '#fff',
        'nbcore_header_bot_color' => '#646464',
        'nbcore_footer_bot_heading' => '#fff',
        'nbcore_header_secondary_color' => '#4285f4',
        'nbcore_footer_bot_color_border' => 'rgba(0, 0, 0, 0)',
        'nbcore_footer_top_color_border' => 'rgba(0, 0, 0, 0)',
        'nbcore_blog_archive_comments' => true,
        'nbcore_blog_archive_summary' => true,
        'nbcore_blog_single_title_positions' => 'position-1',
        'nbcore_blog_single_show_thumb' => true,
        'nbcore_blog_single_title_size' => '24',
        'nbcore_blog_single_show_social' => true,
        'nbcore_blog_single_show_author' => true,
        'nbcore_blog_single_show_nav' => true,
        'nbcore_blog_single_show_comments' => true,
        'nbcore_page_title_size' => '50',
        'share_buttons_style' => 'style-1',
        'share_buttons_position' => 'inside-content',
        'pagination_style' => 'pagination-style-1',
        'show_back_top' => true,
        'back_top_shape' => 'circle',
        'back_top_style' => 'light',
        'shop_sticky_sidebar' => false,
        'product_sticky_sidebar' => false,
        'page_thumb' => 'no-thumb',
        'page_sidebar' => 'full-width',
        'page_content_width' => '70',
        'nbcore_blog_masonry_columns' => '2',
        'nbcore_blog_layout_columns' => '1',
        'product_category_wishlist' => true,
        'product_category_quickview' => false,
        'product_category_compare' => true,
        'heading_font_style' => '400',
        'body_font_style' => '400',
        'nbcore_footer_abs_left_content' => '',
        'nbcore_footer_abs_right_content' => '',
        'nbcore_footer_title' => '',
        'nbcore_footer_phone' => '',
        'nbcore_footer_email' => '',
        'nbcore_footer_address' => '',
        'nbcore_footer_cap' => '',
        'nbcore_page_fullbox' => false,
        'nbcore_footer_list_style' => 'true',
        'nbcore_page_layout' => 'full-width',
        'nbcore_page_content_width' => 60,
        'nbcore_shop_banner' => '',
        'nbcore_container_width_screen' =>'1170',
        'nbcore_header_mainmn_color' => '#000',
        'nbcore_header_mainmnhover_color' => '#4285f4',
        'nbcore_header_top_right_color' => '#000',
        'nbcore_header_top_left_color' => '#000',
        'nbcore_header_top_left_title_color' => '#888888',
        'nbcore_footer_social_media' => '#333333',
        'nbcore_footer_social_media_border' => '#a0a0a0',
        'nbcore_footer_social_media_hover' => '#8881fa',
        'nbcore_footer_social_media_bor_hover' => '#8881fa',
        'nbcore_footer_border_color' => '#8881fa',
        'nbcore_footer_text_color_hover' => '#8881fa',
        'nbcore_title_color' => '#333333',
        'nbcore_detail_color' => '#000',
        'nbcore_description_color' => '#696969',
        'nbcore_title_color_hover' => '#8881fa',
        'nbcore_category_color' => '#999999',
        'nbcore_footer_top_padding_top' => '15',
        'nbcore_footer_top_padding_bottom' => '15',
        'nbcore_footer_border_color_parent' => '#999999',
        'nbcore_header_menu_config' => false,
        'nbcore_footer_social_media_bg_hover' => '',
        'nbcore_blog_classic_columns' => 1,
        'nbcore_blog_single_cover' => '',
        'nbcore_header_menu_border_color' => '',
        'nbcore_sidebar_color' => '',
        'nbcore_footer_top_heading' => '',
        'nbcore_show_header_topbar' => false,
        'nbcore_show_to_shop' => false,
        'nbcore_blog_display_swipper' => false,
        'nbcore_template_designer_style'=>'style1'
    );
}

function printcart_get_options($option)
{
    $result = '';
    $default = printcart_default_options();

    if(class_exists('NBFW_Metaboxes')) {
        $meta = '';
        $qobject = get_queried_object();
        
        if( isset($qobject->post_type) ) {
            $id = $qobject->ID;
            $global = get_post_meta($id, 'nbcore_global_setting', true);
            $meta = get_post_meta($id, $option, true);
        } elseif(is_tax() || is_category() || is_tag()) {
            $id = get_queried_object_id();
            $global = get_term_meta($id, 'nbcore_global_setting', true);
            $meta = get_term_meta($id, $option, true);
        }

        if($meta !== '' && $global !== '') {
            $result = $meta;
        } else if( isset($default[$option]) ) {
            $result = get_theme_mod($option, $default[$option]);
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            if( !empty($meta) ) {
                $result = $meta;
            }else{
                $result = get_theme_mod($option, $default[$option]);
            }
        }
    } else {
        $result = get_theme_mod($option, $default[$option]);
    }


    return $result;
}

function printcart_blog_classes()
{
    $classes = array();

    $classes['sidebar'] = printcart_get_options('nbcore_blog_sidebar');
    $classes['meta_align'] = 'meta-align-' . printcart_get_options('nbcore_blog_meta_align');
    $classes['single_blog_title'] = 'title-' . printcart_get_options('nbcore_blog_single_title_position');

    if ('classic' === printcart_get_options('nbcore_blog_archive_layout')) {
        $classes['classic_columns'] = 'classic-' . printcart_get_options('nbcore_blog_classic_columns') . '-columns';
    }

    if ('layout' === printcart_get_options('nbcore_blog_archive_layout')) {
        $classes['layout_columns'] = 'layout-' . printcart_get_options('nbcore_blog_layout_columns') . '-columns';
    }

    if (printcart_get_options('nbcore_blog_style_sidebar')){
        $classes['style_sidebar'] = 'enable';
    }

    if (printcart_get_options('nbcore_blog_collapse_post')){
        $classes['collapse_post'] = 'collapse';
    }

    echo implode(' ', $classes);
}

function printcart_shop_classes()
{
    $classes = array();

    if ((is_shop() || is_product_category() || is_product_tag()) && 'list-type' !== printcart_get_options('nbcore_product_list')) {
        $classes['shop_columns'] = printcart_get_options('nbcore_loop_columns');
    }

    $classes['meta_layout'] = printcart_get_options('nbcore_pd_meta_layout');

    if (function_exists('is_product') && is_product()) {
        $classes['nbcore_pd_thumb_pos'] = printcart_get_options('nbcore_pd_thumb_pos');
    }

    if ('split' === printcart_get_options('nbcore_reviews_form')) {
        $classes['nbcore_reviews_form'] = 'split-reviews-form';
    }

    if (printcart_get_options('nbcore_reviews_round_avatar')) {
        $classes['nbcore_round_avatar'] = 'round-reviewer-avatar';
    }

    if (printcart_get_options('nbcore_show_separated_border')) {
        $classes['has_separated_border'] = 'has-separated-border';
    }

    $classes['wc_tab_style'] = printcart_get_options('nbcore_info_style');

    if (is_product()) {
        $classes['related_columns'] = 'related-' . printcart_get_options('nbcore_pd_related_columns') . '-columns';
        $classes['upsells_columns'] = 'upsells-' . printcart_get_options('nbcore_pd_upsells_columns') . '-columns';
    }

    echo implode(' ', $classes);
}



function printcart_back_to_top()
{
    $shape = printcart_get_options('back_top_shape');
    $style = printcart_get_options('back_top_style');
    echo '<div class="nb-back-to-top-wrap"><a id="back-to-top-button" class="' . esc_attr($shape) . ' ' . esc_attr($style) . '" href="#"><i class="pt-icon-angle-up"></i><span>' . esc_html('Top') . '</span></a></div>';
}

function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return '<i class="fa fa-eye" aria-hidden="true"></i>0';
    }
    return '<i class="fa fa-eye" aria-hidden="true"></i>'.$count;
}

// function to count views.
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}