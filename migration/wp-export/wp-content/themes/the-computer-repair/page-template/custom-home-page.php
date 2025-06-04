<?php
/**
 * Template Name: Custom Home Page
 */

get_header(); ?>

<main id="maincontent" role="main">
  <?php do_action( 'the_computer_repair_before_slider' ); ?>

  <?php if( get_theme_mod( 'the_computer_repair_slider_hide_show', false) != '' || get_theme_mod( 'the_computer_repair_resp_slider_hide_show', false) != '') { ?>
    <section id="slider">
      <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="<?php echo esc_attr(get_theme_mod( 'the_computer_repair_slider_speed',4000)) ?>"> 
        <?php $the_computer_repair_pages = array();
          for ( $count = 1; $count <= 4; $count++ ) {
            $mod = intval( get_theme_mod( 'the_computer_repair_slider_page' . $count ));
            if ( 'page-none-selected' != $mod ) {
              $the_computer_repair_pages[] = $mod;
            }
          }
          if( !empty($the_computer_repair_pages) ) :
            $args = array(
              'post_type' => 'page',
              'post__in' => $the_computer_repair_pages,
              'orderby' => 'post__in'
            );
            $query = new WP_Query( $args );
            if ( $query->have_posts() ) :
              $i = 1;
        ?>     
        <div class="carousel-inner" role="listbox">
          <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <div <?php if($i == 1){echo 'class="carousel-item active"';} else{ echo 'class="carousel-item"';}?>>
              <?php the_post_thumbnail(); ?>
              <div class="carousel-caption">
                <div class="inner_carousel">
                  <h1><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                  <p><?php $excerpt = get_the_excerpt(); echo esc_html( the_computer_repair_string_limit_words( $excerpt, esc_attr(get_theme_mod('the_computer_repair_slider_excerpt_number','30')))); ?></p>
                  <?php if( get_theme_mod('the_computer_repair_slider_button_text','READ MORE') != ''){ ?>
                    <div class="more-btn">
                      <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_theme_mod('the_computer_repair_slider_button_text',__('READ MORE','the-computer-repair')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('the_computer_repair_slider_button_text',__('READ MORE','the-computer-repair')));?></span></a>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          <?php $i++; endwhile; 
          wp_reset_postdata();?>
        </div>
        <?php else : ?>
            <div class="no-postfound"></div>
        <?php endif;
        endif;?>
        <a class="carousel-control-prev" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev" role="button">
          <span class="carousel-control-prev-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-left"></i></span>
          <span class="screen-reader-text"><?php esc_html_e( 'Previous','the-computer-repair' );?></span>
        </a>
        <a class="carousel-control-next" data-bs-target="#carouselExampleCaptions" data-bs-slide="next" role="button">
          <span class="carousel-control-next-icon w-auto h-auto" aria-hidden="true"><i class="fas fa-chevron-right"></i></span>
          <span class="screen-reader-text"><?php esc_html_e( 'Next','the-computer-repair' );?></span>
        </a>
      </div>
      <div class="clearfix"></div>
    </section>
  <?php } ?>

  <?php do_action( 'the_computer_repair_after_slider' ); ?>

  <section id="serv-section">
    <div class="container">
      <div class="heading-box">
        <?php if( get_theme_mod( 'the_computer_repair_section_title') != '') { ?>
          <h2><?php echo esc_html(get_theme_mod('the_computer_repair_section_title',''));?></h2>
          <hr>
        <?php } ?>
        <?php if( get_theme_mod( 'the_computer_repair_section_text') != '') { ?>
          <p><?php echo esc_html(get_theme_mod('the_computer_repair_section_text',''));?></p>
        <?php } ?>      
      </div>
      <div class="row">
        <?php
          $catData =  get_theme_mod('the_computer_repair_our_services','');
          if($catData){
          $page_query = new WP_Query(array( 'category_name' => esc_html($catData,'the-computer-repair'))); ?>
          <?php while( $page_query->have_posts() ) : $page_query->the_post(); ?>
          <div class="col-md-4 col-sm-6">
            <div class="box">
              <?php the_post_thumbnail(); ?>
              <div class="box-content">
                <h3 class="title"><?php the_title(); ?></h3>
                <?php if( get_theme_mod('the_computer_repair_services_button_text','READ MORE') != ''){ ?>
                  <div class="serv-btn">
                    <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_theme_mod('the_computer_repair_services_button_text',__('READ MORE','the-computer-repair')));?><span class="screen-reader-text"><?php echo esc_html(get_theme_mod('the_computer_repair_services_button_text',__('READ MORE','the-computer-repair')));?></span></a>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <?php endwhile;
          wp_reset_postdata();
        } ?>
      </div>
    </div>
  </section>

  <?php do_action( 'the_computer_repair_after_second_section' ); ?>

  <div class="content-vw">
    <div class="container">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
      <?php endwhile; // end of the loop. ?>
    </div>
  </div>
</main>

<?php get_footer(); ?>