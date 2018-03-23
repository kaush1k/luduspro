<?php
/**
 * Maintenance Mode
 *
 * @package Youplay
 */
wp_load_translations_early();
$protocol = $_SERVER["SERVER_PROTOCOL"];
if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
        $protocol = 'HTTP/1.0';
header( "$protocol 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Retry-After: 600' );

/**
 * Title Tag filter for 404
 */
add_filter( 'wp_title', 'yp_maintenance_title', 10, 2 );
if ( ! function_exists( 'yp_maintenance_title' ) ) :
function yp_maintenance_title( $title ) {
    return $title = yp_opts('maintenance_title');
}
endif;

?>

<!DOCTYPE html>
<html class="youplay_maintenance">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php if ( yp_opts('general_favicon') ): ?>
      <link rel="shortcut icon" href="<?php echo esc_url(yp_opts('general_favicon')); ?>" />
  <?php endif; ?>

  <style><?php yp_opts_e('general_custom_css'); ?></style>
  <script><?php yp_opts_e('general_custom_js'); ?></script>

  <?php wp_head(); ?>
</head>
<body>
  <!-- Main Content -->
  <section class="content-wrap full">

    <!-- Preorder -->
    <section class="youplay-banner">
      <div class="image">
          <?php echo youplay_get_image( yp_opts('maintenance_background_image'), '1920x1080', false, array(
              'class' => 'jarallax-img'
          ) ); ?>
      </div>

      <div class="info container align-center">
        <div>
          <?php if(yp_opts('maintenance_show_logo')): ?>
            <?php echo youplay_get_image( yp_opts('maintenance_logo'), '500x375' ); ?>
            <br><br>
          <?php endif; ?>

          <?php if(yp_opts('maintenance_show_countdown')): ?>
            <?php echo do_shortcode('[yp_countdown style="styled" date="' . esc_attr(yp_opts('maintenance_countdown_date')) . '" timezone="' . esc_attr(yp_opts('maintenance_countdown_timezone')) . '"]') ?>
            <br><br>
          <?php endif; ?>

          <?php echo do_shortcode(wp_kses_post(yp_opts('maintenance_text'))); ?>
        </div>
      </div>
    </section>
    <!-- /Preorder -->

  </section>
  <!-- /Main Content -->

  <!-- init youplay -->
  <script>
  jQuery(function() {
      if(typeof youplay !== 'undefined') {
          youplay.init({
              parallax:         <?php echo (yp_opts('general_parallax')?'true':'false'); ?>,
              navbarSmall:      false,
              fadeBetweenPages: false
          })
      }
  })
  </script>
  <!-- /init youplay -->

  <?php wp_footer(); ?>
</body>
</html>
