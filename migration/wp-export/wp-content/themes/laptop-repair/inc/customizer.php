<?php
/**
 * Laptop Repair Theme Customizer
 *
 * @package Laptop Repair
 */
function laptop_repair_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'laptop_repair_custom_header_args', array(
		'default-text-color'     => '949494',
		'width'                  => 1600,
		'height'                 => 200,
		'wp-head-callback'       => 'laptop_repair_header_style',
 		'default-text-color' => false,
 		'header-text' => false,
	) ) );
}
add_action( 'after_setup_theme', 'laptop_repair_custom_header_setup' );
if ( ! function_exists( 'laptop_repair_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see laptop_repair_custom_header_setup().
 */
function laptop_repair_header_style() {
	$header_text_color = get_header_textcolor();
	?>
	<style type="text/css">
	<?php
		//Check if user has defined any header image.
		if ( get_header_image() ) :
	?>
		.header {
			background: url(<?php echo esc_url(get_header_image()); ?>) no-repeat;
			background-position: center top;
		}
	<?php endif; ?>	
	</style>
	<?php
}
endif; // laptop_repair_header_style 
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */ 
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function laptop_repair_customize_register( $wp_customize ) {
	//Add a class for titles
    class laptop_repair_Info extends WP_Customize_Control {
        public $type = 'info';
        public $label = '';
        public function render_content() {
        ?>
			<h3 style="text-decoration: underline; color: #DA4141; text-transform: uppercase;"><?php echo esc_html( $this->label ); ?></h3>
        <?php
        }
    }
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->add_setting('color_scheme',array(
			'default'	=> '#54be73',
			'sanitize_callback'	=> 'sanitize_hex_color'
	));
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => esc_html__('Color Scheme','laptop-repair'),			
			 'description'	=> esc_html__('More color options in PRO Version','laptop-repair'),	
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	// Slider Section		
	$wp_customize->add_section( 'slider_section', array(
            'title' => esc_html__('Slider Settings', 'laptop-repair'),
            'priority' => null,
            'description'	=> esc_html__('Featured Image Size Should be ( 1400 X 748 ) More slider settings available in PRO Version','laptop-repair'),		
        )
    );
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'laptop_repair_sanitize_integer'
	));
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> esc_html__('Select page for slide one:','laptop-repair'),
			'section'	=> 'slider_section'
	));	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',			
			'sanitize_callback'	=> 'laptop_repair_sanitize_integer'
	));
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> esc_html__('Select page for slide two:','laptop-repair'),
			'section'	=> 'slider_section'
	));	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'laptop_repair_sanitize_integer'
	));
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> esc_html__('Select page for slide three:','laptop-repair'),
			'section'	=> 'slider_section'
	));	
	//Slider hide
	$wp_customize->add_setting('hide_slides',array(
			'sanitize_callback' => 'laptop_repair_sanitize_checkbox',
			'default' => true,
	));	 
	$wp_customize->add_control( 'hide_slides', array(
    	   'section'   => 'slider_section',    	 
		   'label'	=> esc_html__('Uncheck To Show Slider','laptop-repair'),
    	   'type'      => 'checkbox'
     )); // Slider Section		 
	 
	$wp_customize->add_section('header_top_bar',array(
			'title'	=> esc_html__('Header Information','laptop-repair'),				
			'description'	=> esc_html__('Add Information For Header Area','laptop-repair'),		
			'priority'		=> null
	));
	
	$wp_customize->add_setting('contact_no',array(
			'default'	=> null,
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('contact_no',array(
			'label'	=> esc_html__('Add contact number here.','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'contact_no'
	));
	
	$wp_customize->add_setting('email_add',array(
			'default'	=> null,
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	$wp_customize->add_control('email_add',array(
			'label'	=> esc_html__('Add email address here.','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'email_add'
	));	
	
	$wp_customize->add_setting('hdr_fb_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'	
	));
	$wp_customize->add_control('hdr_fb_link',array(
			'label'	=> esc_html__('Add Facebook link here','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'hdr_fb_link'
	));	
	$wp_customize->add_setting('hdr_twitt_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('hdr_twitt_link',array(
			'label'	=> esc_html__('Add Twitter link here','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'hdr_twitt_link'
	));
	$wp_customize->add_setting('hdr_gplus_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('hdr_gplus_link',array(
			'label'	=> esc_html__('Add Google plus link here','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'hdr_gplus_link'
	));
	$wp_customize->add_setting('hdr_youtube_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('hdr_youtube_link',array(
			'label'	=> esc_html__('Add Youtube link here','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'hdr_youtube_link'
	));	
	$wp_customize->add_setting('hdr_instagram_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('hdr_instagram_link',array(
			'label'	=> esc_html__('Add Instagram link here','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'hdr_instagram_link'
	));		
	$wp_customize->add_setting('hdr_linkedin_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('hdr_linkedin_link',array(
			'label'	=> esc_html__('Add Linkedin link here','laptop-repair'),
			'section'	=> 'header_top_bar',
			'setting'	=> 'hdr_linkedin_link'
	));		

	//Hide Header Top Bar
	$wp_customize->add_setting('hide_header_topbar',array(
			'sanitize_callback' => 'laptop_repair_sanitize_checkbox',
			'default' => true,
	));	 
	$wp_customize->add_control( 'hide_header_topbar', array(
    	   'section'   => 'header_top_bar',    	 
		   'label'	=> esc_html__('Uncheck To Show This Section','laptop-repair'),
    	   'type'      => 'checkbox'
     )); 	//Hide Header Top Bar	
	 
	// Home Section 1
	$wp_customize->add_section('section_thumb_with_content', array(
		'title'	=> esc_html__('Home Section One','laptop-repair'),
		'description'	=> esc_html__('Select Page from the dropdown for section','laptop-repair'),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('sec-column-left1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'sec-column-left1',array('type' => 'dropdown-pages',
			'section' => 'section_thumb_with_content',
	));	
	
	$wp_customize->add_setting('sec-column-left2',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'sec-column-left2',array('type' => 'dropdown-pages',
			'section' => 'section_thumb_with_content',
	));		
 	
	$wp_customize->add_setting('sec-column-left3',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'sec-column-left3',array('type' => 'dropdown-pages',
			'section' => 'section_thumb_with_content',
	));	
 	
	$wp_customize->add_setting('sec-column-left4',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'sec-column-left4',array('type' => 'dropdown-pages',
			'section' => 'section_thumb_with_content',
	));			
 	
	//Hide Section 	
	$wp_customize->add_setting('hide_home_secwith_content',array(
			'sanitize_callback' => 'laptop_repair_sanitize_checkbox',
			'default' => true,
	));	 
	$wp_customize->add_control( 'hide_home_secwith_content', array(
    	   'section'   => 'section_thumb_with_content',    	 
		   'label'	=> esc_html__('Uncheck To Show This Section','laptop-repair'),
    	   'type'      => 'checkbox'
     )); // Hide Section 			
	 
	 
	 
// Home Section 2
	$wp_customize->add_section('section_two', array(
		'title'	=> esc_html__('Home Section Two','laptop-repair'),
		'description'	=> esc_html__('Select Page from the dropdown','laptop-repair'),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('section2_title',array(
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('section2_title',array(
			'label'	=> __('Add section top title','laptop-repair'),
			'section'	=> 'section_two',
			'setting'	=> 'section2_title'
	));		

	$wp_customize->add_setting('page-column1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'page-column1',array('type' => 'dropdown-pages',
			'section' => 'section_two',
	));	
	
	//Hide Section
	$wp_customize->add_setting('hide_sectiontwo',array(
			'sanitize_callback' => 'laptop_repair_sanitize_checkbox',
			'default' => true,
	));	 
	$wp_customize->add_control( 'hide_sectiontwo', array(
    	   'section'   => 'section_two',    	 
		   'label'	=> esc_html__('Uncheck To Show This Section','laptop-repair'),
    	   'type'      => 'checkbox'
     )); //Hide Section	 	 

	// Home Section 3
	$wp_customize->add_section('hm_section_3', array(
		'title'	=> esc_html__('Home Section Three','laptop-repair'),
		'description'	=> esc_html__('Select Page from the dropdown for section','laptop-repair'),
		'priority'	=> null
	));	
	
	$wp_customize->add_setting('section3_title',array(
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('section3_title',array(
			'label'	=> __('Add title for section title','laptop-repair'),
			'section'	=> 'hm_section_3',
			'setting'	=> 'section3_title'
	));	
	
	$wp_customize->add_setting('third-column-left1',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'third-column-left1',array('type' => 'dropdown-pages',
			'section' => 'hm_section_3',
	));	
	
	$wp_customize->add_setting('third-column-left2',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'third-column-left2',array('type' => 'dropdown-pages',
			'section' => 'hm_section_3',
	));		
 	
	$wp_customize->add_setting('third-column-left3',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'third-column-left3',array('type' => 'dropdown-pages',
			'section' => 'hm_section_3',
	));	
	
	$wp_customize->add_setting('third-column-left4',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'third-column-left4',array('type' => 'dropdown-pages',
			'section' => 'hm_section_3',
	));			
	
	$wp_customize->add_setting('third-column-left5',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'third-column-left5',array('type' => 'dropdown-pages',
			'section' => 'hm_section_3',
	));	
	
	$wp_customize->add_setting('third-column-left6',	array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback' => 'laptop_repair_sanitize_integer',
		));
	$wp_customize->add_control(	'third-column-left6',array('type' => 'dropdown-pages',
			'section' => 'hm_section_3',
	));				
 	
	//Hide Section 	
	$wp_customize->add_setting('hide_home_third_content',array(
			'sanitize_callback' => 'laptop_repair_sanitize_checkbox',
			'default' => true,
	));	 
	$wp_customize->add_control( 'hide_home_third_content', array(
    	   'section'   => 'hm_section_3',    	 
		   'label'	=> esc_html__('Uncheck To Show This Section','laptop-repair'),
    	   'type'      => 'checkbox'
     )); // Hide Section 	
	 
	// Social Icons 
	$wp_customize->add_section('social_sec',array(
			'title'	=> esc_html__('Footer Social Settings','laptop-repair'),				
			'description'	=> esc_html__('More social icon available in PRO Version','laptop-repair'),		
			'priority'		=> null
	));
	$wp_customize->add_setting('fb_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'	
	));
	$wp_customize->add_control('fb_link',array(
			'label'	=> esc_html__('Add Facebook link here','laptop-repair'),
			'section'	=> 'social_sec',
			'setting'	=> 'fb_link'
	));	
	$wp_customize->add_setting('twitt_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('twitt_link',array(
			'label'	=> esc_html__('Add Twitter link here','laptop-repair'),
			'section'	=> 'social_sec',
			'setting'	=> 'twitt_link'
	));
	$wp_customize->add_setting('gplus_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('gplus_link',array(
			'label'	=> esc_html__('Add Google plus link here','laptop-repair'),
			'section'	=> 'social_sec',
			'setting'	=> 'gplus_link'
	));
	$wp_customize->add_setting('youtube_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('youtube_link',array(
			'label'	=> esc_html__('Add Youtube link here','laptop-repair'),
			'section'	=> 'social_sec',
			'setting'	=> 'youtube_link'
	));	
	$wp_customize->add_setting('instagram_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('instagram_link',array(
			'label'	=> esc_html__('Add Instagram link here','laptop-repair'),
			'section'	=> 'social_sec',
			'setting'	=> 'instagram_link'
	));		
	$wp_customize->add_setting('linkedin_link',array(
			'default'	=> null,
			'sanitize_callback'	=> 'esc_url_raw'
	));
	$wp_customize->add_control('linkedin_link',array(
			'label'	=> esc_html__('Add Linkedin link here','laptop-repair'),
			'section'	=> 'social_sec',
			'setting'	=> 'linkedin_link'
	));	
	// Social Icons 
}
add_action( 'customize_register', 'laptop_repair_customize_register' );
//Integer
function laptop_repair_sanitize_integer( $input ) {
    if( is_numeric( $input ) ) {
        return intval( $input );
    }
}
function laptop_repair_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

//setting inline css.
function laptop_repair_custom_css() {
    wp_enqueue_style(
        'laptop-repair-custom-style',
        get_template_directory_uri() . '/css/laptop-repair-custom-style.css'
    );
        $color = get_theme_mod( 'color_scheme' ); //E.g. #e64d43
		$header_text_color = get_header_textcolor();
        $custom_css = "
					#sidebar ul li a:hover,
					.cols-3 ul li.current_page_item a,				
					.phone-no strong,					
					.left a:hover,
					.blog_lists h4 a:hover,
					.recent-post h6 a:hover,
					.recent-post a:hover,
					.design-by a,
					.fancy-title h2 span,
					.postmeta a:hover,
					.logo h2,
					.left-fitbox a:hover h3, .right-fitbox a:hover h3, .tagcloud a,
					.slide_info h2 span,
					.homefour_section_content h2 span,
					.section5-column:hover h3,
					.footer-row span,
					.hm-rightcols h2 small,
					.sitenav ul li.current_page_item a, .sitenav ul li a:hover,
					.footerarea a:hover,
					.copyright-txt a:hover
					{ 
						 color: {$color} !important;
					}
					.pagination .nav-links span.current, .pagination .nav-links a:hover,
					#commentform input#submit:hover,
					.nivo-controlNav a.active,								
					.wpcf7 input[type='submit'],
					a.ReadMore,
					.section2button,
					input.search-submit,
					.slide_info .slide_more:hover,
					.recent-post .morebtn:hover, 
					.footersocial .social-icons a:hover,
					.homefour_section_content .button,
					.perf-thumb,
					.yellowdivide,
					.sitenav ul li:last-child a,
					.blocksboxbg:hover
					{ 
					   background-color: {$color} !important;
					}
					.titleborder span:after, .perf-thumb:before{border-bottom-color: {$color} !important;}
					.perf-thumb:after{border-top-color: {$color} !important;}
					.section5-column:hover .section5-column-inner{border-color: {$color} !important;}
				";
        wp_add_inline_style( 'laptop-repair-custom-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'laptop_repair_custom_css' );          
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function laptop_repair_customize_preview_js() {
	wp_enqueue_script( 'laptop_repair_customizer', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'laptop_repair_customize_preview_js' );