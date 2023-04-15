<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
global $post;
?><!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
	<meta charset="<?php bloginfo('charset');?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/632054597762a91166d37dc21/2f512d9df3c2516c7c0abd793.js");</script>

	<?php wp_head(); ?>
</head>

<body <?php body_class();?>>
	<div id="page" class="site">
		<?php if (is_front_page()) { ?>
			<h1 class="h1-hidden"><?php echo get_bloginfo(); ?></h1>
		<?php } ?>
		<div id="site-wrapper" <?php if(printcart_get_options('nbcore_page_fullbox')) { echo 'class="container"'; } ?>>


			<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'printcart');?></a>

			<header class="site-header <?php printcart_header_class(); ?>  <?php if(printcart_get_options('nbcore_header_menu_config')){ echo 'border-bottom'; } ?>" role="banner">

				<?php
				printcart_get_header();

				do_action('nb_core_after_header');
				?>

			</header>			
			<div id="content" class="site-content">