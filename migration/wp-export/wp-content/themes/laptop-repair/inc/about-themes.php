<?php
//about theme info
add_action( 'admin_menu', 'laptop_repair_abouttheme' );
function laptop_repair_abouttheme() {    	
	add_theme_page( esc_html__('About Theme', 'laptop-repair'), esc_html__('About Theme', 'laptop-repair'), 'edit_theme_options', 'laptop_repair_guide', 'laptop_repair_mostrar_guide');   
} 
//guidline for about theme
function laptop_repair_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
?>
<div class="wrapper-info">
	<div class="col-left">
   		   <div class="col-left-area">
			  <?php esc_attr_e('Theme Information', 'laptop-repair'); ?>
		   </div>
          <p><?php esc_html_e('SKT Laptop Repair is responsive template useful for computer, mobile phones, tablet, Mac, Windows, electronics, digital, software, desktop. online support, maintenance, services, consulting, training, help desk. SEO friendly, Multilingual friendly, RTL compatible, WooCommerce friendly, multipurpose, simple, flexible and easy to use. It comes with a ready to import Elementor template plugin as add on which allows to import 63+ design templates for making use in home and other inner pages. Use it to create any type of business, personal, blog and eCommerce website.','laptop-repair'); ?></p>
		  <a href="<?php echo esc_url(LAPTOP_REPAIR_SKTTHEMES_PRO_THEME_URL); ?>"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/free-vs-pro.png" alt="" /></a>
	</div><!-- .col-left -->
	<div class="col-right">			
			<div class="centerbold">
				<hr />
				<a href="<?php echo esc_url(LAPTOP_REPAIR_SKTTHEMES_LIVE_DEMO); ?>" target="_blank"><?php esc_html_e('Live Demo', 'laptop-repair'); ?></a> | 
				<a href="<?php echo esc_url(LAPTOP_REPAIR_SKTTHEMES_PRO_THEME_URL); ?>"><?php esc_html_e('Buy Pro', 'laptop-repair'); ?></a> | 
				<a href="<?php echo esc_url(LAPTOP_REPAIR_SKTTHEMES_THEME_DOC); ?>" target="_blank"><?php esc_html_e('Documentation', 'laptop-repair'); ?></a>
                <div class="space5"></div>
				<hr />                
                <a href="<?php echo esc_url(LAPTOP_REPAIR_SKTTHEMES_THEMES); ?>" target="_blank"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/sktskill.jpg" alt="" /></a>
			</div>		
	</div><!-- .col-right -->
</div><!-- .wrapper-info -->
<?php } ?>