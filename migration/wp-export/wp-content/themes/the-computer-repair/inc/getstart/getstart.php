<?php
//about theme info
add_action( 'admin_menu', 'the_computer_repair_gettingstarted' );
function the_computer_repair_gettingstarted() {
	add_theme_page( esc_html__('About The Computer Repair', 'the-computer-repair'), esc_html__('About The Computer Repair', 'the-computer-repair'), 'edit_theme_options', 'the_computer_repair_guide', 'the_computer_repair_mostrar_guide');
}

// Add a Custom CSS file to WP Admin Area
function the_computer_repair_admin_theme_style() {
   wp_enqueue_style('the-computer-repair-custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/getstart/getstart.css');
   wp_enqueue_script('the-computer-repair-tabs', esc_url(get_template_directory_uri()) . '/inc/getstart/js/tab.js');
   wp_enqueue_style( 'font-awesome-css', esc_url(get_template_directory_uri()).'/assets/css/fontawesome-all.css' );
}
add_action('admin_enqueue_scripts', 'the_computer_repair_admin_theme_style');

//guidline for about theme
function the_computer_repair_mostrar_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'the-computer-repair' );
?>

<div class="wrapper-info">
    <div class="col-left">
    	<h2><?php esc_html_e( 'Welcome to The Computer Repair Theme', 'the-computer-repair' ); ?> <span class="version">Version: <?php echo esc_html($theme['Version']);?></span></h2>
    	<p><?php esc_html_e('All our WordPress themes are modern, minimalist, 100% responsive, seo-friendly,feature-rich, and multipurpose that best suit designers, bloggers and other professionals who are working in the creative fields.','the-computer-repair'); ?></p>
    </div>
    <div class="col-right">
    	<div class="logo">
			<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/final-logo.png" alt="" />
		</div>
		<div class="update-now">
			<h4><?php esc_html_e('Buy The Computer Repair at 20% Discount','the-computer-repair'); ?></h4>
			<h4><?php esc_html_e('Use Coupon','the-computer-repair'); ?> ( <span><?php esc_html_e('vwpro20','the-computer-repair'); ?></span> ) </h4> 
			<div class="info-link">
				<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_BUY_NOW ); ?>" target="_blank"> <?php esc_html_e( 'Upgrade to Pro', 'the-computer-repair' ); ?></a>
			</div>
		</div>
    </div>

    <div class="tab-sec">
		<div class="tab">
			<button class="tablinks" onclick="the_computer_repair_open_tab(event, 'lite_theme')"><?php esc_html_e( 'Setup With Customizer', 'the-computer-repair' ); ?></button>
			<button class="tablinks" onclick="the_computer_repair_open_tab(event, 'block_pattern')"><?php esc_html_e( 'Setup With Block Pattern', 'the-computer-repair' ); ?></button>
		  	<button class="tablinks" onclick="the_computer_repair_open_tab(event, 'gutenberg_editor')"><?php esc_html_e( 'Setup With Gutunberg Block', 'the-computer-repair' ); ?></button>
		  	<button class="tablinks" onclick="the_computer_repair_open_tab(event, 'product_addons_editor')"><?php esc_html_e( 'Woocommerce Product Addons', 'the-computer-repair' ); ?></button>
		  	<button class="tablinks" onclick="the_computer_repair_open_tab(event, 'computer_pro')"><?php esc_html_e( 'Get Premium', 'the-computer-repair' ); ?></button>
		  	<button class="tablinks" onclick="the_computer_repair_open_tab(event, 'free_pro')"><?php esc_html_e( 'Support', 'the-computer-repair' ); ?></button>
		</div>

		
		<?php
			$the_computer_repair_plugin_custom_css = '';
			if(class_exists('Ibtana_Visual_Editor_Menu_Class')){
				$the_computer_repair_plugin_custom_css ='display: block';
			}
		?>
		<div id="lite_theme" class="tabcontent open">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
				$plugin_ins = The_Computer_Repair_Plugin_Activation_Settings::get_instance();
				$the_computer_repair_actions = $plugin_ins->recommended_actions;
				?>
				<div class="the-computer-repair-recommended-plugins">
				    <div class="the-computer-repair-action-list">
				        <?php if ($the_computer_repair_actions): foreach ($the_computer_repair_actions as $key => $the_computer_repair_actionValue): ?>
				                <div class="the-computer-repair-action" id="<?php echo esc_attr($the_computer_repair_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($the_computer_repair_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($the_computer_repair_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($the_computer_repair_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" get-start-tab-id="lite-theme-tab" href="javascript:void(0);"><?php esc_html_e('Skip','the-computer-repair'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="lite-theme-tab" style="<?php echo esc_attr($the_computer_repair_plugin_custom_css); ?>">
				<h3><?php esc_html_e( 'Lite Theme Information', 'the-computer-repair' ); ?></h3>
				<hr class="h3hr">
			  	<p><?php esc_html_e('Computer repair is a WP theme in high demand as of today and has been extremely beneficial since its inception in the market for the computer repair and services. It is responsive to the core with multipurpose capabilities and is a fine tool for computer, mobile phones, tablet, Mac, Windows, electronics, digital, software, desktop. online support, maintenance, services, consulting, training, help desk, IT Solutions, Infrastructure, software cleaning, junk removal, renovation, cleaning, computer, digital, repair, handyman. Because of the features like user friendliness, CTA, personalization options, testimonial sections, SEO friendliness, optimised codes, retina ready, Bootstrap framework and much more, it is a much demandable theme for the electronics fixing services as well as cellular repair center. Computer repair is interactive with customization options and secondly it is translation ready making it a preferable choice for the laptop repair service companies and for the companies that are into the business of home repair. If such companies want the online presence and excellence in business, Computer repair theme will play a good role in this. It is good for all repairs like smart phone repair, desktop repair, iPad repair, tablet repair, printer repair, data recovery and game console repair. It is very professional, clean and possesses design of high quality.','the-computer-repair'); ?></p>
			  	<div class="col-left-inner">
			  		<h4><?php esc_html_e( 'Theme Documentation', 'the-computer-repair' ); ?></h4>
					<p><?php esc_html_e( 'If you need any assistance regarding setting up and configuring the Theme, our documentation is there.', 'the-computer-repair' ); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_FREE_THEME_DOC ); ?>" target="_blank"> <?php esc_html_e( 'Documentation', 'the-computer-repair' ); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Theme Customizer', 'the-computer-repair'); ?></h4>
					<p> <?php esc_html_e('To begin customizing your website, start by clicking "Customize".', 'the-computer-repair'); ?></p>
					<div class="info-link">
						<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e('Customizing', 'the-computer-repair'); ?></a>
					</div>
					<hr>				
					<h4><?php esc_html_e('Having Trouble, Need Support?', 'the-computer-repair'); ?></h4>
					<p> <?php esc_html_e('Our dedicated team is well prepared to help you out in case of queries and doubts regarding our theme.', 'the-computer-repair'); ?></p>
					<div class="info-link">
						<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'the-computer-repair'); ?></a>
					</div>
					<hr>
					<h4><?php esc_html_e('Reviews & Testimonials', 'the-computer-repair'); ?></h4>
					<p> <?php esc_html_e('All the features and aspects of this WordPress Theme are phenomenal. I\'d recommend this theme to all.', 'the-computer-repair'); ?>  </p>
					<div class="info-link">
						<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_REVIEW ); ?>" target="_blank"><?php esc_html_e('Reviews', 'the-computer-repair'); ?></a>
					</div>
			  		<div class="link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'the-computer-repair' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','the-computer-repair'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-slides"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_slidersettings') ); ?>" target="_blank"><?php esc_html_e('Slider Settings','the-computer-repair'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_topbar') ); ?>" target="_blank"><?php esc_html_e('Topbar Settings','the-computer-repair'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-editor-table"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_services_section') ); ?>" target="_blank"><?php esc_html_e('Our Services','the-computer-repair'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','the-computer-repair'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','the-computer-repair'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','the-computer-repair'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','the-computer-repair'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','the-computer-repair'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','the-computer-repair'); ?></a>
								</div>
							</div>
						</div>
					</div>
			  	</div>
				<div class="col-right-inner">
					<h3 class="page-template"><?php esc_html_e('How to set up Home Page Template','the-computer-repair'); ?></h3>
				  	<hr class="h3hr">
					<p><?php esc_html_e('Follow these instructions to setup Home page.','the-computer-repair'); ?></p>
	                <ul>
	                  	<p><span class="strong"><?php esc_html_e('1. Create a new page :','the-computer-repair'); ?></span><?php esc_html_e(' Go to ','the-computer-repair'); ?>
					  	<b><?php esc_html_e(' Dashboard >> Pages >> Add New Page','the-computer-repair'); ?></b></p>

	                  	<p><?php esc_html_e('Name it as "Home" then select the template "Custom Home Page".','the-computer-repair'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/home-page-template.png" alt="" />
	                  	<p><span class="strong"><?php esc_html_e('2. Set the front page:','the-computer-repair'); ?></span><?php esc_html_e(' Go to ','the-computer-repair'); ?>
					  	<b><?php esc_html_e(' Settings >> Reading ','the-computer-repair'); ?></b></p>
					  	<p><?php esc_html_e('Select the option of Static Page, now select the page you created to be the homepage, while another page to be your default page.','the-computer-repair'); ?></p>
	                  	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/set-front-page.png" alt="" />
	                  	<p><?php esc_html_e(' Once you are done with this, then follow the','the-computer-repair'); ?> <a class="doc-links" href="https://www.vwthemesdemo.com/docs/free-computer-repair/" target="_blank"><?php esc_html_e('Documentation','the-computer-repair'); ?></a></p>
	                </ul>
			  	</div>
			</div>
		</div>	

		<div id="block_pattern" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = The_Computer_Repair_Plugin_Activation_Settings::get_instance();
			$the_computer_repair_actions = $plugin_ins->recommended_actions;
			?>
				<div class="the-computer-repair-recommended-plugins">
				    <div class="the-computer-repair-action-list">
				        <?php if ($the_computer_repair_actions): foreach ($the_computer_repair_actions as $key => $the_computer_repair_actionValue): ?>
				                <div class="the-computer-repair-action" id="<?php echo esc_attr($the_computer_repair_actionValue['id']);?>">
			                        <div class="action-inner">
			                            <h3 class="action-title"><?php echo esc_html($the_computer_repair_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($the_computer_repair_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($the_computer_repair_actionValue['link']); ?>
			                            <a class="ibtana-skip-btn" href="javascript:void(0);" get-start-tab-id="gutenberg-editor-tab"><?php esc_html_e('Skip','the-computer-repair'); ?></a>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php } ?>
			<div class="gutenberg-editor-tab" style="<?php echo esc_attr($the_computer_repair_plugin_custom_css); ?>">
				<div class="block-pattern-img">
				  	<h3><?php esc_html_e( 'Block Patterns', 'the-computer-repair' ); ?></h3>
					<hr class="h3hr">
					<p><?php esc_html_e('Follow the below instructions to setup Home page with Block Patterns.','the-computer-repair'); ?></p>
	              	<p><b><?php esc_html_e('Click on Below Add new page button >> Click on "+" Icon >> Click Pattern Tab >> Click on homepage sections >> Publish.','the-computer-repair'); ?></span></b></p>
	              	<div class="the-computer-repair-pattern-page">
				    	<a href="javascript:void(0)" class="vw-pattern-page-btn button-primary button"><?php esc_html_e('Add New Page','the-computer-repair'); ?></a>
				    </div>
	              	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/block-pattern.png" alt="" />
	            </div>

              	<div class="block-pattern-link-customizer">
						<h3><?php esc_html_e( 'Link to customizer', 'the-computer-repair' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','the-computer-repair'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-networking"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_social_icon_settings') ); ?>" target="_blank"><?php esc_html_e('Social Icons','the-computer-repair'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','the-computer-repair'); ?></a>
								</div>
								
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','the-computer-repair'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','the-computer-repair'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','the-computer-repair'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','the-computer-repair'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','the-computer-repair'); ?></a>
								</div> 
							</div>
						</div>
				</div>					
	        </div>
		</div>

		<div id="gutenberg_editor" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = The_Computer_Repair_Plugin_Activation_Settings::get_instance();
			$the_computer_repair_actions = $plugin_ins->recommended_actions;
			?>
				<div class="the-computer-repair-recommended-plugins">
				    <div class="the-computer-repair-action-list">
				        <?php if ($the_computer_repair_actions): foreach ($the_computer_repair_actions as $key => $the_computer_repair_actionValue): ?>
				                <div class="the-computer-repair-action" id="<?php echo esc_attr($the_computer_repair_actionValue['id']);?>">
			                        <div class="action-inner plugin-activation-redirect">
			                            <h3 class="action-title"><?php echo esc_html($the_computer_repair_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($the_computer_repair_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($the_computer_repair_actionValue['link']); ?>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Gutunberg Blocks', 'the-computer-repair' ); ?></h3>
				<hr class="h3hr">
				<div class="the-computer-repair-pattern-page">
			    	<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Ibtana Settings','the-computer-repair'); ?></a>
			    </div>

			    <div class="link-customizer-with-guternberg-ibtana">
						<h3><?php esc_html_e( 'Link to customizer', 'the-computer-repair' ); ?></h3>
						<hr class="h3hr">
						<div class="first-row">
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','the-computer-repair'); ?></a>
								</div>
								<div class="row-box2">
									<span class="dashicons dashicons-networking"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_social_icon_settings') ); ?>" target="_blank"><?php esc_html_e('Social Icons','the-computer-repair'); ?></a>
								</div>
							</div>
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','the-computer-repair'); ?></a>
								</div>
								
								<div class="row-box2">
									<span class="dashicons dashicons-text-page"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_footer') ); ?>" target="_blank"><?php esc_html_e('Footer Text','the-computer-repair'); ?></a>
								</div>
							</div>

							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-format-gallery"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_post_settings') ); ?>" target="_blank"><?php esc_html_e('Post settings','the-computer-repair'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_woocommerce_section') ); ?>" target="_blank"><?php esc_html_e('WooCommerce Layout','the-computer-repair'); ?></a>
								</div> 
							</div>
							
							<div class="row-box">
								<div class="row-box1">
									<span class="dashicons dashicons-admin-generic"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=the_computer_repair_left_right') ); ?>" target="_blank"><?php esc_html_e('General Settings','the-computer-repair'); ?></a>
								</div>
								 <div class="row-box2">
									<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','the-computer-repair'); ?></a>
								</div> 
							</div>
						</div>
				</div>
			<?php } ?>
		</div>

		<div id="product_addons_editor" class="tabcontent">
			<?php if(!class_exists('Ibtana_Visual_Editor_Menu_Class')){ 
			$plugin_ins = The_Computer_Repair_Plugin_Activation_Settings::get_instance();
			$the_computer_repair_actions = $plugin_ins->recommended_actions;
			?>
				<div class="the-computer-repair-recommended-plugins">
				    <div class="the-computer-repair-action-list">
				        <?php if ($the_computer_repair_actions): foreach ($the_computer_repair_actions as $key => $the_computer_repair_actionValue): ?>
				                <div class="the-computer-repair-action" id="<?php echo esc_attr($the_computer_repair_actionValue['id']);?>">
			                        <div class="action-inner plugin-activation-redirect">
			                            <h3 class="action-title"><?php echo esc_html($the_computer_repair_actionValue['title']); ?></h3>
			                            <div class="action-desc"><?php echo esc_html($the_computer_repair_actionValue['desc']); ?></div>
			                            <?php echo wp_kses_post($the_computer_repair_actionValue['link']); ?>
			                        </div>
				                </div>
				            <?php endforeach;
				        endif; ?>
				    </div>
				</div>
			<?php }else{ ?>
				<h3><?php esc_html_e( 'Woocommerce Products Blocks', 'the-computer-repair' ); ?></h3>
				<hr class="h3hr">
				<div class="the-computer-repair-pattern-page">
					<p><?php esc_html_e('Follow the below instructions to setup Products Templates.','the-computer-repair'); ?></p>
					<p><b><?php esc_html_e('1. First you need to activate these plugins','the-computer-repair'); ?></b></p>
						<p><?php esc_html_e('1. Ibtana - WordPress Website Builder ','the-computer-repair'); ?></p>
						<p><?php esc_html_e('2. Ibtana - Ecommerce Product Addons.','the-computer-repair'); ?></p>
						<p><?php esc_html_e('3. Woocommerce','the-computer-repair'); ?></p>

	              	<div class="the-computer-repair-pattern-page">
			    			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ibtana-visual-editor-templates&woo=true&ive_wizard_view=parent' ) ); ?>" class="vw-pattern-page-btn ibtana-dashboard-page-btn button-primary button"><?php esc_html_e('Woocommerce Templates','the-computer-repair'); ?></a>
			    		</div>
			    </div>
			<?php } ?>
		</div>

		<div id="computer_pro" class="tabcontent">
		  	<h3><?php esc_html_e( 'Premium Theme Information', 'the-computer-repair' ); ?></h3>
			<hr class="h3hr">
		    <div class="col-left-pro">
		    	<p><?php esc_html_e('Get the Computer Repair WordPress theme from us at affordable price and this is a very good step for the startup or for any kind of established business related to the Computer Repair or precious or semi-precious metals that include gold and diamonds. One of the best things about this theme is that it is responsive to the core apart from being multipurpose and all this a significant choice to take the Computer Repair business on the path of global expansion. With significant features like CTA, Bootstrap framework, interactive nature as well as clean code, Computer Repair WordPress theme is a preferred choice for making the gift shop or a Computer Repair store with the potential to grow. The best part of this WP theme is that it can be used for any type of online store and not just related to Computer Repair. It is also good for the online store for fashion.','the-computer-repair'); ?></p>
		    	<div class="pro-links">
			    	<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'the-computer-repair'); ?></a>
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Pro', 'the-computer-repair'); ?></a>
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Documentation', 'the-computer-repair'); ?></a>
				</div>
		    </div>
		    <div class="col-right-pro">
		    	<img src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/getstart/images/responsive.png" alt="" />
		    </div>
		    <div class="featurebox">
			    <h3><?php esc_html_e( 'Theme Features', 'the-computer-repair' ); ?></h3>
				<hr class="h3hr">
				<div class="table-image">
					<table class="tablebox">
						<thead>
							<tr>
								<th></th>
								<th><?php esc_html_e('Free Themes', 'the-computer-repair'); ?></th>
								<th><?php esc_html_e('Premium Themes', 'the-computer-repair'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e('Theme Customization', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Responsive Design', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Logo Upload', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Social Media Links', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Slider Settings', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Number of Slides', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('4', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('Unlimited', 'the-computer-repair'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Template Pages', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('3', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('6', 'the-computer-repair'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Home Page Template', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('1', 'the-computer-repair'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Theme sections', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('2', 'the-computer-repair'); ?></td>
								<td class="table-img"><?php esc_html_e('15', 'the-computer-repair'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Contact us Page Template', 'the-computer-repair'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('1', 'the-computer-repair'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Blog Templates & Layout', 'the-computer-repair'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('3(Full width/Left/Right Sidebar)', 'the-computer-repair'); ?></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Page Templates & Layout', 'the-computer-repair'); ?></td>
								<td class="table-img">0</td>
								<td class="table-img"><?php esc_html_e('2(Left/Right Sidebar)', 'the-computer-repair'); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Color Pallete For Particular Sections', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Global Color Option', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Reordering', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Demo Importer', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Allow To Set Site Title, Tagline, Logo', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Enable Disable Options On All Sections, Logo', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Full Documentation', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Latest WordPress Compatibility', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Woo-Commerce Compatibility', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Support 3rd Party Plugins', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Secure and Optimized Code', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Exclusive Functionalities', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Section Enable / Disable', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Section Google Font Choices', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Gallery', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Simple & Mega Menu Option', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Support to add custom CSS / JS ', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Shortcodes', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Background, Colors, Header, Logo & Menu', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Premium Membership', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Budget Friendly Value', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('Priority Error Fixing', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Custom Feature Addition', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr class="odd">
								<td><?php esc_html_e('All Access Theme Pass', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td><?php esc_html_e('Seamless Customer Support', 'the-computer-repair'); ?></td>
								<td class="table-img"><i class="fas fa-times"></i></td>
								<td class="table-img"><i class="fas fa-check"></i></td>
							</tr>
							<tr>
								<td></td>
								<td class="table-img"></td>
								<td class="update-link"><a href="<?php echo esc_url( THE_COMPUTER_REPAIR_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade to Pro', 'the-computer-repair'); ?></a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="free_pro" class="tabcontent">
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-star-filled"></span><?php esc_html_e('Pro Version', 'the-computer-repair'); ?></h4>
				<p> <?php esc_html_e('To gain access to extra theme options and more interesting features, upgrade to pro version.', 'the-computer-repair'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Get Pro', 'the-computer-repair'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-cart"></span><?php esc_html_e('Pre-purchase Queries', 'the-computer-repair'); ?></h4>
				<p> <?php esc_html_e('If you have any pre-sale query, we are prepared to resolve it.', 'the-computer-repair'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_CONTACT ); ?>" target="_blank"><?php esc_html_e('Question', 'the-computer-repair'); ?></a>
				</div>
		  	</div>
		  	<div class="col-3">		  		
		  		<h4><span class="dashicons dashicons-admin-customizer"></span><?php esc_html_e('Child Theme', 'the-computer-repair'); ?></h4>
				<p> <?php esc_html_e('For theme file customizations, make modifications in the child theme and not in the main theme file.', 'the-computer-repair'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_CHILD_THEME ); ?>" target="_blank"><?php esc_html_e('About Child Theme', 'the-computer-repair'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e('Frequently Asked Questions', 'the-computer-repair'); ?></h4>
				<p> <?php esc_html_e('We have gathered top most, frequently asked questions and answered them for your easy understanding. We will list down more as we get new challenging queries. Check back often.', 'the-computer-repair'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_FAQ ); ?>" target="_blank"><?php esc_html_e('View FAQ','the-computer-repair'); ?></a>
				</div>
		  	</div>

		  	<div class="col-3">
		  		<h4><span class="dashicons dashicons-sos"></span><?php esc_html_e('Support Queries', 'the-computer-repair'); ?></h4>
				<p> <?php esc_html_e('If you have any queries after purchase, you can contact us. We are eveready to help you out.', 'the-computer-repair'); ?></p>
				<div class="info-link">
					<a href="<?php echo esc_url( THE_COMPUTER_REPAIR_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Contact Us', 'the-computer-repair'); ?></a>
				</div>
		  	</div>
		</div>
	</div>
</div>
<?php } ?>