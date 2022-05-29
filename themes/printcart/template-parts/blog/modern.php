<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package nbcore
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php printcart_featured_thumb();?>
	<div class="entry-content">
        <?php
        printcart_get_categories();
        the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
        printcart_posted_date();
        
		if(printcart_get_options('nbcore_blog_archive_summary')):
		?>
		<div class="entry-text">
			<?php
			if(printcart_get_options('nbcore_excerpt_only')) {
				printcart_get_excerpt();
				echo '<div class="read-more-link"><a class="bt-4 nb-secondary-button" href="' . get_permalink() . '">' . esc_html__('Read more', 'printcart') . '</a>';
                if(printcart_get_options('nbcore_blog_single_show_social') && function_exists('nbcore_share_social')) {
                    // nbcore_share_social();
                }
                echo '</div>';
			} else {
				the_content( sprintf(
					/* translators: %s: Name of current post. */
					__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'printcart' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				) );

				wp_link_pages( array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'printcart' ),
					'after'  => '</div>',
				) );
			}			
			?>
		</div>
		<?php endif; ?>
	</div>
	
</article><!-- #post-## -->
