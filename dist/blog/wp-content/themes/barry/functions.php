<?php
  /*
    ===========================================
          ENABLES WORDPRESS FEATURED IMAGES
    ===========================================
  */
  add_theme_support( 'post-thumbnails' );
  /*
    ==========================================
        CREATING CUSTOM WORDPRESS MENU
    ==========================================

    Change the name "Header-menu" and "Header menu" to call your menu
    To add to template files
    <?php
      wp_nav_menu( array(
          'theme_location' => 'header-menu',
          'container_class' => 'custom-menu-class' ) );
      ?>
  */

  function register_header_menu() {
    register_nav_menu('header-menu',__( 'Header Menu' ));
  }
  add_action( 'init', 'register_header_menu' );



  /* Alters the ellipsis on a wordpress excerpt, this will change the ellipsis from the default [...] to ... */

  function new_excerpt_more($more) {
    return '...';
    }
    add_filter('excerpt_more', 'new_excerpt_more');


  /*
    ==========================================
        CREATING CUSTOM WORDPRESS WIDGET
    ==========================================
    The vidget is used to create dynamic sidebars that can
    be altered in the back end panel of wordpress.

    To implement into theme folder

    <?php if ( is_active_sidebar( 'home_right_1' ) ) : ?>
    	<div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
    		<?php dynamic_sidebar( 'home_right_1' ); ?>
    	</div><!-- #primary-sidebar -->
    <?php endif; ?>
  */

    /**
     * Register our sidebars and widgetized areas.
     *
     */
    function arphabet_widgets_init() {

    	register_sidebar( array(
    		'name'          => 'Home right sidebar',
    		'id'            => 'home_right_1',
    		'before_widget' => '<div>',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2 class="rounded">',
    		'after_title'   => '</h2>',
    	) );

    }
    add_action( 'widgets_init', 'arphabet_widgets_init' );
?>
