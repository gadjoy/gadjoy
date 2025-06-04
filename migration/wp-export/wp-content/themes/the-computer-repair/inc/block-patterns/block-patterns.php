<?php
/**
 * The Computer Repair: Block Patterns
 *
 * @package The Computer Repair
 * @since   1.0.0
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'the-computer-repair',
		array( 'label' => __( 'The Computer Repair', 'the-computer-repair' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {
	register_block_pattern(
		'the-computer-repair/banner-section',
		array(
			'title'      => __( 'Banner Section', 'the-computer-repair' ),
			'categories' => array( 'the-computer-repair' ),
			'content'    => "<!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png\",\"id\":6044,\"dimRatio\":0,\"align\":\"full\",\"className\":\"banner-section\"} -->\n<div class=\"wp-block-cover alignfull banner-section\" style=\"background-image:url(" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png)\"><div class=\"wp-block-cover__inner-container\"><!-- wp:columns {\"verticalAlignment\":\"center\",\"align\":\"wide\"} -->\n<div class=\"wp-block-columns alignwide are-vertically-aligned-center\"><!-- wp:column {\"verticalAlignment\":\"center\",\"width\":\"\"} -->\n<div class=\"wp-block-column is-vertically-aligned-center\"><!-- wp:heading {\"textAlign\":\"left\",\"level\":4,\"className\":\"mb-0 px-3 py-1\",\"fontSize\":\"small\",\"style\":{\"color\":{\"background\":\"#23cfca\"}}} -->\n<h4 class=\"has-text-align-left mb-0 px-3 py-1 has-background has-small-font-size\" style=\"background-color:#23cfca\">WE GIVE YOU THE BEST!</h4>\n<!-- /wp:heading -->\n\n<!-- wp:heading {\"textAlign\":\"left\",\"level\":1,\"className\":\"m-0\"} -->\n<h1 class=\"has-text-align-left m-0\">TE OBTINUIT UD ADEPTO SATIS SOMNO</h1>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"align\":\"left\",\"className\":\"m-0\"} -->\n<p class=\"has-text-align-left m-0\">Lorem Ipsum has been the industrys standard. Lorem Ipsum has been the industrys standard. </p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"align\":\"left\",\"className\":\"mt-0\"} -->\n<div class=\"wp-block-buttons alignleft mt-0\"><!-- wp:button {\"style\":{\"color\":{\"background\":\"#23cfca\"}},\"className\":\"is-style-fill\"} -->\n<div class=\"wp-block-button is-style-fill\"><a class=\"wp-block-button__link has-background\" style=\"background-color:#23cfca\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"verticalAlignment\":\"center\"} -->\n<div class=\"wp-block-column is-vertically-aligned-center\"></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div></div>\n<!-- /wp:cover -->",
		)
	);

	register_block_pattern(
		'the-computer-repair/services-section',
		array(
			'title'      => __( 'Services Section', 'the-computer-repair' ),
			'categories' => array( 'the-computer-repair' ),
			'content'    => "<!-- wp:cover {\"customOverlayColor\":\"#f7f6fd\",\"align\":\"full\",\"className\":\"article-outer-box\"} -->\n<div class=\"wp-block-cover alignfull has-background-dim article-outer-box\" style=\"background-color:#f7f6fd\"><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"textAlign\":\"left\",\"className\":\"ms-5\",\"style\":{\"color\":{\"text\":\"#151414\"}}} -->\n<h2 class=\"has-text-align-left ms-5 has-text-color\" style=\"color:#151414\">OUR SERVICES</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"className\":\"ms-5\",\"style\":{\"color\":{\"text\":\"#b3b3c0\"}}} -->\n<p class=\"ms-5 has-text-color\" style=\"color:#b3b3c0\">Lorem Ipsum has been the industrys standard.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:columns {\"align\":\"wide\",\"className\":\"article-container\"} -->\n<div class=\"wp-block-columns alignwide article-container\"><!-- wp:column {\"className\":\"article-section pt-4\"} -->\n<div class=\"wp-block-column article-section pt-4\"><!-- wp:image {\"align\":\"center\",\"id\":6076,\"sizeSlug\":\"large\",\"linkDestination\":\"media\"} -->\n<div class=\"wp-block-image\"><figure class=\"aligncenter size-large\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/services1.png\" alt=\"\" class=\"wp-image-6076\"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"textAlign\":\"center\",\"level\":3,\"className\":\"mt-2\",\"style\":{\"color\":{\"text\":\"#151414\"}}} -->\n<h3 class=\"has-text-align-center mt-2 has-text-color\" style=\"color:#151414\">OUR SERVICES TITLE 1</h3>\n<!-- /wp:heading -->\n\n<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"style\":{\"color\":{\"text\":\"#b3b3c0\",\"background\":\"#e4e3ed\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"background-color:#e4e3ed;color:#b3b3c0\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"className\":\"article-section pt-4\"} -->\n<div class=\"wp-block-column article-section pt-4\"><!-- wp:image {\"align\":\"center\",\"id\":6077,\"sizeSlug\":\"large\",\"linkDestination\":\"media\"} -->\n<div class=\"wp-block-image\"><figure class=\"aligncenter size-large\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/services2.png\" alt=\"\" class=\"wp-image-6077\"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"textAlign\":\"center\",\"level\":3,\"className\":\"mt-2\",\"style\":{\"color\":{\"text\":\"#151414\"}}} -->\n<h3 class=\"has-text-align-center mt-2 has-text-color\" style=\"color:#151414\">OUR SERVICES TITLE 2</h3>\n<!-- /wp:heading -->\n\n<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"style\":{\"color\":{\"text\":\"#b3b3c0\",\"background\":\"#e4e3ed\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"background-color:#e4e3ed;color:#b3b3c0\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"className\":\"article-section pt-4\"} -->\n<div class=\"wp-block-column article-section pt-4\"><!-- wp:image {\"align\":\"center\",\"id\":6078,\"sizeSlug\":\"large\",\"linkDestination\":\"media\"} -->\n<div class=\"wp-block-image\"><figure class=\"aligncenter size-large\"><img src=\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/services3.png\" alt=\"\" class=\"wp-image-6078\"/></figure></div>\n<!-- /wp:image -->\n\n<!-- wp:heading {\"textAlign\":\"center\",\"level\":3,\"className\":\"mt-2\",\"style\":{\"color\":{\"text\":\"#151414\"}}} -->\n<h3 class=\"has-text-align-center mt-2 has-text-color\" style=\"color:#151414\">OUR SERVICES TITLE 3</h3>\n<!-- /wp:heading -->\n\n<!-- wp:buttons {\"align\":\"center\"} -->\n<div class=\"wp-block-buttons aligncenter\"><!-- wp:button {\"style\":{\"color\":{\"text\":\"#b3b3c0\",\"background\":\"#e4e3ed\"}}} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link has-text-color has-background\" style=\"background-color:#e4e3ed;color:#b3b3c0\">READ MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:cover -->",
		)
	);
}