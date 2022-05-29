<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package nbcore
 */
$single_blog_sidebar = printcart_get_options('nbcore_blog_sidebar');
$title_position = printcart_get_options('nbcore_blog_single_title_positions');
get_header();?>

<?php
// if ('position-1' === $title_position) {
// 	echo '<div class="nb-page-title-wrap"><div class="container"><div class="nb-page-title">';
// 	printcart_posted_on();
// 	the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
// 	printcart_get_categories();
// 	echo '</div></div></div>';
// }

?>
<?php if( function_exists('is_woocommerce') && is_woocommerce() ) {?>
<div class="nb-page-title-wrap single-breadcrum">
	<div class="container">
		<?php woocommerce_breadcrumb();	?>
	</div>
</div>
<?php } ?>
<div class="container">
	<div class="single-blog row <?php printcart_blog_classes(); ?>">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
			
				<?php
				while ( have_posts() ) : the_post();
				setPostViews(get_the_ID());
				?>
					<div class="entry-content">
						<div class="single-post">
						<?php
						if(printcart_get_options('nbcore_blog_single_show_thumb')):
							$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full' );
							if($thumb):
								?>
								<div class="entry-image">
									<?php
									printf('<img src="%1$s" title="%2$s" width="%3$s" height="%4$s" />',
										$thumb[0],
										esc_attr(get_the_title()),
										$thumb[1],
										$thumb[2]
									);
									?>
								</div>
							<?php endif;
						endif;
						printcart_get_categories();
						the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
						?>
						<div class="entry-wrap">
							<?php printcart_posted_on();?>
						</div>
						<div class="entry-text">
							<?php
							the_content( sprintf(
								/* translators: %s: Name of current post. */
								wp_kses( esc_html__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'printcart' ), array( 'span' => array( 'class' => array() ) ) ),
								the_title( '<span class="screen-reader-text">"', '"</span>', false )
							) );

							wp_link_pages( array(
								'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'printcart' ),
								'after'  => '</div>',
							) );
							?>
						</div>
						
						
						<?php
                        if('inside-content' === printcart_get_options('share_buttons_position')) {
                            if(printcart_get_options('nbcore_blog_single_show_social') && function_exists('nbcore_share_social')) {
								echo '<div class="entry-footer">';
								printcart_get_tags();
								echo '</div>';
                            }
						}
						?>

						
						<?php if(printcart_get_options('nbcore_blog_single_show_nav')): ?>
							<nav class="single-blog-nav" role="navigation">
								<?php
								$nextPost = get_next_post(true);
								$prevPost = get_previous_post(true);
								if($prevPost != null){
									$prevthumbnail = get_the_post_thumbnail($prevPost->ID, array(100,100) );
									previous_post_link( '<div class="prev">%link<span></span></div>', _x( '<span class="thumb">'.$prevthumbnail.'</span><div class="navi"><span>Previous post</span>%title</div>','printcart' ) );
								}
								if($nextPost != null){
									$nextthumbnail = get_the_post_thumbnail($nextPost->ID, array(100,100) );
									next_post_link(     '<div class="next">%link<span></span></div>',     _x( '<div class="navi"><span>Next post</span>%title</div><span class="thumb">'.$nextthumbnail.'</span>', 'printcart' ) );
								}
								?>
							</nav><!-- .single-nav -->
						<?php endif; ?>

						<?php
						if(printcart_get_options('nbcore_blog_single_show_author')):
							$author_meta = esc_html( get_the_author_meta( 'display_name' ) );
							$author_avatar = get_avatar(get_the_author_meta('ID'), 100);
							$author_desc = get_the_author_meta('user_description');
							if($author_meta !== null){?>
							<div class="author-title"><h2><?php esc_html_e('About author', 'printcart'); ?></h2></div>
							<div class="entry-author-wrap">
								<div class="entry-author">
									<div class="author-image">
										<?php echo get_avatar(get_the_author_meta('ID'), 100); ?>
									</div>
									<div class="author-meta">
										<div class="author-name">
											<?php echo esc_html($author_meta); ?>
										</div>
										<div class="author-description">
											<?php echo esc_html($author_desc); ?>
										</div>
										<div class="author">
											<?php  
											if(printcart_get_options('nbcore_blog_single_show_social') && function_exists('nbcore_share_social')) {
												nbcore_share_social(); 
											}
											?>
										</div>
									</div>
								</div>
							</div>				
					<?php
						}
						endif;
					$args = array(
						'posts_per_page'    => 3,
						'post_type' => 'post',
					);

					// The Query
					$the_query = new WP_Query( $args );
					if ( $the_query->have_posts() ) {?>
						<div class="entry-recent">
							<div class="related-title"><h2><?php esc_html_e('Related Articles', 'printcart');?></h2></div>

							<div class="related-post-wrap owl-carousel owl-theme">
								<?php while ( $the_query->have_posts() ) {
									$the_query->the_post();
									$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'blog-home');
									?>
									<div class="related-item item">
										<a href="<?php echo esc_url( get_permalink($post->ID) );?>">
											<img src="<?php echo esc_url( $thumb[0] );?>" />
										</a>
										<h5><a href="<?php echo esc_url( get_permalink($post->ID) );?>"><?php echo esc_attr($post->post_title);?></a></h5>

									</div>
									<?php
								}?>
							</div>
						</div>
						<?php
					}
					wp_reset_postdata();
					if(printcart_get_options('nbcore_blog_single_show_comments')) {
						if ( comments_open() || get_comments_number() ) {
							echo '<div class="comment-title"><h2>' . esc_html('Leave your comment', 'printcart') . '</h2></div>';
							comments_template();
						}
					}
					?>
					<?php
				endwhile; // End of the loop.
				?>

			</main><!-- #main -->
		</div><!-- #primary -->
		<?php
		if('no-sidebar' !== $single_blog_sidebar) {
			get_sidebar();
		}
		?>
	</div>
</div>

<script>
	jQuery(document).ready(function ($) {
		var $rtl = false;
		if($('body .rtl')){
			$rtl = true;
		}
		$('.related-post-wrap').owlCarousel({
			margin: 30,
			items: 3, 
			rtl : $rtl,
           //autoplay:true, 
           //autoplayTimeout:5000,
           nav: true,
           responsive : {
           // breakpoint from 480 up
           0 : {
           	items: 1
           },
           768 : {
           	items: 3
           }
       },
       dots: false,
       navText : ['<i class="pt-icon-chevron-left" aria-hidden="true"></i>','<i class="pt-icon-chevron-right" aria-hidden="true"></i>']

   })
	});
</script>

<?php
get_footer();
