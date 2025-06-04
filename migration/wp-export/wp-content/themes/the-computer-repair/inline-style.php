<?php

	$the_computer_repair_first_color = get_theme_mod('the_computer_repair_first_color');

	$the_computer_repair_custom_css = '';

	/*------------------------------ Global First Color -----------*/
	
	if($the_computer_repair_first_color != false){
		$the_computer_repair_custom_css .='.cart-value,.top-btn a,.more-btn a,input[type="submit"],#sidebar h3,.pagination .current,.pagination a:hover,#sidebar .custom-social-icons i, #footer .custom-social-icons i,#sidebar .tagcloud a:hover,#footer .tagcloud a:hover,#footer-2,#slider .carousel-control-prev-icon:hover, #slider .carousel-control-next-icon:hover,.box-content:hover a,#comments input[type="submit"],nav.woocommerce-MyAccount-navigation ul li,.woocommerce #respond input#submit, .woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit.alt, .woocommerce a.button.alt, .woocommerce button.button.alt, .woocommerce input.button.alt, .scrollup i, #comments a.comment-reply-link, .toggle-nav i, #sidebar .widget_price_filter .ui-slider .ui-slider-range, #sidebar .widget_price_filter .ui-slider .ui-slider-handle, #sidebar .woocommerce-product-search button, #footer .widget_price_filter .ui-slider .ui-slider-range, #footer .widget_price_filter .ui-slider .ui-slider-handle, #footer .woocommerce-product-search button, #footer a.custom_read_more, #sidebar a.custom_read_more, .nav-previous a:hover, .nav-next a:hover, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current, .wp-block-button__link, #preloader{';
			$the_computer_repair_custom_css .='background-color: '.esc_attr($the_computer_repair_first_color).';';
		$the_computer_repair_custom_css .='}';
	}
	if($the_computer_repair_first_color != false){
		$the_computer_repair_custom_css .='#slider .carousel-control-prev-icon:hover, #slider .carousel-control-next-icon:hover,.box-content:hover{';
			$the_computer_repair_custom_css .='border-color: '.esc_attr($the_computer_repair_first_color).';';
		$the_computer_repair_custom_css .='}';
	}
	if($the_computer_repair_first_color != false){
		$the_computer_repair_custom_css .='a ,.lower-bar i,.post-main-box:hover h2,#sidebar ul li a:hover,#footer .custom-social-icons i:hover, #footer li a:hover, .post-navigation a:hover .post-title, .post-navigation a:focus .post-title,.post-main-box:hover h2 a, .entry-content a, .sidebar .textwidget p a, .textwidget p a, #comments p a, .slider .inner_carousel p a, .main-navigation ul.sub-menu a:hover, .main-navigation a:hover, #footer a.custom_read_more:hover, .post-main-box:hover .entry-date a, .post-main-box:hover .entry-author a, .single-post .post-info:hover .entry-date a, .single-post .post-info:hover .entry-author a, .lower-bar p a:hover, .logo .site-title a:hover{';
			$the_computer_repair_custom_css .='color: '.esc_attr($the_computer_repair_first_color).';';
		$the_computer_repair_custom_css .='}';
	}	
	if($the_computer_repair_first_color != false){
		$the_computer_repair_custom_css .='.main-navigation ul ul{';
			$the_computer_repair_custom_css .='border-top-color: '.esc_attr($the_computer_repair_first_color).';';
		$the_computer_repair_custom_css .='}';
	}
	if($the_computer_repair_first_color != false){
		$the_computer_repair_custom_css .='.lower-bar,#footer h3:after,.heading-box hr, .header-fixed, .main-navigation ul ul{';
			$the_computer_repair_custom_css .='border-bottom-color: '.esc_attr($the_computer_repair_first_color).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_custom_css .='@media screen and (max-width:1000px) {';
		if($the_computer_repair_first_color != false){
			$the_computer_repair_custom_css .='.search-box i{
			background-color:'.esc_attr($the_computer_repair_first_color).';
			}';
		}
	$the_computer_repair_custom_css .='}';

	/*---------------------------Width Layout -------------------*/

	$the_computer_repair_theme_lay = get_theme_mod( 'the_computer_repair_width_option','Full Width');
    if($the_computer_repair_theme_lay == 'Boxed'){
		$the_computer_repair_custom_css .='body{';
			$the_computer_repair_custom_css .='max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;';
		$the_computer_repair_custom_css .='}';
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='right: 100px;';
		$the_computer_repair_custom_css .='}';
	}else if($the_computer_repair_theme_lay == 'Wide Width'){
		$the_computer_repair_custom_css .='body{';
			$the_computer_repair_custom_css .='width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;';
		$the_computer_repair_custom_css .='}';
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='right: 30px;';
		$the_computer_repair_custom_css .='}';
	}else if($the_computer_repair_theme_lay == 'Full Width'){
		$the_computer_repair_custom_css .='body{';
			$the_computer_repair_custom_css .='max-width: 100%;';
		$the_computer_repair_custom_css .='}';
	}

	/*--------------------------- Slider Opacity -------------------*/

	$the_computer_repair_theme_lay = get_theme_mod( 'the_computer_repair_slider_opacity_color','0.5');
	if($the_computer_repair_theme_lay == '0'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.1'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.1';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.2'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.2';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.3'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.3';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.4'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.4';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.5'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.5';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.6'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.6';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.7'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.7';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.8'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.8';
		$the_computer_repair_custom_css .='}';
		}else if($the_computer_repair_theme_lay == '0.9'){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='opacity:0.9';
		$the_computer_repair_custom_css .='}';
		}

	/*---------------------------Slider Content Layout -------------------*/

	$the_computer_repair_theme_lay = get_theme_mod( 'the_computer_repair_slider_content_option','Left');
    if($the_computer_repair_theme_lay == 'Left'){
		$the_computer_repair_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$the_computer_repair_custom_css .='text-align:left; left:10%; right:55%;';
		$the_computer_repair_custom_css .='}';
	}else if($the_computer_repair_theme_lay == 'Center'){
		$the_computer_repair_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$the_computer_repair_custom_css .='text-align:center; left:20%; right:20%;';
		$the_computer_repair_custom_css .='}';
	}else if($the_computer_repair_theme_lay == 'Right'){
		$the_computer_repair_custom_css .='#slider .carousel-caption, #slider .inner_carousel, #slider .inner_carousel h1{';
			$the_computer_repair_custom_css .='text-align:right; left:55%; right:10%;';
		$the_computer_repair_custom_css .='}';
	}

	/*---------------------------Slider Height ------------*/

	$the_computer_repair_slider_height = get_theme_mod('the_computer_repair_slider_height');
	if($the_computer_repair_slider_height != false){
		$the_computer_repair_custom_css .='#slider img{';
			$the_computer_repair_custom_css .='height: '.esc_attr($the_computer_repair_slider_height).';';
		$the_computer_repair_custom_css .='}';
	}

	/*---------------------------Blog Layout -------------------*/

	$the_computer_repair_theme_lay = get_theme_mod( 'the_computer_repair_blog_layout_option','Default');
    if($the_computer_repair_theme_lay == 'Default'){
		$the_computer_repair_custom_css .='.post-main-box{';
			$the_computer_repair_custom_css .='';
		$the_computer_repair_custom_css .='}';
	}else if($the_computer_repair_theme_lay == 'Center'){
		$the_computer_repair_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn{';
			$the_computer_repair_custom_css .='text-align:center;';
		$the_computer_repair_custom_css .='}';
		$the_computer_repair_custom_css .='.post-info{';
			$the_computer_repair_custom_css .='margin-top:10px;';
		$the_computer_repair_custom_css .='}';
		$the_computer_repair_custom_css .='.post-info hr{';
			$the_computer_repair_custom_css .='margin:10px auto;';
		$the_computer_repair_custom_css .='}';
	}else if($the_computer_repair_theme_lay == 'Left'){
		$the_computer_repair_custom_css .='.post-main-box, .post-main-box h2, .post-info, .new-text p, .content-bttn, #our-services p{';
			$the_computer_repair_custom_css .='text-align:Left;';
		$the_computer_repair_custom_css .='}';
		$the_computer_repair_custom_css .='.post-info hr{';
			$the_computer_repair_custom_css .='margin-bottom:10px;';
		$the_computer_repair_custom_css .='}';
		$the_computer_repair_custom_css .='.post-main-box h2{';
			$the_computer_repair_custom_css .='margin-top:10px;';
		$the_computer_repair_custom_css .='}';
	}

	/*------------------------------Responsive Media -----------------------*/

	$the_computer_repair_resp_topbar = get_theme_mod( 'the_computer_repair_resp_topbar_hide_show',false);
	if($the_computer_repair_resp_topbar == true && get_theme_mod( 'the_computer_repair_topbar_hide_show', false) == false){
    	$the_computer_repair_custom_css .='.lower-bar{';
			$the_computer_repair_custom_css .='display:none;';
		$the_computer_repair_custom_css .='} ';
	}
    if($the_computer_repair_resp_topbar == true){
    	$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='.lower-bar{';
			$the_computer_repair_custom_css .='display:block;';
		$the_computer_repair_custom_css .='} }';
	}else if($the_computer_repair_resp_topbar == false){
		$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='.lower-bar{';
			$the_computer_repair_custom_css .='display:none;';
		$the_computer_repair_custom_css .='} }';
	}

	$the_computer_repair_resp_stickyheader = get_theme_mod( 'the_computer_repair_stickyheader_hide_show',false);
	if($the_computer_repair_resp_stickyheader == true && get_theme_mod( 'the_computer_repair_sticky_header',false) != true){
    	$the_computer_repair_custom_css .='.header-fixed{';
			$the_computer_repair_custom_css .='position:static;';
		$the_computer_repair_custom_css .='} ';
	}
    if($the_computer_repair_resp_stickyheader == true){
    	$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='.header-fixed{';
			$the_computer_repair_custom_css .='position:fixed;';
		$the_computer_repair_custom_css .='} }';
	}else if($the_computer_repair_resp_stickyheader == false){
		$the_computer_repair_custom_css .='@media screen and (max-width:575px){';
		$the_computer_repair_custom_css .='.header-fixed{';
			$the_computer_repair_custom_css .='position:static;';
		$the_computer_repair_custom_css .='} }';
	}

	$the_computer_repair_resp_slider = get_theme_mod( 'the_computer_repair_resp_slider_hide_show',false);
	if($the_computer_repair_resp_slider == true && get_theme_mod( 'the_computer_repair_slider_hide_show', false) == false){
    	$the_computer_repair_custom_css .='#slider{';
			$the_computer_repair_custom_css .='display:none;';
		$the_computer_repair_custom_css .='} ';
	}
    if($the_computer_repair_resp_slider == true){
    	$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='#slider{';
			$the_computer_repair_custom_css .='display:block;';
		$the_computer_repair_custom_css .='} }';
	}else if($the_computer_repair_resp_slider == false){
		$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='#slider{';
			$the_computer_repair_custom_css .='display:none;';
		$the_computer_repair_custom_css .='} }';
	}

	$the_computer_repair_resp_sidebar = get_theme_mod( 'the_computer_repair_sidebar_hide_show',true);
    if($the_computer_repair_resp_sidebar == true){
    	$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='#sidebar{';
			$the_computer_repair_custom_css .='display:block;';
		$the_computer_repair_custom_css .='} }';
	}else if($the_computer_repair_resp_sidebar == false){
		$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='#sidebar{';
			$the_computer_repair_custom_css .='display:none;';
		$the_computer_repair_custom_css .='} }';
	}

	$the_computer_repair_resp_scroll_top = get_theme_mod( 'the_computer_repair_resp_scroll_top_hide_show',true);
	if($the_computer_repair_resp_scroll_top == true && get_theme_mod( 'the_computer_repair_hide_show_scroll',true) != true){
    	$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='visibility:hidden !important;';
		$the_computer_repair_custom_css .='} ';
	}
    if($the_computer_repair_resp_scroll_top == true){
    	$the_computer_repair_custom_css .='@media screen and (max-width:575px) {';
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='visibility:visible !important;';
		$the_computer_repair_custom_css .='} }';
	}else if($the_computer_repair_resp_scroll_top == false){
		$the_computer_repair_custom_css .='@media screen and (max-width:575px){';
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='visibility:hidden !important;';
		$the_computer_repair_custom_css .='} }';
	}

	/*------------- Top Bar Settings ------------------*/

	$the_computer_repair_topbar_padding_top_bottom = get_theme_mod('the_computer_repair_topbar_padding_top_bottom');
	if($the_computer_repair_topbar_padding_top_bottom != false){
		$the_computer_repair_custom_css .='.lower-bar{';
			$the_computer_repair_custom_css .='padding-top: '.esc_attr($the_computer_repair_topbar_padding_top_bottom).'; padding-bottom: '.esc_attr($the_computer_repair_topbar_padding_top_bottom).';';
		$the_computer_repair_custom_css .='}';
	}

	/*-------------- Sticky Header Padding ----------------*/

	$the_computer_repair_sticky_header_padding = get_theme_mod('the_computer_repair_sticky_header_padding');
	if($the_computer_repair_sticky_header_padding != false){
		$the_computer_repair_custom_css .='.header-fixed{';
			$the_computer_repair_custom_css .='padding: '.esc_attr($the_computer_repair_sticky_header_padding).';';
		$the_computer_repair_custom_css .='}';
	}

	/*------------------ Search Settings -----------------*/
	
	$the_computer_repair_search_padding_top_bottom = get_theme_mod('the_computer_repair_search_padding_top_bottom');
	$the_computer_repair_search_padding_left_right = get_theme_mod('the_computer_repair_search_padding_left_right');
	$the_computer_repair_search_font_size = get_theme_mod('the_computer_repair_search_font_size');
	$the_computer_repair_search_border_radius = get_theme_mod('the_computer_repair_search_border_radius');
	if($the_computer_repair_search_padding_top_bottom != false || $the_computer_repair_search_padding_left_right != false || $the_computer_repair_search_font_size != false || $the_computer_repair_search_border_radius != false){
		$the_computer_repair_custom_css .='.search-box i{';
			$the_computer_repair_custom_css .='padding-top: '.esc_attr($the_computer_repair_search_padding_top_bottom).'; padding-bottom: '.esc_attr($the_computer_repair_search_padding_top_bottom).';padding-left: '.esc_attr($the_computer_repair_search_padding_left_right).';padding-right: '.esc_attr($the_computer_repair_search_padding_left_right).';font-size: '.esc_attr($the_computer_repair_search_font_size).';border-radius: '.esc_attr($the_computer_repair_search_border_radius).'px;';
		$the_computer_repair_custom_css .='}';
	}

	/*---------------- Button Settings ------------------*/

	$the_computer_repair_button_padding_top_bottom = get_theme_mod('the_computer_repair_button_padding_top_bottom');
	$the_computer_repair_button_padding_left_right = get_theme_mod('the_computer_repair_button_padding_left_right');
	if($the_computer_repair_button_padding_top_bottom != false || $the_computer_repair_button_padding_left_right != false){
		$the_computer_repair_custom_css .='.post-main-box .more-btn a{';
			$the_computer_repair_custom_css .='padding-top: '.esc_attr($the_computer_repair_button_padding_top_bottom).'; padding-bottom: '.esc_attr($the_computer_repair_button_padding_top_bottom).';padding-left: '.esc_attr($the_computer_repair_button_padding_left_right).';padding-right: '.esc_attr($the_computer_repair_button_padding_left_right).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_button_border_radius = get_theme_mod('the_computer_repair_button_border_radius');
	if($the_computer_repair_button_border_radius != false){
		$the_computer_repair_custom_css .='.post-main-box .more-btn a{';
			$the_computer_repair_custom_css .='border-radius: '.esc_attr($the_computer_repair_button_border_radius).'px;';
		$the_computer_repair_custom_css .='}';
	}

	/*------------- Single Blog Page------------------*/

	$the_computer_repair_single_blog_post_navigation_show_hide = get_theme_mod('the_computer_repair_single_blog_post_navigation_show_hide',true);
	if($the_computer_repair_single_blog_post_navigation_show_hide != true){
		$the_computer_repair_custom_css .='.post-navigation{';
			$the_computer_repair_custom_css .='display: none;';
		$the_computer_repair_custom_css .='}';
	}

	/*-------------- Copyright Alignment ----------------*/

	$the_computer_repair_copyright_alingment = get_theme_mod('the_computer_repair_copyright_alingment');
	if($the_computer_repair_copyright_alingment != false){
		$the_computer_repair_custom_css .='.copyright p{';
			$the_computer_repair_custom_css .='text-align: '.esc_attr($the_computer_repair_copyright_alingment).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_copyright_padding_top_bottom = get_theme_mod('the_computer_repair_copyright_padding_top_bottom');
	if($the_computer_repair_copyright_padding_top_bottom != false){
		$the_computer_repair_custom_css .='#footer-2{';
			$the_computer_repair_custom_css .='padding-top: '.esc_attr($the_computer_repair_copyright_padding_top_bottom).'; padding-bottom: '.esc_attr($the_computer_repair_copyright_padding_top_bottom).';';
		$the_computer_repair_custom_css .='}';
	}

	/*----------------Sroll to top Settings ------------------*/

	$the_computer_repair_scroll_to_top_font_size = get_theme_mod('the_computer_repair_scroll_to_top_font_size');
	if($the_computer_repair_scroll_to_top_font_size != false){
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='font-size: '.esc_attr($the_computer_repair_scroll_to_top_font_size).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_scroll_to_top_padding = get_theme_mod('the_computer_repair_scroll_to_top_padding');
	$the_computer_repair_scroll_to_top_padding = get_theme_mod('the_computer_repair_scroll_to_top_padding');
	if($the_computer_repair_scroll_to_top_padding != false){
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='padding-top: '.esc_attr($the_computer_repair_scroll_to_top_padding).';padding-bottom: '.esc_attr($the_computer_repair_scroll_to_top_padding).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_scroll_to_top_width = get_theme_mod('the_computer_repair_scroll_to_top_width');
	if($the_computer_repair_scroll_to_top_width != false){
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='width: '.esc_attr($the_computer_repair_scroll_to_top_width).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_scroll_to_top_height = get_theme_mod('the_computer_repair_scroll_to_top_height');
	if($the_computer_repair_scroll_to_top_height != false){
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='height: '.esc_attr($the_computer_repair_scroll_to_top_height).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_scroll_to_top_border_radius = get_theme_mod('the_computer_repair_scroll_to_top_border_radius');
	if($the_computer_repair_scroll_to_top_border_radius != false){
		$the_computer_repair_custom_css .='.scrollup i{';
			$the_computer_repair_custom_css .='border-radius: '.esc_attr($the_computer_repair_scroll_to_top_border_radius).'px;';
		$the_computer_repair_custom_css .='}';
	}

	/*----------------Social Icons Settings ------------------*/

	$the_computer_repair_social_icon_font_size = get_theme_mod('the_computer_repair_social_icon_font_size');
	if($the_computer_repair_social_icon_font_size != false){
		$the_computer_repair_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$the_computer_repair_custom_css .='font-size: '.esc_attr($the_computer_repair_social_icon_font_size).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_social_icon_padding = get_theme_mod('the_computer_repair_social_icon_padding');
	if($the_computer_repair_social_icon_padding != false){
		$the_computer_repair_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$the_computer_repair_custom_css .='padding: '.esc_attr($the_computer_repair_social_icon_padding).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_social_icon_width = get_theme_mod('the_computer_repair_social_icon_width');
	if($the_computer_repair_social_icon_width != false){
		$the_computer_repair_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$the_computer_repair_custom_css .='width: '.esc_attr($the_computer_repair_social_icon_width).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_social_icon_height = get_theme_mod('the_computer_repair_social_icon_height');
	if($the_computer_repair_social_icon_height != false){
		$the_computer_repair_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$the_computer_repair_custom_css .='height: '.esc_attr($the_computer_repair_social_icon_height).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_social_icon_border_radius = get_theme_mod('the_computer_repair_social_icon_border_radius');
	if($the_computer_repair_social_icon_border_radius != false){
		$the_computer_repair_custom_css .='#sidebar .custom-social-icons i, #footer .custom-social-icons i{';
			$the_computer_repair_custom_css .='border-radius: '.esc_attr($the_computer_repair_social_icon_border_radius).'px;';
		$the_computer_repair_custom_css .='}';
	}

	/*----------------Woocommerce Products Settings ------------------*/

	$the_computer_repair_products_padding_top_bottom = get_theme_mod('the_computer_repair_products_padding_top_bottom');
	if($the_computer_repair_products_padding_top_bottom != false){
		$the_computer_repair_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$the_computer_repair_custom_css .='padding-top: '.esc_attr($the_computer_repair_products_padding_top_bottom).'!important; padding-bottom: '.esc_attr($the_computer_repair_products_padding_top_bottom).'!important;';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_products_padding_left_right = get_theme_mod('the_computer_repair_products_padding_left_right');
	if($the_computer_repair_products_padding_left_right != false){
		$the_computer_repair_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$the_computer_repair_custom_css .='padding-left: '.esc_attr($the_computer_repair_products_padding_left_right).'!important; padding-right: '.esc_attr($the_computer_repair_products_padding_left_right).'!important;';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_products_box_shadow = get_theme_mod('the_computer_repair_products_box_shadow');
	if($the_computer_repair_products_box_shadow != false){
		$the_computer_repair_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
				$the_computer_repair_custom_css .='box-shadow: '.esc_attr($the_computer_repair_products_box_shadow).'px '.esc_attr($the_computer_repair_products_box_shadow).'px '.esc_attr($the_computer_repair_products_box_shadow).'px #ddd;';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_products_border_radius = get_theme_mod('the_computer_repair_products_border_radius');
	if($the_computer_repair_products_border_radius != false){
		$the_computer_repair_custom_css .='.woocommerce ul.products li.product, .woocommerce-page ul.products li.product{';
			$the_computer_repair_custom_css .='border-radius: '.esc_attr($the_computer_repair_products_border_radius).'px;';
		$the_computer_repair_custom_css .='}';
	}

	/*------------------ Preloader Background Color  -------------------*/

	$the_computer_repair_preloader_bg_color = get_theme_mod('the_computer_repair_preloader_bg_color');
	if($the_computer_repair_preloader_bg_color != false){
		$the_computer_repair_custom_css .='#preloader{';
			$the_computer_repair_custom_css .='background-color: '.esc_attr($the_computer_repair_preloader_bg_color).';';
		$the_computer_repair_custom_css .='}';
	}

	$the_computer_repair_preloader_border_color = get_theme_mod('the_computer_repair_preloader_border_color');
	if($the_computer_repair_preloader_border_color != false){
		$the_computer_repair_custom_css .='.loader-line{';
			$the_computer_repair_custom_css .='border-color: '.esc_attr($the_computer_repair_preloader_border_color).'!important;';
		$the_computer_repair_custom_css .='}';
	}