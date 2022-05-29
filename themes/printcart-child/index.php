<?php
global $wp_query;
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package nbcore
 */
$blog_layout = printcart_get_options('nbcore_blog_archive_layout');
$blog_sidebar = printcart_get_options('nbcore_blog_sidebar');
$blog_classic_columns = printcart_get_options('nbcore_blog_classic_columns');
$blog_swipper = printcart_get_options('nbcore_blog_display_swipper');
get_header();

?>
	<?php printcart_page_title(); ?>
	<?php if($blog_swipper){ ?>
	<div id="entry-swiper">
		<div class="swiper-container">
			<div class="swiper-wrapper">
				<?php
					while ( have_posts() ) : the_post();
						echo '<div class="swiper-slide">';
						printcart_featured_thumb();
						echo '<div class="entry-content">';
						printcart_get_categories();
						the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
						printcart_posted_on();
						echo '<div class="read-more-link"><a class="bt-4 nb-secondary-button" href="' . get_permalink() . '">' . esc_html__('Read more', 'printcart') . '</a></div>';
						echo '</div>';	
						echo '</div>';
					endwhile;
				?>
			</div>
		</div>
	</div>
	<?php } ?>
<?php
		global $wp_query;
		/* if($wp_query->query['pagename']=='blog'){ ?>
			<div class="page-cover-header page-title-blog">
                <div class="page-cover-wrap">
                    <div class="page-cover-block">
                        <h1>Blog</h1>
                    </div>
                </div>
            </div>
		<?php } */?>
	<div class="container">
	<?php
		global $wp_query;
		if($wp_query->query['pagename']=='blog'){ ?>
			<div class="nb-nav" style="margin-bottom: 50px;">
       			<nav class="woocommerce-breadcrumb" itemprop="breadcrumb"><a href="<?php echo home_url(); ?>">Home</a><span>/</span><b>Blog</b></nav>
			</div>	
		<?php } ?>
		<div class="blog row <?php echo printcart_blog_classes(); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main <?php echo esc_attr($blog_layout); ?>" role="main">
				<?php
			
				if ( have_posts() ) :
	
					if ( is_home() && ! is_front_page() ) : ?>
						<header>
							<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
						</header>
					<?php
					endif;

					if($blog_classic_columns == 2 || $blog_classic_columns == 3) :
						echo '<div class="blog-wrapper-columns">';
					endif;
					
					/* Start the Loop */
					while ( have_posts() ) : the_post();
	
						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'template-parts/blog/' . $blog_layout );
	
					endwhile;

					if($blog_classic_columns == 2 || $blog_classic_columns == 3) :
						echo '</div>';		
					endif;
				else :
	
					get_template_part( 'template-parts/content', 'none' );
	
				endif; ?>
	
				</main><!-- #main -->
		
			<div class="nb-post-pagination">
				<?php the_posts_pagination(['prev_text'=>'Newer Articles',
				'next_text'=>'Older Articles',]); ?>
			</div>
				
			</div><!-- #primary -->
			<?php
			if('no-sidebar' !== $blog_sidebar) {
				get_sidebar();
			} ?>
		</div>
	</div>
<?php
get_footer();
