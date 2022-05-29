<?php

/**

 * The template for displaying the footer

 *

 * Contains the closing of the #content div and all content after.

 *

 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials

 *

 * @package nbcore

 */



?>

		</div><!-- #content -->

		<footer id="colophon" class="site-footer">

			<?php if(printcart_get_options('nbcore_show_footer_top')){?>

			<div class="footer-top-section">

				<div class="container">

					<div class="row">

						<div class="logo-wrapper-f col-md-12 text-center">
							<?php printcart_get_footer_logo()?>
						</div>
						<?php if(!empty(printcart_get_options('nbcore_footer_title'))){ ?>
							<div class="col-md-12 text-center">
								<div class="footer_top_title ">
									<?php echo printcart_get_options('nbcore_footer_title');?>
								</div>
							</div>
						<?php } ?>
						<?php if( !empty(printcart_get_options('nbcore_footer_phone')) || !empty(printcart_get_options('nbcore_footer_email')) || !empty(printcart_get_options('nbcore_footer_address'))){ ?>
							<div class="col-md-6 footer_top_left text-right">
								<h3 class="phone"><?php echo printcart_get_options('nbcore_footer_phone');?></h3>
								<div class="email"><a href="#"><?php echo printcart_get_options('nbcore_footer_email');?></a></div>
								<p class="street"><?php echo printcart_get_options('nbcore_footer_address');?></p>
							</div>
						<?php } ?>
						<?php if( !empty(printcart_get_options('nbcore_footer_cap')) ){ ?>
							<div class="col-md-6 footer_top_right text-left">
								<p class="caption"><?php echo printcart_get_options('nbcore_footer_cap'); ?></p>
								<?php 
								if(is_active_sidebar( 'footer_newletter' )){
									dynamic_sidebar( 'footer_newletter' );
								}
								?>
							</div>
						<?php } ?>
						<div style="clear:both"></div>
						<div class="wrap-top">
							<?php 
							if (is_active_sidebar( 'top_left' ) || is_active_sidebar( 'top_right' ) ): 

							$top_layout = printcart_get_options('nbcore_footer_top_layout');
							
							switch ($top_layout) {

								case 'layout-1':

									echo '<div class="col-lg-6 top_left">';

									dynamic_sidebar('top_left');

									echo '</div>';

									echo '<div class="col-lg-6 col-md-12 top_right">';

									dynamic_sidebar('top_right');

									echo '</div>';

									break;

								case 'layout-2':

									echo '<div class="col-lg-8 col-md-12 top_left">';

									dynamic_sidebar('top_left');

									echo '</div>';

									echo '<div class="col-lg-4 col-md-12 top_right">';

									dynamic_sidebar('top_right');

									echo '</div>';

									break;
							}
							
							endif;
							?>
						</div>
					</div>

				</div>

			</div>

			<?php }



            if( printcart_get_options('nbcore_show_footer_bot') ):?>

            <?php

			if (is_active_sidebar( 'footer-top-1' ) || is_active_sidebar( 'footer-top-2' ) || is_active_sidebar( 'footer-top-3' ) || is_active_sidebar( 'footer-top-4' ) ): ?>

			<div class="footer-bot-section ">

				<div class="container">

					<div class="row z-index <?php if(printcart_get_options('nbcore_footer_heading_up')){echo 'uppercase';} ?> <?php if(printcart_get_options('nbcore_footer_list_style')==false){echo 'listnone';} ?>">

						<?php

						$bot_layout = printcart_get_options('nbcore_footer_bot_layout');

						switch ($bot_layout) {

							case 'layout-1':

								echo '<div class="col-12">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								break;

							case 'layout-2':

								echo '<div class="col-6">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-6">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								break;

							case 'layout-3':

								echo '<div class="col-8">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-4">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								break;

							case 'layout-4':

								echo '<div class="col-4">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-8">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								break;

							case 'layout-5':

								echo '<div class="col-4">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-4">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								echo '<div class="col-4">';

								dynamic_sidebar('footer-top-3');

								echo '</div>';

								break;

							case 'layout-6':

								echo '<div class="col-6">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-3');

								echo '</div>';

								break;

							case 'layout-7':

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-6">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-3');

								echo '</div>';

								break;

							case 'layout-8':

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								echo '<div class="col-6">';

								dynamic_sidebar('footer-top-3');

								echo '</div>';

								break;

							default :

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-1');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-2');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-3');

								echo '</div>';

								echo '<div class="col-3">';

								dynamic_sidebar('footer-top-4');

								echo '</div>';

						}

						?>

					</div>

				</div>

			</div>

            <?php endif;

			endif;?>

			<div class="footer-abs-section">

				<div class="container">

					<div class="row">
						<div class="col-lg-12 social-media">
							<?php 
							if(is_active_sidebar( 'footer_bottom' )){
								dynamic_sidebar( 'footer_bottom' );
							}
							?>
						</div>
						<div class="col-lg-12 footer-abs-content">
							<?php

							$left_content = printcart_get_options('nbcore_footer_abs_left_content');

							$right_content = printcart_get_options('nbcore_footer_abs_right_content');

							if( ( $left_content && '' !== $left_content ) && ( $right_content && '' !== $right_content ) ) {

								echo '<p class="footer-abs-left">' . $left_content . '</p>';

								echo '<p class="footer-abs-right"><img src="' . $right_content . '" alt="' . esc_attr(get_bloginfo('name', 'display')) . '"></p>';

							} elseif( ( !$left_content || '' == $left_content ) && ( $right_content && '' !== $right_content ) ) {

								echo '<p class="footer-abs-middle"><img src="' . $right_content . '" alt="' . esc_attr(get_bloginfo('name', 'display')) . '"></p>';

							} elseif( ( $left_content && '' !== $left_content ) && ( !$right_content || '' == $right_content ) ) {

								echo '<p class="footer-abs-middle">' . $left_content . '</p>';

							}

							?>
						</div>

					</div>

				</div>

			</div>

		</footer><!-- #colophon -->

    <?php

    if('floating' === printcart_get_options('share_buttons_position')) {

        if(printcart_get_options('nbcore_blog_single_show_social') && function_exists('nbcore_share_social')) {

            nbcore_share_social();

        }

    }

    if(printcart_get_options('show_back_top')) {

        printcart_back_to_top();

    }

    ?>

	</div><!-- #site-wrapper -->

</div><!-- #page -->



<!---->

<?php wp_footer(); ?>

</body>

</html>