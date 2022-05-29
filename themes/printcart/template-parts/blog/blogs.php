<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package printcart
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php
		printcart_featured_thumb();
        printcart_get_categories();
        the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
        echo '<div class="entry-wrap">';
		printcart_posted_on();
		if(printcart_get_options('nbcore_blog_archive_comments')):?>
            <?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
                <span class="comments-link"><?php comments_popup_link( esc_html__( 'Leave a comment', 'printcart' ), esc_html__( '1 Comment', 'printcart' ), esc_html__( '% Comments', 'printcart' ) ); ?></span>
            <?php endif; ?>
        <?php endif;
		printcart_get_tags();
        echo '</div>';
      
		if(printcart_get_options('nbcore_blog_archive_summary')):
		?>
		<div class="entry-text">
			<?php
			if(printcart_get_options('nbcore_excerpt_only')) {
				printcart_get_excerpt();
				echo '<div class="read-more-link"><a class="bt-4 nb-secondary-button" href="' . get_permalink() . '">' . esc_html__('Read more', 'printcart') . '</a></div>';
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
        <div class="entry-footer">
		    <span>share: </span><?php printcart_social_section(); ?>
        </div>
	</div>
	
</article><!-- #post-## -->
