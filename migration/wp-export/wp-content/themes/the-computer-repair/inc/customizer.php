<?php
/**
 * The Computer Repair Theme Customizer
 *
 * @package The Computer Repair
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

function the_computer_repair_custom_controls() {
	load_template( trailingslashit( get_template_directory() ) . '/inc/custom-controls.php' );
}
add_action( 'customize_register', 'the_computer_repair_custom_controls' );

function the_computer_repair_customize_register( $wp_customize ) {

	load_template( trailingslashit( get_template_directory() ) . '/inc/icon-picker.php' );

	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage'; 
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial( 'blogname', array( 
		'selector' => '.logo .site-title a', 
	 	'render_callback' => 'the_computer_repair_customize_partial_blogname', 
	)); 

	$wp_customize->selective_refresh->add_partial( 'blogdescription', array( 
		'selector' => 'p.site-description', 
		'render_callback' => 'the_computer_repair_customize_partial_blogdescription', 
	));

	$TheComputerRepairParentPanel = new The_Computer_Repair_WP_Customize_Panel( $wp_customize, 'the_computer_repair_panel_id', array(
		'capability' => 'edit_theme_options',
		'theme_supports' => '',
		'title' => esc_html__( 'VW Settings', 'the-computer-repair' ),
		'priority' => 10,
	));

	// Layout
	$wp_customize->add_section( 'the_computer_repair_left_right', array(
    	'title'      => esc_html__( 'General Settings', 'the-computer-repair' ),
		'panel' => 'the_computer_repair_panel_id'
	) );

	$wp_customize->add_setting('the_computer_repair_width_option',array(
        'default' => 'Full Width',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control(new The_Computer_Repair_Image_Radio_Control($wp_customize, 'the_computer_repair_width_option', array(
        'type' => 'select',
        'label' => __('Width Layouts','the-computer-repair'),
        'description' => __('Here you can change the width layout of Website.','the-computer-repair'),
        'section' => 'the_computer_repair_left_right',
        'choices' => array(
            'Full Width' => esc_url(get_template_directory_uri()).'/assets/images/full-width.png',
            'Wide Width' => esc_url(get_template_directory_uri()).'/assets/images/wide-width.png',
            'Boxed' => esc_url(get_template_directory_uri()).'/assets/images/boxed-width.png',
    ))));

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('the_computer_repair_theme_options',array(
        'default' => 'Right Sidebar',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'	        
	) );
	$wp_customize->add_control('the_computer_repair_theme_options', array(
        'type' => 'select',
        'label' => __('Post Sidebar Layout','the-computer-repair'),
        'description' => __('Here you can change the sidebar layout for posts. ','the-computer-repair'),
        'section' => 'the_computer_repair_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','the-computer-repair'),
            'Right Sidebar' => __('Right Sidebar','the-computer-repair'),
            'One Column' => __('One Column','the-computer-repair'),
            'Three Columns' => __('Three Columns','the-computer-repair'),
            'Four Columns' => __('Four Columns','the-computer-repair'),
            'Grid Layout' => __('Grid Layout','the-computer-repair')
        ),
	));

	$wp_customize->add_setting('the_computer_repair_page_layout',array(
        'default' => 'One Column',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control('the_computer_repair_page_layout',array(
        'type' => 'select',
        'label' => __('Page Sidebar Layout','the-computer-repair'),
        'description' => __('Here you can change the sidebar layout for pages. ','the-computer-repair'),
        'section' => 'the_computer_repair_left_right',
        'choices' => array(
            'Left Sidebar' => __('Left Sidebar','the-computer-repair'),
            'Right Sidebar' => __('Right Sidebar','the-computer-repair'),
            'One Column' => __('One Column','the-computer-repair')
        ),
	) );

	//Pre-Loader
	$wp_customize->add_setting( 'the_computer_repair_loader_enable',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_loader_enable',array(
        'label' => esc_html__( 'Pre-Loader','the-computer-repair' ),
        'section' => 'the_computer_repair_left_right'
    )));

	$wp_customize->add_setting('the_computer_repair_preloader_bg_color', array(
		'default'           => '#23cfca',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'the_computer_repair_preloader_bg_color', array(
		'label'    => __('Pre-Loader Background Color', 'the-computer-repair'),
		'section'  => 'the_computer_repair_left_right',
	)));

	$wp_customize->add_setting('the_computer_repair_preloader_border_color', array(
		'default'           => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color',
	));
	$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'the_computer_repair_preloader_border_color', array(
		'label'    => __('Pre-Loader Border Color', 'the-computer-repair'),
		'section'  => 'the_computer_repair_left_right',
	)));

	//Top Bar
	$wp_customize->add_section( 'the_computer_repair_topbar', array(
    	'title'      => __( 'Top Bar Settings', 'the-computer-repair' ),
		'panel' => 'the_computer_repair_panel_id'
	) );

	$wp_customize->add_setting( 'the_computer_repair_topbar_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_topbar_hide_show',
       array(
      'label' => esc_html__( 'Show / Hide Topbar','the-computer-repair' ),
      'section' => 'the_computer_repair_topbar'
    )));

    $wp_customize->add_setting('the_computer_repair_topbar_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_topbar_padding_top_bottom',array(
		'label'	=> __('Topbar Padding Top Bottom','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

    //Sticky Header
	$wp_customize->add_setting( 'the_computer_repair_sticky_header',array(
        'default' => 0,
        'transport' => 'refresh',
        'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_sticky_header',array(
        'label' => esc_html__( 'Sticky Header','the-computer-repair' ),
        'section' => 'the_computer_repair_topbar'
    )));

    $wp_customize->add_setting('the_computer_repair_sticky_header_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_sticky_header_padding',array(
		'label'	=> __('Sticky Header Padding','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_header_search',array(
    	'default' => 1,
      	'transport' => 'refresh',
      	'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_header_search',array(
      	'label' => esc_html__( 'Show / Hide Search','the-computer-repair' ),
      	'section' => 'the_computer_repair_topbar'
    )));

    $wp_customize->add_setting('the_computer_repair_search_icon',array(
		'default'	=> 'fas fa-search',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_search_icon',array(
		'label'	=> __('Add Search Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_topbar',
		'setting'	=> 'the_computer_repair_search_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('the_computer_repair_search_close_icon',array(
		'default'	=> 'fa fa-window-close',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_search_close_icon',array(
		'label'	=> __('Add Search Close Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_topbar',
		'setting'	=> 'the_computer_repair_search_close_icon',
		'type'		=> 'icon'
	)));

    $wp_customize->add_setting('the_computer_repair_search_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_search_font_size',array(
		'label'	=> __('Search Font Size','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_search_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_search_padding_top_bottom',array(
		'label'	=> __('Search Padding Top Bottom','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_search_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_search_padding_left_right',array(
		'label'	=> __('Search Padding Left Right','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_search_border_radius', array(
		'default'              => "",
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_search_border_radius', array(
		'label'       => esc_html__( 'Search Border Radius','the-computer-repair' ),
		'section'     => 'the_computer_repair_topbar',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('the_computer_repair_location', array( 
		'selector' => '.lower-bar p', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_location', 
	));

    $wp_customize->add_setting('the_computer_repair_location_icon',array(
		'default'	=> 'fas fa-map-marker-alt',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_location_icon',array(
		'label'	=> __('Add Location Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_topbar',
		'setting'	=> 'the_computer_repair_location_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('the_computer_repair_location',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_location',array(
		'label'	=> __('Add Location','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '123 Dummy street lorem ipsum, USA', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_phone_icon',array(
		'default'	=> 'fas fa-phone',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_phone_icon',array(
		'label'	=> __('Add Phone Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_topbar',
		'setting'	=> 'the_computer_repair_phone_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('the_computer_repair_call',array(
		'default'=> '',
		'sanitize_callback'	=> 'the_computer_repair_sanitize_phone_number'
	));
	$wp_customize->add_control('the_computer_repair_call',array(
		'label'	=> __('Add Phone Number','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '+00 1234 567 890', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_email_address_icon',array(
		'default'	=> 'fas fa-envelope',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_email_address_icon',array(
		'label'	=> __('Add Email Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_topbar',
		'setting'	=> 'the_computer_repair_email_address_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('the_computer_repair_email',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_email'
	));
	$wp_customize->add_control('the_computer_repair_email',array(
		'label'	=> __('Add Email Address','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'example@gmail.com', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_top_btn_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_top_btn_text',array(
		'label'	=> __('Add Button Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'FREE EVALUATION', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_top_btn_url',array(
		'default'=> '',
		'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('the_computer_repair_top_btn_url',array(
		'label'	=> __('Add Button URL','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'https://example.com/', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_topbar',
		'type'=> 'url'
	));

	$wp_customize->add_setting('the_computer_repair_cart_icon',array(
		'default'	=> 'fas fa-shopping-basket',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_cart_icon',array(
		'label'	=> __('Add Cart Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_topbar',
		'setting'	=> 'the_computer_repair_cart_icon',
		'type'		=> 'icon'
	)));
    
	//Slider
	$wp_customize->add_section( 'the_computer_repair_slidersettings' , array(
    	'title'      => __( 'Slider Settings', 'the-computer-repair' ),
		'panel' => 'the_computer_repair_panel_id'
	) );

	$wp_customize->add_setting( 'the_computer_repair_slider_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_slider_hide_show',array(
      'label' => esc_html__( 'Show / Hide Slider','the-computer-repair' ),
      'section' => 'the_computer_repair_slidersettings'
    )));

    //Selective Refresh
    $wp_customize->selective_refresh->add_partial('the_computer_repair_slider_hide_show',array(
		'selector'        => '#slider .inner_carousel h1',
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_slider_hide_show',
	));

	for ( $count = 1; $count <= 4; $count++ ) {
		$wp_customize->add_setting( 'the_computer_repair_slider_page' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'the_computer_repair_sanitize_dropdown_pages'
		) );
		$wp_customize->add_control( 'the_computer_repair_slider_page' . $count, array(
			'label'    => __( 'Select Slider Page', 'the-computer-repair' ),
			'description' => __('Slider image size (1600 x 600)','the-computer-repair'),
			'section'  => 'the_computer_repair_slidersettings',
			'type'     => 'dropdown-pages'
		) );
	}

	$wp_customize->add_setting('the_computer_repair_slider_button_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_slider_button_text',array(
		'label'	=> __('Add Slider Button Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'READ MORE', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_slidersettings',
		'type'=> 'text'
	));

	//content layout
	$wp_customize->add_setting('the_computer_repair_slider_content_option',array(
        'default' => 'Left',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control(new The_Computer_Repair_Image_Radio_Control($wp_customize, 'the_computer_repair_slider_content_option', array(
        'type' => 'select',
        'label' => __('Slider Content Layouts','the-computer-repair'),
        'section' => 'the_computer_repair_slidersettings',
        'choices' => array(
            'Left' => esc_url(get_template_directory_uri()).'/assets/images/slider-content1.png',
            'Center' => esc_url(get_template_directory_uri()).'/assets/images/slider-content2.png',
            'Right' => esc_url(get_template_directory_uri()).'/assets/images/slider-content3.png',
    ))));

    //Slider excerpt
	$wp_customize->add_setting( 'the_computer_repair_slider_excerpt_number', array(
		'default'              => 30,
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_slider_excerpt_number', array(
		'label'       => esc_html__( 'Slider Excerpt length','the-computer-repair' ),
		'section'     => 'the_computer_repair_slidersettings',
		'type'        => 'range',
		'settings'    => 'the_computer_repair_slider_excerpt_number',
		'input_attrs' => array(
			'step'             => 5,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	//Opacity
	$wp_customize->add_setting('the_computer_repair_slider_opacity_color',array(
      'default'              => 0.5,
      'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));

	$wp_customize->add_control( 'the_computer_repair_slider_opacity_color', array(
	'label'       => esc_html__( 'Slider Image Opacity','the-computer-repair' ),
	'section'     => 'the_computer_repair_slidersettings',
	'type'        => 'select',
	'settings'    => 'the_computer_repair_slider_opacity_color',
	'choices' => array(
      '0' =>  esc_attr('0','the-computer-repair'),
      '0.1' =>  esc_attr('0.1','the-computer-repair'),
      '0.2' =>  esc_attr('0.2','the-computer-repair'),
      '0.3' =>  esc_attr('0.3','the-computer-repair'),
      '0.4' =>  esc_attr('0.4','the-computer-repair'),
      '0.5' =>  esc_attr('0.5','the-computer-repair'),
      '0.6' =>  esc_attr('0.6','the-computer-repair'),
      '0.7' =>  esc_attr('0.7','the-computer-repair'),
      '0.8' =>  esc_attr('0.8','the-computer-repair'),
      '0.9' =>  esc_attr('0.9','the-computer-repair')
	),
	));

	//Slider height
	$wp_customize->add_setting('the_computer_repair_slider_height',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_slider_height',array(
		'label'	=> __('Slider Height','the-computer-repair'),
		'description'	=> __('Specify the slider height (px).','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '500px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_slidersettings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_slider_speed', array(
		'default'  => 4000,
		'sanitize_callback'	=> 'the_computer_repair_sanitize_float'
	) );
	$wp_customize->add_control( 'the_computer_repair_slider_speed', array(
		'label' => esc_html__('Slider Transition Speed','the-computer-repair'),
		'section' => 'the_computer_repair_slidersettings',
		'type'  => 'number',
	) );
 
	//Our Services section
	$wp_customize->add_section( 'the_computer_repair_services_section' , array(
    	'title'      => __( 'Our Services', 'the-computer-repair' ),
		'priority'   => null,
		'panel' => 'the_computer_repair_panel_id'
	) );

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial( 'the_computer_repair_section_title', array( 
		'selector' => '.heading-box h2', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_section_title',
	));

	$wp_customize->add_setting('the_computer_repair_section_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_section_title',array(
		'label'	=> __('Add Section Title','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'Our Services', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_services_section',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_section_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_section_text',array(
		'label'	=> __('Add Section Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'lorem ipsum is dummy text', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_services_section',
		'type'=> 'text'
	));

	$categories = get_categories();
	$cat_post = array();
	$cat_post[]= 'select';
	$i = 0;	
	foreach($categories as $category){
		if($i==0){
			$default = $category->slug;
			$i++;
		}
		$cat_post[$category->slug] = $category->name;
	}

	$wp_customize->add_setting('the_computer_repair_our_services',array(
		'default'	=> 'select',
		'sanitize_callback' => 'the_computer_repair_sanitize_choices',
	));
	$wp_customize->add_control('the_computer_repair_our_services',array(
		'type'    => 'select',
		'choices' => $cat_post,
		'label' => __('Select Category to display Services','the-computer-repair'),
		'description' => __('Image Size (280 x 180)','the-computer-repair'),
		'section' => 'the_computer_repair_services_section',
	));

	$wp_customize->add_setting('the_computer_repair_services_button_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_services_button_text',array(
		'label'	=> __('Add Services Button Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'READ MORE', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_services_section',
		'type'=> 'text'
	));

	//Blog Post
	$wp_customize->add_panel( $TheComputerRepairParentPanel );

	$BlogPostParentPanel = new The_Computer_Repair_WP_Customize_Panel( $wp_customize, 'blog_post_parent_panel', array(
		'title' => __( 'Blog Post Settings', 'the-computer-repair' ),
		'panel' => 'the_computer_repair_panel_id',
	));

	$wp_customize->add_panel( $BlogPostParentPanel );

	// Add example section and controls to the middle (second) panel
	$wp_customize->add_section( 'the_computer_repair_post_settings', array(
		'title' => __( 'Post Settings', 'the-computer-repair' ),
		'panel' => 'blog_post_parent_panel',
	));

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('the_computer_repair_toggle_postdate', array( 
		'selector' => '.post-main-box h2 a', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_toggle_postdate', 
	));

	$wp_customize->add_setting( 'the_computer_repair_toggle_postdate',array(
        'default' => 1,
        'transport' => 'refresh',
        'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_toggle_postdate',array(
        'label' => esc_html__( 'Post Date','the-computer-repair' ),
        'section' => 'the_computer_repair_post_settings'
    )));

    $wp_customize->add_setting( 'the_computer_repair_toggle_author',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_toggle_author',array(
		'label' => esc_html__( 'Author','the-computer-repair' ),
		'section' => 'the_computer_repair_post_settings'
    )));

    $wp_customize->add_setting( 'the_computer_repair_toggle_comments',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_toggle_comments',array(
		'label' => esc_html__( 'Comments','the-computer-repair' ),
		'section' => 'the_computer_repair_post_settings'
    )));

    $wp_customize->add_setting( 'the_computer_repair_toggle_time',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_toggle_time',array(
		'label' => esc_html__( 'Time','the-computer-repair' ),
		'section' => 'the_computer_repair_post_settings'
    )));

    $wp_customize->add_setting( 'the_computer_repair_excerpt_number', array(
		'default'              => 30,
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_excerpt_number', array(
		'label'       => esc_html__( 'Excerpt length','the-computer-repair' ),
		'section'     => 'the_computer_repair_post_settings',
		'type'        => 'range',
		'settings'    => 'the_computer_repair_excerpt_number',
		'input_attrs' => array(
			'step'             => 5,
			'min'              => 0,
			'max'              => 50,
		),
	) );

	//Blog layout
    $wp_customize->add_setting('the_computer_repair_blog_layout_option',array(
        'default' => 'Default',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
    ));
    $wp_customize->add_control(new The_Computer_Repair_Image_Radio_Control($wp_customize, 'the_computer_repair_blog_layout_option', array(
        'type' => 'select',
        'label' => __('Blog Layouts','the-computer-repair'),
        'section' => 'the_computer_repair_post_settings',
        'choices' => array(
            'Default' => esc_url(get_template_directory_uri()).'/assets/images/blog-layout1.png',
            'Center' => esc_url(get_template_directory_uri()).'/assets/images/blog-layout2.png',
            'Left' => esc_url(get_template_directory_uri()).'/assets/images/blog-layout3.png',
    ))));

    $wp_customize->add_setting('the_computer_repair_excerpt_settings',array(
        'default' => 'Excerpt',
        'transport' => 'refresh',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control('the_computer_repair_excerpt_settings',array(
        'type' => 'select',
        'label' => __('Post Content','the-computer-repair'),
        'section' => 'the_computer_repair_post_settings',
        'choices' => array(
        	'Content' => __('Content','the-computer-repair'),
            'Excerpt' => __('Excerpt','the-computer-repair'),
            'No Content' => __('No Content','the-computer-repair')
        ),
	) );

	$wp_customize->add_setting('the_computer_repair_excerpt_suffix',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_excerpt_suffix',array(
		'label'	=> __('Add Excerpt Suffix','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '[...]', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_post_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_blog_pagination_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_blog_pagination_hide_show',array(
      'label' => esc_html__( 'Show / Hide Blog Pagination','the-computer-repair' ),
      'section' => 'the_computer_repair_post_settings'
    )));

	$wp_customize->add_setting( 'the_computer_repair_blog_pagination_type', array(
        'default'			=> 'blog-page-numbers',
        'sanitize_callback'	=> 'the_computer_repair_sanitize_choices'
    ));
    $wp_customize->add_control( 'the_computer_repair_blog_pagination_type', array(
        'section' => 'the_computer_repair_post_settings',
        'type' => 'select',
        'label' => __( 'Blog Pagination', 'the-computer-repair' ),
        'choices'		=> array(
            'blog-page-numbers'  => __( 'Numeric', 'the-computer-repair' ),
            'next-prev' => __( 'Older Posts/Newer Posts', 'the-computer-repair' ),
    )));

    // Button Settings
	$wp_customize->add_section( 'the_computer_repair_button_settings', array(
		'title' => __( 'Button Settings', 'the-computer-repair' ),
		'panel' => 'blog_post_parent_panel',
	));

	$wp_customize->add_setting('the_computer_repair_button_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_button_padding_top_bottom',array(
		'label'	=> __('Padding Top Bottom','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_button_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_button_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_button_padding_left_right',array(
		'label'	=> __('Padding Left Right','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_button_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_button_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_button_border_radius', array(
		'label'       => esc_html__( 'Button Border Radius','the-computer-repair' ),
		'section'     => 'the_computer_repair_button_settings',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('the_computer_repair_button_text', array( 
		'selector' => '.post-main-box .more-btn a', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_button_text', 
	));

	$wp_customize->add_setting('the_computer_repair_button_text',array(
		'default'=> esc_html__( 'READ MORE', 'the-computer-repair' ),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_button_text',array(
		'label'	=> __('Add Button Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'READ MORE', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_button_settings',
		'type'=> 'text'
	));

	// Related Post Settings
	$wp_customize->add_section( 'the_computer_repair_related_posts_settings', array(
		'title' => __( 'Related Posts Settings', 'the-computer-repair' ),
		'panel' => 'blog_post_parent_panel',
	));

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('the_computer_repair_related_post_title', array( 
		'selector' => '.related-post h3', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_related_post_title', 
	));

    $wp_customize->add_setting( 'the_computer_repair_related_post',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_related_post',array(
		'label' => esc_html__( 'Related Post','the-computer-repair' ),
		'section' => 'the_computer_repair_related_posts_settings'
    )));

    $wp_customize->add_setting('the_computer_repair_related_post_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_related_post_title',array(
		'label'	=> __('Add Related Post Title','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'Related Post', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_related_posts_settings',
		'type'=> 'text'
	));

   	$wp_customize->add_setting('the_computer_repair_related_posts_count',array(
		'default'=> '3',
		'sanitize_callback'	=> 'the_computer_repair_sanitize_float'
	));
	$wp_customize->add_control('the_computer_repair_related_posts_count',array(
		'label'	=> __('Add Related Post Count','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '3', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_related_posts_settings',
		'type'=> 'number'
	));

	// Single Posts Settings
	$wp_customize->add_section( 'the_computer_repair_single_blog_settings', array(
		'title' => __( 'Single Post Settings', 'the-computer-repair' ),
		'panel' => 'blog_post_parent_panel',
	));

	 $wp_customize->add_setting( 'the_computer_repair_toggle_tags',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
	));
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_toggle_tags', array(
		'label' => esc_html__( 'Tags','the-computer-repair' ),
		'section' => 'the_computer_repair_single_blog_settings'
    )));

	$wp_customize->add_setting( 'the_computer_repair_single_blog_post_navigation_show_hide',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
	));
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_single_blog_post_navigation_show_hide', array(
		'label' => esc_html__( 'Post Navigation','the-computer-repair' ),
		'section' => 'the_computer_repair_single_blog_settings'
    )));

	//navigation text
	$wp_customize->add_setting('the_computer_repair_single_blog_prev_navigation_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_single_blog_prev_navigation_text',array(
		'label'	=> __('Post Navigation Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'PREVIOUS', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_single_blog_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_single_blog_next_navigation_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_single_blog_next_navigation_text',array(
		'label'	=> __('Post Navigation Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'NEXT', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_single_blog_settings',
		'type'=> 'text'
	));

    //404 Page Setting
	$wp_customize->add_section('the_computer_repair_404_page',array(
		'title'	=> __('404 Page Settings','the-computer-repair'),
		'panel' => 'the_computer_repair_panel_id',
	));	

	$wp_customize->add_setting('the_computer_repair_404_page_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));

	$wp_customize->add_control('the_computer_repair_404_page_title',array(
		'label'	=> __('Add Title','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '404 Not Found', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_404_page',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_404_page_content',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));

	$wp_customize->add_control('the_computer_repair_404_page_content',array(
		'label'	=> __('Add Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'Looks like you have taken a wrong turn, Dont worry, it happens to the best of us.', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_404_page',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_404_page_button_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_404_page_button_text',array(
		'label'	=> __('Add Button Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'Go Back', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_404_page',
		'type'=> 'text'
	));

	//Social Icon Setting
	$wp_customize->add_section('the_computer_repair_social_icon_settings',array(
		'title'	=> __('Social Icons Settings','the-computer-repair'),
		'panel' => 'the_computer_repair_panel_id',
	));	

	$wp_customize->add_setting('the_computer_repair_social_icon_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_social_icon_font_size',array(
		'label'	=> __('Icon Font Size','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_social_icon_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_social_icon_padding',array(
		'label'	=> __('Icon Padding','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_social_icon_width',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_social_icon_width',array(
		'label'	=> __('Icon Width','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_social_icon_height',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_social_icon_height',array(
		'label'	=> __('Icon Height','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_social_icon_settings',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_social_icon_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_social_icon_border_radius', array(
		'label'       => esc_html__( 'Icon Border Radius','the-computer-repair' ),
		'section'     => 'the_computer_repair_social_icon_settings',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Responsive Media Settings
	$wp_customize->add_section('the_computer_repair_responsive_media',array(
		'title'	=> __('Responsive Media','the-computer-repair'),
		'panel' => 'the_computer_repair_panel_id',
	));

	$wp_customize->add_setting( 'the_computer_repair_resp_topbar_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_resp_topbar_hide_show',array(
      'label' => esc_html__( 'Show / Hide Topbar','the-computer-repair' ),
      'section' => 'the_computer_repair_responsive_media'
    )));

    $wp_customize->add_setting( 'the_computer_repair_stickyheader_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_stickyheader_hide_show',array(
      'label' => esc_html__( 'Sticky Header','the-computer-repair' ),
      'section' => 'the_computer_repair_responsive_media'
    )));

    $wp_customize->add_setting( 'the_computer_repair_resp_slider_hide_show',array(
      'default' => 0,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_resp_slider_hide_show',array(
      'label' => esc_html__( 'Show / Hide Slider','the-computer-repair' ),
      'section' => 'the_computer_repair_responsive_media'
    )));

    $wp_customize->add_setting( 'the_computer_repair_sidebar_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_sidebar_hide_show',array(
      'label' => esc_html__( 'Show / Hide Sidebar','the-computer-repair' ),
      'section' => 'the_computer_repair_responsive_media'
    )));

     $wp_customize->add_setting( 'the_computer_repair_resp_scroll_top_hide_show',array(
      'default' => 1,
      'transport' => 'refresh',
      'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_resp_scroll_top_hide_show',array(
      'label' => esc_html__( 'Show / Hide Scroll To Top','the-computer-repair' ),
      'section' => 'the_computer_repair_responsive_media'
    )));

    $wp_customize->add_setting('the_computer_repair_res_open_menu_icon',array(
		'default'	=> 'fas fa-bars',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_res_open_menu_icon',array(
		'label'	=> __('Add Open Menu Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_responsive_media',
		'setting'	=> 'the_computer_repair_res_open_menu_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('the_computer_repair_res_close_menu_icon',array(
		'default'	=> 'fas fa-times',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_res_close_menu_icon',array(
		'label'	=> __('Add Close Menu Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_responsive_media',
		'setting'	=> 'the_computer_repair_res_close_menu_icon',
		'type'		=> 'icon'
	)));

	//Footer Text
	$wp_customize->add_section('the_computer_repair_footer',array(
		'title'	=> __('Footer Settings','the-computer-repair'),
		'panel' => 'the_computer_repair_panel_id',
	));	

	//Selective Refresh
	$wp_customize->selective_refresh->add_partial('the_computer_repair_footer_text', array( 
		'selector' => '#footer-2 .copyright p', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_footer_text', 
	));
	
	$wp_customize->add_setting('the_computer_repair_footer_text',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control('the_computer_repair_footer_text',array(
		'label'	=> __('Copyright Text','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( 'Copyright 2019, .....', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_footer',
		'type'=> 'text'
	));	

	$wp_customize->add_setting('the_computer_repair_copyright_alingment',array(
        'default' => 'center',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control(new The_Computer_Repair_Image_Radio_Control($wp_customize, 'the_computer_repair_copyright_alingment', array(
        'type' => 'select',
        'label' => __('Copyright Alignment','the-computer-repair'),
        'section' => 'the_computer_repair_footer',
        'settings' => 'the_computer_repair_copyright_alingment',
        'choices' => array(
            'left' => esc_url(get_template_directory_uri()).'/assets/images/copyright1.png',
            'center' => esc_url(get_template_directory_uri()).'/assets/images/copyright2.png',
            'right' => esc_url(get_template_directory_uri()).'/assets/images/copyright3.png'
    ))));

    $wp_customize->add_setting('the_computer_repair_copyright_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_copyright_padding_top_bottom',array(
		'label'	=> __('Copyright Padding Top Bottom','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_hide_show_scroll',array(
    	'default' => 1,
      	'transport' => 'refresh',
      	'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ));  
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_hide_show_scroll',array(
      	'label' => esc_html__( 'Show / Hide Scroll To Top','the-computer-repair' ),
      	'section' => 'the_computer_repair_footer'
    )));

    //Selective Refresh
	$wp_customize->selective_refresh->add_partial('the_computer_repair_scroll_to_top_icon', array( 
		'selector' => '.scrollup i', 
		'render_callback' => 'the_computer_repair_customize_partial_the_computer_repair_scroll_to_top_icon', 
	));

    $wp_customize->add_setting('the_computer_repair_scroll_to_top_icon',array(
		'default'	=> 'fas fa-long-arrow-alt-up',
		'sanitize_callback'	=> 'sanitize_text_field'
	));	
	$wp_customize->add_control(new The_Computer_Repair_Fontawesome_Icon_Chooser(
        $wp_customize,'the_computer_repair_scroll_to_top_icon',array(
		'label'	=> __('Add Scroll to Top Icon','the-computer-repair'),
		'transport' => 'refresh',
		'section'	=> 'the_computer_repair_footer',
		'setting'	=> 'the_computer_repair_scroll_to_top_icon',
		'type'		=> 'icon'
	)));

	$wp_customize->add_setting('the_computer_repair_scroll_to_top_font_size',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_scroll_to_top_font_size',array(
		'label'	=> __('Icon Font Size','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_scroll_to_top_padding',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_scroll_to_top_padding',array(
		'label'	=> __('Icon Top Bottom Padding','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_scroll_to_top_width',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_scroll_to_top_width',array(
		'label'	=> __('Icon Width','the-computer-repair'),
		'description'	=> __('Enter a value in pixels Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_scroll_to_top_height',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_scroll_to_top_height',array(
		'label'	=> __('Icon Height','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_footer',
		'type'=> 'text'
	));

	$wp_customize->add_setting( 'the_computer_repair_scroll_to_top_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_scroll_to_top_border_radius', array(
		'label'       => esc_html__( 'Icon Border Radius','the-computer-repair' ),
		'section'     => 'the_computer_repair_footer',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	$wp_customize->add_setting('the_computer_repair_scroll_top_alignment',array(
        'default' => 'Right',
        'sanitize_callback' => 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control(new The_Computer_Repair_Image_Radio_Control($wp_customize, 'the_computer_repair_scroll_top_alignment', array(
        'type' => 'select',
        'label' => __('Scroll To Top','the-computer-repair'),
        'section' => 'the_computer_repair_footer',
        'settings' => 'the_computer_repair_scroll_top_alignment',
        'choices' => array(
            'Left' => esc_url(get_template_directory_uri()).'/assets/images/layout1.png',
            'Center' => esc_url(get_template_directory_uri()).'/assets/images/layout2.png',
            'Right' => esc_url(get_template_directory_uri()).'/assets/images/layout3.png'
    ))));

    //Woocommerce settings
	$wp_customize->add_section('the_computer_repair_woocommerce_section', array(
		'title'    => __('WooCommerce Layout', 'the-computer-repair'),
		'priority' => null,
		'panel'    => 'woocommerce',
	));

    //Woocommerce Shop Page Sidebar
	$wp_customize->add_setting( 'the_computer_repair_woocommerce_shop_page_sidebar',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_woocommerce_shop_page_sidebar',array(
		'label' => esc_html__( 'Shop Page Sidebar','the-computer-repair' ),
		'section' => 'the_computer_repair_woocommerce_section'
    )));

    //Woocommerce Single Product page Sidebar
	$wp_customize->add_setting( 'the_computer_repair_woocommerce_single_product_page_sidebar',array(
		'default' => 1,
		'transport' => 'refresh',
		'sanitize_callback' => 'the_computer_repair_switch_sanitization'
    ) );
    $wp_customize->add_control( new The_Computer_Repair_Toggle_Switch_Custom_Control( $wp_customize, 'the_computer_repair_woocommerce_single_product_page_sidebar',array(
		'label' => esc_html__( 'Single Product Sidebar','the-computer-repair' ),
		'section' => 'the_computer_repair_woocommerce_section'
    )));

    //Products per page
    $wp_customize->add_setting('the_computer_repair_products_per_page',array(
		'default'=> '9',
		'sanitize_callback'	=> 'the_computer_repair_sanitize_float'
	));
	$wp_customize->add_control('the_computer_repair_products_per_page',array(
		'label'	=> __('Products Per Page','the-computer-repair'),
		'description' => __('Display on shop page','the-computer-repair'),
		'input_attrs' => array(
            'step'             => 1,
			'min'              => 0,
			'max'              => 50,
        ),
		'section'=> 'the_computer_repair_woocommerce_section',
		'type'=> 'number',
	));

    //Products per row
    $wp_customize->add_setting('the_computer_repair_products_per_row',array(
		'default'=> '3',
		'sanitize_callback'	=> 'the_computer_repair_sanitize_choices'
	));
	$wp_customize->add_control('the_computer_repair_products_per_row',array(
		'label'	=> __('Products Per Row','the-computer-repair'),
		'description' => __('Display on shop page','the-computer-repair'),
		'choices' => array(
            '2' => '2',
			'3' => '3',
			'4' => '4',
        ),
		'section'=> 'the_computer_repair_woocommerce_section',
		'type'=> 'select',
	));

	//Products padding
	$wp_customize->add_setting('the_computer_repair_products_padding_top_bottom',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_products_padding_top_bottom',array(
		'label'	=> __('Products Padding Top Bottom','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_woocommerce_section',
		'type'=> 'text'
	));

	$wp_customize->add_setting('the_computer_repair_products_padding_left_right',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('the_computer_repair_products_padding_left_right',array(
		'label'	=> __('Products Padding Left Right','the-computer-repair'),
		'description'	=> __('Enter a value in pixels. Example:20px','the-computer-repair'),
		'input_attrs' => array(
            'placeholder' => __( '10px', 'the-computer-repair' ),
        ),
		'section'=> 'the_computer_repair_woocommerce_section',
		'type'=> 'text'
	));

	//Products box shadow
	$wp_customize->add_setting( 'the_computer_repair_products_box_shadow', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_products_box_shadow', array(
		'label'       => esc_html__( 'Products Box Shadow','the-computer-repair' ),
		'section'     => 'the_computer_repair_woocommerce_section',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

	//Products border radius
    $wp_customize->add_setting( 'the_computer_repair_products_border_radius', array(
		'default'              => '',
		'transport' 		   => 'refresh',
		'sanitize_callback'    => 'the_computer_repair_sanitize_number_range'
	) );
	$wp_customize->add_control( 'the_computer_repair_products_border_radius', array(
		'label'       => esc_html__( 'Products Border Radius','the-computer-repair' ),
		'section'     => 'the_computer_repair_woocommerce_section',
		'type'        => 'range',
		'input_attrs' => array(
			'step'             => 1,
			'min'              => 1,
			'max'              => 50,
		),
	) );

    // Has to be at the top
	$wp_customize->register_panel_type( 'The_Computer_Repair_WP_Customize_Panel' );
	$wp_customize->register_section_type( 'The_Computer_Repair_WP_Customize_Section' );
}

add_action( 'customize_register', 'the_computer_repair_customize_register' );

load_template( trailingslashit( get_template_directory() ) . '/inc/logo/logo-resizer.php' );

if ( class_exists( 'WP_Customize_Panel' ) ) {
  	class The_Computer_Repair_WP_Customize_Panel extends WP_Customize_Panel {
	    public $panel;
	    public $type = 'the_computer_repair_panel';
	    public function json() {

	      $array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'type', 'panel', ) );
	      $array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
	      $array['content'] = $this->get_content();
	      $array['active'] = $this->active();
	      $array['instanceNumber'] = $this->instance_number;
	      return $array;
    	}
  	}
}

if ( class_exists( 'WP_Customize_Section' ) ) {
  	class The_Computer_Repair_WP_Customize_Section extends WP_Customize_Section {
	    public $section;
	    public $type = 'the_computer_repair_section';
	    public function json() {

	      $array = wp_array_slice_assoc( (array) $this, array( 'id', 'description', 'priority', 'panel', 'type', 'description_hidden', 'section', ) );
	      $array['title'] = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
	      $array['content'] = $this->get_content();
	      $array['active'] = $this->active();
	      $array['instanceNumber'] = $this->instance_number;

	      if ( $this->panel ) {
	        $array['customizeAction'] = sprintf( 'Customizing &#9656; %s', esc_html( $this->manager->get_panel( $this->panel )->title ) );
	      } else {
	        $array['customizeAction'] = 'Customizing';
	      }
	      return $array;
    	}
  	}
}

// Enqueue our scripts and styles
function the_computer_repair_customize_controls_scripts() {
  wp_enqueue_script( 'customizer-controls', get_theme_file_uri( '/assets/js/customizer-controls.js' ), array(), '1.0', true );
}
add_action( 'customize_controls_enqueue_scripts', 'the_computer_repair_customize_controls_scripts' );

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class The_Computer_Repair_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	*/
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'The_Computer_Repair_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section( new The_Computer_Repair_Customize_Section_Pro( $manager,'the_computer_repair_upgrade_pro_link', array(
			'priority'   => 1,
			'title'    => esc_html__( 'Computer Repair Pro', 'the-computer-repair' ),
			'pro_text' => esc_html__( 'UPGRADE PRO', 'the-computer-repair' ),
			'pro_url'  => esc_url('https://www.vwthemes.com/themes/computer-repair-wordpress-theme/'),
		)));

		$manager->add_section(new The_Computer_Repair_Customize_Section_Pro($manager,'the_computer_repair_get_started_link',array(
			'priority'   => 1,
			'title'    => esc_html__( 'Documentation', 'the-computer-repair' ),
			'pro_text' => esc_html__( 'DOCS', 'the-computer-repair' ),
			'pro_url'  => admin_url('themes.php?page=the_computer_repair_guide'),
		)));
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'the-computer-repair-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/assets/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'the-computer-repair-customize-controls', trailingslashit( esc_url(get_template_directory_uri()) ) . '/assets/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
The_Computer_Repair_Customize::get_instance();