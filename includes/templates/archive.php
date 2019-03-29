<?php get_header();
	$sidebar = get_post_meta($post->ID, "sidebar");
    $breadcrumbs = get_post_meta($post->ID, "breadcrumb");
?>

<?php get_template_part( 'header', 'image' ); ?>

	<!--<div class="col-md-12 mobile-menu"> <?php get_template_part( 'menu', 'mobile' ); ?> </div>-->
	<div class="container uams-body">

	  <div class="row">

	    <div class="col-md-8 uams-content" role='main'>

	      <?php
		      if((!isset($breadcrumbs[0]) || $breadcrumbs[0]!="on")) {
		      	get_template_part( 'breadcrumbs' );
		      }
		  ?>

	      <div id='main_content' class="uams-body-copy" tabindex="-1">

          <?php
          // Start the Loop.
          while ( have_posts() ) : the_post(); ?>

          <h2 style="font-size: 27px;">
            <a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title() ?></a>
          </h2>
          <?php
            if ( ! is_home() && ! is_search() && ! is_archive() ) :
              uams_mobile_menu();
            endif;
           if ( has_post_thumbnail() ) :
               the_post_thumbnail( 'thumbnail' , 'style=margin-bottom:5px;');
           endif;
          ?>
          <?php the_excerpt(); ?>
          <hr>
          <?php
          endwhile;
          ?>

            <?php posts_nav_link(' ', 'Previous page', 'Next page'); ?>
 
		  </div><!-- #main_content -->

    	</div><!-- uams-content -->

		<div id="sidebar"><?php
      if(!isset($sidebar[0]) || $sidebar[0]!="on"){
        get_sidebar();
      }
    ?></div>

  </div><!-- row -->

</div>
 
<?php get_footer(); ?>