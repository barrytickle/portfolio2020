<?php
  get_header();
?>
<!--
  =========================================
                SINGLE.PHP
  =========================================

  This page is the default template file for all blog posts
  if you need to make a page for a full theme website it's
  "page.php". All the code is the exact same, but both files
  are for different purposes.

  We need a slightly different loop for this page,
  otherwise it will pull in all the other blog posts.
  The loop will check the database to see if there
  is a blog post which matches the url, then
  the loop will loop through all the content in the
  db.
-->

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<header class="blog-header columns two-col">
  <section class="section--details flex flex-column flex-justify-center flex-align-center">
    <div class="content">
      <a href="/blog/" class="blog--back">Go Back</a>
      <h1><?php the_title(); ?></h1>
      <div class="flex flex-column">
        <span><?php the_time( get_option( 'date_format' ) ); ?></span>
        <div class="flex flex-justify-center flex-row"><div><a href="#post" class="btn">Read post</a></div></div>
      </div>

    </div>
  </section>
  <section class="section-image" style="background-image:url('<?php echo the_post_thumbnail_url(); ?>');">

  </section>
</header>


<main class="blog-main">
  <div class="smallContainer" id="post">
<?php the_content(); ?>
  </div>
</main>

<?php endwhile; else : ?>
  <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>

<?php
  get_footer();
?>
