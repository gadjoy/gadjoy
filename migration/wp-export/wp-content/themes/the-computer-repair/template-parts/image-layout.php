<?php
/**
 * The template part for displaying image post
 *
 * @package The Computer Repair
 * @subpackage the-computer-repair
 * @since the-computer-repair 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('inner-service'); ?>>
    <div class="entry-content">
        <h1 class="section-title"><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php the_title();?><span class="screen-reader-text"><?php the_title(); ?></span></a></h1>   
        <div class="entry-attachment">
            <div class="attachment">
                <?php the_computer_repair_the_attached_image(); ?>
            </div>

            <?php if ( has_excerpt() ) : ?>
                <div class="entry-caption">
                    <div class="entry-content"><p><?php $excerpt = get_the_excerpt(); echo esc_html( the_computer_repair_string_limit_words( $excerpt, esc_attr(get_theme_mod('the_computer_repair_excerpt_number','30')))); ?></p></div>
                </div>
            <?php endif; ?>
        </div>    
        <?php
            the_content();
            wp_link_pages( array(
                'before' => '<div class="page-links">' . __( 'Pages:', 'the-computer-repair' ),
                'after'  => '</div>',
            ) );
        ?>
    </div>    
    <?php edit_post_link( __( 'Edit', 'the-computer-repair' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
    <div class="clearfix"></div>
</article>