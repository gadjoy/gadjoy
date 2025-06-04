<?php
/**
 * The template part for header
 *
 * @package The Computer Repair 
 * @subpackage the-computer-repair
 * @since the-computer-repair 1.0
 */
?>

<div class="top-bar">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3">
		    </div>
		    <div class="col-lg-5 col-md-5">
			    <?php dynamic_sidebar('social-links'); ?>
		    </div>
		    <div class="col-lg-1 col-md-1 col-3">
		    	<?php if(class_exists('woocommerce')){ ?>
		          <p class="cart_no">
		            <a href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_attr_e( 'shopping cart','the-computer-repair' ); ?>"><i class="<?php echo esc_attr(get_theme_mod('the_computer_repair_cart_icon','fas fa-shopping-basket')); ?>"></i><span class="screen-reader-text"><?php esc_html_e( 'shopping cart','the-computer-repair' );?></span></a>
		            <span class="cart-value"> <?php echo wp_kses_data( WC()->cart->get_cart_contents_count() );?></span>
		          </p>
		        <?php } ?>
		    </div>
		    <div class="col-lg-3 col-md-3 col-9">
		    	<?php if( get_theme_mod( 'the_computer_repair_top_btn_url') != '' || get_theme_mod( 'the_computer_repair_top_btn_text') != '') { ?>
		    	<div class="top-btn">
		    		<a href="<?php echo esc_url(get_theme_mod('the_computer_repair_top_btn_url',''));?>"><?php echo esc_html(get_theme_mod('the_computer_repair_top_btn_text',''));?><span class="screen-reader-text"><?php esc_html_e( 'BOOK AN APPOINTMENT','the-computer-repair' );?></span></a>
		    	</div>
		    	<?php }?>
		    </div>
		</div>
	</div>
</div>