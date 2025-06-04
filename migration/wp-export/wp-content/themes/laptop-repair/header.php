<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="container">
 *
 * @package Laptop Repair
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action( 'wp_body_open' ); ?>
<div class="header">
  <div class="container">
    <div class="logo">
		<?php laptop_repair_the_custom_logo(); ?>
        <div class="clear"></div>
		<?php	
        $description = get_bloginfo( 'description', 'display' );
        ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
        <h2 class="site-title"><?php bloginfo('name'); ?></h2>
        <?php if ( $description || is_customize_preview() ) :?>
        <p class="site-description"><?php echo esc_html($description); ?></p>                          
        <?php endif; ?>
        </a>
    </div> 
	 <?php
	$fb_link = get_theme_mod('hdr_fb_link'); 
	$twitt_link = get_theme_mod('hdr_twitt_link');
	$gplus_link = get_theme_mod('hdr_gplus_link');
	$youtube_link = get_theme_mod('hdr_youtube_link');
	$instagram_link = get_theme_mod('hdr_instagram_link');
	$linkedin_link = get_theme_mod('hdr_linkedin_link'); 
	$email_add = get_theme_mod('email_add'); 
	$contact_no = get_theme_mod('contact_no'); 
	$hidetopbar = get_theme_mod('hide_header_topbar', 1);
	if( $hidetopbar == '') { ?>    
    <div class="header-right"> 
    <?php if(!empty($email_add)){ ?>
    <div class="emltp">
<img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/mail-icon.png"><span><?php echo esc_html( antispambot( $email_add ) ); ?></span></div>
<?php } ?>
<?php if(!empty($contact_no)){?>
<div class="sintp"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/phone-icon.png"><span><?php echo esc_html($contact_no); ?></span></div>
<?php } ?>
<div class="header-social-icons"><div class="social-icons">
    	<?php 
            if (!empty($fb_link)) { ?>
            <a title="<?php echo esc_attr__('Facebook','laptop-repair'); ?>" class="fb" target="_blank" href="<?php echo esc_url($fb_link); ?>"></a> 
            <?php }  
            if (!empty($twitt_link)) { ?>
            <a title="<?php echo esc_attr__('Twitter','laptop-repair'); ?>" class="tw" target="_blank" href="<?php echo esc_url($twitt_link); ?>"></a> 
            <?php }  
            if (!empty($gplus_link)) { ?>
            <a title="<?php echo esc_attr__('Google Plus','laptop-repair'); ?>" class="gp" target="_blank" href="<?php echo esc_url($gplus_link); ?>"></a> 
            <?php }   
            if (!empty($youtube_link)) { ?>
            <a title="<?php echo esc_attr__('Youtube','laptop-repair'); ?>" class="tube" target="_blank" href="<?php echo esc_url($youtube_link); ?>"></a> 
            <?php }   
            if (!empty($instagram_link)) { ?>
            <a title="<?php echo esc_attr__('Instagram','laptop-repair'); ?>" class="insta" target="_blank" href="<?php echo esc_url($instagram_link); ?>"></a> 
            <?php }   
            if (!empty($linkedin_link)) { ?>
            <a title="<?php echo esc_attr__('Linkedin','laptop-repair'); ?>" class="in" target="_blank" href="<?php echo esc_url($linkedin_link); ?>"></a> 
            <?php } ?>   
            </div></div>
        <div class="clear"></div>                
    </div>
<?php } ?>    
    <div class="clear"></div>
    <div id="topmenu">
    	         <div class="toggle"><a class="toggleMenu" href="#" style="display:none;"><?php esc_html_e('Menu','laptop-repair'); ?></a></div> 
        <div class="sitenav">
          <?php wp_nav_menu( array('theme_location' => 'primary') ); ?>         
        </div><!-- .sitenav--> 
    </div>
  </div> <!-- container -->
  <div class="clear"></div>
</div><!--.header -->
<div class="clear"></div>