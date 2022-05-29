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
	<?php printcart_featured_thumb(); ?>
	<div class="entry-content">
		<?php
		printcart_get_categories();
		the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );

		printcart_posted_date();
		?>
		
		<?php
		if(printcart_get_options('nbcore_blog_archive_summary')):
			if(printcart_get_options('nbcore_excerpt_only')) {
				?>
				<div class="entry-text">
					<?php printcart_get_excerpt(); ?>
				</div>
				<?php
				echo '<div class="read-more-link"><a class="bt-4 nb-secondary-button" href="' . get_permalink() . '">' . esc_html__('Read more', 'printcart') . '</a></div>';
				?>
			<?php } else { ?>
				<div class="entry-text">
					<?php 
					the_content( sprintf(
						/* translators: %s: Name of current post. */
						__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'printcart' ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					) ); ?>
				</div>
			<?php } ?>
		<?php endif; ?>
	</div>

</article><!-- #post-## -->
