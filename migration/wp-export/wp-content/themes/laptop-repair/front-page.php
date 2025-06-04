<?php
/**
 * The template for displaying home page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Laptop Repair
 */
get_header(); 

$hideslide = get_theme_mod('hide_slides', 1);
$secwithcontent = get_theme_mod('hide_home_secwith_content', 1);
$hide_sectiontwo = get_theme_mod('hide_sectiontwo', 1);
$hide_home_third_content = get_theme_mod('hide_home_third_content', 1);
$hide_sectionfour = get_theme_mod('hide_sectionfour', 1);
$hide_home_five_content = get_theme_mod('hide_home_five_content', 1);

if (!is_home() && is_front_page()) { 
if( $hideslide == '') { ?>
<!-- Slider Section -->
<?php 
$pages = array();
for($sld=7; $sld<10; $sld++) { 
	$mod = absint( get_theme_mod('page-setting'.$sld));
    if ( 'page-none-selected' != $mod ) {
      $pages[] = $mod;
    }	
} 
if( !empty($pages) ) :
$args = array(
      'posts_per_page' => 3,
      'post_type' => 'page',
      'post__in' => $pages,
      'orderby' => 'post__in'
    );
    $query = new WP_Query( $args );
    if ( $query->have_posts() ) :	
	$sld = 7;
?>
<section id="home_slider">
  <div class="slider-wrapper theme-default">
    <div class="slider-shadow"></div>
    <div id="slider" class="nivoSlider">
		<?php
        $i = 0;
        while ( $query->have_posts() ) : $query->the_post();
          $i++;
          $laptop_repair_slideno[] = $i;
          $laptop_repair_slidetitle[] = get_the_title();
		  $laptop_repair_slidedesc[] = get_the_excerpt();
          $laptop_repair_slidelink[] = esc_url(get_permalink());
          ?>
          <img src="<?php the_post_thumbnail_url('full'); ?>" title="#slidecaption<?php echo esc_attr( $i ); ?>" />
          <?php
        $sld++;
        endwhile;
          ?>
    </div>
        <?php
        $k = 0;
        foreach( $laptop_repair_slideno as $laptop_repair_sln ){ ?>
    <div id="slidecaption<?php echo esc_attr( $laptop_repair_sln ); ?>" class="nivo-html-caption">
      <div class="slide_info">
        <h2><?php echo esc_html($laptop_repair_slidetitle[$k] ); ?></h2>
        <p><?php echo esc_html($laptop_repair_slidedesc[$k] ); ?></p>
        <div class="clear"></div>
        <a class="slide_more" href="<?php echo esc_url($laptop_repair_slidelink[$k] ); ?>">
          <?php esc_html_e('Make an Appointment', 'laptop-repair');?>
          </a>
      </div>
    </div>
 	<?php $k++;
       wp_reset_postdata();
      } ?>
<?php endif; endif; ?>
  </div>
  <div class="clear"></div>
</section>
<?php } } 
?>
<?php
	if(!is_home() && is_front_page()){ 
	if( $secwithcontent == '') {
?>
 <section id="sectionone">
 	<div class="container">
      <div class="home_section1_content">
        <div class="row_area">	
			<?php 
			for($l=1; $l<5; $l++) { 
	  		if( get_theme_mod('sec-column-left'.$l,false)) {
			$leftblock = new WP_query('page_id='.get_theme_mod('sec-column-left'.$l,true)); 
			while( $leftblock->have_posts() ) : $leftblock->the_post(); 
			?>
 			<div class="blocksbox"> 
            
              <div class="blocksboxbg">	
              <div class="blockboxinner">	
			  <?php if( has_post_thumbnail() ) { ?>	
              <a href="<?php echo esc_url( get_permalink() ); ?>"><div class="blockthumb"><?php the_post_thumbnail('full'); ?></div>
              </a>
              <?php } ?>
              <a href="<?php echo esc_url( get_permalink() ); ?>">
              <div class="blocktitle">
                <h3><?php the_title(); ?></h3>
              </div>
              </a>
              </div>
              </div>
            </div>
			<?php endwhile; wp_reset_postdata(); 
               }} 
            ?>             
</div>
      </div>
    </div>
 </section>
<?php }} ?>
<?php
if (!is_home() && is_front_page()) { 
if( $hide_sectiontwo == '') { ?>
<section class="homeone_section_area">
    	<div class="center">
            <div class="homeone_section_content">
         	 <?php 
	  		if( get_theme_mod('page-column1',false)) {
			$sectiononequery = new WP_query('page_id='.get_theme_mod('page-column1',true)); 
			while( $sectiononequery->have_posts() ) : $sectiononequery->the_post(); ?>
            <?php if( has_post_thumbnail() ) { ?><div class="hm-leftcols"><?php the_post_thumbnail('full'); ?></div><?php } ?>
            <div class="hm-rightcols">
            <h2>    <?php
    	$section2_title = get_theme_mod('section2_title');
		if(!empty($section2_title)){
	?><small><?php echo esc_html($section2_title); ?></small><?php } ?><?php the_title(); ?></h2>
            <?php the_content(); ?>              
            </div>
          	<?php endwhile;
       		wp_reset_postdata(); 
	   		} ?>
            <div class="clear"></div>
            </div>
        </div>
    </section>
<?php } } ?>

<?php if (!is_home() && is_front_page()) {
	  if( $hide_home_third_content == '' ){	
?>
<section class="home3_section_area">
  <div class="center">
    <div class="home_section3_content">
    <?php
    	$section3_title = get_theme_mod('section3_title');
		if(!empty($section3_title)){
	?>
      <div class="center-title">
        <h2><?php echo esc_html($section3_title); ?></h2>
      </div>
      <?php } ?>
      <div class="row_area">
			<?php 
			for($j=1; $j<7; $j++) { 
	  		if( get_theme_mod('third-column-left'.$j,false)) {
			$thirdblockbox = new WP_query('page_id='.get_theme_mod('third-column-left'.$j,true)); 
			while( $thirdblockbox->have_posts() ) : $thirdblockbox->the_post(); 
			?>
            <div class="s3-servicebox">
            <div class="s3-service-column"><a href="<?php echo esc_url( get_permalink() ); ?>">
            <div class="s3-service-column-inner">
            <?php if( has_post_thumbnail() ) { ?>	
              <div class="s3-service-image"><?php the_post_thumbnail('full'); ?></div>
              <?php } ?>
              <div class="s3-service-des">
                <h3 class="s3-service-column-title"><?php the_title(); ?></h3>
                <div class="s3-service-column-text">
                  <p><?php the_excerpt(); ?></p>
                </div>
              </div>
              <div class="clear"></div>
            </div>
            </a></div>
            </div>
		<?php endwhile; wp_reset_postdata(); 
               }} 
            ?>  
	        <div class="clear"></div>
      </div>
    </div>
  </div>
</section>
<?php } } ?>
<?php
if (!is_home() && is_front_page()) { 
if( $hide_sectionfour == '') { ?>
<section class="homefour_section_area">
    	<div class="center">
            <div class="homefour_section_content">
         	 <?php 
			$section4_button_text = get_theme_mod('section4_button_text');
			$section4_button_link = get_theme_mod('section4_button_link'); 

	  		if( get_theme_mod('sectionfour-column1',false)) {
			$sectionfourquery = new WP_query('page_id='.get_theme_mod('sectionfour-column1',true)); 
			while( $sectionfourquery->have_posts() ) : $sectionfourquery->the_post(); ?>
            <div class="hmfour-cols">
            <h2><?php the_title(); ?></h2>
            <div class="hmfourcontent"><?php the_content(); ?></div>
			<?php if(!empty($section4_button_text)){ ?><a class="button" href="<?php echo esc_url($section4_button_link); ?>"><?php echo esc_html($section4_button_text); ?></a><?php } ?>          
            </div>
          	<?php endwhile;
       		wp_reset_postdata(); 
	   		} ?>
            <div class="clear"></div>
            </div>
        </div>
    </section>
<?php } } ?>
<?php if (!is_home() && is_front_page()) {
	  if( $hide_home_five_content == '' ){	
?>
<section class="home5_section_area">
  <div class="center">
    <div class="home_section5_content">
    <?php
    	$section5_title = get_theme_mod('section5_title');
		if(!empty($section5_title)){
	?>
      <div class="center-title">
        <h2><?php echo esc_html($section5_title); ?></h2>
      </div>
      <?php } ?>
      <div class="row_area">
			<?php 
			for($f=1; $f<7; $f++) { 
	  		if( get_theme_mod('sectionfive-column'.$f,false)) {
			$secfiveblockbox = new WP_query('page_id='.get_theme_mod('sectionfive-column'.$f,true)); 
			while( $secfiveblockbox->have_posts() ) : $secfiveblockbox->the_post(); 
			?>
            <div class="section5-column">
            <a href="<?php echo esc_url( get_permalink() ); ?>">
            <div class="section5-column-inner">
            <?php if( has_post_thumbnail() ) { ?>
            <?php the_post_thumbnail('full'); ?>
            <?php } ?>
            <h3><?php the_title(); ?></h3>
            <div class="section5-column-text"><?php the_excerpt(); ?></div>
            </div></a>
            </div>            
			<?php endwhile; wp_reset_postdata(); 
               }} 
            ?>  
	        <div class="clear"></div>
      </div>
    </div>
  </div>
</section>
<?php } } ?>
<div class="clear"></div>
<div class="container">
     <div class="page_content">
      <?php 
	if ( 'posts' == get_option( 'show_on_front' ) ) {
    ?>
    <section class="site-main">
      <div class="blog-post">
        <?php
                    if ( have_posts() ) :
                        // Start the Loop.
                        while ( have_posts() ) : the_post();
                            /*
                             * Include the post format-specific template for the content. If you want to
                             * use this in a child theme, then include a file called called content-___.php
                             * (where ___ is the post format) and that will be used instead.
                             */
                            get_template_part( 'content', get_post_format() );
                        endwhile;
                        // Previous/next post navigation.
						the_posts_pagination( array(
							'mid_size' => 2,
							'prev_text' => esc_html__( 'Back', 'laptop-repair' ),
							'next_text' => esc_html__( 'Next', 'laptop-repair' ),
						) );
                    else :
                        // If no content, include the "No posts found" template.
                         get_template_part( 'no-results', 'index' );
                    endif;
                    ?>
      </div>
      <!-- blog-post --> 
    </section>
    <?php
} else {
    ?>
	<section class="site-main">
      <div class="blog-post">
        <?php
                    if ( have_posts() ) :
                        // Start the Loop.
                        while ( have_posts() ) : the_post();
                            /*
                             * Include the post format-specific template for the content. If you want to
                             * use this in a child theme, then include a file called called content-___.php
                             * (where ___ is the post format) and that will be used instead.
                             */
							 ?>
                             <header class="entry-header">           
            				<h1><?php the_title(); ?></h1>
                    		</header>
                             <?php
                            the_content();
                        endwhile;
                        // Previous/next post navigation.
						the_posts_pagination( array(
							'mid_size' => 2,
							'prev_text' => esc_html__( 'Back', 'laptop-repair' ),
							'next_text' => esc_html__( 'Next', 'laptop-repair' ),
						) );
                    else :
                        // If no content, include the "No posts found" template.
                         get_template_part( 'no-results', 'index' );
                    endif;
                    ?>
      </div>
      <!-- blog-post --> 
    </section>
	<?php
}
	?>
    <?php get_sidebar();?>
    <div class="clear"></div>
  </div><!-- site-aligner -->
</div><!-- content -->
<?php get_footer(); ?>