<?php
/**
 * The template part for header
 *
 * @package The Computer Repair 
 * @subpackage the-computer-repair
 * @since the-computer-repair 1.0
 */
?>
<?php if( get_theme_mod( 'the_computer_repair_topbar_hide_show', false) != '' || get_theme_mod( 'the_computer_repair_resp_topbar_hide_show', false) != '') { ?>
	<div class="lower-bar">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-md-3">
				</div>
				<div class="col-lg-4 col-md-4">
				    <?php if( get_theme_mod( 'the_computer_repair_location') != '') { ?>
	          			<p><i class="<?php echo esc_attr(get_theme_mod('the_computer_repair_location_icon','fas fa-map-marker-alt')); ?>"></i><?php echo esc_html(get_theme_mod('the_computer_repair_location',''));?></p>
	    			<?php } ?>
			    </div>
			    <div class="col-lg-2 col-md-2">
				    <?php if( get_theme_mod( 'the_computer_repair_call') != '') { ?>
	          			<p><i class="<?php echo esc_attr(get_theme_mod('the_computer_repair_phone_icon','fas fa-phone')); ?>"></i><a href="tel:<?php echo esc_attr( get_theme_mod('the_computer_repair_call','') ); ?>"><?php echo esc_html(get_theme_mod('the_computer_repair_call',''));?></a></p>
	        		<?php }?>
			    </div>
			    <div class="col-lg-3 col-md-3">
				    <?php if( get_theme_mod( 'the_computer_repair_email') != '') { ?>
	          			<p><i class="<?php echo esc_attr(get_theme_mod('the_computer_repair_email_address_icon','fas fa-envelope')); ?>"></i><a href="mailto:<?php echo esc_attr(get_theme_mod('the_computer_repair_email',''));?>"><?php echo esc_html(get_theme_mod('the_computer_repair_email',''));?></a></p>
	        		<?php }?>
			    </div>
			</div>
		</div>
	</div>
<?php } ?>