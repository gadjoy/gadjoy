<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content-vw">
 *
 * @package The Computer Repair
 */

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

	<head>
	  	<meta charset="<?php bloginfo( 'charset' ); ?>">
	  	<meta name="viewport" content="width=device-width">
	  	<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>
	<?php if ( function_exists( 'wp_body_open' ) ) 
		{
			wp_body_open();
		}else{
			do_action('wp_body_open');
		}
	?>

	<header role="banner">
		<a class="screen-reader-text skip-link" href="#maincontent"><?php esc_html_e( 'Skip to content', 'the-computer-repair' ); ?></a>
		<div class="home-page-header">
			<?php get_template_part('template-parts/header/top-header'); ?>
			<?php get_template_part('template-parts/header/middle-header'); ?>
			<?php get_template_part('template-parts/header/lower-header'); ?>
		</div>
	</header>

	<?php if(get_theme_mod('the_computer_repair_loader_enable',false) != '') { ?>
  		<div id="preloader">
		    <div class="loader-inner">
		      <div class="loader-line-wrap">
		        <div class="loader-line"></div>
		      </div>
		      <div class="loader-line-wrap">
		        <div class="loader-line"></div>
		      </div>
		      <div class="loader-line-wrap">
		        <div class="loader-line"></div>
		      </div>
		      <div class="loader-line-wrap">
		        <div class="loader-line"></div>
		      </div>
		      <div class="loader-line-wrap">
		        <div class="loader-line"></div>
		      </div>
		    </div>
	  	</div>
	<?php } ?>