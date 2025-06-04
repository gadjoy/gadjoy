<?php
/**
 * Related posts based on categories and tags.
 * 
 */

$the_computer_repair_related_posts_taxonomy = get_theme_mod( 'the_computer_repair_related_posts_taxonomy', 'category' );

$the_computer_repair_post_args = array(
    'posts_per_page'    => absint( get_theme_mod( 'the_computer_repair_related_posts_count', '3' ) ),
    'orderby'           => 'rand',
    'post__not_in'      => array( get_the_ID() ),
);

$the_computer_repair_tax_terms = wp_get_post_terms( get_the_ID(), 'category' );
$the_computer_repair_terms_ids = array();
foreach( $the_computer_repair_tax_terms as $tax_term ) {
	$the_computer_repair_terms_ids[] = $tax_term->term_id;
}

$the_computer_repair_post_args['category__in'] = $the_computer_repair_terms_ids; 

if(get_theme_mod('the_computer_repair_related_post',true)==1){

$the_computer_repair_related_posts = new WP_Query( $the_computer_repair_post_args );

if ( $the_computer_repair_related_posts->have_posts() ) : ?>
    <div class="related-post">
        <h3><?php echo esc_html(get_theme_mod('the_computer_repair_related_post_title','Related Post'));?></h3>
        <div class="row">
            <?php while ( $the_computer_repair_related_posts->have_posts() ) : $the_computer_repair_related_posts->the_post(); ?>
                <?php get_template_part('template-parts/grid-layout'); ?>
            <?php endwhile; ?>
        </div>
    </div>
<?php endif;
wp_reset_postdata();

}