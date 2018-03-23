<?php
// bbPress is active
if ( !class_exists( 'bbPress' ) ) {
  return;
}

/* Page classes */
if ( ! function_exists( 'yp_topic_class' ) ) :
function yp_topic_class($classes) {
  foreach($classes as $class) {
    switch($class) {
      case 'status-closed':
        $classes[] = 'closed';
        break;
      case 'super-sticky':
        $classes[] = 'sticky';
        break;
    }
  }

  return $classes;
}
endif;
add_filter( 'bbp_get_topic_class','yp_topic_class' );

/* Breadcrumbs */
if ( ! function_exists( 'yp_bb_breadcrumb' ) ) :
function yp_bb_breadcrumb( $clearfix = true ) {
  if(!yp_opts('press_breadcrumbs')) {
    return;
  }
  
  bbp_breadcrumb( array(
    'before'    => '<div class="mt-10 mb-20 pull-left">',
    'after'     => '</div>' . ($clearfix ? '<div class="clearfix"></div>' : ''),
  ) );
}
endif;

/* Pagination */
if ( ! function_exists( 'yp_bb_pagination' ) ) :
function yp_bb_pagination( $args ) {
  $args['type'] = 'array';
  return $args;
}
endif;
add_filter( 'bbp_topic_pagination', 'yp_bb_pagination' );
add_filter( 'bbp_replies_pagination', 'yp_bb_pagination' );
add_filter( 'bbp_search_results_pagination', 'yp_bb_pagination' );


/* Override Widgets */
add_action( 'widgets_init', 'yp_override_bbpress_widgets', 16 );
if ( ! function_exists( 'yp_override_bbpress_widgets' ) ) :
function yp_override_bbpress_widgets() {
  $override_list = array(
    'BBP_Login_Widget'     => 'bbp-login-widget.php',
    'BBP_Views_Widget'     => 'bbp-views-widget.php',
    'BBP_Forums_Widget'    => 'bbp-forums-widget.php',
    'BBP_Topics_Widget'    => 'bbp-topics-widget.php',
    'BBP_Replies_Widget'   => 'bbp-replies-widget.php',
  );

  foreach($override_list as $key => $val) {
    if ( class_exists( $key ) ) {
      unregister_widget( $key );
      include_once( get_template_directory() . '/bbpress/widgets/' . $val );
      register_widget( 'yp_' . $key );
    }
  }
} 
endif;