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



get_header();

$page_sidebar = printcart_get_options( 'nbcore_page_layout');

?>

	<?php printcart_page_title(); ?>

	<div class="container">

		<div class="row">
			<?php

            if($page_sidebar == 'left-sidebar' || $page_sidebar == 'right-sidebar' ) {

                get_sidebar();

            }

			?>

			<div id="primary" class="content-area page-<?php echo esc_attr($page_sidebar); ?>">

				<main id="main" class="site-main" role="main">



					<?php

					while ( have_posts() ) : the_post();



						?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                            <?php

                            if('no-thumb' !== printcart_get_options('page_thumb')) {

                                printcart_featured_thumb();

                            }

                            ?>

                            <div class="entry-content">

                                <?php

                                the_content();



                                wp_link_pages( array(

                                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'printcart' ),

                                    'after'  => '</div>',

                                ) );

                                ?>

                            </div><!-- .entry-content -->

                        </article><!-- #post-## -->

                        <?php

						// If comments are open or we have at least one comment, load up the comment template.

						if ( comments_open() || get_comments_number() ) :

							comments_template();

						endif;



					endwhile; // End of the loop.

					?>



				</main><!-- #main -->

			</div><!-- #primary -->

			

		</div>

	</div>


<?php

get_footer();

