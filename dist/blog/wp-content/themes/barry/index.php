<?php
  get_header();
?>

<!--
  =========================================
                INDEX.PHP
  =========================================
  The fallback page for wordpress, by default wordpress will use
  this template for everything, this will be the homepage
  of the blog where all the blog posts are generated.
-->
<?php $wpb_all_query = new WP_Query(array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1)); ?>

<header class="largeContainer contentHeader">
  <h1>My blogging lifestyle</h1>
</header>
<main>
  <div class="blog-container largeContainer">
      <div class="blog-row flex flex-justify--space_between flex-row" >
		  <?php $counter = 0; ?>
	      <?php while ( $wpb_all_query->have_posts() ) : $wpb_all_query->the_post(); ?>
          <?php $counter++; ?>
          <a class="blog-post" style="background-image:url('<?php echo the_post_thumbnail_url(); ?>');" href="<?php the_permalink(); ?>">
                  <div class="content">
                    <!-- <span class="new">Latest</span> -->
                    <h3><?php the_title(); ?></h3>
                    <p><?php the_time( get_option( 'date_format' ) ); ?></p>
                  </div>
          </a>
          <?php if($counter % 2 == 0){ ?>
            </div>
            <div class="blog-row flex flex-justify--space_between flex-row" >
          <?php } ?>
          <?php if($counter % 5 == 0){
                $counter = 0; ?>
                </div>
                <div class="blog-row flex flex-justify--space_between flex-row" >
          <?php } ?>
		  <?php endwhile; ?>
      </div>
  </div>
</main>


<?php
  get_footer();
?>
